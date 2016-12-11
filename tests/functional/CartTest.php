<?php

use Codeception\Util\Stub;

class CartTest extends \Codeception\Test\Unit
{
    /**
     * @var \FunctionalTester
     */
    protected $tester;

    protected function _before()
    {
    }

    protected function _after()
    {
    }

    public function testShouldCallCheckoutOneTimeWhenCartOrder()
    {
        // Arrange
        $payMock = Stub::make(\HelloWorld\Pay::class,
            [
                'checkout' => Stub::once(function() { return true;}),
            ]
        , $this);
        $target = new \HelloWorld\Cart($payMock);

        // Act
        $target->order();
    }

    public function testShouldThrowExceptionWhenCallOrderTwice()
    {
        // Arrange
        $this->expectException(Exception::class);
        $payMock = Stub::make(\HelloWorld\Pay::class,
            [
                'checkout' => Stub::consecutive(true, false),
            ]
            , $this);
        $target = new \HelloWorld\Cart($payMock);

        // Act
        $target->order();
        $target->order();
    }
}