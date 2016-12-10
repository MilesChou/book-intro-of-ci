<?php

namespace HelloWorld;

class Square
{
    public function square(Number $number)
    {
        return $number->mux($number->get());
    }
}
