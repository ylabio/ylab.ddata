<?php

namespace Ylab\Ddata\Data;

use Bitrix\Main\Localization\Loc;
use Ylab\Ddata\Interfaces\DataUnitClass;
use Bitrix\Main\HttpRequest;
use Ylab\Ddata\Helpers;

Loc::loadMessages(__FILE__);

/**
 * Генерация случайного числа
 *
 * Class RandomInteger
 * @package Ylab\Ddata\Data
 */
class RandomInteger extends DataUnitClass
{
    protected $iMin = 0;

    /** @var int $iMax */
    protected $iMax = 9999;

    /** @var integer|null $iUserNumber  */
    protected $iUserNumber = null;

    /**
     * RandomInteger constructor.
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

        if (!empty($this->options['min'])) {
            $this->iMin = $this->options['min'];
        }

        if (!empty($this->options['max'])) {
            $this->iMax = $this->options['max'];
        }

        if (!empty($this->options['user-number'])) {
            $this->iUserNumber = $this->options['user-number'];
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
            'ID' => 'random.integer.unit',
            'NAME' => Loc::getMessage('YLAB_DDATA_DATA_UNIT_INTEGER_NAME'),
            'DESCRIPTION' => Loc::getMessage('YLAB_DDATA_DATA_UNIT_INTEGER_DESCRIPTION'),
            'TYPE' => 'integer',
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
        include Helpers::getModulePath() . '/admin/fragments/random_integer_settings_form.php';
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
            $iMin = (int)$arPrepareRequest['min'];
            $iMax = (int)$arPrepareRequest['max'];

            if ($iMax > $iMin) {
                return true;
            }
        }

        return false;
    }

    /**
     * Возвращает случайную запись соответствующего типа
     *
     * @return int
     * @throws \Exception
     */
    public function getValue()
    {
        if ($this->iUserNumber) {
            return $this->iUserNumber;
        }

        return rand($this->iMin, $this->iMax);
    }
}