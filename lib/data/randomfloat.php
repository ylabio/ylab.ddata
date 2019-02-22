<?php

namespace Ylab\Ddata\Data;

use Bitrix\Main\Localization\Loc;
use Ylab\Ddata\Interfaces\DataUnitClass;
use Bitrix\Main\HttpRequest;
use Ylab\Ddata\Helpers;

Loc::loadMessages(__FILE__);

/**
 * Генерация случайного дробного числа
 *
 * Class RandomFloat
 * @package Ylab\Ddata\Data
 */
class RandomFloat extends RandomInteger
{
    /**
     * RandomFloat constructor.
     * @param string $sProfileID
     * @param string $sFieldCode
     * @param string $sGeneratorID
     * @throws \Bitrix\Main\ArgumentException
     * @throws \Bitrix\Main\ObjectPropertyException
     * @throws \Bitrix\Main\SystemException
     */
    public function __construct(string $sProfileID = '', string $sFieldCode = '', string $sGeneratorID = '')
    {
        parent::__construct($sProfileID, $sFieldCode, $sGeneratorID);
    }

    /**
     * @return array
     */
    public  function getDescription()
    {
        return [
            'ID' => "random.float.unit",
            'NAME' => Loc::getMessage('YLAB_DDATA_DATA_UNIT_FLOAT_NAME'),
            'DESCRIPTION' => Loc::getMessage('YLAB_DDATA_DATA_UNIT_FLOAT_DESCRIPTION'),
            'TYPE' => 'float',
            'CLASS' => __CLASS__
        ];
    }

    /**
     * Возвращает случайную запись соответствующего типа
     *
     * @return int
     * @throws \Exception
     */
    public function getValue()
    {
        $iInteger = parent::getValue();
        $iInteger += (float)rand() / (float)getrandmax();

        return $iInteger;
    }
}