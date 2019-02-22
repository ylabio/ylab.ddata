<?php

namespace Ylab\Ddata\data;

use Bitrix\Main\HttpRequest;
use Bitrix\Main\Web\Json;
use CCurrency;
use Ylab\Ddata\interfaces\DataUnitClass;
use Bitrix\Main\Localization\Loc;
use Ylab\Ddata\Helpers;
use Bitrix\Main\Loader;
use Ylab\Ddata\Orm\EntityUnitProfileTable;

Loc::loadMessages(__FILE__);

/**
 * Генерация случайной валюты, доступной в торговом каталоге
 *
 * Class RandomCurrency
 * @package Ylab\Ddata\data
 */
class RandomCurrency extends DataUnitClass
{
    protected $sRandom = 'N';

    /** @var string $sSelectedValue Выбранное знаечени */
    protected $sSelectedValue = '';

    /** @var array $arCurrency Список доступных валят */
    protected $arCurrency = [];

    /**
     * RandomCurrency constructor.
     * @param $sProfileID - ID профиля
     * @param $sFieldCode - Симфольный код свойства
     * @param $sGeneratorID - ID уже сохраненного генератора
     * @throws \Bitrix\Main\ArgumentException
     * @throws \Bitrix\Main\LoaderException
     * @throws \Bitrix\Main\ObjectPropertyException
     * @throws \Bitrix\Main\SystemException
     */
    public function __construct(string $sProfileID = '', string $sFieldCode = '', string $sGeneratorID = '')
    {
        parent::__construct($sProfileID, $sFieldCode, $sGeneratorID);

        $arCurrency = $this->getCurrency();

        if (!empty($arCurrency)) {
            $this->arCurrency = $arCurrency;
        }

        if (!empty($this->options['random'])) {
            $this->sRandom = $this->options['random'];
        }

        if (!empty($this->options['selected-value'])) {
            $this->sSelectedValue = $this->options['selected-value'];
        } else {
            $this->sSelectedValue = $this->getBaseCurrency();
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
            'ID' => 'currency.unit',
            'NAME' => Loc::getMessage('YLAB_DDATA_DATA_UNIT_CURRENCY_NAME'),
            'DESCRIPTION' => Loc::getMessage('YLAB_DDATA_DATA_UNIT_CURRENCY_DESCRIPTION'),
            'TYPE' => 'currency',
            'CLASS' => __CLASS__
        ];
    }

    /**
     * Метод возвращает html строку формы с настройкой генератора если таковые необходимы
     *
     * @param HttpRequest $request
     * @return mixed
     */
    public function getOptionForm(HttpRequest $request)
    {
        $sGeneratorID = $request->get('generator');
        $sProfileID = $request->get('profile_id');
        $sPropertyName = $request->get('property-name');
        $sPropertyCode = $request->get('property-code');

        ob_start();
        include Helpers::getModulePath() . '/admin/fragments/random_currency_settings_form.php';
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
    public function isValidateOptions(HttpRequest $request)
    {
        $arPrepareRequest = $request->get('option');

        if ($arPrepareRequest) {
            $sRandom = $arPrepareRequest['random'];
            $sSelectedValue = $arPrepareRequest['selected-value'];

            if (!empty($sRandom) || !empty($sSelectedValue)) {
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
        if ($this->sRandom === 'Y') {
            if ($this->arCurrency) {
                $arCurrency = array_keys($this->arCurrency);
                return array_rand($arCurrency);
            }
        } else {
            if ($this->sSelectedValue) {
                return $this->sSelectedValue;
            }
        }

        return '';
    }

    /**
     * Метод для получения базовой валюты
     * @return string
     * @throws \Bitrix\Main\LoaderException
     */
    public function getBaseCurrency()
    {
        Loader::includeModule('iblock');
        Loader::includeModule('catalog');

        $sResult = \Bitrix\Currency\CurrencyManager::getBaseCurrency();

        return $sResult;
    }

    /**
     * Метод для получения всех доступных валют
     * @return array
     * @throws \Bitrix\Main\LoaderException
     */
    public function getCurrency()
    {
        Loader::includeModule('catalog');

        $arResult = [];
        $oCurrency = CCurrency::GetList(($by = "name"), ($order = "asc"), LANGUAGE_ID);
        while ($arCurrency = $oCurrency->Fetch()) {
            $arResult[$arCurrency['CURRENCY']] = $arCurrency['FULL_NAME'];
        }

        return $arResult;
    }
}