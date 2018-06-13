<?php
require_once($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_admin_before.php");

use Bitrix\Main\Loader;
use Bitrix\Main\Localization\Loc;
use Ylab\Ddata\Interfaces\DeleteDataClass;
use Ylab\Ddata\LoadUnits;

try {
    Loc::loadMessages(__FILE__);

    define('MODULE_ID', 'ylab.ddata');
    Loader::includeModule(MODULE_ID);

    /** @var \CMain $APPLICATION */
    global $APPLICATION;

    $POST_RIGHT = $APPLICATION->GetGroupRight(MODULE_ID);
    if ($POST_RIGHT == "D") {
        $APPLICATION->AuthForm(Loc::getMessage('YLAB_DDATA_ACCESS_DENIED'));
    }

    $ctx = \Bitrix\Main\Application::getInstance()->getContext();
    $request = $ctx->getRequest();

    $oClasses = new LoadUnits();
    $arClassesEntity = $oClasses->getEntityUnits();
    $arClassesData = $oClasses->getDataUnits();

    $iProfileID = $request->get('PROFILE')['ID'];
    $sEntityID = $request->get('prepare')['entity_id'];

    $arEntity = [];
    foreach ($arClassesEntity as $arClass) {
        if ($arClass['ID'] == $sEntityID) {
            $arEntity = $arClass;
        }
    }

    if (
        $request->getRequestMethod() == "POST" // проверка метода вызова страницы
        &&
        ($save != "" || $apply != "" || $generate != "" || $delete != "") // проверка нажатия кнопок "Сохранить" и "Применить" и "Генерировать"
        &&
        $POST_RIGHT == "W"          // проверка наличия прав на запись для модуля
        &&
        check_bitrix_sessid()     // проверка идентификатора сессии
    ) {
        $arProfile = $request->get('PROFILE');
        $arFields = array_merge((array)$request->get('FIELDS'), (array)$request->get('PROPERTIES'));
        $iProfileID = $arEntity['CLASS']::setProfile($arProfile, $arFields);

        $entityId = $request->get('prepare');
        $entityId = $entityId['entity_id'];

        if (!empty($save)) {
            LocalRedirect("/bitrix/admin/ylab.ddata_entity_profile_list.php?lang=" . LANGUAGE_ID);
        } else {
            if (!empty($apply)) {
                LocalRedirect("/bitrix/admin/ylab.ddata_entity_profile_edit.php?PROFILE[ID]=" . $iProfileID . "&prepare[entity_id]=" . $entityId);
            } else {
                if (!empty($generate)) {
                    LocalRedirect("/bitrix/admin/ylab.ddata_entity_profile_gen.php?ID=" . $iProfileID . "&entity_id=" . $entityId . "&lang=" . LANGUAGE_ID);
                } elseif (!empty($delete)) {
                    $oGenDataClass = new DeleteDataClass();
                    $oGenDataClass::deleteGenData($iProfileID);
                }
            }
        }
    }

    if (!empty($iProfileID)) {
        $oEntity = new $arEntity['CLASS']($iProfileID);
        $arProfile = $oEntity->getProfile($iProfileID);
        $oGenDataClass = new DeleteDataClass();
        $arGenData = $oGenDataClass::getGenData($iProfileID);
    } else {
        $oEntity = new $arEntity['CLASS']();
    }

    $arEntityFields = $oEntity->getFields($request);
    $isSetFields = array_key_exists('FIELDS', $arEntityFields) && !empty($arEntityFields['FIELDS']) ? true : false;
    $isSetProperties = array_key_exists('PROPERTIES',
        $arEntityFields) && !empty($arEntityFields['PROPERTIES']) ? true : false;
    $aTabs = [
        [
            "DIV" => "edit1",
            "TAB" => Loc::getMessage('YLAB_DDATA_TAB_NAME_MAIN'),
            "ICON" => "main_user_edit",
            "TITLE" => Loc::getMessage('YLAB_DDATA_TAB_NAME_TITLE')
        ]
    ];
    if ($isSetFields) {
        $aTabs[] =
            [
                "DIV" => "edit2",
                "TAB" => Loc::getMessage('YLAB_DDATA_TAB_FIELDS_MAIN'),
                "ICON" => "main_user_edit",
                "TITLE" => Loc::getMessage('YLAB_DDATA_TAB_FIELDS_TITLE')
            ];
    }
    if ($isSetProperties) {
        $aTabs[] =
            [
                "DIV" => "edit3",
                "TAB" => Loc::getMessage('YLAB_DDATA_TAB_PROPERTIES_MAIN'),
                "ICON" => "main_user_edit",
                "TITLE" => Loc::getMessage('YLAB_DDATA_TAB_PROPERTIES_TITLE')
            ];
    }
    $tabControl = new CAdminTabControl("tabControl", $aTabs);

    $APPLICATION->SetTitle(Loc::getMessage('YLAB_DDATA_PAGE_TITLE_ADD'));
} catch (\Exception $e) {
    $error = $e->getMessage();
}
require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_admin_after.php");
if (isset($error)) {
    CAdminMessage::ShowMessage([
        "MESSAGE" => $error,
        "TYPE" => "ERROR",
    ]);
}
CJSCore::Init(['WindowEntityDataForm']);
?>

<form method="post" action=""
      enctype="multipart/form-data"
      name="post_form"
      id="entityForm">
    <? echo bitrix_sessid_post(); ?>
    <input type="hidden" name="PROFILE[TYPE]"
           value="<?= (isset($arProfile) ? $arProfile['TYPE'] : $sEntityID) ?>">
    <input type="hidden" name="PROFILE[OPTIONS]"
           value='<?= (isset($arProfile) ? $arProfile['OPTIONS'] : json_encode((array)$request->get('prepare'))) ?>'>
    <? if (!empty($iProfileID)): ?>
        <input type="hidden" name="PROFILE[ID]" value="<?= (isset($arProfile) ? $arProfile['ID'] : $iProfileID) ?>">
    <? endif; ?>
    <?
    $tabControl->Begin();
    ?>
    <?
    $tabControl->BeginNextTab();
    ?>
    <? if (!empty($iProfileID)): ?>
        <tr>
            <td width="40%" class="adm-detail-content-cell-l">
                <b>ID:</b>
            </td>
            <td class="adm-detail-content-cell-r">
                <?= $iProfileID ?>
            </td>
        </tr>
    <? endif; ?>
    <tr>
        <td width="40%" class="adm-detail-content-cell-l">
            <b><?= Loc::getMessage('YLAB_DDATA_FIELD_PROFILE_NAME') ?>:</b>
        </td>
        <td class="adm-detail-content-cell-r">
            <input type="text" name="PROFILE[NAME]" value="<?= (isset($arProfile) ? $arProfile['NAME'] : "") ?>"
                   size="50" maxlength="255" required>
        </td>
    </tr>
    <tr>
        <td width="40%" class="adm-detail-content-cell-l">
            <b><?= Loc::getMessage('YLAB_DDATA_FIELD_PROFILE_XML_ID') ?>:</b>
        </td>
        <td class="adm-detail-content-cell-r">
            <input type="text" name="PROFILE[XML_ID]" value="<?= (isset($arProfile) ? $arProfile['XML_ID'] : "") ?>"
                   size="50" maxlength="255" required>
        </td>
    </tr>
    <?php if ($isSetFields): ?>
        <?
        $tabControl->BeginNextTab();
        uasort($arEntityFields['FIELDS'], function ($a, $b) {
            if ($a['required'] == $b['required']) {
                return 0;
            }
            return ($a['required'] > $b['required']) ? -1 : 1;
        });
        ?>
        <? foreach ($arEntityFields['FIELDS'] as $sFieldName => $arField): ?>
            <?
            $arFieldOptions = [];
            if (!empty($arProfile['FIELDS'])) {
                $iFieldIndex = array_search($sFieldName, array_column($arProfile['FIELDS'], 'FIELD_CODE'));
                if ($iFieldIndex !== false) {
                    $arFieldOptions = $arProfile['FIELDS'][$iFieldIndex];
                }
            }
            ?>
            <tr>
                <td width="40%">
                    <?php if ($arField['required']): ?>
                        <b><?= $arField['title'] ?></b>
                    <?php else: ?>
                        <?= $arField['title'] ?>
                    <?php endif ?>
                </td>
                <td width="17%">
                    <select class="data-options-select"
                            name="FIELDS[<?= $sFieldName ?>]" <?= $arField['required'] ? "required" : "" ?>
                            style="width: 100%;"">
                    <option value=""><?= Loc::getMessage('YLAB_DDATA_TAB_CHOOSE_VALUE') ?></option>
                    <? foreach ($arClassesData as $arData): ?>
                        <?php if (in_array($arData['TYPE'], $arField['type'])): ?>
                            <option value="<?= $arData['ID'] ?>"
                                <?= (isset($arFieldOptions) && $arFieldOptions['DATA_ID'] == $arData['ID'] ? "selected" : "") ?>>
                                <?= $arData['NAME'] ?>
                            </option>
                        <?php endif ?>
                    <? endforeach ?>
                    </select>
                </td>
                <td style="padding: 0 15px;">
                    <? if (isset($arFieldOptions) && !empty($arFieldOptions['OPTIONS']) && $arFieldOptions['FIELD_CODE'] == $sFieldName): ?>
                        <input type="hidden"
                               name="FIELDS[<?= $sFieldName ?>][<?= $arFieldOptions['DATA_ID'] ?>]"
                               value='<?= $arFieldOptions['OPTIONS'] ?>'
                               class="options-input">
                    <? endif; ?>
                    <input type="button" title="<?= Loc::getMessage('YLAB_DDATA_FIELD_PROFILE_BUTTON') ?>"
                           class="DataFieldButton" value="..." data-name="FIELDS[<?= $sFieldName ?>]"
                           data-type="<?= $arField['type'] ?>" data-code="<?= $sFieldName ?>"
                           data-profile="<?= (isset($arProfile) ? $arProfile['ID'] : $iProfileID) ?>"
                    >
                </td>
            </tr>
        <? endforeach ?>
    <?php endif ?>
    <?php if ($isSetProperties): ?>
        <?
        $tabControl->BeginNextTab();
        uasort($arEntityFields['PROPERTIES'], function ($a, $b) {
            if ($a['required'] == $b['required']) {
                return 0;
            }
            return ($a['required'] > $b['required']) ? -1 : 1;
        });
        ?>
        <? foreach ($arEntityFields['PROPERTIES'] as $sPropertyName => $arProperty): ?>
            <?
            $arPropertyOptions = [];
            if (!empty($arProfile['FIELDS'])) {
                $iFieldIndex = array_search($sPropertyName, array_column($arProfile['FIELDS'], 'FIELD_CODE'));
                if ($iFieldIndex !== false) {
                    $arPropertyOptions = $arProfile['FIELDS'][$iFieldIndex];
                }
            }
            ?>
            <tr>
                <td width="40%">
                    <?php if ($arProperty['required']): ?>
                        <b><?= $arProperty['title'] ?></b>
                    <?php else: ?>
                        <?= $arProperty['title'] ?>
                    <?php endif ?>
                </td>
                <td width="17%">
                    <select class="data-options-select" name="PROPERTIES[<?= $sPropertyName ?>]"
                            style="width: 100%;" <?= $arProperty['required'] ? "required" : "" ?>>
                        <option value=""><?= Loc::getMessage('YLAB_DDATA_TAB_CHOOSE_VALUE') ?></option>
                        <? foreach ($arClassesData as $arData): ?>
                            <?php if (in_array($arData['TYPE'], $arProperty['type'])): ?>
                                <option value="<?= $arData['ID'] ?>"
                                    <?= (isset($arPropertyOptions) && $arPropertyOptions['DATA_ID'] == $arData['ID'] ? "selected" : "") ?>>
                                    <?= $arData['NAME'] ?>
                                </option>
                            <?php endif ?>
                        <? endforeach ?>
                    </select>
                </td>
                <td style="padding: 0 15px;">
                    <? if (isset($arPropertyOptions) && !empty($arPropertyOptions['OPTIONS']) && $arPropertyOptions['FIELD_CODE'] == $sPropertyName): ?>
                        <input type="hidden"
                               name="PROPERTIES[<?= $sPropertyName ?>][<?= $arPropertyOptions['DATA_ID'] ?>]"
                               value='<?= $arPropertyOptions['OPTIONS'] ?>'
                               class="options-input">
                    <? endif; ?>
                    <input type="button" title="<?= Loc::getMessage('YLAB_DDATA_FIELD_PROFILE_BUTTON') ?>"
                           class="DataFieldButton" value="..." data-name="PROPERTIES[<?= $sPropertyName ?>]"
                           data-profile="<?= (isset($arProfile) ? $arProfile['ID'] : $iProfileID) ?>">
                </td>
            </tr>
        <? endforeach ?>
    <?php endif ?>
    <?
    $tabControl->Buttons(
        array(
            "disabled" => ($POST_RIGHT < "W"),
            "back_url" => "ylab.ddata_entity_profile_list.php?lang=" . LANG,
        )
    );
    ?>
    <input name="generate" value="<?= Loc::getMessage('YLAB_DDATA_GEN')?>" class="adm-btn-save" title="<?= Loc::getMessage('YLAB_DDATA_GEN')?>" type="submit">
    <? if ($arGenData): ?>
        <input name="delete" value="<?= Loc::getMessage('YLAB_DDATA_GENERATE_BUTTON_DELETE') ?>" type="submit">
    <? endif; ?>
    <? $tabControl->End(); ?>
</form>
<?
echo BeginNote();
echo '<div style="display: inline-block; vertical-align: middle"><img width="64px" height="64px" src="' . \Ylab\Ddata\Helpers::getModulePath(true) . '/assets/images/ylab.ddata.jpg' . '" alt=""></div>';
echo '<div style="display: inline-block; vertical-align: middle; margin-left: 5px">';
echo Loc::getMessage('YLAB_DDATA_ADVERTISING_SITE');
echo Loc::getMessage('YLAB_DDATA_ADVERTISING_GIT');
echo '</div>';
echo EndNote();
?>
<? require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/epilog_admin.php"); ?>
