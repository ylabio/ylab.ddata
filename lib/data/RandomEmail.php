<?php

namespace Ylab\Ddata\Data;

use Bitrix\Main\Localization\Loc;
use Ylab\Ddata\Interfaces\DataUnitClass;
use Bitrix\Main\HttpRequest;
use Ylab\Ddata\Helpers;

Loc::loadMessages(__FILE__);

/**
 * Class RandomEmail
 * @package Ylab\Ddata\Data
 */
class RandomEmail extends DataUnitClass
{
    private static $bCheckStaticMethod = true;

    protected $sSpecialChars = 'N';
    protected $iLoginMinLength = 6;
    protected $iLoginMaxLength = 12;
    protected $sDomains = 'yandex|mail|gmail';
    protected $sSubDomains = 'ru|com';

    /**
     * Email constructor.
     * @param $sProfileID
     * @param $sFieldCode
     * @param $sGeneratorID
     * @throws \Bitrix\Main\ArgumentException
     */
    public function __construct($sProfileID, $sFieldCode, $sGeneratorID)
    {
        self::$bCheckStaticMethod = false;

        parent::__construct($sProfileID, $sFieldCode, $sGeneratorID);

        if (!empty($this->options['special-chars'])) {
            $this->sSpecialChars = $this->options['special-chars'];
        }

        if (!empty($this->options['login-min-length'])) {
            $this->iLoginMinLength = $this->options['login-min-length'];
        }

        if (!empty($this->options['login-max-length'])) {
            $this->iLoginMaxLength = $this->options['login-max-length'];
        }

        if (!empty($this->options['domains'])) {
            $this->sDomains = $this->options['domains'];
        }

        if (!empty($this->options['sub-domains'])) {
            $this->sSubDomains = $this->options['sub-domains'];
        }
    }

    /**
     * @return array
     */
    public static function getDescription()
    {
        return [
            "ID" => "email.string.unit",
            "NAME" => Loc::getMessage("YLAB_DDATA_DATA_UNIT_EMAIL_NAME"),
            "DESCRIPTION" => Loc::getMessage('YLAB_DDATA_DATA_UNIT_EMAIL_DESCRIPTION'),
            "TYPE" => "string",
            "CLASS" => __CLASS__
        ];
    }

    /**
     * @param HttpRequest $request
     * @return string
     * @throws \Bitrix\Main\ArgumentException
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

        ob_start();
        include Helpers::getModulePath() . '/admin/fragments/random_email_settings_form.php';
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
            $sDomains = $arPrepareRequest['domains'];
            $sSubDomains = $arPrepareRequest['sub-domains'];

            $iLoginMinLength = (int) $arPrepareRequest['login-min-length'];
            $iLoginMaxLength = (int) $arPrepareRequest['login-max-length'];

            if (!empty($sSpecialChars) &&
                !empty($sDomains) &&
                !empty($sSubDomains) &&
                $iLoginMaxLength > $iLoginMinLength
            ) {
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

            $iStringLength = rand($this->iLoginMinLength, $this->iLoginMaxLength);
            $sResultName = '';
            while (strlen($sResultName) < $iStringLength) {
                $sResultName .= mb_substr($sSymbols, rand(0, $iSize - 1), 1);
            }

            $sResultDomain = '';
            $arDomains = array_diff(explode("|", $this->sDomains), ['']);
            if (count($arDomains) > 0) {
                shuffle($arDomains);
            }
            $sResultDomain = $arDomains[0];

            $sResultSubDomains = '';
            $arSubDomains = array_diff(explode("|", $this->sSubDomains), ['']);
            if (count($arSubDomains) > 0) {
                shuffle($arSubDomains);
            }
            $sResultSubDomains = $arSubDomains[0];

            if ($sResultName && $sResultDomain && $sResultSubDomains) {
                return strtolower($sResultName . "@" . $sResultDomain . "." . $sResultSubDomains);
            }
        } else {
            throw new \Exception(Loc::getMessage('YLAB_DDATA_DATA_UNIT_PASSWORD_EXCEPTION_STATIC'));
        }
    }
}