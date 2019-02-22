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

$arIBlocks = $this->arIBlocks;
$arIBlockElements = $this->arIBlockElements;
$sRandom = $this->sRandom;
$arSelectedElements = $this->arSelectedElements;
?>
<script type='text/javascript'>
    BX.ready(function () {
        BX.Ylab.Settings = function(){};
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
                if (key == 'random') {
                    if (optionsValue[key] == 'Y') {
                        document.getElementById('iblock-elements-selector').multiple = true;
                    } else {
                        document.getElementById('iblock-elements-selector').multiple = false;
                    }
                }
            });
        }
        BX.bind(BX('random-selector'), 'change', function () {
            if (this.value == 'N') {
                document.getElementById('iblock-elements-selector').multiple = false;
            } else {
                document.getElementById('iblock-elements-selector').multiple = true;
            }
        });
        BX.bind(BX('iblock-selector'), 'change', function () {
            var iIBlockID = this.value;
            var sRandom = document.getElementById('random-selector').value;
            var arIBlockElements = <?=json_encode($arIBlockElements)?>;
            document.getElementById('iblock-elements-selector').innerHTML = '';
            var arResultElements = [];
            var iElementIBlockID = 0;
            arIBlockElements.forEach(function (item, key) {
                iElementIBlockID = arIBlockElements[key]['IBLOCK_ID'];
                if (iIBlockID == iElementIBlockID) {
                    arResultElements.push(arIBlockElements[key]);
                }
            });
            var sSelectHtml = '';
            if (arResultElements.length > 0) {
                arResultElements.forEach(function (elementKey, elementValue) {
                    sSelectHtml = sSelectHtml + '<option value="' + arResultElements[elementValue]["ID"] + '">' + arResultElements[elementValue]["NAME"] + '</option>';
                });
            } else {
                sSelectHtml = '<option><?=Loc::getMessage('EMPTY_ELEMENTS')?></option>';
            }
            document.getElementById('iblock-elements-selector').innerHTML = sSelectHtml;
        });
    })
</script>
<table class="adm-detail-content-table edit-table">
    <tbody>
    <tr>
        <td width="40%" class="adm-detail-content-cell-l">
            <?= Loc::getMessage('GENERATE_RANDOM') ?>
        </td>
        <td width="60%" class="adm-detail-content-cell-r">
            <select class="data-option" name="option[random]" id="random-selector">
                <option value="N" <?= $sRandom == 'N' ? 'selected' : '' ?>><?= Loc::getMessage('RANDOM_VALUE_NO') ?></option>
                <option value="Y" <?= $sRandom == 'Y' ? 'selected' : '' ?>><?= Loc::getMessage('RANDOM_VALUE_YES') ?></option>
            </select>
        </td>
    </tr>
    <tr>
        <td width="40%" class="adm-detail-content-cell-l">
            <?= Loc::getMessage('CHOOSE_IBLOCK') ?>
        </td>
        <td width="60%" class="adm-detail-content-cell-r">
            <select name="option[iblock]" id="iblock-selector" size="5" style="width: 50%;">
                <? foreach ($arIBlocks as $key => $arIBlock): ?>
                    <option value="<?= $arIBlock['ID'] ?>" <?= ($key == 0 ? "selected" : "") ?> ><?= $arIBlock['NAME'] ?></option>
                <? endforeach; ?>
            </select>
        </td>
    </tr>
    <tr>
        <td width="40%" class="adm-detail-content-cell-l">
            <?= Loc::getMessage('CHOOSE_ELEMENTS') ?>
        </td>
        <td width="60%" class="adm-detail-content-cell-r">
            <select class="data-option" name="option[selected-elements][]" size="5" multiple style="width: 50%;"
                    id="iblock-elements-selector">
                <? foreach ($arIBlockElements as $arIBlockElement): ?>
                    <option value="<?= $arIBlockElement['ID'] ?>" <?= in_array($arIBlockElement['ID'],
                        $arSelectedElements) ? 'selected' : '' ?>><?= $arIBlockElement['NAME'] ?></option>
                <? endforeach; ?>
            </select>
        </td>
    </tr>
    </tbody>
</table>