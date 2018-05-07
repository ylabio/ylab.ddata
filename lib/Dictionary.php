<?php

namespace Ylab\Ddata;

use Bitrix\Main\FileTable;

/**
 * Class Dictionary
 * @package Ylab\Ddata
 */
class Dictionary
{
    /**
     * @param string $sFilePath
     * @param string $sOriginName
     * @param string $sDataId
     * @return int
     */
    public static function add($sFilePath, $sOriginName, $sDataId)
    {
        $iFileID = 0;
        if (is_uploaded_file($sFilePath)) {
            $arLoadFile = [
                'name' => $sOriginName,
                'size' => filesize($sFilePath),
                'tmp_name' => $sFilePath,
                'MODULE_ID' => 'ylab.ddata',
                'description' => $sDataId
            ];
            $iFileID = \CFile::SaveFile($arLoadFile, 'ylab/dictionaries');
            if ($iFileID > 0) {
                return $iFileID;
            }
        }

        return $iFileID;
    }

    /**
     * @param array $parameters
     * @return mixed
     * @throws \Bitrix\Main\ArgumentException
     */
    public static function getList($parameters = [])
    {
        if (isset($parameters['DATA_ID'])) {
            $parameters['DESCRIPTION'] = $parameters['DATA_ID'];
            unset($parameters['DATA_ID']);
        }

        $arFiles = [];
        $oRes = FileTable::getList([
            'select' => ['ID', 'FILE_SIZE', 'ORIGINAL_NAME', 'FILE_NAME', 'DESCRIPTION', 'HANDLER_ID', 'SUBDIR'],
            'filter' => array_merge(['MODULE_ID' => 'ylab.ddata'], $parameters)
        ]);

        while ($arRes = $oRes->fetch()) {
            $arFiles[] = [
                'ID' => $arRes['ID'],
                'SRC' => "/upload/" . $arRes['SUBDIR'] . "/" . $arRes['FILE_NAME'],
                'FILE_SIZE' => $arRes['FILE_SIZE'],
                'ORIGINAL_NAME' => $arRes['ORIGINAL_NAME'],
                'DESCRIPTION' => $arRes['DESCRIPTION'],
                'HANDLER_ID' => $arRes['HANDLER_ID']
            ];
        }

        return $arFiles;
    }

    /**
     * @param array $parameters
     * @throws \Bitrix\Main\ArgumentException
     */
    public static function delete($parameters = [])
    {
        $arFiles = self::getList($parameters);

        foreach ($arFiles as $arFile) {
            \CFile::Delete($arFile['ID']);
        }
    }
}