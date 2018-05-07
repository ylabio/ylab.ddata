<?
/**
 * @global $arRequest
 * @global $arOptions
 */

use Bitrix\Main\Localization\Loc;
use Ylab\Ddata\LoadUnits;

Loc::loadMessages(__FILE__);

global $USER;
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

$arSelectedGroups = $oData->arSelectedGroups;
$sUserChoice = $oData->sUserChoice;
$iUserID = $oData->iUserID;
$arUsers = $oData->arUsers;
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
                    console.log(optionsForm.value);
                    if (optionsForm.value == 'DEFINED' && key == 'choice') {
                        document.getElementById('choose-user-selector').disabled = false;
                    } else if(optionsForm.value == 'RANDOM' && key == 'choice') {
                        document.getElementById('choose-user-selector').disabled = true;
                    }
                }
            });
        }
    });
    BX.bind(BX('choose-value-selector'), 'change', function () {
        if (this.value == 'DEFINED') {
            document.getElementById('choose-user-selector').disabled = false;
        } else {
            document.getElementById('choose-user-selector').disabled = true;
        }
    });
    BX.bind(BX('choose-group-selector'), 'change', function () {
       var groupValue = this.value;
       var choiceValue = document.getElementById('choose-user-selector').value;
       var arUsers = <?=json_encode($arUsers)?>;
       document.getElementById('choose-user-selector').innerHTML = '';
       var arResultUsers = [];
       $arUserGroups = [];
       arUsers.forEach(function (item, key) {
           $arUserGroups = arUsers[key]['GROUPS_ID'];
           if (in_array(groupValue, $arUserGroups)) {
               arResultUsers.push(arUsers[key]);
           }
       });
       var sSelectHtml = '';
       arResultUsers.forEach(function(userKey, userValue){
           sSelectHtml = sSelectHtml +
               '<option value="' + arResultUsers[userValue]["ID"]+ '">' +
                    (arResultUsers[userValue]["ID"] || "") + " [" + (arResultUsers[userValue]["LOGIN"] || "") + "] " +
                    (arResultUsers[userValue]["LAST_NAME"] || "") + (arResultUsers[userValue]["NAME"] || "") +
               '</option>';
       });
       document.getElementById('choose-user-selector').innerHTML = sSelectHtml;

    });

    function in_array(value, array) {
        for(var i=0; i<array.length; i++){
            if(value == array[i]) return true;
        }
        return false;
    }
</script>
<table class="adm-detail-content-table edit-table">
    <tbody>
    <tr>
        <td width="40%" class="adm-detail-content-cell-l">
            <?= Loc::getMessage('CHOICE_USER') ?>
        </td>
        <td width="60%" class="adm-detail-content-cell-r">
            <select class="data-option" name="option[choice]" id="choose-value-selector">
                <option value="RANDOM" <?= $sUserChoice == 'RANDOM' ? 'selected' : '' ?>><?= Loc::getMessage('RANDOM_USER') ?></option>
                <option value="DEFINED" <?= $sUserChoice == 'DEFINED' ? 'selected' : '' ?>><?= Loc::getMessage('DEFINED_USER') ?></option>
            </select>
        </td>
    </tr>
        <tr>
            <td width="40%" class="adm-detail-content-cell-l">
                <?= Loc::getMessage('SELECT_GROUPS') ?>
            </td>
            <td width="60%" class="adm-detail-content-cell-r">
                <select class="data-option" name="option[selected-group]" size="5" style="width: 50%;" id="choose-group-selector">
                    <? foreach ($arGroupList as $group): ?>
                        <option value="<?= $group['ID'] ?>" <?= in_array($group['ID'],
                            $arSelectedGroups) ? 'selected' : '' ?>><?= $group['NAME'] ?></option>
                    <? endforeach; ?>
                </select>
            </td>
        </tr>
        <tr>
            <td width="40%" class="adm-detail-content-cell-l">
                <?= Loc::getMessage('SELECT_USER') ?>
            </td>
            <td width="60%" class="adm-detail-content-cell-r">
                <select name="option[user-id]" size="5" disabled="" id="choose-user-selector">
                    <? foreach ($arUsers as $arUser): ?>
                        <option value="<?= $arUser['ID'] ?>" <?= $iUserID == $arUser['ID'] ? 'selected' : '' ?>><?= $arUser['LAST_NAME'] . " " . $arUser['NAME'] ?></option>
                    <? endforeach; ?>
                </select>
            </td>
        </tr>
    </tbody>
</table>
