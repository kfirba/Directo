Directo
=============

A library to generate AWS Signature V4 and supplement form inputs, for direct upload to Amazon S3. Includes a bridge to Laravel as-well.

Install
--------
```bash
$ composer require kfirba/directo
```

Prerequisites - CORS & Bucket Policy
--------
In order for the upload to succeed and not get denied by Amazon S3 we will need to configure the CORS option and the bucket policy.

In order to update the CORS and the bucket policy, log in to your AWS account and go to S3. Now select your bucket and click **Properties** on the top-right corner. Click on **Permissions**.

To update the CORS configuration, click the **Edit CORS Configuration** button. I recommend the following CORS configuration:

```xml
<?xml version="1.0" encoding="UTF-8"?>
<CORSConfiguration xmlns="http://s3.amazonaws.com/doc/2006-03-01/">
    <CORSRule>
        <AllowedOrigin>*</AllowedOrigin>
        <AllowedMethod>GET</AllowedMethod>
        <AllowedMethod>POST</AllowedMethod>
        <AllowedMethod>PUT</AllowedMethod>
        <MaxAgeSeconds>3000</MaxAgeSeconds>
        <AllowedHeader>*</AllowedHeader>
    </CORSRule>
</CORSConfiguration>
```

To update the bucket policy, click the **Edit bucket policy** button. I recommend the following bucket policy:

```js
{
	"Version": "2012-10-17",
	"Statement": [
		{
			"Effect": "Allow",
			"Principal": "*",
			"Action": [
				"s3:PutObject",
				"s3:PutObjectAcl"
			],
			"Resource": "arn:aws:s3:::BUCKET_NAME/*"
		}
	]
}
```

Don't forget to change `BUCKET_NAME` to your bucket name.

**Note:** If you don't plan to ever upload any file with any other ACL than `private`, you may want to omit the `"s3:PutObjectAcl"`.

Laravel Users
--------
If you are a Laravel user, the package contains a bridge for you.

Update your **app.php** `providers` array and add:

```php
Kfirba\Directo\Support\DirectoServiceProvider::class,
```

Also, update your `aliases` array and add:

```
'Directo' => Kfirba\Directo\Support\Facades\Directo::class,
```

Usage
--------
```javascript
use Kfirba\Directo\Directo;

require_once __DIR__ . "/vendor/autoload.php";

$directo = new Directo('bucket', 'region', 'key', 'secret', $options = []);
```

Then, using the object we've just made, we can generate the form's url and all the needed hidden inputs.

```php
<form action="<?php echo $directo->formUrl()?>" method="post" ectype="multipart/form-data">
    <?php echo $directo->inputsAsHtml() ?>
    
    <input type="file" name="file">
</form>
```

Laravel Users Usage
--------
If you are using laravel, there is a `Directo` facade you can use. Also, you can type-hint the `Directo` class in any auto-resolving class, such as any `Controller` and it will be automatically resolved.

```php
// Facade:
Directo::formUrl();
Directo::inputsAsHtml();

// Type-hinted
// SomeController.php:

use Kfirba\Directo\Directo;
// ...
public function index(Directo $directo)
{
    dd($directo->signature());
}
```


Options
--------

The options availalbe are:

| Option            | Default     | Description  |
| ----------------- | ----------- |------------- |
| success_action_redirect    |          | The URL to which the client is redirected upon successful upload. Useful if you are not using any kind of AJAX uploading mechanism. |
| success_action_status    | 201         | The status code returned to the client upon successful upload if `success_action_redirect` is not specified. |
| acl               | public-read     | The ACL for the uploaded file. **Supported:** private, public-read, public-read-write, aws-exec-read, authenticated-read, bucket-owner-read, bucket-owner-full-control, log-delivery-write |
| default_filename  | ${filename} | The file's name on s3, can be set with JS by changing the input[name="key"]. Leaving this as ${filename} will retain the original file's name. |
| max_file_size     | 500         | The maximum file size of an upload in MB. Will refuse with a EntityTooLarge and 400 Bad Request if you exceed this limit. |
| expires           | +6 hours    | Request expiration time, specified in relative time format or in seconds. min: 1 (+1 second), max: 604800 (+7 days) |
| valid_prefix      |             | Server will check that the filename starts with this prefix and fail with a AccessDenied 403 if not. |
| content_type      |             | Strictly only allow a single content type, blank will allow all. Will fail with a AccessDenied 403 is this condition is not met. |
| additional_inputs |             | Any additional inputs to add to the form. This is an array of name => value pairs e.g. ['Content-Disposition' => 'attachment'] |

For example:

```php
$directo = new Directo("bucket", "region", "key", "secret", [
    'acl' => 'private',
    'max_file_size' => 10,
    'additional_inputs' => [
        'Content-Disposition' => 'attachment'
    ]
]);
```

Available Methods
--------

| Method                | Description  |
| --------------------- | ------------ |
| signature()          | Get the AWS Signature (V4). Useful if you are using a plugin such as [FineUploader](http://fineuploader.com/) which requires an endpoint to return a signature. |
| policy()        | Get the policy used by the signature. |
| inputsAsArray()       | Returns an **array** of all the inputs you'll need to submit in your form. |
| inputsAsHtml() | Returns an **HTML string** of all the inputs you'll need to submit in your form. |

Thumbs Up :thumbsup:
--------
I want to thank [Edd Turtle](https://www.designedbyaturtle.co.uk/) for guiding me in the right way with his [GREAT article](https://www.designedbyaturtle.co.uk/2015/direct-upload-to-s3-using-aws-signature-v4-php/). The purpose of this package is to make make the process of signature generating a bit more modular and create a bridge for Laravel users.

License
--------
Directo is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).
