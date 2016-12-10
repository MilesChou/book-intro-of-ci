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
        $pdoMock = \Codeception\Util\Stub::make('PDO');
        $numbers = [
            new \HelloWorld\Number(1, $pdoMock),
            new \HelloWorld\Number(2, $pdoMock),
            new \HelloWorld\Number(3, $pdoMock),
        ];
        $excepted = 6;

        // Act
        $actual = $target->sum($numbers, $pdoMock)->get();

        // Assert
        $this->assertEquals($excepted, $actual);
    }
}