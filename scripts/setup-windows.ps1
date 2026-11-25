param(
    [switch] $Demo,
    [switch] $Serve
)

$ErrorActionPreference = 'Stop'

function Invoke-SetupCommand {
    param([string] $Program, [string[]] $Arguments)

    & $Program @Arguments
    if ($LASTEXITCODE -ne 0) {
        throw "$Program failed with exit code $LASTEXITCODE."
    }
}

Push-Location (Join-Path $PSScriptRoot '..')
try {
    foreach ($program in @('php', 'composer')) {
        Get-Command $program -ErrorAction Stop | Out-Null
    }

    if (-not (Test-Path '.env')) {
        Copy-Item '.env.example' '.env'
    }

    if ((Select-String -Path '.env' -Pattern '^DB_CONNECTION=sqlite\s*$' -Quiet) -and
        -not (Test-Path 'database/database.sqlite')) {
        New-Item -ItemType File -Path 'database/database.sqlite' | Out-Null
    }

    Invoke-SetupCommand 'composer' @('install', '--no-interaction')
    if (Select-String -Path '.env' -Pattern '^APP_KEY=(\s*|""|\x27\x27)\s*$' -Quiet) {
        Invoke-SetupCommand 'php' @('artisan', 'key:generate', '--no-interaction')
    }

    Invoke-SetupCommand 'php' @('artisan', 'migrate', '--no-interaction')
    if ($Demo) {
        Invoke-SetupCommand 'php' @('artisan', 'db:seed', '--no-interaction')
    }

    Invoke-SetupCommand 'php' @('artisan', 'test')
    if ($Serve) {
        Invoke-SetupCommand 'php' @('artisan', 'serve', '--host=127.0.0.1', '--port=8001')
    } else {
        Write-Host 'Setup complete. Run php artisan serve --port=8001 to start SpendSense Blade.'
    }
} finally {
    Pop-Location
}
