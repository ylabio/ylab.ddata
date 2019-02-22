<?php

namespace Ylab\Ddata\Data;

use Bitrix\Main\Localization\Loc;
use Ylab\Ddata\Interfaces\DataUnitClass;
use Bitrix\Main\HttpRequest;
use Ylab\Ddata\Helpers;

Loc::loadMessages(__FILE__);

/**
 * Генерация случайного изображения
 *
 * Class RandomPicture
 * @package Ylab\Ddata\Data
 */
class RandomPicture extends DataUnitClass
{
    protected $iWidth = 100;

    /** @var int Высота изображения по умолчанию */
    protected $iHeight = 100;

    /**
     * RandomPicture constructor.
     * @param $sProfileID - ID профиля
     * @param $sFieldCode - Симфольный код свойства
     * @param $sGeneratorID - ID уже сохраненного генератора
     * @throws \Bitrix\Main\ArgumentException
     * @throws \Bitrix\Main\ObjectPropertyException
     * @throws \Bitrix\Main\SystemException
     */
    public function __construct(string $sProfileID = '', string $sFieldCode = '', string $sGeneratorID = '')
    {
        parent::__construct($sProfileID, $sFieldCode, $sGeneratorID);

        if (!empty($this->options['width'])) {
            $this->iWidth = $this->options['width'];
        }

        if (!empty($this->options['height'])) {
            $this->iHeight = $this->options['height'];
        }
    }

    /**
     * Метод возврящает массив описывающий тип данных. ID, Имя, scalar type php
     *
     * @return array
     */
    public  function getDescription()
    {
        return [
            'ID' => 'picture.file.unit',
            'NAME' => Loc::getMessage('YLAB_DDATA_DATA_UNIT_PICTURE_NAME'),
            'DESCRIPTION' => Loc::getMessage('YLAB_DDATA_DATA_UNIT_PICTURE_DESCRIPTION'),
            'TYPE' => 'file',
            'CLASS' => __CLASS__
        ];
    }

    /**
     * Метод возвращает html строку формы с настройкой генератора если таковые необходимы
     *
     * @param HttpRequest $request
     * @return false|mixed|string
     */
    public function getOptionForm(HttpRequest $request)
    {
        $sGeneratorID = $request->get('generator');
        $sProfileID = $request->get('profile_id');
        $sPropertyName = $request->get('property-name');

        ob_start();
        include Helpers::getModulePath() . '/admin/fragments/random_picture_settings_form.php';
        $tpl = ob_get_contents();
        ob_end_clean();

        return $tpl;
    }

    /**
     * Метод проверяет на валидность данные настройки генератора
     *
     * @param HttpRequest $request
     * @return bool
     */
    public  function isValidateOptions(HttpRequest $request)
    {
        $arPrepareRequest = $request->get('option');

        if ($arPrepareRequest) {
            $iWidth = (int)$arPrepareRequest['width'];
            $iHeight = (int)$arPrepareRequest['height'];

            if ($iWidth > 50 && $iHeight > 50) {
                return true;
            }
        }

        return false;
    }

    /**
     * Возвращает случайную запись соответствующего типа
     *
     * @return string
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
            return \CFile::MakeFileArray($iImageId);
        }
    }
}