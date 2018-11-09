<?php
/**
 * @global $arRequest
 * @global $arOptions
 * @global $sPropertyCode
 */

use Bitrix\Main\Localization\Loc;
use Ylab\Ddata\LoadUnits;

Loc::loadMessages(__FILE__);

$oRequest = \Bitrix\Main\Application::getInstance()->getContext()->getRequest();
$sEntityID = $oRequest->get('generator');
$oClasses = new LoadUnits();
$arClassesData = $oClasses->getDataUnits();

$arEntity = [];
foreach ($arClassesData as $arClass) {
    if ($arClass['ID'] == $sEntityID) {
        $arData = $arClass;
    }
}

$oData = new $arData['CLASS']($sProfileID, $sPropertyCode, $sGeneratorID);
$iDateFrom = $oData->iDateFrom;
$iDateTo = $oData->iDateTo;
$sDateFormat = $oData->sDateFormat;
Loc::loadMessages(__FILE__);
?>
<script type='text/javascript'>
    BX.ready(function () {
        var inputOptions = BX.findChild(
            BX(document),
            {
                attribute: {
                    'name': '<?= $sPropertyName ?>[<?= $sGeneratorID ?>]'
                }
            },
            true,
            true
        )[0];
        console.log(inputOptions);
        if (inputOptions) {
            var optionsValue = JSON.parse(inputOptions.value);
        }
        if (inputOptions != undefined) {
            Object.keys(optionsValue).forEach(function (key, item) {
                var optionsForm = BX.findChild(
                    BX('WindowEntityDataForm'),
                    {
                        attribute: {
                            'name': 'option[' + key + ']'
                        }
                    },
                    true,
                    true
                );
                if (optionsForm.length > 0) {
                    if (key == 'date_format') {
                        optionsForm.forEach(function (key1, item1) {
                            if (key1.value == optionsValue[key]) {
                                key1.checked = true;
                            }
                        });
                    } else {
                        optionsForm[0].value = optionsValue[key];
                    }
                }
            });
        }
    });
</script>
<table class="adm-detail-content-table edit-table">
    <tbody>
    <tr>
        <td width="40%" class="adm-detail-content-cell-l">
            <?= Loc::getMessage('DATE_FROM') ?>
        </td>
        <td width="60%" class="adm-detail-content-cell-r">
            <input type="text" class="data-option" name="option[date_from]"
                   onclick="BX.calendar({node: this, field: this, bTime: true});" value="<?= $iDateFrom ?>">
        </td>
    </tr>
    <tr>
        <td width="40%" class="adm-detail-content-cell-l">
            <?= Loc::getMessage('DATE_TO') ?>
        </td>
        <td width="60%" class="adm-detail-content-cell-r">
            <input type="text" class="data-option" name="option[date_to]"
                   onclick="BX.calendar({node: this, field: this, bTime: true});" value="<?= $iDateTo ?>">
        </td>
    </tr>
    <tr>
        <td width="40%" class="adm-detail-content-cell-l">
            <?= Loc::getMessage('FORMAT_DATE') ?>
        </td>
        <td width="60%" class="adm-detail-content-cell-r">
            <input type="radio" class="data-option" name="option[date_format]"
                   value="FULL" <?= $sDateFormat == 'FULL' ? 'checked' : '' ?>>
            <label for="option[date_format]"><?= Loc::getMessage('FORMAT_DATE_FULL') ?></label>
        </td>
    </tr>
    <tr>
        <td width="40%" class="adm-detail-content-cell-l"></td>
        <td width="60%" class="adm-detail-content-cell-r">
            <input type="radio" class="data-option" name="option[date_format]"
                   value="SHORT" <?= $sDateFormat == 'SHORT' ? 'checked' : '' ?>>
            <label for="option[date_format]"><?= Loc::getMessage('FORMAT_DATE_SHORT') ?></label>
        </td>
    </tr>
    </tbody>
</table>

