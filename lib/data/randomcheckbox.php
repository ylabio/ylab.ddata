<?php

namespace Ylab\Ddata\Data;

use Bitrix\Main\Localization\Loc;
use Ylab\Ddata\Interfaces\DataUnitClass;
use Bitrix\Main\HttpRequest;
use Ylab\Ddata\Helpers;

Loc::loadMessages(__FILE__);

/**
 * Генерация случайного значения checkbox (Да, Нет)
 *
 * Class RandomCheckbox
 *
 * @package Ylab\Ddata\Data
 */
class RandomCheckbox extends DataUnitClass
{
    /**
     * Метод возврящает массив описывающий тип данных. ID, Имя, scalar type php
     *
     * @return array
     */
    public function getDescription()
    {
        return [
            'ID' => 'checkbox.unit',
            'NAME' => Loc::getMessage('YLAB_DDATA_DATA_UNIT_CHECKBOX_NAME'),
            'DESCRIPTION' => Loc::getMessage('YLAB_DDATA_DATA_UNIT_CHECKBOX_DESCRIPTION'),
            'TYPE' => 'checkbox',
            'CLASS' => __CLASS__
        ];
    }

    /** @var string $sRandom Настройка генерировать рандомно значение или нет */
    protected $sRandom = 'N';

    /** @var string $sCheckbox Статус checkbox`а */
    protected $sCheckbox = 'Y';

    /**
     * RandomCheckbox constructor.
     *
     * @param $sProfileID   - ID профиля
     * @param $sFieldCode   - Симфольный код свойства
     * @param $sGeneratorID - ID уже сохраненного генератора
     *
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

        if (!empty($this->options['checkbox'])) {

            $this->sCheckbox = $this->options['checkbox'];
        }
    }

    /**
     * Метод возвращает html строку формы с настройкой генератора если таковые необходимы
     *
     * @param HttpRequest $request
     *
     * @return false|mixed|string
     */
    public function getOptionForm(HttpRequest $request)
    {
        $sGeneratorID = $request->get('generator');
        $sProfileID = $request->get('profile_id');
        $sPropertyName = $request->get('property-name');
        $sPropertyCode = $request->get('property-code');

        ob_start();
        include Helpers::getModulePath() . '/admin/fragments/random_checkbox_settings_form.php';
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

        if ($arPrepareRequest) {

            $sRandom = $arPrepareRequest['random'];
            $sCheckbox = $arPrepareRequest['checkbox'];

            if (!empty($sRandom) || !empty($sCheckbox)) {

                return true;
            }
        }

        return false;
    }

    /**
     * Возвращает случайную запись соответствующего типа
     *
     * @return string
     * @throws \Exception
     */
    public function getValue()
    {
        if ($this->sRandom == 'Y') {

            $arCheckbox = ['Y', 'N'];
            $sResult = array_rand($arCheckbox);

            return $arCheckbox[$sResult];
        }
        return $this->sCheckbox;
    }
}