function Ensure-Directory([string]$path)
{
    if (!(Test-Path $path))
    {
        New-Item -ItemType Directory $path | Out-Null;
    }
}

Ensure-Directory -path './temp/instance/cache'
Ensure-Directory -path './temp/instance/logs'
Ensure-Directory -path './temp/instance/user'
