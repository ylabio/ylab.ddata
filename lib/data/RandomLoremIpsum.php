<?php

namespace Ylab\Ddata\Data;

use Bitrix\Main\Localization\Loc;
use Bitrix\Main\HttpRequest;
use Ylab\Ddata\Interfaces\DataUnitClass;
use Ylab\Ddata\Helpers;
use Ylab\Ddata\LoremIpsum;

Loc::loadLanguageFile(__FILE__);

/**
 * Class RandomLoremIpsum
 * @package Ylab\Ddata\Data
 */
class RandomLoremIpsum extends DataUnitClass
{
    private static $bCheckStaticMethod = true;

    protected $sGenerate = "WORDS";
    protected $iCount = 1;
    protected $sHtmlWrap;
    protected $oIpsum;

    /**
     * RandomLoremIpsum constructor.
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

        if (!empty($this->options['generate'])) {
            $this->sGenerate = $this->options['generate'];
        }

        if (!empty($this->options['count'])) {
            $this->iCount = $this->options['count'];
        }

        if (!empty($this->options['wrap'])) {
            $this->sHtmlWrap = $this->options['wrap'];
        }

        $this->oIpsum = new LoremIpsum();
    }

    /**
     * @return array
     */
    public static function getDescription()
    {
        return [
            "ID" => "random.loremipsum.unit",
            "NAME" => Loc::getMessage('YLAB_DDATA_DATA_UNIT_LOREM_IPSUM_NAME'),
            "DESCRIPTION" => Loc::getMessage('YLAB_DDATA_DATA_UNIT_LOREM_IPSUM_DESCRIPTION'),
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
        $arOptions = $arRequest['option'];
        $sGeneratorID = $request->get('generator');
        $sFieldID = $request->get('field');
        $sProfileID = $request->get('profile_id');
        $sPropertyName = $request->get('property-name');

        $arOptions = array_merge(self::getOptions($sGeneratorID, $sProfileID, $sFieldID), $arOptions);
        ob_start();
        include Helpers::getModulePath() . "/admin/fragments/random_lorem_ipsum_settings_form.php";
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
        $bFlag = false;

        if ($arPrepareRequest['count'] > 0) {
            $bFlag = true;
        } else {
            return false;
        }


        return $bFlag;
    }

    /**
     * @return string
     * @throws \Exception
     */
    public function getValue()
    {
        $sGenerate = $this->sGenerate;
        $iCount = $this->iCount;
        $sHtmlWrap = $this->sHtmlWrap;
        $oIpsum = $this->oIpsum;

        if (!self::$bCheckStaticMethod) {
            switch ($sGenerate) {
                case "WORDS":
                    if ($sHtmlWrap) {
                        $sResult = $oIpsum->words($iCount, $sHtmlWrap);
                    } else {
                        $sResult = $oIpsum->words($iCount);
                    }
                    break;
                case "SENTENCES":
                    if ($sHtmlWrap) {
                        $sResult = $oIpsum->sentences($iCount, $sHtmlWrap);
                    } else {
                        $sResult = $oIpsum->sentences($iCount);
                    }
                    break;
                case "PARAGRAPHS":
                    if ($sHtmlWrap) {
                        $sResult = $oIpsum->paragraphs($iCount, $sHtmlWrap);
                    } else {
                        $sResult = $oIpsum->paragraphs($iCount);
                    }
                    break;
            }

            return $sResult;
        } else {
            throw new \Exception(Loc::getMessage('YLAB_DDATA_DATA_UNIT_LOREM_IPSUM_EXCEPTION_STATIC'));
        }
    }
}