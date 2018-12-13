<?php
namespace Gbucket\Lib;

class Data
{

    /**
     * @var string
     */
    private $bucketName;


    /**
     * @var string
     */
    private $jsonKeyPath;


    /**
     * @var string
     */
    private $syncFolder;


    /**
     * @var string
     */
    private $uploadFiletypes;


    /**
     * @return string
     */
    public function getBucketName()
    {
        return $this->bucketName;
    }



    /**
     * @param string $hostname
     * @return void
     */
    public function setBucketName($bucketName)
    {
        $this->bucketName = $bucketName;
    }


    /**
     * @param int $port
     * @return void
     */
    public function setJsonKeyPath($jsonKeyPath)
    {
        $this->jsonKeyPath = $jsonKeyPath;
    }


    /**
     * @param string $database
     * @return void
     */
    public function setSyncFolder($syncFolder)
    {
        $this->syncFolder = $syncFolder;
    }


    /**
     * @param string $username
     * @return void
     */
    public function setUploadFiletypes($uploadFiletypes)
    {
        $this->uploadFiletypes = '{'.$uploadFiletypes.'}';
    }

}
