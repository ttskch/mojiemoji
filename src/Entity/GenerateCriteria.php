<?php

namespace App\Entity;

use Symfony\Component\Validator\Constraints as Assert;

class GenerateCriteria
{
    const FONT_GOTHIC = 'ゴシック';
    const FONT_MINCHO = '明朝';

    /**
     * @var string
     *
     * @Assert\NotBlank()
     * @Assert\Length(max = 4)
     */
    public $moji;

    /**
     * @var string
     *
     * @Assert\Choice({"ゴシック", "明朝"})
     */
    public $font;

    public function __construct()
    {
        $this->font = self::FONT_GOTHIC;
    }
}
