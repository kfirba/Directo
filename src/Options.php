<?php
namespace Kfirba\Directo;

use Kfirba\Directo\Exceptions\InvalidACLException;
use Kfirba\Directo\Exceptions\InvalidExpiresStringException;

/**
 * Class Options
 *
 * @property integer success_status
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
    protected $options = [

        // The http code return by Amazon S3 upon successful upload.
        'success_status'    => "201",

        // The ACL for the uploaded file. More info: http://amzn.to/1SSOgwO
        // Supported: private, public-read, public-read-write, aws-exec-read, authenticated-read,
        //            bucket-owner-read, bucket-owner-full-control, log-delivery-write
        'acl'               => 'public-read',

        // The file's name on s3, can be set with JS by changing the input[name="key"].
        // Leaving this as ${filename} will retain the original file's name.
        'default_filename'  => '${filename}',

        // The maximum file size of an upload in MB. Will refuse with a EntityTooLarge
        // and 400 Bad Request if you exceed this limit.
        'max_file_size'     => 500,

        // Request expiration time, specified in relative time format or in seconds.
        // min: 1 (+1 second), max: 604800 (+7 days)
        'expires'           => '+6 hours',

        // Server will check that the filename starts with this prefix
        // and fail with a AccessDenied 403 if not.
        'valid_prefix'      => '',

        // Strictly only allow a single content type, blank will allow all.
        // Will fail with a AccessDenied 403 if this condition is not met.
        'content_type'      => '',

        // Any additional inputs to add to the form. This is an array of name => value
        // pairs e.g. ['Content-Disposition' => 'attachment']
        'additional_inputs' => []
    ];

    /**
     * Options constructor.
     *
     * @param array $options
     */
    public function __construct(array $options = [])
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

        $this->options = $options + $this->options;
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