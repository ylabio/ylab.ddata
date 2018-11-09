<?
/**
 * @global  $arIblockType
 * @global $arIblock
 */
use Bitrix\Main\Loader;
use Bitrix\Main\Localization\Loc;

Loc::loadMessages(__FILE__);

/**
 * Проверка на торговый каталог
 */
Loader::includeModule('catalog');

$arIblockNoCatalog = [];
foreach ($arIblock as $id => $name) {
    if (!(CModule::IncludeModule("catalog") && \CCatalog::GetByID($id))) {
        $arIblockNoCatalog[$id] = $name;
    }
}

if (!empty($arIblock) && empty($arIblockNoCatalog)) {
    ShowError(Loc::getMessage("YLAB_DDAYA_CATALOG_ELEMENT_ERROR"));
}
?>
<tr>
    <td width="40%" class="adm-detail-content-cell-l">
        <?= Loc::getMessage('YLAB_DDATA_FMG_IB_EL_IBLOCK') ?>:
    </td>
    <td class="adm-detail-content-cell-r">
        <select name="prepare[iblock_type]" id="iblock-type-selector">
            <option value=""><?= Loc::getMessage('YLAB_DDATA_FMG_IB_EL_IBLOCK_TYPE_CODE') ?></option>
            <? foreach ($arIblockType as $type => $name): ?>
                <option value="<?= $type ?>" <?= ($arPrepareRequest['iblock_type'] == $type ? "selected" : "") ?>><?= $name ?></option>
            <? endforeach; ?>
        </select>
        <? if ($arIblockNoCatalog) { ?>
            <select name="prepare[iblock_id]" id="iblock-selector">
                <option value=""><?= Loc::getMessage('YLAB_DDATA_FMG_IB_EL_IBLOCK_TYPE_ID') ?></option>
                <? foreach ($arIblockNoCatalog as $id => $name): ?>
                    <option value="<?= $id ?>" <?= ($arPrepareRequest['iblock_id'] == $id ? "selected" : "") ?>><? echo $arPrepareRequest['iblock_id'] ?><?= $name ?></option>
                <? endforeach; ?>
            </select>
        <? } ?>
        <script>
            BX.ready(function () {
                BX.bind(BX('iblock-type-selector'), 'change', function () {
                    window.YlabDdata.WindowEntityPrepareForm.PostParameters();
                });
                BX.bind(BX('iblock-selector'), 'change', function () {
                    window.YlabDdata.WindowEntityPrepareForm.PostParameters();
                })
            });
        </script>
    </td>
</tr>