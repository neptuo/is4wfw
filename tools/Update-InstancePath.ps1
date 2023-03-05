# Example: .\tools\Update-InstancePath.ps1 instance
param($instancePath)

Push-Location $PSScriptRoot;

$instancePath.TrimEnd('/')

$path = "..\src\app\scripts\php\includes\settings.inc.php";
$content = Get-Content -Path $path;
$content = $content -replace "INSTANCE_PATH;", ('INSTANCE_PATH . "' + $instancePath + '/";');
Set-Content -Path $path $content;

Pop-Location;
