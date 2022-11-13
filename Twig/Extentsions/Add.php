<?php

use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class Add extends AbstractExtension
{
    public function getFunctions()
    {
        return [
            new TwigFunction('add', [$this, 'addTwoNumbers']),
        ];
    }

    public function addTwoNumbers(int $first, int $second): int
    {
        return $first + $second;
    }

}