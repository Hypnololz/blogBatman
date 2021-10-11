<?php

namespace App\Twig;

use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;
use Twig\TwigFunction;

class AppExtension extends AbstractExtension
{
    public function getFilters(): array
    {
        return [
            new TwigFilter('excerpt', [$this, 'excerpt']),
        ];
    }

    public function excerpt(string $string,int $nbWords): string
    {
        $arrayText = explode(' ',$string,($nbWords+1));
        if(count($arrayText)>$nbWords){
            array_pop($arrayText);
            return implode(' ',$arrayText). '...';
        }
        return $string;
    }
}
