<?

namespace Ylab\Ddata\data;

use Bitrix\Main\HttpRequest;
use Bitrix\Main\Web\Json;
use Ylab\Ddata\interfaces\DataUnitClass;
use Bitrix\Main\Localization\Loc;
use Ylab\Ddata\Helpers;
use Bitrix\Main\Loader;
use Bitrix\Highloadblock\HighloadBlockTable as HLBT;
use Ylab\Ddata\Orm\EntityUnitProfileTable;

/**
 * Class RandomDictionaryHL
 * @package Ylab\Ddata\data
 */
class RandomDictionaryHL extends DataUnitClass
{
    private static $bCheckStaticMethod = true;

    protected $sRandom = 'Y';
    protected $iHLBlock = '';
    protected $irField = '';
    protected $arFieldSelectedElements = [];

    /**
     * RandomDictionaryHL constructor.
     * @param $sProfileID
     * @param $sFieldCode
     * @param $sGeneratorID
     * @throws \Bitrix\Main\ArgumentException
     * @throws \Bitrix\Main\LoaderException
     */
    public function __construct($sProfileID, $sFieldCode, $sGeneratorID)
    {
        Loader::includeModule('highloadblock');

        self::$bCheckStaticMethod = false;
        parent::__construct($sProfileID, $sFieldCode, $sGeneratorID);

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
                $this->arFieldSelectedElements = static::getHLBlockElements($this->iHLBlock, '', false);
            } else {
                $this->arFieldSelectedElements = static::getHLBlockElements($this->iHLBlock, '', false, $this->options['elements']);
            }
        }
    }

    /**
     * @return array
     */
    public static function getDescription()
    {
        return [
            "ID" => "dictionary.iblock",
            "NAME" => Loc::getMessage("YLAB_DDATA_DATA_DICTIONARY_IBLOCK_NAME"),
            "DESCRIPTION" => Loc::getMessage('YLAB_DDATA_DATA_DICTIONARY_IBLOCK_DESCRIPTION'),
            "TYPE" => "dictionary",
            "CLASS" => __CLASS__
        ];
    }

    /**
     * @param HttpRequest $request
     * @return mixed|string
     * @throws \Bitrix\Main\ArgumentException
     */
    public static function getOptionForm(HttpRequest $request)
    {
        Loader::includeModule('iblock');
        Loader::includeModule('highloadblock');
        $arRequest = $request->toArray();
        $arOptions = $arRequest['option'];
        $sGeneratorID = $request->get('generator');
        $sFieldID = $request->get('field');
        $sProfileID = $request->get('profile_id');
        $sPropertyName = $request->get('property-name');

        preg_match_all('/^(.*\[)(.*)(\])/', $sPropertyName, $matches);
        $sPropertyCode = $matches[2][0];
        $arOptions = array_merge(self::getOptions($sGeneratorID, $sProfileID, $sFieldID), $arOptions);

        $iIblockID = $arRequest['prepare']['iblock_id'];
        if(!empty($sProfileID)) {
            $arProfile1 = EntityUnitProfileTable::getById($sProfileID)->fetch();
            $arProfileOptions = Json::decode($arProfile1['OPTIONS']);
            $iIblockID = $arProfileOptions['iblock_id'];
        }

        $oProperties = \CIBlockProperty::GetList([],
            ["ACTIVE" => "Y", "IBLOCK_ID" => $iIblockID, 'CODE' => $sPropertyCode]);
        $arProperty = [];
        while ($arProperties = $oProperties->GetNext()) {
            $sHLBTableName = $arProperties['USER_TYPE_SETTINGS']['TABLE_NAME'];
        }
        $arHLBlock = HLBT::getList([
            'filter' => ['=TABLE_NAME' => $sHLBTableName]
        ])->fetch();
        $iHLBlock = $arHLBlock['ID'];
        $arFields = static::getHLBlockFields($iHLBlock);

        ob_start();
        include Helpers::getModulePath() . '/admin/fragments/random_dictionary_iblock_settings_form.php';
        $tpl = ob_get_contents();
        ob_end_clean();

        return $tpl;
    }

    /**
     * @param HttpRequest $request
     * @return bool|mixed
     */
    public static function isValidateOptions(HttpRequest $request)
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
     * @return mixed
     * @throws \Exception
     */
    public function getValue()
    {
        if (!self::$bCheckStaticMethod) {

                if ($this->arFieldSelectedElements) {
                    $sResult = array_rand($this->arFieldSelectedElements);

                    return $this->arFieldSelectedElements[$sResult];
                }

        } else {
            throw new \Exception(Loc::getMessage('YLAB_DDATA_DATA_HL_ELEMENT_EXCEPTION_STATIC'));
        }
    }

    /**
     * @param int $iHLBlockId
     * @return array
     * @throws \Bitrix\Main\ArgumentException
     * @throws \Bitrix\Main\LoaderException
     */
    public static function getHLBlockFields($iHLBlockId = 0)
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
     * @param int $iHLBlockId
     * @param string $sField
     * @param bool $bFullData
     * @return array
     * @throws \Bitrix\Main\LoaderException
     */
    public static function getHLBlockElements($iHLBlockId = 0, $sField = '', $bFullData = true, $arElementsID = [])
    {
        Loader::includeModule('highloadblock');

        $arList = [];

        if ($iHLBlockId) {
            $sEntityDataClass = static::GetEntityDataClass($iHLBlockId);

            if(!empty($arElementsID)) {
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