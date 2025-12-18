@echo off
REM ========================================
REM Database Backup Script
REM Sistem Informasi Klinik
REM ========================================

echo [INFO] Starting database backup...

REM Set MySQL path
SET MYSQL_PATH=C:\xampp\mysql\bin

REM Get current date and time for filename
for /f "tokens=2 delims==" %%I in ('wmic os get localdatetime /value') do set datetime=%%I
set TIMESTAMP=%datetime:~0,8%_%datetime:~8,6%

REM Set backup filename
SET BACKUP_FILE=db_klinik_backup_%TIMESTAMP%.sql

echo [INFO] Backup file: %BACKUP_FILE%

REM Create backup
"%MYSQL_PATH%\mysqldump.exe" -u root db_klinik --add-drop-table --complete-insert --extended-insert=FALSE --routines --triggers > "%BACKUP_FILE%"

if %ERRORLEVEL% EQU 0 (
    echo [SUCCESS] Database backup created successfully!
    echo [INFO] File location: %CD%\%BACKUP_FILE%
) else (
    echo [ERROR] Backup failed with error code %ERRORLEVEL%
    exit /b 1
)

pause
