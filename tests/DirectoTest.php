<?php

use Kfirba\Directo\Credentials;
use Kfirba\Directo\Directo;
use Kfirba\Directo\Options;
use Kfirba\Directo\Policy;
use Kfirba\Directo\Signature;

class DirectoTest extends TestCase
{
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

    /** @test */
    public function it_returns_the_correct_s3_form_url()
    {
        $directo = new Directo('bucket', 'eu-central-1', 'key', 'secret');

        $this->assertEquals('//bucket.s3-eu-central-1.amazonaws.com', $directo->formUrl());
    }

    /** @test */
    public function it_returns_the_form_inputs_in_array_format()
    {
        $directo = new Directo('bucket', 'eu-central-1', 'key', 'secret');

        $inputs = $directo->inputsAsArray();

        $this->assertArrayHasKey('Content-Type', $inputs);
        $this->assertArrayHasKey('acl', $inputs);
        $this->assertArrayHasKey('success_action_status', $inputs);
        $this->assertArrayHasKey('policy', $inputs);
        $this->assertArrayHasKey('X-amz-credential', $inputs);
        $this->assertArrayHasKey('X-amz-algorithm', $inputs);
        $this->assertArrayHasKey('X-amz-date', $inputs);
        $this->assertArrayHasKey('X-amz-signature', $inputs);
        $this->assertArrayHasKey('key', $inputs);
    }

    /** @test */
    public function it_returns_the_form_inputs_in_html_format()
    {
        $directo = new Directo('bucket', 'eu-central-1', 'key', 'secret');

        $this->assertEquals(implode(PHP_EOL, [
            '<input type="hidden" name="Content-Type" value=""/>',
            '<input type="hidden" name="acl" value="public-read"/>',
            '<input type="hidden" name="success_action_redirect" value=""/>',
            '<input type="hidden" name="success_action_status" value="201"/>',
            '<input type="hidden" name="policy" value="'. $directo->policy() .'"/>',
            '<input type="hidden" name="X-amz-credential" value="'. (new Credentials('key', 'eu-central-1', $directo->signingTime()))->AMZCredentials() .'"/>',
            '<input type="hidden" name="X-amz-algorithm" value="AWS4-HMAC-SHA256"/>',
            '<input type="hidden" name="X-amz-date" value="'. gmdate('Ymd\THis\Z', $directo->signingTime()) .'"/>',
            '<input type="hidden" name="X-amz-signature" value="'. $directo->signature() .'"/>',
            '<input type="hidden" name="key" value="${filename}"/>'
        ]), $directo->inputsAsHtml());
    }

    /** @test */
    public function it_sets_the_options_on_the_fly()
    {
        $options = Mockery::mock(Options::class);
        $options->shouldReceive('merge')->atLeast()->once();

        $directo = new Directo('bucket', 'eu-central-1', 'key', 'secret', $options);
        $directo->setOptions(['acl' => 'private']);
    }

    /** @test */
    function it_delegates_a_json_policy_signature_to_the_signature_object()
    {
        $directo = new Directo('bucket', 'eu-central-1', 'key', 'secret');

        $result = $directo->sign($this->getJsonPolicy());

        $policy = base64_decode($result['policy']);
        $this->assertEquals($this->getJsonPolicy(), $policy);
        $this->assertNotEquals('bucket', json_decode($policy, true)['conditions'][1]['bucket']);

        $this->assertArrayHasKey('policy', $result);
        $this->assertArrayHasKey('signature', $result);
        $this->assertTrue(ctype_alnum($result['signature']));
        $this->assertTrue(strlen($result['signature']) === 64);
        $this->assertEquals('e569448322b940de8a6317748475de959866614e81e3d04a7260353d1b5437f1', $result['signature']);
    }

    /** @test */
    function it_delegates_a_base64_encoded_policy_signature_to_the_signature_object()
    {
        $directo = new Directo('bucket', 'eu-central-1', 'key', 'secret');

        $result = $directo->sign(base64_encode($this->getJsonPolicy()));

        $policy = base64_decode($result['policy']);
        $this->assertEquals($this->getJsonPolicy(), $policy);
        $this->assertNotEquals('bucket', json_decode($policy, true)['conditions'][1]['bucket']);

        $this->assertArrayHasKey('policy', $result);
        $this->assertArrayHasKey('signature', $result);
        $this->assertTrue(ctype_alnum($result['signature']));
        $this->assertTrue(strlen($result['signature']) === 64);
        $this->assertEquals('e569448322b940de8a6317748475de959866614e81e3d04a7260353d1b5437f1', $result['signature']);
    }
}