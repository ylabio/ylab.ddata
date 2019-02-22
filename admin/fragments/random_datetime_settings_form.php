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



$iDateFrom = $this->iDateFrom;
$iDateTo = $this->iDateTo;
$sDateFormat = $this->sDateFormat;
Loc::loadMessages(__FILE__);
?>
<table class="adm-detail-content-table edit-table">
    <tbody>
    <tr>
        <td width="40%" class="adm-detail-content-cell-l">
            <?= Loc::getMessage('DATE_FROM') ?>
        </td>
        <td width="60%" class="adm-detail-content-cell-r">
            <input type="text" class="data-option" name="option[date_from]"
                   onclick="BX.calendar({node: this, field: this, bTime: true});" value="<?= $iDateFrom ?>">
        </td>
    </tr>
    <tr>
        <td width="40%" class="adm-detail-content-cell-l">
            <?= Loc::getMessage('DATE_TO') ?>
        </td>
        <td width="60%" class="adm-detail-content-cell-r">
            <input type="text" class="data-option" name="option[date_to]"
                   onclick="BX.calendar({node: this, field: this, bTime: true});" value="<?= $iDateTo ?>">
        </td>
    </tr>
    <tr>
        <td width="40%" class="adm-detail-content-cell-l">
            <?= Loc::getMessage('FORMAT_DATE') ?>
        </td>
        <td width="60%" class="adm-detail-content-cell-r">
            <input type="radio" class="data-option" name="option[date_format]"
                   value="FULL" <?= $sDateFormat == 'FULL' ? 'checked' : '' ?>>
            <label for="option[date_format]"><?= Loc::getMessage('FORMAT_DATE_FULL') ?></label>
        </td>
    </tr>
    <tr>
        <td width="40%" class="adm-detail-content-cell-l"></td>
        <td width="60%" class="adm-detail-content-cell-r">
            <input type="radio" class="data-option" name="option[date_format]"
                   value="SHORT" <?= $sDateFormat == 'SHORT' ? 'checked' : '' ?>>
            <label for="option[date_format]"><?= Loc::getMessage('FORMAT_DATE_SHORT') ?></label>
        </td>
    </tr>
    </tbody>
</table>