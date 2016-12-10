<?php

namespace HelloWorld;

class Sum
{
    public function sum(array $numbers, $pdoMock)
    {
        $sum = new Number(0, $pdoMock);

        foreach ($numbers as $number) {
            $sum = new Number($sum->add($number->get()), $pdoMock);
        }

        return $sum;
    }
}
