@echo off
REM Script untuk dump database SIK

echo Dumping database SIK...

REM Dump seluruh database
mysqldump -u root -p SIK > SIK_backup.sql

echo Done! File saved as SIK_backup.sql
pause
