<?php

namespace Ylab\Ddata\Data;

use Bitrix\Main\GroupTable;
use Bitrix\Main\Localization\Loc;
use Ylab\Ddata\Interfaces\DataUnitClass;
use Bitrix\Main\HttpRequest;
use Ylab\Ddata\Helpers;

Loc::loadMessages(__FILE__);

/**
 * Случайный ID пользователя
 *
 * Class RandomUser
 * @package Ylab\Ddata\Data
 */
class RandomUser extends DataUnitClass
{
    /** @var int iUsersGroup Группа администраторов */
    const iUsersGroup = 3;

    protected $sUserChoice = 'RANDOM';

    /** @var array $arSelectedGroups */
    protected $arSelectedGroups = [RandomUser::iUsersGroup];

    /** @var array $arUsers */
    protected $arUsers = [];

    /** @var array $arAllUsers */
    protected $arAllUsers = [];

    /**
     * RandomUser constructor.
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

        $arAllUsers = [];
        $oAllUsers = \CUser::GetList(($by = "id"), ($order = "asc"), []);
        while ($arAllUsersDB = $oAllUsers->GetNext()) {
            $arAllUsers[] = [
                'ID' => $arAllUsersDB['ID'],
                'NAME' => $arAllUsersDB['NAME'],
                'LAST_NAME' => $arAllUsersDB['LAST_NAME'],
                'LOGIN' => $arAllUsersDB['LOGIN'],
                'GROUPS_ID' => \CUser::GetUserGroup($arAllUsersDB['ID'])
            ];
        }
        $this->arAllUsers = $arAllUsers;
        if (!empty($this->options['choice'])) {
            $this->sUserChoice = $this->options['choice'];
        }

        if (!empty($this->options['selected-group'])) {
            $this->arSelectedGroups = [$this->options['selected-group']];
        }

        if (!empty($this->options['user-id'])) {
            $this->arUsers = $this->options['user-id'];
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
            'ID' => 'user.unit',
            'NAME' => Loc::getMessage('YLAB_DDATA_DATA_UNIT_USER_NAME'),
            'DESCRIPTION' => Loc::getMessage('YLAB_DDATA_DATA_UNIT_USER_DESCRIPTION'),
            'TYPE' => 'user',
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
        include Helpers::getModulePath() . '/admin/fragments/random_user_settings_form.php';
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
            $sUserChoice = $arPrepareRequest['choice'];

            if ($sUserChoice == 'RANDOM') {
                $iUserGroup = $arPrepareRequest['selected-group'];
                if ($iUserGroup > 0) {
                    return true;
                }
            } elseif ($sUserChoice == 'DEFINED') {
                $iUserID = $arPrepareRequest['user-id'];
                if ($iUserID > 0) {
                    return true;
                }
            }
        }

        return false;
    }

    /**
     * Возвращает случайную запись соответствующего типа
     *
     * @return int
     * @throws \Exception
     */
    public function getValue()
    {
        $sUserChoice = $this->sUserChoice;
        $arAllUsers = $this->arUsers;
        if ($sUserChoice == 'RANDOM') {
            $arSelectedGroups = $this->arSelectedGroups;
            $arResult = [];
            foreach ($arUsers as $arUser) {
                if (in_array($arSelectedGroups[0], $arUser['GROUPS_ID'])) {
                    $arResult[] = $arUser;
                }
            }
            $iResult = array_rand($arResult);

            return $arUsers[$iResult]['ID'];
        } elseif ($sUserChoice == 'DEFINED') {
            $iResult = array_rand($arUsers);

            return $arUsers[$iResult];
        }

        return false;
    }
}