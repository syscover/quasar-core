<?php namespace Quasar\Core\Support;

class File
{
    public static function read(array $file)
    {    
        header('Content-Type: ' . $file['mime'] ?? null);
        readfile($file['pathname'] ? storage_path($file['pathname']) :  null);
        exit;
    }
}
