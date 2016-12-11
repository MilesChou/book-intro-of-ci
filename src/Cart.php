<?php

namespace HelloWorld;

use Exception;

class Cart
{
    private $pay;

    public function __construct(Pay $pay)
    {
        $this->pay = $pay;
    }

    public function order()
    {
        if (!$this->pay->checkout()) {
            throw new Exception('Checkout error');
        }
    }
}
