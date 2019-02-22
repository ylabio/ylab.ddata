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
$iLoginMinLength = $this->iLoginMinLength;
$iLoginMaxLength = $this->iLoginMaxLength;
$sDomains = $this->sDomains;
$sSubDomains = $this->sSubDomains;
?>
<table class="adm-detail-content-table edit-table">
    <tbody>
    <tr>
        <td width="40%" class="adm-detail-content-cell-l">
            <?= Loc::getMessage('SPECIAL_CHARS') ?>
        </td>
        <td width="60%" class="adm-detail-content-cell-r">
            <select class="data-option" name="option[special-chars]">
                <option value="N" <?= $sSpecialChars == 'N' ? 'selected' : '' ?>><?= Loc::getMessage('CHECK_SPECIAL_CHARS_N') ?></option>
                <option value="Y" <?= $sSpecialChars == 'Y' ? 'selected' : '' ?>><?= Loc::getMessage('CHECK_SPECIAL_CHARS_Y') ?></option>
            </select>
        </td>
    </tr>
    <tr>
        <td width="40%" class="adm-detail-content-cell-l">
            <?= Loc::getMessage('LOGIN_MIN_LENGHT') ?>
        </td>
        <td width="60%" class="adm-detail-content-cell-r">
            <input type="text" class="data-option" name="option[login-min-length]" value="<?= $iLoginMinLength ?>"/>
        </td>
    </tr>
    <tr>
        <td width="40%" class="adm-detail-content-cell-l">
            <?= Loc::getMessage('LOGIN_MAX_LENGHT') ?>
        </td>
        <td width="60%" class="adm-detail-content-cell-r">
            <input type="text" class="data-option" name="option[login-max-length]" value="<?= $iLoginMaxLength ?>"/>
        </td>
    </tr>
    <tr>
        <td width="40%" class="adm-detail-content-cell-l">
            <?= Loc::getMessage('DOMAINS') ?>
        </td>
        <td width="60%" class="adm-detail-content-cell-r">
            <input type="text" class="data-option" name="option[domains]" style="width: 90%;" value="<?= $sDomains ?>"/>
        </td>
    </tr>
    <tr>
        <td width="40%" class="adm-detail-content-cell-l">
            <?= Loc::getMessage('SUB_DOMAINS') ?>
        </td>
        <td width="60%" class="adm-detail-content-cell-r">
            <input type="text" class="data-option" name="option[sub-domains]" style="width: 90%;"
                   value="<?= $sSubDomains ?>"/>
        </td>
    </tr>
    </tbody>
</table>