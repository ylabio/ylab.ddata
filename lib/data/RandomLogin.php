<?php

namespace Ylab\Ddata\Data;

use Bitrix\Main\Localization\Loc;
use Ylab\Ddata\Interfaces\DataUnitClass;
use Bitrix\Main\HttpRequest;
use Ylab\Ddata\Helpers;

Loc::loadMessages(__FILE__);

/**
 * Class RandomLogin
 * @package Ylab\Ddata\Data
 */
class RandomLogin extends DataUnitClass
{
    private static $bCheckStaticMethod = true;

    protected $iLength = 12;

    /**
     * RandomLogin constructor.
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

        if (!empty($this->options['length'])) {
            $this->iLength = $this->options['length'];
        }
    }

    /**
     * @return array
     */
    public static function getDescription()
    {
        return [
            "ID" => "login.string.unit",
            "NAME" => Loc::getMessage("YLAB_DDATA_DATA_UNIT_LOGIN_NAME"),
            "DESCRIPTION" => Loc::getMessage('YLAB_DDATA_DATA_UNIT_LOGIN_DESCRIPTION'),
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
        $arOptions = (array)$arRequest['option'];
        $sGeneratorID = $request->get('generator');
        $sFieldID = $request->get('field');
        $sProfileID = $request->get('profile_id');
        $sPropertyName = $request->get('property-name');

        $arOptions = array_merge(self::getOptions($sGeneratorID, $sProfileID, $sFieldID), $arOptions);
        ob_start();

        include Helpers::getModulePath() . "/admin/fragments/random_login_settings_form.php";
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
        $iLength = (int)$arPrepareRequest['length'];
        $bFlag = false;

        if (!empty($iLength) && is_int($iLength) && $iLength >= 6 && $iLength < 24) {
            $bFlag = true;
        } else {
            return false;
        }

        return $bFlag;
    }

    /**
     * @return array|string
     * @throws \Exception
     */
    public function getValue()
    {
        if (!self::$bCheckStaticMethod) {
            $arSymbols = array('aeiouy', 'bcdfghjklmnpqrstvwxz');
            $iLength = $this->iLength;
            $arReturn = array();
            foreach ($arSymbols as $k => $v) {
                $arSymbols[$k] = str_split($v);
            }

            for ($i = 0; $i < $iLength; $i++) {
                while (true) {
                    $symbolX = mt_rand(0, sizeof($arSymbols) - 1);
                    $symbolY = mt_rand(0, sizeof($arSymbols[$symbolX]) - 1);
                    if ($i > 0 && in_array($arReturn[$i - 1], $arSymbols[$symbolX])) {
                        continue;
                    }
                    $arReturn[] = $arSymbols[$symbolX][$symbolY];
                    break;
                }
            }
            $arReturn = implode('', $arReturn);

            return $arReturn;
        } else {
            throw new \Exception(Loc::getMessage('YLAB_DDATA_DATA_UNIT_LOGIN_EXCEPTION'));
        }
    }
}