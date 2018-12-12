<?php

namespace Ylab\Ddata\Interfaces;

use Bitrix\Main\Web\Json;
use Bitrix\Main\Application;
use Ylab\Ddata\Orm\DataUnitOptionsTable;
use Bitrix\Main\Localization\Loc;

Loc::loadMessages(__FILE__);

/**
 * Class DataUnitClass
 * @package Ylab\Ddata\Interfaces
 */
abstract class DataUnitClass implements GenDataUnit
{
    /**
     * @var array|mixed
     */
    public $options = [];

    /**
     * DataUnitClass constructor.
     * @param $sProfileID
     * @param $sFieldCode
     * @param $sGeneratorID
     * @throws \Bitrix\Main\ArgumentException
     * @throws \Bitrix\Main\ObjectPropertyException
     * @throws \Bitrix\Main\SystemException
     */
    public function __construct($sProfileID, $sFieldCode, $sGeneratorID)
    {
        $this->options = self::getOptions($sProfileID, $sFieldCode, $sGeneratorID);
    }

    /**
     * @param $sProfileID
     * @param $sFieldCode
     * @param $sGeneratorID
     * @param $sJsonOptions
     * @param int $iCount
     * @return array|bool|int
     * @throws \Bitrix\Main\ArgumentException
     * @throws \Bitrix\Main\Db\SqlQueryException
     * @throws \Bitrix\Main\ObjectPropertyException
     * @throws \Bitrix\Main\SystemException
     */
    public static function setOptions($sProfileID, $sFieldCode, $sGeneratorID, $sJsonOptions, $iCount = 1)
    {
        $connection = Application::getConnection();
        $objOptionRow = DataUnitOptionsTable::getList([
            'filter' => [
                'PROFILE_ID' => $sProfileID,
                'FIELD_CODE' => $sFieldCode
            ]
        ]);

        if ($objOptionRow->getSelectedRowsCount() > 0) {
            $connection->startTransaction();
            $arOptionRow = $objOptionRow->fetch();
            $bResult = DataUnitOptionsTable::update($arOptionRow['ID'], [
                'PROFILE_ID' => $sProfileID,
                'FIELD_CODE' => $sFieldCode,
                'DATA_ID' => $sGeneratorID,
                'OPTIONS' => $sJsonOptions,
                'MULTIPLE' => $iCount > 1 ? 'Y' : 'N',
                'COUNT' => $iCount == 0 ? 1 : $iCount
            ]);
            if (!$bResult->isSuccess() && $bResult->getAffectedRowsCount() <= 0) {
                $connection->rollbackTransaction();
                throw new \Exception(Loc::getMessage('YLAB_DDATA_DATA_UNIT_OPTION_ERR_UPDATE',
                        ['#FIELD_GODE#' => $sFieldCode]) . implode(",<br>", $bResult->getErrorMessages()));
            }
            $connection->commitTransaction();
            return $bResult->getId();
        } else {
            $connection->startTransaction();
            $bResult = DataUnitOptionsTable::add([
                'PROFILE_ID' => $sProfileID,
                'FIELD_CODE' => $sFieldCode,
                'DATA_ID' => $sGeneratorID,
                'OPTIONS' => $sJsonOptions,
                'MULTIPLE' => $iCount > 1 ? 'Y' : 'N',
                'COUNT' => $iCount == 0 ? 1 : $iCount
            ]);
            if (!$bResult->isSuccess()) {
                $connection->rollbackTransaction();
                throw new \Exception(Loc::getMessage('YLAB_DDATA_DATA_UNIT_OPTION_ERR_ADD',
                        ['#FIELD_GODE#' => $sFieldCode]) . implode(",<br>", $bResult->getErrorMessages()));
            }
            $connection->commitTransaction();

            return $bResult->getId();
        }
    }

    /**
     * @param $sProfileID
     * @param $sFieldCode
     * @param $sGeneratorID
     * @return array|mixed
     * @throws \Bitrix\Main\ArgumentException
     * @throws \Bitrix\Main\ObjectPropertyException
     * @throws \Bitrix\Main\SystemException
     */
    public static function getOptions($sProfileID, $sFieldCode, $sGeneratorID)
    {
        if ($sGeneratorID && $sProfileID && $sFieldCode) {
            $optionsJSON = DataUnitOptionsTable::getList([
                'select' => [
                    'OPTIONS'
                ],
                'filter' => [
                    'PROFILE_ID' => $sProfileID,
                    'FIELD_CODE' => $sFieldCode,
                    'DATA_ID' => $sGeneratorID
                ]
            ])->Fetch();
            if ($optionsJSON) {
                return Json::decode($optionsJSON['OPTIONS']);
            }
        }

        return [];
    }

    /**
     * Удаление параметров типа данных
     *
     * @param $sProfileID
     * @param $sFieldCode
     * @throws \Bitrix\Main\ArgumentException
     * @throws \Bitrix\Main\Db\SqlQueryException
     * @throws \Bitrix\Main\ObjectPropertyException
     * @throws \Bitrix\Main\SystemException
     */
    public static function deleteOptions($sProfileID, $sFieldCode)
    {
        $oConnection = Application::getConnection();

        $objOptionRow = DataUnitOptionsTable::getList([
            'filter' => [
                'PROFILE_ID' => $sProfileID,
                'FIELD_CODE' => $sFieldCode
            ]
        ]);

        if ($objOptionRow->getSelectedRowsCount() > 0) {
            $oConnection->startTransaction();
            $arOptionRow = $objOptionRow->fetch();
            $bResult = DataUnitOptionsTable::delete($arOptionRow['ID']);
            if (!$bResult->isSuccess()) {
                $oConnection->rollbackTransaction();
                throw new \Exception(Loc::getMessage('YLAB_DDATA_DATA_UNIT_OPTION_ERR_DELETE',
                        ['#FIELD_СODE#' => $sFieldCode]) . implode(",<br>", $bResult->getErrorMessages()));
            }
            $oConnection->commitTransaction();
        }
    }
}