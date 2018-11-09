<?
/**
 * @global $arCatalog
 * @global $arCatalogTypes
 * @global $iProductTypeSKU
 * @global $iProductTypeSimple
 */
use Bitrix\Main\Localization\Loc;

Loc::loadMessages(__FILE__);

?>
<tr>
    <td width="40%" class="adm-detail-content-cell-l">
        <?= Loc::getMessage('YLAB_DDATA_FMG_CAT_EL_IBLOCK') ?>:
    </td>
    <td class="adm-detail-content-cell-r">
        <select name="prepare[iblock_id]" id="catalog-selector">
            <option value=""><?= Loc::getMessage('YLAB_DDATA_FMG_CAT_EL_IBLOCK_ID') ?></option>
            <? foreach ($arCatalog as $iId => $sName): ?>
                <option value="<?= $iId ?>" <?= ($arPrepareRequest['iblock_id'] == $iId ? "selected" : "") ?>><?= $sName ?></option>
            <? endforeach; ?>
        </select>
    </td>
</tr>
<tr>
    <td width="40%" class="adm-detail-content-cell-l"><?= Loc::getMessage("YLAB_DDATA_FMG_CAT_EL_TYPE") ?></td>
    <td class="adm-detail-content-cell-r">
        <select name="prepare[catalog_type]" id="type-selector">
            <option value=""><?= Loc::getMessage("YLAB_DDATA_FMG_CAT_EL_TYPE_CHOOSE") ?></option>
            <? foreach ($arCatalogTypes as $iTypeID => $sTypeName): ?>
                <? if($iTypeID == $iProductTypeSKU || $iTypeID == $iProductTypeSimple): ?>
                    <option value="<?=$iTypeID?>" <?=$arPrepareRequest['catalog_type'] != null && $arPrepareRequest['catalog_type'] == $iTypeID ? 'selected' : ''?>><?=$sTypeName?></option>
                <? else: ?>
                    <option value="<?=$iTypeID?>" <?=$arPrepareRequest['catalog_type'] != null && $arPrepareRequest['catalog_type'] == $iTypeID ? 'selected' : ''?> disabled><?=$sTypeName?></option>
                <? endif; ?>
            <? endforeach; ?>
        </select>
    </td>
</tr>
<? if ($arPrepareRequest['catalog_type'] == $iProductTypeSKU): ?>
    <tr>
        <td width="40%" class="adm-detail-content-cell-l"><?= Loc::getMessage("YLAB_DDATA_FMG_CAT_EL_OFFER_COUNT") ?></td>
        <td class="adm-detail-content-cell-r">
            <div>
                <?= Loc::getMessage("YLAB_DDATA_MIN_OFFERS_COUNT") ?>
                <input type="text" name="prepare[min_offers]" style="width: 10%" value="<?=$arPrepareRequest['min_offers']?>">
                <?= Loc::getMessage("YLAB_DDATA_MAX_OFFERS_COUNT") ?>
                <input type="text" name="prepare[max_offers]" style="width: 10%" value="<?=$arPrepareRequest['max_offers']?>">
                <input type="button" id="save-count-offers" class="adm-btn-save" value="OK"/>
            </div>
        </td>
    </tr>
<? endif; ?>
<script>
    BX.ready(function () {
        BX.bind(BX('catalog-selector'), 'change', function () {
            window.YlabDdata.WindowEntityPrepareForm.PostParameters();
        });
        BX.bind(BX('type-selector'), 'change', function () {
            window.YlabDdata.WindowEntityPrepareForm.PostParameters();
        });
        BX.bind(BX('save-count-offers'), 'click', function (event) {
            event.preventDefault();
            window.YlabDdata.WindowEntityPrepareForm.PostParameters();
        });
    });
</script>