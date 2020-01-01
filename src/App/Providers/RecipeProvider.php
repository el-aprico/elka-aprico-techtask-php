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
        $jsonResult = $this->getJsonResult();
        foreach ($jsonResult as $key => $val) {
            $diff = array_diff($val['ingredients'], $args);
            if (!$diff) {
                $newJson[] = $val;
            }
        }
        $this->setJsonResult($newJson);
        return $this;
    }
}
