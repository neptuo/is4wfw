# Example: .\tools\Pack.ps1 v339.0-preview1
param([string] $Version = $env:APPVEYOR_REPO_TAG_NAME)

$Source = $null;

if ($Version.Contains("-")) 
{
    # Pre-release.
    $Source = Invoke-Expression "git describe --abbrev=0 --tags HEAD~1";
} 
elseif ($Version.EndsWith(".0")) 
{
    # Major release.
    $Source = $null;
} 
else 
{
    # Patch release.
    $Index = 0;
    do {
        $Index++;
        $Source = Invoke-Expression "git describe --abbrev=0 --tags HEAD~$Index";
    }
    while ($Source.Contains("-"));
}

Write-Host "Packing full version '$Version'.";
Invoke-Expression ((Join-Path -Path $PSScriptRoot -ChildPath "Pack-Full.ps1") + " $Version");

if (!($null -eq $Source)) {
    Write-Host "Packing patch version '$Version' - '$Source'.";
    Invoke-Expression ((Join-Path -Path $PSScriptRoot -ChildPath "Pack-Patch.ps1") + " $Source $Version")
}