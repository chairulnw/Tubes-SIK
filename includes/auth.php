<?php
/**
 * File Authentication Functions
 * Sistem Informasi Klinik - Milestone 1
 */

require_once __DIR__ . '/config.php';

/**
 * Register user baru
 * @param string $username Username untuk login
 * @param string $password Password (akan di-hash)
 * @param string $role Role user
 * @param string $nama_lengkap Nama lengkap user
 * @param string|null $email Email user (opsional)
 * @param string|null $no_rm Nomor rekam medis (untuk role pasien)
 * @return array Array dengan key 'success' dan 'message'
 */
function registerUser($username, $password, $role, $nama_lengkap, $email = null, $no_rm = null) {
    $conn = getDBConnection();

    // Cek apakah username sudah digunakan
    $stmt = $conn->prepare("SELECT id_user FROM users WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $stmt->close();
        $conn->close();
        return ['success' => false, 'message' => 'Username sudah digunakan'];
    }
    $stmt->close();

    // Hash password dengan bcrypt
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    // Insert user baru
    $stmt = $conn->prepare("INSERT INTO users (username, password, role, nama_lengkap, email, no_rm) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("ssssss", $username, $hashed_password, $role, $nama_lengkap, $email, $no_rm);

    if ($stmt->execute()) {
        $user_id = $conn->insert_id;
        $stmt->close();
        $conn->close();
        return ['success' => true, 'user_id' => $user_id, 'message' => 'Registrasi berhasil'];
    } else {
        $error = $stmt->error;
        $stmt->close();
        $conn->close();
        return ['success' => false, 'message' => 'Gagal mendaftar: ' . $error];
    }
}

/**
 * Login user
 * @param string $username Username
 * @param string $password Password
 * @return array Array dengan key 'success', 'message', dan 'user' (jika berhasil)
 */
function loginUser($username, $password) {
    $conn = getDBConnection();

    // Ambil data user dari database
    $stmt = $conn->prepare("SELECT id_user, username, password, role, nama_lengkap, email, no_rm, is_active FROM users WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 0) {
        $stmt->close();
        $conn->close();
        return ['success' => false, 'message' => 'Username tidak ditemukan'];
    }

    $user = $result->fetch_assoc();
    $stmt->close();

    // Cek apakah akun aktif
    if (!$user['is_active']) {
        $conn->close();
        return ['success' => false, 'message' => 'Akun tidak aktif'];
    }

    // Verifikasi password
    if (!password_verify($password, $user['password'])) {
        $conn->close();
        return ['success' => false, 'message' => 'Password salah'];
    }

    // Update last login
    $stmt = $conn->prepare("UPDATE users SET last_login = NOW() WHERE id_user = ?");
    $stmt->bind_param("i", $user['id_user']);
    $stmt->execute();
    $stmt->close();

    // Buat session
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    $_SESSION['user_id'] = $user['id_user'];
    $_SESSION['username'] = $user['username'];
    $_SESSION['role'] = $user['role'];
    $_SESSION['nama_lengkap'] = $user['nama_lengkap'];
    $_SESSION['email'] = $user['email'];
    $_SESSION['no_rm'] = $user['no_rm'];

    // Simpan session ke database
    $session_id = session_id();
    $ip_address = $_SERVER['REMOTE_ADDR'];
    $user_agent = $_SERVER['HTTP_USER_AGENT'];

    // Hapus session lama user ini jika ada
    $stmt = $conn->prepare("DELETE FROM sessions WHERE id_user = ?");
    $stmt->bind_param("i", $user['id_user']);
    $stmt->execute();
    $stmt->close();

    // Insert session baru
    $stmt = $conn->prepare("INSERT INTO sessions (session_id, id_user, ip_address, user_agent) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("siss", $session_id, $user['id_user'], $ip_address, $user_agent);
    $stmt->execute();
    $stmt->close();

    $conn->close();

    return ['success' => true, 'user' => $user, 'message' => 'Login berhasil'];
}

/**
 * Validasi kekuatan password
 * @param string $password Password yang akan divalidasi
 * @return array Array berisi error messages (kosong jika valid)
 */
function validatePassword($password) {
    $errors = [];

    if (strlen($password) < 8) {
        $errors[] = "Password minimal 8 karakter";
    }

    if (!preg_match("/[A-Z]/", $password)) {
        $errors[] = "Password harus mengandung minimal 1 huruf besar";
    }

    if (!preg_match("/[a-z]/", $password)) {
        $errors[] = "Password harus mengandung minimal 1 huruf kecil";
    }

    if (!preg_match("/[0-9]/", $password)) {
        $errors[] = "Password harus mengandung minimal 1 angka";
    }

    return $errors;
}

/**
 * Validasi username
 * @param string $username Username yang akan divalidasi
 * @return array Array berisi error messages (kosong jika valid)
 */
function validateUsername($username) {
    $errors = [];

    if (strlen($username) < 4) {
        $errors[] = "Username minimal 4 karakter";
    }

    if (strlen($username) > 50) {
        $errors[] = "Username maksimal 50 karakter";
    }

    if (!preg_match("/^[a-zA-Z0-9_]+$/", $username)) {
        $errors[] = "Username hanya boleh mengandung huruf, angka, dan underscore";
    }

    return $errors;
}

/**
 * Generate hash password baru (untuk testing atau reset password)
 * @param string $password Password yang akan di-hash
 * @return string Hashed password
 */
function generatePasswordHash($password) {
    return password_hash($password, PASSWORD_DEFAULT);
}

?>
