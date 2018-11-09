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
$sSpecialChars = $oData->sSpecialChars;
$iLoginMinLength = $oData->iLoginMinLength;
$iLoginMaxLength = $oData->iLoginMaxLength;
$sDomains = $oData->sDomains;
$sSubDomains = $oData->sSubDomains;
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
                )[0];
                if (optionsForm) {
                    optionsForm.value = optionsValue[key];
                }
            });
        }
    });
</script>
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
