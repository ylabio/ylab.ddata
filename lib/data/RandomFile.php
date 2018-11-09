<?php

namespace Ylab\Ddata\Data;

use Bitrix\Main\DB\Exception;
use Bitrix\Main\Localization\Loc;
use Ylab\Ddata\Interfaces\DataUnitClass;
use Bitrix\Main\HttpRequest;
use Ylab\Ddata\Helpers;

Loc::loadMessages(__FILE__);

/**
 * Class RandomFile
 * @package Ylab\Ddata\Data
 */
class RandomFile extends DataUnitClass
{
    protected static $bCheckStaticMethod = true;
    protected $sPath;

    /**
     * RandomFile constructor.
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

        if (!empty($this->options['path'])) {

            $this->sPath = $_SERVER['DOCUMENT_ROOT'] . $this->options['path'] . "/";
        } else {

            $this->sPath = Helpers::getModulePath() . '\assets\\';
        }

        $this->sPath = realpath($this->sPath );
    }

    /**
     * @return array
     */
    public static function getDescription()
    {
        return [
            "ID" => "file.unit",
            "NAME" => Loc::getMessage("YLAB_DDATA_DATA_UNIT_FILE_NAME"),
            "DESCRIPTION" => Loc::getMessage('YLAB_DDATA_DATA_UNIT_FILE_DESCRIPTION'),
            "TYPE" => "file",
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
        include Helpers::getModulePath() . "/admin/fragments/random_file_settings_form.php";
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
        $sPath = $_SERVER['DOCUMENT_ROOT'] . $arPrepareRequest['path'] . "/";
        $bFlag = false;

        if (!file_exists($sPath)) {
            throw new Exception(Loc::getMessage("YLAB_DDATA_DATA_UNIT_FILE_ERROR_DIRECTORY"));
        } elseif (!glob($sPath . "*.*") && empty(glob($sPath . "*.*"))) {
            throw new Exception(Loc::getMessage("YLAB_DDATA_DATA_UNIT_FILE_ERROR_FILES"));
        } else {
            $bFlag = true;
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
            $arFiles = glob($this->sPath . "*.*");
            $sResult = array_rand($arFiles);
            $arFile = \CFile::MakeFileArray($arFiles[$sResult]);
            $iFileId = \CFile::SaveFile($arFile, "demo");

            if ($iFileId) {
                return \CFile::MakeFileArray($iFileId);
            }
        } else {
            throw new \Exception(Loc::getMessage('YLAB_DDATA_DATA_UNIT_FILE_EXCEPTION_STATIC'));
        }

        return '';
    }
}