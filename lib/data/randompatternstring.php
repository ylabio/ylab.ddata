<?php

namespace Ylab\Ddata\Data;

use Bitrix\Main\Localization\Loc;
use Ylab\Ddata\Interfaces\DataUnitClass;
use Bitrix\Main\HttpRequest;
use Ylab\Ddata\Helpers;

Loc::loadLanguageFile(__FILE__);

/**
 * Class RandomPatternString
 * @package Ylab\Ddata\Data
 */
class RandomPatternString extends DataUnitClass
{
    public static $bCheckStaticMethod = true;
    public static $sPattern = "/{([а-яА-ЯёЁa-zA-Z0-9|]+)}/ui";
    public static $sExample = "Стол {красный|синий|белый} из {дерева|стекла|пластика}";
    public static $iMaxLength = 255;

    /**
     * RandomPatternString constructor.
     * @param $sProfileID
     * @param $sFieldCode
     * @param $sGeneratorID
     * @throws \Bitrix\Main\ArgumentException
     * @throws \Bitrix\Main\ObjectPropertyException
     * @throws \Bitrix\Main\SystemException
     */
    public function __construct($sProfileID, $sFieldCode, $sGeneratorID)
    {
        self::$bCheckStaticMethod = false;
        parent::__construct($sProfileID, $sFieldCode, $sGeneratorID);
    }

    /**
     * @return array
     */
    public static function getDescription()
    {
        return [
            "ID" => "random.patternstring.unit",
            "NAME" => Loc::getMessage('YLAB_DDATA_DATA_UNIT_PATTERN_STRING_NAME'),
            "DESCRIPTION" => Loc::getMessage('YLAB_DDATA_DATA_UNIT_PATTERN_STRING_DESCRIPTION'),
            "TYPE" => "string",
            "CLASS" => __CLASS__
        ];
    }

    /**
     * @param HttpRequest $oRequest
     * @return mixed|string
     * @throws \Bitrix\Main\ArgumentException
     * @throws \Bitrix\Main\ObjectPropertyException
     * @throws \Bitrix\Main\SystemException
     */
    public static function getOptionForm(HttpRequest $oRequest)
    {
        $arRequest = $oRequest->toArray();
        $arOptions = (array)$arRequest['option'];
        $sGeneratorID = $oRequest->get('generator');
        $sFieldID = $oRequest->get('field');
        $sProfileID = $oRequest->get('profile_id');
        $sPropertyName = $oRequest->get('property-name');

        $arOptions = array_merge(self::getOptions($sGeneratorID, $sProfileID, $sFieldID), $arOptions);
        ob_start();
        include Helpers::getModulePath() . "/admin/fragments/random_pattern_string_settings_form.php";
        $tpl = ob_get_contents();
        ob_end_clean();

        return $tpl;
    }

    /**
     * @param HttpRequest $oRequest
     * @return bool
     */
    public static function isValidateOptions(HttpRequest $oRequest)
    {
        $bFlag = false;
        $arPrepareRequest = $oRequest->get('option');

        if ($arPrepareRequest['pattern'] && strlen($arPrepareRequest['pattern']) < self::$iMaxLength) {
            $bFlag = true;
        }

        return $bFlag;
    }

    /**
     * @return string
     * @throws \Exception
     */
    public function getValue()
    {
        if (!self::$bCheckStaticMethod) {

            if (!empty($this->options['pattern'])) {
                $sRes = $this->options['pattern'];
            } else {
                $sRes = self::$sExample;
            }

            if (preg_match_all(self::$sPattern, $sRes, $matches)) {

                if ($matches[1]) {

                    foreach ($matches[1] as $sKey => $arStr) {
                        $sStr[$sKey] = explode('|', $arStr);

                        $iCount = count($sStr[$sKey]);
                        $iRand = $sStr[$sKey][rand(0, --$iCount)];
                        $sRes = str_replace('{' . $arStr . '}', $iRand, $sRes);
                    }
                }
                return $sRes;
            } else {
                return false;
            }

        } else {
            throw new \Exception(Loc::getMessage('YLAB_DDATA_DATA_UNIT_PATTERN_STRING_EXCEPTION_STATIC'));
        }
    }
}