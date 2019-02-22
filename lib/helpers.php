<?php

namespace Ylab\Ddata;

/**
 * Class Helpers
 * @package Ylab\Ddata
 */
class Helpers
{
    /**
     * @param bool $notDocumentRoot
     * @return mixed
     */
    public static function getModulePath($notDocumentRoot = false)
    {
        $oModule = \CModule::CreateModuleObject('ylab.ddata');

        return $oModule->GetPath($notDocumentRoot);
    }

    /**
     * Метод для получения данных из файла .csv
     * @param $sFile
     * @return array
     */
    public static function parseCSV($sFile)
    {
        if (is_uploaded_file($sFile)) {
            $arResult = [];
            $handle = fopen($sFile, 'r');
            while (($arData = fgetcsv($handle, 1000, ';')) !== false) {
                $arResult[] = $arData;
            }
            fclose($handle);
        }

        return $arResult;
    }

    /**
     * Метод для получения данных из файла
     * @param $sFilePath
     * @return array
     * @throws \Exception
     */
    public static function parseFile($sFilePath)
    {
        if (file_exists($sFilePath)) {
            $arResult = [];
            $handle = fopen($sFilePath, 'r
            ');
            while (($arData = fgets($handle, 4096)) !== false) {
                $arResult[] = $arData;
            }
            fclose($handle);
        } else {
            throw new \Exception('Не найден указанный файл');
        }

        return $arResult;
    }
}