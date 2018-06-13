<?php

namespace Ylab\Ddata\Data;

use Bitrix\Main\Localization\Loc;
use Ylab\Ddata\Interfaces\DataUnitClass;
use Bitrix\Main\HttpRequest;
use Ylab\Ddata\Helpers;

Loc::loadMessages(__FILE__);

/**
 * Class RandomUserGender
 * @package Ylab\Ddata\Data
 */
class RandomUserGender extends DataUnitClass
{
    private static $checkStaticMethod = true;

    protected $sRandom = 'N';
    protected $sGender = 'M';

    /**
     * RandomUserGender constructor.
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

        if (!empty($this->options['gender'])) {
            $this->sGender = $this->options['gender'];
        }
    }

    /**
     * @return array
     */
    public static function getDescription()
    {
        return [
            "ID" => "user.gender",
            "NAME" => Loc::getMessage('YLAB_DDATA_DATA_GENDER_NAME'),
            "DESCRIPTION" => Loc::getMessage('YLAB_DDATA_DATA_GENDER_DESCRIPTION'),
            "TYPE" => "user.gender",
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
        include Helpers::getModulePath() . '/admin/fragments/random_user_gender_settings_form.php';
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
            $sGender = $arPrepareRequest['gender'];

            if (!empty($sRandom) || !empty($sGender)) {
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
            if ($this->sRandom == 'Y') {
                $arCheckbox = ['M', 'F'];
                $sResult = array_rand($arCheckbox);

                return $arCheckbox[$sResult];
            } else {
                return $this->sGender;
            }
        } else {
            throw new \Exception(Loc::getMessage('YLAB_DDATA_DATA_GENDER_EXCEPTION_STATIC'));
        }
    }
}