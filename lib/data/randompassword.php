<?php

namespace Ylab\Ddata\Data;

use Bitrix\Main\Localization\Loc;
use Bitrix\Main\HttpRequest;
use Ylab\Ddata\Interfaces\DataUnitClass;
use Ylab\Ddata\Helpers;

Loc::loadMessages(__FILE__);

/**
 * Генерация случайного пароля
 *
 * Class RandomPassword
 * @package Ylab\Ddata\Data
 */
class RandomPassword extends DataUnitClass
{
    protected $sSpecialChars = 'N';

    /** @var int $iPasswordMinLength Минимальная длина по умолчанию */
    protected $iPasswordMinLength = 6;

    /** @var int $iPasswordMaxLength Максимальная длина по умолчанию */
    protected $iPasswordMaxLength = 12;

    /**
     * RandomPassword constructor.
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

        if (!empty($this->options['password-min-length'])) {
            $this->iPasswordMinLength = $this->options['password-min-length'];
        }

        if (!empty($this->options['password-max-length'])) {
            $this->iPasswordMaxLength = $this->options['password-max-length'];
        }
    }

    /**
     * Метод возврящает массив описывающий тип данных. ID, Имя, scalar type php
     *
     * @return array
     */
    public function getDescription()
    {
        return [
            'ID' => 'password.unit',
            'NAME' => Loc::getMessage('YLAB_DDATA_DATA_UNIT_PASSWORD_NAME'),
            'DESCRIPTION' => Loc::getMessage('YLAB_DDATA_DATA_UNIT_PASSWORD_DESCRIPTION'),
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
        include Helpers::getModulePath() . '/admin/fragments/random_password_settings_form.php';
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
            $iPasswordMinLength = (int)$arPrepareRequest['password-min-length'];
            $iPasswordMaxLength = (int)$arPrepareRequest['password-max-length'];

            if (!empty($sSpecialChars) && ($iPasswordMaxLength > $iPasswordMinLength) && $iPasswordMinLength > 5) {
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
        $iStringLength = rand($this->iPasswordMinLength, $this->iPasswordMaxLength);
        $sResultPassword = '';

        while (strlen($sResultPassword) < $iStringLength) {
            $sResultPassword .= mb_substr($sSymbols, rand(0, $iSize - 1), 1);
        }

        return $sResultPassword;
    }
}