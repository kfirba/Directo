<?php
namespace Kfirba\Directo;

class Policy
{
    /**
     * @var Options
     */
    protected $options;

    /**
     * @var Credentials
     */
    protected $credentials;

    /**
     * @var string
     */
    protected $bucket;

    /**
     * The signing time.
     *
     * @var integer
     */
    protected $time;

    /**
     * Policy constructor.
     *
     * @param Options     $options
     * @param Credentials $credentials
     * @param             $time
     * @param             $bucket
     */
    public function __construct(Options $options, Credentials $credentials, $bucket, $time)
    {
        $this->options = $options;
        $this->credentials = $credentials;
        $this->bucket = $bucket;
        $this->time = $time;
    }

    /**
     * Generates the policy.
     *
     * @return string
     */
    public function generate()
    {
        $contentTypePrefix = empty($this->options->content_type) ? 'starts-with' : 'eq';

        $policy = [
            'expiration' => $this->expirationDate(),
            'conditions' => [
                ['bucket' => $this->bucket],
                ['acl' => $this->options->acl],
                ['starts-with', '$key', $this->options->valid_prefix],
                [$contentTypePrefix, '$Content-Type', $this->options->content_type],
                ['content-length-range', 0, $this->mbToBytes($this->options->max_file_size)],
                ['success_action_redirect' => $this->options->success_action_redirect],
                ['success_action_status' => $this->options->success_action_status],
                ['x-amz-credential' => $this->credentials->AMZCredentials()],
                ['x-amz-algorithm' => 'AWS4-HMAC-SHA256'],
                ['x-amz-date' => gmdate('Ymd\THis\Z', $this->time)]
            ]
        ];

        $this->addAdditionalInputs($policy);

        return base64_encode(json_encode($policy));
    }

    /**
     * Convert MB to Bytes.
     *
     * @param $mb
     * @return int|string
     */
    protected function mbToBytes($mb)
    {
        if (is_numeric($mb)) {
            return $mb * pow(1024, 2);
        }

        return 0;
    }

    /**
     * Get the expiration date in the right format.
     *
     * @return false|string
     */
    protected function expirationDate()
    {
        return gmdate(
            'Y-m-d\TG:i:s\Z',
            strtotime($this->options->expires, $this->time)
        );
    }

    /**
     * Adds any additional inputs to the policy.
     *
     * @param $policy
     * @return mixed
     */
    protected function addAdditionalInputs(&$policy)
    {
        foreach ($this->options->additional_inputs as $name => $value) {
            $policy['conditions'][] = ['starts-with', '$' . $name, $value];
        }
    }
}