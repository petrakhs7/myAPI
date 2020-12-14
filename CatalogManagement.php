<?php


class CatalogManagement
{
    private $outputformat;
    private $re;
    private $temp;
    private $data;
    private $invalid_line;
    private $valid_catalog;
    private $invalid_catalog;
    private $Catalogs;
    private $invalid_number;
    private $raw_catalog = array();
    private $tempcatalog = array();
    private $invalidcatalog = array();
    private $finalcatalog = array();

    function __construct($data, $outputformat = "+")
    {
        $this->data = $data;
        $this->outputformat = $outputformat;
        $this->re = '/^((\+\d{2})(\d{10})$)|^((00\d{2})(\d{10})$)|(^\d{10}$)/m';
    }

    //this function retrieves a dellimeted array and returns it as an object of various catalogs
    public function manageCatalog()
    {
        foreach ($this->data as $line) {
            if (count($line) > 2 || ($line[0] == '' && $line[1] == '')) {
                $this->invalid_line[] = implode(";", $line);
            } else {
                foreach ($line as $k => $v) {
                    if ($this->getType($v) == "name") {
                        $name[] = $v;
                    } elseif ($this->getType($v) == "number") {
                        $v = str_replace(" ", "", trim($v));
                        $number[] = $v;
                        $validatednumber[] = $this->validateTelNum($v);
                    } elseif ($this->getType($v) == "empty") {
                        if ($this->getType($this->temp) == "name") {
                            $v = str_replace(" ", "", trim($v));
                            $number[] = $v;
                            $validatednumber[] = $this->validateTelNum($v);
                        } else{
                            $name[] = $v;
                        }
                    }
                    $this->temp = $v;
                }
            }
        }
        $this->raw_catalog = array_combine($name, $number); // array me ola ta onomata kai ta noumera opws erxetai apo to arxeio
        $this->tempcatalog = array_combine($name, $validatednumber); // array me ola ta onomata kai "invalid number" opou einai lathos to noumero 
        $this->create_catalogs($this->tempcatalog);
        /*foreach (array_keys($this->tempcatalog, "invalid number") as $k => $v) {
            $invalidname[] = $v;
            $invalidnumber[] = $this->raw_catalog[$v];
            $this->invalidcatalog = array_combine($invalidname, $invalidnumber); //aray me onomata kai "invalid number" mono
            unset($this->tempcatalog[$v]); // kopsimo twn "invalid number"
        }
        foreach ($this->tempcatalog as $k => $v) {
            if ($k == '') {
                unset($this->tempcatalog[$k]);
                $this->invalidcatalog += array($k => $v);
                
            }
        }
        $this->invalidcatalog += array('invalid line' => $this->invalid_line);*/
        //foreach ($this->tempcatalog as $k => $v) {
        /*foreach ($this->valid_catalog->number as $k => $v) {
            $finalnumber[] = $this->transformTelNum($v, $this->outputformat);
            $final_name[] = $k;
            if ($this->order == "names") {
                $this->finalcatalog = array_combine($final_name, $finalnumber); //array me onomata kai noumera se morfh pou edwse o xrhsths
            } else {
                $this->finalcatalog = array_combine($finalnumber, $final_name); //array me onomata kai noumera se morfh pou edwse o xrhsths
            }
        }*/
        $this->Catalogs = new ArrayObject();
        $this->Catalogs["rawcatalog"] = $this->raw_catalog;
        $this->Catalogs["validcatalog"] = $this->valid_catalog;
        $this->Catalogs["invalidcatalog"] = $this->invalid_catalog;
        $this->Catalogs["validatedcatalog"] = $this->validated_catalog;
        return $this->Catalogs;
        //return array($this->finalcatalog, $this->invalid_catalog);
    }

    //function to create type of catalogs
    function create_catalogs($catalog)
    {
        $this->valid_catalog = new stdClass;
        $this->invalid_catalog = new stdClass;
        $this->validated_catalog = new stdClass;
        foreach ($catalog as $k => $v) {
            if ($v == "invalid number") {
                $this->invalid_catalog->name[] = $k;
                $this->invalid_catalog->number = $this->invalid_number;
            } elseif($k == ''){
                $this->invalid_catalog->invalid_name[] = $k;
                $this->invalid_catalog->number_of_invalid_name[] = $v;
            }
            else {
                $this->valid_catalog->name[] = $k;
                $this->valid_catalog->number[] = $v;
                $this->validated_catalog->validated_name[]= $k;
                $this->validated_catalog->validated_number[]= $this->transformTelNum($v, $this->outputformat);;
            }
        }
        $this->invalid_catalog->invalidline =  $this->invalid_line;
    }


    //function gia na gurnaei invalid number an to noumero den exei swsth morfh
    function validateTelNum($num)
    {
        preg_match_all($this->re, $num, $matches, PREG_SET_ORDER, 0);
        if (empty($matches)) {
            $this->invalid_number[] = $num;
            return "invalid number";
        } else {
            return $num;
        }
    }

    //function pou dexetai string kai gurnaei an einai noumero/onoma/keno
    function getType($input)
    {
        if (is_numeric(str_replace(" ", "", trim($input)))) {
            return "number";
        } elseif (strpos($input, "+") !== false) {
            return "number";
        } elseif (is_string($input) && !empty($input)) {
            return "name";
        } elseif ($input == '') {
            return "empty";
        }
    }

    //function gia na gurnaei to noumero sumfwna me to format pou exei orisei o xrhsths
    public function transformTelNum($num, $format)
    {
        if ($format == "00") {
            if (strpos($num, "+") !== false) {
                return substr_replace($num, '00', 0, -12);
            } elseif (strpos($num, "00") !== false) {
                return $num;
            } else {
                return "0030" . $num;
            }
        } elseif ($format == "+") {
            if (strpos($num, '+') !== false) {
                return $num;
            } elseif (strpos($num, '00') !== false) {
                return substr_replace($num, '+', 0, -12);
            } else {
                return "+30" . $num;
            }
        } else {
            if (strpos($num, "+") !== false) {
                return $num;
            } elseif (strpos($num, "00") !== false) {
                return substr_replace($num, '+', 0, -12);
            } else {
                return "+30" . $num;
            }
        }
    }
}
