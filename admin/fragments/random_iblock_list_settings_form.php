<?
/**
 * @global $arRequest
 * @global $arOptions
 * @global $arItemList
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