<?php
namespace Kfirba\Directo;

class Signature
{
    /**
     * @var string
     */
    protected $signature;

    /**
     * @var string
     */
    protected $secret;

    /**
     * @var string
     */
    protected $region;

    /**
     * @var Policy
     */
    protected $policy;

    /**
     * The signing time.
     *
     * @var integer
     */
    protected $time;

    public function __construct($secret, $region, Policy $policy, $time)
    {
        $this->secret = $secret;
        $this->region = $region;
        $this->policy = $policy;
        $this->time = $time;
    }

    /**
     * Generates the signature.
     *
     * @return string
     */
    public function generate()
    {
        if (isset($this->signature)) {
            return $this->signature;
        }

        return $this->signature = $this->keyHash(
            $this->policy->generate(), $this->signingKey(), false
        );
    }

    /**
     * Generates the signing key.
     *
     * @return mixed
     */
    protected function signingKey()
    {
        return array_reduce($this->signatureData(), function ($key, $data) {
            return $this->keyHash($data, $key);
        }, 'AWS4' . $this->secret);
    }

    /**
     * Get the signature data.
     *
     * @return array
     */
    protected function signatureData()
    {
        return [
            gmdate('Ymd', $this->time),
            $this->region,
            's3',
            'aws4_request'
        ];
    }

    /**
     * Hash the given value with the given key.
     *
     * @param      $value
     * @param      $key
     * @param bool $raw
     * @return string
     */
    protected function keyHash($value, $key, $raw = true)
    {
        return hash_hmac('sha256', $value, $key, $raw);
    }
}