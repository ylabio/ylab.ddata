<?php
/**
 * @global $sGeneratorID
 * @global $sProfileID
 * @global $sPropertyName
 * @global $this
 */

use Bitrix\Main\Localization\Loc;

Loc::loadMessages(__FILE__);

$iWidth = $this->iWidth;
$iHeight = $this->iHeight;
?>
<table class="adm-detail-content-table edit-table">
    <tbody>
    <tr>
        <td width="40%" class="adm-detail-content-cell-l">
            <?= Loc::getMessage('PICTURE_WIDTH') ?>
        </td>
        <td width="60%" class="adm-detail-content-cell-r">
            <input type="text" class="data-option" name="option[width]" value="<?= $iWidth ?>"/>
        </td>
    </tr>
    <tr>
        <td width="40%" class="adm-detail-content-cell-l">
            <?= Loc::getMessage('PICTURE_HEIGHT') ?>
        </td>
        <td width="60%" class="adm-detail-content-cell-r">
            <input type="text" class="data-option" name="option[height]" value="<?= $iHeight ?>"/>
        </td>
    </tr>
    </tbody>
</table>