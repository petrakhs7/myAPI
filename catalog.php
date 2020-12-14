<?php

$log_file = "./testerror.log";
ini_set("log_errors", TRUE);
ini_set('error_log', $log_file);
require_once "parseFile.php";
require_once "CatalogManagement.php";
require_once "fileAsCSV.php";


$file = new fileAsCSV("phone_catalog.txt",false, ";");
$data = $file->get();
echo ("<pre>");
//print_r($data);

$phonemanage = new CatalogManagement($data,"00");
$catalogs = $phonemanage->manageCatalog();
echo ("<pre>");
print_r($catalogs);
//print_r($catalogs["validatedcatalog"]);
//print_r($catalogs["validatedcatalog"]->validated_name);
$order = "numbers_first";
foreach($catalogs["validatedcatalog"]->validated_name as $k=>$v) {
    if ($order =="names_first"){
        $file->writeFile("final_catalog/", "final_catalog", $v . ";" . $catalogs["validatedcatalog"]->validated_number[$k], ".csv");
    }
    else{
        $file->writeFile("final_catalog/", "final_catalog", $catalogs["validatedcatalog"]->validated_number[$k] . ";" . $v, ".csv");
    }
    
}
foreach($catalogs["invalidcatalog"]->name as $k=>$v) {
    $file->writeFile("invalid-numbers/", "invalid_numbers", $v . ";" . $catalogs["invalidcatalog"]->number[$k], ".csv");
}
foreach($catalogs["invalidcatalog"]->invalid_name as $k=>$v) {
    $file->writeFile("invalid-numbers/", "invalid_numbers", $v . ";" . $catalogs["invalidcatalog"]->number_of_invalid_name[$k], ".csv");
}
foreach($catalogs["invalidcatalog"]->invalidline as $k=>$v) {
    $file->writeFile("invalid-numbers/", "invalid_numbers",$v, ".csv");
}
//$file->writeFile("invalid-numbers/", "invalid_numbers",$catalogs["invalidcatalog"]->invalidline , ".csv");
//$file->writeFile("final_catalog/", "final_catalog", $catalogs["validatedcatalog"]->validated_name . ";" . $catalogs["validatedcatalog"]->validated_number, ".csv");
/*foreach ($final[0] as $k => $v) {
    $file->writeFile("final_catalog/", "final_catalog", $k . ";" . $v, ".csv");
}*/
/*foreach ($final[1] as $k => $v) {
    $file->writeFile("invalid-numbers/", "invalid_numbers", $k . ";" . $v, ".csv");
}*/




