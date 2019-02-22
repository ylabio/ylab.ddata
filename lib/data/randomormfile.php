<?php

namespace Ylab\Ddata\Data;

use Bitrix\Main\Localization\Loc;

Loc::loadMessages(__FILE__);

/**
 * Генерация файла для ORM
 *
 * Class RandomOrmFile
 * @package Ylab\Ddata\Data
 */
class RandomOrmFile extends RandomFile
{
    public function __construct(string $sProfileID = '', string $sFieldCode = '', string $sGeneratorID = '')
    {
        parent::__construct($sProfileID, $sFieldCode, $sGeneratorID);
    }

    /**
     * Метод возврящает массив описывающий тип данных. ID, Имя, scalar type php
     *
     * @return array
     */
    public  function getDescription()
    {
        return [
            'ID' => 'file.unit.id',
            'NAME' => Loc::getMessage('YLAB_DDATA_DATA_UNIT_FILE_ORM_NAME'),
            'DESCRIPTION' => Loc::getMessage('YLAB_DDATA_DATA_UNIT_FILE_ORM_DESCRIPTION'),
            'TYPE' => 'file.orm',
            'CLASS' => __CLASS__
        ];
    }

    /**
     * Возвращает случайную запись соответствующего типа
     *
     * @return int|mixed|string
     * @throws \Exception
     */
    public function getValue()
    {
        $arFiles = glob($this->sPath . "*.*");
        $sResult = array_rand($arFiles);
        $arFile = \CFile::MakeFileArray($arFiles[$sResult]);
        $iFileId = \CFile::SaveFile($arFile, "demo");

        if ($iFileId) {
            return $iFileId;
        }
    }
}