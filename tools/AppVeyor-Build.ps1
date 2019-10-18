$isTag = 'true' -eq $env:APPVEYOR_REPO_TAG;
if ($isTag) {
    $source = Invoke-Expression "git describe --abbrev=0 --tags HEAD~1";
    $target = $env:APPVEYOR_REPO_TAG_NAME;
    Write-Host "Running an Appveyor release build (tag '$target', patch source '$source').";

    Invoke-Expression ((Join-Path -Path $PSScriptRoot -ChildPath "Pack.ps1") + " $source $target");
}