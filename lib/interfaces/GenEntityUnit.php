<?php

namespace Ylab\Ddata\Interfaces;

use Bitrix\Main\HttpRequest;

/**
 * Interface GenEntityUnit
 * @package Ylab\Ddata\interfaces
 */
interface GenEntityUnit
{
    /**
     * Метод возврящает описывающий массив
     *
     * @return array
     */
    public static function getDescription();

    /**
     * Метод фозвращает html строку с полями предварительной настройки сущности
     *
     * @param HttpRequest $oRequest
     * @return string
     */
    public static function getPrepareForm(HttpRequest $oRequest);

    /**
     * Метод проверяет на валидность данные  предварительной настройки сущности
     *
     * @param HttpRequest $oRequest
     * @return boolean
     */
    public static function isValidPrepareForm(HttpRequest $oRequest);

    /**
     * Метод сохраняет профиль и его настройки полей
     *
     * @param $arProfile - Содержит массив полей таблицы профиля см. EntityUnitProfile
     * @param $arFields - Содержит массив полей профиля в виде $arFields["Код поля"]["Id генератора"] => "Json строка опций генератора"
     * @param $arCounts - Содержит массив полей кол-ва значений для множественных свойств вида $arCount["Код свойства"] => "Кол-во"
     * @return mixed
     */
    public static function setProfile(array $arProfile, array $arFields, array $arCounts);

    /**
     * Метод возвращает профиль и его настройки полей
     *
     * @param $iProfileID
     * @return mixed
     */
    public static function getProfile($iProfileID);

    /**
     * Записывает в базу 1 экземляр сгенерированной сущности
     *
     * @return mixed
     */
    public function genUnit();

    /**
     * Метод возвращает массив полей и свойств сущности
     *
     * @param HttpRequest $oRequest
     * @return array
     */
    public function getFields(HttpRequest $oRequest);
}