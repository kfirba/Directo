<?php

use Kfirba\Directo\Credentials;

class CredentialsTest extends TestCase
{
    /** @test */
    public function it_returns_amz_credentials()
    {
        $time = 1477763563;
        $credentials = new Credentials('key', 'region', $time);

        $this->assertEquals(
            'key/20161029/region/s3/aws4_request',
            $credentials->AMZCredentials()
        );
    }
}