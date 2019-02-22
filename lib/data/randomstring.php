<?php

namespace Ylab\Ddata\Data;

use Bitrix\Main\Localization\Loc;
use Ylab\Ddata\Interfaces\DataUnitClass;
use Bitrix\Main\HttpRequest;
use Ylab\Ddata\Helpers;

Loc::loadLanguageFile(__FILE__);

/**
 * Генерация случайной строки
 *
 * Class RandomString
 *
 * @package Ylab\Ddata\Data
 */
class RandomString extends DataUnitClass
{
    /** @var string $sLang Язык строки по умолчанию */
    protected $sLang = "EN";

    /** @var int $iMinLength Минимальная длина строки по умолчанию */
    protected $iMinLength = 6;

    /** @var int $iMaxLength Максимальная длина строки по умолчанию */
    protected $iMaxLength = 255;

    /** @var string $sRegister Учитывать регистр или нет */
    protected $sRegister = 'N';

    /** @var string $sUserString */
    protected $sUserString = '';

    /**
     * RandomString constructor.
     *
     * @param $sProfileID   - ID профиля
     * @param $sFieldCode   - Симфольный код свойства
     * @param $sGeneratorID - ID уже сохраненного генератора
     *
     * @throws \Bitrix\Main\ArgumentException
     * @throws \Bitrix\Main\ObjectPropertyException
     * @throws \Bitrix\Main\SystemException
     */
    public function __construct($sProfileID, $sFieldCode, $sGeneratorID)
    {
        parent::__construct($sProfileID, $sFieldCode, $sGeneratorID);

        if (!empty($this->options['lang'])) {
            $this->sLang = $this->options['lang'];
        }

        if (!empty($this->options['min'])) {
            $this->iMinLength = $this->options['min'];
        }

        if (!empty($this->options['max'])) {
            $this->iMaxLength = $this->options['max'];
        }

        if (!empty($this->options['register'])) {
            $this->sRegister = $this->options['register'];
        }

        if (!empty($this->options['user-string'])) {
            $this->sUserString = $this->options['user-string'];
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
            "ID" => "random.string.unit",
            "NAME" => Loc::getMessage('YLAB_DDATA_DATA_UNIT_STRING_NAME'),
            "DESCRIPTION" => Loc::getMessage('YLAB_DDATA_DATA_UNIT_STRING_DESCRIPTION'),
            "TYPE" => "string",
            "CLASS" => __CLASS__
        ];
    }

    /**
     * Метод возвращает html строку формы с настройкой генератора если таковые необходимы
     *
     * @param HttpRequest $request
     *
     * @return mixed|string
     * @throws \Bitrix\Main\ArgumentException
     * @throws \Bitrix\Main\ObjectPropertyException
     * @throws \Bitrix\Main\SystemException
     */
    public function getOptionForm(HttpRequest $request)
    {
        $arRequest = $request->toArray();
        $arOptions = (array)$arRequest['option'];
        $sGeneratorID = $request->get('generator');
        $sFieldID = $request->get('field');
        $sProfileID = $request->get('profile_id');
        $sPropertyName = $request->get('property-name');

        $arOptions = array_merge($this->getOptions($sGeneratorID, $sProfileID, $sFieldID), $arOptions);
        ob_start();
        include Helpers::getModulePath() . "/admin/fragments/random_string_settings_form.php";
        $tpl = ob_get_contents();
        ob_end_clean();

        return $tpl;
    }

    /**
     * Метод проверяет на валидность данные настройки генератора
     *
     * @param HttpRequest $request
     *
     * @return bool
     */
    public function isValidateOptions(HttpRequest $request)
    {
        $arPrepareRequest = $request->get('option');
        $bFlag = false;

        if (!empty($arPrepareRequest['lang'])) {
            $bFlag = true;
        } else {
            return false;
        }

        if (!empty($arPrepareRequest['min']) && !empty($arPrepareRequest['max'])) {
            $iMin = (int)$arPrepareRequest['min'];
            $iMax = (int)$arPrepareRequest['max'];

            if ($iMax > $iMin) {
                $bFlag = true;
            }
        } else {
            return false;
        }

        return $bFlag;
    }

    /**
     * Возвращает случайную запись соответствующего типа
     *
     * @return string
     * @throws \Exception
     */
    public function getValue()
    {
        if ($this->sUserString) {
            return $this->sUserString;
        }

        $sLang = $this->sLang;
        $iMinLength = $this->iMinLength;
        $iMaxLength = $this->iMaxLength;
        $sRegister = $this->sRegister;

        $sCyrillic = "абвгдеёжзийклмнопрстуфхцчшщъыьэюяАБВГДЕЁЖЗИЙКЛМНОПРСТУФХЦЧШЩЪЫЬЭЮЯ";
        $sLatin = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ";

        if ($sLang == 'RU') {
            $iSize = strlen($sCyrillic) - 1;
            $iStringLength = rand($iMinLength, $iMaxLength);
            $sResult = '';

            while (strlen($sResult) < $iStringLength) {
                $sResult .= mb_substr($sCyrillic, rand(0, $iSize - 1), 1);
            }

            switch ($sRegister) {
                case 'UP':
                    $sResult = strtoupper($sResult);
                    break;
                case 'LOW':
                    $sResult = strtolower($sResult);
                    break;
                case 'NO':
                    break;
            }

            return $sResult;
        } elseif ($sLang == 'EN') {
            $iSize = strlen($sLatin) - 1;
            $iStringLength = rand($iMinLength, $iMaxLength);
            $sResult = '';

            while (strlen($sResult) < $iStringLength) {
                $sResult .= $sLatin[rand(0, $iSize)];
            }

            switch ($sRegister) {
                case 'UP':
                    $sResult = strtoupper($sResult);
                    break;
                case 'LOW':
                    $sResult = strtolower($sResult);
                    break;
                case 'NO':
                    break;
            }

            return $sResult;
        }
    }
}