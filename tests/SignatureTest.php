<?php

use Kfirba\Directo\Policy;
use Kfirba\Directo\Signature;

class SignatureTest extends TestCase
{
    /** @test */
    public function it_should_generate_the_signature()
    {
        $time = 1477763563;
        $policy = Mockery::mock(Policy::class);
        $policy->shouldReceive('generate')
            ->once()
            ->andReturn('eyJleHBpcmF0aW9uIjoiMjAxNi0xMC0yOVQyMzo1Mjo0M1oiLCJjb25kaXRpb25zIjpbeyJidWNrZXQiOiJidWNrZXQifSx7ImFjbCI6InB1YmxpYy1yZWFkIn0sWyJzdGFydHMtd2l0aCIsIiRrZXkiLCIiXSxbInN0YXJ0cy13aXRoIiwiJENvbnRlbnQtVHlwZSIsIiJdLFsiY29udGVudC1sZW5ndGgtcmFuZ2UiLDAsNTI0Mjg4MDAwXSx7InN1Y2Nlc3NfYWN0aW9uX3N0YXR1cyI6IjIwMSJ9LHsieC1hbXotY3JlZGVudGlhbCI6ImtleVwvMjAxNjEwMjlcL3JlZ2lvblwvczNcL2F3czRfcmVxdWVzdCJ9LHsieC1hbXotYWxnb3JpdGhtIjoiQVdTNC1ITUFDLVNIQTI1NiJ9LHsieC1hbXotZGF0ZSI6IjIwMTYxMDI5VDE3NTI0M1oifV19');

        $signature = (new Signature('secret', 'eu-central-1', $policy, $time))->generate();

        $this->assertTrue(strlen($signature) === 64);
        $this->assertTrue(ctype_alnum($signature));
        $this->assertEquals('81c617d1bbd1cc477821f40492d82fb21e7b72824a8b1a9e0a5e8621281c5b90', $signature);
    }

    /** @test */
    public function it_signs_a_given_json_policy()
    {
        $policy = $this->getJsonPolicy();
        $signature = new Signature('secret', 'eu-central-1', $policy);

        $result = $signature->sign();

        $this->assertEquals($this->getJsonPolicy(), base64_decode($result['policy']));

        $this->assertArrayHasKey('policy', $result);
        $this->assertArrayHasKey('signature', $result);
        $this->assertTrue(ctype_alnum($result['signature']));
        $this->assertTrue(strlen($result['signature']) === 64);
        $this->assertEquals('e569448322b940de8a6317748475de959866614e81e3d04a7260353d1b5437f1', $result['signature']);
    }

    /** @test */
    function it_signs_a_given_base64_encoded_policy()
    {
        $policy = base64_encode($this->getJsonPolicy());
        $signature = new Signature('secret', 'eu-central-1', $policy);

        $result = $signature->sign();

        $this->assertEquals($this->getJsonPolicy(), base64_decode($result['policy']));

        $this->assertArrayHasKey('policy', $result);
        $this->assertArrayHasKey('signature', $result);
        $this->assertTrue(ctype_alnum($result['signature']));
        $this->assertTrue(strlen($result['signature']) === 64);
        $this->assertEquals('e569448322b940de8a6317748475de959866614e81e3d04a7260353d1b5437f1', $result['signature']);
    }
}