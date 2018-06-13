<?php

namespace Ylab\Ddata\Data;

use Bitrix\Main\Localization\Loc;
use Ylab\Ddata\Interfaces\DataUnitClass;
use Bitrix\Main\HttpRequest;
use Ylab\Ddata\Helpers;

Loc::loadLanguageFile(__FILE__);

/**
 * Class RandomString
 * @package Ylab\Ddata\Data
 */
class RandomString extends DataUnitClass
{
    private static $bCheckStaticMethod = true;

    protected $sLang = "EN";
    protected $iMinLength = 6;
    protected $iMaxLength = 255;
    protected $sRegister = 'N';
    protected $sUserString = '';

    /**
     * RandomString constructor.
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

        if (!empty($this->options['lang'])) {
            $this->sLang = $this->options['lang'];
        }

        if (!empty($this->options['min'])) {
            $this->iMinLength = $this->options['min'];
        }

        if (!empty($this->options['max'])) {
            $this->iMaxLength = $this->options['max'];
        }

        if (!empty($this->options['register'])) {
            $this->sRegister = $this->options['register'];
        }

        if (!empty($this->options['user-string'])) {
            $this->sUserString = $this->options['user-string'];
        }
    }

    /**
     * @return array
     */
    public static function getDescription()
    {
        return [
            "ID" => "random.string.unit",
            "NAME" => Loc::getMessage('YLAB_DDATA_DATA_UNIT_STRING_NAME'),
            "DESCRIPTION" => Loc::getMessage('YLAB_DDATA_DATA_UNIT_STRING_DESCRIPTION'),
            "TYPE" => "string",
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
        include Helpers::getModulePath() . "/admin/fragments/random_string_settings_form.php";
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
        $bFlag = false;

        if (!empty($arPrepareRequest['lang'])) {
            $bFlag = true;
        } else {
            return false;
        }

        if (!empty($arPrepareRequest['min']) && !empty($arPrepareRequest['max'])) {
            $iMin = (int)$arPrepareRequest['min'];
            $iMax = (int)$arPrepareRequest['max'];

            if ($iMax > $iMin) {
                $bFlag = true;
            }
        } else {
            return false;
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
            if ($this->sUserString) {
                return $this->sUserString;
            }

            $sLang = $this->sLang;
            $iMinLength = $this->iMinLength;
            $iMaxLength = $this->iMaxLength;
            $sRegister = $this->sRegister;

            $sCyrillic = "абвгдеёжзийклмнопрстуфхцчшщъыьэюяАБВГДЕЁЖЗИЙКЛМНОПРСТУФХЦЧШЩЪЫЬЭЮЯ";
            $sLatin = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ";

            if ($sLang == 'RU') {
                $iSize = strlen($sCyrillic) - 1;
                $iStringLength = rand($iMinLength, $iMaxLength);
                $sResult = '';

                while (strlen($sResult) < $iStringLength) {
                    $sResult .= mb_substr($sCyrillic, rand(0, $iSize - 1), 1);
                }

                switch ($sRegister) {
                    case 'UP':
                        $sResult = strtoupper($sResult);
                        break;
                    case 'LOW':
                        $sResult = strtolower($sResult);
                        break;
                    case 'NO':
                        break;
                }

                return $sResult;
            } elseif ($sLang == 'EN') {
                $iSize = strlen($sLatin) - 1;
                $iStringLength = rand($iMinLength, $iMaxLength);
                $sResult = '';

                while (strlen($sResult) < $iStringLength) {
                    $sResult .= $sLatin[rand(0, $iSize)];
                }

                switch ($sRegister) {
                    case 'UP':
                        $sResult = strtoupper($sResult);
                        break;
                    case 'LOW':
                        $sResult = strtolower($sResult);
                        break;
                    case 'NO':
                        break;
                }

                return $sResult;
            }
        } else {
            throw new \Exception(Loc::getMessage('YLAB_DDATA_DATA_UNIT_STRING_EXCEPTION_STATIC'));
        }
    }
}