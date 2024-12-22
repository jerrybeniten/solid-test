<?php

namespace App\Config;


class Config
{
    public static function getDirectories(): array
    {
        return [
            'xmlDirectory' => dirname(__DIR__, 2) . '/data/xml_files',
            'processedXmlDirectory' => dirname(__DIR__, 2) . '/data/xml_files_processed',
            'errorLog' => dirname(__DIR__, 2) . '/logs/xml-errors.log',
        ];
    }

    public static function getDbConfig(): array
    {
        return [
            'host' => 'postgres',
            'database' => 'mydb',
            'username' => 'docker',
            'password' => 'docker'
        ];
    }
}
