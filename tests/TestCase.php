<?php

use PHPUnit\Framework\TestCase as PHPUnit;

class TestCase extends PHPUnit
{
    public function tearDown()
    {
        if (class_exists('Mockery')) {
            if (($container = \Mockery::getContainer()) !== null) {
                $this->addToAssertionCount($container->mockery_getExpectationCount());
            }

            Mockery::close();
        }
    }
}