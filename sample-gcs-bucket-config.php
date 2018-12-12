<?php

require(__DIR__. '/vendor/autoload.php');

use Gbucket\FS\FileSystem;
use Gbucket\CloudFiles\FileOperations;

$authFile = __DIR__ . '/keys/gcs-bucket-service-key.json';
$bucketName = 'your-bucket-name';

//upload
$uploadFileTypes =  '{jpg,gif,png,PNG,jpeg}';

//assets
$assestsPath = __DIR__ . '/assets';

$gBucket = new FileOperations($bucketName, $authFile);

//Upload All assets from local directory to Bucket
$gBucket->uploadFile($assestsPath, $uploadFileTypes);

//$gBucket->list_objects($bucketName);

//Download All assets from Bucket to local directory
//$gBucket->download_objects($assestsPath);

