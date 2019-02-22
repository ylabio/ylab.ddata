<?php

namespace Ylab\Ddata\Data;

use Bitrix\Main\Localization\Loc;
use Bitrix\Main\HttpRequest;
use Ylab\Ddata\Interfaces\DataUnitClass;
use Ylab\Ddata\Helpers;

Loc::loadMessages(__FILE__);

/**
 * Генерация случайной страны
 *
 * Class RandomUserCountry
 * @package Ylab\Ddata\Data
 */
class RandomUserCountry extends DataUnitClass
{
    protected $sRandom = 'N';

    /** @var array $arSelectedCountries ID предустановленных стран */
    protected $arSelectedCountries = [1];

    /**
     * RandomUserCountry constructor.
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

        if (!empty($this->options['selected-countries'])) {
            $this->arSelectedCountries = $this->options['selected-countries'];
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
            'ID' => 'user.country',
            'NAME' => Loc::getMessage('YLAB_DDATA_DATA_COUNTRY_NAME'),
            'DESCRIPTION' => Loc::getMessage('YLAB_DDATA_DATA_COUNTRY_DESCRIPTION'),
            'TYPE' => 'user.country',
            'CLASS' => __CLASS__
        ];
    }

    /**
     * Метод возвращает html строку формы с настройкой генератора если таковые необходимы
     *
     * @param HttpRequest $request
     * @return false|mixed|string
     */
    public function getOptionForm(HttpRequest $request)
    {
        $sGeneratorID = $request->get('generator');
        $sProfileID = $request->get('profile_id');
        $sPropertyName = $request->get('property-name');
        $sPropertyCode = $request->get('property-code');

        $arCountries = GetCountryArray('RU');
        $arCountries = array_combine($arCountries['reference_id'], $arCountries['reference']);

        ob_start();
        include Helpers::getModulePath() . '/admin/fragments/random_country_settings_form.php';
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

        if ($arPrepareRequest) {
            $sRandom = $arPrepareRequest['random'];
            $arSelectedCountries = $arPrepareRequest['selected-countries'];

            if (!empty($sRandom) || (!empty($arSelectedCountries) && is_array($arSelectedCountries))) {
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
            $arCountries = GetCountryArray('RU');
            $sResult = array_rand($arCountries['reference_id']);

            return $arCountries['reference_id'][$sResult];
        }

        $sResult = array_rand($this->arSelectedCountries);

        return $this->arSelectedCountries[$sResult];
    }
}