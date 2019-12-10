<?php

namespace App\Providers;

use App\Providers\AppServiceProvider;

class IngredientProvider extends AppServiceProvider
{

    CONST TITLE = 'title';

    protected $path = 'data/Ingredient/data.json';
    protected $indexName = 'ingredients';
    protected $date = '';
    protected $filtered = '';
    protected $filterAllowed = [
        'use-by' => 'UseBy',
        'best-before' => 'BestBefore'
    ];

    public function getFormatedJsonResult()
    {
        return array_column($this->jsonResult, self::TITLE);
    }

    public function setDate($date)
    {
        $this->date = $date;
        return $this;
    }

    public function getDate()
    {
        return $this->date;
    }

    public function setFiltered($args)
    {
        $this->filtered = $args;
        return $this;
    }

    public function getFiltered($args)
    {
        return $this->filtered;
    }

    public function freshFilter(array $args)
    {
        if ($args) {
            foreach ($this->filterAllowed as $key => $val) {
                if (isset($args[$key])) {
                    $this->{'applyFilterBy'.$val}($args[$key]);
                    break;
                }
            }
        }
        return $this;
    }

    public function applyFilterByBestBefore($date)
    {
        $name = 'best-before';
        $tmpUseBy = $this->applyFilterByUseBy($date);
        $tmpBest = $this->applyFiltered($name, $date, $tmpUseBy);
        $diff = array_diff_key($tmpUseBy, $tmpBest);
        $json = array_merge($tmpBest, $diff);
        $this->setJsonResult($json);
        return $json;
    }

    public function applyFilterByUseBy($date)
    {
        $name = 'use-by';
        $jsonRes = $this->getJsonResult();
        $json = $this->applyFiltered($name, $date, $jsonRes);
        $this->setJsonResult($json);
        return $json;
    }


    public function applyFiltered($name, $vals, $json = [])
    {
        if (!$json) {
            return $json;
        }
        $convDate = $this->dateToInt($vals);
        $resJson = [];
        foreach ($json as $key => $val) {
            $expiredDate = $this->dateToInt($val[$name]);
            if ($expiredDate >= $convDate) {
                /*
                 if bestBefore greter than conversion date, insert to $resJson
                 else insert to temporary
                */
                $resJson[$key] = $val;
            }
        }
        return $resJson;
    }
}
