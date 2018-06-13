<?php

namespace Ylab\Ddata\Data;

use Bitrix\Main\Localization\Loc;
use Ylab\Ddata\Interfaces\DataUnitClass;
use Bitrix\Main\HttpRequest;
use Ylab\Ddata\Helpers;

Loc::loadMessages(__FILE__);

/**
 * Class RandomPicture
 * @package Ylab\Ddata\Data
 */
class RandomPicture extends DataUnitClass
{
    protected static $checkStaticMethod = true;

    protected $iWidth = 100;
    protected $iHeight = 100;

    /**
     * RandomPicture constructor.
     * @param $sProfileID
     * @param $sFieldCode
     * @param $sGeneratorID
     * @throws \Bitrix\Main\ArgumentException
     * @throws \Bitrix\Main\ObjectPropertyException
     * @throws \Bitrix\Main\SystemException
     */
    public function __construct($sProfileID, $sFieldCode, $sGeneratorID)
    {
        self::$checkStaticMethod = false;
        parent::__construct($sProfileID, $sFieldCode, $sGeneratorID);

        if (!empty($this->options['width'])) {
            $this->iWidth = $this->options['width'];
        }

        if (!empty($this->options['height'])) {
            $this->iHeight = $this->options['height'];
        }
    }

    /**
     * @return array
     */
    public static function getDescription()
    {
        return [
            "ID" => "picture.file.unit",
            "NAME" => Loc::getMessage("YLAB_DDATA_DATA_UNIT_PICTURE_NAME"),
            "DESCRIPTION" => Loc::getMessage('YLAB_DDATA_DATA_UNIT_PICTURE_DESCRIPTION'),
            "TYPE" => "file",
            "CLASS" => __CLASS__
        ];
    }

    /**
     * @param HttpRequest $request
     * @return mixed|string
     * @throws \Bitrix\Main\ArgumentException
     * @throws \Bitrix\Main\ObjectPropertyException
     * @throws \Bitrix\Main\SystemException
     */
    public static function getOptionForm(HttpRequest $request)
    {
        $arRequest = $request->toArray();
        $arOptions = $arRequest['option'];
        $sGeneratorID = $request->get('generator');
        $sFieldID = $request->get('field');
        $sProfileID = $request->get('profile_id');
        $sPropertyName = $request->get('property-name');
        $arOptions = array_merge(self::getOptions($sGeneratorID, $sProfileID, $sFieldID), $arOptions);

        ob_start();
        include Helpers::getModulePath() . '/admin/fragments/random_picture_settings_form.php';
        $tpl = ob_get_contents();
        ob_end_clean();

        return $tpl;
    }

    /**
     * @param HttpRequest $request
     * @return bool
     */
    public static function isValidateOptions(HttpRequest $request)
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
     * @return string
     * @throws \Exception
     */
    public function getValue()
    {
        if (!self::$checkStaticMethod) {
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
        } else {
            throw new \Exception(Loc::getMessage('YLAB_DDATA_DATA_UNIT_PICTURE_EXCEPTION_STATIC'));
        }
    }
}