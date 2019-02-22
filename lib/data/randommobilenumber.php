<?php

namespace Ylab\Ddata\Data;

use Bitrix\Main\Localization\Loc;
use Ylab\Ddata\Interfaces\DataUnitClass;
use Bitrix\Main\HttpRequest;
use Ylab\Ddata\Helpers;

Loc::loadMessages(__FILE__);

/**
 * Генератор случайного номера мобильного телефона
 *
 * Class RandomMobileNumber
 * @package Ylab\Ddata\Data
 */
class RandomMobileNumber extends DataUnitClass
{
    protected $sCountryCode = '';

    /** @var string $sGenerationOption */
    protected $sGenerationOption = 'constructor';

    /** @var array $arCode */
    protected $arCode = [901, 999];

    /** @var array $arStart */
    protected $arStart = [1, 999];

    /** @var array $arFinish */
    protected $arFinish = [1, 9999];

    /** @var array $arRangeNumbers */
    protected $arRangeNumbers = [];

    /**
     * RandomMobileNumber constructor.
     * @param $sProfileID - ID профиля
     * @param $sFieldCode - Симфольный код свойства
     * @param $sGeneratorID - ID уже сохраненного генератора
     * @throws \Bitrix\Main\ArgumentException
     * @throws \Bitrix\Main\ObjectPropertyException
     * @throws \Bitrix\Main\SystemException
     */
    public function __construct(string $sProfileID = '', string $sFieldCode = '', string $sGeneratorID = '')
    {
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
    public  function getDescription()
    {
        return [
            'ID' => 'random.mobilenumber.unit',
            'NAME' => Loc::getMessage('YLAB_DDATA_RANDOM_MOBILE_NUMBER_NAME'),
            'DESCRIPTION' => Loc::getMessage('YLAB_DDATA_RANDOM_MOBILE_NUMBER_NAME'),
            'TYPE' => 'integer',
            'CLASS' => __CLASS__
        ];
    }

    /**
     * @param HttpRequest $request
     * @return false|mixed|string
     */
    public function getOptionForm(HttpRequest $request)
    {
        $sGeneratorID = $request->get('generator');
        $sProfileID = $request->get('profile_id');
        $sPropertyName = $request->get('property-name');
        $sPropertyCode = $request->get('property-code');

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
    public  function isValidateOptions(HttpRequest $oRequest)
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
    }
}