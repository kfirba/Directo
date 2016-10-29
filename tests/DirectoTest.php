<?php

use Kfirba\Directo\Policy;
use Kfirba\Directo\Directo;
use Kfirba\Directo\Signature;

class DirectoTest extends PHPUnit_Framework_TestCase
{
    public function tearDown()
    {
        Mockery::close();
    }

    /**
     * @test
     * @expectedException Kfirba\Directo\Exceptions\InvalidRegionException
     */
    public function it_validates_the_given_region()
    {
        new Directo('bucket', 'region', 'key', 'secret');
    }

    /** @test */
    public function it_defers_to_signature_to_generate_one()
    {
        $signature = Mockery::mock(Signature::class);
        $signature->shouldReceive('generate')->atLeast()->once();

        $directo = new Directo('bucket', 'eu-central-1', 'key', 'secret', null, null, $signature);

        $directo->signature();
    }

    /** @test */
    public function it_defers_to_policy_generate_one()
    {
        $policy = Mockery::mock(Policy::class);
        $policy->shouldReceive('generate')->atLeast()->once();

        $directo = new Directo('bucket', 'eu-central-1', 'key', 'secret', null, null, null, $policy);

        $directo->signature();
    }
}