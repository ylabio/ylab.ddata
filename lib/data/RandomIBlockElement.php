<?php

namespace Ylab\Ddata\Data;

use Bitrix\Main\Loader;
use Bitrix\Main\Localization\Loc;
use Bitrix\Main\HttpRequest;
use Ylab\Ddata\Interfaces\DataUnitClass;
use Ylab\Ddata\Helpers;

Loc::loadLanguageFile(__FILE__);

/**
 * Class RandomIBlockElement
 * @package Ylab\Ddata\Data
 */
class RandomIBlockElement extends DataUnitClass
{
    private static $bCheckStaticMethod = true;

    protected $sRandom = "Y";
    protected $arIBlockElements = [];
    protected $arIBlocks = [];
    protected $arSelectedElements = [];

    /**
     * RandomIBlockElement constructor.
     * @param $sProfileID
     * @param $sFieldCode
     * @param $sGeneratorID
     * @throws \Bitrix\Main\ArgumentException
     * @throws \Bitrix\Main\LoaderException
     * @throws \Bitrix\Main\ObjectPropertyException
     * @throws \Bitrix\Main\SystemException
     */
    public function __construct($sProfileID, $sFieldCode, $sGeneratorID)
    {
        Loader::includeModule('iblock');
        self::$bCheckStaticMethod = false;

        parent::__construct($sProfileID, $sFieldCode, $sGeneratorID);

        $oProperty = \CIBlockProperty::GetList([], ['CODE' => $sFieldCode]);
        $arProperty = $oProperty->Fetch();

        $arIblockFilter = ["ACTIVE" => "Y"];
        $arElementFilter = ["ACTIVE" => "Y"];
        if (!empty($arProperty['LINK_IBLOCK_ID']) && intval($arProperty['LINK_IBLOCK_ID']) > 0) {
            $arIblockFilter['ID'] = $arProperty['LINK_IBLOCK_ID'];
            $arElementFilter['IBLOCK_ID'] = $arProperty['LINK_IBLOCK_ID'];
        }

        $oIBlocksPrepare = \CIBlock::GetList([], $arIblockFilter, false);
        while ($arIBlocksPrepare = $oIBlocksPrepare->GetNext()) {
            $arIBlocks[] = [
                'ID' => $arIBlocksPrepare['ID'],
                'NAME' => $arIBlocksPrepare['NAME']
            ];
        }

        if (!empty($arIBlocks)) {
            $this->arIBlocks = $arIBlocks;
        }

        $oIBlockElementsPrepare = \CIBlockElement::GetList([], $arElementFilter, false, false, []);
        while ($arIBlockElementsPrepare = $oIBlockElementsPrepare->GetNext()) {
            $arIBlockElements[] = [
                'ID' => $arIBlockElementsPrepare['ID'],
                'NAME' => $arIBlockElementsPrepare['NAME'],
                'IBLOCK_ID' => $arIBlockElementsPrepare['IBLOCK_ID']
            ];
        }
        if (!empty($arIBlockElements)) {
            $this->arIBlockElements = $arIBlockElements;
        }

        if (!empty($this->options['random'])) {
            $this->sRandom = $this->options['random'];
        }
        if (!empty($this->options['selected-elements'])) {
            $this->arSelectedElements = $this->options['selected-elements'];
        }
    }

    /**
     * @return array
     */
    public static function getDescription()
    {
        return [
            "ID" => "random.iblock.element.unit",
            "NAME" => Loc::getMessage('YLAB_DDATA_DATA_IBLOCK_ELEMENT_NAME'),
            "DESCRIPTION" => Loc::getMessage('YLAB_DDATA_DATA_IBLOCK_ELEMENT_DESCRIPTION'),
            "TYPE" => "iblock.element",
            "CLASS" => __CLASS__
        ];
    }

    /**
     * @param HttpRequest $request
     * @return mixed|string
     * @throws \Bitrix\Main\ArgumentException
     * @throws \Bitrix\Main\ObjectPropertyException
     * @throws \Bitrix\Main\SystemException
     */
    public static function getOptionForm(HttpRequest $request)
    {
        $arRequest = $request->toArray();
        $arOptions = (array)$arRequest['option'];
        $sGeneratorID = $request->get('generator');
        $sFieldID = $request->get('field');
        $sProfileID = $request->get('profile_id');
        $sPropertyName = $request->get('property-name');

        preg_match_all('/^(.*\[)(.*)(\])/', $sPropertyName, $matches);
        $sPropertyCode = $matches[2][0];

        $arOptions = array_merge(self::getOptions($sGeneratorID, $sProfileID, $sFieldID), $arOptions);
        ob_start();
        include Helpers::getModulePath() . "/admin/fragments/random_iblockelement_settings_form.php";
        $tpl = ob_get_contents();
        ob_end_clean();
        return $tpl;
    }

    /**
     * @param HttpRequest $request
     * @return bool
     */
    public static function isValidateOptions(HttpRequest $request)
    {
        $arPrepareRequest = $request->get('option');
        $bFlag = false;
        if ($arPrepareRequest['random'] == 'Y') {
            if (!empty($arPrepareRequest['iblock'])) {
                $bFlag = true;
            }
        } else {
            if (!empty($arPrepareRequest['iblock']) && !empty($arPrepareRequest['selected-elements'])) {
                $bFlag = true;
            }
        }

        return $bFlag;
    }

    /**
     * @return string
     * @throws \Exception
     */
    public function getValue()
    {
        if (!self::$bCheckStaticMethod) {
            $sRandom = $this->sRandom;
            $arSelectedElements = $this->arSelectedElements;
            $arIBlockElements = $this->arIBlockElements;

            if ($sRandom == 'Y') {
                $arSelectedElements = array_filter($arSelectedElements);
                if (!empty($arSelectedElements)) {
                    $iSelectedElement = array_rand($arSelectedElements);

                    return $arSelectedElements[$iSelectedElement];
                } else {
                    $iSelectedElement = array_rand($arIBlockElements);

                    return $arIBlockElements[$iSelectedElement]['ID'];
                }
            } else {
                return $this->arSelectedElements[0];
            }
        } else {
            throw new \Exception(Loc::getMessage('YLAB_DDATA_DATA_IBLOCK_ELEMENT_EXCEPTION_STATIC'));
        }
    }
}