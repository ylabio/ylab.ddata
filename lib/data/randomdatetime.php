<?php

namespace Ylab\Ddata\Data;

use Bitrix\Main\Localization\Loc;
use Ylab\Ddata\Interfaces\DataUnitClass;
use Bitrix\Main\HttpRequest;
use Ylab\Ddata\Helpers;

Loc::loadMessages(__FILE__);

/**
 * Генерация случайной даты
 *
 * Class RandomDateTime
 * @package Ylab\Ddata\Data
 */
class RandomDateTime extends DataUnitClass
{
    protected $iDateFrom;

    /** @var false|string $iDateTo timestamp конечной даты */
    protected $iDateTo;

    /** @var string $sDateFormat Формат вывода даты */
    protected $sDateFormat = 'FULL';

    /**
     * RandomDateTime constructor.
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
     * Метод возврящает массив описывающий тип данных. ID, Имя, scalar type php
     *
     * @return array
     */
    public function getDescription()
    {
        return [
            'ID' => 'datetime.unit',
            'NAME' => Loc::getMessage('YLAB_DDATA_DATA_UNIT_DATETIME_NAME'),
            'DESCRIPTION' => Loc::getMessage('YLAB_DDATA_DATA_UNIT_DATETIME_DESCRIPTION'),
            'TYPE' => 'datetime',
            'CLASS' => __CLASS__
        ];
    }

    /**
     * Метод возвращает html строку формы с настройкой генератора если таковые необходимы
     *
     * @param HttpRequest $request
     * @return mixed|string
     */
    public function getOptionForm(HttpRequest $request)
    {
        $sGeneratorID = $request->get('generator');
        $sProfileID = $request->get('profile_id');
        $sPropertyName = $request->get('property-name');
        $sPropertyCode = $request->get('property-code');

        ob_start();
        include Helpers::getModulePath() . '/admin/fragments/random_datetime_settings_form.php';
        $tpl = ob_get_contents();
        ob_end_clean();

        return $tpl;
    }

    /**
     * Метод проверяет на валидность данные настройки генератора
     *
     * @param HttpRequest $request
     * @return bool
     */
    public  function isValidateOptions(HttpRequest $request)
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
     * Возвращает случайную запись соответствующего типа
     *
     * @return mixed
     * @throws \Exception
     */
    public function getValue()
    {
        $sStartDate = $this->iDateFrom;
        $sEndDate = $this->iDateTo;
        $sFormatDate = $this->sDateFormat;
        $iStartDateUnix = MakeTimeStamp($sStartDate);
        $iEndDateUnix = MakeTimeStamp($sEndDate);
        $iRandDateUnix = rand($iStartDateUnix, $iEndDateUnix);
        $sRandDate = ConvertTimeStamp($iRandDateUnix, $sFormatDate);

        return $sRandDate;
    }
}