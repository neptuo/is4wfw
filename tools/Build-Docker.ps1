# Example: .\tools\Pack-Patch.ps1 v338.7
param(
    [Parameter(Mandatory=$true)][string]$versionName,
    [Parameter(Mandatory=$false)][switch]$push
)

# VersionName is required.
if ($null -eq $versionName) 
{
    Write-Output "Missing required 'versionName' parameter.";
    return;
}

Push-Location $PSScriptRoot;

# Update version file.
Invoke-Expression ".\Update-Version.ps1 $versionName";

$versionName = $versionName.Substring(1);
$tagName = "neptuo/is4wfw:$versionName";

# Build docker image
Invoke-Expression "docker build .. -f ..\docker\is4wfw\dockerfile -t $tagName";

Write-Host "Docker image built and tagged as '$tagName'.";

if ($push) {
    Invoke-Expression "docker push $tagName";
    Write-Host "Docker image pushed.";
}

Pop-Location;