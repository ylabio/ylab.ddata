<?php

namespace Ylab\Ddata\Interfaces;

use Bitrix\Main\Application;
use Bitrix\Main\HttpRequest;
use Bitrix\Main\Web\Json;
use Ylab\Ddata\Orm\DataUnitGenElementsTable;
use Ylab\Ddata\Orm\DataUnitOptionsTable;
use Ylab\Ddata\Orm\EntityUnitProfileTable;
use Ylab\Ddata\LoadUnits;
use Bitrix\Main\Localization\Loc;

Loc::loadMessages(__FILE__);

/**
 * Class EntityUnitClass
 *
 * @package Ylab\Ddata\Interfaces
 */
abstract class EntityUnitClass
{
    /**
     * @var array Запись профиля с параметрами
     */
    public $profile;

    /**
     * Метод возвращает описывающий массив
     *
     * @return array
     */
    public abstract function getDescription();

    /**
     * EntityUnitClass constructor.
     *
     * @param bool $iProfileID
     *
     * @throws \Bitrix\Main\ArgumentException
     * @throws \Bitrix\Main\SystemException
     */
    public function __construct($iProfileID = false)
    {
        $profile = $this->getProfile($iProfileID);
        if (!empty($profile['OPTIONS'])) {
            $profile['OPTIONS'] = Json::decode($profile['OPTIONS']);
        }
        $this->profile = $profile;
    }

    /**
     * Метод возвращает html строку с полями предварительной настройки сущности
     *
     * @param HttpRequest $oRequest
     *
     * @return string
     */
    public abstract function getPrepareForm(HttpRequest $oRequest);

    /**
     * Метод проверяет на валидность данные  предварительной настройки сущности
     *
     * @param HttpRequest $oRequest
     *
     * @return boolean
     */
    public abstract function isValidPrepareForm(HttpRequest $oRequest);

    /**
     * Записывает в базу 1 экземляр сгенерированной сущности
     *
     * @return mixed
     */
    public abstract function genUnit();

    /**
     * Метод возвращает массив полей и свойств сущности
     *
     * @param HttpRequest $oRequest
     *
     * @return array
     */
    public abstract function getFields(HttpRequest $oRequest);

    /**
     * Метод сохраняет профиль и его настройки полей
     *
     * @param array $arProfile - Содержит массив полей таблицы профиля см. EntityUnitProfile
     * @param array $arFields  - Содержит массив полей профиля в виде $arFields["Код поля"]["Id генератора"] => "Json
     *                         строка опций генератора"
     * @param array $arCounts  - Содержит массив полей кол-ва значений для множественных свойств вида $arCount["Код
     *                         свойства"] => "Кол-во"
     *
     * @return mixed
     * @throws \Bitrix\Main\Db\SqlQueryException
     */
    public function setProfile(array $arProfile, array $arFields, array $arCounts)
    {
        $connection = Application::getConnection();

        if (isset($arProfile['ID'])) {
            $connection->startTransaction();
            $iProfileID = $arProfile['ID'];
            unset($arProfile['ID']);
            $bResult = EntityUnitProfileTable::update($iProfileID, $arProfile);
            if (!$bResult->isSuccess()) {
                $connection->rollbackTransaction();
                throw new \Exception(Loc::getMessage("YLAB_DDATA_ENTITY_UNIT_ERR_UPDATE") . implode(",<br>",
                        $bResult->getErrorMessages()));
            }
            $connection->commitTransaction();
        } else {
            $connection->startTransaction();
            $bResult = EntityUnitProfileTable::add($arProfile);
            if (!$bResult->isSuccess()) {
                $connection->rollbackTransaction();
                throw new \Exception(Loc::getMessage("YLAB_DDATA_ENTITY_UNIT_ERR_ADD") . implode(",<br>",
                        $bResult->getErrorMessages()));
            }
            $iProfileID = $bResult->getId();
            $connection->commitTransaction();
        }

        if ($iProfileID >= 0) {
            $objLoader = new LoadUnits();
            $arData = $objLoader->getDataUnits();
            foreach ($arFields as $sFieldCode => $arField) {
                if (!is_array($arField)) {
                    $sDataClass = $arData[0]['CLASS'];
                    $sDataClass::deleteOptions($iProfileID, $sFieldCode);
                    continue;
                }
                $sDataId = (array_keys($arField)[0]);
                $sDataOption = $arField[$sDataId];
                $iDataIndex = array_search($sDataId, array_column($arData, 'ID'));
                $sDataClass = $arData[$iDataIndex]['CLASS'];
                if (!empty($arCounts) && array_key_exists($sFieldCode, $arCounts)) {
                    $iCount = $arCounts[$sFieldCode];
                    $sDataClass::setOptions($iProfileID, $sFieldCode, $sDataId, $sDataOption, $iCount);
                } else {
                    $sDataClass::setOptions($iProfileID, $sFieldCode, $sDataId, $sDataOption);
                }
            }
        }

        return $iProfileID;
    }

