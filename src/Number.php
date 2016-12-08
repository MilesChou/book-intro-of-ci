<?php

namespace HelloWorld;

class Number
{
    private $number;

    public function __construct($number)
    {
        $this->number = $number;
    }

    public function add($addend)
    {
        return $this->number + $addend;
    }

    public function sub($subtrahend)
    {
        return $this->number - $subtrahend;
    }

    public function get()
    {
        return $this->number;
    }
}
