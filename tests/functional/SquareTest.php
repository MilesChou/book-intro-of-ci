<?php

class SquareTest extends \Codeception\Test\Unit
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

    // tests
    public function testShouldGet100WhenParamsIs10()
    {
        // Arrange
        $number = \Codeception\Util\Stub::make(\HelloWorld\Number::class, ['mux' => 100]);
        $target = new \HelloWorld\Square();
        $excepted = 100;

        // Act
        $actual = $target->square($number);

        // Assert
        $this->assertEquals($excepted, $actual);
    }
}
