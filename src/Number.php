<?php

namespace HelloWorld;

use PDO;

class Number
{
    private $number;
    private $pdo;

    public function __construct($number, PDO $pdo)
    {
        $this->number = $number;
        $this->pdo = $pdo;
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

    public function mux()
    {
        // Not implement;
    }

    public function save()
    {
        // Use PDO
    }

    public function load()
    {
        // Use PDO
    }
}
