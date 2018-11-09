<?php

namespace Ylab\Ddata;

use Bitrix\Main\Application;
use Bitrix\Main\Entity;
use Bitrix\Main\Error;
use Bitrix\Main\ErrorCollection;
use Bitrix\Main\Localization\Loc;
use Ylab\Ddata\Orm;

Loc::loadMessages(__FILE__);

/**
 * Class ExportImportProfile - Класс реализует функционал импорта/экспорта профилей
 * @package Ylab\Ddata
 */
class ExportImportProfile
{
    /**
     * @var ErrorCollection
     */
    public static $oError = null;

    public static function onPrepare()
    {
        if (!(self::$oError instanceof ErrorCollection)) {
            self::$oError = new ErrorCollection();
        }
    }

    /**
     * @return array
     * @throws \Bitrix\Main\ArgumentException
     * @throws \Bitrix\Main\ObjectPropertyException
     * @throws \Bitrix\Main\SystemException
     */
    protected static function getProfileOptionsList()
    {
        $arProfileBD = Orm\EntityUnitProfileTable::getList()->fetchAll();
        $arOptionsBD = Orm\DataUnitOptionsTable::getList()->fetchAll();

        $arProfiles = array();
        foreach ($arProfileBD as $arProfile) {

            $arProfiles[$arProfile['ID']]['profile'] = $arProfile;

            foreach ($arOptionsBD as $arOption) {

                if ($arOption['PROFILE_ID'] == $arProfile['ID']) {

                    $arProfiles[$arProfile['ID']]['options'][] = $arOption;
                }
            }
        }

        //Массив с таблицами: 'ylab_ddata_entity_unit_profile', 'ylab_ddata_data_unit_options'.
        return $arProfiles;
    }

    /**
     * @param $sPath
     * @return mixed
     * @throws \Exception
     */
    protected static function parseJson($sPath)
    {
        $sFile = file_get_contents($sPath);
        $arJsonValue = json_decode($sFile, JSON_UNESCAPED_UNICODE);
        if ($arJsonValue === null && json_last_error() !== JSON_ERROR_NONE) {
            self::$oError->setError(new Error(Loc::getMessage('IMPORT_JSON_ERROR',
                ["#FILE_PATH#" => $sPath]), 'FILE_IO_ERROR'));
            return [];
        }
        return $arJsonValue;
    }

    /**
     * @return string
     */
    public static function pathDirExport()
    {
        return \Bitrix\Main\Application::getInstance()->getContext()->getServer()->getDocumentRoot() . '/upload/tmp/profiles';
    }

    /**
     * @return bool
     */
    protected static function newDir()
    {
        $sPathDir = self::pathDirExport();

        if (!file_exists($sPathDir)) {
            return mkdir($sPathDir, 0700);
        } else {
            return false;
        }
    }

    /**
     * @param $str
     * @return string
     */
    public static function translit($str)
    {
        $arParams = array("replace_space" => "-", "replace_other" => "-");
        $str = \Cutil::translit($str, "ru", $arParams);
        return $str;
    }

    /**
     * @param $sPathFile
     * @return bool
     */
    public static function readfile($sPathFile)
    {

        if (file_exists($sPathFile)) {
            header('Content-Description: File Transfer');
            header('Content-Type: application/octet-stream');
            header('Content-Disposition: attachment; filename="' . basename($sPathFile) . '"');
            header('Expires: 0');
            header('Cache-Control: must-revalidate');
            header('Pragma: public');
            header('Content-Length: ' . filesize($sPathFile));
            readfile($sPathFile);
            exit;
        }
        return false;
    }

    /**
     * @param $iIdProfile
     * @return string
     */
    public static function getPathFile($iIdProfile)
    {
        $sPathDir = self::pathDirExport();
        $arProfiles = self::getProfileOptionsList();

        $sName = self::translit($arProfiles[$iIdProfile]['profile']['NAME']);
        $sXML_ID = self::translit($arProfiles[$iIdProfile]['profile']['XML_ID']);

        return $sPathDir . '/' . $sName . '_' . $sXML_ID . '.json';
    }

