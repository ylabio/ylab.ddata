<?php

namespace Ylab\Ddata\Data;

use Bitrix\Main\Localization\Loc;
use Ylab\Ddata\Interfaces\DataUnitClass;
use Bitrix\Main\HttpRequest;
use Ylab\Ddata\Helpers;
use Ylab\Ddata\Orm\EntityUnitProfileTable;
use Bitrix\Main\Web\Json;

Loc::loadMessages(__FILE__);

/**
 * Class RandomIBlockSection
 * @package Ylab\Ddata\Data
 */
class RandomIBlockSection extends DataUnitClass
{
    private static $bCheckStaticMethod = true;

    protected $sRandom = 'Y';
    protected $arSectionsRandom = [];
    protected $arSelectedSections = [];

    /**
     * RandomIBlockSection constructor.
     * @param $sProfileID
     * @param $sFieldCode
     * @param $sGeneratorID
     * @throws \Bitrix\Main\ArgumentException
     * @throws \Exception
     */
    public function __construct($sProfileID, $sFieldCode, $sGeneratorID)
    {
        self::$bCheckStaticMethod = false;

        parent::__construct($sProfileID, $sFieldCode, $sGeneratorID);

        if (!empty($this->options['selected-sections'])) {
            $this->arSelectedSections = $this->options['selected-sections'];
        }

        if (!empty($this->options['random'])) {
            $this->sRandom = $this->options['random'];

            if ($this->sRandom == 'Y') {
                $iIblockId = static::getIblockId($sProfileID);
                if (empty($iIblockId)) {
                    throw new \Exception(Loc::getMessage('YLAB_DDATA_DATA_IBLOCK_SECTION_EXCEPTION_IBLOC_ID'));
                }

                if (count($this->arSelectedSections) == 1) {
                    $this->arSectionsRandom = static::getSectionList($iIblockId, $this->arSelectedSections[0]);
                } else {
                    $this->arSectionsRandom = static::getSectionList($iIblockId);
                }
            }
        }
    }

    /**
     * @return array
     */
    public static function getDescription()
    {
        return [
            "ID" => "iblock.section",
            "NAME" => Loc::getMessage("YLAB_DDATA_DATA_IBLOCK_SECTION_NAME"),
            "DESCRIPTION" => Loc::getMessage('YLAB_DDATA_DATA_IBLOCK_SECTION_DESCRIPTION'),
            "TYPE" => "iblock.section",
            "CLASS" => __CLASS__
        ];
    }

    /**
     * @inheritdoc
     * @param HttpRequest $request
     * @return string
     * @throws \Bitrix\Main\ArgumentException
     * @throws \Exception
     */
    public static function getOptionForm(HttpRequest $request)
    {

        $arRequest = $request->toArray();
        $arOptions = $arRequest['option'];
        $sGeneratorID = $request->get('generator');
        $sFieldID = $request->get('field');
        $sProfileID = $request->get('profile_id');
        $sPropertyName = $request->get('property-name');
        $arClassVars = get_class_vars(__CLASS__);
        $arDefaultOptions = [
            'random' => $arClassVars['sRandom'],
            'selected-sections' => $arClassVars['arSelectedSections']
        ];
        if (!is_array($arOptions)) {
            $arOptions = [];
        }
        $arOptions = array_merge($arDefaultOptions, self::getOptions($sGeneratorID, $sProfileID, $sFieldID),
            $arOptions);
        $arIblockId = $request->get('prepare');
        $iIblockId = $arIblockId['iblock_id'];

        if (empty($iIblockId)) {
            $iIblockId = static::getIblockId($sProfileID);
            if (empty($iIblockId)) {
                throw new \Exception(Loc::getMessage('YLAB_DDATA_DATA_IBLOCK_SECTION_EXCEPTION_IBLOC_ID'));
            }
        }

        $arSection = static::getSectionList($iIblockId);

        ob_start();
        include Helpers::getModulePath() . '/admin/fragments/random_iblock_section_settings_form.php';
        $tpl = ob_get_contents();
        ob_end_clean();

        return $tpl;
    }

    /**
     * @inheritdoc
     * @param HttpRequest $request
     * @return bool
     */
    public static function isValidateOptions(HttpRequest $request)
    {
        $arPrepareRequest = $request->get('option');

        if ($arPrepareRequest) {
            $sRandom = $arPrepareRequest['random'];
            $arSelectedSections = $arPrepareRequest['selected-sections'];

            if (!empty($sRandom) || !empty($arSelectedSections)) {
                return true;
            }
        }
    }

    /**
     * @inheritdoc
     * @return mixed|string
     * @throws \Exception
     */
    public function getValue()
    {
        if (!self::$bCheckStaticMethod) {
            if ($this->sRandom === 'Y') {
                if ($this->arSectionsRandom) {
                    return array_rand($this->arSectionsRandom);
                }
            } else {
                if ($this->arSelectedSections) {
                    $sResult = array_rand($this->arSelectedSections);

                    return $this->arSelectedSections[$sResult];
                }
            }

            return '';
        } else {
            throw new \Exception(Loc::getMessage('YLAB_DDATA_DATA_IBLOCK_SECTION_EXCEPTION_STATIC'));
        }
    }

    /**
     * @param int $iIblockId
     * @return array
     */
    private function getSectionList($iIblockId = 0, $iParendID = 0)
    {
        $arSection = [];

        If ($iIblockId && \CModule::IncludeModule('iblock')) {
            $arFilter = [
                'IBLOCK_ID' => $iIblockId
            ];

            if ($iParendID) {
                $arFilter['SECTION_ID'] = $iParendID;
            }

            $oSection = \CIBlockSection::GetList(["left_margin" => "asc"], $arFilter);
            while ($arSectionRes = $oSection->Fetch()) {
                $arSection[$arSectionRes['ID']][] = $arSectionRes['NAME'];
                $arSection[$arSectionRes['ID']][] = $arSectionRes['DEPTH_LEVEL'];
            }
        }

        return $arSection;
    }

    /**
     * @param $sProfileID
     * @return bool
     * @throws \Bitrix\Main\ArgumentException
     * @throws \Bitrix\Main\ObjectPropertyException
     * @throws \Bitrix\Main\SystemException
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