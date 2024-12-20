<?php

require_once '../vendor/autoload.php';

use App\Services\XmlProcessor;

$directory = '../data/xml_files'; // Path to the XML files directory
$processor = new XmlProcessor($directory);

echo "<pre>";
var_dump($processor->process());
