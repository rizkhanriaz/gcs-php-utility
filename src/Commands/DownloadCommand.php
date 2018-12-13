<?php

namespace Gbucket\Commands;

use Pimple\Container;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputArgument;
use Gbucket\Lib\Config;
use Gbucket\CloudFiles\FileOperations;

class DownloadCommand extends Command
{

    /**
     * @var DBDump\Lib\Config
     */
    public $gBucket;

    /**
     * @var DBDump\Lib\Config
     */
    protected $config;

    /**
     * @var DBDump\Database\Connection
     */
    protected $data;


    /**
     * @param Container $container
     */
    public function __construct(Container $container)
    {
        parent::__construct();
        $this->config = $container['config'];
        $this->data = $container['data'];

    }

    protected function configure()
    {
        $this->setName('gcs:download')
            ->setDescription('Download all assets from Google bucket');
            // ->setHelp('Demonstration of custom commands created by Symfony Console component.')
            // ->addArgument('username', InputArgument::REQUIRED, 'Pass the username.');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {

        $bucketName = $this->config->get('bucket.name');
        $authFile = $this->config->get('bucket.key');

        $gBucket = new FileOperations($bucketName, $authFile);

        $gBucket->download_objects($this->config->get('bucket.syncfolder'));

    }
}
