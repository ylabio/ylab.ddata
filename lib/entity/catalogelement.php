<?php

namespace Ylab\Ddata\Entity;

use Bitrix\Catalog\ProductTable;
use Bitrix\Main\HttpRequest;
use Bitrix\Main\Loader;
use Bitrix\Main\Localization\Loc;
use CCatalogGroup;
use CCatalogSku;
use Ylab\Ddata\Interfaces\EntityUnitClass;
use Ylab\Ddata\Helpers;

Loc::loadMessages(__FILE__);

/**
 * Class CatalogElement
 * @package Ylab\Ddata\Entity
 */
class CatalogElement extends EntityUnitClass
{
    /**
     * @var null id каталога
     */
    public $iCatalogIBlockID = null;

    /**
     * @var null Тип товара
     */
    public $iProductType = null;

    /**
     * @var array Типы цен
     */
    public $arGroupsPrice = [];

    /**
     * @var array Типы товаров
     */
    public $arCatalogTypes = [];

    /**
     * @var array Массив с информацией о предложениях товарного каталога
     */
    public $arOfferInfo = [];

    /**
     * Метод возврящает описывающий массив
     *
     * @return array
     */
    public static function getDescription()
    {
        return [
            "ID" => "catalog-element",
            "NAME" => Loc::getMessage('YLAB_DDATA_CAT_ELEM_ENTITY_NAME'),
            "DESCRIPTION" => Loc::getMessage('YLAB_DDATA_CAT_ELEM_ENTITY_DESCRIPTION'),
            "TYPE" => "catalog",
            "CLASS" => __CLASS__
        ];
    }

