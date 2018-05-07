<?

namespace Ylab\Ddata\data;

use Bitrix\Main\HttpRequest;
use Bitrix\Main\Loader;
use Bitrix\Main\Localization\Loc;
use Ylab\Ddata\interfaces\DataUnitClass;
use Ylab\Ddata\Helpers;

/**
 * Class RandomEnumHL
 * @package Ylab\Ddata\data
 */
class RandomEnumHL extends DataUnitClass
{
    /**
     * @var bool
     */
    private static $bCheckStaticMethod = true;
    protected $sRandom = 'N';
    protected $iSelectedValue = 0;
    protected $arAllValues = [];

    /**
     * RandomEnumHL constructor.
     * @param $sProfileID
     * @param $sFieldCode
     * @param $sGeneratorID
     * @throws \Bitrix\Main\ArgumentException
     */
    public function __construct($sProfileID, $sFieldCode, $sGeneratorID)
    {
        self::$bCheckStaticMethod = false;

        parent::__construct($sProfileID, $sFieldCode, $sGeneratorID);

        if (!empty($this->options['random'])) {
            $this->sRandom = $this->options['random'];
        }

        if (!empty($this->options['selected-value'])) {
            $this->iSelectedValue = $this->options['selected-value'];
        }

        $oEnum = new \CUserFieldEnum;
        $rsEnum = $oEnum->GetList([], ['USER_FIELD_NAME' => $sFieldCode]);
        while ($arEnum = $rsEnum->GetNext()) {
            $this->arAllValues[] = $arEnum['ID'];
        }
    }

    /**
     * Метод возвращает массив описывающий тип данных. ID, Имя, scalar type php
     *
     * @return array
     */
    public static function getDescription()
    {
        return [
            "ID" => "enum.hl.unit",
            "NAME" => Loc::getMessage("YLAB_DDATA_DATA_UNIT_ENUM_HL_NAME"),
            "DESCRIPTION" => Loc::getMessage('YLAB_DDATA_DATA_UNIT_ENUM_HL_DESCRIPTION'),
            "TYPE" => "enum.hl",
            "CLASS" => __CLASS__
        ];
    }

    /**
     * Метод возвращает html строку формы с настройкой генератора если таковые необходимы
     *
     * @param HttpRequest $request
     * @return mixed
     * @throws \Bitrix\Main\ArgumentException
     * @throws \Bitrix\Main\LoaderException
     */
    public static function getOptionForm(HttpRequest $request)
    {
        Loader::includeModule('highloadblock');

        $arRequest = $request->toArray();
        $arOptions = $arRequest['option'];
        $sGeneratorID = $request->get('generator');
        $sFieldID = $request->get('field');
        $sProfileID = $request->get('profile_id');
        $sPropertyName = $request->get('property-name');
        $sPropertyCode = $request->get('property-code');

        $arOptions = array_merge(self::getOptions($sGeneratorID, $sProfileID, $sFieldID), $arOptions);

        $oEnum = new \CUserFieldEnum;
        $rsEnum = $oEnum->GetList([], ['USER_FIELD_NAME' => $sPropertyCode]);
        while ($arEnum = $rsEnum->GetNext()) {
            $arValues[] = $arEnum['VALUE'];
        }

        ob_start();
        include Helpers::getModulePath() . '/admin/fragments/random_enum_settings_form.php';
        $tpl = ob_get_contents();
        ob_end_clean();

        return $tpl;
    }

    /**
     * Метод проверяет на валидность данные настройки генератора
     *
     * @param HttpRequest $request
     * @return mixed
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
     * Возвращает случайную запись соответствующего типа
     *
     * @return mixed
     * @throws \Exception
     */
    public function getValue()
    {
        if (!self::$bCheckStaticMethod) {
            $arAllValues = $this->arAllValues;
            if ($this->sRandom == 'Y') {
                $iResult = array_rand($arAllValues);

                return $arAllValues[$iResult];
            } else {
                return $arAllValues[$this->iSelectedValue];
            }
        } else {
            throw new \Exception(Loc::getMessage('YLAB_DDATA_DATA_UNIT_ENUM_HL_EXCEPTION_STATIC'));
        }
    }
}