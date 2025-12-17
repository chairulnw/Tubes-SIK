/**
 * Heart Rate Sensor with WiFi Integration (SpO2 Not Included)
 * Hardware: ESP32 + MAX30105 Sensor
 *
 * KONFIGURASI WAJIB:
 * 1. Ganti WIFI_SSID dan WIFI_PASSWORD dengan WiFi Anda
 * 2. Ganti SERVER_URL dengan IP address komputer server
 * 3. Ganti PATIENT_NO_RM dengan nomor rekam medis pasien yang sedang diukur
 *
 * Data dikirim ke server setiap 30 detik untuk menghindari database penuh
 */

#include <WiFi.h>
#include <HTTPClient.h>
#include <Wire.h>
#include "MAX30105.h"
#include "heartRate.h"

// ========== KONFIGURASI WIFI ==========
const char* WIFI_SSID = "Meli";        // Ganti dengan nama WiFi Anda
const char* WIFI_PASSWORD = "melissaaatr"; // Ganti dengan password WiFi
const char* SERVER_URL = "http://192.168.100.11/Tubes-SIK/api/wearable_data.php"; // Fixed: single slash

// ========== KONFIGURASI PASIEN ==========
const char* PATIENT_NO_RM = "RM0000002"; // WAJIB DIGANTI per pasien yang diperiksa

// ========== KONFIGURASI DEVICE ==========
const char* DEVICE_ID = "HR_001";
const unsigned long SEND_INTERVAL = 30000; // 30 detik (30000 ms)

// ========== SENSOR VARIABLES ==========
MAX30105 particleSensor;
const byte RATE_SIZE = 4; // Average 4 beats untuk smooth reading
byte rates[RATE_SIZE];
byte rateSpot = 0;
long lastBeat = 0;
float beatsPerMinute;
int beatAvg = 0;

// ========== TIMING VARIABLES ==========
unsigned long lastSendTime = 0;
bool dataReadyToSend = false;

// ========== LED PINS (Optional) ==========
#define LED_CONNECTED 2  // Built-in LED ESP32 - hijau saat connected

void setup() {
  Serial.begin(115200);

  // Initialize LED pin
  pinMode(LED_CONNECTED, OUTPUT);
  digitalWrite(LED_CONNECTED, LOW);

  // Initialize MAX30105 sensor
  Serial.println("\n[1/3] Initializing MAX30105 sensor...");
  if (!particleSensor.begin(Wire, I2C_SPEED_FAST)) {
    Serial.println("ERROR: MAX30105 not found!");
    while (1); // Stuck here if sensor not found
  }

  Serial.println("MAX30105 sensor OK!");

  particleSensor.setup(); // Configure sensor with default settings
  particleSensor.setPulseAmplitudeRed(0x0A); // Turn Red LED to low to indicate sensor is running
  particleSensor.setPulseAmplitudeGreen(0);

  // Connect to WiFi
  Serial.println("\n[2/3] Connecting to WiFi...");
  Serial.print("SSID: ");
  Serial.println(WIFI_SSID);

  WiFi.begin(WIFI_SSID, WIFI_PASSWORD);

  int attempts = 0;
  while (WiFi.status() != WL_CONNECTED && attempts < 20) {
    delay(500);
    Serial.print(".");
    attempts++;
  }

  if (WiFi.status() == WL_CONNECTED) {
    Serial.println("\nWiFi Connected!");
    Serial.print("IP Address: ");
    Serial.println(WiFi.localIP());
    digitalWrite(LED_CONNECTED, HIGH); // LED hijau ON
  }

  // Show configuration
  Serial.println("\n[3/3] Configuration:");
  Serial.print("Patient No. RM: ");
  Serial.println(PATIENT_NO_RM);
  Serial.print("Server URL: ");
  Serial.println(SERVER_URL);
  Serial.print("Send Interval: ");
  Serial.print(SEND_INTERVAL / 1000);
  Serial.println(" seconds");

  Serial.println("\n========================================");
  Serial.println("System ready! Place finger on sensor...");
  Serial.println("========================================\n");
}

void loop() {
  long irValue = particleSensor.getIR();

  // Check if finger is detected
  if (irValue < 50000) {
    Serial.print(".");
    delay(1000);
    beatAvg = 0; // Reset average when no finger
  }

  // Detect heartbeat
  if (checkForBeat(irValue) == true) {
    // Calculate BPM
    long delta = millis() - lastBeat;
    lastBeat = millis();
    beatsPerMinute = 60 / (delta / 1000.0);

    // Validate BPM range (20-255 BPM)
    if (beatsPerMinute < 255 && beatsPerMinute > 20) {
      rates[rateSpot++] = (byte)beatsPerMinute;
      rateSpot %= RATE_SIZE;

      // Calculate average BPM
      beatAvg = 0;
      for (byte x = 0; x < RATE_SIZE; x++) {
        beatAvg += rates[x];
      }
      beatAvg /= RATE_SIZE;

      dataReadyToSend = true;
    }
  }

  // Display current readings
  Serial.print("IR=");
  Serial.print(irValue);
  Serial.print(", BPM=");
  Serial.print(beatsPerMinute, 1);
  Serial.print(", Avg BPM=");
  Serial.println(beatAvg);

  // Send data to server every SEND_INTERVAL
  if ((millis() - lastSendTime >= SEND_INTERVAL) && beatAvg > 40) {
      sendDataToServer(beatAvg);
      lastSendTime = millis();
  }
}

void sendDataToServer(int bpm) {
  if (WiFi.status() != WL_CONNECTED) {
    Serial.println("\n[ERROR] WiFi not connected! Reconnecting...");
    WiFi.reconnect();
    delay(2000);
    return;
  }

  Serial.println("\n========================================");
  Serial.println("Sending data to server...");
  Serial.print("BPM: ");
  Serial.println(bpm);

  HTTPClient http;
  http.begin(SERVER_URL);
  http.addHeader("Content-Type", "application/json");
  http.setTimeout(5000); // 5 second timeout

  // Build JSON payload (Heart Rate only, no SpO2)
  String jsonPayload = "{";
  jsonPayload += "\"no_rm\":\"" + String(PATIENT_NO_RM) + "\",";
  jsonPayload += "\"detak_jantung\":" + String(bpm) + ",";
  jsonPayload += "\"device_id\":\"" + String(DEVICE_ID) + "\"";
  jsonPayload += "}";

  Serial.print("Payload: ");
  Serial.println(jsonPayload);

  // Send HTTP POST request
  int httpCode = http.POST(jsonPayload);

  if (httpCode > 0) {
    Serial.print("HTTP Response Code: ");
    Serial.println(httpCode);

    String response = http.getString();
    Serial.print("Server Response: ");
    Serial.println(response);

    if (httpCode == 200) {
      Serial.println("[SUCCESS] Data sent successfully!");
      digitalWrite(LED_CONNECTED, HIGH);
    } else {
      Serial.println("[WARNING] Server returned non-200 code");
    }
  } else {
    Serial.print("[ERROR] HTTP POST failed: ");
    Serial.println(http.errorToString(httpCode));
    Serial.println("Please check:");
    Serial.println("  1. Server URL is correct");
    Serial.println("  2. Server is running (XAMPP Apache)");
    Serial.println("  3. Firewall allows connections");
  }

  http.end();
  Serial.println("========================================\n");
}
