$isTag = 'true' -eq $env:APPVEYOR_REPO_TAG;
if ($isTag) {
    $sourceCommitHash = Invoke-Expression "git rev-list --tags --max-count=1";
    $source = Invoke-Expression "git describe --tags $sourceCommitHash";
    $target = $env:APPVEYOR_REPO_TAG_NAME;
    Write-Host "Running an Appveyor release build (tag '$target', patch source '$source').";

    Invoke-Expression ((Join-Path -Path $PSScriptRoot -ChildPath "Pack.ps1") + " $source $target");
}