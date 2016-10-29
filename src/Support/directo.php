<?php

return [

    // The URL to which the client is redirected upon successful upload.
    'success_action_redirect' => null,

    // The status code returned to the client upon successful upload if
    // success_action_redirect is not specified.
    'success_action_status'   => "201",

    // The ACL for the uploaded file. More info: http://amzn.to/1SSOgwO
    // Supported: private, public-read, public-read-write, aws-exec-read, authenticated-read,
    //            bucket-owner-read, bucket-owner-full-control, log-delivery-write
    'acl'                     => 'public-read',

    // The file's name on s3, can be set with JS by changing the input[name="key"].
    // Leaving this as ${filename} will retain the original file's name.
    'default_filename'        => '${filename}',

    // The maximum file size of an upload in MB. Will refuse with a EntityTooLarge
    // and 400 Bad Request if you exceed this limit.
    'max_file_size'           => 500,

    // Request expiration time, specified in relative time format or in seconds.
    // min: 1 (+1 second), max: 604800 (+7 days)
    'expires'                 => '+6 hours',

    // Server will check that the filename starts with this prefix
    // and fail with a AccessDenied 403 if not.
    'valid_prefix'            => '',

    // Strictly only allow a single content type, blank will allow all.
    // Will fail with a AccessDenied 403 if this condition is not met.
    'content_type'            => '',

    // Any additional inputs to add to the form. This is an array of name => value
    // pairs e.g. ['Content-Disposition' => 'attachment']
    'additional_inputs'       => []

];