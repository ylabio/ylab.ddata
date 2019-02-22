<?php

namespace Ylab\Ddata\data;

use Bitrix\Main\HttpRequest;
use Bitrix\Main\Web\Json;
use Ylab\Ddata\interfaces\DataUnitClass;
use Bitrix\Main\Localization\Loc;
use Ylab\Ddata\Helpers;
use Bitrix\Main\Loader;
use Bitrix\Highloadblock\HighloadBlockTable as HLBT;
use Ylab\Ddata\Orm\EntityUnitProfileTable;

Loc::loadMessages(__FILE__);

/**
 * Генератор справочника инфоблока
 *
 * Class RandomDictionaryHL
 * @package Ylab\Ddata\data
 */
class RandomDictionaryHL extends DataUnitClass
{
    protected $sRandom = 'Y';

    /** @var integer $iHLBlock */
    protected $iHLBlock = '';
    protected $iField = '';
    protected $arFieldSelectedElements = [];

    /**
     * RandomDictionaryHL constructor.
     * @param $sProfileID - ID профиля
     * @param $sFieldCode - Симфольный код свойства
     * @param $sGeneratorID - ID уже сохраненного генератора
     * @throws \Bitrix\Main\ArgumentException
     * @throws \Bitrix\Main\LoaderException
     * @throws \Bitrix\Main\SystemException
     */
    public function __construct(string $sProfileID = '', string $sFieldCode = '', string $sGeneratorID = '')
    {
        Loader::includeModule('highloadblock');
        Loader::includeModule('iblock');

        parent::__construct($sProfileID, $sFieldCode, $sGeneratorID);

        if (!empty($this->options['random'])) {
            $this->sRandom = $this->options['random'];
        }

        if (!empty($this->options['hlblock'])) {
            $this->iHLBlock = $this->options['hlblock'];
        } else {
            if (!empty($sProfileID)) {
                $arProfile = EntityUnitProfileTable::getById($sProfileID)->fetch();
                $arProfileOptions = Json::decode($arProfile['OPTIONS']);
                $iIblockID = $arProfileOptions['iblock_id'];
            }

            $oProperties = \CIBlockProperty::GetList([],
                ["ACTIVE" => "Y", "IBLOCK_ID" => $iIblockID, 'CODE' => $sFieldCode]);
            while ($arProperties = $oProperties->GetNext()) {
                $sHLBTableName = $arProperties['USER_TYPE_SETTINGS']['TABLE_NAME'];
            }
            $arHLBlock = HLBT::getList([
                'filter' => ['=TABLE_NAME' => $sHLBTableName]
            ])->fetch();
            $this->iHLBlock = $arHLBlock['ID'];
        }

        if (!empty($this->options['field'])) {
            $this->iField = $this->options['field'];
        }

        if (!empty($this->options['elements'])) {
            if ($this->sRandom === 'Y') {
                $this->arFieldSelectedElements = $this->getHLBlockElements($this->iHLBlock, '', false);
            } else {
                $this->arFieldSelectedElements = $this->getHLBlockElements($this->iHLBlock, '', false, $this->options['elements']);
            }
        } else {
            $this->arFieldSelectedElements = $this->getHLBlockElements($this->iHLBlock, '', false);
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
            'ID' => 'dictionary.iblock',
            'NAME' => Loc::getMessage('YLAB_DDATA_DATA_DICTIONARY_IBLOCK_NAME'),
            'DESCRIPTION' => Loc::getMessage('YLAB_DDATA_DATA_DICTIONARY_IBLOCK_DESCRIPTION'),
            'TYPE' => 'dictionary',
            'CLASS' => __CLASS__
        ];
    }

    /**
     * Метод возвращает html строку формы с настройкой генератора если таковые необходимы
     *
     * @param HttpRequest $request
     * @return false|mixed|string
     * @throws \Bitrix\Main\ArgumentException
     * @throws \Bitrix\Main\LoaderException
     * @throws \Bitrix\Main\ObjectPropertyException
     * @throws \Bitrix\Main\SystemException
     */
    public function getOptionForm(HttpRequest $request)
    {
        Loader::includeModule('iblock');
        Loader::includeModule('highloadblock');
        $arRequest = $request->toArray();
        $sGeneratorID = $request->get('generator');
        $sProfileID = $request->get('profile_id');
        $sPropertyName = $request->get('property-name');

        preg_match_all('/^(.*\[)(.*)(\])/', $sPropertyName, $matches);
        $sPropertyCode = $matches[2][0];

        $iIblockID = $arRequest['prepare']['iblock_id'];
        if (!empty($sProfileID)) {
            $arProfile1 = EntityUnitProfileTable::getById($sProfileID)->fetch();
            $arProfileOptions = Json::decode($arProfile1['OPTIONS']);
            $iIblockID = $arProfileOptions['iblock_id'];
        }

        $oProperties = \CIBlockProperty::GetList([],
            ["ACTIVE" => "Y", "IBLOCK_ID" => $iIblockID, 'CODE' => $sPropertyCode]);

        $sHLBTableName = '';
        while ($arProperties = $oProperties->GetNext()) {
            $sHLBTableName = $arProperties['USER_TYPE_SETTINGS']['TABLE_NAME'];
        }
        $arHLBlock = HLBT::getList([
            'filter' => ['=TABLE_NAME' => $sHLBTableName]
        ])->fetch();
        $iHLBlock = $arHLBlock['ID'];
        $arFields = $this->getHLBlockFields($iHLBlock);

        ob_start();
        include Helpers::getModulePath() . '/admin/fragments/random_dictionary_iblock_settings_form.php';
        $tpl = ob_get_contents();
        ob_end_clean();

        return $tpl;
    }

