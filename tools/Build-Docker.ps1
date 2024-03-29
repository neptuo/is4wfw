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

# Backup files to rewrite
$VersionSourceFile = "..\src\app\scripts\php\includes\version.inc.php"
$VersionTempFile = New-TemporaryFile
$SettingsSourceFile = "..\src\app\scripts\php\includes\settings.inc.php"
$SettingsTempFile = New-TemporaryFile
$HtaccessSourceFile = "..\src\.htaccess"
$HtaccessTempFile = New-TemporaryFile

Copy-Item $VersionSourceFile -Destination $VersionTempFile
Copy-Item $SettingsSourceFile -Destination $SettingsTempFile
Copy-Item $HtaccessSourceFile -Destination $HtaccessTempFile

# Rewrite various files for docker
Invoke-Expression ".\Update-Version.ps1 $versionName";
Invoke-Expression ".\Update-InstancePath.ps1 instance";
Invoke-Expression ".\DockerizeHtaccess.ps1";

$versionName = $versionName.Substring(1);
$tag = "neptuo/is4wfw:$versionName";
$tagDev = "$tag-dev";

# Build docker image
Invoke-Expression "docker build .. -f ..\docker\is4wfw\dockerfile -t $tag";
Invoke-Expression "docker build .. -f ..\docker\is4wfw-dev\dockerfile -t $tagDev --build-arg baseversion=$versionName";

Write-Host "Docker image built and tagged as '$tag' and '$tagDev'.";

# Restore backup
Copy-Item $VersionTempFile -Destination $VersionSourceFile
Copy-Item $SettingsTempFile -Destination $SettingsSourceFile
Copy-Item $HtaccessTempFile -Destination $HtaccessSourceFile

if ($push) {
    Invoke-Expression "docker push $tag";
    Invoke-Expression "docker push $tagDev";
    Write-Host "Docker image pushed.";
}

Pop-Location;