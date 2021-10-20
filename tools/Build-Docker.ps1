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
$tag = "neptuo/is4wfw:$versionName";
$tagDev = "$tag-dev";

# Build docker image
Invoke-Expression "docker build .. -f ..\docker\is4wfw\dockerfile -t $tag";
Invoke-Expression "docker build .. -f ..\docker\is4wfw-dev\dockerfile -t $tagDev";

Write-Host "Docker image built and tagged as '$tag' and '$tagDev'.";

if ($push) {
    Invoke-Expression "docker push $tag";
    Invoke-Expression "docker push $tagDev";
    Write-Host "Docker image pushed.";
}

Pop-Location;