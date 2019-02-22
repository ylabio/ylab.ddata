<?php

namespace Ylab\Ddata\Entity;

use Bitrix\Main\Application;
use Bitrix\Main\HttpRequest;
use Bitrix\Main\Localization\Loc;
use Ylab\Ddata\Interfaces\EntityUnitClass;
use Ylab\Ddata\Orm\DataUnitGenElementsTable;

Loc::loadMessages(__FILE__);

/**
 * Class User
 *
 * @package Ylab\Ddata\entity
 */
class User extends EntityUnitClass
{
    /**
     * @inheritdoc
     * @return array
     */
    public function getDescription()
    {
        return [
            'ID' => 'user',
            'NAME' => Loc::getMessage('YLAB_DDATA_USER_ENTITY_NAME'),
            'DESCRIPTION' => Loc::getMessage('YLAB_DDATA_USER_ENTITY_DESCRIPTION'),
            'TYPE' => 'user',
            'CLASS' => __CLASS__
        ];
    }

    /**
     * User constructor.
     *
     * @param $iProfileID
     *
     * @throws \Bitrix\Main\ArgumentException
     * @throws \Bitrix\Main\SystemException
     */
    public function __construct($iProfileID)
    {
        parent::__construct($iProfileID);
    }

    /**
     * @inheritdoc
     *
     * @param HttpRequest $request
     *
     * @return string
     */
    public function getPrepareForm(HttpRequest $request)
    {
        return '';
    }

    /**
     * @inheritdoc
     *
     * @param HttpRequest $request
     *
     * @return boolean
     */
    public function isValidPrepareForm(HttpRequest $request)
    {
        return true;
    }

