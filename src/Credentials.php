<?php
namespace Kfirba\Directo;

class Credentials
{
    /**
     * @var string
     */
    protected $credentials;

    /**
     * @var string
     */
    protected $key;

    /**
     * @var string
     */
    protected $region;

    /**
     * @var string
     */
    protected $time;

    public function __construct($key, $region, $time)
    {
        $this->key = $key;
        $this->region = $region;
        $this->time = $time;
    }

    /**
     * Generate the AMZ Credentials.
     *
     * @return string
     */
    public function AMZCredentials()
    {
        if (isset($this->credentials)) {
            return $this->credentials;
        }

        return $this->credentials = sprintf(
            '%s/%s/%s/s3/aws4_request',
            $this->key,
            gmdate('Ymd', $this->time),
            $this->region
        );
    }
}