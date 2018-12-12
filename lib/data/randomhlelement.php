<?

namespace Ylab\Ddata\data;

use Bitrix\Main\HttpRequest;
use Ylab\Ddata\interfaces\DataUnitClass;
use Bitrix\Main\Localization\Loc;
use Ylab\Ddata\Helpers;
use Bitrix\Main\Loader;
use Bitrix\Highloadblock\HighloadBlockTable as HLBT;

/**
 * Class RandomHLElement
 * @package Ylab\Ddata\data
 */
class RandomHLElement extends DataUnitClass
{
    private static $bCheckStaticMethod = true;

    protected $sRandom = 'Y';
    protected $iHLBlock = '';
    protected $irField = '';
    protected $arFieldSelectedElements = [];

    /**
     * RandomHLElement constructor.
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
        Loader::includeModule('highloadblock');

        self::$bCheckStaticMethod = false;
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
                $this->arFieldSelectedElements = static::getHLBlockElements($this->iHLBlock, '', false);
            } else {
                $this->arFieldSelectedElements = $this->options['elements'];
            }
        }
    }

    /**
     * @return array
     */
    public static function getDescription()
    {
        return [
            "ID" => "hl.element",
            "NAME" => Loc::getMessage("YLAB_DDATA_DATA_HL_ELEMENT_NAME"),
            "DESCRIPTION" => Loc::getMessage('YLAB_DDATA_DATA_HL_ELEMENT_DESCRIPTION'),
            "TYPE" => "hl.element",
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
        include Helpers::getModulePath() . '/admin/fragments/random_hl_element_form.php';
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
            $ihlblock = $arPrepareRequest['hlblock'];

            if (!empty($sRandom) && !empty($ihlblock)) {
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
        } else {
            throw new \Exception(Loc::getMessage('YLAB_DDATA_DATA_HL_ELEMENT_EXCEPTION_STATIC'));
        }
    }

    /**
     * @param int $iHLBlockId
     * @return array
     * @throws \Bitrix\Main\ArgumentException
     * @throws \Bitrix\Main\LoaderException
     * @throws \Bitrix\Main\ObjectPropertyException
     * @throws \Bitrix\Main\SystemException
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
     * @param int $iHLBlockId
     * @param string $sField
     * @param bool $bFullData
     * @return array
     * @throws \Bitrix\Main\ArgumentException
     * @throws \Bitrix\Main\LoaderException
     * @throws \Bitrix\Main\ObjectPropertyException
     * @throws \Bitrix\Main\SystemException
     */
    public static function getHLBlockElements($iHLBlockId = 0, $sField = '', $bFullData = true)
    {
        Loader::includeModule('highloadblock');

        $arList = [];

        if ($iHLBlockId) {
            $sEntityDataClass = static::GetEntityDataClass($iHLBlockId);

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