    /**
     * @inheritdoc
     * @return array
     */
    public function GetFields(HttpRequest $oRequest)
    {
        global $USER_FIELD_MANAGER;

        $arUserProperties = $USER_FIELD_MANAGER->GetUserFields('USER');

        $arFields = [
            'FIELDS' => [
                'EMAIL' => [
                    'type' => ['string', 'integer'],
                    'title' => Loc::getMessage('YLAB_DDATA_USER_FIELD_EMAIL'),
                    'required' => true
                ],
                'LOGIN' => [
                    'type' => ['string', 'integer'],
                    'title' => Loc::getMessage('YLAB_DDATA_USER_FIELD_LOGIN'),
                    'required' => true
                ],
                'PASSWORD' => [
                    'type' => ['string', 'integer'],
                    'title' => Loc::getMessage('YLAB_DDATA_USER_FIELD_NEW_PASSWORD'),
                    'required' => true
                ],
                'ACTIVE' => [
                    'type' => ['checkbox'],
                    'title' => Loc::getMessage('YLAB_DDATA_USER_FIELD_ACTIVE'),
                ],
                'TITLE' => [
                    'type' => ['string'],
                    'title' => Loc::getMessage('YLAB_DDATA_USER_FIELD_TITLE')
                ],
                'NAME' => [
                    'type' => ['string', 'integer'],
                    'title' => Loc::getMessage('YLAB_DDATA_USER_FIELD_NAME')
                ],
                'LAST_NAME' => [
                    'type' => ['string', 'integer'],
                    'title' => Loc::getMessage('YLAB_DDATA_USER_FIELD_LAST_NAME')
                ],
                'SECOND_NAME' => [
                    'type' => ['string', 'integer'],
                    'title' => Loc::getMessage('YLAB_DDATA_USER_FIELD_SECOND_NAME')
                ],
                'XML_ID' => [
                    'type' => ['string', 'integer'],
                    'title' => Loc::getMessage('YLAB_DDATA_USER_FIELD_XML_ID')
                ],
                'GROUP_ID' => [
                    'type' => ['user.group'],
                    'title' => Loc::getMessage('YLAB_DDATA_USER_GROUP')
                ],
                'PERSONAL_PROFESSION' => [
                    'type' => ['string', 'integer'],
                    'title' => Loc::getMessage('YLAB_DDATA_USER_FIELD_PERSONAL_PROFESSION')
                ],
                'PERSONAL_WWW' => [
                    'type' => ['string', 'integer'],
                    'title' => Loc::getMessage('YLAB_DDATA_USER_FIELD_PERSONAL_WWW')
                ],
                'PERSONAL_ICQ' => [
                    'type' => ['integer', 'integer'],
                    'title' => Loc::getMessage('YLAB_DDATA_USER_FIELD_PERSONAL_ICQ')
                ],
                'PERSONAL_GENDER' => [
                    'type' => ['user.gender'],
                    'title' => Loc::getMessage('YLAB_DDATA_USER_FIELD_PERSONAL_GENDER')
                ],
                'PERSONAL_PHOTO' => [
                    'type' => ['file'],
                    'title' => Loc::getMessage('YLAB_DDATA_USER_FIELD_PERSONAL_PHOTO')
                ],
                'PERSONAL_PHONE' => [
                    'type' => ['integer'],
                    'title' => Loc::getMessage('YLAB_DDATA_USER_FIELD_PERSONAL_PHONE')
                ],
                'PERSONAL_FAX' => [
                    'type' => ['integer'],
                    'title' => Loc::getMessage('YLAB_DDATA_USER_FIELD_PERSONAL_FAX')
                ],
                'PERSONAL_MOBILE' => [
                    'type' => ['integer'],
                    'title' => Loc::getMessage('YLAB_DDATA_USER_FIELD_PERSONAL_MOBILE')
                ],
                'PERSONAL_PAGER' => [
                    'type' => ['string', 'integer'],
                    'title' => Loc::getMessage('YLAB_DDATA_USER_FIELD_PERSONAL_PAGER')
                ],
                'PERSONAL_COUNTRY' => [
                    'type' => ['user.country'],
                    'title' => Loc::getMessage('YLAB_DDATA_USER_FIELD_PERSONAL_COUNTRY')
                ],
                'PERSONAL_STATE' => [
                    'type' => ['string', 'integer'],
                    'title' => Loc::getMessage('YLAB_DDATA_USER_FIELD_PERSONAL_STATE')
                ],
                'PERSONAL_CITY' => [
                    'type' => ['string', 'integer'],
                    'title' => Loc::getMessage('YLAB_DDATA_USER_FIELD_PERSONAL_CITY')
                ],
                'PERSONAL_ZIP' => [
                    'type' => ['integer'],
                    'title' => Loc::getMessage('YLAB_DDATA_USER_FIELD_PERSONAL_ZIP')
                ],
                'PERSONAL_STREET' => [
                    'type' => ['string', 'integer'],
                    'title' => Loc::getMessage('YLAB_DDATA_USER_FIELD_PERSONAL_STREET')
                ],
                'PERSONAL_MAILBOX' => [
                    'type' => ['string', 'integer'],
                    'title' => Loc::getMessage('YLAB_DDATA_USER_FIELD_PERSONAL_MAILBOX')
                ],
                'PERSONAL_NOTES' => [
                    'type' => ['string', 'integer'],
                    'title' => Loc::getMessage('YLAB_DDATA_USER_FIELD_PERSONAL_NOTES')
                ],
                'WORK_COMPANY' => [
                    'type' => ['string', 'integer'],
                    'title' => Loc::getMessage('YLAB_DDATA_USER_FIELD_WORK_COMPANY')
                ],
                'WORK_WWW' => [
                    'type' => ['string', 'integer'],
                    'title' => Loc::getMessage('YLAB_DDATA_USER_FIELD_WORK_WWW')
                ],
                'WORK_DEPARTMENT' => [
                    'type' => ['string', 'integer'],
                    'title' => Loc::getMessage('YLAB_DDATA_USER_FIELD_WORK_DEPARTMENT')
                ],
                'WORK_POSITION' => [
                    'type' => ['string', 'integer'],
                    'title' => Loc::getMessage('YLAB_DDATA_USER_FIELD_WORK_POSITION')
                ],
                'WORK_PROFILE' => [
                    'type' => ['string', 'integer'],
                    'title' => Loc::getMessage('YLAB_DDATA_USER_FIELD_WORK_PROFILE')
                ],
                'WORK_LOGO' => [
                    'type' => ['file'],
                    'title' => Loc::getMessage('YLAB_DDATA_USER_FIELD_WORK_LOGO')
                ],
                'WORK_PHONE' => [
                    'type' => ['integer'],
                    'title' => Loc::getMessage('YLAB_DDATA_USER_FIELD_WORK_PHONE')
                ],
                'WORK_FAX' => [
                    'type' => ['integer'],
                    'title' => Loc::getMessage('YLAB_DDATA_USER_FIELD_WORK_FAX')
                ],
                'WORK_PAGER' => [
                    'type' => ['integer'],
                    'title' => Loc::getMessage('YLAB_DDATA_USER_FIELD_WORK_PAGER')
                ],
                'WORK_COUNTRY' => [
                    'type' => ['user.country'],
                    'title' => Loc::getMessage('YLAB_DDATA_USER_FIELD_WORK_COUNTRY')
                ],
                'WORK_STATE' => [
                    'type' => ['string', 'integer'],
                    'title' => Loc::getMessage('YLAB_DDATA_USER_FIELD_WORK_STATE')
                ],
                'WORK_CITY' => [
                    'type' => ['string', 'integer'],
                    'title' => Loc::getMessage('YLAB_DDATA_USER_FIELD_WORK_CITY')
                ],
                'WORK_ZIP' => [
                    'type' => ['integer'],
                    'title' => Loc::getMessage('YLAB_DDATA_USER_FIELD_WORK_ZIP')
                ],
                'WORK_STREET' => [
                    'type' => ['string', 'integer'],
                    'title' => Loc::getMessage('YLAB_DDATA_USER_FIELD_WORK_STREET')
                ],
                'WORK_MAILBOX' => [
                    'type' => ['string', 'integer'],
                    'title' => Loc::getMessage('YLAB_DDATA_USER_FIELD_WORK_MAILBOX')
                ],
                'WORK_NOTES' => [
                    'type' => ['string', 'integer'],
                    'title' => Loc::getMessage('YLAB_DDATA_USER_FIELD_WORK_NOTES')
                ],
            ],
        ];

        foreach ($arUserProperties as $sPropertyCode => $arProperty) {
            $arFields['PROPERTIES'][$sPropertyCode] = [];
            $arItem = &$arFields['PROPERTIES'][$sPropertyCode];
            $arPropertyWithLang = $USER_FIELD_MANAGER->GetUserFields('USER', $arProperty['ID'], LANGUAGE_ID);
            $arItem['title'] = $arPropertyWithLang[$sPropertyCode]['EDIT_FORM_LABEL'];
            $arItem['required'] = ($arProperty['MANDATORY'] == 'Y');
            $arItem['multiple'] = ($arProperty['MULTIPLE'] == 'Y');

            switch ($arProperty['USER_TYPE_ID']) {
                case 'string':
                    $arItem['type'] = ['string', 'integer'];
                    break;
                case 'integer':
                    $arItem['type'] = ['integer'];
                    break;
                case 'double':
                    $arItem['type'] = ['integer'];
                    break;
                case 'datetime':
                    $arItem['type'] = ['datetime'];
                    break;
                case 'date':
                    $arItem['type'] = ['datetime'];
                    break;
                case 'file':
                    $arItem['type'] = ['file'];
                    break;
                case 'iblock_section':
                    $arItem['type'] = ['iblock.section'];
                    break;
                case 'iblock_element':
                    $arItem['type'] = ['iblock.element'];
                    break;
                default:
                    $arItem['type'] = [];
                    break;
            }
        }

        return $arFields;
    }

