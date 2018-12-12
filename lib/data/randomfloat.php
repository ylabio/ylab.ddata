<?php

namespace Ylab\Ddata\Data;

use Bitrix\Main\Localization\Loc;
use Ylab\Ddata\Interfaces\DataUnitClass;
use Bitrix\Main\HttpRequest;
use Ylab\Ddata\Helpers;

Loc::loadMessages(__FILE__);

/**
 * Class RandomFloat
 * @package Ylab\Ddata\Data
 */
class RandomFloat extends RandomInteger
{
    /**
     * @return array
     */
    public static function getDescription()
    {
        return [
            "ID" => "random.float.unit",
            "NAME" => Loc::getMessage("YLAB_DDATA_DATA_UNIT_FLOAT_NAME"),
            "DESCRIPTION" => Loc::getMessage('YLAB_DDATA_DATA_UNIT_FLOAT_DESCRIPTION'),
            "TYPE" => "float",
            "CLASS" => __CLASS__
        ];
    }

    /**
     * @return int
     * @throws \Exception
     */
    public function getValue()
    {
        $iInteger = parent::getValue();
        $iInteger += (float)rand()/(float)getrandmax();

        return $iInteger;
    }
}