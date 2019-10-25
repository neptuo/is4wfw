# Example: .\tools\Pack-Patch.ps1 v338.7
param($versionName)

# VersionName is required.
if ($null -eq $versionName) 
{
    Write-Output "Missing required 'versionName' parameter.";
    return;
}

Push-Location $PSScriptRoot;

# Update version file.
Invoke-Expression ".\Update-Version.ps1 $versionName";

# Compute release name.
$srcPath = "src";
$artifactsPath = "artifacts";
$releaseName = "phpwfw-" + $versionName + "-full";
$targetFileName = $releaseName + ".zip";

# Create artifacts directory.
$targetFilePath = Join-Path -Path (Join-Path -Path (Get-Location) -ChildPath "..") -ChildPath $artifactsPath;
if (!(Test-Path $targetFilePath))
{
    New-Item -ItemType Directory $targetFilePath | Out-Null;
}
    
# Delete full file if exists.
$targetFilePath = Join-Path -Path $targetFilePath -ChildPath $targetFileName;
if (Test-Path($targetFilePath)) 
{
    Remove-Item $targetFilePath;
}

# Create new archive.
$currentPath = $PSScriptRoot;
$archiverPath = Join-Path -Path $currentPath -ChildPath "7za.exe";
$sourceDirectoryPath = Join-Path -Path ".." -ChildPath $srcPath;
Push-Location $sourceDirectoryPath;
Invoke-Expression ($archiverPath + " a -tzip " + $targetFilePath) | Out-Null;
Pop-Location;

Write-Host "Created file '$targetFilePath'";
Pop-Location;