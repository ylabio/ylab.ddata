<?php

namespace Ylab\Ddata\Interfaces;

use Bitrix\Main\Application;
use Bitrix\Main\Loader;
use Bitrix\Main\Web\Json;
use Ylab\Ddata\Orm\DataUnitGenElementsTable;
use Bitrix\Main\Localization\Loc;
use Ylab\Ddata\Orm\EntityUnitProfileTable;
use Bitrix\Highloadblock\HighloadBlockTable;

Loc::loadMessages(__FILE__);

/**
 * Class DeleteDataClass
 * @package Ylab\Ddata\Interfaces
 */
class DeleteDataClass implements DeleteGenData
{

    /**
     * Запись параметров сгенерированных данных
     * @param $iProfileID
     * @param $sProfileType
     * @param $iGenElementID
     * @throws \Exception
     * @return mixed
     */
    public static function setGenData($iProfileID, $sProfileType, $iGenElementID)
    {
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
     * Получение параметров сгенерированных данных
     * @param $iProfileID
     * @throws \Exception
     * @return mixed
     */
    public static function getGenData($iProfileID)
    {
        if ($iProfileID) {
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
     * Удаление сгенерированных данных
     * @param $iProfileID
     * @return mixed
     * @throws \Bitrix\Main\LoaderException
     * @throws \Bitrix\Main\Db\SqlQueryException
     * @throws \Exception
     */
    public static function deleteGenData($iProfileID)
    {
        if ($iProfileID) {
            $arGenData = DataUnitGenElementsTable::getList([
                'filter' => ['=PROFILE_ID' => $iProfileID]
            ])->fetchAll();

            $objProfile = EntityUnitProfileTable::getList([
                'filter' => ['=ID' => $iProfileID]
            ]);
            $arProfile = $objProfile->Fetch();
            $arGenDataID = array_column($arGenData, 'ID');
            $arElementsID = array_column($arGenData, 'GEN_ELEMENT_ID');
            $sProfileType = $arProfile['TYPE'];
            $arOptions = Json::decode($arProfile['OPTIONS']);
            $connection = Application::getConnection();
            $connection->startTransaction();
            switch ($sProfileType) {
                case "highloadblock-element":
                    Loader::includeModule('highloadblock');
                    $rsData = HighloadBlockTable::getList([
                        'filter' => [
                            'ID' => $arOptions['highloadblock_id']
                        ]
                    ]);
                    if (($arData = $rsData->fetch())) {
                        $oEntity = HighloadBlockTable::compileEntity($arData);
                    }
                    $oEntityDataClass = $oEntity->getDataClass();
                    foreach ($arElementsID as $iElementID) {
                        $oResult = $oEntityDataClass::delete($iElementID);
                        if (!$oResult->isSuccess()) {
                            $connection->rollbackTransaction();
                            throw new \Exception(Loc::getMessage('YLAB_DDATA_DELETE_DATA_OPTION_ERR_DELETE',
                                ['#ELEMENT_ID#' => $iElementID]));
                        }
                        $connection->commitTransaction();
                    }
                    break;
                case "iblock-element":
                    Loader::includeModule('iblock');
                    foreach ($arElementsID as $iElementID) {
                        if (!\CIBlockElement::Delete($iElementID)) {
                            $connection->rollbackTransaction();
                            throw new \Exception(Loc::getMessage('YLAB_DDATA_DELETE_DATA_OPTION_ERR_DELETE',
                                ['#ELEMENT_ID#' => $iElementID]));
                        }
                        $connection->commitTransaction();
                    }
                    break;
                case "orm":
                    $sOrmNamespace = $arOptions['namespace'];
                    foreach ($arElementsID as $iElementID) {
                        $oResult = $sOrmNamespace::delete($iElementID);
                        if (!$oResult->isSuccess()) {
                            $connection->rollbackTransaction();
                            throw new \Exception(Loc::getMessage('YLAB_DDATA_DELETE_DATA_OPTION_ERR_DELETE',
                                ['#ELEMENT_ID#' => $iElementID]));
                        }
                    }
                    break;
                case "user":
                    foreach ($arElementsID as $iElementID) {
                        if (!\CUser::Delete($iElementID)) {
                            $connection->rollbackTransaction();
                            throw new \Exception(Loc::getMessage('YLAB_DDATA_DELETE_DATA_OPTION_ERR_DELETE',
                                ['#ELEMENT_ID#' => $iElementID]));
                        }
                        $connection->commitTransaction();
                    }
                    break;
            }

            foreach ($arGenDataID as $iGenDataID) {
                $oResult = DataUnitGenElementsTable::delete($iGenDataID);
                if (!$oResult->isSuccess()) {
                    $connection->rollbackTransaction();
                    throw new \Exception(Loc::getMessage('YLAB_DDATA_DELETE_DATA_OPTION_ERR_DELETE',
                        ['#ELEMENT_ID#' => $iElementID]));
                }
            }
            $connection->commitTransaction();
        }
    }
}