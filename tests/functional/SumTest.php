<?php

class SumTest extends \Codeception\Test\Unit
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
    public function testShouldGet6WhenParamsOneTwoThreeNumberObject()
    {
        // Arrange
        $target = new \HelloWorld\Sum();
        $numbers = [
            new \HelloWorld\Number(1),
            new \HelloWorld\Number(2),
            new \HelloWorld\Number(3),
        ];
        $excepted = 6;

        // Act
        $actual = $target->sum($numbers)->get();

        // Assert
        $this->assertEquals($excepted, $actual);
    }
}