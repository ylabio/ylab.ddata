<?php

namespace Ylab\Ddata\data;

use Bitrix\Main\DB\Exception;
use Bitrix\Main\Localization\Loc;
use Ylab\Ddata\Helpers;
use Ylab\Ddata\interfaces\DataUnitClass;
use Bitrix\Main\HttpRequest;

Loc::loadMessages(__FILE__);

/**
 * Генерация строки из справочника
 *
 * Class RandomDictionary
 * @package Ylab\Ddata\data
 */
class RandomDictionary extends DataUnitClass
{
    public $sDictionaryPath;

    /** @var string $sMethod Метод заполнения */
    public $sMethod = 'RANDOM';

    /** @var array $arDictionaryElements */
    public $arDictionaryElements;

    /** @var int $iCounter */
    public $iCounter = 0;

    /**
     * File constructor.
     * @param $sProfileID - ID профиля
     * @param $sFieldCode - Симфольный код свойства
     * @param $sGeneratorID - ID уже сохраненного генератора
     * @throws \Bitrix\Main\ArgumentException
     * @throws \Exception
     */
    public function __construct(string $sProfileID = '', string $sFieldCode = '', string $sGeneratorID = '')
    {
        parent::__construct($sProfileID, $sFieldCode, $sGeneratorID);

        if (!empty($this->options['path'])) {
            $this->sDictionaryPath = $_SERVER['DOCUMENT_ROOT'] . $this->options['path'];
        } else {
            $this->sDictionaryPath = Helpers::getModulePath() . '/assets/dictionary/ylab.dictionary.txt';
        }
        if (!empty($this->options['method'])) {
            $this->sMethod = $this->options['method'];
        }

        $this->sDictionaryPath = realpath($this->sDictionaryPath);

        if (!empty($this->sDictionaryPath)) {
            $this->arDictionaryElements = Helpers::parseFile($this->sDictionaryPath);
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
            'ID' => 'string.dictionary.unit',
            'NAME' => Loc::getMessage('YLAB_DDATA_DATA_UNIT_DICTIONARY_NAME'),
            'DESCRIPTION' => Loc::getMessage('YLAB_DDATA_DATA_UNIT_DICTIONARY_DESCRIPTION'),
            'TYPE' => 'string',
            'CLASS' => __CLASS__
        ];
    }

    /**
     * Метод возвращает html строку формы с настройкой генератора если таковые необходимы
     *
     * @param HttpRequest $request
     * @return mixed|string
     */
    public function getOptionForm(HttpRequest $request)
    {
        $sGeneratorID = $request->get('generator');
        $sProfileID = $request->get('profile_id');
        $sPropertyName = $request->get('property-name');
        $sPropertyCode = $request->get('property-code');
        $arOptions = $request->get('option');
        if ($arOptions['path']) {
            $this->sDictionaryPath = $_SERVER['DOCUMENT_ROOT'] . $arOptions['path'];
        } elseif ($request->get('path')) {
            $this->sDictionaryPath = $request->get('path');
        }

        ob_start();
        include Helpers::getModulePath() . '/admin/fragments/random_dictionary_settings_form.php';
        $tpl = ob_get_contents();
        ob_end_clean();

        return $tpl;
    }

    /**
     * Метод проверяет на валидность данные настройки генератора
     *
     * @param HttpRequest $request
     * @return bool
     * @throws Exception
     */
    public  function isValidateOptions(HttpRequest $request)
    {
        $arPrepareRequest = $request->get('option');
        $sPath = $_SERVER['DOCUMENT_ROOT'] . $arPrepareRequest['path'];
        $bFlag = false;

        if (file_exists($sPath)) {
            $handle = fopen($sPath, 'r');
            if (fgets($handle, 4096) !== false) {
                $bFlag = true;
            } else {
                throw new Exception(Loc::getMessage('YLAB_DDATA_DATA_UNIT_DICTIONARY_ERROR_FILE_EMPTY'));
            }
        } else {
            throw new Exception(Loc::getMessage('YLAB_DDATA_DATA_UNIT_DICTIONARY_ERROR_FILE'));
        }

        return $bFlag;
    }

    /**
     * Возвращает случайную запись соответствующего типа
     *
     * @return mixed
     * @throws \Exception
     */
    public function getValue()
    {
        $sMethod = $this->sMethod;
        $arDictionaryElements = $this->arDictionaryElements;
        if (!empty($arDictionaryElements)) {
            if ($sMethod == 'RANDOM') {
                $iResult = array_rand($arDictionaryElements);
                $arResult = $arDictionaryElements[$iResult];

                return $arResult;
            } else {
                $arResult = $arDictionaryElements[$this->iCounter];
                if (($this->iCounter + 1) === count($arDictionaryElements)) {
                    $this->iCounter = 0;
                } else {
                    $this->iCounter++;
                }

                return $arResult;
            }
        }

        return [];
    }
}