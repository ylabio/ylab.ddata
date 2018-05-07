<?php

namespace Ylab\Ddata\Interfaces;

use Bitrix\Main\Application;
use Bitrix\Main\Web\Json;
use Ylab\Ddata\Orm\DataUnitOptionsTable;
use Ylab\Ddata\Orm\EntityUnitProfileTable;
use Ylab\Ddata\LoadUnits;
use Bitrix\Main\Localization\Loc;

Loc::loadMessages(__FILE__);

/**
 * Class EntityUnitClass
 * @package Ylab\Ddata\Interfaces
 */
abstract class EntityUnitClass implements GenEntityUnit
{
    public $profile;

    /**
     * EntityUnitClass constructor.
     * @param bool $iProfileID
     * @throws \Bitrix\Main\ArgumentException
     * @throws \Bitrix\Main\SystemException
     */
    public function __construct($iProfileID = false)
    {
        $profile = self::getProfile($iProfileID);
        if (!empty($profile['OPTIONS'])) {
            $profile['OPTIONS'] = Json::decode($profile['OPTIONS']);
        }
        $this->profile = $profile;
    }

    /**
     * @param array $arProfile
     * @param array $arFields
     * @return int
     * @throws \Exception
     */
    public static function setProfile(array $arProfile, array $arFields)
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
                    continue;
                }
                $sDataId = (array_keys($arField)[0]);
                $sDataOption = $arField[$sDataId];
                $iDataIndex = array_search($sDataId, array_column($arData, 'ID'));
                $sDataClass = $arData[$iDataIndex]['CLASS'];
                $sDataClass::setOptions($iProfileID, $sFieldCode, $sDataId, $sDataOption);
            }
        }

        return $iProfileID;
    }

    /**
     * @param $iProfileID
     * @return mixed|array
     * @throws \Bitrix\Main\ArgumentException
     * @throws \Bitrix\Main\SystemException
     */
    public static function getProfile($iProfileID)
    {
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
}