# Example: .\tools\Pack.ps1 v338.6 v338.7
param($source, $target, $versionName)

$fullVersion;
if ($null -eq $versionName)
{
    $fullVersion = $target;
}
else 
{
    $fullVersion = $versionName;
}

Invoke-Expression ((Join-Path -Path $PSScriptRoot -ChildPath "Pack-Full.ps1") + " $fullVersion")
Invoke-Expression ((Join-Path -Path $PSScriptRoot -ChildPath "Pack-Patch.ps1") + " $source $target $versionName")