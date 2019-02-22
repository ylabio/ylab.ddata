<?php

namespace Ylab\Ddata\Data;

use Bitrix\Main\Localization\Loc;
use Ylab\Ddata\Interfaces\DataUnitClass;
use Bitrix\Main\HttpRequest;
use Ylab\Ddata\Helpers;

Loc::loadMessages(__FILE__);

/**
 * Генерация случайного e-mail
 *
 * Class RandomEmail
 * @package Ylab\Ddata\Data
 */
class RandomEmail extends DataUnitClass
{
    protected $sSpecialChars = 'N';

    /** @var int $iLoginMinLength Минимальная длина имени email`а */
    protected $iLoginMinLength = 6;

    /** @var int $iLoginMaxLength Максимальная длина имени email`а */
    protected $iLoginMaxLength = 12;

    /** @var string $sDomains Список доступных доменов */
    protected $sDomains = 'yandex|mail|gmail';

    /** @var string $sSubDomains Список доступных доменных зон */
    protected $sSubDomains = 'ru|com';

    /**
     * RandomEmail constructor.
     * @param $sProfileID - ID профиля
     * @param $sFieldCode - Симфольный код свойства
     * @param $sGeneratorID - ID уже сохраненного генератора
     * @throws \Bitrix\Main\ArgumentException
     * @throws \Bitrix\Main\ObjectPropertyException
     * @throws \Bitrix\Main\SystemException
     */
    public function __construct(string $sProfileID = '', string $sFieldCode = '', string $sGeneratorID = '')
    {
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
     * Метод возврящает массив описывающий тип данных. ID, Имя, scalar type php
     *
     * @return array
     */
    public  function getDescription()
    {
        return [
            'ID' => 'email.string.unit',
            'NAME' => Loc::getMessage('YLAB_DDATA_DATA_UNIT_EMAIL_NAME'),
            'DESCRIPTION' => Loc::getMessage('YLAB_DDATA_DATA_UNIT_EMAIL_DESCRIPTION'),
            'TYPE' => 'string',
            'CLASS' => __CLASS__
        ];
    }

    /**
     * Метод возвращает html строку формы с настройкой генератора если таковые необходимы
     *
     * @param HttpRequest $request
     * @return false|mixed|string
     */
    public function getOptionForm(HttpRequest $request)
    {
        $sGeneratorID = $request->get('generator');
        $sProfileID = $request->get('profile_id');
        $sPropertyName = $request->get('property-name');
        $sPropertyCode = $request->get('property-code');

        ob_start();
        include Helpers::getModulePath() . '/admin/fragments/random_email_settings_form.php';
        $tpl = ob_get_contents();
        ob_end_clean();

        return $tpl;
    }

    /**
     * Метод проверяет на валидность данные настройки генератора
     *
     * @param HttpRequest $request
     * @return bool
     */
    public  function isValidateOptions(HttpRequest $request)
    {
        $arPrepareRequest = $request->get('option');

        if ($arPrepareRequest) {
            $sSpecialChars = $arPrepareRequest['special-chars'];
            $sDomains = $arPrepareRequest['domains'];
            $sSubDomains = $arPrepareRequest['sub-domains'];

            $iLoginMinLength = (int)$arPrepareRequest['login-min-length'];
            $iLoginMaxLength = (int)$arPrepareRequest['login-max-length'];

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
     * Возвращает случайную запись соответствующего типа
     *
     * @return string
     * @throws \Exception
     */
    public function getValue()
    {

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

        return '';
    }
}