<?php

namespace Ylab\Ddata\Interfaces;

use Bitrix\Main\Web\Json;
use Bitrix\Main\Application;
use Ylab\Ddata\Orm\DataUnitOptionsTable;
use Bitrix\Main\Localization\Loc;
use Bitrix\Main\HttpRequest;

Loc::loadMessages(__FILE__);

/**
 * Class DataUnitClass
 *
 * @package Ylab\Ddata\Interfaces
 */
abstract class DataUnitClass
{
    /**
     * @var array|mixed
     */
    public $options = [];

    /**
     * Метод возврящает описывающий массив
     *
     * @return array
     */
    public abstract function getDescription();

    /**
     * DataUnitClass constructor.
     *
     * @param $sProfileID
     * @param $sFieldCode
     * @param $sGeneratorID
     *
     * @throws \Bitrix\Main\ArgumentException
     */
    public function __construct($sProfileID, $sFieldCode, $sGeneratorID)
    {
        if (!empty($sProfileID) && !empty($sFieldCode) && !empty($sGeneratorID)) {
            $this->options = $this->getOptions($sProfileID, $sFieldCode, $sGeneratorID);
        }
    }

    /**
     * Метод getOptionForm возвращает html строку формы с настройкой генератора если таковые необходимы
     *
     * @param HttpRequest $request
     *
     * @return mixed
     */
    abstract public function getOptionForm(HttpRequest $request);

    /**
     * Метод isValidateOptions проверяет на валидность данные настройки генератора
     *
     * @param HttpRequest $request
     *
     * @return mixed
     */
    abstract public function isValidateOptions(HttpRequest $request);

    /**
     * Метод возвращает случайные данные соответствующего типа
     *
     * @return mixed
     */
    abstract public function getValue();

    /**
     * Метод setOptions записывает параметры типа данных
     *
     * @param     $sProfileID
     * @param     $sFieldCode
     * @param     $sGeneratorID
     * @param     $sJsonOptions
     * @param int $iCount
     *
     * @return array|bool|int
     * @throws \Bitrix\Main\ArgumentException
     * @throws \Bitrix\Main\Db\SqlQueryException
     * @throws \Bitrix\Main\ObjectPropertyException
     * @throws \Bitrix\Main\SystemException
     */
    public function setOptions($sProfileID, $sFieldCode, $sGeneratorID, $sJsonOptions, $iCount = 1)
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
     * Метод getOptions получает параметры типа данных
     *
     * @param $sProfileID
     * @param $sFieldCode
     * @param $sGeneratorID
     *
     * @return array|mixed
     * @throws \Bitrix\Main\ArgumentException
     * @throws \Bitrix\Main\ObjectPropertyException
     * @throws \Bitrix\Main\SystemException
     */
    public function getOptions($sProfileID, $sFieldCode, $sGeneratorID)
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
     * Метод deleteOptions удаляет параметры типа данных
     *
     * @param $sProfileID
     * @param $sFieldCode
     *
     * @throws \Bitrix\Main\ArgumentException
     * @throws \Bitrix\Main\Db\SqlQueryException
     * @throws \Bitrix\Main\ObjectPropertyException
     * @throws \Bitrix\Main\SystemException
     */
    public function deleteOptions($sProfileID, $sFieldCode)
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