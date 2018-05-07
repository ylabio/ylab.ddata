<?
if (!empty($iWidth) && !empty($iHeight)) {

    $arConfig = [
        'font-path' => '../../assets/fonts/bellb.ttf',
        'offset-top' => 1,
        'offset-bottom' => 1,
        'offset-left' => 1,
        'offset-right' => 1,
        'colum-count' => 5,
        'colum-solt' => 17
    ];

    $iColWidth = ($iWidth - $arConfig['offset-left'] - $arConfig['offset-right']) / $arConfig['colum-count'];
    $iColHeight = ($iHeight - $arConfig['offset-top'] - $arConfig['offset-bottom']);

    $sImage = imagecreatetruecolor($iWidth, $iHeight);

    imagefill($sImage, 0, 0, 0xFFFFFF);

    $x = $arConfig['offset-left'];
    $y = $arConfig['offset-top'] + $iColHeight;
    for ($i = 0; $i < $arConfig['colum-count']; $i++) {

        $iColor1 = rand(0, 255);
        $iColor2 = rand(0, 255);
        $iColor3 = rand(0, 255);

        imagefilledrectangle(
            $sImage,
            $x,
            $y - round($arConfig['colum-solt'] * $iColHeight / $arConfig['colum-solt']),
            $x + $iColWidth - 1,
            $y,
            imagecolorallocate(
                $sImage,
                $iColor1,
                $iColor2,
                $iColor3
            )
        );

        $x += $iColWidth;
    }

    // Размещаем контейнер с текстом по середине изображения
    $iWidthTextBox = $iWidth * 0.6;
    $iHeightTextBox = $iHeight * 0.3;

    $iXTextBox = ($iWidth / 2) - ($iWidthTextBox / 2);
    $iYTextBox = ($iHeight / 2) - ($iHeightTextBox / 2);

    $sImageTextBox = imagecreatetruecolor($iWidthTextBox, $iHeightTextBox);
    imagefill($sImageTextBox, 0, 0, 0xFFFFFF);

    $sRandomStringText = substr(base64_encode(md5(md5(uniqid()))), 0, $arConfig['colum-count']);
    $iFontSizeText = $iHeightTextBox * 0.15;

    $iTTFbboxText = imagettfbbox($iFontSizeText, 0, $arConfig['font-path'], $sRandomStringText);
    $iTTFbboxTextWidth = abs($iTTFbboxText[4] - $iTTFbboxText[0]);
    $iTTFbboxTextWHeight = abs($iTTFbboxText[5] - $iTTFbboxText[1]);

    $iXText = ($iWidthTextBox / 2) - ($iTTFbboxTextWidth / 2);
    $iYText = ($iHeightTextBox / 2) - ($iTTFbboxTextWHeight / 2);

    imagefttext($sImageTextBox, $iFontSizeText, 0, $iXText, $iYText, imageColorAllocate(
        $sImageTextBox, 0, 0, 0
    ), $arConfig['font-path'], $sRandomStringText);

    imagecopymerge($sImage, $sImageTextBox, $iXTextBox, $iYTextBox, 0, 0, $iWidthTextBox, $iHeightTextBox, 92);

    header('Content-type: image/png');

    imagepng($sImage);
    imagedestroy($sImage);
}
?>