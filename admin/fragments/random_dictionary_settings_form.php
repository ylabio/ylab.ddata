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
$sMethod = $oData->sMethod;
$sServerPath = str_replace("/", "\\", $_SERVER['DOCUMENT_ROOT']);
$sPath = str_replace($sServerPath, "", $oData->sDictionaryPath);
$sPath = substr($sPath, 0, -1);

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
            <?= Loc::getMessage('YLAB_DDATA_DATA_DICTIONARY_CHOOSE_RANDOM') ?>
        </td>
        <td width="60%" class="adm-detail-content-cell-r">
            <select name="option[method]">
                <option value="RANDOM" <?=$sMethod == 'RANDOM' ? 'selected' : ''?>><?= Loc::getMessage('YLAB_DDATA_DATA_DICTIONARY_CHOOSE_RANDOM') ?></option>
                <option value="SERIALLY" <?=$sMethod == 'SERIALLY' ? 'selected' : ''?>><?= Loc::getMessage('YLAB_DDATA_DATA_DICTIONARY_CHOOSE_SERIALLY') ?></option>
            </select>
        </td>
    </tr>
    <tr>
        <td width="40%" class="adm-detail-content-cell-l">
            <?= Loc::getMessage('YLAB_DDATA_DATA_DICTIONARY_PATH_DESC') ?>
        </td>
        <td width="60%" class="adm-detail-content-cell-r">
            <input class="data-option" type="text" name="option[path]" id="pathFile" value="<?= $sPath ?>">
            <input type="button" title="<?= Loc::getMessage('YLAB_DDATA_DATA_DICTIONARY_BUTTON') ?>"
                   class="DataFieldButton" id="chooseFile" value="...">
        </td>
    </tr>
    </tbody>
</table>

<?
CAdminFileDialog::ShowScript([
    "event" => "openPath",
    "arResultDest" => ["FUNCTION_NAME" => "setPathUrl"],
    "arPath" => [],
    "select" => 'F',
    "operation" => 'O',
    "showUploadTab" => true,
    "showAddToMenuTab" => false,
    "fileFilter" => 'doc,docx,txt',
    "allowAllFiles" => true,
    "saveConfig" => true
]);
?>
<script type="text/javascript">
    document.getElementById("chooseFile").onclick = openPath;
    var setPathUrl = function (filename, path, site) {
        var inputPath = document.getElementById('pathFile');
        inputPath.value = path + "/" + filename;
    }
</script>

