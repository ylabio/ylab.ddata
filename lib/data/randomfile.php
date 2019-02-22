<?php

namespace Ylab\Ddata\Data;

use Bitrix\Main\DB\Exception;
use Bitrix\Main\Localization\Loc;
use Ylab\Ddata\Interfaces\DataUnitClass;
use Bitrix\Main\HttpRequest;
use Ylab\Ddata\Helpers;

Loc::loadMessages(__FILE__);

/**
 * Генерация случайного файла
 *
 * Class RandomFile
 * @package Ylab\Ddata\Data
 */
class RandomFile extends DataUnitClass
{
    protected $sPath;

    /**
     * RandomFile constructor.
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

        if (!empty($this->options['path'])) {

            $this->sPath = $_SERVER['DOCUMENT_ROOT'] . $this->options['path'] . "/";
        } else {

            $this->sPath = Helpers::getModulePath() . '\assets\\';
        }

        $this->sPath = realpath($this->sPath);
    }

    /**
     * Метод возврящает массив описывающий тип данных. ID, Имя, scalar type php
     *
     * @return array
     */
    public  function getDescription()
    {
        return [
            'ID' => 'file.unit',
            'NAME' => Loc::getMessage('YLAB_DDATA_DATA_UNIT_FILE_NAME'),
            'DESCRIPTION' => Loc::getMessage('YLAB_DDATA_DATA_UNIT_FILE_DESCRIPTION'),
            'TYPE' => 'file',
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
        include Helpers::getModulePath() . '/admin/fragments/random_file_settings_form.php';
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
        $sPath = $_SERVER['DOCUMENT_ROOT'] . $arPrepareRequest['path'] . "/";
        $bFlag = false;

        if (!file_exists($sPath)) {
            throw new Exception(Loc::getMessage('YLAB_DDATA_DATA_UNIT_FILE_ERROR_DIRECTORY'));
        }

        if (!glob($sPath . '*.*') && empty(glob($sPath . '*.*'))) {
            throw new Exception(Loc::getMessage('YLAB_DDATA_DATA_UNIT_FILE_ERROR_FILES'));
        }

        $bFlag = true;

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
        $arFiles = glob($this->sPath . "*.*");
        $sResult = array_rand($arFiles);
        $arFile = \CFile::MakeFileArray($arFiles[$sResult]);
        $iFileId = \CFile::SaveFile($arFile, "demo");

        if ($iFileId) {
            return \CFile::MakeFileArray($iFileId);
        }

        return '';
    }
}