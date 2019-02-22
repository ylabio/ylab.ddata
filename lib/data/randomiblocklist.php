<?php

namespace Ylab\Ddata\Data;

use Bitrix\Main\Loader;
use Bitrix\Main\Localization\Loc;
use Bitrix\Main\Web\Json;
use Bitrix\Main\HttpRequest;
use Ylab\Ddata\Helpers;
use Ylab\Ddata\Orm\EntityUnitProfileTable;
use Ylab\Ddata\Interfaces\DataUnitClass;

Loc::loadMessages(__FILE__);

/**
 * Генерация случайного элемента списка
 *
 * Class RandomIBlockList
 * @package Ylab\Ddata\Data
 */
class RandomIBlockList extends DataUnitClass
{
    protected $sRandom = 'Y';

    /** @var array $arItemsRandom */
    protected $arItemsRandom = [];

    /** @var array $arSelectedItems */
    protected $arSelectedItems = [];

    /**
     * RandomIBlockList constructor.
     * @param $sProfileID - ID профиля
     * @param $sFieldCode - Симфольный код свойства
     * @param $sGeneratorID - ID уже сохраненного генератора
     * @throws \Bitrix\Main\ArgumentException
     * @throws \Exception
     */
    public function __construct(string $sProfileID = '', string $sFieldCode = '', string $sGeneratorID = '')
    {
        parent::__construct($sProfileID, $sFieldCode, $sGeneratorID);

        if (!empty($this->options['selected-items'])) {
            $this->arSelectedItems = $this->options['selected-items'];
        }

        if (!empty($this->options['random'])) {
            $this->sRandom = $this->options['random'];

            if ($this->sRandom == 'Y') {
                $iIblockId = $this->getIblockId($sProfileID);
                if (strpos($sFieldCode, "YLAB_DDATA_OFFER_") !== false) {
                    Loader::includeModule('catalog');
                    $sFieldCode = substr($sFieldCode, strlen("YLAB_DDATA_OFFER_"));
                    $arOfferInfo = \CCatalogSKU::GetInfoByProductIBlock($iIblockId);
                    $iIblockId = $arOfferInfo['IBLOCK_ID'];
                }

                if (empty($iIblockId)) {
                    throw new \Exception(Loc::getMessage('YLAB_DDATA_DATA_IBLOCK_SECTION_EXCEPTION_IBLOC_ID'));
                }

                $this->arItemsRandom = $this->getItemList($iIblockId, $sFieldCode);
            }
        } else {
            if ($this->sRandom == 'Y') {
                $iIblockId = $this->getIblockId($sProfileID);
                if (strpos($sFieldCode, "YLAB_DDATA_OFFER_") !== false) {
                    Loader::includeModule('catalog');
                    $sFieldCode = substr($sFieldCode, strlen('YLAB_DDATA_OFFER_'));
                    $arOfferInfo = \CCatalogSKU::GetInfoByProductIBlock($iIblockId);
                    $iIblockId = $arOfferInfo['IBLOCK_ID'];
                }

                if (empty($iIblockId)) {
                    throw new \Exception(Loc::getMessage('YLAB_DDATA_DATA_IBLOCK_SECTION_EXCEPTION_IBLOC_ID'));
                }

                $this->arItemsRandom = $this->getItemList($iIblockId, $sFieldCode);
            }
        }
    }

    /**
     * Метод возврящает массив описывающий тип данных. ID, Имя, scalar type php
     *
     * @return array
     */
    public function getDescription()
    {
        return [
            'ID' => 'iblock.list',
            'NAME' => Loc::getMessage('YLAB_DDATA_DATA_IBLOCK_LIST_NAME'),
            'DESCRIPTION' => Loc::getMessage("YLAB_DDATA_DATA_IBLOCK_LIST_DESCRIPTION"),
            'TYPE' => 'iblock.list',
            'CLASS' => __CLASS__
        ];
    }

