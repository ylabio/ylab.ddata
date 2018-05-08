<?php

use Bitrix\Main\Application;
use Bitrix\Main\Entity\Base;
use Bitrix\Main\Loader;
use Bitrix\Main\Localization\Loc;
use Bitrix\Main\ModuleManager;

Loc::loadMessages(__FILE__);

/**
 * Class ylab_ddata
 */
class ylab_ddata extends CModule
{
    /**
     * @var string Код модуля
     */
    public $MODULE_ID = 'ylab.ddata';
    /**
     * @var array Исключения при копировании файлов административного раздела
     */
    public $arExclusionAdminFiles = [
        '..',
        '.',
        'menu.php',
        'fragments'
    ];
    /**
     * @var array Namespace классов с таблицами
     */
    public $arOrmTables = [
        "\Ylab\Ddata\Orm\EntityUnitProfileTable",
        "\Ylab\Ddata\Orm\DataUnitOptionsTable",
    ];

    /**
     * ylab_ddata constructor.
     */
    public function __construct()
    {
        $arModuleVersion = array();

        include __DIR__ . '/version.php';

        if (is_array($arModuleVersion) && array_key_exists('VERSION', $arModuleVersion)) {
            $this->MODULE_VERSION = $arModuleVersion['VERSION'];
            $this->MODULE_VERSION_DATE = $arModuleVersion['VERSION_DATE'];
        }

        $this->MODULE_NAME = Loc::getMessage('YLAB_DDATA_MODULE_NAME');
        $this->MODULE_DESCRIPTION = Loc::getMessage('YLAB_DDATA_MODULE_DESCRIPTION');
        $this->MODULE_GROUP_RIGHTS = 'N';
        $this->PARTNER_NAME = Loc::getMessage('YLAB_DDATA_MODULE_PARTNER_NAME');
        $this->PARTNER_URI = 'http://ylab.io';
    }

    /**
     * @param bool $notDocumentRoot
     * @return mixed|string
     */
    public function GetPath($notDocumentRoot = false)
    {
        if ($notDocumentRoot) {
            return str_ireplace(Application::getDocumentRoot(), '', str_replace('\\', '/', dirname(__DIR__)));
        } else {
            return dirname(__DIR__);
        }
    }

    /**
     * @throws \Bitrix\Main\LoaderException
     */
    public function DoInstall()
    {
        ModuleManager::registerModule($this->MODULE_ID);
        $this->installDB();
        $this->InstallFiles();
    }

    /**
     * @throws \Bitrix\Main\Db\SqlQueryException
     * @throws \Bitrix\Main\LoaderException
     */
    public function DoUninstall()
    {
        $this->uninstallDB();
        $this->UnInstallFiles();
        ModuleManager::unRegisterModule($this->MODULE_ID);
    }

    /**
     * @return bool|void
     * @throws \Bitrix\Main\LoaderException
     */
    public function installDB()
    {
        Loader::includeModule($this->MODULE_ID);

        foreach ($this->arOrmTables as $table) {
            if (!Application::getConnection()->isTableExists(Base::getInstance($table)->getDBTableName())) {
                Base::getInstance($table)->createDbTable();
            }
        }
    }

    /**
     * @throws \Bitrix\Main\Db\SqlQueryException
     * @throws \Bitrix\Main\LoaderException
     */
    public function uninstallDB()
    {
        Loader::includeModule($this->MODULE_ID);

        foreach ($this->arOrmTables as $table) {
            Application::getConnection()->queryExecute('DROP TABLE IF EXISTS ' . Base::getInstance($table)->getDBTableName());
        }
    }

    /**
     * @param array $arParams
     */
    public function InstallFiles($arParams = array())
    {
        if (\Bitrix\Main\IO\Directory::isDirectoryExists($path = $this->GetPath() . '/admin')) {
            if ($dir = opendir($path)) {
                while (false !== $item = readdir($dir)) {
                    if (in_array($item, $this->arExclusionAdminFiles)) {
                        continue;
                    }
                    file_put_contents($_SERVER['DOCUMENT_ROOT'] . '/bitrix/admin/' . $this->MODULE_ID . '_' . $item,
                        '<' . '? require($_SERVER["DOCUMENT_ROOT"]."' . $this->GetPath(true) . '/admin/' . $item . '");?' . '>');
                }
                closedir($dir);
            }
        }

        if (\Bitrix\Main\IO\Directory::isDirectoryExists($path = $this->GetPath() . '/assets')) {
            CopyDirFiles($this->GetPath() . "/assets/",
                $_SERVER["DOCUMENT_ROOT"] . "/bitrix/themes/" . $this->MODULE_ID,
                true, true);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function UnInstallFiles()
    {
        if (\Bitrix\Main\IO\Directory::isDirectoryExists($path = $this->GetPath() . '/admin')) {
            DeleteDirFiles($_SERVER["DOCUMENT_ROOT"] . $this->GetPath() . '/admin/',
                $_SERVER["DOCUMENT_ROOT"] . '/bitrix/admin');
            if ($dir = opendir($path)) {
                while (false !== $item = readdir($dir)) {
                    if (in_array($item, $this->arExclusionAdminFiles)) {
                        continue;
                    }
                    \Bitrix\Main\IO\File::deleteFile($_SERVER['DOCUMENT_ROOT'] . '/bitrix/admin/' . $this->MODULE_ID . '_' . $item);
                }
                closedir($dir);
            }
        }

        if (\Bitrix\Main\IO\Directory::isDirectoryExists($path = $this->GetPath() . '/assets')) {
            DeleteDirFiles($_SERVER["DOCUMENT_ROOT"] . $this->GetPath() . '/assets/',
                $_SERVER["DOCUMENT_ROOT"] . '/bitrix/themes/' . $this->MODULE_ID);
        }
    }
}
