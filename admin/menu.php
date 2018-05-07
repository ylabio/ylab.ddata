<?php

use Bitrix\Main\Localization\Loc;

Loc::loadMessages(__FILE__);

AddEventHandler("main", "OnBuildGlobalMenu", "YlabDdataMenu");

/**
 * @param $adminMenu
 * @param $moduleMenu
 */
function YlabDdataMenu(&$adminMenu, &$moduleMenu)
{
    $adminMenu['global_menu_services']['items'][] = [
        "section" => "ylab-ddata",
        "sort" => 110,
        "text" => 'Ylab Демо данные',
        "icon" => "ylab-ddata-icon",
        "page_icon" => "ylab-ddata-icon",
        "items_id" => "ylab-ddata",
        "items" => [
            [
                "parent_menu" => "ylab-ddata",
                "section" => "ylab-ddata-profile-list",
                "sort" => 500,
                "url" => "ylab.ddata_entity_profile_list.php?lang=" . LANG,
                "more_url" => [
                    "ylab.ddata_entity_profile_edit.php",
                    "ylab.ddata_entity_profile_gen.php"
                ],
                "text" => Loc::getMessage('YLAB_DDATA_ENTITY_MENU_ITEM_NAME'),
                "title" => Loc::getMessage('YLAB_DDATA_ETITY_MENU_ITEM_DESCRIPTION'),
                "icon" => "form_menu_icon",
                "page_icon" => "form_page_icon",
                "items_id" => "ylab-ddata-profile-list"
            ]
        ]
    ];
}