    /**
     * @inheritdoc
     * @return mixed
     */
    public function genUnit()
    {
        $oUser = new \CUser();
        $arFields = $this->profile['FIELDS'];
        $arProperties = $this->profile['PROPERTIES'];
        $iNewUserID = 0;
        $arUserFields = [];
        $arResult = [];

        if (isset($arFields)) {
            foreach ($arFields as $arField) {
                if ($arField['MULTIPLE'] == 'Y') {
                    $arGeneratorDescription = $arField['OBJECT']->getDescription();
                    if ($arGeneratorDescription['TYPE'] == 'file') {
                        for ($iCount = 1; $iCount <= $arField['COUNT']; $iCount++) {
                            $arUserFields[$arField['FIELD_CODE']]['n' . $iCount] = $arField['OBJECT']->getValue();
                        }
                    } else {
                        for ($iCount = 1; $iCount <= $arField['COUNT']; $iCount++) {
                            $arUserFields[$arField['FIELD_CODE']][] = $arField['OBJECT']->getValue();
                        }
                    }
                } else {
                    $arUserFields[$arField['FIELD_CODE']] = $arField['OBJECT']->getValue();
                }
                if ($arField['FIELD_CODE'] == 'PASSWORD') {
                    $arUserFields['CONFIRM_PASSWORD'] = $arUserFields['PASSWORD'];
                }
            }
        }

        $iNewUserID = $oUser->Add($arUserFields);
        if ($iNewUserID > 0) {
            $arResult['NEW_ELEMENT_ID'] = $iNewUserID;
        } else {
            $arResult['ERROR'] = $oUser->LAST_ERROR;
        }

        return $arResult;
    }

    /**
     * Удаление сгенерированных данных
     *
     * @return mixed
     * @throws \Bitrix\Main\ArgumentException
     * @throws \Bitrix\Main\Db\SqlQueryException
     * @throws \Bitrix\Main\ObjectPropertyException
     * @throws \Bitrix\Main\SystemException
     * @throws \Exception
     */
    public function deleteGenData()
    {
        $arGenData = $this->getGenData();

        $connection = Application::getConnection();
        $connection->startTransaction();

        foreach ($arGenData as $arGenDatum) {
            if (!\CUser::Delete($arGenDatum['GEN_ELEMENT_ID'])) {
                $connection->rollbackTransaction();
                throw new \Exception(Loc::getMessage('YLAB_DDATA_DELETE_DATA_OPTION_ERR_DELETE',
                    ['#ELEMENT_ID#' => $arGenDatum['GEN_ELEMENT_ID']]));
            }
            $oResult = DataUnitGenElementsTable::delete($arGenDatum['ID']);
            if (!$oResult->isSuccess()) {
                $connection->rollbackTransaction();
                throw new \Exception(Loc::getMessage('YLAB_DDATA_DELETE_DATA_OPTION_ERR_DELETE',
                    ['#ELEMENT_ID#' => $arGenDatum['ID']]));
            }
        }

        $connection->commitTransaction();
    }
}