<?php

require_once __DIR__ . '/../vendor/autoload.php';


use App\Services\XmlProcessor;

$directory =  __DIR__ . '/../data/xml_files'; // Path to the XML files directory
$processedDirectory = __DIR__ . '/../data/xml_files_processed';
$processor = new XmlProcessor($directory, $processedDirectory);

echo "<pre>";
var_dump($processor->process());
