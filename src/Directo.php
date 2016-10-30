<?php
namespace Kfirba\Directo;

use Kfirba\Directo\Exceptions\InvalidACLException;
use Kfirba\Directo\Exceptions\InvalidRegionException;
use Kfirba\Directo\Exceptions\InvalidOptionsException;

class Directo
{
    /**
     * The signing time.
     *
     * @var integer
     */
    protected $time;

    /**
     * @var string
     */
    protected $bucket;

    /**
     * @var string
     */
    protected $region;

    /**
     * @var Options
     */
    protected $options;

    /**
     * @var Credentials
     */
    protected $credentials;

    /**
     * @var Policy
     */
    protected $policy;

    /**
     * @var Signature
     */
    protected $signature;

    /**
     * Directo constructor.
     *
     * @param             $bucket
     * @param             $region
     * @param             $key
     * @param             $secret
     * @param null        $options
     * @param Credentials $credentials
     * @param Signature   $signature
     * @param Policy      $policy
     */
    public function __construct(
        $bucket,
        $region,
        $key,
        $secret,
        $options = null,
        Credentials $credentials = null,
        Signature $signature = null,
        Policy $policy = null
    ) {
        $this->validateRegion($region);

        $this->time = time();
        $this->bucket = $bucket;
        $this->region = $region;
        $this->options = $this->normalizeOptions($options);

        $this->credentials = $credentials ?: new Credentials($key, $region, $this->time);
        $this->policy = $policy ?: new Policy($this->options, $this->credentials, $bucket, $this->time);
        $this->signature = $signature ?: new Signature($secret, $region, $this->policy, $this->time);
    }

    /**
     * Normalizes the Options object.
     *
     * @param $options
     * @return Options
     */
    protected function normalizeOptions($options)
    {
        if (is_null($options)) {
            return new Options;
        }

        if (is_array($options)) {
            return new Options($options);
        }

        if ($options instanceof Options) {
            return $options;
        }

        throw new InvalidOptionsException;
    }

    /**
     * Validates the given region against Amazon S3 available regions.
     *
     * @param $region
     * @throws InvalidACLException
     */
    protected function validateRegion($region)
    {
        $availableRegions = [
            'us-east-1',
            'us-east-2',
            'us-west-1',
            'us-west-2',
            'ap-south-1',
            'ap-northeast-2',
            'ap-southeast-1',
            'ap-southeast-2',
            'ap-northeast-1',
            'eu-central-1',
            'eu-west-1',
            'sa-east-1'
        ];

        if ( ! in_array($region, $availableRegions)) {
            throw new InvalidRegionException;
        }
    }

    /**
     * Get the signature
     *
     * @return string
     */
    public function signature()
    {
        return $this->signature->generate();
    }

    /**
     * Get the policy.
     *
     * @return string
     */
    public function policy()
    {
        return $this->policy->generate();
    }

    /**
     * Get the action string for the upload form.
     *
     * @return string
     */
    public function formUrl()
    {
        return sprintf(
            '//%s.s3-%s.amazonaws.com',
            $this->bucket,
            $this->region
        );
    }

    /**
     * Gets the hidden inputs in array format.
     *
     * @return array
     */
    public function inputsAsArray()
    {
        $inputs = [
            'Content-Type'            => $this->options->content_type,
            'acl'                     => $this->options->acl,
            'success_action_redirect' => $this->options->success_action_redirect,
            'success_action_status'   => $this->options->success_action_status,
            'policy'                  => $this->policy(),
            'X-amz-credential'        => $this->credentials->AMZCredentials(),
            'X-amz-algorithm'         => 'AWS4-HMAC-SHA256',
            'X-amz-date'              => gmdate('Ymd\THis\Z', $this->time),
            'X-amz-signature'         => $this->signature(),
            'key'                     => $this->options->default_filename
        ];

        $inputs = array_merge($inputs, $this->options->additional_inputs);

        return $inputs;
    }

    /**
     * Get the hidden inputs as HTML.
     *
     * @return string
     */
    public function inputsAsHtml()
    {
        $inputs = [];
        foreach ($this->inputsAsArray() as $key => $value) {
            $inputs[] = sprintf(
                '<input type="hidden" name="%s" value="%s"/>',
                $key, $value
            );
        }

        return implode(PHP_EOL, $inputs);
    }

    /**
     * Merge options on the fly.
     *
     * @param array $options
     */
    public function setOptions(array $options = [])
    {
        $this->options->merge($options);
    }

    /**
     * The signing time.
     *
     * @return int
     */
    public function signingTime()
    {
        return $this->time;
    }
}