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
        if (Test-Path $file) 
        {
            Copy-Item -Force $file $tempTargetFile | Out-Null;
        }

        $hasFile = $true;
    }
}

if ($hasFile) 
{
    # Create artifacts directory.
    $targetFilePath = Join-Path -Path (Get-Location) -ChildPath $artifactsPath;
    if (!(Test-Path $targetFilePath))
    {
        New-Item -ItemType Directory $targetFilePath | Out-Null;
    }
    
    # Delete patch file if exists.
    $targetFilePath = Join-Path -Path $artifactsPath -ChildPath $targetFileName;
    if (Test-Path($targetFilePath)) 
    {
        Remove-Item $targetFilePath;
    }

    # Create new archive.
    Compress-Archive -Path ($tempPath + "\*") -DestinationPath $targetFilePath | Out-Null;
}
else 
{
    Write-Output "Nothing in patch.";
}

# Delete files in temp.
Remove-Item -Force -Recurse $tempPath;