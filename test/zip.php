<pre><?php

$zipFileName = "phpwfw-v338.7-patch.zip";
$targetPath = $_SERVER["DOCUMENT_ROOT"] . "/out";

echo 'target: ' . $targetPath . "\n";

$zip = new ZipArchive();
if ($zip->open($zipFileName) === TRUE) {

    print_r($zip);
    var_dump($zip);
    echo "numFiles: " . $zip->numFiles . "\n";
    echo "status: " . $zip->status  . "\n";
    echo "statusSys: " . $zip->statusSys . "\n";
    echo "filename: " . $zip->filename . "\n";
    echo "comment: " . $zip->comment . "\n";

    $extractResult = $zip->extractTo($targetPath);
    echo 'ok - ' . $extractResult . ' : ' . ZipStatusString($extractResult);
    $zip->close();
} else {
    echo 'nok';
}

function ZipStatusString( $status )
{
    switch( (int) $status )
    {
        case ZipArchive::ER_OK           : return 'N No error';
        case ZipArchive::ER_MULTIDISK    : return 'N Multi-disk zip archives not supported';
        case ZipArchive::ER_RENAME       : return 'S Renaming temporary file failed';
        case ZipArchive::ER_CLOSE        : return 'S Closing zip archive failed';
        case ZipArchive::ER_SEEK         : return 'S Seek error';
        case ZipArchive::ER_READ         : return 'S Read error';
        case ZipArchive::ER_WRITE        : return 'S Write error';
        case ZipArchive::ER_CRC          : return 'N CRC error';
        case ZipArchive::ER_ZIPCLOSED    : return 'N Containing zip archive was closed';
        case ZipArchive::ER_NOENT        : return 'N No such file';
        case ZipArchive::ER_EXISTS       : return 'N File already exists';
        case ZipArchive::ER_OPEN         : return 'S Can\'t open file';
        case ZipArchive::ER_TMPOPEN      : return 'S Failure to create temporary file';
        case ZipArchive::ER_ZLIB         : return 'Z Zlib error';
        case ZipArchive::ER_MEMORY       : return 'N Malloc failure';
        case ZipArchive::ER_CHANGED      : return 'N Entry has been changed';
        case ZipArchive::ER_COMPNOTSUPP  : return 'N Compression method not supported';
        case ZipArchive::ER_EOF          : return 'N Premature EOF';
        case ZipArchive::ER_INVAL        : return 'N Invalid argument';
        case ZipArchive::ER_NOZIP        : return 'N Not a zip archive';
        case ZipArchive::ER_INTERNAL     : return 'N Internal error';
        case ZipArchive::ER_INCONS       : return 'N Zip archive inconsistent';
        case ZipArchive::ER_REMOVE       : return 'S Can\'t remove file';
        case ZipArchive::ER_DELETED      : return 'N Entry has been deleted';
        
        default: return sprintf('Unknown status %s', $status );
    }
}

?></pre>