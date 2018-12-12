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
        }

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
        $dirs = array_filter(glob($uploadFolderPath.'/*'), 'is_dir');

        foreach ($dirs as $dir) {

            $folderNameExplode = explode('/', $dir);

            $folderName = end($folderNameExplode);

            $subFolderContents = glob($uploadFolderPath."/".$folderName."/*.".$uploadFileTypes, GLOB_BRACE);

            foreach ($subFolderContents as $subFolderContent) {

                $subFilePathExplode = explode('/', $subFolderContent);
                $subFileName = end($subFilePathExplode);

                $options['name'] = $folderName.'/'.$subFileName;

                $file = fopen($subFolderContent, 'r');

                echo 'Uploading '. $folderName.'/'.$subFileName . "\r\n";
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

            //check the content type to detect file or directory
            if($objInfo['contentType'] === 'application/x-www-form-urlencoded;charset=utf-8'){

                $dirPath = $destination.'/'.$objName;

                //if folder doesn't exist create folder
                if (!file_exists($dirPath)) {

                    mkdir($dirPath, 0777, true);
                    echo 'Folder Created'. $objName . "\r\n";
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