    /**
     * Метод проверяет на валидность данные настройки генератора
     *
     * @param HttpRequest $request
     * @return bool|mixed
     */
    public  function isValidateOptions(HttpRequest $request)
    {
        $arPrepareRequest = $request->get('option');

        if ($arPrepareRequest) {
            $sRandom = $arPrepareRequest['random'];

            if (!empty($sRandom)) {

                return true;
            }
        }

        return false;
    }

    /**
     * Возвращает случайную запись соответствующего типа
     *
     * @return mixed
     * @throws \Exception
     */
    public function getValue()
    {
        if ($this->arFieldSelectedElements) {
            $sResult = array_rand($this->arFieldSelectedElements);

            return $this->arFieldSelectedElements[$sResult];
        }

        return [];
    }

    /**
     * Получение свойст HL блока
     *
     * @param int $iHLBlockId
     * @return array
     * @throws \Bitrix\Main\LoaderException
     */
    public function getHLBlockFields($iHLBlockId = 0)
    {
        Loader::includeModule('highloadblock');

        global $USER_FIELD_MANAGER;

        $arList = [];

        if ($iHLBlockId) {
            $arHLBlocks = HLBT::getList([
                'order' => 'NAME'
            ])->fetchAll();

            $arList = [];
            foreach ($arHLBlocks as $arHLBlock) {
                if ($iHLBlockId == $arHLBlock['ID']) {
                    $oBserFields = $USER_FIELD_MANAGER->GetUserFields('HLBLOCK_' . $arHLBlock['ID'], 0, LANGUAGE_ID);
                    $arList['ID'] = 'ID';
                    foreach ($oBserFields as $arBserField) {
                        $fieldTitle = strlen($arBserField['LIST_COLUMN_LABEL']) ? $arBserField['LIST_COLUMN_LABEL'] : $arBserField['FIELD_NAME'];
                        $arList[$arBserField['FIELD_NAME']] = $fieldTitle;
                    }
                }
            }
        }

        return $arList;
    }

    /**
     * Получение элементов HL блока
     *
     * @param int $iHLBlockId
     * @param string $sField
     * @param bool $bFullData
     * @param array $arElementsID
     * @return array
     * @throws \Bitrix\Main\LoaderException
     * @throws \Bitrix\Main\SystemException
     */
    public function getHLBlockElements($iHLBlockId = 0, $sField = '', $bFullData = true, $arElementsID = [])
    {
        Loader::includeModule('highloadblock');

        $arList = [];

        if ($iHLBlockId) {
            $sEntityDataClass = $this->GetEntityDataClass($iHLBlockId);

            if (!empty($arElementsID)) {
                $arFilter = ['=ID' => $arElementsID];
            } else {
                $arFilter = [];
            }
            $oData = $sEntityDataClass::getList([
                'filter' => $arFilter,
                'select' => ['*']
            ]);
            while ($arData = $oData->fetch()) {
                if ($bFullData) {
                    $arList[] = [
                        'ID' => $arData['ID'],
                        'FIELD' => $sField,
                        'VALUE' => $arData[$sField],
                        'XML_ID' => $arData['UF_XML_ID']
                    ];
                } else {
                    $arList[] = $arData['UF_XML_ID'];
                }
            }
        }

        return $arList;
    }

    /**
     * Получение класса сущности HL
     *
     * @param $iHlBlockId
     * @return \Bitrix\Main\ORM\Data\DataManager|bool
     * @throws \Bitrix\Main\SystemException
     */
    private function GetEntityDataClass($iHlBlockId)
    {
        if (empty($iHlBlockId) || $iHlBlockId < 1) {
            return false;
        }

        $arHlblock = HLBT::getById($iHlBlockId)->fetch();
        $oEntity = HLBT::compileEntity($arHlblock);
        $sEntityDataClass = $oEntity->getDataClass();

        return $sEntityDataClass;
    }
}