    /**
     * CatalogElement constructor.
     * @param $iProfileID
     * @throws \Bitrix\Main\ArgumentException
     * @throws \Bitrix\Main\SystemException
     * @throws \Bitrix\Main\LoaderException
     */
    public function __construct($iProfileID)
    {
        parent::__construct($iProfileID);

        Loader::includeModule('catalog');

        if (!empty($this->profile['OPTIONS']['iblock_id'])) {
            $this->iCatalogIBlockID = $this->profile['OPTIONS']['iblock_id'];
        }

        if (!empty($this->profile['OPTIONS']['catalog_type'])) {
            $this->iProductType = $this->profile['OPTIONS']['catalog_type'];
        }

        $this->arGroupsPrice = $this->getGroupsPrice();
        $this->arCatalogTypes['TYPE_PRODUCT'] = ProductTable::TYPE_PRODUCT;
        $this->arCatalogTypes['TYPE_SET'] = ProductTable::TYPE_SET;
        $this->arCatalogTypes['TYPE_SKU'] = ProductTable::TYPE_SKU;
        $this->arCatalogTypes['TYPE_OFFER'] = ProductTable::TYPE_OFFER;
        $this->arCatalogTypes['TYPE_FREE_OFFER'] = ProductTable::TYPE_FREE_OFFER;

        if ($this->iProductType == $this->arCatalogTypes['TYPE_SKU']) {
            $arOfferInfo = CCatalogSKU::GetInfoByProductIBlock($this->iCatalogIBlockID);
            $this->arOfferInfo = $arOfferInfo;
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
     * Метод фозвращает html строку с полями предварительной настройки сущности
     *
     * @param HttpRequest $oRequest
     * @return string
     * @throws \Bitrix\Main\LoaderException
     */
    public static function getPrepareForm(HttpRequest $oRequest)
    {
        Loader::includeModule('iblock');
        Loader::includeModule('catalog');

        $arCatalog = [];
        $arPrepareRequest = $oRequest->get('prepare');
        if (Loader::includeModule('catalog') && Loader::includeModule('iblock')) {
            $oCatalog = \CCatalog::GetList([], ['SKU_PROPERTY_ID' => false], false, false,
                ['IBLOCK_ID', 'NAME', 'SKU_PROPERTY_ID']);
            while ($arCatalogResult = $oCatalog->GetNext()) {
                $arCatalog[$arCatalogResult['IBLOCK_ID']] = $arCatalogResult['NAME'];
            }
            $arCatalogTypes = ProductTable::getProductTypes(true);
            $iProductTypeSKU = ProductTable::TYPE_SKU;
            $iProductTypeSimple = ProductTable::TYPE_PRODUCT;
        }

        ob_start();
        include Helpers::getModulePath() . "/admin/fragments/catalog_element_prepare_form.php";
        $tpl = ob_get_contents();
        ob_end_clean();

        return $tpl;
    }

    /**
     * Метод проверяет на валидность данные  предварительной настройки сущности
     *
     * @param HttpRequest $oRequest
     * @return boolean
     * @throws \Bitrix\Main\LoaderException
     */
    public static function isValidPrepareForm(HttpRequest $oRequest)
    {
        $arPrepareRequest = $oRequest->get('prepare');
        $flag = false;

        if (Loader::includeModule('catalog')) {
            $flag = true;
        } else {
            return false;
        }

        if (!empty($arPrepareRequest['iblock_id'])) {
            $flag = true;
        } else {
            return false;
        }

        if ($arPrepareRequest['catalog_type'] != null) {
            Loader::includeModule('catalog');

            $flag = true;
            if ($arPrepareRequest['catalog_type'] == ProductTable::TYPE_SKU) {
                if (intval($arPrepareRequest['min_offers']) > 0 && intval($arPrepareRequest['max_offers']) > 0 && (intval($arPrepareRequest['max_offers']) > intval($arPrepareRequest['min_offers']))) {
                    $flag = true;
                } else {
                    return false;
                }
            }
        } else {
            return false;
        }

        return $flag;
    }

    /**
     * Метод возвращает массив полей и свойств сущности
     *
     * @param HttpRequest $oRequest
     * @return array
     * @throws \Bitrix\Main\LoaderException
     */
    public function getFields(HttpRequest $oRequest = null)
    {
        Loader::includeModule('iblock');
        Loader::includeModule('catalog');

        if ($oRequest) {
            $arPrepareRequest = $oRequest->get('prepare');
        }

        if (!empty($this->iCatalogIBlockID)) {
            $iCatalogID = $this->iCatalogIBlockID;
        } else {
            if (!empty($arPrepareRequest['iblock_id'])) {
                $iCatalogID = htmlspecialcharsbx($arPrepareRequest['iblock_id']);
            }
        }

        if (!empty($this->iProductType)) {
            $iProductType = $this->iProductType;
        } else {
            if (intval($arPrepareRequest['catalog_type']) >= 0) {
                $iProductType = htmlspecialcharsbx($arPrepareRequest['catalog_type']);
            }
        }

        if ($iProductType == $this->arCatalogTypes['TYPE_SKU']) {
            $arOfferInfo = CCatalogSKU::GetInfoByProductIBlock($iCatalogID);
        }
        if (!isset($iCatalogID)) {
            return [];
        }

        if (!isset($iProductType)) {
            return [];
        }

        $arIblockProperties = [];
        $arIblockFields = \CIBlock::GetFields($iCatalogID);
        $oIblockProperties = \CIBlock::GetProperties($iCatalogID);
        while ($arProperty = $oIblockProperties->Fetch()) {
            $arIblockProperties[] = $arProperty;
        }

        $arFields = [
            'FIELDS' => [
                'SORT' => [
                    'type' => ['integer'],
                    'title' => Loc::getMessage('YLAB_DDATA_CAT_ELEM_FIELD_SORT'),
                ],
                'NAME' => [
                    'type' => ['string', 'integer'],
                    'title' => Loc::getMessage('YLAB_DDATA_CAT_ELEM_FIELD_NAME'),
                ],
                'TIMESTAMP_X' => [
                    'type' => ['datetime'],
                    'title' => Loc::getMessage('YLAB_DDATA_CAT_ELEM_FIELD_TIMESTAMP_X'),
                ],
                'MODIFIED_BY' => [
                    'type' => ['user'],
                    'title' => Loc::getMessage('YLAB_DDATA_CAT_ELEM_FIELD_MODIFIED_BY')
                ],
                'DATE_CREATE' => [
                    'type' => ['datetime'],
                    'title' => Loc::getMessage('YLAB_DDATA_CAT_ELEM_FIELD_DATE_CREATE'),
                ],
                'CREATED_BY' => [
                    'type' => ['user'],
                    'title' => Loc::getMessage('YLAB_DDATA_CAT_ELEM_FIELD_CREATED_BY')
                ],
                'IBLOCK_SECTION' => [
                    'type' => ['iblock.section'],
                    'title' => Loc::getMessage('YLAB_DDATA_CAT_ELEM_FIELD_IBLOCK_SECTION_ID')
                ],
                'ACTIVE' => [
                    'type' => ['checkbox'],
                    'title' => Loc::getMessage('YLAB_DDATA_CAT_ELEM_FIELD_ACTIVE')
                ],
                'ACTIVE_FROM' => [
                    'type' => ['datetime'],
                    'title' => Loc::getMessage('YLAB_DDATA_CAT_ELEM_FIELD_ACTIVE_FROM'),
                ],
                'ACTIVE_TO' => [
                    'type' => ['datetime'],
                    'title' => Loc::getMessage('YLAB_DDATA_CAT_ELEM_FIELD_ACTIVE_TO'),
                ],
                'PREVIEW_PICTURE' => [
                    'type' => ['file'],
                    'title' => Loc::getMessage('YLAB_DDATA_CAT_ELEM_FIELD_PREVIEW_PICTURE')
                ],
                'PREVIEW_TEXT' => [
                    'type' => ['string', 'integer'],
                    'title' => Loc::getMessage('YLAB_DDATA_CAT_ELEM_FIELD_PREVIEW_TEXT')
                ],
                'DETAIL_PICTURE' => [
                    'type' => ['file'],
                    'title' => Loc::getMessage('YLAB_DDATA_CAT_ELEM_FIELD_DETAIL_PICTURE')
                ],
                'DETAIL_TEXT' => [
                    'type' => ['string', 'integer'],
                    'title' => Loc::getMessage('YLAB_DDATA_CAT_ELEM_FIELD_DETAIL_TEXT')
                ],
                'XML_ID' => [
                    'type' => ['string', 'integer'],
                    'title' => Loc::getMessage('YLAB_DDATA_CAT_ELEM_FIELD_XML_ID'),
                ],
                'CODE' => [
                    'type' => ['string', 'integer'],
                    'title' => Loc::getMessage('YLAB_DDATA_CAT_ELEM_FIELD_CODE')
                ],
            ],
        ];

        $arFields['GROUPS_NAME'] = [
            'property' => Loc::getMessage("YLAB_DDATA_GROUP_IBLOCK_PROPERTIES"),
            'catalog-property' => Loc::getMessage("YLAB_DDATA_GROUP_CATALOG_PROPERTIES"),
            'catalog-property-offer' => Loc::getMessage("YLAB_DDATA_GROUP_OFFERS_PROPERTIES")
        ];

        foreach ($arFields['FIELDS'] as $sCode => &$sField) {
            $sField['required'] = false;
            if (isset($arIblockFields[$sCode]) && $arIblockFields[$sCode]['IS_REQUIRED'] == 'Y') {
                $sField['required'] = true;
            }
        }

        foreach ($arIblockProperties as $arProperty) {
            $arFields['PROPERTIES'][$arProperty['CODE']] = [];
            $arItem = &$arFields['PROPERTIES'][$arProperty['CODE']];
            $arItem['title'] = $arProperty['NAME'];
            $arItem['required'] = ($arProperty['IS_REQUIRED'] == 'Y');
            $arItem['multiple'] = ($arProperty['MULTIPLE'] == 'Y');
            $arItem['group-id'] = 'property';
            $arItem['type'] = $this->getGenType($arProperty);
        }
        $arCatalogTypes = $this->arCatalogTypes;
        switch ($iProductType) {
            case $arCatalogTypes['TYPE_PRODUCT']:
                $arBaseCatalogProperties = $this->getBaseCatalogProperties();
                foreach ($arBaseCatalogProperties as $sCode => $arValue) {
                    $arFields['PROPERTIES'][$sCode] = $arValue;
                }
                break;
            case $arCatalogTypes['TYPE_SET']:
                $arBaseCatalogProperties = $this->getBaseCatalogProperties();
                foreach ($arBaseCatalogProperties as $sCode => $arValue) {
                    $arFields['PROPERTIES'][$sCode] = $arValue;
                }
                break;
            case $arCatalogTypes['TYPE_SKU']:
                $arBaseCatalogProperties = $this->getBaseCatalogProperties();
                foreach ($arBaseCatalogProperties as $sCode => $arValue) {
                    $arFields['PROPERTIES'][$sCode] = $arValue;
                }
                if (!empty($arOfferInfo)) {
                    $iOfferIBlockID = $arOfferInfo['IBLOCK_ID'];
                    $iOfferSkuPropertyID = $arOfferInfo['SKU_PROPERTY_ID'];
                    $oIblockOfferProperties = \CIBlock::GetProperties($iOfferIBlockID);
                    while ($arProperty = $oIblockOfferProperties->Fetch()) {
                        $arIblockOfferProperties[] = $arProperty;
                    }
                    foreach ($arIblockOfferProperties as $arProperty) {
                        if ($arProperty['ID'] != $iOfferSkuPropertyID) {
                            $arFields['PROPERTIES']['YLAB_DDATA_OFFER_' . $arProperty['CODE']] = [];
                            $arItem = &$arFields['PROPERTIES']['YLAB_DDATA_OFFER_' . $arProperty['CODE']];
                            $arItem['title'] = $arProperty['NAME'];
                            $arItem['required'] = ($arProperty['IS_REQUIRED'] == 'Y');
                            $arItem['multiple'] = ($arProperty['MULTIPLE'] == 'Y');
                            $arItem['group-id'] = 'catalog-property-offer';
                            $arItem['type'] = $this->getGenType($arProperty);
                        }
                    }
                }
                break;
        }

        return $arFields;
    }

    /**
     * Записывает в базу 1 экземляр сгенерированной сущности
     *
     * @return mixed
     * @throws \Bitrix\Main\LoaderException
     */
    public function genUnit()
    {
        Loader::includeModule('iblock');
        Loader::includeModule('catalog');

        $oElement = new \CIBlockElement;
        $arFieldsProfile = $this->profile['FIELDS'];
        $arLoadFields = [];
        $arLoadProperties = [];
        $arResult = [];
        $arCatalogTypes = $this->arCatalogTypes;

        foreach ($arFieldsProfile['FIELDS'] as $arProfile) {
            $arLoadFields[$arProfile['FIELD_CODE']] = $arProfile['OBJECT']->getValue();
        }
        $arLoadFields['IBLOCK_ID'] = $this->iCatalogIBlockID;
        $iCountOffers = mt_rand($this->profile['OPTIONS']['min_offers'], $this->profile['OPTIONS']['max_offers']);

        if (isset($arFieldsProfile['PROPERTIES'])) {
            foreach ($arFieldsProfile['PROPERTIES'] as $arProfile) {
                if (strpos($arProfile['FIELD_CODE'], 'YLAB_DDATA_CATALOG_') !== false) {
                    $arProfile['FIELD_CODE'] = substr($arProfile['FIELD_CODE'], strlen('YLAB_DDATA_CATALOG_'));
                    if ($this->iProductType == $arCatalogTypes['TYPE_SKU']) {
                        for ($iCount = 1; $iCount <= $iCountOffers; $iCount++) {
                            $arLoadCatalogProperties[$arProfile['FIELD_CODE']][$iCount] = $arProfile['OBJECT']->getValue();
                        }
                    } else {
                        $arLoadCatalog[$arProfile['FIELD_CODE']] = $arProfile['OBJECT']->getValue();
                    }
                } elseif (strpos($arProfile['FIELD_CODE'], 'YLAB_DDATA_OFFER_') !== false) {
                    $arProfile['FIELD_CODE'] = substr($arProfile['FIELD_CODE'], strlen('YLAB_DDATA_OFFER_'));
                    for ($iCount = 1; $iCount <= $iCountOffers; $iCount++) {
                        if ($arProfile['MULTIPLE'] == 'Y') {
                            $arGeneratorDescription = $arProfile['OBJECT']->getDescription();
                            if ($arGeneratorDescription['TYPE'] == 'file') {
                                for ($iCountMulti = 1; $iCountMulti <= $arProfile['COUNT']; $iCountMulti++) {
                                    $arLoadOfferProperties[$arProfile['FIELD_CODE']][$iCount]['n' . $iCountMulti] = $arProfile['OBJECT']->getValue();
                                }
                            } else {
                                for ($iCountMulti = 1; $iCountMulti <= $arProfile['COUNT']; $iCountMulti++) {
                                    $arLoadOfferProperties[$arProfile['FIELD_CODE']][$iCount][] = $arProfile['OBJECT']->getValue();
                                }
                            }
                        } else {
                            $arLoadOfferProperties[$arProfile['FIELD_CODE']][$iCount] = $arProfile['OBJECT']->getValue();
                        }
                    }
                } else {
                    if ($arProfile['MULTIPLE'] == 'Y') {
                        $arGeneratorDescription = $arProfile['OBJECT']->getDescription();
                        if (array_key_exists('TYPE', $arGeneratorDescription)) {
                            if ($arGeneratorDescription['TYPE'] == 'file') {
                                for ($iCountMulti = 1; $iCountMulti <= $arProfile['COUNT']; $iCountMulti++) {
                                    $arLoadProperties[$arProfile['FIELD_CODE']]['n' . $iCountMulti] = $arProfile['OBJECT']->getValue();
                                }
                            } else {
                                for ($iCountMulti = 1; $iCountMulti <= $arProfile['COUNT']; $iCountMulti++) {
                                    $arLoadProperties[$arProfile['FIELD_CODE']][] = $arProfile['OBJECT']->getValue();
                                }
                            }
                        }
                    } else {
                        $arLoadProperties[$arProfile['FIELD_CODE']] = $arProfile['OBJECT']->getValue();
                    }
                }
            }
        }

        if (!empty($arLoadProperties)) {
            $arLoadFields['PROPERTY_VALUES'] = $arLoadProperties;
        }
        $arOfferInfo = $this->arOfferInfo;
        if (!empty($arOfferInfo)) {
            $arLoadOffers = [];
            $iOfferIBlockID = $arOfferInfo['IBLOCK_ID'];
            $iOfferSkuPropertyID = $arOfferInfo['SKU_PROPERTY_ID'];
            $arLoadOffers['ACTIVE'] = $arLoadFields['ACTIVE'];
            $arLoadOffers['IBLOCK_ID'] = $iOfferIBlockID;
        }
        $iNewElementID = $oElement->Add($arLoadFields);
        if ($iNewElementID > 0) {
            $arResult['NEW_ELEMENT_ID'] = $iNewElementID;
            $arLoadCatalog['ID'] = $iNewElementID;
            $arLoadCatalog['TYPE'] = $this->iProductType;
            $arLoadCatalog['AVAILABLE'] = 'Y';
            $bProductAdd = \Bitrix\Catalog\Model\Product::add($arLoadCatalog);
            if ($bProductAdd) {
                $arGroupsPrice = $this->arGroupsPrice;
                foreach ($arGroupsPrice as $sPriceCode => $arPriceValue) {
                    if (array_key_exists($sPriceCode . '_PRICE',
                            $arLoadCatalog) && array_key_exists($sPriceCode . '_CURRENCY',
                            $arLoadCatalog) && $this->iProductType != $arCatalogTypes['TYPE_SKU']) {
                        $bPriceAdd = \Bitrix\Catalog\Model\Price::add([
                            'PRODUCT_ID' => $iNewElementID,
                            'PRICE' => $arLoadCatalog[$sPriceCode . '_PRICE'],
                            'CURRENCY' => $arLoadCatalog[$sPriceCode . '_CURRENCY'],
                            'CATALOG_GROUP_ID' => $arPriceValue['ID']
                        ]);
                        if (!$bPriceAdd->isSuccess()) {
                            $arResult['ERROR'] = implode(", ", $bPriceAdd->getErrorMessages());
                        }
                    }
                }
                if (!empty($arLoadOffers)) {
                    $arLoadOffers['PROPERTY_VALUES'][$iOfferSkuPropertyID] = $iNewElementID;
                    for ($iCount = 1; $iCount <= $iCountOffers; $iCount++) {
                        if (isset($arLoadOfferProperties)) {
                            foreach ($arLoadOfferProperties as $sPropertyCode => $arPropertyValues) {
                                $arLoadOffers['PROPERTY_VALUES'][$sPropertyCode] = $arPropertyValues[$iCount];
                            }
                        }
                        if (isset($arLoadCatalogProperties)) {
                            foreach ($arLoadCatalogProperties as $sPropertyCode => $arPropertyValues) {
                                $arLoadOffers[$sPropertyCode] = $arPropertyValues[$iCount];
                            }
                        }
                        $arLoadOffers['NAME'] = $arLoadFields['NAME'] . '-' . $iCount;
                        $arLoadOffers['TYPE'] = $arCatalogTypes['TYPE_OFFER'];
                        $arLoadOffers['CODE'] = $arLoadFields['CODE'] . '_' . $iCount;
                        $iNewOfferElementID = $oElement->Add($arLoadOffers);
                        $arLoadOffers['ID'] = $iNewOfferElementID;
                        $arLoadOffers['AVAILABLE'] = 'Y';
                        $bOfferAdd = \Bitrix\Catalog\Model\Product::add($arLoadOffers);
                        if ($bOfferAdd->isSuccess()) {
                            foreach ($arGroupsPrice as $sPriceCode => $arPriceValue) {
                                if (array_key_exists($sPriceCode . '_PRICE',
                                        $arLoadOffers) && array_key_exists($sPriceCode . '_CURRENCY', $arLoadOffers)) {
                                    $bOfferPriceAdd = \Bitrix\Catalog\Model\Price::add([
                                        'PRODUCT_ID' => $iNewOfferElementID,
                                        'PRICE' => $arLoadOffers[$sPriceCode . '_PRICE'],
                                        'CURRENCY' => $arLoadOffers[$sPriceCode . '_CURRENCY'],
                                        'CATALOG_GROUP_ID' => $arPriceValue['ID']
                                    ]);
                                    if (!$bOfferPriceAdd->isSuccess()) {
                                        $arResult['ERROR'] = implode(", ", $bOfferPriceAdd->getErrorMessages());
                                    }
                                }
                            }
                        } else {
                            $arResult['ERROR'] = implode(", ", $bOfferAdd->getErrorMessages());
                        }
                    }
                }
            } else {
                $arResult['ERROR'] = Loc::getMessage("YLAB_DDATA_CATALOG_ADD_ERROR");
            }
        } else {
            $arResult['ERROR'] = $oElement->LAST_ERROR;
        }

        return $arResult;
    }

    /**
     * Метод для получения типов Цены
     * @return array
     * @throws \Bitrix\Main\LoaderException
     */
    public function getGroupsPrice()
    {
        Loader::includeModule('catalog');

        $arResult = [];
        $oPriceGroups = CCatalogGroup::GetList([], [], false, false, ['ID', 'NAME', 'BASE', 'NAME_LANG']);
        while ($arPriceGroups = $oPriceGroups->GetNext()) {
            $arResult[$arPriceGroups['NAME']] = [
                'ID' => $arPriceGroups['ID'],
                'NAME' => $arPriceGroups['NAME_LANG'],
                'BASE' => $arPriceGroups['BASE']
            ];
        }

        return $arResult;
    }

    /**
     * Метод для получения основных свойств Торгового каталога
     * @return array
     */
    public function getBaseCatalogProperties()
    {
        $arResult = [];
        $arResult['YLAB_DDATA_CATALOG_VAT_INCLUDED'] = [
            'title' => Loc::getMessage("YLAB_DDATA_CATALOG_VAT_INCLUDED_TITLE"),
            'required' => false,
            'group-id' => 'catalog-property',
            'type' => ['checkbox']
        ];
        $arResult['YLAB_DDATA_CATALOG_QUANTITY_TRACE'] = [
            'title' => Loc::getMessage("YLAB_DDATA_CATALOG_QUANTITY_TRACE_TITLE"),
            'required' => false,
            'group-id' => 'catalog-property',
            'type' => ['checkbox']
        ];
        $arResult['YLAB_DDATA_CATALOG_QUANTITY'] = [
            'title' => Loc::getMessage("YLAB_DDATA_CATALOG_QUANTITY_TITLE"),
            'required' => false,
            'group-id' => 'catalog-property',
            'type' => ['integer']
        ];
        $arResult['YLAB_DDATA_CATALOG_CAN_BUY_ZERO'] = [
            'title' => Loc::getMessage("YLAB_DDATA_CATALOG_CAN_BUY_ZERO_TITLE"),
            'required' => false,
            'group-id' => 'catalog-property',
            'type' => ['checkbox']
        ];
        $arResult['YLAB_DDATA_CATALOG_SUBSCRIBE'] = [
            'title' => Loc::getMessage("YLAB_DDATA_CATALOG_SUBSCRIBE_TITLE"),
            'required' => false,
            'group-id' => 'catalog-property',
            'type' => ['checkbox']
        ];
        $arResult['YLAB_DDATA_CATALOG_WEIGHT'] = [
            'title' => Loc::getMessage("YLAB_DDATA_CATALOG_WEIGHT_TITLE"),
            'required' => false,
            'group-id' => 'catalog-property',
            'type' => ['integer']
        ];
        $arResult['YLAB_DDATA_CATALOG_LENGTH'] = [
            'title' => Loc::getMessage("YLAB_DDATA_CATALOG_LENGTH_TITLE"),
            'required' => false,
            'group-id' => 'catalog-property',
            'type' => ['integer']
        ];
        $arResult['YLAB_DDATA_CATALOG_WIDTH'] = [
            'title' => Loc::getMessage("YLAB_DDATA_CATALOG_WIDTH_TITLE"),
            'required' => false,
            'group-id' => 'catalog-property',
            'type' => ['integer']
        ];
        $arResult['YLAB_DDATA_CATALOG_HEIGHT'] = [
            'title' => Loc::getMessage("YLAB_DDATA_CATALOG_HEIGHT_TITLE"),
            'required' => false,
            'group-id' => 'catalog-property',
            'type' => ['integer']
        ];

        $arGroupsPrice = $this->arGroupsPrice;
        foreach ($arGroupsPrice as $sPriceGroupCode => $arGroupPriceInfo) {
            $arResult['YLAB_DDATA_CATALOG_' . $sPriceGroupCode . '_PRICE'] = [
                'title' => $arGroupPriceInfo['NAME'],
                'required' => $arGroupPriceInfo['BASE'] == 'Y' ? true : false,
                'group-id' => 'catalog-property',
                'type' => ['integer']
            ];
            $arResult['YLAB_DDATA_CATALOG_' . $sPriceGroupCode . '_CURRENCY'] = [
                'title' => Loc::getMessage("YLAB_DDATA_CATALOG_PROPERTY_CURRENCY",
                    ['#PRICE#' => $arGroupPriceInfo['NAME']]),
                'required' => $arGroupPriceInfo['BASE'] == 'Y' ? true : false,
                'group-id' => 'catalog-property',
                'type' => ['currency']
            ];
        }

        $arResult['YLAB_DDATA_CATALOG_PURCHASING_PRICE'] = [
            'title' => Loc::getMessage("YLAB_DDATA_CATALOG_PURCHASING_PRICE_TITLE"),
            'required' => false,
            'group-id' => 'catalog-property',
            'type' => ['integer']
        ];
        $arResult['YLAB_DDATA_CATALOG_PURCHASING_CURRENCY'] = [
            'title' => Loc::getMessage("YLAB_DDATA_CATALOG_PURCHASING_CURRENCY_TITLE"),
            'required' => false,
            'group-id' => 'catalog-property',
            'type' => ['currency']
        ];

        return $arResult;
    }

    /**
     * Метод для получения подходящего генератора для свойства
     * @param $arProperty
     * @return array
     */
    public function getGenType($arProperty)
    {
        $arResult = [];
        if ($arProperty['USER_TYPE']) {
            switch ($arProperty['USER_TYPE']) {
                case 'UserID':
                    $arResult = ["user"];
                    break;
                case 'DateTime':
                    $arResult = ["datetime"];
                    break;
                case 'Date':
                    $arResult = ["datetime"];
                    break;
                case 'SectionAuto':
                    $arResult = ["iblock.section"];
                    break;
                case 'HTML':
                    $arResult = ["string"];
                    break;
                case 'directory':
                    $arResult = ["dictionary"];
                    break;
                default:
                    $arResult = [];
                    break;
            }
        } else {
            switch ($arProperty['PROPERTY_TYPE']) {
                case 'S':
                    $arResult = ["string", "integer"];
                    break;
                case 'N':
                    $arResult = ["integer"];
                    break;
                case 'F':
                    $arResult = ["file"];
                    break;
                case 'E':
                    $arResult = ["iblock.element"];
                    break;
                case 'G':
                    $arResult = ["iblock.section"];
                    break;
                case 'L':
                    $arResult = ["iblock.list"];
                    break;
                default:
                    $arResult = [];
                    break;
            }
        }

        return $arResult;
    }
}