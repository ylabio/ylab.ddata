<?php

namespace Ylab\Ddata\Data;

use Bitrix\Main\Localization\Loc;
use Ylab\Ddata\Helpers;

Loc::loadMessages(__FILE__);

/**
 * Генерация изображения для ORM
 *
 * Class RandomOrmPicture
 * @package Ylab\Ddata\Data
 */
class RandomOrmPicture extends RandomPicture
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
            'ID' => 'picture.file.unit.id',
            'NAME' => Loc::getMessage('YLAB_DDATA_DATA_UNIT_PICTURE_ORM_NAME'),
            'DESCRIPTION' => Loc::getMessage('YLAB_DDATA_DATA_UNIT_PICTURE_ORM_DESCRIPTION'),
            'TYPE' => 'file.orm',
            'CLASS' => __CLASS__
        ];
    }

    /**
     * Возвращает случайную запись соответствующего типа
     *
     * @return int|string
     * @throws \Exception
     */
    public function getValue()
    {
        $iWidth = $this->iWidth;
        $iHeight = $this->iHeight;

        ob_start();
        include Helpers::getModulePath() . '/admin/fragments/random_picture_image.php';
        $image = ob_get_contents();
        ob_end_clean();

        $arFile = [
            'content' => $image,
            'name' => 'random_picture_image.png',
            'type' => 'image/png'
        ];
        $iImageId = \CFile::SaveFile($arFile, "demo");

        if ($iImageId) {
            return $iImageId;
        }

        return null;
    }
}