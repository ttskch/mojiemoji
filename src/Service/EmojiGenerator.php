<?php

namespace App\Service;

use App\Entity\GenerateCriteria;

class EmojiGenerator
{
    /**
     * @var GenerateCriteria
     */
    private $criteria;

    public function __construct()
    {
    }

    public function setCriteria(GenerateCriteria $criteria): EmojiGenerator
    {
        $this->criteria = $criteria;

        return $this;
    }

    public function generate(): string
    {
        $font = [
            'file' => __DIR__ . '/../Resources/files/' . ($this->criteria->font === GenerateCriteria::FONT_MINCHO ? 'ipaexm.ttf' : 'ipaexg.ttf'),
            'linespacing' => 0.9,
            'size' => [120, 62, 62],
            'x' => [6, 4, 4],
            'y' => [146, 116, 78],
        ];

        switch (mb_strlen($moji = $this->criteria->moji)) {
            case 1:
                $size = $font['size'][0] * 72 / 96;
                $x = $font['x'][0] * 72 / 96;
                $y = $font['y'][0] * 72 / 96;
                break;
            case 2:
                $size = $font['size'][1] * 72 / 96;
                $x = $font['x'][1] * 72 / 96;
                $y = $font['y'][1] * 72 / 96;
                break;
            default:
                $size = $font['size'][2] * 72 / 96;
                $x = $font['x'][2] * 72 / 96;
                $y = $font['y'][2] * 72 / 96;
                $moji = mb_substr($moji, 0, 2) . "\n" . mb_substr($moji, 2, 4);
                break;
        }

        $image = imagecreatefrompng(__DIR__ . '/../Resources/files/template.png');
        $fgcolor = imagecolorallocate($image, 0, 0, 0);
        imagefttext($image, $size, 0, $x, $y, $fgcolor, $font['file'], $moji, ['linespacing' => $font['linespacing']]);

        ob_start();
        imagepng($image);
        $base64 = base64_encode(ob_get_contents());
        ob_end_clean();
        imagedestroy($image);

        return $base64;
    }
}
