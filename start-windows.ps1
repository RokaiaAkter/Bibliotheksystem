$ErrorActionPreference = "Stop"

Set-Location $PSScriptRoot

if (-not (Test-Path ".env")) {
    Copy-Item ".env.example" ".env"
    Write-Host "Created .env from .env.example."
}

docker compose up -d --build
docker compose ps

Write-Host ""
Write-Host "Bibliothksystem: http://localhost:8080"
Write-Host "Adminer:          http://localhost:8081"

