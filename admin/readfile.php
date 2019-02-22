<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/main/include/prolog_admin.php';

use Bitrix\Main\Loader;
use Ylab\Ddata;

/** @global CMain $APPLICATION */
global $APPLICATION;

$APPLICATION->RestartBuffer();

Loader::includeModule('ylab.ddata');
$obRequest = \Bitrix\Main\Application::getInstance()->getContext()->getRequest();
$sPathFile = Ddata\ExportImportProfile::getPathFile($obRequest->get('iIdProfile'));
Ddata\ExportImportProfile::readfile($sPathFile);
exit(0);