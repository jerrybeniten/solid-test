<?php

use DB\Database;
use Lib\XmlHelper\XmlFileReader;
use Lib\XmlHelper\XmlSyncService;

require_once '../vendor/autoload.php';

$host = 'postgres';
$dbname = 'mydb';
$user = 'docker';
$password = 'docker';
$directory = '../data/xml_files';

$db = new Database($host, $dbname, $user, $password);
$iterator = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($directory));
$xmlFileReader = new XmlFileReader($iterator);
$xmlSyncService = new XmlSyncService($db, $xmlFileReader);

$xmlSyncService->syncXmlData($directory);
echo "XML data synced successfully.";
