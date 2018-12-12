<?php

namespace Ylab\Ddata\Data;

use Bitrix\Main\Localization\Loc;
use Ylab\Ddata\Interfaces\DataUnitClass;
use Bitrix\Main\HttpRequest;
use Ylab\Ddata\Helpers;

Loc::loadMessages(__FILE__);

class RandomMobileNumber extends DataUnitClass
{
    private static $bCheckStaticMethod = true;
    protected $sCountryCode = '';
    protected $sGenerationOption = 'constructor';
    protected $arCode = [901, 999];
    protected $arStart = [1, 999];
    protected $arFinish = [1, 9999];
    protected $arRangeNumbers = [];

    /**
     * RandomMobileNumber constructor.
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

        if (!empty($this->options['country-code'])) {
            $this->sCountryCode = $this->options['country-code'];
        }

        if (!empty($this->options['generation-option'])) {
            $this->sGenerationOption = $this->options['generation-option'];
        }

        if (!empty($this->options['code-from']) && !empty($this->options['code-to'])) {
            $this->arCode = [$this->options['code-from'], $this->options['code-to']];
        }

        if (!empty($this->options['number-start-from']) && !empty($this->options['number-start-to'])) {
            $this->arStart = [$this->options['number-start-from'], $this->options['number-start-to']];
        }

        if (!empty($this->options['number-finish-from']) && !empty($this->options['number-finish-to'])) {
            $this->arFinish = [$this->options['number-finish-from'], $this->options['number-finish-to']];
        }

        if (!empty($this->options['range-from']) && !empty($this->options['range-to'])) {
            $this->arRangeNumbers = [$this->options['range-from'], $this->options['range-to']];
        }
    }

    /**
     * Метод возврящает массив описывающий тип данных. ID, Имя, scalar type php
     *
     * @return array
     */
    public static function getDescription()
    {
        return [
            "ID" => "random.mobilenumber.unit",
            "NAME" => Loc::getMessage("YLAB_DDATA_RANDOM_MOBILE_NUMBER_NAME"),
            "DESCRIPTION" => Loc::getMessage("YLAB_DDATA_RANDOM_MOBILE_NUMBER_NAME"),
            "TYPE" => "integer",
            "CLASS" => __CLASS__
        ];
    }

    /**
     * Метод возвращает html строку формы с настройкой генератора если таковые необходимы
     *
     * @param HttpRequest $request
     * @return mixed
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
        include Helpers::getModulePath() . '/admin/fragments/random_mobile_number_prepare_form.php';
        $tpl = ob_get_contents();
        ob_end_clean();

        return $tpl;
    }

    /**
     * Метод проверяет на валидность данные настройки генератора
     *
     * @param HttpRequest $oRequest
     * @return mixed
     */
    public static function isValidateOptions(HttpRequest $oRequest)
    {
        $arPrepareRequest = $oRequest->get('option');
        $bCheck = false;
        if (!empty($arPrepareRequest)) {
            $iCodeFrom = $arPrepareRequest['code-from'];
            $iCodeTo = $arPrepareRequest['code-to'];
            $iNumberStartFrom = $arPrepareRequest['number-start-from'];
            $iNumberStartTo = $arPrepareRequest['number-start-to'];
            $iNumberFinishFrom = $arPrepareRequest['number-finish-from'];
            $iNumberFinishTo = $arPrepareRequest['number-finish-to'];
            $sGenerationOption = $arPrepareRequest['generation-option'];
            $iRangeFrom = $arPrepareRequest['range-from'];
            $iRangeTo = $arPrepareRequest['range-to'];

            if ($sGenerationOption == 'constructor') {
                if (!empty($iCodeFrom) && !empty($iCodeTo) && $iCodeTo > $iCodeFrom) {
                    $bCheck = true;
                } else {
                    return false;
                }

                if (!empty($iNumberStartFrom) && !empty($iNumberStartTo) && $iNumberStartTo > $iNumberStartFrom) {
                    $bCheck = true;
                } else {
                    return false;
                }

                if (!empty($iNumberFinishFrom) && !empty($iNumberFinishTo) && $iNumberFinishTo > $iNumberFinishFrom) {
                    $bCheck = true;
                } else {
                    return false;
                }
            } elseif ($sGenerationOption == 'range') {
                if (!empty($iRangeFrom) && !empty($iRangeTo) && $iRangeTo > $iRangeFrom) {
                    if (strlen($iRangeFrom) < 10) {
                        return false;
                    }
                    if (strlen($iRangeTo) < 10) {
                        return false;
                    }
                    $bCheck = true;
                } else {
                    return false;
                }
            } else {
                return false;
            }
        }

        return $bCheck;
    }

    /**
     * Возвращает случайную запись соответствующего типа
     *
     * @return string
     * @throws \Exception
     */
    public function getValue()
    {
        if (!self::$bCheckStaticMethod) {
            $sResult = '';
            if ($this->sGenerationOption == 'constructor') {
                $iCodeFrom = $this->arCode[0];
                $iCodeTo = $this->arCode[1];
                $iNumberStartFrom = $this->arStart[0];
                $iNumberStartTo = $this->arStart[1];
                $iNumberFinishFrom = $this->arFinish[0];
                $iNumberFinishTo = $this->arFinish[1];
                if (!empty($this->sCountryCode)) {
                    $sResult = $this->sCountryCode;
                }
                $sResult .= rand($iCodeFrom, $iCodeTo);
                $iNumberStart = rand($iNumberStartFrom, $iNumberStartTo);
                $iNumberStartLength = strlen($iNumberStart);
                if ($iNumberStartLength < 3) {
                    for ($i = $iNumberStartLength; $i < 3; $i++) {
                        $iNumberStart .= '0';
                    }
                }
                $sResult .= $iNumberStart;
                $iNumberFinish = rand($iNumberFinishFrom, $iNumberFinishTo);
                $iNumberFinishLength = strlen($iNumberFinish);
                if ($iNumberFinishLength < 4) {
                    for ($iNumberFinishLength; $i < 4; $i++) {
                        $iNumberFinish .= '0';
                    }
                }
                $sResult .= $iNumberFinish;
            } elseif ($this->sGenerationOption == 'range') {
                $iNumberFrom = $this->arRangeNumbers[0];
                $iNumberTo = $this->arRangeNumbers[1];
                if (!empty($this->sCountryCode)) {
                    $sResult = $this->sCountryCode;
                    $iNumberFromLength = strlen($iNumberFrom);
                    $iNumberToLength = strlen($iNumberTo);
                    if ($iNumberFromLength > 10) {
                        $iNumberFrom = mb_substr($iNumberFrom, ($iNumberFromLength - 10));
                    }
                    if ($iNumberToLength > 10) {
                        $iNumberTo = mb_substr($iNumberTo, ($iNumberToLength - 10));
                    }
                }
                $sResult .= rand($iNumberFrom, $iNumberTo);
            }
            return $sResult;
        } else {
            throw new \Exception(Loc::getMessage('YLAB_DDATA_DATA_UNIT_MOBILE_NUMBER_EXCEPTION_STATIC'));
        }
    }
}