<?php

namespace Ylab\Ddata\Orm;

use Bitrix\Main\Entity;

/**
 * Class DataUnitOptionsTable
 * @package Ylab\Ddata\orm
 */
class DataUnitOptionsTable extends Entity\DataManager
{
    /**
     * {@inheritdoc}
     */
    public static function getTableName()
    {
        return 'ylab_ddata_data_unit_options';
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
            new Entity\IntegerField('PROFILE_ID', [
                'required' => true,
            ]),
            new Entity\StringField('FIELD_CODE', [
                'required' => true,
                'validation' => function () {
                    return [
                        new Entity\Validator\Length(1, 255)
                    ];
                }
            ]),
            new Entity\StringField('DATA_ID', [
                'required' => true,
                'validation' => function () {
                    return [
                        new Entity\Validator\Length(1, 255)
                    ];
                }
            ]),
            new Entity\TextField('OPTIONS', [
                'required' => true,
            ]),
            new Entity\BooleanField('MULTIPLE', [
                'values' => ['Y', 'N'],
                'default_value' => 'N'
            ]),
            new Entity\IntegerField('COUNT', [
                'validation' => function () {
                    return [
                        new Entity\Validator\Length(1, 255)
                    ];
                }
            ])
        ];
    }
}