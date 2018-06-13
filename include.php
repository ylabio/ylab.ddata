<?php
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
    ]
];

foreach ($arJsLibs as $jsLib => $arJsLib) {
    CJSCore::RegisterExt($jsLib, $arJsLib);
}