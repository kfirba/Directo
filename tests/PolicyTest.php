<?php

use Kfirba\Directo\Policy;
use Kfirba\Directo\Options;
use Kfirba\Directo\Credentials;


class PolicyTest extends TestCase
{
    /** @test */
    public function it_generate_base64_encoded_policy()
    {
        $time = 1477763563;
        $credentials = Mockery::mock(Credentials::class);
        $credentials->shouldReceive('AMZCredentials')->once()->andReturn('key/20161029/region/s3/aws4_request');

        $options = (new Options)->merge(['additional_inputs' => ['Content-Disposition' => 'attachment']]);

        $policy = (new Policy(
            $options, $credentials, 'bucket', $time)
        )->generate();

        $this->assertJson(base64_decode($policy));

        $data = json_decode(base64_decode($policy), true)['conditions'];

        $this->assertArrayHasKey('bucket', $data[0]);
        $this->assertArrayHasKey('acl', $data[1]);
        $this->assertEquals('$key', $data[2][1]);
        $this->assertEquals('$Content-Type', $data[3][1]);
        $this->assertEquals('content-length-range', $data[4][0]);
        $this->assertArrayHasKey('success_action_redirect', $data[5]);
        $this->assertArrayHasKey('success_action_status', $data[6]);
        $this->assertArrayHasKey('x-amz-credential', $data[7]);
        $this->assertArrayHasKey('x-amz-algorithm', $data[8]);
        $this->assertArrayHasKey('x-amz-date', $data[9]);
        $this->assertEquals('$Content-Disposition', $data[10][1]);
    }
}