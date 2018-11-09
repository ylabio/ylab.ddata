<?

namespace Ylab\Ddata\data;

use Bitrix\Main\HttpRequest;
use Bitrix\Main\Web\Json;
use CCurrency;
use Ylab\Ddata\interfaces\DataUnitClass;
use Bitrix\Main\Localization\Loc;
use Ylab\Ddata\Helpers;
use Bitrix\Main\Loader;
use Ylab\Ddata\Orm\EntityUnitProfileTable;

/**
 * Class RandomCurrency
 * @package Ylab\Ddata\data
 */
class RandomCurrency extends DataUnitClass
{
    private static $bCheckStaticMethod = true;

    protected $sRandom = 'N';
    protected $sSelectedValue = '';
    protected $arCurrency = [];

    /**
     * RandomCurrency constructor.
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
        self::$bCheckStaticMethod = false;

        parent::__construct($sProfileID, $sFieldCode, $sGeneratorID);

        $arCurrency = self::getCurrency();

        if (!empty($arCurrency)) {
            $this->arCurrency = $arCurrency;
        }

        if (!empty($this->options['random'])) {
            $this->sRandom = $this->options['random'];
        }

        if (!empty($this->options['selected-value'])) {
            $this->sSelectedValue = $this->options['selected-value'];
        } else {
            $this->sSelectedValue = self::getBaseCurrency();
        }
    }

    /**
     * Метод возврящает массив описывающий тип данных. ID, Имя, scalar type php
     *
     * @return array
     */
    public static function getDescription()
    {
        return [
            "ID" => "currency.unit",
            "NAME" => Loc::getMessage("YLAB_DDATA_DATA_UNIT_CURRENCY_NAME"),
            "DESCRIPTION" => Loc::getMessage("YLAB_DDATA_DATA_UNIT_CURRENCY_DESCRIPTION"),
            "TYPE" => "currency",
            "CLASS" => __CLASS__
        ];
    }

    /**
     * Метод возвращает html строку формы с настройкой генератора если таковые необходимы
     *
     * @param HttpRequest $request
     * @return mixed
     * @throws \Bitrix\Main\ArgumentException
     * @throws \Bitrix\Main\ObjectPropertyException
     * @throws \Bitrix\Main\SystemException
     * @throws \Bitrix\Main\LoaderException
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

        $arOptions = array_merge(self::getOptions($sGeneratorID, $sProfileID, $sFieldID), $arOptions);
        $arCurrency = static::getCurrency();

        ob_start();
        include Helpers::getModulePath() . '/admin/fragments/random_currency_settings_form.php';
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
            $sSelectedValue = $arPrepareRequest['selected-value'];

            if (!empty($sRandom) || !empty($arSelectedSections)) {
                return true;
            }
        }
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
            if ($this->sRandom === 'Y') {
                if ($this->arCurrency) {
                    $arCurrency = array_keys($this->arCurrency);
                    return array_rand($arCurrency);
                }
            } else {
                if ($this->sSelectedValue) {

                    return $this->sSelectedValue;
                }
            }

            return '';
        } else {
            throw new \Exception(Loc::getMessage('YLAB_DDATA_DATA_IBLOCK_SECTION_EXCEPTION_STATIC'));
        }
    }

    /**
     * Метод для получения базовой валюты
     * @return string
     * @throws \Bitrix\Main\LoaderException
     */
    public function getBaseCurrency()
    {
        Loader::includeModule('iblock');
        Loader::includeModule('catalog');

        $sResult = \Bitrix\Currency\CurrencyManager::getBaseCurrency();

        return $sResult;
    }

    /**
     * Метод для получения всех доступных валют
     * @return array
     * @throws \Bitrix\Main\LoaderException
     */
    public function getCurrency()
    {
        Loader::includeModule('catalog');

        $arResult = [];
        $oCurrency = CCurrency::GetList(($by = "name"), ($order = "asc"), LANGUAGE_ID);
        while ($arCurrency = $oCurrency->Fetch()) {
            $arResult[$arCurrency['CURRENCY']] = $arCurrency['FULL_NAME'];
        }

        return $arResult;
    }
}