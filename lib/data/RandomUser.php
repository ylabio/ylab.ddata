<?php

namespace Ylab\Ddata\Data;

use Bitrix\Main\GroupTable;
use Bitrix\Main\Localization\Loc;
use Ylab\Ddata\Interfaces\DataUnitClass;
use Bitrix\Main\HttpRequest;
use Ylab\Ddata\Helpers;

Loc::loadMessages(__FILE__);

/**
 * Class RandomUser
 * @package Ylab\Ddata\Data
 */
class RandomUser extends DataUnitClass
{
    /**
     * Группа администраторов
     */
    const iUsersGroup = 3;

    private static $bCheckStaticMethod = true;

    protected $sUserChoice = 'RANDOM';
    protected $arSelectedGroups = [RandomUser::iUsersGroup];
    protected $arUsers = [];
    protected $iUserID;

    /**
     * RandomInteger constructor.
     * @param $sProfileID
     * @param $sFieldCode
     * @param $sGeneratorID
     * @throws \Bitrix\Main\ArgumentException
     */
    public function __construct($sProfileID, $sFieldCode, $sGeneratorID)
    {
        self::$bCheckStaticMethod = false;
        parent::__construct($sProfileID, $sFieldCode, $sGeneratorID);

        $arUsers = [];
        $oAllUsers = \CUser::GetList(($by = "id"), ($order = "asc"), []);
        while ($arAllUsers = $oAllUsers->GetNext()) {
            $arUsers[] = [
                'ID' => $arAllUsers['ID'],
                'NAME' => $arAllUsers['NAME'],
                'LAST_NAME' => $arAllUsers['LAST_NAME'],
                'LOGIN' => $arAllUsers['LOGIN'],
                'GROUPS_ID' => \CUser::GetUserGroup($arAllUsers['ID'])
            ];
        }
        $this->arUsers = $arUsers;
        if (!empty($this->options['choice'])) {
            $this->sUserChoice = $this->options['choice'];
        }

        if (!empty($this->options['selected-group'])) {
            $this->arSelectedGroups = [$this->options['selected-group']];
        }

        if (!empty($this->options['user-id'])) {
            $this->iUserID = $this->options['user-id'];
        }

    }

    /**
     * @return array
     */
    public static function getDescription()
    {
        return [
            "ID" => "user.unit",
            "NAME" => Loc::getMessage("YLAB_DDATA_DATA_UNIT_USER_NAME"),
            "DESCRIPTION" => Loc::getMessage('YLAB_DDATA_DATA_UNIT_USER_DESCRIPTION'),
            "TYPE" => "user",
            "CLASS" => __CLASS__
        ];
    }

    /**
     * @param HttpRequest $request
     * @return string
     * @throws \Bitrix\Main\ArgumentException
     */
    public static function getOptionForm(HttpRequest $request)
    {
        $arRequest = $request->toArray();
        $arOptions = $arRequest['option'];
        $sGeneratorID = $arRequest['generator'];
        $sFieldCode = $arRequest['property-code'];
        $iProfileID = $arRequest['PROFILE']['ID'];
        $sPropertyName = $request->get('property-name');
        $arGroupList = GroupTable::getList([
            'select' => [
                'ID',
                'NAME'
            ]
        ])->fetchAll();

        $arOptions = array_merge(self::getOptions($iProfileID, $sFieldCode, $sGeneratorID), $arOptions);
        ob_start();
        include Helpers::getModulePath() . '/admin/fragments/random_user_settings_form.php';
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
     * @return int
     * @throws \Exception
     */
    public function getValue()
    {
        if (!self::$bCheckStaticMethod) {
            $sUserChoice = $this->sUserChoice;
            $arUsers = $this->arUsers;
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
                return $this->iUserID;
            }
        } else {
            throw new \Exception(Loc::getMessage('YLAB_DDATA_DATA_USER_GROUP_EXCEPTION_STATIC'));
        }
    }
}