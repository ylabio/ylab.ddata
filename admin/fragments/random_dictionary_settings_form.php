<?php
/**
 * @global $sGeneratorID
 * @global $sProfileID
 * @global $sPropertyCode
 * @global $sPropertyName
 * @global $this
 */

use Bitrix\Main\Localization\Loc;
use Ylab\Ddata\Helpers;

Loc::loadMessages(__FILE__);

$sMethod = $this->sMethod;
$sServerPath = str_replace("/", "\\", $_SERVER['DOCUMENT_ROOT']);
$sPath = str_replace($sServerPath, "", $this->sDictionaryPath);
$sPath = str_replace("\\", "/", $sPath);
$sFileInfo = '';
if (!empty($this->sDictionaryPath)) {
    $arFile = Helpers::parseFile($this->sDictionaryPath);
    foreach ($arFile as $arFileInfo) {
        $sFileInfo .= $arFileInfo;
    }
}
?>
<table class="adm-detail-content-table edit-table">
    <tbody>
    <tr>
        <td width="40%" class="adm-detail-content-cell-l">
            <?= Loc::getMessage('YLAB_DDATA_DATA_DICTIONARY_CHOOSE_TITLE') ?>
        </td>
        <td width="60%" class="adm-detail-content-cell-r">
            <select name="option[method]">
                <option value="RANDOM" <?= $sMethod == 'RANDOM' ? 'selected' : '' ?>><?= Loc::getMessage('YLAB_DDATA_DATA_DICTIONARY_CHOOSE_RANDOM') ?></option>
                <option value="SERIALLY" <?= $sMethod == 'SERIALLY' ? 'selected' : '' ?>><?= Loc::getMessage('YLAB_DDATA_DATA_DICTIONARY_CHOOSE_SERIALLY') ?></option>
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
    <tr>
        <td width="40%" class="adm-detail-content-cell-l"></td>
        <td width="60%" class="adm-detail-content-cell-r">
            <textarea cols="30" rows="10" id="file-preview" disabled><?= $sFileInfo ?></textarea>
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
        window.YlabDdata.WindowEntityDataForm.PostParameters();
    }
</script>