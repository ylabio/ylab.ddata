<?php

namespace Ylab\Ddata\Entity;

use Bitrix\Main\Entity;
use Bitrix\Main\HttpRequest;
use Bitrix\Main\Localization\Loc;
use Bitrix\Main\Type\DateTime;
use Ylab\Ddata\Interfaces\EntityUnitClass;
use Ylab\Ddata\Helpers;

Loc::loadMessages(__FILE__);

/**
 * Class Orm
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
    public static function getDescription()
    {
        return [
            "ID" => "orm",
            "NAME" => Loc::getMessage('YLAB_DDATA_ORM_ENTITY_NAME'),
            "DESCRIPTION" => Loc::getMessage('YLAB_DDATA_ORM_ENTITY_DESCRIPTION'),
            "TYPE" => "orm",
            "CLASS" => __CLASS__
        ];
    }

    /**
     * Orm constructor.
     * @param $iProfileID
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
            $arFields = self::getFields();

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
     * @param HttpRequest $oRequest
     * @return string
     * @throws \Bitrix\Main\LoaderException
     */
    public static function getPrepareForm(HttpRequest $oRequest)
    {
        $arPrepareRequest = $oRequest->get('prepare');

        ob_start();
        include Helpers::getModulePath() . "/admin/fragments/orm_prepare_form.php";
        $tpl = ob_get_contents();
        ob_end_clean();
        return $tpl;
    }

    /**
     * @inheritdoc
     * @param HttpRequest $oRequest
     * @return boolean
     */
    public static function isValidPrepareForm(HttpRequest $oRequest)
    {
        $arPrepareRequest = $oRequest->get('prepare');

        if (!empty($arPrepareRequest['namespace']) && class_exists($arPrepareRequest['namespace'], true)) {
            return true;
        }

        return false;
    }

    /**
     * @inheritdoc
     * @param HttpRequest|null $oRequest
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

        if (!isset($sOrmNamespace)) {
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
                    if ($arField['data_type'] == 'string') {
                        $arResult['FIELDS'][$key]['type'] = ['string', 'integer'];
                    } elseif ($arField['data_type'] == 'text') {
                        $arResult['FIELDS'][$key]['type'] = ['string'];
                    } elseif ($arField['data_type'] == 'integer') {
                        $arResult['FIELDS'][$key]['type'] = ['integer', 'file.orm'];
                    } elseif ($arField['data_type'] == 'date' || $arField['data_type'] == 'datetime') {
                        $arResult['FIELDS'][$key]['type'] = ['datetime'];
                    } elseif ($arField['data_type'] == 'boolean') {
                        $arResult['FIELDS'][$key]['type'] = ['enum'];
                    } else {
                        $arResult['FIELDS'][$key]['type'] = [$arField['data_type']];
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
        $oResult = $sOrmNamespace::add($arLoadFields);
        if ($oResult->isSuccess()) {
            $arResult['NEW_ELEMENT_ID'] = $oResult->getId();
        } else {
            $arResult['ERROR'] = $oResult->getErrorMessages();
        }

        return $arResult;
    }

    /**
     * Вспомогательный метод для записи даты
     * @param $value
     * @return DateTime
     */
    public function saveDataModificationDate($value)
    {
        if ($value instanceof DateTime) {
            return $value;
        } else {
            return DateTime::createFromPhp(new \DateTime($value));
        }
    }
}