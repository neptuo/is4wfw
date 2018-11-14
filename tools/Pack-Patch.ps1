# Example: .\tools\Pack-Patch.ps1 v338.6 v338.7
param($source, $target, $versionName)

# Source revision is required.
if ($null -eq $source) 
{
    Write-Output "Missing required 'source' parameter.";
    return;
}

# If target revision is missing, take HEAD.
if ($null -eq $target) 
{
    $target = "HEAD";
}

# If version name is missing, take 'target'.
if ($null -eq $versionName) 
{
    $versionName = $target;
}

Push-Location $PSScriptRoot;

# Compute release name.
$srcPath = "src";
$artifactsPath = "artifacts";
$releaseName = "phpwfw-" + $versionName + "-patch";
$targetFileName = $releaseName + ".zip";

# Create temp root.
$tempPath = Join-Path -Path $env:TEMP -ChildPath $releaseName;
New-Item -Force -ItemType Directory $tempPath | Out-Null;

$hasFile = $false;
$changedFiles = git diff --name-only $source $target;
foreach ($file in $changedFiles) 
{
    if ($file.StartsWith($srcPath)) 
    {
        # Get relative path.
        $targetFile = $file.Substring($srcPath.Length + 1);
        $targetDirectoryPath = Split-Path -Parent $targetFile;
        
        # Create relative path in temp.
        $tempDirectoryPath = Join-Path -Path $tempPath -ChildPath $targetDirectoryPath;
        New-Item -Force -ItemType Directory $tempDirectoryPath | Out-Null;

        # Copy to temp including relative path.
        $tempTargetFile = Join-Path -Path $tempPath -ChildPath $targetFile;
        $sourceFile = Join-Path -Path ".." -ChildPath $file;
        if (Test-Path $sourceFile) 
        {
            Copy-Item -Force $sourceFile $tempTargetFile | Out-Null;
            $hasFile = $true;
        }
    }
}

if ($hasFile) 
{
    # Create artifacts directory.
    $targetFilePath = Join-Path -Path (Join-Path -Path (Get-Location) -ChildPath "..") -ChildPath $artifactsPath;
    if (!(Test-Path $targetFilePath))
    {
        New-Item -ItemType Directory $targetFilePath | Out-Null;
    }
    
    $currentPath = $PSScriptRoot;
    $targetFilePath = Join-Path -Path $artifactsPath -ChildPath $targetFileName;
    $targetFilePath = (Join-Path -Path $currentPath -ChildPath (Join-Path -Path ".." -ChildPath $targetFilePath));
    
    # Delete patch file if exists.
    if (Test-Path($targetFilePath)) 
    {
        Remove-Item $targetFilePath;
    }
    
    # Create new archive.
    $archiverPath = Join-Path -Path $currentPath -ChildPath "7za.exe";
    Push-Location $tempPath;
    Invoke-Expression ($archiverPath + " a -tzip " + $targetFilePath) | Out-Null;
    Pop-Location;

    Write-Host ("Created file '" + $targetFilePath + "'");
}
else 
{
    Write-Output "Nothing in patch.";
}

Pop-Location;

# Delete files in temp.
Remove-Item -Force -Recurse $tempPath;