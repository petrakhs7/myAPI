<?php

class parseFile
{

    public function readFile($file, $delimetter=null)
    {
        $handle = fopen($file, "r");
        if ($handle) {
            while (($line = fgets($handle)) !== false) {
                if (empty($delimetter)){
                    $newline[] = $line;
                }
                else {
                    $newline[] = explode($delimetter, $line);
                }

            }
            return $newline;
        } else {
            // error opening the file.
        }
        fclose($handle);
    }

    public function writeFile($path, $title, $message, $extension)
    {
        date_default_timezone_set('Europe/Athens');
        $fh = fopen($path . $title . "_" . date('Y-m-d\_His', time()) . $extension, 'a');
        fwrite($fh, $message . "\n");
        fclose($fh);
    }
}
