<?
/**
 * @global $arRequest
 * @global $arOptions
 */

use Bitrix\Main\Localization\Loc;

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

                var optionsFormMultiple = BX.findChild(
                    BX('WindowEntityDataForm'),
                    {
                        attribute: {
                            'name': 'option[' + key + '][]'
                        }
                    },
                    true,
                    true
                )[0];
                if (optionsFormMultiple) {

                    var optionsForms = optionsFormMultiple.options;

                    for (var i = 0; i < optionsForms.length; i++) {

                        for (var j = 0; j < optionsValue[key].length; j++) {

                            if (optionsForms[i].value == optionsValue[key][j]) {

                                optionsForms[i].selected = true;
                            }
                        }
                    }
                }
            });
        }
    });
</script>
<table class="adm-detail-content-table edit-table">
    <td colspan="2" align="center">

        <div class="adm-info-message-wrap" align="center">
            <div class="adm-info-message">
                <p><?= Loc::getMessage("YLAB_DDATA_SECTION_GENERATE_HELP_MESSAGE") ?></p>
            </div>
        </div>
    </td>
    <tr>
        <td width="40%" class="adm-detail-content-cell-l">
            <?= Loc::getMessage('GENERATE_RANDOM') ?>
        </td>
        <td width="60%" class="adm-detail-content-cell-r">
            <select class="data-option" name="option[random]">
                <option value="Y" <?=($arOptions['random'] == 'Y' ? 'selected' : '')?> ><?= Loc::getMessage('RANDOM_VALUE_YES') ?></option>
                <option value="N" <?=($arOptions['random'] == 'N' ? 'selected' : '')?> ><?= Loc::getMessage('RANDOM_VALUE_NO') ?></option>
            </select>
        </td>
    </tr>
    <tr>
        <td width="40%" class="adm-detail-content-cell-l">
            <?= Loc::getMessage('SELECT_SECTIONS') ?>
        </td>
        <td width="60%" class="adm-detail-content-cell-r">
            <select class="data-option" name="option[selected-sections][]" multiple size="5" style="width: 50%;">
                <? if ($arSection): ?>
                    <? foreach ($arSection as $id => $value): ?>
                        <option value="<?= $id ?>"><?= str_repeat(" . ", $value[1]) ?><?= $value[0] ?></option>
                    <? endforeach; ?>
                <? else: ?>
                    <option value=""><?= Loc::getMessage('NO_SECTIONS') ?></option>
                <? endif; ?>
            </select>
        </td>
    </tr>
</table>