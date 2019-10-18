# Example: .\tools\Update-Version.ps1 v339.0-preview1
param($version)

Push-Location $PSScriptRoot;

$path = "..\src\app\scripts\php\includes\version.inc.php";
$content = Get-Content -Path $path;
$content = $content -replace "v0.0-preview1", $version;
Set-Content -Path $path $content;

Pop-Location;