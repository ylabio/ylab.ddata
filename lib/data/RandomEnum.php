<?

namespace Ylab\Ddata\Data;

use Bitrix\Main\HttpRequest;
use Bitrix\Main\Localization\Loc;
use Ylab\Ddata\Interfaces\DataUnitClass;
use Ylab\Ddata\Orm\EntityUnitProfileTable;
use Ylab\Ddata\Helpers;

/**
 * Class RandomEnum
 * @package Ylab\Ddata\Data
 */
class RandomEnum extends DataUnitClass
{
    /**
     * @var bool
     */
    private static $bCheckStaticMethod = true;
    protected $sRandom = 'Y';
    protected $iSelectedValue = 0;
    protected $arAllValues = [];

    /**
     * RandomEnum constructor.
     * @param $sProfileID
     * @param $sFieldCode
     * @param $sGeneratorID
     * @throws \Bitrix\Main\ArgumentException
     * @throws \Bitrix\Main\ObjectPropertyException
     * @throws \Bitrix\Main\SystemException
     */
    public function __construct($sProfileID, $sFieldCode, $sGeneratorID)
    {
        self::$bCheckStaticMethod = false;

        parent::__construct($sProfileID, $sFieldCode, $sGeneratorID);

        $objProfile = EntityUnitProfileTable::getList([
            'filter' => ['=ID' => $sProfileID]
        ]);

        $arProfile = $objProfile->fetch();
        if (!empty($arProfile)) {
            $arOptions = json_decode($arProfile['OPTIONS'], true);
            $sNamespace = $arOptions['namespace'];
            $arAllValues = $sNamespace::getMap();
            foreach ($arAllValues as $arAllValue) {
                $sName = $arAllValue->getName();
                if ($sName == $sFieldCode) {
                    $arValues = $arAllValue->getValues();
                }
            }
            $this->arAllValues = $arValues;
        }

        if (!empty($this->options['random'])) {
            $this->sRandom = $this->options['random'];
        }

        if (!empty($this->options['selected-value'])) {
            $this->iSelectedValue = $this->options['selected-value'];
        }
    }

    public static function getDescription()
    {
        return [
            "ID" => "enum.unit",
            "NAME" => Loc::getMessage("YLAB_DDATA_DATA_UNIT_ENUM_NAME"),
            "DESCRIPTION" => Loc::getMessage('YLAB_DDATA_DATA_UNIT_ENUM_DESCRIPTION'),
            "TYPE" => "enum",
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
        $sPropertyCode = $request->get('property-code');

        if (intval($sProfileID) > 0) {
            $objProfile = EntityUnitProfileTable::getList([
                'filter' => ['=ID' => $sProfileID]
            ]);

            $arProfile = $objProfile->fetch();
            if (!empty($arProfile)) {
                $arOptions = json_decode($arProfile['OPTIONS'], true);
                $sNamespace = $arOptions['namespace'];
                $arAllValues = $sNamespace::getMap();
                foreach ($arAllValues as $arAllValue) {
                    $sName = $arAllValue->getName();
                    if ($sName == $sPropertyCode) {
                        $arValues = $arAllValue->getValues();
                    }
                }
            }
        } else {
            $sNamespace = $arRequest['prepare']['namespace'];
            $arAllValues = $sNamespace::getMap();
            foreach ($arAllValues as $arAllValue) {
                $sName = $arAllValue->getName();
                if ($sName == $sPropertyCode) {
                    $arValues = $arAllValue->getValues();
                }
            }
        }
        $arOptions = array_merge(self::getOptions($sGeneratorID, $sProfileID, $sFieldID), $arOptions);

        ob_start();
        include Helpers::getModulePath() . '/admin/fragments/random_enum_settings_form.php';
        $tpl = ob_get_contents();
        ob_end_clean();

        return $tpl;
    }


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
            $arAllValues = $this->arAllValues;
            if ($this->sRandom == 'Y') {
                $sResult = array_rand($arAllValues);

                return $arAllValues[$sResult];
            } else {
                return $arAllValues[$this->iSelectedValue];
            }
        } else {
            throw new \Exception(Loc::getMessage('YLAB_DDATA_DATA_UNIT_ENUM_EXCEPTION_STATIC'));
        }
    }
}