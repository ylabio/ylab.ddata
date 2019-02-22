<?php
/**
 * @global $arRequest
 * @global $arOptions
 * @global $sPropertyCode
 * @global $this
 */

use Bitrix\Main\Localization\Loc;

Loc::loadMessages(__FILE__);

$sGenerate = $this->sGenerate;
$iCount = $this->iCount;
$sHtmlWrap = $this->sHtmlWrap;
?>
<script type='text/javascript'>
    BX.Ylab.Settings = function(){};
    RegExp.escape = function (text) {
        if (!arguments.callee.sRE) {
            var specials = [
                '\\', '"'
            ];
            arguments.callee.sRE = new RegExp(
                '(\\' + specials.join('|\\') + ')', 'g'
            );
        }
        return text.replace(arguments.callee.sRE, '\\$1');
    };

    BX.bind(BX('lorem-ipsum-wrap'), 'bxchange', function () {
        BX('lorem-ipsum-wrap-hidden').value = RegExp.escape(this.value).replace(/\n/gmi, "\\n");
    });

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
                    if (key == 'wrap') {
                        BX('lorem-ipsum-wrap').value = optionsValue[key];
                        optionsForm.value = RegExp.escape(optionsValue[key]).replace(/\n/gmi, "\\n");
                    } else {
                        optionsForm.value = optionsValue[key];
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
            <?= Loc::getMessage('GENERATE') ?>
        </td>
        <td width="60%" class="adm-detail-content-cell-r">
            <select class="data-option" name="option[generate]">
                <option value="WORDS" <?= $sGenerate == 'WORDS' ? 'selected' : '' ?> ><?= Loc::getMessage('WORDS') ?></option>
                <option value="SENTENCES" <?= $sGenerate == 'SENTENCES' ? 'selected' : '' ?>><?= Loc::getMessage('SENTENCES') ?></option>
                <option value="PARAGRAPHS" <?= $sGenerate == 'PARAGRAPHS' ? 'selected' : '' ?>><?= Loc::getMessage('PARAGRAPHS') ?></option>
            </select>
        </td>
    </tr>
    <tr>
        <td width="40%" class="adm-detail-content-cell-l">
            <?= Loc::getMessage('COUNT') ?>
        </td>
        <td width="60%" class="adm-detail-content-cell-r">
            <input type="text" class="data-option" name="option[count]" value="<?= $iCount ?>"/>
        </td>
    </tr>
    <tr>
        <td width="40%" class="adm-detail-content-cell-l">
            <?= Loc::getMessage('WRAP') ?>
        </td>
        <td width="60%" class="adm-detail-content-cell-r">
            <p><?= Loc::getMessage('HINT') ?></p>
            <textarea id="lorem-ipsum-wrap" cols="30" rows="5"></textarea>
            <input type="hidden" name="option[wrap]" id="lorem-ipsum-wrap-hidden" value="">
        </td>
    </tr>
    </tbody>
</table>