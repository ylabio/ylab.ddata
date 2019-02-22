<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/main/include/prolog_admin_before.php';

use Bitrix\Main\Loader;
use Bitrix\Main\Localization\Loc;
use Ylab\Ddata\LoadUnits;

try {
    define('MODULE_ID', 'ylab.ddata');
    Loader::includeModule(MODULE_ID);

    Loc::loadMessages(__FILE__);
    $ctx = \Bitrix\Main\Application::getInstance()->getContext();
    $request = $ctx->getRequest();
    $sGeneratorID = $request->get('generator');
    $sPropertyName = $request->get('property-name');
    $sPropertyCode = $request->get('property-code');
    $sProfileID = $request->get('profile_id');
    $arRequest = $request->toArray();
    $arOptions = $arRequest['option'];

    $objLoadUnits = new LoadUnits();

    $arDataForm = $objLoadUnits->getDataUnitById($sGeneratorID);

    $oDataClass = new $arDataForm['CLASS']($sProfileID, $sPropertyCode, $sGeneratorID);
    $sForm = $oDataClass->getOptionForm($request);

    $isValidateOptions = false;
    if ($request->isPost() && !empty($request->get('validate'))) {
        $isValidateOptions = $oDataClass->isValidateOptions($request);
        if (!$isValidateOptions) {
            throw new Exception(Loc::getMessage('ERROR_OPTION'));
        }
    }

} catch (\Exception $e) {
    $error = $e->getMessage();
}

if (isset($error)) {
    CAdminMessage::ShowMessage([
        'MESSAGE' => $error,
        'TYPE' => 'ERROR',
    ]);
}

if (!empty($sForm) && !$isValidateOptions) { ?>
    <form action="<?= $APPLICATION->GetCurPage() ?>" method='post' name="WindowEntityDataForm" id='WindowEntityDataForm'>
        <?= $sForm ?>
        <script type="text/javascript">
            BX.ready(function () {
                new BX.Ylab.Settings('<?= $sPropertyName?>', '<?= $sGeneratorID ?>');
            });

            var saveBtn = {
                title: "<?= Loc::getMessage('YLAB_DDATA_BTN_JS_SAVE')?>",
                id: "savebtn",
                name: "savebtn",
                className: "adm-btn-save",
                action: function () {
                    window.YlabDdata.WindowEntityDataForm.PostParameters("validate=Y");
                }
            };
            window.YlabDdata.WindowEntityDataForm.SetButtons([saveBtn, BX.CDialog.btnCancel]);
        </script>
    </form>
<? } else { ?>
    <script>
        BX.ready(function () {
            var inputButton = BX.findChild(
                BX(document),
                {
                    className: 'DataFieldButton',
                    attribute: {
                        'data-name': '<?= $sPropertyName ?>'
                    }
                },
                true,
                true
            )[0];
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
            if (inputOptions == undefined) {
                BX.prepend(BX.create('input', {
                    attrs: {
                        'type': 'hidden',
                        'class': 'options-input',
                        'name': '<?= $sPropertyName ?>[<?= $sGeneratorID ?>]',
                        'value': '<?= json_encode($arOptions) ?>',
                        'id': '<?= $sGeneratorID ?>'
                    }
                }), BX.findParent(inputButton, {}));
            } else {

                BX.remove(inputOptions);

                BX.prepend(BX.create('input', {
                    attrs: {
                        'type': 'hidden',
                        'class': 'options-input',
                        'name': '<?= $sPropertyName ?>[<?= $sGeneratorID ?>]',
                        'value': '<?= json_encode($arOptions, JSON_HEX_TAG|JSON_HEX_AMP|JSON_HEX_APOS|JSON_HEX_QUOT) ?>',
                        'id': '<?= $sGeneratorID ?>'
                    }
                }), BX.findParent(inputButton, {}));
            }
        });
        window.YlabDdata.WindowEntityDataForm.Close();
    </script>
<? } ?>