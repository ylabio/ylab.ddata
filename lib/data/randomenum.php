<?php

namespace Ylab\Ddata\Data;

use Bitrix\Main\HttpRequest;
use Bitrix\Main\Localization\Loc;
use Ylab\Ddata\Interfaces\DataUnitClass;
use Ylab\Ddata\Orm\EntityUnitProfileTable;
use Ylab\Ddata\Helpers;

Loc::loadMessages(__FILE__);

/**
 * Генерация случайного значения из доступного списка
 *
 * Class RandomEnum
 * @package Ylab\Ddata\Data
 */
class RandomEnum extends DataUnitClass
{
    protected $sRandom = 'Y';

    /** @var int $iSelectedValue ID выбранного значения списка */
    protected $iSelectedValue = 0;

    /** @var array $arAllValues Список значений списка */
    protected $arAllValues = [];

    /**
     * RandomEnum constructor.
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

        $objProfile = EntityUnitProfileTable::getList([
            'filter' => ['=ID' => $sProfileID]
        ]);

        $arProfile = $objProfile->fetch();
        if (!empty($arProfile)) {
            $arOptions = json_decode($arProfile['OPTIONS'], true);
            $sNamespace = $arOptions['namespace'];
            $arAllValues = $sNamespace::getMap();
            foreach ($arAllValues as $arAllValue) {
                $sName = $arAllValue->getName();
                if ($sName == $sFieldCode) {
                    $arValues = $arAllValue->getValues();
                }
            }
            $this->arAllValues = $arValues;
        }

        if (!empty($this->options['random'])) {
            $this->sRandom = $this->options['random'];
        }

        if (!empty($this->options['selected-value'])) {
            $this->iSelectedValue = $this->options['selected-value'];
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
            'ID' => "enum.unit",
            'NAME' => Loc::getMessage('YLAB_DDATA_DATA_UNIT_ENUM_NAME'),
            'DESCRIPTION' => Loc::getMessage('YLAB_DDATA_DATA_UNIT_ENUM_DESCRIPTION'),
            'TYPE' => 'enum',
            'CLASS' => __CLASS__
        ];
    }

    /**
     * Метод возвращает html строку формы с настройкой генератора если таковые необходимы
     *
     * @param HttpRequest $oRequest
     * @return false|mixed|string
     * @throws \Bitrix\Main\ArgumentException
     * @throws \Bitrix\Main\ObjectPropertyException
     * @throws \Bitrix\Main\SystemException
     */
    public function getOptionForm(HttpRequest $oRequest)
    {
        $sGeneratorID = $oRequest->get('generator');
        $sProfileID = $oRequest->get('profile_id');
        $sPropertyName = $oRequest->get('property-name');
        $sPropertyCode = $oRequest->get('property-code');
        $arRequest = $oRequest->toArray();

        if (intval($sProfileID) > 0) {
            $objProfile = EntityUnitProfileTable::getList([
                'filter' => ['=ID' => $sProfileID]
            ]);

            $arProfile = $objProfile->fetch();
            if (!empty($arProfile)) {
                $arOptions = json_decode($arProfile['OPTIONS'], true);
                $sNamespace = $arOptions['namespace'];
                $arAllValues = $sNamespace::getMap();
                foreach ($arAllValues as $arAllValue) {
                    $sName = $arAllValue->getName();
                    if ($sName == $sPropertyCode) {
                        $arValues = $arAllValue->getValues();
                    }
                }
            }
        } else {
            $sNamespace = $arRequest['prepare']['namespace'];
            $arAllValues = $sNamespace::getMap();
            foreach ($arAllValues as $arAllValue) {
                $sName = $arAllValue->getName();
                if ($sName == $sPropertyCode) {
                    $arValues = $arAllValue->getValues();
                }
            }
        }

        ob_start();
        include Helpers::getModulePath() . '/admin/fragments/random_enum_settings_form.php';
        $tpl = ob_get_contents();
        ob_end_clean();

        return $tpl;
    }


    /**
     * Метод проверяет на валидность данные настройки генератора
     *
     * @param HttpRequest $request
     * @return bool|mixed
     */
    public  function isValidateOptions(HttpRequest $request)
    {
        $arPrepareRequest = $request->get('option');

        if ($arPrepareRequest) {
            $sRandom = $arPrepareRequest['random'];
            if (!empty($sRandom)) {
                return true;
            }
        }

        return false;
    }


    /**
     * Возвращает случайную запись соответствующего типа
     *
     * @return mixed
     * @throws \Exception
     */
    public function getValue()
    {
        $arAllValues = $this->arAllValues;
        if ($this->sRandom == 'Y') {
            $sResult = array_rand($arAllValues);

            return $arAllValues[$sResult];
        } else {
            return $arAllValues[$this->iSelectedValue];
        }
    }
}