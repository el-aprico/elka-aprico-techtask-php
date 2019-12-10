<?php

namespace App\Providers;

use Symfony\Component\HttpKernel\KernelInterface;

abstract class AppServiceProvider
{

    protected $path;
    protected $indexName;
    protected $jsonResult;
    private $rootDir;

    public function __construct(KernelInterface $kernel)
    {
        $this->rootDir = $kernel->getProjectDir();
        $this->jsonResult = [];
        $this->setFile($this->rootDir.DIRECTORY_SEPARATOR.$this->getFile());
        return $this;
    }

    public function setJsonResult($result)
    {
        $this->jsonResult = $result;
        return $this;
    }

    public function setFile($path)
    {
        $this->path = $path;
        return $this;
    }

    public function getFile()
    {
        return $this->path;
    }

    public function getJsonResult()
    {
        return $this->jsonResult;
    }

    public function setIndexName($indexName)
    {
        $this->indexName = $indexName;
        return $this;
    }

    public function getIndexName()
    {
        return $this->indexName;
    }

    public function readJson()
    {
        $filePath = $this->getFile();
        $jsonResult = [];
        if (file_exists($filePath)) {
            $contents = file_get_contents($filePath);
            $jsonResult = json_decode($contents, true);
            $indexName = $this->getIndexName();
            if (isset($jsonResult[$indexName])) {
                $jsonResult = $jsonResult[$indexName];
            }
        }
        $this->setJsonResult($jsonResult);
        return $this;
    }

    /**
     * @param $date
     * @return int
     */
    public function dateToInt($date = '')
    {
        if (empty($date)) {
            $date = date('Y-m-d');
        }
        return (int) str_replace('-', '', $date);
    }
}
