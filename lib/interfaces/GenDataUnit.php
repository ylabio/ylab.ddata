<?php

namespace Ylab\Ddata\Interfaces;

use Bitrix\Main\HttpRequest;

/**
 * Interface GenDataUnit
 * @package Ylab\Ddata\interfaces
 */
interface GenDataUnit
{
    /**
     * Метод возврящает массив описывающий тип данных. ID, Имя, scalar type php
     *
     * @return array
     */
    public static function getDescription();

    /**
     * Метод возвращает html строку формы с настройкой генератора если таковые необходимы
     *
     * @param HttpRequest $request
     * @return mixed
     */
    public static function getOptionForm(HttpRequest $request);

    /**
     * Метод проверяет на валидность данные настройки генератора
     *
     * @param HttpRequest $request
     * @return mixed
     */
    public static function isValidateOptions(HttpRequest $request);

    /**
     * Запись параметров типа данных
     *
     * @param $sGeneratorID
     * @param $sProfileID
     * @param $sFieldCode
     * @param $sJsonOptions
     * @return mixed
     */
    public static function setOptions($sGeneratorID, $sProfileID, $sFieldCode, $sJsonOptions);

    /**
     * Получение параметров типа данных
     *
     * @param $sGeneratorID
     * @param $sProfileID
     * @param $sFieldCode
     * @return mixed
     */
    public static function getOptions($sGeneratorID, $sProfileID, $sFieldCode);

    /**
     * Возвращает случайную запись соответствующего типа
     *
     * @return mixed
     */
    public function getValue();
}