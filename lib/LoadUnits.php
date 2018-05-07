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
        $classInterface = 'Ylab\Ddata\Interfaces\GenDataUnit';
        $arClassesList = $this->getClassFiles($classFileDir);
        $arUnits = [];
        foreach ($arClassesList as $class) {
            $class = $classNameSpace . '\\' . $class;
            if (key_exists($classInterface, class_implements($class))) {
                $arUnits[] = $class::getDescription();
            }
        }

        $oEvent = new Event("ylab.ddata", "OnAfterLoadDataUnits", $arUnits);
        $oEvent->send();
        if ($oEvent->getResults()) {
            /** @var \Bitrix\Main\EventResult $eventResult */
            foreach ($oEvent->getResults() as $eventResult) {
                $arUnits[] = $eventResult->getParameters();
            }
        }

        return $arUnits;
    }

    /**
     * @return array
     */
    public function getEntityUnits()
    {
        $classFileDir = '/lib/entity';
        $classNameSpace = 'Ylab\Ddata\Entity';
        $classInterface = 'Ylab\Ddata\Interfaces\GenEntityUnit';
        $arClassesList = $this->getClassFiles($classFileDir);
        $arUnits = [];
        foreach ($arClassesList as $class) {
            $class = $classNameSpace . '\\' . $class;
            if (key_exists($classInterface, class_implements($class))) {
                $arUnits[] = $class::getDescription();
            }
        }

        $oEvent = new Event("ylab.ddata", "OnAfterLoadEntityUnits", $arUnits);
        $oEvent->send();
        if ($oEvent->getResults()) {
            /** @var \Bitrix\Main\EventResult $eventResult */
            foreach ($oEvent->getResults() as $eventResult) {
                $arUnits[] = $eventResult->getParameters();
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