<?php

namespace App\Providers;

use App\Providers\AppServiceProvider;

class RecipeProvider extends AppServiceProvider
{

    protected $path = 'data/Recipe/data.json';
    protected $indexName = 'recipes';

    public function byIngredients(array $args)
    {
        $newJson = [];
        $xxxx = $this->getJsonResult();
        foreach ($xxxx as $key => $val) {
            $diff = array_diff($val['ingredients'], $args);
            if (!$diff) {
                $newJson[] = $val;
            }
        }
        $this->setJsonResult($newJson);
        return $this;
    }
}
