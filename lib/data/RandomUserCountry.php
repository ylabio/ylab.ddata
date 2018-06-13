<?php

namespace Ylab\Ddata\Data;

use Bitrix\Main\Localization\Loc;
use Bitrix\Main\HttpRequest;
use Ylab\Ddata\Interfaces\DataUnitClass;
use Ylab\Ddata\Helpers;

Loc::loadMessages(__FILE__);

/**
 * Class RandomUserCountry
 * @package Ylab\Ddata\Data
 */
class RandomUserCountry extends DataUnitClass
{
    private static $checkStaticMethod = true;

    protected $sRandom = 'N';
    protected $arSelectedCountries = [1];

    /**
     * RandomUserCountry constructor.
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

        if (!empty($this->options['random'])) {
            $this->sRandom = $this->options['random'];
        }

        if (!empty($this->options['selected-countries'])) {
            $this->arSelectedCountries = $this->options['selected-countries'];
        }
    }

    /**
     * @return array
     */
    public static function getDescription()
    {
        return [
            "ID" => "user.country",
            "NAME" => Loc::getMessage("YLAB_DDATA_DATA_COUNTRY_NAME"),
            "DESCRIPTION" => Loc::getMessage('YLAB_DDATA_DATA_COUNTRY_DESCRIPTION'),
            "TYPE" => "user.country",
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

        $arCountries = GetCountryArray('RU');
        $arCountries = array_combine($arCountries['reference_id'], $arCountries['reference']);

        $arOptions = array_merge(self::getOptions($sGeneratorID, $sProfileID, $sFieldID), $arOptions);

        ob_start();
        include Helpers::getModulePath() . '/admin/fragments/random_country_settings_form.php';
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
            $sRandom = $arPrepareRequest['random'];
            $arSelectedCountries = $arPrepareRequest['selected-countries'];

            if (!empty($sRandom) || !empty($arSelectedCountries) && is_array($arSelectedCountries)) {
                return true;
            }
        }

        return false;
    }

    /**
     * @return mixed
     * @throws \Exception
     */
    public function getValue()
    {
        if (!self::$checkStaticMethod) {
            if ($this->sRandom === 'Y') {
                $arCountries = GetCountryArray('RU');
                $sResult = array_rand($arCountries['reference_id']);

                return $arCountries['reference_id'][$sResult];
            } else {
                $sResult = array_rand($this->arSelectedCountries);

                return $this->arSelectedCountries[$sResult];
            }
        } else {
            throw new \Exception(Loc::getMessage('YLAB_DDATA_DATA_COUNTRY_EXCEPTION_STATIC'));
        }
    }
}