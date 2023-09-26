<?php

namespace App\Twig;

use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

class YesNoExtension extends AbstractExtension
{
    public function getFilters()
    {
        return [
            new TwigFilter('yes_no', [$this, 'yesNoFilter'], ['is_safe' => ['html']]),
        ];
    }

    public function yesNoFilter($value)
    {
        return $value ? '<span style="color: limegreen">&#10003;</span>' : '<span style="color: red">&#10008;</span>';
    }
}