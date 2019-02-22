<?php

namespace Ylab\Ddata\data;

use Bitrix\Main\HttpRequest;
use Bitrix\Main\Loader;
use Bitrix\Main\Localization\Loc;
use Ylab\Ddata\interfaces\DataUnitClass;
use Ylab\Ddata\Helpers;

Loc::loadMessages(__FILE__);

/**
 * Генерация случайного значения из доступного списка сущности ХайлоадБлока
 *
 * Class RandomEnumHL
 * @package Ylab\Ddata\data
 */
class RandomEnumHL extends DataUnitClass
{
    protected $sRandom = 'N';

    /** @var int $iSelectedValue ID выбранного значения списка */
    protected $iSelectedValue = 0;

    /** @var array $arAllValues Список значений списка */
    protected $arAllValues = [];

    /**
     * RandomEnumHL constructor.
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

        if (!empty($this->options['random'])) {
            $this->sRandom = $this->options['random'];
        }

        if (!empty($this->options['selected-value'])) {
            $this->iSelectedValue = $this->options['selected-value'];
        }

        $oEnum = new \CUserFieldEnum;
        $rsEnum = $oEnum->GetList([], ['USER_FIELD_NAME' => $sFieldCode]);
        while ($arEnum = $rsEnum->GetNext()) {
            $this->arAllValues[] = $arEnum['ID'];
        }
    }

    /**
     * Метод возвращает массив описывающий тип данных. ID, Имя, scalar type php
     *
     * @return array
     */
    public function getDescription()
    {
        return [
            'ID' => 'enum.hl.unit',
            'NAME' => Loc::getMessage('YLAB_DDATA_DATA_UNIT_ENUM_HL_NAME'),
            'DESCRIPTION' => Loc::getMessage('YLAB_DDATA_DATA_UNIT_ENUM_HL_DESCRIPTION'),
            'TYPE' => 'enum.hl',
            'CLASS' => __CLASS__
        ];
    }

    /**
     * Метод возвращает html строку формы с настройкой генератора если таковые необходимы
     *
     * @param HttpRequest $request
     * @return false|mixed|string
     * @throws \Bitrix\Main\LoaderException
     */
    public function getOptionForm(HttpRequest $request)
    {
        Loader::includeModule('highloadblock');

        $sGeneratorID = $request->get('generator');
        $sProfileID = $request->get('profile_id');
        $sPropertyName = $request->get('property-name');
        $sPropertyCode = $request->get('property-code');

        $oEnum = new \CUserFieldEnum;
        $rsEnum = $oEnum->GetList([], ['USER_FIELD_NAME' => $sPropertyCode]);
        while ($arEnum = $rsEnum->GetNext()) {
            $arValues[] = $arEnum['VALUE'];
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
     * @return mixed
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
            $iResult = array_rand($arAllValues);

            return $arAllValues[$iResult];
        } else {
            return $arAllValues[$this->iSelectedValue];
        }
    }
}