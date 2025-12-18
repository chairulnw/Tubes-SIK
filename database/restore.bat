@echo off
REM ========================================
REM Database Restore Script
REM Sistem Informasi Klinik
REM ========================================

echo [INFO] Database Restore Tool
echo.

REM Set MySQL path
SET MYSQL_PATH=C:\xampp\mysql\bin

REM Check if dump file exists
if not exist "db_klinik_dump.sql" (
    echo [ERROR] File db_klinik_dump.sql tidak ditemukan!
    echo [INFO] Pastikan file db_klinik_dump.sql ada di folder ini.
    pause
    exit /b 1
)

echo [WARNING] Restore akan menghapus semua data yang ada di database db_klinik!
echo.
set /p CONFIRM="Apakah Anda yakin ingin melanjutkan? (Y/N): "

if /i not "%CONFIRM%"=="Y" (
    echo [INFO] Restore dibatalkan.
    pause
    exit /b 0
)

echo.
echo [INFO] Dropping existing database...
"%MYSQL_PATH%\mysql.exe" -u root -e "DROP DATABASE IF EXISTS db_klinik;"

echo [INFO] Creating new database...
"%MYSQL_PATH%\mysql.exe" -u root -e "CREATE DATABASE db_klinik;"

echo [INFO] Restoring database from dump file...
"%MYSQL_PATH%\mysql.exe" -u root db_klinik < db_klinik_dump.sql

if %ERRORLEVEL% EQU 0 (
    echo.
    echo [SUCCESS] Database restored successfully!
    echo [INFO] Database: db_klinik
    echo [INFO] Source: db_klinik_dump.sql
) else (
    echo.
    echo [ERROR] Restore failed with error code %ERRORLEVEL%
    exit /b 1
)

echo.
pause