    /**
     * Метод возвращает html строку формы с настройкой генератора если таковые необходимы
     *
     * @param HttpRequest $request
     * @return string
     * @throws \Exception
     */
    public function getOptionForm(HttpRequest $request)
    {
        $sGeneratorID = $request->get('generator');
        $sProfileID = $request->get('profile_id');
        $sPropertyName = $request->get('property-name');

        $arIblockId = $request->get('prepare');
        $iIblockId = $arIblockId['iblock_id'];

        if (empty($iIblockId)) {
            $iIblockId = $this->getIblockId($sProfileID);

            if (empty($iIblockId)) {
                throw new \Exception(Loc::getMessage('YLAB_DDATA_DATA_IBLOCK_LIST_EXCEPTION_IBLOC_ID'));
            }
        }

        $iStartPropertyCount = strlen('PROPERTIES[');
        $sPropertyCode = rtrim(substr($sPropertyName, $iStartPropertyCount), "]");
        if (strpos($sPropertyCode, "YLAB_DDATA_OFFER_") !== false) {
            Loader::includeModule('catalog');
            $sPropertyCode = substr($sPropertyCode, strlen("YLAB_DDATA_OFFER_"));
            $arOfferInfo = \CCatalogSKU::GetInfoByProductIBlock($iIblockId);
            $iIblockId = $arOfferInfo['IBLOCK_ID'];
        }
        $arItemList = $this->getItemList($iIblockId, $sPropertyCode);

        ob_start();
        include Helpers::getModulePath() . '/admin/fragments/random_iblock_list_settings_form.php';
        $tpl = ob_get_contents();
        ob_end_clean();

        return $tpl;
    }

    /**
     * Метод проверяет на валидность данные настройки генератора
     *
     * @param HttpRequest $request
     * @return bool
     */
    public  function isValidateOptions(HttpRequest $request)
    {
        $arPrepareRequest = $request->get('option');

        if ($arPrepareRequest) {
            $sRandom = $arPrepareRequest['random'];
            $arSelectedItems = $arPrepareRequest['selected-items'];

            if (!empty($sRandom) || !empty($arSelectedItems)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Возвращает случайную запись соответствующего типа
     *
     * @return mixed|string
     * @throws \Exception
     */
    public function getValue()
    {
        if ($this->sRandom === 'Y') {
            if ($this->arItemsRandom) {
                return array_rand($this->arItemsRandom);
            }
        } else {
            if ($this->arSelectedItems) {
                $sResult = array_rand($this->arSelectedItems);

                return $this->arSelectedItems[$sResult];
            }
        }

        return '';
    }

    /**
     * Получение значений множественного списка
     *
     * @param int $iIblockId - ID ифоблока
     * @param string $sCode - Символьный код свойства
     * @return array
     */
    private function getItemList($iIblockId = 0, $sCode = "")
    {
        $arItems = [];

        if ($iIblockId && $sCode && \CModule::IncludeModule('iblock')) {
            $oItemEnum = \CIBlockPropertyEnum::GetList([], ['IBLOCK_ID' => $iIblockId, 'CODE' => $sCode]);
            while ($arItemEnum = $oItemEnum->Fetch()) {
                $arItems[$arItemEnum['ID']] = $arItemEnum['VALUE'];
            }
        }

        return $arItems;
    }

    /**
     * Получение ID инфоблока из настроек профиля
     *
     * @param $sProfileID
     * @return bool
     * @throws \Bitrix\Main\ArgumentException
     */
    private function getIblockId($sProfileID)
    {
        if ($sProfileID) {
            $optionsJSON = EntityUnitProfileTable::getList([
                'select' => [
                    'OPTIONS'
                ],
                'filter' => [
                    'ID' => $sProfileID
                ]
            ])->Fetch();

            if ($optionsJSON) {
                $optionsJSON = Json::decode($optionsJSON['OPTIONS']);

                return $optionsJSON['iblock_id'];
            }
        }

        return false;
    }
}