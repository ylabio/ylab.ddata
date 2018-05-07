<?
/**
 * @global $arResultHlb
 * @global $arRequest
 */

$sHighloadblockId = $arRequest['prepare']['highloadblock_id'];

use Bitrix\Main\Localization\Loc;

Loc::loadMessages(__FILE__);
?>
<tr>
    <td width="40%" class="adm-detail-content-cell-l">
        <?= Loc::getMessage('YLAB_DDATA_FMG_HL_BL_ENTITY') ?>:
    </td>
    <td class="adm-detail-content-cell-r">
        <select name="prepare[highloadblock_id]" id="highloadblock-selector">
            <option value="0">-</option>
            <? foreach ($arResultHlb as $item): ?>
                <option value="<?= $item['ID'] ?>" <?= ($sHighloadblockId == $item['ID'] ? "selected" : "") ?>>
                    <?= (empty($item['LANG_NAME']) ? $item['NAME'] : $item['LANG_NAME']) ?>
                </option>
            <? endforeach; ?>
        </select>
    </td>
</tr>
<script>
    BX.ready(function () {
        BX.bind(BX('highloadblock-selector'), 'change', function () {
            window.YlabDdata.WindowEntityPrepareForm.PostParameters();
        })
    });
</script>