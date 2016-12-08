<?php

class NumberTest extends \Codeception\Test\Unit
{
    /**
     * @var \UnitTester
     */
    protected $tester;

    protected function _before()
    {
    }

    protected function _after()
    {
    }

    public function testShouldGet1WhenConstructArgIs1()
    {
        // Arrange
        $target = new \HelloWorld\Number(1);
        $excepted = 1;

        // Act
        $actual = $target->get();

        // Assert
        $this->assertEquals($excepted, $actual);
    }
}