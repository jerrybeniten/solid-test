<?php

namespace App\Config;


class Config
{
    public static function getDirectories(): array
    {
        return [
            'xmlDirectory' => dirname(__DIR__, 2) . '/data/xml_files',
            'processedXmlDirectory' => dirname(__DIR__, 2) . '/data/xml_files_processed',
        ];
    }
}