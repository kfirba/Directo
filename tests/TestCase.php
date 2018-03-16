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

    protected function getJsonPolicy()
    {
        return json_encode([
            'expiration' => '2018-03-16T09:10:45.082Z',
            'conditions' => [
                ['acl' => 'private'],
                ['bucket' => 'bucket-name'],
                ['Content-Type'=> 'image/jpeg'],
                ['success_action_status' => '200'],
                ['key' => '37f86eae-46e9-4047-91c1-c8ee2cbf90b6.jpg'],
                ['x-amz-credential' => 'GWRQRFFXBA3C3S7GOVQQ/20180316/eu-central-1/s3/aws4\_request'],
                ['x-amz-date' => '20180316T090545Z'],
                ['x-amz-meta-qqfilename' => 'JPG-GG\_400x400.jpg'],
                ['content-length-range', '0', '23424'],
            ],
        ]);
    }
}