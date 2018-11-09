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
$iMin = $oData->iMin;
$iMax = $oData->iMax;
$iUserNumber = $oData->iUserNumber;
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

        /**
         * Визуально отключаем настройки
         * при заполнении собственного числа
         */
        var userParmas = BX.findChild(
            document,
            {
                attribute: {
                    'class': 'user-param'
                }
            },
            true,
            true
        );
        var userNumber = BX('user-number');
        var userNumberInput = BX.findChild(
            userNumber,
            {
                attribute: {
                    'name': 'option[user-number]'
                }
            },
            true,
            false
        );

        addOpacityTr(userParmas, userNumberInput);
        BX.bind(userNumberInput, 'input', function () {
            addOpacityTr(userParmas, this);
        });

        function addOpacityTr(array, userInput) {
            array.forEach(function (tr) {
                if (userInput.value) {
                    BX.style(tr, 'opacity', '0.3');
                } else {
                    BX.style(tr, 'opacity', '1');
                };
            });
        };
    });
</script>
<table class="adm-detail-content-table edit-table">
    <tbody>
    <tr class="user-param" style="<?= $iUserNumber ? "opacity: 0.3" : "" ?>">
        <td width="40%" class="adm-detail-content-cell-l">
            <?= Loc::getMessage('MIN_LENGTH') ?>
        </td>
        <td width="60%" class="adm-detail-content-cell-r">
            <input type="text" class="data-option" name="option[min]" value="<?= $iMin ?>"/>
        </td>
    </tr>
    <tr class="user-param" style="<?= $iUserNumber ? "opacity: 0.3" : "" ?>">
        <td width="40%" class="adm-detail-content-cell-l">
            <?= Loc::getMessage('MAX_LENGTH') ?>
        </td>
        <td width="60%" class="adm-detail-content-cell-r">
            <input type="text" class="data-option" name="option[max]" value="<?= $iMax ?>"/>
        </td>
    </tr>
    <tr id="user-number">
        <td width="40%" class="adm-detail-content-cell-l">
            <?= Loc::getMessage('USER_NUMBER') ?>
        </td>
        <td width="60%" class="adm-detail-content-cell-r">
            <input type="text" class="data-option" name="option[user-number]" value="<?= $iUserNumber ?>"/>
        </td>
    </tr>
    </tbody>
</table>