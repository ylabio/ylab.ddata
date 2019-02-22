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


$arCode = $this->arCode;
$arNumbersStart = $this->arStart;
$arNumbersFinish = $this->arFinish;
$sCountryCode = $this->sCountryCode;
$sGenerationOption = $this->sGenerationOption;
$arRangeNumbers = $this->arRangeNumbers;
?>
<script type='text/javascript'>
    BX.ready(function () {
        var generationConstructorWrapper = BX.findChild(
            document,
            {
                attribute: {
                    'class': 'generation-constructor'
                }
            },
            true,
            true
        );
        var generationRangeWrapper = BX.findChild(
            document,
            {
                attribute: {
                    'class': 'generation-range'
                }
            },
            true,
            true
        );
        BX.bind(BX('generation'), 'change', function () {
            changeGenerationOption(this.value)
        });
        changeGenerationOption(BX('generation').value);

        function changeGenerationOption(sOption) {
            if (sOption === 'constructor') {
                generationRangeWrapper.forEach(function (value) {
                    BX.style(value, 'display', 'none')
                });
                generationConstructorWrapper.forEach(function (value) {
                    BX.style(value, 'display', 'table-row')
                });
            } else if (sOption === 'range') {
                generationRangeWrapper.forEach(function (value) {
                    BX.style(value, 'display', 'table-row')
                });
                generationConstructorWrapper.forEach(function (value) {
                    BX.style(value, 'display', 'none')
                });
            } else {
                generationRangeWrapper.forEach(function (value) {
                    BX.style(value, 'display', 'none')
                });
                generationConstructorWrapper.forEach(function (value) {
                    BX.style(value, 'display', 'none')
                });
            }
        }
    });
</script>
<table class="adm-detail-content-table edit-table">
    <tbody>
    <tr>
        <td width="40%"
            class="adm-detail-content-cell-l"><?= Loc::getMessage('YLAB_SMS_MOBILE_NUMBER_COUNTRY_CODE') ?></td>
        <td width="60%" class="adm-detail-content-cell-r">
            <input type="text" name="option[country-code]" value="<?= $sCountryCode ?>">
        </td>
    </tr>
    <tr>
        <td width="40%"
            class="adm-detail-content-cell-l"><?= Loc::getMessage("YLAB_SMS_MOBILE_NUMBER_GENERATION_OPTION") ?></td>
        <td width="60%" class="adm-detail-content-cell-r">
            <select name="option[generation-option]" id="generation">
                <option value=""><?= Loc::getMessage("YLAB_DDATA_MOBILE_NUMBER_CHOOSE_VALUE") ?></option>
                <option value="constructor" <?= $sGenerationOption == 'constructor' ? 'selected' : '' ?>><?= Loc::getMessage('YLAB_SMS_MOBILE_NUMBER_GENERATION_OPTION_CONSTRUCTOR') ?></option>
                <option value="range" <?= $sGenerationOption == 'range' ? 'selected' : '' ?>><?= Loc::getMessage('YLAB_SMS_MOBILE_NUMBER_GENERATION_OPTION_RANGE') ?></option>
            </select>
        </td>
    </tr>
    <tr class="generation-constructor"
        style="display: <?= $sGenerationOption == 'constructor' ? 'table-row' : 'none' ?>">
        <td width="40%" class="adm-detail-content-cell-l"><?= Loc::getMessage('YLAB_DDATA_MOBILE_NUMBER_CODE') ?></td>
        <td width="60%" class="adm-detail-content-cell-r">
            <?= Loc::getMessage("YLAB_DDATA_MOBILE_NUMBER_FROM") ?>
            <input type="text" class="data-option" name="option[code-from]" maxlength="3" value="<?= $arCode[0] ?>"/>
            <?= Loc::getMessage("YLAB_DDATA_MOBILE_NUMBER_TO") ?>
            <input type="text" class="data-option" name="option[code-to]" maxlength="3" value="<?= $arCode[1] ?>"/>
        </td>
    </tr>
    <tr class="generation-constructor"
        style="display: <?= $sGenerationOption == 'constructor' ? 'table-row' : 'none' ?>">
        <td width="40%"
            class="adm-detail-content-cell-l"><?= Loc::getMessage('YLAB_DDATA_MOBILE_NUMBER_START_NUMBERS') ?></td>
        <td width="60%" class="adm-detail-content-cell-r">
            <?= Loc::getMessage("YLAB_DDATA_MOBILE_NUMBER_FROM") ?>
            <input type="text" class="data-option" name="option[number-start-from]" maxlength="3"
                   value="<?= $arNumbersStart[0] ?>"/>
            <?= Loc::getMessage("YLAB_DDATA_MOBILE_NUMBER_TO") ?>
            <input type="text" class="data-option" name="option[number-start-to]" maxlength="3"
                   value="<?= $arNumbersStart[1] ?>"/>
        </td>
    </tr>
    <tr class="generation-constructor"
        style="display: <?= $sGenerationOption == 'constructor' ? 'table-row' : 'none' ?>">
        <td width="40%"
            class="adm-detail-content-cell-l"><?= Loc::getMessage('YLAB_DDATA_MOBILE_NUMBER_FINISH_NUMBERS') ?></td>
        <td width="60%" class="adm-detail-content-cell-r">
            <?= Loc::getMessage("YLAB_DDATA_MOBILE_NUMBER_FROM") ?>
            <input type="text" class="data-option" name="option[number-finish-from]" maxlength="4"
                   value="<?= $arNumbersFinish[0] ?>"/>
            <?= Loc::getMessage("YLAB_DDATA_MOBILE_NUMBER_TO") ?>
            <input type="text" class="data-option" name="option[number-finish-to]" maxlength="4"
                   value="<?= $arNumbersFinish[1] ?>"/>
        </td>
    </tr>
    <tr class="generation-range" style="display: <?= $sGenerationOption == 'range' ? 'table-row' : 'none' ?>">
        <td width="40%"
            class="adm-detail-content-cell-l"><?= Loc::getMessage('YLAB_SMS_MOBILE_NUMBER_RANGE_NUMBERS') ?></td>
        <td width="60%" class="adm-detail-content-cell-r">
            <?= Loc::getMessage("YLAB_DDATA_MOBILE_NUMBER_FROM") ?>
            <input type="text" class="data-option" name="option[range-from]" minlength="10"
                   value="<?= $arRangeNumbers[0] ?>"/>
            <?= Loc::getMessage("YLAB_DDATA_MOBILE_NUMBER_TO") ?>
            <input type="text" class="data-option" name="option[range-to]" minlength="10"
                   value="<?= $arRangeNumbers[1] ?>"/>
        </td>
    </tr>
    <td colspan="2" align="center">
        <div class="adm-info-message-wrap" align="center">
            <div class="adm-info-message">
                <p><?= Loc::getMessage('YLAB_DDATA_MOBILE_NUMBER_HELPER_1') ?></p>
                <p><?= Loc::getMessage('YLAB_DDATA_MOBILE_NUMBER_HELPER_2') ?></p>
                <p><?= Loc::getMessage('YLAB_DDATA_MOBILE_NUMBER_HELPER_3') ?></p>
                <p><?= Loc::getMessage('YLAB_DDATA_MOBILE_NUMBER_HELPER_4') ?></p>
            </div>
        </div>
    </td>
    </tbody>
</table>