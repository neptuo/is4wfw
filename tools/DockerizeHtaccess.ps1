# Example: .\tools\Update-Htaccess.ps1
param()

Push-Location $PSScriptRoot;

$path = Resolve-Path "..\src\.htaccess";

$lines = New-Object Collections.Generic.List[String];
foreach($line in [System.IO.File]::ReadAllLines($path))
{
    if ($line.Contains("setup.php") -or $line.Contains("migrate.php"))
    {
        $line = "# " + $line;
    }
    elseif ($line.Contains(" user/")) 
    {
        $line = $line.Replace(" user/", " instance/user/")
    }
    elseif ($line.Contains(" modules/")) 
    {
        $line = $line.Replace(" modules/", " instance/modules/")
    }

    $lines.Add($line);
}

[System.IO.File]::WriteAllLines($path, $lines);

Pop-Location;
