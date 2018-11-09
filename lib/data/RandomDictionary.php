<?php

namespace Ylab\Ddata\data;

use Bitrix\Main\DB\Exception;
use Bitrix\Main\Localization\Loc;
use Ylab\Ddata\Helpers;
use Ylab\Ddata\interfaces\DataUnitClass;
use Bitrix\Main\HttpRequest;

Loc::loadMessages(__FILE__);

/**
 * Class RandomDictionary
 * @package Ylab\Ddata\data
 */
class RandomDictionary extends DataUnitClass
{
    /**
     * @var bool
     */
    protected static $bCheckStaticMethod = true;

    public $sDictionaryPath;
    public $sMethod = 'RANDOM';
    public $arDictionaryElements;
    public $iCounter = 0;

    /**
     * File constructor.
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
     * @return array
     */
    public static function getDescription()
    {
        return [
            "ID" => "string.dictionary.unit",
            "NAME" => Loc::getMessage("YLAB_DDATA_DATA_UNIT_DICTIONARY_NAME"),
            "DESCRIPTION" => Loc::getMessage('YLAB_DDATA_DATA_UNIT_DICTIONARY_DESCRIPTION'),
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
        include Helpers::getModulePath() . "/admin/fragments/random_dictionary_settings_form.php";
        $tpl = ob_get_contents();
        ob_end_clean();

        return $tpl;
    }

    /**
     * @param HttpRequest $request
     * @return bool
     * @throws Exception
     */
    public static function isValidateOptions(HttpRequest $request)
    {
        $arPrepareRequest = $request->get('option');
        $sPath = $_SERVER['DOCUMENT_ROOT'] . $arPrepareRequest['path'];
        $bFlag = false;

        if (file_exists($sPath)) {
            $handle = fopen($sPath, "r");
            if (fgets($handle, 4096) !== false) {
                $bFlag = true;
            } else {
                throw new Exception(Loc::getMessage("YLAB_DDATA_DATA_UNIT_DICTIONARY_ERROR_FILE_EMPTY"));
            }
        } else {
            throw new Exception(Loc::getMessage("YLAB_DDATA_DATA_UNIT_DICTIONARY_ERROR_FILE"));
        }

        return $bFlag;
    }

    /**
     * @return mixed
     * @throws \Exception
     */
    public function getValue()
    {
        if (!self::$bCheckStaticMethod) {
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
        } else {
            throw new \Exception(Loc::getMessage('YLAB_DDATA_DATA_UNIT_DICTIONARY_EXCEPTION_STATIC'));
        }

        return [];
    }
}