<?php
/**
 * @global $sGeneratorID
 * @global $sProfileID
 * @global $sPropertyCode
 * @global $sPropertyName
 * @global $arGroupList
 * @global $this
 */

use Bitrix\Main\Localization\Loc;

Loc::loadMessages(__FILE__);



$sRandom = $this->sRandom;
$arSelectedGroups = $this->arSelectedGroups;
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
    <? if ($arGroupList): ?>
        <tr>
            <td width="40%" class="adm-detail-content-cell-l">
                <?= Loc::getMessage('SELECT_GROUPS') ?>
            </td>
            <td width="60%" class="adm-detail-content-cell-r">
                <select class="data-option" name="option[selected-groups][]" multiple size="5" style="width: 50%;">
                    <? foreach ($arGroupList as $group): ?>
                        <option value="<?= $group['ID'] ?>" <?= in_array($group['ID'],
                            $arSelectedGroups) ? 'selected' : '' ?>><?= $group['NAME'] ?></option>
                    <? endforeach; ?>
                </select>
            </td>
        </tr>
    <? endif; ?>
    </tbody>
</table>