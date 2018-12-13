<?php

namespace Gbucket\CloudFiles;

use Google\Cloud\Storage\StorageObject;
use Gbucket\Authenticate\GoogleAuthenticate;
use Gbucket\FS\FileSystem;
use Gbucket\Exceptions\ObjectNotExistException;

class FileOperations
{
    /**
     * Google cloud bucket object
     */
    private $Gbucket;

    private $bucketName;

    protected $authFileContent;

    /**
     * Class constructor
     * @param Google\Cloud\Storage\Bucket $gBucket
     */
    public function __construct($bucketName, $authFile)
    {
        $this->bucketName = $bucketName;
        $this->authFileContent = FileSystem::getContents($authFile);
        $this->Gbucket = $this->initializeApi();
    }

    private function initializeApi()
    {
        $auth = new GoogleAuthenticate($this->authFileContent);
        $storage = $auth->authenticate();
        $bucket = $storage->bucket($this->bucketName);

        return $bucket;
    }

    //get sub directories and files
    private function rglob ($pattern, $flags = 0, $traversePostOrder = false) {
        // Keep away the hassles of the rest if we don't use the wildcard anyway
        if (strpos($pattern, '/**/') === false) {
            return glob($pattern, $flags);
        }

        $patternParts = explode('/**/', $pattern);

        // Get sub dirs
        $dirs = glob(array_shift($patternParts) . '/*', GLOB_ONLYDIR | GLOB_NOSORT);

        // Get files for current dir
        $files = glob($pattern, $flags);

        foreach ($dirs as $dir) {
            $subDirContent = $this->rglob($dir . '/**/' . implode('/**/', $patternParts), $flags, $traversePostOrder);

            if (!$traversePostOrder) {
                $files = array_merge($files, $subDirContent);
            } else {
                $files = array_merge($subDirContent, $files);
            }
        }

        return $files;
    }

    /**
     * Upload an asset
     *
     * @param string $uploadFolderPath
     * @param string $uploadFileTypes
     * @param boolean $publicAccess
     */
    public function uploadFile($uploadFolderPath, $uploadFileTypes, $publicAccess = true)
    {

        $bucket = $this->Gbucket;
        $options = array();

        //make objects public on upload
        if ($publicAccess) {

            $options['predefinedAcl'] = 'PUBLICREAD';
        };

        //upload files
        $filespaths = glob($uploadFolderPath."/*.".$uploadFileTypes, GLOB_BRACE);

        foreach ($filespaths as $filePath) {

            $filePathExplode = explode('/', $filePath);

            $fileName = end($filePathExplode);
            $options['name'] = $fileName;

            $file = fopen($filePath, 'r');

            echo 'Uploading '. $fileName . "\r\n";
            $bucket->upload($file, $options);
        }

        //upload files in sub directories
        $dirs = $this->rglob($uploadFolderPath.'**/**');

        foreach ($dirs as $dir) {

            $folderNameExplode = explode('/', $dir);

            $folderName = end($folderNameExplode);

            $file_parts = pathinfo($folderName);

            $dirTrimed = str_replace($uploadFolderPath, "", $dir);

            if(isset($file_parts['extension'])){

                $dirTrimedExplode = explode('/', $dirTrimed);
                $subFileName = end($dirTrimedExplode);

                $options['name'] = $dirTrimed;

                $file = fopen($dir, 'r');
                echo 'Uploading '. $dirTrimed . "\r\n";
                $bucket->upload($file, $options);

            }

        }
    }

    /**
     * Delete an object from google storage
     * @param string $filename
     */
    public function deleteFile($filename)
    {
        $object = $this->Gbucket->object($filename);

        if (!$object->exists()) {
            throw new ObjectNotExistException("Object not exist on bucket");
            return false;
        }

        $object->delete();

    }

    /**
     * List Cloud Storage bucket files and Directories.
     * @return void
     */
    public function list_objects()
    {

        $bucket = $this->Gbucket;
        foreach ($bucket->objects() as $object) {
            printf('Object: %s' . PHP_EOL, $object->name());
        }
    }


    /**
     * Download an object from Cloud Storage and save it as a local file.
     *
     * @param string $destination the local destination to save the encrypted object.
     *
     * @return void
     */
    public function download_objects($destination)
    {
        $bucket = $this->Gbucket;
        foreach ($bucket->objects() as $object) {

            $objName = $object->name();
            $objInfo = $object->info();

            $folderPathExplode = explode('/', $objName);
            $fileOrFolderName = end($folderPathExplode);
            $file_parts = pathinfo($fileOrFolderName);


            if(isset($file_parts['extension'])){

                $folderPath = str_replace($fileOrFolderName, "", $objName);
                $folderPath = $destination.'/'.$folderPath;

                if (!file_exists($folderPath)) {

                    mkdir($folderPath, 0777, true);
                    echo 'Folder Created '. $folderPath . "\r\n";
                }
            }

            if($objInfo['contentType'] === 'application/x-www-form-urlencoded;charset=utf-8'){

                $dirPath = $destination.'/'.$objName;

                //if folder doesn't exist create folder
                if (!file_exists($dirPath)) {

                    mkdir($dirPath, 0777, true);
                    echo 'Folder Created '. $objName . "\r\n";
                }

            }else{

                echo 'Downloading '. $objName . "\r\n";
                $destinationObj = $destination.'/'.$objName;
                $object->downloadToFile($destinationObj);
            }
        }

        echo 'Task download objects success'. "\r\n";
    }
}
