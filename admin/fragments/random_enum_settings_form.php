<?php
/**
 * @global $sGeneratorID
 * @global $sProfileID
 * @global $sPropertyCode
 * @global $sPropertyName
 * @global $this
 * @global $arValues
 */

use Bitrix\Main\Localization\Loc;

Loc::loadMessages(__FILE__);

$sRandom = $this->sRandom;
$iSelectedValue = $this->iSelectedValue;
?>
<table class="adm-detail-content-table edit-table">
    <tbody>
    <tr>
        <td width="40%" class="adm-detail-content-cell-l">
            <?= Loc::getMessage('GENERATE_RANDOM') ?>
        </td>
        <td width="60%" class="adm-detail-content-cell-r">
            <select class="data-option" name="option[random]">
                <option value="N" <?= $sRandom == 'N' ? 'selected' : '' ?>><?= Loc::getMessage('RANDOM_VALUE_NO') ?></option>
                <option value="Y" <?= $sRandom == 'Y' ? 'selected' : '' ?>><?= Loc::getMessage('RANDOM_VALUE_YES') ?></option>
            </select>
        </td>
    </tr>
    <? if ($arValues): ?>
        <tr>
            <td width="40%" class="adm-detail-content-cell-l">
                <?= Loc::getMessage('SELECT_VALUE') ?>
            </td>
            <td width="60%" class="adm-detail-content-cell-r">
                <select class="data-option" name="option[selected-value]" size="5" style="width: 50%;">
                    <? foreach ($arValues as $id => $name): ?>
                        <option value="<?= $id ?>" <?= $id == $iSelectedValue ? 'selected' : '' ?>><?= $name ?></option>
                    <? endforeach; ?>
                </select>
            </td>
        </tr>
    <? endif; ?>
    </tbody>
</table>