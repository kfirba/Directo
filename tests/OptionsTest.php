<?php

use Kfirba\Directo\Options;

class OptionsTest extends PHPUnit_Framework_TestCase
{
    /** @test */
    public function it_merges_default_options_with_given_options()
    {
        $overrides = ['acl' => 'private', 'max_file_size' => 100];

        $options = new Options($overrides);

        $this->assertEquals('private', $options->acl);
        $this->assertEquals(100, $options->max_file_size);
    }

    /**
     * @test
     * @expectedException Kfirba\Directo\Exceptions\InvalidACLException
     */
    public function it_should_fail_if_the_acl_is_not_available()
    {
        new Options(['acl' => 'gibberish']);
    }

    /**
     * @test
     * @expectedException Kfirba\Directo\Exceptions\InvalidExpiresStringException
     */
    public function it_should_fail_if_the_expires_property_is_0()
    {
        new Options(['expires' => '+0 hours']);
    }

    /**
     * @test
     * @expectedException Kfirba\Directo\Exceptions\InvalidExpiresStringException
     */
    public function it_should_fail_if_the_expires_property_is_not_within_7_days()
    {
        new Options(['expires' => '+691200 hours']); // 8 days
    }
}