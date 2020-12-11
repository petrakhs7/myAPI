<?php

$log_file = "./testerror.log";
ini_set("log_errors", TRUE);
ini_set('error_log', $log_file);
require_once "parseFile.php";
require_once "CatalogManagement.php";
require_once "CsvImporter.php";

//$inputnumberformat = "+"; 
//$inputfilename = "phone_catalog.txt";
//$inputfiledelimmeter = ";";
//$order = "numbers"; // OR "names"

$phonecatag = new parseFile();
$lines = $phonecatag->readFile("phone_catalog.txt", ";");
echo ("<pre>");
print_r($lines);

$phonemanage = new CatalogManagement("+");
$final = $phonemanage->manageCatalog($lines, "names");
foreach ($final[0] as $k => $v) {
    $phonecatag->writeFile("final_catalog/", "final_catalog", $k . ";" . $v, ".csv");
}
foreach ($final[1] as $k => $v) {
    $phonecatag->writeFile("invalid-numbers/", "invalid_numbers", $k . ";" . $v, ".csv");
}
echo ("<pre>");
print_r($final);


$importer = new CsvImporter("phone_catalog.txt", ";");
$data = $importer->get();
print_r($data);


