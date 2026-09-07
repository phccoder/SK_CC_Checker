# ============================================================
# SK/CC Checker - No-XAMPP Launcher
# ------------------------------------------------------------
# This starts the app using PHP's built-in web server, so you
# don't need XAMPP/WAMP at all. It only needs PHP itself.
#
#  1. Install PHP (standalone zip, no XAMPP) if not found:
#     https://windows.php.net/download/
#     Add php.exe to your PATH, OR set $phpPath below.
#  2. Right-click this file -> "Run with PowerShell"
#     In PowerShell:  .\serve.ps1
#  3. Open  http://localhost:8080  in your browser.
# ============================================================

param(
    [int]$Port = 8080,
    [string]$phpPath = ""
)

$hostAddress = "localhost"

function Find-Php {
    # 1. Explicitly provided path
    if ($phpPath -ne "" -and (Test-Path -LiteralPath $phpPath)) {
        return $phpPath
    }
    # 2. In PATH
    $inPath = (Get-Command php -ErrorAction SilentlyContinue)
    if ($inPath) {
        return $inPath.Source
    }
    # 3. Common default install locations
    $candidates = @(
        "$env:ProgramFiles\php\php.exe",
        "${env:ProgramFiles(x86)}\php\php.exe",
        "$env:LOCALAPPDATA\Programs\PHP\php.exe",
        "C:\php\php.exe",
        "C:\tools\php\php.exe"
    )
    foreach ($c in $candidates) {
        if (Test-Path -LiteralPath $c) {
            return $c
        }
    }
    return $null
}

$php = Find-Php

if (-not $php) {
    Write-Host "=" -ForegroundColor Yellow
    Write-Host "  PHP was not found on this system." -ForegroundColor Yellow
    Write-Host "  To run without XAMPP, please install standalone PHP:" -ForegroundColor Yellow
    Write-Host "    https://windows.php.net/download/" -ForegroundColor Yellow
    Write-Host "  Then add php.exe to your PATH, or edit `$phpPath in this file." -ForegroundColor Yellow
    Write-Host "  Alternatively you can still use XAMPP if you prefer." -ForegroundColor Yellow
    Write-Host "=" -ForegroundColor Yellow
    exit 1
}

$dir = $PSScriptRoot

Write-Host ""
Write-Host "  SK/CC Checker" -ForegroundColor Green
Write-Host "  --------------" -ForegroundColor Green
Write-Host "  PHP      : $php" -ForegroundColor Gray
Write-Host "  URL      : http://$hostAddress`:$Port" -ForegroundColor Cyan
Write-Host "  Directory: $dir" -ForegroundColor Gray
Write-Host "  Press Ctrl+C to stop the server." -ForegroundColor Gray
Write-Host ""

Push-Location $dir
try {
    & $php -S "$hostAddress`:$Port" -t $dir
} finally {
    Pop-Location
}