    /**
     * Метод возвращает профиль и его настройки полей
     *
     * @param $iProfileID
     *
     * @return mixed
     * @throws \Bitrix\Main\ArgumentException
     * @throws \Bitrix\Main\ObjectPropertyException
     * @throws \Bitrix\Main\SystemException
     */
    public function getProfile($iProfileID)
    {
        if (empty($iProfileID) && !empty($this->profile['ID'])) {
            $iProfileID = $this->profile['ID'];
        }

        if ($iProfileID > 0) {
            $objLoader = new LoadUnits();
            $arDataClasses = $objLoader->getDataUnits();
            $objProfile = EntityUnitProfileTable::getList([
                'filter' => ['=ID' => $iProfileID]
            ]);
            $arProfile = $objProfile->fetch();
            if (!empty($arProfile)) {
                $arProfile['FIELDS'] = [];
                $objDataUnit = DataUnitOptionsTable::getList([
                    'filter' => ['PROFILE_ID' => $iProfileID]
                ]);
                $arDataUnit = $objDataUnit->fetchAll();
                if (!empty($arDataUnit)) {
                    foreach ($arDataUnit as &$field) {
                        $iIndexInDataArray = array_search($field['DATA_ID'], array_column($arDataClasses, 'ID'));
                        $sDataClass = $arDataClasses[$iIndexInDataArray]['CLASS'];
                        $field['OBJECT'] = new $sDataClass($iProfileID, $field['FIELD_CODE'], $field['DATA_ID']);
                    }
                }
                $arProfile['FIELDS'] = $arDataUnit;
            }
            return $arProfile;
        }

        return false;
    }

    /**
     * Получение лога сгенерированных елементов для профиля
     *
     * @param $iProfileID
     *
     * @return mixed
     * @throws \Bitrix\Main\ArgumentException
     * @throws \Bitrix\Main\ObjectPropertyException
     * @throws \Bitrix\Main\SystemException
     */
    public function getGenData($iProfileID = false)
    {
        if (empty($iProfileID) && !empty($this->profile['ID'])) {
            $iProfileID = $this->profile['ID'];
        }

        if ($iProfileID > 0) {
            $arGenData = DataUnitGenElementsTable::getList([
                'filter' => ['=PROFILE_ID' => $iProfileID]
            ])->fetchAll();
            if (!empty($arGenData)) {

                return $arGenData;
            }
        }

        return false;
    }

    /**
     * Запись лого сгенерированных елементов для профиля
     *
     * @param $iGenElementID
     *
     * @return mixed
     * @throws \Bitrix\Main\Db\SqlQueryException
     * @throws \Exception
     */
    public function setGenData($iGenElementID)
    {
        $iProfileID = $this->profile['ID'];
        $sProfileType = $this->profile['TYPE'];
        if ($iProfileID <= 0) {
            return false;
        }

        $connection = Application::getConnection();
        $connection->startTransaction();
        $bResult = DataUnitGenElementsTable::add([
            'PROFILE_ID' => $iProfileID,
            'PROFILE_TYPE' => $sProfileType,
            'GEN_ELEMENT_ID' => $iGenElementID
        ]);

        if (!$bResult->isSuccess()) {
            $connection->rollbackTransaction();
            throw new \Exception(Loc::getMessage('YLAB_DDATA_DELETE_DATA_OPTION_ERR_ADD',
                    ['#PROFILE_ID#' => $iProfileID]) . implode(",<br>", $bResult->getErrorMessages()));
        }

        $connection->commitTransaction();

        return $bResult->getId();
    }


    /**
     * Удаление сгенерированных данных
     *
     * @return mixed
     * @throws \Bitrix\Main\ArgumentException
     * @throws \Bitrix\Main\Db\SqlQueryException
     * @throws \Bitrix\Main\ObjectPropertyException
     * @throws \Bitrix\Main\SystemException
     */
    public abstract function deleteGenData();
}