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
$sLang = $oData->sLang;
$iWidth = $oData->iWidth;
$iHeight = $oData->iHeight;
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
            <?= Loc::getMessage('PICTURE_WIDTH') ?>
        </td>
        <td width="60%" class="adm-detail-content-cell-r">
            <input type="text" class="data-option" name="option[width]" value="<?=$iWidth?>"/>
        </td>
    </tr>
    <tr>
        <td width="40%" class="adm-detail-content-cell-l">
            <?= Loc::getMessage('PICTURE_HEIGHT') ?>
        </td>
        <td width="60%" class="adm-detail-content-cell-r">
            <input type="text" class="data-option" name="option[height]" value="<?=$iHeight?>"/>
        </td>
    </tr>
    </tbody>
</table>
