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
$sGender = $this->sGender;
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
    <tr>
        <td width="40%" class="adm-detail-content-cell-l">
            <?= Loc::getMessage('SELECT_GENDER') ?>
        </td>
        <td width="60%" class="adm-detail-content-cell-r">
            <select class="data-option" name="option[gender]">
                <option value="M" <?= $sGender == 'M' ? 'selected' : '' ?>><?= Loc::getMessage('GENDER_MALE') ?></option>
                <option value="F" <?= $sGender == 'F' ? 'selected' : '' ?>><?= Loc::getMessage('GENDER_FEMALE') ?></option>
            </select>
        </td>
    </tr>
    </tbody>
</table>