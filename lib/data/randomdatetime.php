<?php

namespace Ylab\Ddata\Data;

use Bitrix\Main\Localization\Loc;
use Ylab\Ddata\Interfaces\DataUnitClass;
use Bitrix\Main\HttpRequest;
use Ylab\Ddata\Helpers;

Loc::loadMessages(__FILE__);

/**
 * Class RandomDateTime
 * @package Ylab\Ddata\Data
 */
class RandomDateTime extends DataUnitClass
{
    private static $checkStaticMethod = true;

    protected $iDateFrom;
    protected $iDateTo;
    protected $sDateFormat = 'FULL';

    /**
     * RandomDateTime constructor.
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

        if (!empty($this->options['date_from'])) {

            $this->iDateFrom = $this->options['date_from'];
        } else {

            $this->iDateFrom = date("d.m.Y H:i:s", time());
        }

        if (!empty($this->options['date_to'])) {

            $this->iDateTo = $this->options['date_to'];
        } else {

            $this->iDateTo = date("d.m.Y H:i:s", time() + 60 * 60 * 24 * 7);
        }

        if (!empty($this->options['date_format'])) {

            $this->sDateFormat = $this->options['date_format'];
        }
    }

    /**
     * @return array
     */
    public static function getDescription()
    {
        return [
            "ID" => "datetime.unit",
            "NAME" => Loc::getMessage("YLAB_DDATA_DATA_UNIT_DATETIME_NAME"),
            "DESCRIPTION" => Loc::getMessage('YLAB_DDATA_DATA_UNIT_DATETIME_DESCRIPTION'),
            "TYPE" => "datetime",
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
        include Helpers::getModulePath() . "/admin/fragments/random_datetime_settings_form.php";
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
        if (!empty($arPrepareRequest['date_from'])) {
            $bFlag = true;
        } else {
            return false;
        }

        if (!empty($arPrepareRequest['date_to'])) {
            $bFlag = true;
        } else {
            return false;
        }

        if (!empty($arPrepareRequest['date_format'])) {
            $bFlag = true;
        } else {
            return false;
        }

        return $bFlag;
    }

    /**
     * @return mixed
     * @throws \Exception
     */
    public function getValue()
    {
        if (!self::$checkStaticMethod) {

            $sStartDate = $this->iDateFrom;
            $sEndDate = $this->iDateTo;
            $sFormatDate = $this->sDateFormat;
            $iStartDateUnix = MakeTimeStamp($sStartDate);
            $iEndDateUnix = MakeTimeStamp($sEndDate);
            $iRandDateUnix = rand($iStartDateUnix, $iEndDateUnix);
            $sRandDate = ConvertTimeStamp($iRandDateUnix, $sFormatDate);

            return $sRandDate;
        } else {

            throw new \Exception(Loc::getMessage('YLAB_DDATA_DATA_UNIT_DATETIME_EXCEPTION_STATIC'));
        }
    }
}