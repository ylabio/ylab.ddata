<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/main/include/prolog_admin_before.php';

use Bitrix\Main\Loader;
use Bitrix\Main\Localization\Loc;
use Ylab\Ddata;

/** @var \CMain $APPLICATION */
global $APPLICATION;

try {
    define('MODULE_ID', 'ylab.ddata');
    Loader::includeModule(MODULE_ID);

    Loc::loadMessages(__FILE__);
    Loc::loadMessages(LANG_ROOT);

    $POST_RIGHT = $APPLICATION->GetGroupRight(MODULE_ID);
    if ($POST_RIGHT == 'D') {
        $APPLICATION->AuthForm(Loc::getMessage('YLAB_DDATA_ACCESS_DENIED'));
    }

    $oRequest = \Bitrix\Main\Application::getInstance()->getContext()->getRequest();
    if ($oRequest->isAjaxRequest() && check_bitrix_sessid()) {
        $sActionButton = $oRequest->get('action_button');
        $iProfileID = $oRequest->get('ID');
        if ($sActionButton == 'delete') {
            $objProfile = \Ylab\Ddata\Orm\EntityUnitProfileTable::getList([
                'filter' => ['=ID' => $iProfileID]
            ]);
            $arProfile = $objProfile->fetch();
            if (!empty($arProfile)) {
                $arProfile['FIELDS'] = [];
                $objDataUnit = Ylab\Ddata\Orm\DataUnitOptionsTable::getList([
                    'filter' => ['PROFILE_ID' => $iProfileID]
                ]);
                $arDataUnit = $objDataUnit->fetchAll();
                $arProfile['FIELDS'] = $arDataUnit;
                $bResultFields = false;
                $bResultProfile = false;
                foreach ($arProfile['FIELDS'] as $arField) {
                    $bResultFields = Ylab\Ddata\Orm\DataUnitOptionsTable::delete($arField['ID']);
                    if ($bResultFields->isSuccess()) {
                        $bResultFields = true;
                    }
                }
                $bResultProfile = \Ylab\Ddata\Orm\EntityUnitProfileTable::delete($iProfileID);
                if ($bResultProfile->isSuccess()) {
                    $bResultProfile = true;
                }
            }
        } elseif ($sActionButton == 'delete-data') {
            $objProfile = \Ylab\Ddata\Orm\EntityUnitProfileTable::getList([
                'filter' => ['=ID' => $iProfileID]
            ]);
            $arProfile = $objProfile->fetch();
            $arOptions = json_decode($arProfile['OPTIONS']);
            $oClasses = new Ddata\LoadUnits();
            $arClassesEntity = $oClasses->getEntityUnits();
            $arEntity = [];
            foreach ($arClassesEntity as $arClass) {
                if ($arClass['ID'] == $arOptions->entity_id) {
                    $arEntity = $arClass;
                    break;
                }
            }
            if ($arEntity['CLASS']) {
                $oEntity = new $arEntity['CLASS']($iProfileID);
                $oEntity->deleteGenData();
                echo '<script>alert("Удаление демо-данных успешно завершено")</script>';
            }
        }
    } elseif ($oRequest->isPost()) {
        $arProfileID = $oRequest->get('ID');
        $sActionButton = $oRequest->get('action_button');
        if ($sActionButton == 'delete') {
            if (!empty($arProfileID)) {
                foreach ($arProfileID as $iProfileID) {
                    $objProfile = \Ylab\Ddata\Orm\EntityUnitProfileTable::getList([
                        'filter' => ['=ID' => $iProfileID]
                    ]);
                    $arProfile = $objProfile->fetch();
                    if (!empty($arProfile)) {
                        $arProfile['FIELDS'] = [];
                        $objDataUnit = Ylab\Ddata\Orm\DataUnitOptionsTable::getList([
                            'filter' => ['PROFILE_ID' => $iProfileID]
                        ]);
                        $arDataUnit = $objDataUnit->fetchAll();
                        $arProfile['FIELDS'] = $arDataUnit;
                        $bResultFields = false;
                        $bResultProfile = false;
                        foreach ($arProfile['FIELDS'] as $arField) {
                            $bResultFields = Ylab\Ddata\Orm\DataUnitOptionsTable::delete($arField['ID']);
                            if ($bResultFields->isSuccess()) {
                                $bResultFields = true;
                            }
                        }
                        $bResultProfile = \Ylab\Ddata\Orm\EntityUnitProfileTable::delete($iProfileID);
                        if ($bResultProfile->isSuccess()) {
                            $bResultProfile = true;
                        }
                    }
                }
            }
        }
    }

    $sTableID = \Ylab\Ddata\Orm\EntityUnitProfileTable::getTableName();
    $oSort = new CAdminSorting($sTableID, 'ID', 'desc');
    $lAdmin = new CAdminList($sTableID, $oSort);

    function CheckFilter()
    {
        global $FilterArr, $lAdmin;
        foreach ($FilterArr as $f) {
            global $$f;
        }

        return count($lAdmin->arFilterErrors) == 0; // если ошибки есть, вернем false;
    }

    $FilterArr = [
        'ID' => 'find_id',
        'NAME' => 'find_name',
        'TYPE' => 'find_type',
        'XML_ID' => 'find_xml_id',
    ];

    $lAdmin->InitFilter($FilterArr);

    if (CheckFilter()) {
        $setFilter = $lAdmin->getFilter();
        foreach ($FilterArr as $filter => $value) {
            if (isset($setFilter[$value]) && !empty($setFilter[$value])) {
                $arFilter[$filter] = $setFilter[$value];
            }
        }
    }

    $arTableHeaders = [
        [
            'id' => 'ID',
            'content' => 'ID',
            'sort' => 'ID',
            'default' => true,
        ],
        [
            'id' => 'NAME',
            'content' => Loc::getMessage('YLAB_DDATA_THEAD_NAME'),
            'sort' => 'NAME',
            'default' => true,
        ],
        [
            'id' => 'TYPE',
            'content' => Loc::getMessage('YLAB_DDATA_THEAD_TYPE'),
            'sort' => 'TYPE',
            'default' => true,
        ],
        [
            'id' => 'XML_ID',
            'content' => Loc::getMessage('YLAB_DDATA_THEAD_XML_ID'),
            'sort' => 'XML_ID',
            'default' => true,
        ]
    ];
    $lAdmin->AddHeaders($arTableHeaders);

    $arAdminContextMenu = [];

    if ($POST_RIGHT >= 'W') {

        $arAdminContextMenu[] = [
            'TEXT' => Loc::getMessage('YLAB_DDATA_ADMIN_ACTION_ADD_TEXT'),
            'TITLE' => Loc::getMessage('YLAB_DDATA_ADMIN_ACTION_ADD_TITLE'),
            'LINK' => "javascript:window.YlabDdata.WindowEntityPrepareForm.Show()",
            'ICON' => 'btn_new'
        ];
        $arAdminContextMenu[] = [
            'TEXT' => Loc::getMessage('YLAB_DDATA_ADMIN_ACTION_IMPORT_PROFILE_TEXT'),
            'TITLE' => Loc::getMessage('YLAB_DDATA_ADMIN_ACTION_IMPORT_PROFILE_TITLE'),
            'ICON' => 'btn_new',
            'HTML' => "<span id='import_profile' class='adm-btn adm-btn-save adm-btn-add'>
                            " . Loc::getMessage('YLAB_DDATA_ADMIN_ACTION_IMPORT_PROFILE_TEXT') . "
                            <form style='display: none;' action='ylab.ddata_import_profile.php' method='post' enctype='multipart/form-data'>
                                <input name='file' type='file' id='import_profile_inp'>
                            </form>
                        </span>",
        ];
    }

    $lAdmin->AddAdminContextMenu($arAdminContextMenu);

    //Вывод навигации
    $obAllData = \Ylab\Ddata\Orm\EntityUnitProfileTable::getList([
        'filter' => (!empty($arFilter) ? $arFilter : []),
        'select' => ['ID']
    ]);
    $rsAllData = new CAdminResult($obAllData, $sTableID);
    $rsAllData->NavStart();
    $lAdmin->NavText($rsAllData->GetNavPrint(Loc::getMessage('YLAB_DDATA_NAV_TITLE')));

    //Запрос данных для таблицы
    $arRequest = [];
    if (!empty($arFilter)) {
        $arRequest['filter'] = $arFilter;
    }
    $arRequest['select'] = $lAdmin->GetVisibleHeaderColumns();
    $arRequest['order'] = [$oSort->getField() => $oSort->getOrder()];
    if (!$rsAllData->NavShowAll) {//Ограничения выборки с учетом навигации
        $arRequest['limit'] = $rsAllData->NavPageSize;
        if ((int)$rsAllData->NavPageNomer > 1) {
            $arRequest['offset'] = ($rsAllData->NavPageNomer - 1) * $rsAllData->NavPageSize;
        }
    }
    $obgData = \Ylab\Ddata\Orm\EntityUnitProfileTable::getList($arRequest);
    $rsData = new CAdminResult($obgData, $sTableID);

    while ($arRes = $rsData->NavNext(true, 'f_')) {
        $row =& $lAdmin->AddRow($f_ID, $arRes);

        $arActions = [];

        if ($POST_RIGHT >= 'W') {
            $arActions[] = [
                'ICON' => 'edit',
                'DEFAULT' => true,
                'TEXT' => Loc::getMessage('YLAB_DDATA_ROW_ACTION_EDIT'),
                'ACTION' => $lAdmin->ActionRedirect('ylab.ddata_entity_profile_edit.php?PROFILE[ID]=' . $f_ID . '&prepare[entity_id]=' . $f_TYPE)
            ];
            $arActions[] = array(
                'ICON' => 'delete',
                'TEXT' => Loc::getMessage('YLAB_DDATA_ROW_ACTION_DEL'),
                'ACTION' => "if(confirm('" . Loc::getMessage('YLAB_DDATA_ROW_ACTION_DEL_CONFIRM') . "')) " . $lAdmin->ActionDoGroup($f_ID,
                        'delete')
            );
        }

        $row->AddViewField('NAME',
            '<a href="ylab.ddata_entity_profile_edit.php?PROFILE[ID]=' . $f_ID . '&prepare[entity_id]=' . $f_TYPE . '">' . $f_NAME . '</a>');

        $arActions[] = [
            'ICON' => 'copy',
            'TEXT' => Loc::getMessage('YLAB_DDATA_ROW_ACTION_GEN'),
            'ACTION' => $lAdmin->ActionRedirect('ylab.ddata_entity_profile_gen.php?ID=' . $f_ID . '&entity_id=' . $f_TYPE)
        ];

        $arActions[] = [
            'ICON' => 'delete',
            'TEXT' => Loc::getMessage('YLAB_DDATA_ROW_ACTION_DEL_DATA'),
            'ACTION' => "if(confirm('" . Loc::getMessage('YLAB_DDATA_ROW_ACTION_DEL_DATA_CONFIRM') . "')) " . $lAdmin->ActionDoGroup($f_ID,
                    'delete-data')
        ];

        $arActions[] = [
            'ICON' => '',
            'TEXT' => Loc::getMessage('YLAB_DDATA_ROW_ACTION_EXPORT_PROFILE'),
            'ACTION' => $lAdmin->ActionRedirect('ylab.ddata_export_profile.php?lang=ru' . '&export_profile=' . $f_ID)
        ];

        $row->AddActions($arActions);
    }


    $lAdmin->AddGroupActionTable([
        'delete' => Loc::getMessage('YLAB_DDATA_GROUP_ACTION_DELETE'),
    ]);

    $lAdmin->CheckListMode();

    $APPLICATION->SetTitle(Loc::getMessage('YLAB_DDATA_PAGE_TITLE'));

    //Получаем необходимые данные для формирования фильтра
    $obgDataForFilter = \Ylab\Ddata\Orm\EntityUnitProfileTable::getList([
        'select' => ['TYPE'],
        'group' => ['TYPE'],
    ]);
    $arDataForFilter = $obgDataForFilter->fetchAll();
    $arTypes = array_column($arDataForFilter, 'TYPE');

} catch (\Exception $e) {
    $error = $e->getMessage();
}
require $_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/main/include/prolog_admin_after.php';

