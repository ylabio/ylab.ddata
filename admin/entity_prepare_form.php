<?php
require_once($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_admin_before.php");

use Bitrix\Main\Loader;
use Bitrix\Main\Localization\Loc;
use Ylab\Ddata\LoadUnits;

try {
    define('MODULE_ID', 'ylab.ddata');
    Loader::includeModule(MODULE_ID);

    Loc::loadMessages(__FILE__);
    $ctx = \Bitrix\Main\Application::getInstance()->getContext();
    $request = $ctx->getRequest();
    $objLoadUnits = new LoadUnits();
    $arEntityUnits = $objLoadUnits->getEntityUnits();
    $arPrepareRequest = $request->get('prepare');
    $isValidEntityPrepareForm = false;
    if ($request->isPost() && !empty($arPrepareRequest['entity_id'])) {
        $entityIndex = array_search($arPrepareRequest['entity_id'], array_column($arEntityUnits, 'ID'));
        $class = $arEntityUnits[$entityIndex]['CLASS'];
        $entityPrepareForm = $class::getPrepareForm($request);
        $isValidEntityPrepareForm = $class::isValidPrepareForm($request);
    }
} catch (\Exception $e) {
    $error = $e->getMessage();
}

if (isset($error)) {
    CAdminMessage::ShowMessage([
        "MESSAGE" => $error,
        "TYPE" => "ERROR",
    ]);
}

?>
<form action="<? echo $APPLICATION->GetCurPage(); ?>" method="post" id="WindowEntityPrepareForm">
    <table class="adm-detail-content-table edit-table">
        <tbody>
        <tr>
            <td width="40%"
                class="adm-detail-content-cell-l"><?= Loc::getMessage("YLAB_DDATA_ENTITY_PREPARE_FORM_FIELD_ENTITY_ID") ?></td>
            <td class="adm-detail-content-cell-r">
                <select name="prepare[entity_id]" id="entity_id">
                    <option value="">-</option>
                    <? foreach ($arEntityUnits as $entityUnit): ?>
                        <option value="<?= $entityUnit['ID'] ?>" <?= ($arPrepareRequest['entity_id'] == $entityUnit['ID'] ? "selected" : "") ?>><?= $entityUnit['NAME'] ?></option>
                    <? endforeach; ?>
                </select>
            </td>
        </tr>
        <? if (isset($entityPrepareForm) && !empty($entityPrepareForm)) {
            echo $entityPrepareForm;
        } ?>
        </tbody>
    </table>
    <script type="text/javascript">
        BX.bind(BX('entity_id'), 'change', function () {
            window.YlabDdata.WindowEntityPrepareForm.PostParameters();
        });

        var saveBtn = {
            title: "Сохранить",
            id: "savebtn",
            name: "savebtn",
            className: "adm-btn-save",
            action: function () {
                window.location.replace("/bitrix/admin/ylab.ddata_entity_profile_edit.php?<?=http_build_query(['prepare'=> $arPrepareRequest]);?>")
            }
        };

        window.YlabDdata.WindowEntityPrepareForm.SetButtons([<?=($isValidEntityPrepareForm ? "saveBtn," : "")?> BX.CDialog.prototype.btnCancel]);
    </script>
</form>

