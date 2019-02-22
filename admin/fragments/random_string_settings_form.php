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

$sLang = $this->sLang;
$iMinLength = $this->iMinLength;
$iMaxLength = $this->iMaxLength;
$sRegister = $this->sRegister;
$sUserString = $this->sUserString;

?>
<script type='text/javascript'>
    BX.ready(function () {

        /**
         * Визуально отключаем настройки
         * при заполнении собственной строки
         */
        var userParmas = BX.findChild(
            document,
            {
                attribute: {
                    'class': 'user-param'
                }
            },
            true,
            true
        );
        var userString = BX('user-string');
        var userStringInput = BX.findChild(
            userString,
            {
                attribute: {
                    'name': 'option[user-string]'
                }
            },
            true,
            false
        );

        addOpacityTr(userParmas, userStringInput);
        BX.bind(userStringInput, 'input', function () {
            addOpacityTr(userParmas, this);
        });

        function addOpacityTr(array, userInput) {
            array.forEach(function (tr) {
                if (userInput.value) {
                    BX.style(tr, 'opacity', '0.3');
                } else {
                    BX.style(tr, 'opacity', '1');
                }
            });
        }
    });
</script>
<table class="adm-detail-content-table edit-table">
    <tbody>
    <tr class="user-param" style="<?= $sUserString ? "opacity: 0.3" : "" ?>">
        <td width="40%" class="adm-detail-content-cell-l">
            <?= Loc::getMessage('LANG') ?>
        </td>
        <td width="60%" class="adm-detail-content-cell-r">
            <select class="data-option" name="option[lang]">
                <option value=""><?= Loc::getMessage('CHOOSE_LANG') ?></option>
                <option value="EN" <?= $sLang == 'EN' ? 'selected' : '' ?> ><?= Loc::getMessage('LANG_EN') ?></option>
                <option value="RU" <?= $sLang == 'RU' ? 'selected' : '' ?>><?= Loc::getMessage('LANG_RU') ?></option>
            </select>
        </td>
    </tr>
    <tr class="user-param" style="<?= $sUserString ? "opacity: 0.3" : "" ?>">
        <td width="40%" class="adm-detail-content-cell-l">
            <?= Loc::getMessage('MIN_LENGTH') ?>
        </td>
        <td width="60%" class="adm-detail-content-cell-r">
            <input type="text" class="data-option" name="option[min]" value="<?= $iMinLength ?>"/>
        </td>
    </tr>
    <tr class="user-param" style="<?= $sUserString ? "opacity: 0.3" : "" ?>">
        <td width="40%" class="adm-detail-content-cell-l">
            <?= Loc::getMessage('MAX_LENGTH') ?>
        </td>
        <td width="60%" class="adm-detail-content-cell-r">
            <input type="text" class="data-option" name="option[max]" value="<?= $iMaxLength ?>"/>
        </td>
    </tr>
    <tr class="user-param" style="<?= $sUserString ? "opacity: 0.3" : "" ?>">
        <td width="40%" class="adm-detail-content-cell-l">
            <?= Loc::getMessage('CHOOSE_REGISTER') ?>
        </td>
        <td width="60%" class="adm-detail-content-cell-r">
            <select class="data-option" name="option[register]">
                <option value="NO"><?= Loc::getMessage('REGISTER_NO') ?></option>
                <option value="UP" <?= $sLang == 'UP' ? 'selected' : '' ?> ><?= Loc::getMessage('REGISTER_UP') ?></option>
                <option value="LOW" <?= $sLang == 'LOW' ? 'selected' : '' ?>><?= Loc::getMessage('REGISTER_LOW') ?></option>
            </select>
        </td>
    </tr>
    <tr id="user-string">
        <td width="40%" class="adm-detail-content-cell-l">
            <?= Loc::getMessage('USER_STRING') ?>
        </td>
        <td width="60%" class="adm-detail-content-cell-r">
            <input type="text" class="data-option" name="option[user-string]" value="<?= $sUserString ?>"/>
        </td>
    </tr>
    </tbody>
</table>