<?php

namespace Ylab\Ddata\Orm;

use Bitrix\Main\Entity;

/**
 * Class EntityUnitProfileTable
 * @package Ylab\Ddata\orm
 */
class EntityUnitProfileTable extends Entity\DataManager
{
    /**
     * {@inheritdoc}
     */
    public static function getTableName()
    {
        return 'ylab_ddata_entity_unit_profile';
    }

    /**
     * {@inheritdoc}
     */
    public static function getMap()
    {
        return [
            new Entity\IntegerField('ID', [
                'primary' => true,
                'autocomplete' => true,
            ]),
            new Entity\StringField('NAME', [
                'required' => true,
                'validation' => function() {
                    return [
                        new Entity\Validator\Unique(),
                        new Entity\Validator\Length(1, 255)
                    ];
                }
            ]),
            new Entity\StringField('TYPE', [
                'required' => true,
                'validation' => function() {
                    return [
                        new Entity\Validator\Length(1, 255)
                    ];
                }
            ]),
            new Entity\StringField('XML_ID', [
                'unique' => true,
                'required' => true,
                'validation' => function() {
                    return [
                        new Entity\Validator\Unique(),
                        new Entity\Validator\Length(0, 255)
                    ];
                }
            ]),
            new Entity\TextField('OPTIONS')
        ];
    }
}