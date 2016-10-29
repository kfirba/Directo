<?php
namespace Kfirba\Directo;

use Kfirba\Directo\Exceptions\InvalidRegionException;

class Directo
{
    /**
     * @var Signature
     */
    protected $signature;

    /**
     * @var Policy
     */
    protected $policy;

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

        $time = time();
        $options = $this->normalizeOptions($options);

        $credentials = $credentials ?: new Credentials($key, $region, $time);
        $this->policy = $policy ?: new Policy($options, $credentials, $bucket, $time);
        $this->signature = $signature ?: new Signature($secret, $region, $this->policy, $time);
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

        throw new InvalidArgumentException(sprintf(
            "The Options must to be either an instance of [%s] or an array",
            Options::class
        ));
    }

    /**
     * Validates the given region against Amazon S3 available regions.
     *
     * @throws InvalidRegionException
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
}