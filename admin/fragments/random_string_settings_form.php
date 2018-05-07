<?
/**
 * @global $arRequest
 * @global $arOptions
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

$oData = new $arData['CLASS']();
$sLang = $oData->sLang;
$iMinLength = $oData->iMinLength;
$iMaxLength = $oData->iMaxLength;
$sRegister = $oData->sRegister;
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
    <tr>
        <td width="40%" class="adm-detail-content-cell-l">
            <?= Loc::getMessage('MIN_LENGTH') ?>
        </td>
        <td width="60%" class="adm-detail-content-cell-r">
            <input type="text" class="data-option" name="option[min]" value="<?= $iMinLength ?>"/>
        </td>
    </tr>
    <tr>
        <td width="40%" class="adm-detail-content-cell-l">
            <?= Loc::getMessage('MAX_LENGTH') ?>
        </td>
        <td width="60%" class="adm-detail-content-cell-r">
            <input type="text" class="data-option" name="option[max]" value="<?= $iMaxLength ?>"/>
        </td>
    </tr>
    <tr>
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
    </tbody>
</table>

