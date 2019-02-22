<?php

namespace Ylab\Ddata\Entity;

use Bitrix\Main\Application;
use Bitrix\Main\Entity;
use Bitrix\Main\HttpRequest;
use Bitrix\Main\Localization\Loc;
use Bitrix\Main\Type\DateTime;
use Ylab\Ddata\Interfaces\EntityUnitClass;
use Ylab\Ddata\Helpers;
use Ylab\Ddata\Orm\DataUnitGenElementsTable;

Loc::loadMessages(__FILE__);

/**
 * Class Orm
 *
 * @package Ylab\Ddata\entity
 */
class Orm extends EntityUnitClass
{
    /**
     * @var null namespace ORM
     */
    public $sOrmNamespace = null;

    /**
     * Метод возврящает описывающий массив
     *
     * @return array
     */
    public function getDescription()
    {
        return [
            'ID' => 'orm',
            'NAME' => Loc::getMessage('YLAB_DDATA_ORM_ENTITY_NAME'),
            'DESCRIPTION' => Loc::getMessage('YLAB_DDATA_ORM_ENTITY_DESCRIPTION'),
            'TYPE' => 'orm',
            'CLASS' => __CLASS__
        ];
    }

    /**
     * Orm constructor.
     *
     * @param $iProfileID
     *
     * @throws \Bitrix\Main\ArgumentException
     * @throws \Bitrix\Main\SystemException
     */
    public function __construct($iProfileID)
    {
        parent::__construct($iProfileID);

        if (!empty($this->profile['OPTIONS']['namespace'])) {
            $this->sOrmNamespace = $this->profile['OPTIONS']['namespace'];
        }

        if (!empty($this->profile['FIELDS'])) {
            $arTmp = [];
            $arFields = $this->getFields();

            foreach ($this->profile['FIELDS'] as $arField) {
                if (isset($arFields['FIELDS'][$arField['FIELD_CODE']])) {
                    $arTmp['FIELDS'][$arField['FIELD_CODE']] = $arField;
                } else {
                    if (isset($arFields['PROPERTIES'][$arField['FIELD_CODE']])) {
                        $arTmp['PROPERTIES'][$arField['FIELD_CODE']] = $arField;
                    }
                }
            }

            $this->profile['FIELDS'] = $arTmp;
        }
    }

    /**
     * @inheritdoc
     *
     * @param HttpRequest $oRequest
     *
     * @return string
     * @throws \Bitrix\Main\LoaderException
     */
    public function getPrepareForm(HttpRequest $oRequest)
    {
        $arPrepareRequest = $oRequest->get('prepare');

        ob_start();
        include Helpers::getModulePath() . '/admin/fragments/orm_prepare_form.php';
        $tpl = ob_get_contents();
        ob_end_clean();
        return $tpl;
    }

    /**
     * @inheritdoc
     *
     * @param HttpRequest $oRequest
     *
     * @return boolean
     */
    public function isValidPrepareForm(HttpRequest $oRequest)
    {
        $arPrepareRequest = $oRequest->get('prepare');

        if (!empty($arPrepareRequest['namespace']) && class_exists($arPrepareRequest['namespace'], true)) {
            $oCheckClass = new $arPrepareRequest['namespace'];
            if ($oCheckClass instanceof \Bitrix\Main\Entity\DataManager) {

                return true;
            }
        }

        return false;
    }

