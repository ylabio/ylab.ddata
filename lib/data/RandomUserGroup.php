<?php

namespace Ylab\Ddata\Data;

use Bitrix\Main\Localization\Loc;
use Ylab\Ddata\Interfaces\DataUnitClass;
use Bitrix\Main\HttpRequest;
use Ylab\Ddata\Helpers;
use \Bitrix\Main\GroupTable;

Loc::loadMessages(__FILE__);

/**
 * Class RandomUserGroup
 * @package Ylab\Ddata\Data
 */
class RandomUserGroup extends DataUnitClass
{
    /**
     * Группа администраторов
     */
    const iAdminGroup = 1;

    private static $checkStaticMethod = true;

    protected $sRandom = 'N';
    protected $arSelectedGroups = [RandomUserGroup::iAdminGroup];

    /**
     * RandomUserGroup constructor.
     * @param $sProfileID
     * @param $sFieldCode
     * @param $sGeneratorID
     * @throws \Bitrix\Main\ArgumentException
     * @throws \Bitrix\Main\ObjectPropertyException
     * @throws \Bitrix\Main\SystemException
     */
    public function __construct($sProfileID, $sFieldCode, $sGeneratorID)
    {
        self::$checkStaticMethod = false;
        parent::__construct($sProfileID, $sFieldCode, $sGeneratorID);

        if (!empty($this->options['random'])) {
            $this->sRandom = $this->options['random'];
        }

        if (!empty($this->options['selected-groups'])) {
            $this->arSelectedGroups = $this->options['selected-groups'];
        }
    }

    /**
     * @return array
     */
    public static function getDescription()
    {
        return [
            "ID" => "user.group",
            "NAME" => Loc::getMessage("YLAB_DDATA_DATA_USER_GROUP_NAME"),
            "DESCRIPTION" => Loc::getMessage("YLAB_DDATA_DATA_USER_GROUP_DESCRIPTION"),
            "TYPE" => "user.group",
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

        $arGroupList = GroupTable::getList([
            'select' => [
                'ID',
                'NAME'
            ]
        ])->fetchAll();

        $arOptions = array_merge(self::getOptions($sGeneratorID, $sProfileID, $sFieldID), $arOptions);

        ob_start();
        include Helpers::getModulePath() . '/admin/fragments/random_user_group _settings_form.php';
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
            $sRandom = $arPrepareRequest['random'];
            $arSelectedGroups = $arPrepareRequest['selected-groups'];

            if (!empty($sRandom) || !empty($arSelectedGroups) && is_array($arSelectedGroups)) {
                return true;
            }
        }

        return false;
    }

    /**
     * @return mixed
     * @throws \Exception
     */
    public function getValue()
    {
        if (!self::$checkStaticMethod) {
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
            } else {
                shuffle($this->arSelectedGroups);

                return $this->arSelectedGroups[0];
            }
        } else {
            throw new \Exception(Loc::getMessage('YLAB_DDATA_DATA_USER_GROUP_EXCEPTION_STATIC'));
        }
    }
}