<?php

namespace Ylab\Ddata\data;

use Bitrix\Main\HttpRequest;
use Ylab\Ddata\interfaces\DataUnitClass;
use Bitrix\Main\Localization\Loc;
use Ylab\Ddata\Helpers;
use Bitrix\Main\Loader;
use Bitrix\Highloadblock\HighloadBlockTable as HLBT;

Loc::loadMessages(__FILE__);

/**
 * Генератор привязки к элементам highload-блоков
 *
 * Class RandomHLElement
 * @package Ylab\Ddata\data
 */
class RandomHLElement extends DataUnitClass
{
    protected $sRandom = 'Y';

    /** @var integer $iHLBlock */
    protected $iHLBlock = '';
    protected $arHLBlocks = [];
    protected $iField = '';
    protected $arFieldSelectedElements = [];

    /**
     * RandomHLElement constructor.
     * @param $sProfileID - ID профиля
     * @param $sFieldCode - Симфольный код свойства
     * @param $sGeneratorID - ID уже сохраненного генератора
     * @throws \Bitrix\Main\ArgumentException
     * @throws \Bitrix\Main\LoaderException
     * @throws \Bitrix\Main\ObjectPropertyException
     * @throws \Bitrix\Main\SystemException
     */
    public function __construct(string $sProfileID = '', string $sFieldCode = '', string $sGeneratorID = '')
    {
        Loader::includeModule('highloadblock');

        parent::__construct($sProfileID, $sFieldCode, $sGeneratorID);

        $arHLBlocks = HLBT::getList([
            'order' => 'NAME'
        ])->fetchAll();

        if (!empty($arHLBlocks)) {
            $this->arHLBlocks = $arHLBlocks;
        }

        if (!empty($this->options['random'])) {
            $this->sRandom = $this->options['random'];
        }

        if (!empty($this->options['hlblock'])) {
            $this->iHLBlock = $this->options['hlblock'];
        }

        if (!empty($this->options['field'])) {
            $this->iField = $this->options['field'];
        }

        if (!empty($this->options['elements'])) {
            if ($this->sRandom === 'Y') {
                $this->arFieldSelectedElements = $this->getHLBlockElements($this->iHLBlock, '', false);
            } else {
                $this->arFieldSelectedElements = $this->options['elements'];
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
            'ID' => 'hl.element',
            'NAME' => Loc::getMessage('YLAB_DDATA_DATA_HL_ELEMENT_NAME'),
            'DESCRIPTION' => Loc::getMessage('YLAB_DDATA_DATA_HL_ELEMENT_DESCRIPTION'),
            'TYPE' => 'hl.element',
            'CLASS' => __CLASS__
        ];
    }

    /**
     * Метод возвращает html строку формы с настройкой генератора если таковые необходимы
     *
     * @param HttpRequest $request
     * @return false|mixed|string
     */
    public function getOptionForm(HttpRequest $request)
    {
        $arRequest = $request->toArray();
        $sGeneratorID = $request->get('generator');
        $sProfileID = $request->get('profile_id');
        $sPropertyName = $request->get('property-name');

        preg_match_all('/^(.*\[)(.*)(\])/', $sPropertyName, $matches);
        $sPropertyCode = $matches[2][0];

        ob_start();
        include Helpers::getModulePath() . '/admin/fragments/random_hl_element_form.php';
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
            $ihlblock = $arPrepareRequest['hlblock'];

            if (!empty($sRandom) && !empty($ihlblock)) {
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
        if ($this->sRandom === 'Y') {
            if ($this->arFieldSelectedElements) {
                return array_rand($this->arFieldSelectedElements);
            }
        } else {
            if ($this->arFieldSelectedElements) {
                $sResult = array_rand($this->arFieldSelectedElements);
                return $this->arFieldSelectedElements[$sResult];
            }
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
                    $arList[0] = 'ID';
                    foreach ($oBserFields as $arBserField) {
                        $fieldTitle = strlen($arBserField['LIST_COLUMN_LABEL']) ? $arBserField['LIST_COLUMN_LABEL'] : $arBserField['FIELD_NAME'];
                        $arList[(int)$arBserField['ID']] = $fieldTitle;
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
     * @return array
     * @throws \Bitrix\Main\LoaderException
     * @throws \Bitrix\Main\SystemException
     */
    public function getHLBlockElements($iHLBlockId = 0, $sField = '', $bFullData = true)
    {
        Loader::includeModule('highloadblock');

        $arList = [];

        if ($iHLBlockId) {
            $sEntityDataClass = $this->GetEntityDataClass($iHLBlockId);

            $oData = $sEntityDataClass::getList([
                'select' => ['*']
            ]);
            while ($arData = $oData->fetch()) {
                if ($bFullData) {
                    $arList[] = [
                        'ID' => $arData['ID'],
                        'FIELD' => $sField,
                        'VALUE' => $arData[$sField]
                    ];
                } else {
                    $arList[] = $arData['ID'];
                }
            }
        }

        return $arList;
    }

    /**
     * Получение класса сущности HL
     *
     * @param $iHlBlockId
     * @return \Bitrix\Main\Entity\DataManager|bool
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