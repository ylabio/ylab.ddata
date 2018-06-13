<?
namespace Ylab\Ddata\Orm;

use Bitrix\Main\Entity;

/**
 * Class DataUnitGenElements
 * @package Ylab\Ddata\Orm
 */
class DataUnitGenElementsTable extends Entity\DataManager
{
    /**
     * {@inheritdoc}
     */
    public static function getTableName()
    {
        return 'ylab_ddata_data_unit_gen_elements';
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
            new Entity\StringField('PROFILE_TYPE', [
                'required' => true,
                'validation' => function() {
                    return [
                        new Entity\Validator\Length(1, 255)
                    ];
                }
            ]),
            new Entity\IntegerField('GEN_ELEMENT_ID', [
                'required' => true,
                'validation' => function() {
                    return [
                        new Entity\Validator\Length(1, 255)
                    ];
                }
            ]),
        ];
    }
}