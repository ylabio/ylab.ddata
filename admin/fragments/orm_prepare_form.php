<?php

use Bitrix\Main\Localization\Loc;

Loc::loadMessages(__FILE__);

if (!empty($arPrepareRequest['namespace']) && !class_exists($arPrepareRequest['namespace'])) {
    CAdminMessage::ShowMessage([
        'MESSAGE' => Loc::getMessage("YLAB_DDATA_ORM_NAMESPACE_ERROR"),
        'TYPE' => 'ERROR',
    ]);
}

?>
<td colspan="2" align="center">

    <div class="adm-info-message-wrap" align="center">
        <div class="adm-info-message">
            <p><?= Loc::getMessage('YLAB_DDATA_ORM_NAMESPACE_INFO_MSG') ?></p>
        </div>
    </div>
</td>
<tr>
    <td width="40%" class="adm-detail-content-cell-l">
        <?= Loc::getMessage('YLAB_DDATA_ORM_NAMESPACE') ?>:
    </td>
    <td class="adm-detail-content-cell-r">
        <input type="text" name="prepare[namespace]" id="namespace" value="<?= $arPrepareRequest['namespace'] ?>">
        <input type="button" id="check-namespace" class="adm-btn-save"
               value="<?= Loc::getMessage('YLAB_DDATA_ORM_CHECK_NAMESPACE') ?>"/>
    </td>
</tr>

<script>
    BX.ready(function () {
        BX.bind(BX('check-namespace'), 'click', function (event) {
            event.preventDefault();
            window.YlabDdata.WindowEntityPrepareForm.PostParameters();
        });
    });
</script>