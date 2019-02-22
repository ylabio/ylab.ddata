<?php

namespace Ylab\Ddata;

use Bitrix\Main\Event;

/**
 * Class LoadUnits
 * @package Ylab\Ddata
 */
class LoadUnits
{
    /**
     * @var null
     */
    protected $ModulePath = null;

    /**
     * LoadUnits constructor.
     */
    public function __construct()
    {
        $this->ModulePath = Helpers::getModulePath();
    }

    /**
     * @return array
     */
    public function getDataUnits()
    {
        $classFileDir = '/lib/data';
        $classNameSpace = 'Ylab\Ddata\Data';
        $classInterface = 'Ylab\Ddata\Interfaces\DataUnitClass';
        $arClassesList = $this->getClassFiles($classFileDir);
        $arUnits = [];
        foreach ($arClassesList as $class) {
            $class = $classNameSpace . '\\' . $class;
            if (key_exists($classInterface, class_parents($class))) {
                $arUnits[] = $class::getDescription();
            }
        }

        $oEvent = new Event("ylab.ddata", "OnAfterLoadDataUnits");
        $oEvent->send();
        if ($oEvent->getResults()) {
            /** @var \Bitrix\Main\EventResult $eventResult */
            foreach ($oEvent->getResults() as $eventResult) {
                $sClass = $eventResult->getParameters();
                if (class_exists($sClass) && key_exists($classInterface, class_parents($sClass))) {
                    $arUnits[] = $sClass::getDescription();
                }
            }
        }

        return $arUnits;
    }

    /**
     * Выбирает массив данных генератора по id
     *
     * @param string $sId - Идентификатор нужного генератора
     * @return array - Возвращает массив с данными
     */
    public function getDataUnitById(string $sId){
        $arDataForm = [];

        foreach ($this->getDataUnits() as $arData) {
            if ($arData['ID'] == $sId) {
                $arDataForm = $arData;
                break;
            }
        }

        return $arDataForm;
    }

    /**
     * @return array
     */
    public function getEntityUnits()
    {
        $classFileDir = '/lib/entity';
        $classNameSpace = 'Ylab\Ddata\Entity';
        $classInterface = 'Ylab\Ddata\Interfaces\EntityUnitClass';
        $arClassesList = $this->getClassFiles($classFileDir);
        $arUnits = [];
        foreach ($arClassesList as $class) {
            $class = $classNameSpace . '\\' . $class;
            if (key_exists($classInterface, class_parents($class))) {
                $arUnits[] = $class::getDescription();
            }
        }

        $oEvent = new Event("ylab.ddata", "OnAfterLoadEntityUnits");
        $oEvent->send();
        if ($oEvent->getResults()) {
            /** @var \Bitrix\Main\EventResult $eventResult */
            foreach ($oEvent->getResults() as $eventResult) {
                $sClass = $eventResult->getParameters();
                if (class_exists($sClass) && key_exists($classInterface, class_parents($sClass))) {
                    $arUnits[] = $sClass::getDescription();
                }
            }
        }

        return $arUnits;
    }

    /**
     * @param $dirPath
     * @return array|bool
     */
    protected function getClassFiles($dirPath)
    {
        $sPath = $this->ModulePath . $dirPath;
        if (!is_dir($sPath)) {
            return false;
        }

        $arFiles = glob($sPath . "/*.php");
        $arFiles = array_map(function ($sFile) {
            return str_replace('.php', '', basename($sFile));
        }, $arFiles);

        sort($arFiles);

        return $arFiles;
    }
}