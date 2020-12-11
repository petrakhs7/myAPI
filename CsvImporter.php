<?php

class CsvImporter
{
    private $fp;
    private $header;
    private $delimiter;
    private $length;
    //--------------------------------------------------------------------
    function __construct($file_name, $delimiter = "\t", $length = 8000)
    {
        $this->fp = fopen($file_name, "r");
        $this->delimiter = $delimiter;
        $this->length = $length;
    }
    //--------------------------------------------------------------------
    function __destruct()
    {
        if ($this->fp) {
            fclose($this->fp);
        }
    }
    //--------------------------------------------------------------------
    function get($max_lines = 0)
    {
        //if $max_lines is set to 0, then get all the data

        $data = array();

        if ($max_lines > 0)
            $line_count = 0;
        else
            $line_count = -1; // so loop limit is ignored

        while ($line_count < $max_lines && ($row = fgetcsv($this->fp, $this->length, $this->delimiter)) !== FALSE) {

            $data[] = $row;

            if ($max_lines > 0)
                $line_count++;
        }
        return $data;
    }
    //--------------------------------------------------------------------

}