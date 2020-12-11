<?php

class CatalogManagement
{
    private $numberformat;
    private $re;
    private $temp;
    private $catalog = array("names" => array(), "numbers" => array());
    private $rawcatalog = array();
    private $tempcatalog = array();
    private $invalidcatalog = array();
    private $finalcatalog = array();

    function __construct($numberformat = "+")
    {
        $this->numberformat = $numberformat;
        $this->re = '/^((\+\d{2})(\d{10})$)|^((00\d{2})(\d{10})$)|(^\d{10}$)/m';
    }


    //function pou dexetai string kai gurnaei an einai noumero/onoma/keno
    function type($input)
    {
        if (is_numeric($input)) {
            return "number";
        } elseif (strpos($input, "+") !== false) {
            return "number";
        } elseif (is_string($input) && !empty($input)) {
            return "name";
        } elseif ($input == '') {
            return "empty";
        }
    }

    public function manageCatalog($input, $order = "names")
    {
        foreach ($input as $line) {
            foreach ($line as $k => $v) {
                $v = str_replace(" ", "", trim($v));
                if ($this->type($v) == "name") {
                    $name[] = $v;
                } elseif($this->type($v) == "number") {
                    $number[] = $v;
                    $validatednumber[] = $this->validateTelNum($v);
                }
                elseif($this->type($v) == "empty"){
                    if ($this->type($this->temp) == "name"){
                        $number[] = $v;
                        $validatednumber[] = $this->validateTelNum($v);
                    }
                    else {
                        $name[] = $v;
                    }
                }
                $this->temp = $v;
            }
        }
        $this->rawcatalog = array_combine($name, $number); // array me ola ta onomata kai ta noumera opws erxetai apo to arxeio
        $this->tempcatalog = array_combine($name, $validatednumber); // array me ola ta onomata kai "invalid number" opou einai lathos to noumero 
        foreach (array_keys($this->tempcatalog, "invalid number") as $k => $v) {
            $invalidname[] = $v;
            $invalidnumber[] = $this->rawcatalog[$v];
            $this->invalidcatalog = array_combine($invalidname, $invalidnumber); //aray me onomata kai "invalid number" mono
            unset($this->tempcatalog[$v]); // kopsimo twn "invalid number"
        }
        foreach ($this->tempcatalog as $k => $v) {
            if ($k == ''){
                unset($this->tempcatalog[$k]);
                $this->invalidcatalog += array($k => $v);
            }
        }
        foreach ($this->tempcatalog as $k => $v) {
            $finalnumber[] = $this->transformTelNum($v, $this->numberformat);
            $final_name[] = $k;
            if ($order == "names") {
                $this->finalcatalog = array_combine($final_name, $finalnumber); //array me onomata kai noumera se morfh pou edwse o xrhsths
            } else {
                $this->finalcatalog = array_combine($finalnumber, $final_name); //array me onomata kai noumera se morfh pou edwse o xrhsths
            }
        }
        return array($this->finalcatalog, $this->invalidcatalog);
    }

    //function gia na gurnaei invalid number an to noumero den exei swsth morfh
    function validateTelNum($num)
    {
        preg_match_all($this->re, $num, $matches, PREG_SET_ORDER, 0);
        if (empty($matches)) {
            return "invalid number";
        } else {
            return $num;
        }
    }

    //function gia na gurnaei to noumero sumfwna me to format pou exei orisei o xrhsths
    function transformTelNum($num, $format)
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
