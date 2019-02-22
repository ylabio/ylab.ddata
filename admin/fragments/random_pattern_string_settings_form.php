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
                    if (key == 'pattern') {
                        BX('string-pattern-wrap').value = optionsValue[key];
                        optionsForm.value = RegExp.escape(optionsValue[key]);
                    } else {
                        optionsForm.value = optionsValue[key];
                    }
                }
            });
        }

        BX.bind(BX('string-pattern-wrap'), 'keyup', function () {
            BX('string-pattern-wrap-hidden').value = RegExp.escape(this.value);
        });
    });
</script>
<table class="adm-detail-content-table edit-table">
    <tbody>
    <tr>
        <td width="40%" class="adm-detail-content-cell-l">
            <?= Loc::getMessage('NAME') ?>
        </td>
        <td width="60%" class="adm-detail-content-cell-r">
            <p><?= Loc::getMessage('PATTERN_DESCRIPTION') ?></p>
            <textarea id="string-pattern-wrap" cols="30" rows="5"></textarea>
            <input type="hidden" name="option[pattern]" id="string-pattern-wrap-hidden" value="">
        </td>
    </tr>
    </tbody>
</table>