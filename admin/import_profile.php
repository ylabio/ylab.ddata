<?
require_once($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_admin.php");

use Bitrix\Main;
use Bitrix\Main\Loader;
use Bitrix\Main\Localization\Loc;
use Ylab\Ddata;

Loader::includeModule('ylab.ddata');
Loc::loadMessages(__FILE__);
Loc::loadMessages(LANG_ROOT);

global $APPLICATION;
$APPLICATION->SetTitle(Loc::getMessage('YLAB_DDATA_TITLE'));

$arFile = \Bitrix\Main\Application::getInstance()->getContext()->getRequest()->getFile('file');
$sPath = $arFile['tmp_name'];
if ($sPath) {
    $oErrors = Ddata\ExportImportProfile::import($sPath);
    if (!$oErrors->count()) {
        LocalRedirect('ylab.ddata_entity_profile_list.php?lang=ru&import_profile=Y');
    } else {
        ?>
        <div class="adm-info-message">
            <? foreach ($oErrors->toArray() as $oError) {
                echo $oError->getMessage();
                echo '<br>';
            } ?>
            <a href="ylab.ddata_entity_profile_list.php?lang=ru"><?=Loc::getMessage('YLAB_DDATA_RETURN')?></a>
        </div>
        <?
    }
}


echo BeginNote();
echo '<div style="display: inline-block; vertical-align: middle"><img width="64px" height="64px" src="' . \Ylab\Ddata\Helpers::getModulePath(true) . '/assets/images/ylab.ddata.jpg' . '" alt=""></div>';
echo '<div style="display: inline-block; vertical-align: middle; margin-left: 5px">';
echo Loc::getMessage('YLAB_DDATA_ADVERTISING_SITE');
echo Loc::getMessage('YLAB_DDATA_ADVERTISING_GIT');
echo '</div>';
echo EndNote();


require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/epilog_admin.php");
?>