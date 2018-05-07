<?

namespace Ylab\Ddata\Data;

use Bitrix\Main\Localization\Loc;
use Ylab\Ddata\Helpers;

Loc::loadMessages(__FILE__);

/**
 * Class RandomOrmPicture
 * @package Ylab\Ddata\Data
 */
class RandomOrmPicture extends RandomPicture
{
    /**
     * @return array
     */
    public static function getDescription()
    {
        return [
            "ID" => "picture.file.unit.id",
            "NAME" => Loc::getMessage("YLAB_DDATA_DATA_UNIT_PICTURE_ORM_NAME"),
            "DESCRIPTION" => Loc::getMessage('YLAB_DDATA_DATA_UNIT_PICTURE_ORM_DESCRIPTION'),
            "TYPE" => "file.orm",
            "CLASS" => __CLASS__
        ];
    }

    /**
     * @return array|bool|null|string
     * @throws \Exception
     */
    public function getValue()
    {
        if (!parent::$checkStaticMethod) {
            $iWidth = $this->iWidth;
            $iHeight = $this->iHeight;

            ob_start();
            include Helpers::getModulePath() . '/admin/fragments/random_picture_image.php';
            $image = ob_get_contents();
            ob_end_clean();

            $arFile = [
                'content' => $image,
                'name' => 'random_picture_image.png',
                'type' => 'image/png'
            ];
            $iImageId = \CFile::SaveFile($arFile, "demo");

            if ($iImageId) {
                return $iImageId;
            }
        } else {
            throw new \Exception(Loc::getMessage('YLAB_DDATA_DATA_UNIT_PICTURE_ORM_EXCEPTION_STATIC'));
        }
    }
}