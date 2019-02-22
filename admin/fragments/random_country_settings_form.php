<?php
/**
 * @global $sGeneratorID
 * @global $sProfileID
 * @global $sPropertyCode
 * @global $sPropertyName
 * @global $this
 * @global $arCountries
 */

use Bitrix\Main\Localization\Loc;

Loc::loadMessages(__FILE__);


$sRandom = $this->sRandom;
$arSelectedCountries = $this->arSelectedCountries;
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
    <? if ($arCountries): ?>
        <tr>
            <td width="40%" class="adm-detail-content-cell-l">
                <?= Loc::getMessage('SELECT_COUNTRIES') ?>
            </td>
            <td width="60%" class="adm-detail-content-cell-r">
                <select class="data-option" name="option[selected-countries][]" multiple size="5" style="width: 50%;">
                    <? foreach ($arCountries as $id => $name): ?>
                        <option value="<?= $id ?>" <?= $id == $arSelectedCountries[0] ? 'selected' : '' ?>><?= $name ?></option>
                    <? endforeach; ?>
                </select>
            </td>
        </tr>
    <? endif; ?>
    </tbody>
</table>