<?php

require(__DIR__. '/vendor/autoload.php');

use Gbucket\FS\FileSystem;
use Gbucket\CloudFiles\FileOperations;

$authFile = __DIR__ . '/keys/gcs-bucket-service-key.json';
$bucketName = 'your-bucket-key';

//upload
$uploadFileName = 'filename.png';
$uploadFilePath =  __DIR__ . '/filename.png';

//download
$objectName = 'folder/filename.jpg';
$destination = __DIR__ . '/downloads/filename.jpg';

$gBucket = new FileOperations($bucketName, $authFile);


$gBucket->uploadFile($uploadFileName, FileSystem::getContents($uploadFilePath));

$gBucket->list_objects($bucketName);

$gBucket->download_object($objectName, $destination);

echo 'success'. "\r\n";
