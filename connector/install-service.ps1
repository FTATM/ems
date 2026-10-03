<#
.SYNOPSIS
    Install the EMS meter collector as a Windows service.

.DESCRIPTION
    Two ways to run the collector unattended:

      -Method NSSM      (default) A proper Windows service, with automatic
                        restart on crash and log rotation. Needs nssm.exe.
      -Method Scheduler A Task Scheduler job that starts at boot. No extra
                        download, but no crash recovery.

    Run from an elevated PowerShell prompt.

.PARAMETER Method
    NSSM or Scheduler.

.PARAMETER Python
    Interpreter to run. Defaults to EMS_PYTHON from .env, then to whatever
    `python` resolves to.

.PARAMETER RunAsCurrentUser
    Run the service as the logged-in account. RS485 meters usually need this:
    the default LocalSystem account often cannot open a COM port that is bound
    to the interactive session. You will be prompted for the password.

.EXAMPLE
    .\install-service.ps1
    .\install-service.ps1 -RunAsCurrentUser
    .\install-service.ps1 -Method Scheduler
#>

[CmdletBinding()]
param(
    [ValidateSet('NSSM', 'Scheduler')]
    [string]$Method = 'NSSM',
    [string]$Python,
    [switch]$RunAsCurrentUser,
    [string]$ServiceName = 'EMSCollector'
)

$ErrorActionPreference = 'Stop'

$ConnectorDir = $PSScriptRoot
$RepoRoot = Split-Path -Parent $ConnectorDir
$LogDir = Join-Path $ConnectorDir 'logs'

Write-Host "EMS collector service installer" -ForegroundColor Cyan
Write-Host "  connector : $ConnectorDir"

# ── Resolve the interpreter ────────────────────────────────────────────────
if (-not $Python) {
    $envFile = Join-Path $RepoRoot '.env'
    if (Test-Path $envFile) {
        $line = Select-String -Path $envFile -Pattern '^\s*EMS_PYTHON\s*=' | Select-Object -First 1
        if ($line) {
            # Strip the key, then any surrounding quotes.
            $Python = ($line.Line -replace '^\s*EMS_PYTHON\s*=', '').Trim().Trim("'", '"')
        }
    }
}
if (-not $Python) {
    $cmd = Get-Command python -ErrorAction SilentlyContinue
    if ($cmd) { $Python = $cmd.Source }
}
if (-not $Python -or -not (Test-Path $Python)) {
    throw "Could not find a Python interpreter. Pass -Python <path to python.exe>."
}
Write-Host "  python    : $Python"

# ── Verify the collector actually starts before registering it ─────────────
Write-Host "`nChecking dependencies..." -NoNewline
& $Python -c "import pymodbus, mysql.connector, requests, dotenv" 2>&1 | Out-Null
if ($LASTEXITCODE -ne 0) {
    Write-Host " missing" -ForegroundColor Red
    throw "Install them first:  & '$Python' -m pip install -r '$(Join-Path $ConnectorDir 'requirements.txt')'"
}
Write-Host " ok" -ForegroundColor Green

if (-not (Test-Path $LogDir)) {
    New-Item -ItemType Directory -Path $LogDir | Out-Null
}

# ── Install ────────────────────────────────────────────────────────────────
if ($Method -eq 'NSSM') {
    $nssm = (Get-Command nssm -ErrorAction SilentlyContinue).Source
    if (-not $nssm) {
        throw "nssm.exe not found on PATH. Download it from https://nssm.cc/download, " +
              "or re-run with -Method Scheduler."
    }

    # Replace any previous installation rather than erroring on a duplicate.
    $existing = Get-Service -Name $ServiceName -ErrorAction SilentlyContinue
    if ($existing) {
        Write-Host "Removing existing service..."
        & $nssm stop $ServiceName 2>&1 | Out-Null
        & $nssm remove $ServiceName confirm 2>&1 | Out-Null
        Start-Sleep -Seconds 2
    }

    Write-Host "Installing service '$ServiceName'..."
    & $nssm install $ServiceName $Python '-m' 'ems' 'run'
    & $nssm set $ServiceName AppDirectory $ConnectorDir
    & $nssm set $ServiceName DisplayName 'EMS Meter Collector'
    & $nssm set $ServiceName Description 'Polls electrical and water meters over Modbus and stores readings in the EMS database.'
    & $nssm set $ServiceName Start SERVICE_AUTO_START
    & $nssm set $ServiceName AppStdout (Join-Path $LogDir 'collector.log')
    & $nssm set $ServiceName AppStderr (Join-Path $LogDir 'collector.log')
    & $nssm set $ServiceName AppRotateFiles 1
    & $nssm set $ServiceName AppRotateBytes 10485760
    # Wait before restarting so a failing DB doesn't spin the service.
    & $nssm set $ServiceName AppRestartDelay 10000

    if ($RunAsCurrentUser) {
        $account = "$env:USERDOMAIN\$env:USERNAME"
        Write-Host "`nThe service will run as $account."
        $password = Read-Host -AsSecureString "Password for $account"
        $plain = [Runtime.InteropServices.Marshal]::PtrToStringAuto(
            [Runtime.InteropServices.Marshal]::SecureStringToBSTR($password))
        & $nssm set $ServiceName ObjectName $account $plain
    }

    & $nssm start $ServiceName
    Start-Sleep -Seconds 3
    & $nssm status $ServiceName

    Write-Host "`nDone." -ForegroundColor Green
    Write-Host "  logs   : $(Join-Path $LogDir 'collector.log')"
    Write-Host "  stop   : nssm stop $ServiceName"
    Write-Host "  remove : nssm remove $ServiceName confirm"
}
else {
    # Task Scheduler fallback. Runs at boot regardless of who is logged on.
    $taskName = $ServiceName
    $action = New-ScheduledTaskAction -Execute $Python -Argument '-m ems run' -WorkingDirectory $ConnectorDir
    $trigger = New-ScheduledTaskTrigger -AtStartup
    $settings = New-ScheduledTaskSettingsSet -AllowStartIfOnBatteries `
        -DontStopIfGoingOnBatteries -RestartInterval (New-TimeSpan -Minutes 1) -RestartCount 999
    # No ExecutionTimeLimit: this task is meant to run forever.
    $settings.ExecutionTimeLimit = 'PT0S'

    if (Get-ScheduledTask -TaskName $taskName -ErrorAction SilentlyContinue) {
        Unregister-ScheduledTask -TaskName $taskName -Confirm:$false
    }

    Register-ScheduledTask -TaskName $taskName -Action $action -Trigger $trigger `
        -Settings $settings -RunLevel Highest -Description 'EMS meter collector' | Out-Null
    Start-ScheduledTask -TaskName $taskName

    Write-Host "`nDone." -ForegroundColor Green
    Write-Host "  status : Get-ScheduledTask -TaskName $taskName"
    Write-Host "  stop   : Stop-ScheduledTask -TaskName $taskName"
    Write-Host "  remove : Unregister-ScheduledTask -TaskName $taskName"
}

Write-Host "`nThe meter management page shows whether the collector is alive."
