<?php

namespace Ylab\Ddata\Entity;

use Bitrix\Main\HttpRequest;
use Bitrix\Main\Loader;
use Bitrix\Main\Localization\Loc;
use Ylab\Ddata\Interfaces\EntityUnitClass;
use Ylab\Ddata\Helpers;

Loc::loadMessages(__FILE__);

/**
 * Class IblockElement
 * @package Ylab\Ddata\entity
 */
class IblockElement extends EntityUnitClass
{
    /**
     * @var null id инфоблока
     */
    public $iblock_id = null;

    /**
     * @var null Тип инфоблока
     */
    public $iblock_type = null;

    /**
     * Метод возврящает описывающий массив
     *
     * @return array
     */
    public static function getDescription()
    {
        return [
            "ID" => "iblock-element",
            "NAME" => Loc::getMessage('YLAB_DDATA_IBELEM_ENTITY_NAME'),
            "DESCRIPTION" => Loc::getMessage('YLAB_DDATA_IBELEM_ENTITY_DESCRIPTION'),
            "TYPE" => "iblock",
            "CLASS" => __CLASS__
        ];
    }

    /**
     * IblockElement constructor.
     * @param $iProfileID
     * @throws \Bitrix\Main\ArgumentException
     * @throws \Bitrix\Main\SystemException
     */
    public function __construct($iProfileID)
    {
        parent::__construct($iProfileID);

        if (!empty($this->profile['OPTIONS']['iblock_id'])) {
            $this->iblock_id = $this->profile['OPTIONS']['iblock_id'];
        }

        if (!empty($this->profile['OPTIONS']['iblock_type'])) {
            $this->iblock_type = $this->profile['OPTIONS']['iblock_type'];
        }

        if (!empty($this->profile['FIELDS'])) {
            $arTmp = [];
            $arFields = self::getFields();

            foreach ($this->profile['FIELDS'] as $arField) {
                if (isset($arFields['FIELDS'][$arField['FIELD_CODE']])) {
                    $arTmp['FIELDS'][$arField['FIELD_CODE']] = $arField;
                } else if (isset($arFields['PROPERTIES'][$arField['FIELD_CODE']])) {
                    $arTmp['PROPERTIES'][$arField['FIELD_CODE']] = $arField;
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
        Loader::includeModule('iblock');

        $arIblockType = [];
        $arPrepareRequest = $oRequest->get('prepare');
        $obgIblockType = \CIBlockType::GetList();
        while ($IblockType = $obgIblockType->Fetch()) {
            $IblockType = \CIBlockType::GetByIDLang($IblockType["ID"], LANG);
            $arIblockType[$IblockType["ID"]] = $IblockType['NAME'] . " [{$IblockType["ID"]}]";
        }

        $arIblock = [];
        if (!empty($arPrepareRequest['iblock_type']) && strlen($arPrepareRequest['iblock_type']) > 0) {
            $obgIblock = \CIBlock::GetList([], ['TYPE' => htmlspecialcharsbx($arPrepareRequest['iblock_type'])]);
            while ($Iblock = $obgIblock->Fetch()) {
                $arIblock[$Iblock['ID']] = $Iblock['NAME'] . " [{$Iblock['CODE']}]";
            }
        }

        ob_start();
        include Helpers::getModulePath() . "/admin/fragments/iblock_element_prepare_form.php";
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
        $flag = false;
        if (!empty($arPrepareRequest['iblock_type'])) {
            $flag = true;
        } else {
            return false;
        }

        if (!empty($arPrepareRequest['iblock_id'])) {
            $flag = true;
        } else {
            return false;
        }

        return $flag;
    }

    /**
     * @inheritdoc
     * @return array
     */
    public function getFields(HttpRequest $oRequest = null)
    {
        Loader::includeModule('iblock');

        if ($oRequest) {
            $arPrepareRequest = $oRequest->get('prepare');
        }

        if (!empty($this->iblock_id)) {
            $iIblockId = $this->iblock_id;
        } else {
            if (!empty($arPrepareRequest['iblock_id'])) {
                $iIblockId = htmlspecialcharsbx($arPrepareRequest['iblock_id']);
            }
        }

        if (!isset($iIblockId)) {
            return [];
        }

        $arIblockProperties = [];
        $arIblockFields = \CIBlock::GetFields($iIblockId);
        $oIblockProperties = \CIBlock::GetProperties($iIblockId);
        while ($arProperty = $oIblockProperties->Fetch()) {
            $arIblockProperties[] = $arProperty;
        }

        $arFields = [
            'FIELDS' => [
                'SORT' => [
                    'type' => ['integer'],
                    'title' => Loc::getMessage('YLAB_DDATA_IBELEM_FIELD_SORT'),
                ],
                'NAME' => [
                    'type' => ['string', 'integer'],
                    'title' => Loc::getMessage('YLAB_DDATA_IBELEM_FIELD_NAME'),
                ],
                'TIMESTAMP_X' => [
                    'type' => ['datetime'],
                    'title' => Loc::getMessage('YLAB_DDATA_IBELEM_FIELD_TIMESTAMP_X'),
                ],
                'MODIFIED_BY' => [
                    'type' => ['user'],
                    'title' => Loc::getMessage('YLAB_DDATA_IBELEM_FIELD_MODIFIED_BY')
                ],
                'DATE_CREATE' => [
                    'type' => ['datetime'],
                    'title' => Loc::getMessage('YLAB_DDATA_IBELEM_FIELD_DATE_CREATE'),
                ],
                'CREATED_BY' => [
                    'type' => ['user'],
                    'title' => Loc::getMessage('YLAB_DDATA_IBELEM_FIELD_CREATED_BY')
                ],
                'IBLOCK_SECTION_ID' => [
                    'type' => ['iblock.section'],
                    'title' => Loc::getMessage('YLAB_DDATA_IBELEM_FIELD_IBLOCK_SECTION_ID')
                ],
                'ACTIVE' => [
                    'type' => ['checkbox'],
                    'title' => Loc::getMessage('YLAB_DDATA_IBELEM_FIELD_ACTIVE')
                ],
                'ACTIVE_FROM' => [
                    'type' => ['datetime'],
                    'title' => Loc::getMessage('YLAB_DDATA_IBELEM_FIELD_ACTIVE_FROM'),
                ],
                'ACTIVE_TO' => [
                    'type' => ['datetime'],
                    'title' => Loc::getMessage('YLAB_DDATA_IBELEM_FIELD_ACTIVE_TO'),
                ],
                'PREVIEW_PICTURE' => [
                    'type' => ['file'],
                    'title' => Loc::getMessage('YLAB_DDATA_IBELEM_FIELD_PREVIEW_PICTURE')
                ],
                'PREVIEW_TEXT' => [
                    'type' => ['string', 'integer'],
                    'title' => Loc::getMessage('YLAB_DDATA_IBELEM_FIELD_PREVIEW_TEXT')
                ],
                'DETAIL_PICTURE' => [
                    'type' => ['file'],
                    'title' => Loc::getMessage('YLAB_DDATA_IBELEM_FIELD_DETAIL_PICTURE')
                ],
                'DETAIL_TEXT' => [
                    'type' => ['string', 'integer'],
                    'title' => Loc::getMessage('YLAB_DDATA_IBELEM_FIELD_DETAIL_TEXT')
                ],
                'XML_ID' => [
                    'type' => ['string', 'integer'],
                    'title' => Loc::getMessage('YLAB_DDATA_IBELEM_FIELD_XML_ID'),
                ],
                'CODE' => [
                    'type' => ['string', 'integer'],
                    'title' => Loc::getMessage('YLAB_DDATA_IBELEM_FIELD_CODE')
                ],
            ],
        ];

        foreach ($arFields['FIELDS'] as $code => &$field) {
            $field['required'] = false;
            if (isset($arIblockFields[$code]) && $arIblockFields[$code]['IS_REQUIRED'] == 'Y') {
                $field['required'] = true;
            }
        }

        foreach ($arIblockProperties as $arProperty) {
            $arFields['PROPERTIES'][$arProperty['CODE']] = [];
            $arItem = &$arFields['PROPERTIES'][$arProperty['CODE']];
            $arItem['title'] = $arProperty['NAME'];
            $arItem['required'] = ($arProperty['IS_REQUIRED'] == 'Y');
            if ($arProperty['USER_TYPE']) {
                switch ($arProperty['USER_TYPE']) {
                    case 'UserID':
                        $arItem['type'] = ["user"];
                        break;
                    case 'DateTime':
                        $arItem['type'] = ["datetime"];
                        break;
                    case 'Date':
                        $arItem['type'] = ["datetime"];
                        break;
                    case 'SectionAuto':
                        $arItem['type'] = ["iblock.section"];
                        break;
                    case 'HTML':
                        $arItem['type'] = ["string"];
                        break;
                    case 'directory':
                        $arItem['type'] = ["dictionary"];
                        break;
                    default:
                        $arItem['type'] = [];
                        break;
                }
            } else {
                switch ($arProperty['PROPERTY_TYPE']) {
                    case 'S':
                        $arItem['type'] = ["string", "integer"];
                        break;
                    case 'N':
                        $arItem['type'] = ["integer"];
                        break;
                    case 'F':
                        $arItem['type'] = ["file"];
                        break;
                    case 'E':
                        $arItem['type'] = ["iblock.element"];
                        break;
                    case 'G':
                        $arItem['type'] = ["iblock.section"];
                        break;
                    case 'L':
                        $arItem['type'] = ["iblock.list"];
                        break;
                    default:
                        $arItem['type'] = [];
                        break;
                }
            }
        }

        return $arFields;
    }

    /**
     * @inheritdoc
     * @return mixed
     */
    public function genUnit()
    {
        Loader::includeModule('iblock');

        $oElement = new \CIBlockElement;
        $arFieldsProfile = $this->profile['FIELDS'];
        $arLoadFields = [];
        $arLoadProperties = [];
        $arResult = [];

        foreach ($arFieldsProfile['FIELDS'] as $arProfile) {
            $arLoadFields[$arProfile['FIELD_CODE']] = $arProfile['OBJECT']->getValue();
        }
        $arLoadFields['IBLOCK_ID'] = $this->iblock_id;

        foreach ($arFieldsProfile['PROPERTIES'] as $arProfile) {
            $arLoadProperties[$arProfile['FIELD_CODE']] = $arProfile['OBJECT']->getValue();
        }

        if (!empty($arLoadProperties)) {
            $arLoadFields['PROPERTY_VALUES'] = $arLoadProperties;
        }

        $iNewElementID = $oElement->Add($arLoadFields);
        if ($iNewElementID > 0) {
            $arResult['NEW_ELEMENT_ID'] = $iNewElementID;
        } else {
            $arResult['ERROR'] = $oElement->LAST_ERROR;
        }

        return $arResult;
    }
}