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

$sSpecialChars = $this->sSpecialChars;
$iPasswordMinLength = $this->iPasswordMinLength;
$iPasswordMaxLength = $this->iPasswordMaxLength;
?>
<table class="adm-detail-content-table edit-table">
    <tbody>
    <tr>
        <td width="40%" class="adm-detail-content-cell-l">
            <?= Loc::getMessage('SPECIAL_CHARS') ?>
        </td>
        <td width="60%" class="adm-detail-content-cell-r">
            <select class="data-option" name="option[special-chars]">
                <option value="N" <?= $sSpecialChars == 'N' ? $sSpecialChars : '' ?> ><?= Loc::getMessage('CHECK_SPECIAL_CHARS_N') ?></option>
                <option value="Y" <?= $sSpecialChars == 'Y' ? $sSpecialChars : '' ?>><?= Loc::getMessage('CHECK_SPECIAL_CHARS_Y') ?></option>
            </select>
        </td>
    </tr>
    <tr>
        <td width="40%" class="adm-detail-content-cell-l">
            <?= Loc::getMessage('PASSWORD_MIN_LENGHT') ?>
        </td>
        <td width="60%" class="adm-detail-content-cell-r">
            <input type="text" class="data-option" name="option[password-min-length]"
                   value="<?= $iPasswordMinLength ?>"/>
        </td>
    </tr>
    <tr>
        <td width="40%" class="adm-detail-content-cell-l">
            <?= Loc::getMessage('PASSWORD_MAX_LENGHT') ?>
        </td>
        <td width="60%" class="adm-detail-content-cell-r">
            <input type="text" class="data-option" name="option[password-max-length]"
                   value="<?= $iPasswordMaxLength ?>"/>
        </td>
    </tr>
    </tbody>
</table>