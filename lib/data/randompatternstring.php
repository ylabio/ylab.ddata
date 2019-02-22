<?php

namespace Ylab\Ddata\Data;

use Bitrix\Main\Localization\Loc;
use Ylab\Ddata\Interfaces\DataUnitClass;
use Bitrix\Main\HttpRequest;
use Ylab\Ddata\Helpers;

Loc::loadLanguageFile(__FILE__);

/**
 * Генерация строки по шаблону
 *
 * Class RandomPatternString
 *
 * @package Ylab\Ddata\Data
 */
class RandomPatternString extends DataUnitClass
{
    /** @var string $sPattern Паттерн по умолчанию */
    public $sPattern = "/{([а-яА-ЯёЁa-zA-Z0-9|]+)}/ui";

    /** @var string $sExample Пример строки */
    public $sExample = "Стол {красный|синий|белый} из {дерева|стекла|пластика}";

    /** @var int $iMaxLength Максимальная длина строки по умолчанию */
    public  $iMaxLength = 255;

    /**
     * RandomPatternString constructor.
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
    }

    /**
     * Метод возврящает массив описывающий тип данных. ID, Имя, scalar type php
     *
     * @return array
     */
    public  function getDescription()
    {
        return [
            "ID" => "random.patternstring.unit",
            "NAME" => Loc::getMessage('YLAB_DDATA_DATA_UNIT_PATTERN_STRING_NAME'),
            "DESCRIPTION" => Loc::getMessage('YLAB_DDATA_DATA_UNIT_PATTERN_STRING_DESCRIPTION'),
            "TYPE" => "string",
            "CLASS" => __CLASS__
        ];
    }

    /**
     * Метод возвращает html строку формы с настройкой генератора если таковые необходимы
     *
     * @param HttpRequest $oRequest
     *
     * @return mixed|string
     * @throws \Bitrix\Main\ArgumentException
     * @throws \Bitrix\Main\ObjectPropertyException
     * @throws \Bitrix\Main\SystemException
     */
    public function getOptionForm(HttpRequest $oRequest)
    {
        $arRequest = $oRequest->toArray();
        $arOptions = (array)$arRequest['option'];
        $sGeneratorID = $oRequest->get('generator');
        $sFieldID = $oRequest->get('field');
        $sProfileID = $oRequest->get('profile_id');
        $sPropertyName = $oRequest->get('property-name');

        $arOptions = array_merge($this->getOptions($sGeneratorID, $sProfileID, $sFieldID), $arOptions);
        ob_start();
        include Helpers::getModulePath() . "/admin/fragments/random_pattern_string_settings_form.php";
        $tpl = ob_get_contents();
        ob_end_clean();

        return $tpl;
    }

    /**
     * Метод проверяет на валидность данные настройки генератора
     *
     * @param HttpRequest $oRequest
     *
     * @return bool
     */
    public function isValidateOptions(HttpRequest $oRequest)
    {
        $bFlag = false;
        $arPrepareRequest = $oRequest->get('option');

        if ($arPrepareRequest['pattern'] && strlen($arPrepareRequest['pattern']) < $this->iMaxLength) {
            $bFlag = true;
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
        if (!empty($this->options['pattern'])) {
            $sRes = $this->options['pattern'];
        } else {
            $sRes = $this->sExample;
        }

        if (preg_match_all($this->sPattern, $sRes, $matches)) {

            if ($matches[1]) {

                foreach ($matches[1] as $sKey => $arStr) {
                    $sStr[$sKey] = explode('|', $arStr);

                    $iCount = count($sStr[$sKey]);
                    $iRand = $sStr[$sKey][rand(0, --$iCount)];
                    $sRes = str_replace('{' . $arStr . '}', $iRand, $sRes);
                }
            }
            return $sRes;
        } else {
            return false;
        }
    }
}