<?php

namespace Ylab\Ddata\Data;

use Bitrix\Main\Localization\Loc;
use Ylab\Ddata\Interfaces\DataUnitClass;
use Bitrix\Main\HttpRequest;
use Ylab\Ddata\Helpers;

Loc::loadMessages(__FILE__);

/**
 * Class RandomInteger
 * @package Ylab\Ddata\Data
 */
class RandomInteger extends DataUnitClass
{
    private static $bCheckStaticMethod = true;

    protected $iMin = 0;
    protected $iMax = 9999;

    /**
     * RandomInteger constructor.
     * @param $sProfileID
     * @param $sFieldCode
     * @param $sGeneratorID
     * @throws \Bitrix\Main\ArgumentException
     */
    public function __construct($sProfileID, $sFieldCode, $sGeneratorID)
    {
        self::$bCheckStaticMethod = false;

        parent::__construct($sProfileID, $sFieldCode, $sGeneratorID);

        if (!empty($this->options['min'])) {
            $this->iMin = $this->options['min'];
        }

        if (!empty($this->options['max'])) {
            $this->iMax = $this->options['max'];
        }
    }

    /**
     * @return array
     */
    public static function getDescription()
    {
        return [
            "ID" => "random.integer.unit",
            "NAME" => Loc::getMessage("YLAB_DDATA_DATA_UNIT_INTEGER_NAME"),
            "DESCRIPTION" => Loc::getMessage('YLAB_DDATA_DATA_UNIT_INTEGER_DESCRIPTION'),
            "TYPE" => "integer",
            "CLASS" => __CLASS__
        ];
    }

    /**
     * @param HttpRequest $request
     * @return string
     * @throws \Bitrix\Main\ArgumentException
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
        include Helpers::getModulePath() . '/admin/fragments/random_integer_settings_form.php';
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
            $iMin = (int)$arPrepareRequest['min'];
            $iMax = (int)$arPrepareRequest['max'];

            if ($iMax > $iMin) {
                return true;
            }
        }

        return false;
    }

    /**
     * @return int
     * @throws \Exception
     */
    public function getValue()
    {
        if (!self::$bCheckStaticMethod) {
            return rand($this->iMin, $this->iMax);
        } else {
            throw new \Exception(Loc::getMessage('YLAB_DDATA_DATA_UNIT_INTEGER_EXCEPTION_STATIC'));
        }
    }
}