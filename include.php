<?php
spl_autoload_register(function ($className) {
    preg_match('/^(.*?)([\w]+)$/i', $className, $matches);
    if (count($matches) < 3) {
        return;
    }

    $filePath = implode(DIRECTORY_SEPARATOR, array(
        __DIR__,
        "lib",
        str_replace('\\', DIRECTORY_SEPARATOR, trim($matches[1], '\\')),
        str_replace('_', DIRECTORY_SEPARATOR, $matches[2]) . '.php'
    ));
    $filePath = str_replace('Ylab\Ddata' . DIRECTORY_SEPARATOR, '', $filePath);
    $filePath = preg_replace('#Ylab/\Ddata\/#', '', $filePath);
    $filePath = str_replace(DIRECTORY_SEPARATOR . DIRECTORY_SEPARATOR, DIRECTORY_SEPARATOR, $filePath);

    if (is_readable($filePath) && is_file($filePath)) {
        /** @noinspection PhpIncludeInspection */
        require_once $filePath;
    }
});

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