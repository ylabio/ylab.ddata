<?php
/**
 * @global $request
 * @global $arRequest
 * @global $sGeneratorID
 * @global $sProfileID
 * @global $sPropertyCode
 * @global $sPropertyName
 * @global $this
 */

use Bitrix\Main\Localization\Loc;

Loc::loadMessages(__FILE__);

$arHLBlocks = $this->arHLBlocks;

$arFields = [];
if ($request->isPost()) {
    if (!empty($arRequest['option']['hlblock'])) {
        $arFields = $this::getHLBlockFields($arRequest['option']['hlblock']);
        if (isset($arRequest['option']['field']) && $arRequest['option']['field'] != "") {
            $arFieldElements = $this::getHLBlockElements($arRequest['option']['hlblock'], $arFields[$arRequest['option']['field']]);
        }
    }
}

if ($request->isPost() && $arRequest['save-data']) {
    if (isset($arRequest['hlblock']) && $arRequest['hlblock'] != '') {
        $arFields = $this::getHLBlockFields($arRequest['hlblock']);
        $arFieldElements = $this::getHLBlockElements($arRequest['hlblock'], $arRequest['field']);

        echo(json_encode([
            'fields' => $arFields,
            'elements' => $arFieldElements
        ]));
        die();
    }
}
?>
<script type='text/javascript'>
    BX.Ylab.Settings = function(){};
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

            if (Object.keys(optionsValue).length !== 0) {

                if (optionsValue.hlblock) {
                    BX.style(BX('wr-fields-selector'), 'display', 'table-row');
                }

                if (optionsValue.field && optionsValue.elements) {
                    BX.style(BX('wr-elements-selector'), 'display', 'table-row');
                }

                var post = {},
                    action = window.YlabDdata.WindowEntityDataForm.PARAMS.content_url;
                post['save-data'] = 'Y';
                post['hlblock'] = optionsValue.hlblock;
                post['field'] = optionsValue.field;

                BX.ajax.post(
                    action,
                    post,
                    function (data) {
                        var data = JSON.parse(data);

                        var sSelectHtmlFeilds = '<option value="">--</option>';
                        for (var index in data.fields) {
                            if (optionsValue.field != undefined && index == optionsValue.field) {
                                sSelectHtmlFeilds = sSelectHtmlFeilds + '<option value="' + index + '" selected>' + data.fields[index] + '</option>';
                            } else {
                                sSelectHtmlFeilds = sSelectHtmlFeilds + '<option value="' + index + '">' + data.fields[index] + '</option>';
                            }
                        }
                        document.getElementById('fields-selector').innerHTML = sSelectHtmlFeilds;

                        var sSelectHtmlElements = '';
                        for (var index in data.elements) {
                            if (data.elements[index].VALUE == null) {
                                data.elements[index].VALUE = '';
                            }

                            if (optionsValue.elements.indexOf(data.elements[index].ID) != -1) {
                                sSelectHtmlElements = sSelectHtmlElements + '<option value="' + data.elements[index].ID + '" selected>[' + data.elements[index].ID + '] ' + data.elements[index].VALUE + '</option>';
                            } else {
                                sSelectHtmlElements = sSelectHtmlElements + '<option value="' + data.elements[index].ID + '">[' + data.elements[index].ID + '] ' + data.elements[index].VALUE + '</option>';
                            }
                        }
                        document.getElementById('elements-selector').innerHTML = sSelectHtmlElements;
                    }
                );
            }
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
            <select class="data-option" name="option[random]" id="random-selector">
                <option value="N" <?= $arRequest['option']['random'] == 'N' ? 'selected' : '' ?>><?= Loc::getMessage('RANDOM_VALUE_NO') ?></option>
                <option value="Y" <?= $arRequest['option']['random'] == 'Y' ? 'selected' : '' ?>><?= Loc::getMessage('RANDOM_VALUE_YES') ?></option>
            </select>
        </td>
    </tr>
    <tr>
        <td width="40%" class="adm-detail-content-cell-l">
            <?= Loc::getMessage('CHOOSE_HLBLOCK') ?>
        </td>
        <td width="60%" class="adm-detail-content-cell-r">
            <select name="option[hlblock]" id="hlblock-selector" style="width: 50%;">
                <option value="">--</option>
                <? foreach ($arHLBlocks as $key => $arHLBlock): ?>
                    <option value="<?= $arHLBlock['ID'] ?>" <?= ($arHLBlock['ID'] === $arRequest['option']['hlblock'] ? "selected" : "") ?>><?= $arHLBlock['NAME'] ?></option>
                <? endforeach; ?>
            </select>
        </td>
    </tr>
    <tr style="<?= ($arFields ? "" : "display:none;") ?>" id="wr-fields-selector">
        <td width="40%" class="adm-detail-content-cell-l">
            <?= Loc::getMessage('CHOOSE_FIELD') ?>
        </td>
        <td width="60%" class="adm-detail-content-cell-r">
            <select class="data-option" name="option[field]" style="width: 50%;"
                    id="fields-selector">
                <option value="">--</option>
                <? foreach ($arFields as $iId => $sName): ?>
                    <option value="<?= $iId ?>" <?= ($arRequest['option']['field'] != '' && $iId == $arRequest['option']['field'] ? "selected" : "") ?>><?= $sName ?></option>
                <? endforeach; ?>
            </select>
        </td>
    </tr>
    <tr style="<?= ($arFieldElements ? "" : "display:none;") ?>" id="wr-elements-selector">
        <td width="40%" class="adm-detail-content-cell-l">
            <?= Loc::getMessage('CHOOSE_ELEMENTS') ?>
        </td>
        <td width="60%" class="adm-detail-content-cell-r">
            <select id="elements-selector" class="data-option" name="option[elements][]" multiple
                    size="5" style="width: 50%;">
                <? foreach ($arFieldElements as $arFieldElement): ?>
                    <option value="<?= $arFieldElement['ID'] ?>">
                        [<?= $arFieldElement['ID'] ?>]
                        <?= $arFieldElement['VALUE'] ?>
                    </option>
                <? endforeach; ?>
            </select>
        </td>
    </tr>
</table>
<script>
    BX.bind(BX('hlblock-selector'), 'change', function () {
        var inputOptions = BX.findChild(
            BX(document),
            {
                attribute: {
                    'name': '<?= $sPropertyName ?>[<?= $sGeneratorID ?>]'
                }
            },
            true,
            false
        );
        var optionsValue = JSON.parse(inputOptions.value);

        delete optionsValue.hlblock;
        delete optionsValue.field;
        delete optionsValue.elements;

        inputOptions.value = JSON.stringify(optionsValue);

        BX('fields-selector').innerHTML = '';
        BX('elements-selector').innerHTML = '';
        BX.style(BX('wr-fields-selector'), 'display', 'none');
        BX.style(BX('wr-elements-selector'), 'display', 'none');

        window.YlabDdata.WindowEntityDataForm.PostParameters();
    });
    BX.bind(BX('fields-selector'), 'change', function () {
        var inputOptions = BX.findChild(
            BX(document),
            {
                attribute: {
                    'name': '<?= $sPropertyName ?>[<?= $sGeneratorID ?>]'
                }
            },
            true,
            false
        );
        var optionsValue = JSON.parse(inputOptions.value);

        delete optionsValue.elements;

        var selectedIndexField = BX('fields-selector').options.selectedIndex;
        optionsValue.field = BX('fields-selector').options[selectedIndexField].value;
        inputOptions.value = JSON.stringify(optionsValue);

        window.YlabDdata.WindowEntityDataForm.PostParameters();
    });
</script>