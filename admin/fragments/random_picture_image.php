<?php
if (!empty($iWidth) && !empty($iHeight)) {

    $arConfig = [
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

    header('Content-type: image/png');

    imagepng($sImage);
    imagedestroy($sImage);
}