# Example: .\tools\Pack-Patch.ps1 v338.7
param($versionName)

# VersionName is required.
if ($null -eq $versionName) 
{
    Write-Output "Missing required 'versionName' parameter.";
    return;
}

# Compute release name.
$srcPath = "src";
$artifactsPath = "artifacts";
$releaseName = "phpwfw-" + $versionName + "-full";
$targetFileName = $releaseName + ".zip";

# Create artifacts directory.
$targetFilePath = Join-Path -Path (Get-Location) -ChildPath $artifactsPath;
if (!(Test-Path $targetFilePath))
{
    New-Item -ItemType Directory $targetFilePath | Out-Null;
}
    
# Delete patch file if exists.
$targetFilePath = Join-Path -Path $targetFilePath -ChildPath $targetFileName;
if (Test-Path($targetFilePath)) 
{
    Remove-Item $targetFilePath;
}

# Create new archive.
$sourceDirectoryPath = Join-Path -Path $srcPath -ChildPath "*";
Compress-Archive -Path $sourceDirectoryPath -DestinationPath $targetFilePath | Out-Null;