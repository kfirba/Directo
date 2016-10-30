<?php
namespace Kfirba\Directo;

use Kfirba\Directo\Exceptions\InvalidACLException;
use Kfirba\Directo\Exceptions\InvalidExpiresStringException;

/**
 * Class Options
 *
 * @property integer success_action_redirect
 * @property integer success_action_status
 * @property string  acl
 * @property string  default_filename
 * @property int     max_file_size
 * @property string  expires
 * @property string  valid_prefix
 * @property string  content_type
 * @property array   additional_inputs
 *
 * @package Kfirba\Directo
 */
class Options
{
    /**
     * Default options.
     *
     * @var array
     */
    protected $options;

    /**
     * Options constructor.
     *
     * @param array $options
     */
    public function __construct(array $options = [])
    {
        $this->merge($options);
    }

    /**
     * Validates the ACL.
     *
     * @param $acl
     * @throws InvalidACLException
     */
    protected function validateACL($acl)
    {
        $availableACLs = [
            "private",
            "public-read",
            "public-read-write",
            "aws-exec-read",
            "authenticated-read",
            "bucket-owner-read",
            "bucket-owner-full-control",
            "log-delivery-write"
        ];

        if ( ! in_array($acl, $availableACLs)) {
            throw new InvalidACLException;
        }
    }

    /**
     * Validates the expires property to be within 1 second and 7 days in the future.
     *
     * @param $expires
     * @throws InvalidExpiresStringException
     */
    protected function validateExpiryString($expires)
    {
        $time = time();
        $exp = strtotime($expires, $time);
        $diff = $exp - $time;

        if ($diff < 1 || $diff > 604800) {
            throw new InvalidExpiresStringException;
        }
    }

    /**
     * Merges options.
     *
     * @param array $options
     * @return $this
     */
    public function merge(array $options)
    {
        if (array_key_exists('expires', $options)) {
            $this->validateExpiryString($options['expires']);
        }

        if (array_key_exists('acl', $options)) {
            $this->validateACL($options['acl']);
        }

        if (array_key_exists('success_status', $options)) {
            $options['success_status'] = (string) $options['success_status'];
        }

        $base = isset($this->options) ? $this->options : require('Support/directo.php');
        $this->options = array_merge($base, $options);

        return $this;
    }

    /**
     * Dynamically retrieve options.
     *
     * @param $property
     * @return mixed|null
     */
    public function __get($property)
    {
        return isset($this->options[$property]) ? $this->options[$property] : null;
    }
}