<?php
/**
 * @global $arSection
 * @global $arOptions
 * @global $sGeneratorID
 * @global $sProfileID
 * @global $sPropertyName
 */

use Bitrix\Main\Localization\Loc;

Loc::loadMessages(__FILE__);
?>
<table class="adm-detail-content-table edit-table">
    <td colspan="2" align="center">

        <div class="adm-info-message-wrap" align="center">
            <div class="adm-info-message">
                <p><?= Loc::getMessage("YLAB_DDATA_SECTION_GENERATE_HELP_MESSAGE") ?></p>
            </div>
        </div>
    </td>
    <tr>
        <td width="40%" class="adm-detail-content-cell-l">
            <?= Loc::getMessage('GENERATE_RANDOM') ?>
        </td>
        <td width="60%" class="adm-detail-content-cell-r">
            <select class="data-option" name="option[random]">
                <option value="Y" <?= ($arOptions['random'] == 'Y' ? 'selected' : '') ?> ><?= Loc::getMessage('RANDOM_VALUE_YES') ?></option>
                <option value="N" <?= ($arOptions['random'] == 'N' ? 'selected' : '') ?> ><?= Loc::getMessage('RANDOM_VALUE_NO') ?></option>
            </select>
        </td>
    </tr>
    <tr>
        <td width="40%" class="adm-detail-content-cell-l">
            <?= Loc::getMessage('SELECT_SECTIONS') ?>
        </td>
        <td width="60%" class="adm-detail-content-cell-r">
            <select class="data-option" name="option[selected-sections][]" multiple size="5" style="width: 50%;">
                <? if ($arSection): ?>
                    <? foreach ($arSection as $id => $value): ?>
                        <option value="<?= $id ?>"><?= str_repeat(" . ", $value[1]) ?><?= $value[0] ?></option>
                    <? endforeach; ?>
                <? else: ?>
                    <option value=""><?= Loc::getMessage('NO_SECTIONS') ?></option>
                <? endif; ?>
            </select>
        </td>
    </tr>
</table>