# PowerShell wrapper for Windows users who don't have `make`.
#
# Usage:
#   .\run.ps1 composer install
#   .\run.ps1 artisan route:list
#   .\run.ps1 serve            # boots http://localhost:8000
#   .\run.ps1 test

param(
    [Parameter(Mandatory=$true, Position=0)][string]$Cmd,
    [Parameter(ValueFromRemainingArguments=$true)][string[]]$Rest
)

$composerImg = "composer:2"
$phpImg      = "php:8.2-cli"
$mount       = @("-v", "${PWD}:/app", "-w", "/app")

# Docker Desktop on Windows handles drive-letter paths natively here — no MSYS
# quirk because PowerShell isn't a POSIX shell. Keep it simple.

switch ($Cmd) {
    "composer" { docker run --rm @mount $composerImg @Rest }
    "artisan"  { docker run --rm @mount $phpImg php artisan @Rest }
    "serve"    { docker run --rm -it @mount -p 8000:8000 $phpImg php -S 0.0.0.0:8000 -t public }
    "tinker"   { docker run --rm -it @mount $phpImg php artisan tinker }
    "test"     { docker run --rm @mount $phpImg php artisan test }
    default    { Write-Error "Unknown command: $Cmd. Use: composer | artisan | serve | tinker | test" }
}
