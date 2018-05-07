<?

namespace Ylab\Ddata\Data;

use Bitrix\Main\Localization\Loc;
use Bitrix\Main\Web\Json;
use Bitrix\Main\HttpRequest;
use Ylab\Ddata\Helpers;
use Ylab\Ddata\Orm\EntityUnitProfileTable;
use Ylab\Ddata\Interfaces\DataUnitClass;

Loc::loadMessages(__FILE__);

/**
 * Class RandomIBlockList
 * @package Ylab\Ddata\Data
 */
class RandomIBlockList extends DataUnitClass
{
    private static $bCheckStaticMethod = true;

    protected $sRandom = 'N';
    protected $arItemsRandom = '';
    protected $arSelectedItems = '';

    /**
     * RandomIBlockList constructor.
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

        if (!empty($this->options['selected-items'])) {
            $this->arSelectedItems = $this->options['selected-items'];
        }

        if (!empty($this->options['random'])) {
            $this->sRandom = $this->options['random'];

            if ($this->sRandom == 'Y') {
                $iIblockId = static::getIblockId($sProfileID);

                if (empty($iIblockId)) {
                    throw new \Exception(Loc::getMessage('YLAB_DDATA_DATA_IBLOCK_SECTION_EXCEPTION_IBLOC_ID'));
                }

                $this->arItemsRandom = static::getItemList($iIblockId, $sFieldCode);
            }
        }
    }

    /**
     * @return array
     */
    public static function getDescription()
    {
        return [
            "ID" => "iblock.list",
            "NAME" => Loc::getMessage("YLAB_DDATA_DATA_IBLOCK_LIST_NAME"),
            "DESCRIPTION" => Loc::getMessage("YLAB_DDATA_DATA_IBLOCK_LIST_DESCRIPTION"),
            "TYPE" => "iblock.list",
            "CLASS" => __CLASS__
        ];
    }

    /**
     * @param HttpRequest $request
     * @return string
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

        $arOptions = array_merge(self::getOptions($sGeneratorID, $sProfileID, $sFieldID), $arOptions);

        $arIblockId = $request->get('prepare');
        $iIblockId = $arIblockId['iblock_id'];

        if (empty($iIblockId)) {
            $iIblockId = static::getIblockId($sProfileID);

            if (empty($iIblockId)) {
                throw new \Exception(Loc::getMessage('YLAB_DDATA_DATA_IBLOCK_LIST_EXCEPTION_IBLOC_ID'));
            }
        }

        $arItemList = static::getItemList($iIblockId, rtrim(ltrim($sPropertyName, "PROPERTIES["), "]"));

        ob_start();
        include Helpers::getModulePath() . '/admin/fragments/random_iblock_list_settings_form.php';
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

        if ($arPrepareRequest) {
            $sRandom = $arPrepareRequest['random'];
            $arSelectedItems = $arPrepareRequest['selected-items'];

            if (!empty($sRandom) || !empty($arSelectedItems)) {
                return true;
            }
        }
    }

    /**
     * @return mixed|string
     * @throws \Exception
     */
    public function getValue()
    {
        if (!self::$bCheckStaticMethod) {
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
        } else {
            throw new \Exception(Loc::getMessage('YLAB_DDATA_DATA_IBLOCK_SECTION_EXCEPTION_STATIC'));
        }
    }

    /**
     * @param int $iIblockId
     * @param string $sCode
     * @return array
     */
    private function getItemList($iIblockId = 0, $sCode = "")
    {
        $arItems = [];

        if ($iIblockId && $sCode && \CModule::IncludeModule('iblock')) {
            $oItemEnum = \CIBlockPropertyEnum::GetList([], ["IBLOCK_ID" => $iIblockId, "CODE" => $sCode]);
            while ($arItemEnum = $oItemEnum->fetch()) {
                $arItems[$arItemEnum['ID']] = $arItemEnum['VALUE'];
            }
        }

        return $arItems;
    }

    /**
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