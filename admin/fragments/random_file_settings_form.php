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


$sServerPath = str_replace("/", "\\", $_SERVER['DOCUMENT_ROOT']);
$sPath = str_replace($sServerPath, "", $this->sPath);
$sPath = str_replace("\\", "/", $sPath);

?>
<table class="adm-detail-content-table edit-table">
    <tbody>
    <tr>
        <td width="40%" class="adm-detail-content-cell-l">
            <?= Loc::getMessage('YLAB_DDATA_DATA_FILE_DESC') ?>
        </td>
        <td width="60%" class="adm-detail-content-cell-r">
            <input class="data-option" type="text" name="option[path]" id="pathFile" value="<?= $sPath ?>">
            <input type="button" title="<?= Loc::getMessage('YLAB_DDATA_DATA_FILE_BUTTON') ?>"
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
    "select" => 'D',
    "operation" => 'O',
    "showUploadTab" => true,
    "showAddToMenuTab" => false,
    "fileFilter" => 'image',
    "allowAllFiles" => true,
    "saveConfig" => true
]);
?>
<script type="text/javascript">
    document.getElementById("chooseFile").onclick = openPath;
    var setPathUrl = function (filename, path, site) {
        var inputPath = document.getElementById('pathFile');
        inputPath.value = path;
    }
</script>