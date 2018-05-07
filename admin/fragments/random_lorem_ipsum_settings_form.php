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
$sGenerate = $oData->sGenerate;
$iCount = $oData->iCount;
$sHtmlWrap = $oData->sHtmlWrap;
?>
<script type='text/javascript'>
    RegExp.escape = function(text) {
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
                    if(key == 'wrap') {
                        BX('lorem-ipsum-wrap').value = optionsValue[key];
                        optionsForm.value = RegExp.escape(optionsValue[key]);
                    } else {
                        optionsForm.value = optionsValue[key];
                    }
                }
            });
        }

        BX.bind(BX('lorem-ipsum-wrap'), 'keyup',function(){
            BX('lorem-ipsum-wrap-hidden').value = RegExp.escape(this.value);
        });
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
            <p><?=Loc::getMessage('HINT')?></p>
            <textarea id="lorem-ipsum-wrap" cols="30" rows="5"></textarea>
            <input type="hidden" name="option[wrap]" id="lorem-ipsum-wrap-hidden" value="">
        </td>
    </tr>
    </tbody>
</table>

