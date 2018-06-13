<?php

namespace Ylab\Ddata\Interfaces;

/**
 * Interface DeleteGenData
 * @package Ylab\Ddata\Interfaces
 */
interface DeleteGenData
{
    /**
     * Запись параметров сгенерированных данных
     * @param $iProfileID
     * @param $sProfileType
     * @param $iGenElementID
     * @return mixed
     */
    public static function setGenData($iProfileID, $sProfileType, $iGenElementID);

    /**
     * Получение параметров сгенерированных данных
     * @param $iProfileID
     * @return mixed
     */
    public static function getGenData($iProfileID);

    /**
     * Удаление сгенерированных данных
     * @param $arGenData
     * @return mixed
     */
    public static function deleteGenData($arGenData);
}