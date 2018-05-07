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
    public $exclusionAdminFiles;
    public $orm_tables;

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

        $this->exclusionAdminFiles = [
            '..',
            '.',
            'menu.php',
            'fragments'
        ];

        $this->orm_tables = [
            "\Ylab\Ddata\Orm\EntityUnitProfileTable",
            "\Ylab\Ddata\Orm\DataUnitOptionsTable",
        ];

        $this->MODULE_ID = 'ylab.ddata';
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
    public function doInstall()
    {
        ModuleManager::registerModule($this->MODULE_ID);
        $this->installDB();
        $this->InstallFiles();
    }

    /**
     * @throws \Bitrix\Main\Db\SqlQueryException
     * @throws \Bitrix\Main\LoaderException
     */
    public function doUninstall()
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

        foreach ($this->orm_tables as $table) {
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

        foreach ($this->orm_tables as $table) {
            Application::getConnection()->queryExecute('DROP TABLE IF EXISTS ' . Base::getInstance($table)->getDBTableName());
        }
    }

    /**
     * @param array $arParams
     * @return bool|void
     */
    public function InstallFiles($arParams = array())
    {
        if (\Bitrix\Main\IO\Directory::isDirectoryExists($path = $this->GetPath() . '/admin')) {
            if ($dir = opendir($path)) {
                while (false !== $item = readdir($dir)) {
                    if (in_array($item, $this->exclusionAdminFiles)) {
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

        return true;
    }

    /**
     * @return bool|void
     */
    public function UnInstallFiles()
    {
        if (\Bitrix\Main\IO\Directory::isDirectoryExists($path = $this->GetPath() . '/admin')) {
            DeleteDirFiles($_SERVER["DOCUMENT_ROOT"] . $this->GetPath() . '/admin/',
                $_SERVER["DOCUMENT_ROOT"] . '/bitrix/admin');
            if ($dir = opendir($path)) {
                while (false !== $item = readdir($dir)) {
                    if (in_array($item, $this->exclusionAdminFiles)) {
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

        return true;
    }
}
