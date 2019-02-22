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

$iMin = $this->iMin;
$iMax = $this->iMax;
$iUserNumber = $this->iUserNumber;
?>
<script type='text/javascript'>
    BX.ready(function () {

        /**
         * Визуально отключаем настройки
         * при заполнении собственного числа
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
        var userNumber = BX('user-number');
        var userNumberInput = BX.findChild(
            userNumber,
            {
                attribute: {
                    'name': 'option[user-number]'
                }
            },
            true,
            false
        );

        addOpacityTr(userParmas, userNumberInput);
        BX.bind(userNumberInput, 'input', function () {
            addOpacityTr(userParmas, this);
        });

        function addOpacityTr(array, userInput) {
            array.forEach(function (tr) {
                if (userInput.value) {
                    BX.style(tr, 'opacity', '0.3');
                } else {
                    BX.style(tr, 'opacity', '1');
                }
                ;
            });
        };
    });
</script>
<table class="adm-detail-content-table edit-table">
    <tbody>
    <tr class="user-param" style="<?= $iUserNumber ? "opacity: 0.3" : "" ?>">
        <td width="40%" class="adm-detail-content-cell-l">
            <?= Loc::getMessage('MIN_LENGTH') ?>
        </td>
        <td width="60%" class="adm-detail-content-cell-r">
            <input type="text" class="data-option" name="option[min]" value="<?= $iMin ?>"/>
        </td>
    </tr>
    <tr class="user-param" style="<?= $iUserNumber ? "opacity: 0.3" : "" ?>">
        <td width="40%" class="adm-detail-content-cell-l">
            <?= Loc::getMessage('MAX_LENGTH') ?>
        </td>
        <td width="60%" class="adm-detail-content-cell-r">
            <input type="text" class="data-option" name="option[max]" value="<?= $iMax ?>"/>
        </td>
    </tr>
    <tr id="user-number">
        <td width="40%" class="adm-detail-content-cell-l">
            <?= Loc::getMessage('USER_NUMBER') ?>
        </td>
        <td width="60%" class="adm-detail-content-cell-r">
            <input type="text" class="data-option" name="option[user-number]" value="<?= $iUserNumber ?>"/>
        </td>
    </tr>
    </tbody>
</table>