    /**
     * @param $iIdProfile
     * @return array|ErrorCollection|bool|int
     * @throws \Bitrix\Main\ArgumentException
     * @throws \Bitrix\Main\ObjectPropertyException
     * @throws \Bitrix\Main\SystemException
     */
    public static function export($iIdProfile)
    {
        self::onPrepare();
        $sPathDir = self::pathDirExport();
        $arProfiles = self::getProfileOptionsList();

        $sName = self::translit($arProfiles[$iIdProfile]['profile']['NAME']);
        $sXML_ID = self::translit($arProfiles[$iIdProfile]['profile']['XML_ID']);

        self::newDir();

        $arJsonValue = file_put_contents($sPathDir . '/' . $sName . '_' . $sXML_ID . '.json',
            json_encode($arProfiles[$iIdProfile], JSON_UNESCAPED_UNICODE));

        if ($arJsonValue === null && json_last_error() !== JSON_ERROR_NONE) {
            self::$oError->setError(new Error(Loc::getMessage('EXPORT_ERROR'),
                'EXPORT_ERROR'));
        }

        return self::$oError;
    }

    /**
     * @param $sPath
     * @return ErrorCollection
     * @throws \Exception
     */
    public static function import($sPath)
    {
        self::onPrepare();
        //Получение данных
        $arProfiles = ExportImportProfile::getProfileOptionsList();
        $arProfileImport = ExportImportProfile::parseJson($sPath);
        $oConnection = Application::getConnection();

        if (self::$oError->count()) {
            return self::$oError;
        }

        //Обновление или добавление
        if (is_array($arProfiles)) {
            foreach ($arProfiles as $arProfileBD) {
                if ($arProfileBD['profile']['XML_ID'] == $arProfileImport['profile']['XML_ID']) {

                    //Обновление профиля
                    $oConnection->startTransaction();
                    $oResProfile = Orm\EntityUnitProfileTable::update($arProfileImport['profile']['ID'], [
                        'NAME' => $arProfileImport['profile']['NAME'],
                        'TYPE' => $arProfileImport['profile']['TYPE'],
                        'OPTIONS' => $arProfileImport['profile']['OPTIONS']
                    ]);

                    if (!$oResProfile->isSuccess()) {
                        $oConnection->rollbackTransaction();
                        self::$oError->setError(new Error(Loc::getMessage('IMPORT_BD_UPLOAD_PROFILE_ERROR'),
                            'BD_UPLOAD_PROFILE_ERROR'));
                        return self::$oError;
                    }

                    //Обновление полей профиля
                    $idProfile = $oResProfile->getID();

                    foreach ($arProfileBD['options'] as $arOption) {
                        $oResFields = Orm\DataUnitOptionsTable::update($idProfile, [
                            'FIELD_CODE' => $arOption['FIELD_CODE'],
                            'DATA_ID' => $arOption['DATA_ID'],
                            'OPTIONS' => $arOption['OPTIONS']
                        ]);

                        if (!$oResFields->isSuccess()) {
                            $oConnection->rollbackTransaction();
                            self::$oError->setError(new Error(Loc::getMessage('IMPORT_BD_UPLOAD_FIELDS_ERROR'),
                                'BD_UPLOAD_FIELDS_ERROR'));
                        } else {
                            $oConnection->commitTransaction();
                        }
                    }

                    $arProfileImport = false;
                }
            }

            //Добавление профиля
            if ($arProfileImport) {

                $oConnection->startTransaction();
                $oResProfile = Orm\EntityUnitProfileTable::add([
                    'NAME' => $arProfileImport['profile']['NAME'],
                    'TYPE' => $arProfileImport['profile']['TYPE'],
                    'XML_ID' => $arProfileImport['profile']['XML_ID'],
                    'OPTIONS' => $arProfileImport['profile']['OPTIONS']
                ]);

                if (!$oResProfile->isSuccess()) {
                    $oConnection->rollbackTransaction();
                    self::$oError->setError(new Error(Loc::getMessage('IMPORT_BD_ADD_PROFILE_ERROR'),
                        'BD_ADD_PROFILE_ERROR'));
                }

                //Добавление полей профиля
                $idProfile = $oResProfile->getID();

                foreach ($arProfileImport['options'] as $arOption) {
                    $oResFields = Orm\DataUnitOptionsTable::add([
                        'PROFILE_ID' => $idProfile,
                        'FIELD_CODE' => $arOption['FIELD_CODE'],
                        'DATA_ID' => $arOption['DATA_ID'],
                        'OPTIONS' => $arOption['OPTIONS']
                    ]);

                    if (!$oResFields->isSuccess()) {
                        $oConnection->rollbackTransaction();
                        self::$oError->setError(new Error(Loc::getMessage('IMPORT_BD_ADD_FIELDS_ERROR'),
                            'BD_ADD_FIELDS_ERROR'));
                    } else {
                        $oConnection->commitTransaction();
                    }
                }
            }
        }

        return self::$oError;
    }
}