<?php

namespace Ylab\Ddata\Data;

use Bitrix\Main\Localization\Loc;
use Bitrix\Main\HttpRequest;
use Ylab\Ddata\Interfaces\DataUnitClass;
use Ylab\Ddata\Helpers;

Loc::loadMessages(__FILE__);

/**
 * Class RandomPassword
 * @package Ylab\Ddata\Data
 */
class RandomPassword extends DataUnitClass
{
    private static $bCheckStaticMethod = true;

    protected $sSpecialChars = 'N';
    protected $iPasswordMinLength = 6;
    protected $iPasswordMaxLength = 12;

    /**
     * RandomPassword constructor.
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

        if (!empty($this->options['special-chars'])) {
            $this->sSpecialChars = $this->options['special-chars'];
        }

        if (!empty($this->options['password-min-length'])) {
            $this->iPasswordMinLength = $this->options['password-min-length'];
        }

        if (!empty($this->options['password-max-length'])) {
            $this->iPasswordMaxLength = $this->options['password-max-length'];
        }
    }

    /**
     * @return array
     */
    public static function getDescription()
    {
        return [
            "ID" => "password.unit",
            "NAME" => Loc::getMessage("YLAB_DDATA_DATA_UNIT_PASSWORD_NAME"),
            "DESCRIPTION" => Loc::getMessage('YLAB_DDATA_DATA_UNIT_PASSWORD_DESCRIPTION'),
            "TYPE" => "string",
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

        $arOptions = array_merge(self::getOptions($sGeneratorID, $sProfileID, $sFieldID), $arOptions);

        ob_start();
        include Helpers::getModulePath() . '/admin/fragments/random_password_settings_form.php';
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
            $sSpecialChars = $arPrepareRequest['special-chars'];
            $iPasswordMinLength = (int)$arPrepareRequest['password-min-length'];
            $iPasswordMaxLength = (int)$arPrepareRequest['password-max-length'];

            if (!empty($sSpecialChars) && ($iPasswordMaxLength > $iPasswordMinLength) && $iPasswordMinLength > 5) {
                return true;
            }
        }

        return false;
    }

    /**
     * @return string
     * @throws \Exception
     */
    public function getValue()
    {
        if (!self::$bCheckStaticMethod) {

            $sSymbols = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ";
            if ($this->sSpecialChars === 'Y') {
                $sSymbols .= "!#$%&'*+-/=?^_`{|}~";
            }

            $sSymbols = str_shuffle($sSymbols);
            $iSize = strlen($sSymbols) - 1;
            $iStringLength = rand($this->iPasswordMinLength, $this->iPasswordMaxLength);
            $sResultPassword = '';

            while (strlen($sResultPassword) < $iStringLength) {
                $sResultPassword .= mb_substr($sSymbols, rand(0, $iSize - 1), 1);
            }

            return $sResultPassword;
        } else {
            throw new \Exception(Loc::getMessage('YLAB_DDATA_DATA_UNIT_PASSWORD_EXCEPTION_STATIC'));
        }
    }
}