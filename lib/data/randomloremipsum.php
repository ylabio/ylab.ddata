<?php

namespace Ylab\Ddata\Data;

use Bitrix\Main\Localization\Loc;
use Bitrix\Main\HttpRequest;
use Ylab\Ddata\Interfaces\DataUnitClass;
use Ylab\Ddata\Helpers;
use Ylab\Ddata\LoremIpsum;

Loc::loadLanguageFile(__FILE__);

/**
 * Генерация текста Lorem Ipsum
 *
 * Class RandomLoremIpsum
 * @package Ylab\Ddata\Data
 */
class RandomLoremIpsum extends DataUnitClass
{
    protected $sGenerate = "WORDS";

    /** @var int $iCount Количество элементов для генерации*/
    protected $iCount = 1;

    /** @var string $sHtmlWrap */
    protected $sHtmlWrap;

    /** @var object $oIpsum */
    protected $oIpsum;

    /**
     * RandomLoremIpsum constructor.
     * @param $sProfileID - ID профиля
     * @param $sFieldCode - Симфольный код свойства
     * @param $sGeneratorID - ID уже сохраненного генератораD
     * @throws \Bitrix\Main\ArgumentException
     * @throws \Bitrix\Main\ObjectPropertyException
     * @throws \Bitrix\Main\SystemException
     */
    public function __construct(string $sProfileID = '', string $sFieldCode = '', string $sGeneratorID = '')
    {
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
     * Метод возврящает массив описывающий тип данных. ID, Имя, scalar type php
     *
     * @return array
     */
    public  function getDescription()
    {
        return [
            'ID' => 'random.loremipsum.unit',
            'NAME' => Loc::getMessage('YLAB_DDATA_DATA_UNIT_LOREM_IPSUM_NAME'),
            'DESCRIPTION' => Loc::getMessage('YLAB_DDATA_DATA_UNIT_LOREM_IPSUM_DESCRIPTION'),
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
        include Helpers::getModulePath() . '/admin/fragments/random_lorem_ipsum_settings_form.php';
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

        if ($arPrepareRequest['count'] > 0) {
            return true;
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
        $sGenerate = $this->sGenerate;
        $iCount = $this->iCount;
        $sHtmlWrap = $this->sHtmlWrap;
        $oIpsum = $this->oIpsum;
        $sResult = '';

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
    }
}