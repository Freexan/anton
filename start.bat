@echo off
docker-compose down >nul 2>&1
docker-compose up -d
timeout /t 3 /nobreak >nul
start http://localhost:8080/items

