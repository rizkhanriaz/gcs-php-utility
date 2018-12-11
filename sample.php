<?php

require(__DIR__. '/vendor/autoload.php');

use Gbucket\FS\FileSystem;
use Gbucket\CloudFiles\FileOperations;

$authFile = __DIR__ . '/keys/gcs-bucket-service-key.json';
$bucketName = 'your-bucket-name';

//upload
$uploadFileName = 'filename.png';
$uploadFilePath =  __DIR__ . '/filename.png';

//download
$destination = __DIR__ . '/downloads';

$gBucket = new FileOperations($bucketName, $authFile);


//$gBucket->uploadFile($uploadFileName, FileSystem::getContents($uploadFilePath));

//$gBucket->list_objects($bucketName);

//Download All assets from Bucket to local directory
$gBucket->download_objects($destination);
