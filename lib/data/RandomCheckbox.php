<?php

namespace Ylab\Ddata\Data;

use Bitrix\Main\Localization\Loc;
use Ylab\Ddata\Interfaces\DataUnitClass;
use Bitrix\Main\HttpRequest;
use Ylab\Ddata\Helpers;

Loc::loadMessages(__FILE__);

/**
 * Class RandomCheckbox
 * @package Ylab\Ddata\Data
 */
class RandomCheckbox extends DataUnitClass
{
    private static $bCheckStaticMethod = true;

    protected $sRandom = 'N';
    protected $sCheckbox = 'Y';

    /**
     * Checkbox constructor.
     * @param $sProfileID
     * @param $sFieldCode
     * @param $sGeneratorID
     * @throws \Bitrix\Main\ArgumentException
     */
    public function __construct($sProfileID, $sFieldCode, $sGeneratorID)
    {
        self::$bCheckStaticMethod = false;

        parent::__construct($sProfileID, $sFieldCode, $sGeneratorID);

        if (!empty($this->options['random'])) {

            $this->sRandom = $this->options['random'];
        }

        if (!empty($this->options['checkbox'])) {

            $this->sCheckbox = $this->options['checkbox'];
        }
    }

    /**
     * @return array
     */
    public static function getDescription()
    {
        return [
            "ID" => "checkbox.unit",
            "NAME" => Loc::getMessage("YLAB_DDATA_DATA_UNIT_CHECKBOX_NAME"),
            "DESCRIPTION" => Loc::getMessage('YLAB_DDATA_DATA_UNIT_CHECKBOX_DESCRIPTION'),
            "TYPE" => "checkbox",
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
        include Helpers::getModulePath() . '/admin/fragments/random_checkbox_settings_form.php';
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
            $sCheckbox = $arPrepareRequest['checkbox'];

            if (!empty($sRandom) || !empty($sCheckbox)) {

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
        if (!self::$bCheckStaticMethod) {

            if ($this->sRandom == 'Y') {

                $arCheckbox = ['Y', 'N'];
                $sResult = array_rand($arCheckbox);

                return $arCheckbox[$sResult];
            } else {

                return $this->sCheckbox;
            }
        } else {

            throw new \Exception(Loc::getMessage('YLAB_DDATA_DATA_UNIT_CHECKBOX_EXCEPTION_STATIC'));
        }
    }
}