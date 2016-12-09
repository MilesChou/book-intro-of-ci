<?php

namespace HelloWorld;

class Sum
{
    public function sum(array $numbers)
    {
        $sum = new Number(0);

        foreach ($numbers as $number) {
            $sum = new Number($sum->add($number->get()));
        }

        return $sum;
    }
}
