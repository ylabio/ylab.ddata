<?php

namespace Ylab\Ddata\Data;

use Bitrix\Main\Localization\Loc;
use Ylab\Ddata\Interfaces\DataUnitClass;
use Bitrix\Main\HttpRequest;
use Ylab\Ddata\Helpers;

Loc::loadMessages(__FILE__);

/**
 * Генерация случайного логина
 *
 * Class RandomLogin
 * @package Ylab\Ddata\Data
 */
class RandomLogin extends DataUnitClass
{
    protected $iLength = 12;

    /**
     * RandomLogin constructor.
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

        if (!empty($this->options['length'])) {
            $this->iLength = $this->options['length'];
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
            'ID' => 'login.string.unit',
            'NAME' => Loc::getMessage('YLAB_DDATA_DATA_UNIT_LOGIN_NAME'),
            'DESCRIPTION' => Loc::getMessage('YLAB_DDATA_DATA_UNIT_LOGIN_DESCRIPTION'),
            'TYPE' => 'string',
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

        ob_start();
        include Helpers::getModulePath() . '/admin/fragments/random_login_settings_form.php';
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
        $iLength = (int)$arPrepareRequest['length'];
        $bFlag = false;

        if (!empty($iLength) && is_int($iLength) && $iLength >= 6 && $iLength <= 24) {
            $bFlag = true;
        }

        return $bFlag;
    }

    /**
     * Возвращает случайную запись соответствующего типа
     *
     * @return array|string
     * @throws \Exception
     */
    public function getValue()
    {
        $arSymbols = array('aeiouy', 'bcdfghjklmnpqrstvwxz');
        $iLength = $this->iLength;
        $arReturn = array();
        foreach ($arSymbols as $k => $v) {
            $arSymbols[$k] = str_split($v);
        }

        for ($i = 0; $i < $iLength; $i++) {
            while (true) {
                $symbolX = mt_rand(0, sizeof($arSymbols) - 1);
                $symbolY = mt_rand(0, sizeof($arSymbols[$symbolX]) - 1);
                if ($i > 0 && in_array($arReturn[$i - 1], $arSymbols[$symbolX])) {
                    continue;
                }
                $arReturn[] = $arSymbols[$symbolX][$symbolY];
                break;
            }
        }
        $arReturn = implode('', $arReturn);

        return $arReturn;
    }
}