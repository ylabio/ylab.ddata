<?php

namespace Ylab\Ddata\Data;

use Bitrix\Main\Localization\Loc;
use Ylab\Ddata\Interfaces\DataUnitClass;
use Bitrix\Main\HttpRequest;
use Ylab\Ddata\Helpers;
use \Bitrix\Main\GroupTable;

Loc::loadMessages(__FILE__);

/**
 * Генерация пользовательской группы
 *
 * Class RandomUserGroup
 * @package Ylab\Ddata\Data
 */
class RandomUserGroup extends DataUnitClass
{
    /** @var int iAdminGroup Группа администраторов */
    const iAdminGroup = 1;

    protected $sRandom = 'N';

    /** @var array $arSelectedGroups Предустановленные группы */
    protected $arSelectedGroups = [];

    /**
     * RandomUserGroup constructor.
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

        if (!empty($this->options['random'])) {
            $this->sRandom = $this->options['random'];
        }

        if (!empty($this->options['selected-groups'])) {
            $this->arSelectedGroups = $this->options['selected-groups'];
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
            'ID' => 'user.group',
            'NAME' => Loc::getMessage('YLAB_DDATA_DATA_USER_GROUP_NAME'),
            'DESCRIPTION' => Loc::getMessage('YLAB_DDATA_DATA_USER_GROUP_DESCRIPTION'),
            'TYPE' => 'user.group',
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
        $arGroupList = GroupTable::getList([
            'select' => [
                'ID',
                'NAME'
            ]
        ])->fetchAll();

        ob_start();
        include Helpers::getModulePath() . '/admin/fragments/random_user_group _settings_form.php';
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
    public function isValidateOptions(HttpRequest $request)
    {
        $arPrepareRequest = $request->get('option');

        if ($arPrepareRequest) {
            $sRandom = $arPrepareRequest['random'];
            $arSelectedGroups = $arPrepareRequest['selected-groups'];

            if (!empty($sRandom) || (!empty($arSelectedGroups) && is_array($arSelectedGroups))) {
                return true;
            }
        }

        return false;
    }

    /**
     * Возвращает случайную запись соответствующего типа
     *
     * @return array
     * @throws \Exception
     */
    public function getValue()
    {
        if (empty($this->arSelectedGroups)) {
            $this->arSelectedGroups = [RandomUserGroup::iAdminGroup];
        }
        if ($this->sRandom === 'Y') {
            $arGroupList = GroupTable::getList([
                'select' => [
                    'ID',
                ]
            ])->fetchAll();

            $result = [];
            array_walk_recursive(
                $arGroupList, function ($item, $key) use (&$result) {

                $result[] = $item['ID'];
            });
            shuffle($result);

            return $result[0];
        }

        shuffle($this->arSelectedGroups);

        return [$this->arSelectedGroups[0]];
    }
}