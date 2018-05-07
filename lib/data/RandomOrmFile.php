<?

namespace Ylab\Ddata\Data;

use Bitrix\Main\Localization\Loc;

Loc::loadMessages(__FILE__);

/**
 * Class RandomOrmFile
 * @package Ylab\Ddata\Data
 */
class RandomOrmFile extends RandomFile
{
    /**
     * @return array
     */
    public static function getDescription()
    {
        return [
            "ID" => "file.unit.id",
            "NAME" => Loc::getMessage("YLAB_DDATA_DATA_UNIT_FILE_ORM_NAME"),
            "DESCRIPTION" => Loc::getMessage('YLAB_DDATA_DATA_UNIT_FILE_ORM_DESCRIPTION'),
            "TYPE" => "file.orm",
            "CLASS" => __CLASS__
        ];
    }

    /**
     * @return array|bool|mixed|null
     * @throws \Exception
     */
    public function getValue()
    {
        if (!parent::$bCheckStaticMethod) {
            $arFiles = glob($this->sPath . "*.*");
            $sResult = array_rand($arFiles);
            $arFile = \CFile::MakeFileArray($arFiles[$sResult]);
            $iFileId = \CFile::SaveFile($arFile, "demo");

            if ($iFileId) {
                return $iFileId;
            }
        } else {
            throw new \Exception(Loc::getMessage('YLAB_DDATA_DATA_UNIT_FILE_ORM_EXCEPTION_STATIC'));
        }
    }
}