    /**
     * @inheritdoc
     *
     * @param HttpRequest|null $oRequest
     *
     * @return array
     */
    public function getFields(HttpRequest $oRequest = null)
    {
        if ($oRequest) {
            $arPrepareRequest = $oRequest->get('prepare');
        }

        if (!empty($this->sOrmNamespace)) {
            $sOrmNamespace = $this->sOrmNamespace;
        } else {
            if (!empty($arPrepareRequest['namespace'])) {
                $sOrmNamespace = htmlspecialcharsbx($arPrepareRequest['namespace']);
            }
        }

        if (!class_exists($sOrmNamespace)) {
            return [];
        }

        $arResult = [];
        $arFields = $sOrmNamespace::getMap();

        foreach ($arFields as $key => $arField) {
            if (is_object($arField) && !$arField instanceof Entity\ReferenceField) {
                if (!$arField->isAutocomplete()) {
                    $sName = $arField->getName();
                    $arResult['FIELDS'][$sName] = [
                        'title' => $sName
                    ];
                    if ($arField->isRequired()) {
                        $arResult['FIELDS'][$sName]['required'] = true;
                    }
                    if ($arField instanceof Entity\IntegerField) {
                        $arResult['FIELDS'][$sName]['type'] = ['integer', 'file.orm'];
                    } elseif ($arField instanceof Entity\FloatField) {
                        $arResult['FIELDS'][$sName]['type'] = ['float'];
                    } elseif ($arField instanceof Entity\StringField) {
                        $arResult['FIELDS'][$sName]['type'] = ['string', 'integer'];
                    } elseif ($arField instanceof Entity\TextField) {
                        $arResult['FIELDS'][$sName]['type'] = ['string'];
                    } elseif ($arField instanceof Entity\DateField || $arField instanceof Entity\DatetimeField) {
                        $arResult['FIELDS'][$sName]['type'] = ['datetime'];
                    } elseif ($arField instanceof Entity\BooleanField || $arField instanceof Entity\EnumField) {
                        $arResult['FIELDS'][$sName]['type'] = ['enum'];
                    }
                }
            } else {
                if (!array_key_exists('autocomplete', $arField) && is_array($arField) && !array_key_exists('reference',
                        $arField)) {
                    $arResult['FIELDS'][$key] = [
                        'title' => $key
                    ];
                    if (array_key_exists('required', $arField) && $arField['required'] == true) {
                        $arResult['FIELDS'][$key]['required'] = true;
                    }
                    switch ($arField['data_type']) {
                        case 'string':
                            $arResult['FIELDS'][$key]['type'] = ['string', 'integer'];
                            break;
                        case 'text':
                            $arResult['FIELDS'][$key]['type'] = ['string'];
                            break;
                        case 'integer':
                            $arResult['FIELDS'][$key]['type'] = ['integer', 'file.orm'];
                            break;
                        case 'date':
                            $arResult['FIELDS'][$key]['type'] = ['datetime'];
                            break;
                        case 'datetime':
                            $arResult['FIELDS'][$key]['type'] = ['datetime'];
                            break;
                        case 'boolean':
                            $arResult['FIELDS'][$key]['type'] = ['enum'];
                            break;
                        default:
                            $arResult['FIELDS'][$key]['type'] = [$arField['data_type']];
                            break;
                    }
                }
            }
        }

        return $arResult;
    }

    /**
     * @inheritdoc
     * @return array
     */
    public function genUnit()
    {
        $arResult = [];
        $arLoadFields = [];
        $arFieldsProfile = $this->profile['FIELDS'];

        foreach ($arFieldsProfile['FIELDS'] as $arProfile) {
            if ($arProfile['DATA_ID'] == 'datetime.unit') {
                $arLoadFields[$arProfile['FIELD_CODE']] = $this->saveDataModificationDate($arProfile['OBJECT']->getValue());
            } else {
                $arLoadFields[$arProfile['FIELD_CODE']] = $arProfile['OBJECT']->getValue();
            }
        }

        $sOrmNamespace = $this->sOrmNamespace;

        if (!class_exists($sOrmNamespace)) {
            $arResult['ERROR'] = Loc::getMessage('YLAB_DDATA_ORM_ENTITY_ERROR_EXIST_CLASS');
            return $arResult;
        }

        try {
            $oResult = $sOrmNamespace::add($arLoadFields);
        } catch (\Exception $e) {
            $sError = $e->getMessage();
        }

        if (empty($sError)) {
            if ($oResult->isSuccess()) {
                $arResult['NEW_ELEMENT_ID'] = $oResult->getId();
            } else {
                $arResult['ERROR'] = $oResult->getErrorMessages();
            }
        } else {
            $arResult['ERROR'] = $sError;
        }

        return $arResult;
    }

    /**
     * Вспомогательный метод для записи даты
     *
     * @param $value
     *
     * @return DateTime
     * @throws \Exception
     */
    public function saveDataModificationDate($value)
    {
        if ($value instanceof DateTime) {
            return $value;
        }

        return DateTime::createFromPhp(new \DateTime($value));
    }

    /**
     * Удаление сгенерированных данных
     *
     * @return mixed
     * @throws \Bitrix\Main\ArgumentException
     * @throws \Bitrix\Main\Db\SqlQueryException
     * @throws \Bitrix\Main\ObjectPropertyException
     * @throws \Bitrix\Main\SystemException
     * @throws \Exception
     */
    public function deleteGenData()
    {
        $arGenData = $this->getGenData();
        $connection = Application::getConnection();
        $connection->startTransaction();

        $sOrmNamespace = $this->sOrmNamespace;
        foreach ($arGenData as $arGenDatum) {
            $oResult = $sOrmNamespace::delete($arGenDatum['GEN_ELEMENT_ID']);
            if (!$oResult->isSuccess()) {
                $connection->rollbackTransaction();
                throw new \Exception(Loc::getMessage('YLAB_DDATA_DELETE_DATA_OPTION_ERR_DELETE',
                    ['#ELEMENT_ID#' => $arGenDatum['GEN_ELEMENT_ID']]));
            }
            $oResult = DataUnitGenElementsTable::delete($arGenDatum['ID']);
            if (!$oResult->isSuccess()) {
                $connection->rollbackTransaction();
                throw new \Exception(Loc::getMessage('YLAB_DDATA_DELETE_DATA_OPTION_ERR_DELETE',
                    ['#ELEMENT_ID#' => $arGenDatum['ID']]));
            }
        }

        $connection->commitTransaction();
    }
}