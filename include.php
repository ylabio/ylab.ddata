<?php
use Bitrix\Main\Localization\Loc;
Loc::loadMessages(__FILE__);

$arJsLibs = [
    'WindowEntityPrepareForm' => [
        'js' => '/bitrix/themes/ylab.ddata/js/WindowEntityPrepareForm.js',
        'lang' => '/bitrix/themes/ylab.ddata/lang/' . LANGUAGE_ID . '/WindowEntityPrepareForm.php',
        'rel' => ['ajax', 'window']
    ],
    'WindowEntityDataForm' => [
        'js' => '/bitrix/themes/ylab.ddata/js/WindowEntityDataForm.js',
        'lang' => '/bitrix/themes/ylab.ddata/lang/' . LANGUAGE_ID . '/WindowEntityDataForm.php',
        'rel' => ['ajax', 'window']
    ],
    'WindowEntityProfileGen' => [
        'js' => '/bitrix/themes/ylab.ddata/js/WindowEntityProfileGen.js',
        'lang' => '/bitrix/themes/ylab.ddata/lang/' . LANGUAGE_ID . '/WindowEntityProfileGen.php',
        'rel' => ['ajax', 'window']
    ],
    'ErrorChecking' => [
        'js' => '/bitrix/themes/ylab.ddata/js/ErrorChecking.js'
    ],
    'ImportProfile' => [
        'js' => '/bitrix/themes/ylab.ddata/js/ImportProfile.js',
        'lang' => '/bitrix/themes/ylab.ddata/lang/' . LANGUAGE_ID . '/ImportProfile.php',
        'rel' => ['ajax']
    ],
    'SettingsForm' => [
        'js' => '/bitrix/themes/ylab.ddata/js/SettingsForm.js',
        'lang' => '/bitrix/themes/ylab.ddata/lang/' . LANGUAGE_ID . '/SettingsForm.php'
    ]
];

foreach ($arJsLibs as $jsLib => $arJsLib) {
    CJSCore::RegisterExt($jsLib, $arJsLib);
}

define('LANG_ROOT', \Bitrix\Main\Application::getInstance()->getContext()->getServer()->getDocumentRoot() . \Ylab\Ddata\Helpers::getModulePath(true) . '/include.php');