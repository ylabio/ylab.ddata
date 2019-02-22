<?php
/**
 * @global $arRequest
 * @global $arOptions
 * @global $arItemList
 */

use Bitrix\Main\Localization\Loc;

Loc::loadMessages(__FILE__);
?>
<table class="adm-detail-content-table edit-table">
    <tr>
        <td width="40%" class="adm-detail-content-cell-l">
            <?= Loc::getMessage('GENERATE_RANDOM') ?>
        </td>
        <td width="60%" class="adm-detail-content-cell-r">
            <select class="data-option" name="option[random]">
                <option value="N"><?= Loc::getMessage('RANDOM_VALUE_NO') ?></option>
                <option value="Y"><?= Loc::getMessage('RANDOM_VALUE_YES') ?></option>
            </select>
        </td>
    </tr>
    <tr>
        <td width="40%" class="adm-detail-content-cell-l">
            <?= Loc::getMessage('SELECT_ITEM') ?>
        </td>
        <td width="60%" class="adm-detail-content-cell-r">
            <select class="data-option" name="option[selected-items][]" multiple size="5" style="width: 50%;">
                <? if ($arItemList): ?>
                    <? foreach ($arItemList as $id => $name): ?>
                        <option value="<?= $id ?>"><?= $name ?></option>
                    <? endforeach; ?>
                <? else: ?>
                    <option value=""><?= Loc::getMessage('NO_ITEMS') ?></option>
                <? endif; ?>
            </select>
        </td>
    </tr>
</table>