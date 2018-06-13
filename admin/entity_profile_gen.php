<?php
require_once($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_admin_before.php");

use Bitrix\Main\Loader;
use Bitrix\Main\Localization\Loc;
use Ylab\Ddata\Interfaces\DeleteDataClass;
use Ylab\Ddata\LoadUnits;

/** @var \CMain $APPLICATION */
global $APPLICATION;

try {
    Loc::loadMessages(__FILE__);

    define('MODULE_ID', 'ylab.ddata');
    Loader::includeModule(MODULE_ID);
    $sPostRight = $APPLICATION->GetGroupRight(MODULE_ID);
    if ($sPostRight == "D") {
        $APPLICATION->AuthForm(Loc::getMessage('YLAB_DDATA_ACCESS_DENIED'));
    }

    $oRequest = \Bitrix\Main\Application::getInstance()->getContext()->getRequest();

    /**
     * проверка наличия прав на запись для модуля
     * проверка идентификатора сессии
     */
    if (!empty($oRequest->get('work_start')) && $sPostRight == "W" && check_bitrix_sessid()) {
        $iCountElements = $oRequest->get('count');
        $iDuration = intval($oRequest->get('duration'));
        $iProfileIDAjax = $oRequest->get('profileID');
        $sEntityIDAjax = $oRequest->get('entityID');

        $oClasses = new LoadUnits();
        $arClassesEntity = $oClasses->getEntityUnits();
        $arClassesData = $oClasses->getDataUnits();

        $arEntity = [];
        foreach ($arClassesEntity as $arClass) {
            if ($arClass['ID'] == $sEntityIDAjax) {
                $arEntity = $arClass;
            }
        }

        if (!empty($iProfileIDAjax)) {
            $oEntity = new $arEntity['CLASS']($iProfileIDAjax);
            $oGenDataClass = new DeleteDataClass();
        }

        $iLastCounter = $oRequest->get('lastcounter') > 0 ? $oRequest->get('lastcounter') : 0;

        $iStart = time();
        while ($iLastCounter < $iCountElements) {
            $arResult = [];
            $sError = '';
            $arResult = $oEntity->genUnit();
            if (empty($arResult['ERROR'])) {
                $arNewElements[] = $arResult['NEW_ELEMENT_ID'];
                $oGenDataClass::setGenData($iProfileIDAjax, $sEntityIDAjax, $arResult['NEW_ELEMENT_ID']);
            } else {
                $sErrors = $arResult['ERROR'];
            }

            $iLastCounter++;
            $iPercent = round(100 * ($iLastCounter / $iCountElements), 2);
            $sResult = '';
            if (empty($sErrors)) {
                $sResult = Loc::getMessage('YLAB_DDATA_GENERATE_RESULT_SUCCESS',
                    ['#ELEMENTS#' => implode(", ", $arNewElements)]);
            } else {
                $sResult = Loc::getMessage('YLAB_DDATA_GENERATE_RESULT_ERROR', ['#ERROR#' => $sErrors]);
            }

            if (intval(time() - $iStart) == $iDuration) {
                break;
            }
        }

        echo 'CurrentStatus = Array(' . $iPercent . ',"' . ($iPercent < 100 ? '&lastcounter=' . $iLastCounter : '') . '", "' . $sResult . '");';
        die();
    }

    $oClasses = new LoadUnits();
    $arClassesEntity = $oClasses->getEntityUnits();
    $arClassesData = $oClasses->getDataUnits();

    $iProfileID = $oRequest->get('ID');
    $sEntityID = $oRequest->get('entity_id');

    $arEntity = [];
    foreach ($arClassesEntity as $arClass) {
        if ($arClass['ID'] == $sEntityID) {
            $arEntity = $arClass;
        }
    }

    if (!empty($iProfileID)) {
        $oEntity = new $arEntity['CLASS']($iProfileID);
        $arProfile = $oEntity->getProfile($iProfileID);
        $oGenDataClass = new DeleteDataClass();
        $arGenData = $oGenDataClass::getGenData($iProfileID);
    }
    $arTabs = [
        [
            "DIV" => "edit1",
            "TAB" => Loc::getMessage('YLAB_DDATA_TAB_NAME_MAIN'),
            "ICON" => "main_user_edit",
            "TITLE" => Loc::getMessage('YLAB_DDATA_TAB_NAME_TITLE')
        ]
    ];
    $tabControl = new CAdminTabControl("tabControl", $arTabs);

    $APPLICATION->SetTitle(Loc::getMessage('YLAB_DDATA_PAGE_TITLE_ADD'));
} catch (\Exception $e) {
    $error = $e->getMessage();
}
require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_admin_after.php");
if (isset($error)) {
    CAdminMessage::ShowMessage([
        "MESSAGE" => $error,
        "TYPE" => "ERROR",
    ]);
}
CJSCore::Init(['WindowEntityProfileGen']);
?>

<form method="post" action="<?= $APPLICATION->GetCurPage() ?>"
      enctype="multipart/form-data"
      name="post_form"
      id="genEntityForm">
    <? echo bitrix_sessid_post(); ?>
    <? if (!empty($iProfileID)): ?>
        <input type="hidden" value="<?= $iProfileID ?>" id="profile-id">
    <? endif; ?>
    <? if (!empty($sEntityID)): ?>
        <input type="hidden" value="<?= $sEntityID ?>" id="entity-id">
    <? endif; ?>
    <input type="hidden"
           value="<?= $_SERVER["PHP_SELF"] ?>?work_start=Y&lang=<?= LANGUAGE_ID ?>&<?= bitrix_sessid_get() ?>&profileID=<?= $iProfileID ?>&entityID=<?= $sEntityID ?>"
           id="ajax-path">
    <?
    $tabControl->Begin();
    $tabControl->BeginNextTab();
    ?>
    <? if (!empty($iProfileID)): ?>
        <tr>
            <td colspan="2">
                <div id="progress" style="display:none;" width="100%">
                    <br/>
                    <div id="status"></div>
                    <table border="0" cellspacing="0" cellpadding="2" width="100%">
                        <tr>
                            <td height="10">
                                <div style="border:1px solid #B9CBDF">
                                    <div id="indicator" style="height:10px; width:0%; background-color:#B9CBDF"></div>
                                </div>
                            </td>
                            <td width=30>&nbsp;<span id="percent">0%</span></td>
                        </tr>
                    </table>
                </div>
                <div id="result" style="padding-top:10px"></div>

            </td>
        </tr>
        <tr>
            <td width="40%" class="adm-detail-content-cell-l">
                <b>ID:</b>
            </td>
            <td class="adm-detail-content-cell-r">
                <?= $iProfileID ?>
            </td>
        </tr>
    <? endif; ?>
    <tr>
        <td width="40%" class="adm-detail-content-cell-l">
            <b><?= Loc::getMessage('YLAB_DDATA_GENERATE_PROFILE_NAME') ?>:</b>
        </td>
        <td class="adm-detail-content-cell-r">
            <?= $arProfile['NAME'] ?>
        </td>
    </tr>
    <tr>
        <td width="40%" class="adm-detail-content-cell-l">
            <?= Loc::getMessage('YLAB_DDATA_GENERATE_PROFILE_XML_ID') ?>:
        </td>
        <td class="adm-detail-content-cell-r">
            <?= $arProfile['XML_ID'] ?>
        </td>
    </tr>
    <tr>
        <td width="40%" class="adm-detail-content-cell-l">
            <?= Loc::getMessage('YLAB_DDATA_GENERATE_COUNT') ?>:
        </td>
        <td class="adm-detail-content-cell-r">
            <input type="text" id="count-elements" name="count" size="2" value="1">
        </td>
    </tr>
    <tr>
        <td width="40%" class="adm-detail-content-cell-l">
            <?= Loc::getMessage('YLAB_DDATA_GENERATE_DURATION') ?>:
        </td>
        <td class="adm-detail-content-cell-r">
            <input type="text" id="duration" name="duration" size="2"
                   value="20"> <?= Loc::getMessage('YLAB_DDATA_GENERATE_DURATION_COUNT') ?>
        </td>
    </tr>
    <?
    $tabControl->BeginNextTab();
    $tabControl->Buttons();
    ?>
    <input type="button"
           class="adm-btn-save"
           id="work_start"
           value="<?= Loc::getMessage('YLAB_DDATA_GENERATE_BUTTON_GEN') ?>"
           onclick="set_start(1)"
    >
    <input type="button"
           value="<?= Loc::getMessage('YLAB_DDATA_GENERATE_BUTTON_STOP') ?>"
           disabled
           id="work_stop"
           onclick="bSubmit=false;set_start(0)"
    >
    <input type="button"
           value="<?= Loc::getMessage('YLAB_DDATA_GENERATE_BUTTON_CANCEL') ?>"
           onclick="window.location.replace('ylab.ddata_entity_profile_list.php?lang=<?= LANG ?>')"
    >
    <? if ($arGenData): ?>
        <input type="button"
               class="adm-btn-cancel"
               value="<?= Loc::getMessage('YLAB_DDATA_GENERATE_BUTTON_DELETE') ?>"
               onclick="window.location.replace('ylab.ddata_entity_profile_list.php?lang=<?= LANG ?>')"
        >
    <? endif; ?>
    <? $tabControl->End(); ?>
</form>
<?
echo BeginNote();
echo '<div style="display: inline-block; vertical-align: middle"><img width="64px" height="64px" src="' . \Ylab\Ddata\Helpers::getModulePath(true) . '/assets/images/ylab.ddata.jpg' . '" alt=""></div>';
echo '<div style="display: inline-block; vertical-align: middle; margin-left: 5px">';
echo Loc::getMessage('YLAB_DDATA_ADVERTISING_SITE');
echo Loc::getMessage('YLAB_DDATA_ADVERTISING_GIT');
echo '</div>';
echo EndNote();
?>

<? require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/epilog_admin.php"); ?>
