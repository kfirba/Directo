<?php

namespace Kfirba\Directo;

class Signature
{
    /**
     * @var string
     */
    protected $secret;

    /**
     * @var string
     */
    protected $region;

    /**
     * @var mixed
     */
    protected $policy;

    /**
     * The signing time.
     *
     * @var int
     */
    protected $time;

    public function __construct($secret, $region, $policy, $time = null)
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
        return $this->sign()['signature'];
    }

    /**
     * Signs the given policy (json or base64-encoded).
     *
     * @return array
     */
    public function sign()
    {
        $policy = $this->getBase64EncodedPolicy();

        if (is_null($this->time)) {
            $this->time = $this->extractTimestampFromBase64EncodedPolicy($policy);
        }

        return [
            'policy'    => $policy,
            'signature' => $this->keyHash($policy, $this->signingKey(), false),
        ];
    }

    /**
     * Get base64-encoded policy.
     *
     * @return string
     */
    protected function getBase64EncodedPolicy()
    {
        if ($this->policy instanceof Policy) {
            return $this->policy->generate();
        }

        json_decode($this->policy);

        // If there was an error while attempting to json_decode the policy, we assume it is already a base64 policy.
        return json_last_error() === JSON_ERROR_NONE ? base64_encode($this->policy) : $this->policy;
    }

    /**
     * Extracts the timestamp from the given policy.
     *
     * @param $policy
     *
     * @return int
     */
    protected function extractTimestampFromBase64EncodedPolicy($policy)
    {
        preg_match('/"x-amz-date":"(.+?Z)/i', base64_decode($policy), $matches);

        return \DateTime::createFromFormat('Ymd\THis\Z', $matches[1])->getTimestamp();
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
            'aws4_request',
        ];
    }

    /**
     * Hash the given value with the given key.
     *
     * @param      $value
     * @param      $key
     * @param bool $raw
     *
     * @return string
     */
    protected function keyHash($value, $key, $raw = true)
    {
        return hash_hmac('sha256', $value, $key, $raw);
    }
}
