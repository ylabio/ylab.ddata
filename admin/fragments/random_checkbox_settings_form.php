<?php
/**
 * @global $sGeneratorID
 * @global $sProfileID
 * @global $sPropertyCode
 * @global $sPropertyName
 * @global $this
 */

use Bitrix\Main\Localization\Loc;

Loc::loadMessages(__FILE__);

$sRandom = $this->sRandom;
$sCheckbox = $this->sCheckbox;
?>
<table class="adm-detail-content-table edit-table">
    <tbody>
    <tr>
        <td width="40%" class="adm-detail-content-cell-l">
            <?= Loc::getMessage('GENERATE_RANDOM') ?>
        </td>
        <td width="60%" class="adm-detail-content-cell-r">
            <select class="data-option" name="option[random]">
                <option value="N" <?= $sRandom == 'N' ? 'selected' : '' ?>><?= Loc::getMessage('CHECK_N') ?></option>
                <option value="Y" <?= $sRandom == 'Y' ? 'selected' : '' ?>><?= Loc::getMessage('CHECK_Y') ?></option>
            </select>
        </td>
    </tr>
    <tr>
        <td width="40%" class="adm-detail-content-cell-l">
            <?= Loc::getMessage('SELECT_CHECBOX') ?>
        </td>
        <td width="60%" class="adm-detail-content-cell-r">
            <select class="data-option" name="option[checkbox]">
                <option value="Y" <?= $sCheckbox == 'Y' ? 'selected' : '' ?>><?= Loc::getMessage('CHECK_Y') ?></option>
                <option value="N" <?= $sCheckbox == 'N' ? 'selected' : '' ?>><?= Loc::getMessage('CHECK_N') ?></option>
            </select>
        </td>
    </tr>
    </tbody>
</table>