if (isset($error)) {
    CAdminMessage::ShowMessage([
        'MESSAGE' => $error,
        'TYPE' => 'ERROR',
    ]);
}

$oFilter = new CAdminFilter(
    $sTableID . '_filter',
    [
        'ID',
        Loc::getMessage('YLAB_DDATA_FILTER_COL_NAME'),
        Loc::getMessage('YLAB_DDATA_FILTER_TYPE'),
        Loc::getMessage('YLAB_DDATA_FILTER_XML_ID'),
    ]
);

CJSCore::Init(array('WindowEntityPrepareForm'));
CJSCore::Init(array('ImportProfile'));
?>


<?
$obRequest = \Bitrix\Main\Application::getInstance()->getContext()->getRequest();

if ($obRequest->get('import_profile') == 'Y') {
    ?>
    <div class="adm-info-message"><?= Loc::getMessage('YLAB_DDATA_IMPORT_PROFILE_Y') ?></div>
    <?
}
if ($obRequest->get('export_profile') == 'Y') {
    ?>
    <script>window.open("ylab.ddata_readfile.php?iIdProfile=<?=$obRequest->get('iIdProfile')?>");</script>
    <div class="adm-info-message"><?= Loc::getMessage('YLAB_DDATA_EXPORT_PROFILE_Y') ?></div>
    <?
}
?>

    <form name="find_form" method="get" action="<? echo $APPLICATION->GetCurPage(); ?>">
        <? $oFilter->Begin(); ?>
        <tr>
            <td>ID:</td>
            <td>
                <input type="text" name="find_id" size="47" value="<? echo htmlspecialchars($find_id) ?>">
            </td>
        </tr>
        <tr>
            <td><?= Loc::getMessage('YLAB_DDATA_FILTER_COL_NAME') . ":" ?></td>
            <td><input type="text" name="find_name" size="47" value="<? echo htmlspecialchars($find_name) ?>"></td>
        </tr>
        <tr>
            <td><?= Loc::getMessage('YLAB_DDATA_FILTER_TYPE') . ":" ?></td>
            <td>
                <select name="find_type">
                    <option value="">-</option>
                    <? foreach ($arTypes as $type): ?>
                        <option
                                value="<?= $type ?>" <?= ($type == $find_type ? "selected" : "") ?>><?= $type ?></option>
                    <? endforeach; ?>
                </select>
            </td>
        </tr>
        <tr>
            <td><?= Loc::getMessage('YLAB_DDATA_FILTER_XML_ID') . ":" ?></td>
            <td><input type="text" name="find_xml_id" size="47" value="<? echo htmlspecialchars($find_xml_id) ?>"></td>
        </tr>
        <?
        $oFilter->Buttons(["table_id" => $sTableID, "url" => $APPLICATION->GetCurPage(), "form" => "find_form"]);
        $oFilter->End();
        ?>
    </form>

<? $lAdmin->DisplayList(); ?>
<?
echo BeginNote();
echo '<div style="display: inline-block; vertical-align: middle"><img width="64px" height="64px" src="' . \Ylab\Ddata\Helpers::getModulePath(true) . '/assets/images/ylab.ddata.jpg' . '" alt=""></div>';
echo '<div style="display: inline-block; vertical-align: middle; margin-left: 5px">';
echo Loc::getMessage('YLAB_DDATA_ADVERTISING_SITE');
echo Loc::getMessage('YLAB_DDATA_ADVERTISING_GIT');
echo '</div>';
echo EndNote();
?>

<? require $_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/main/include/epilog_admin.php'; ?>