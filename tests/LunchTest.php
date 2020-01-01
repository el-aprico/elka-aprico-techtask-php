<?php

namespace App\Tests;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpKernel\KernelInterface;

/**
 * Description of LunchController
 *
 * @author reachusolutions
 */
class LunchTest extends WebTestCase
{
    const URL = 'api/v1/lunch';
    const RESPONSE_CODE = 200;
    const PATH_INGREDIENT = 'data/Ingredient/data.json';
    const INDEX_INGREDIENT = 'ingredients';
    const PATH_RECIPE = 'data/Recipe/data.json';
    const INDEX_RECIPE = 'recipes';

    public function testValidResponseUrl()
    {
        $client = static::createClient();
        $client->request('GET', self::URL);
        $response = $client->getResponse();
    	$content = $response->getContent();
    	$this->assertJson($content);
    }

    public function testValidAllBestBeforeIsExpired()
    {
        $url = self::URL.'?best-before='.$this->getDateAllWillExpired();
        $content = $this->getClientContent($url);
    	$this->assertEquals(0, $content['total']);
    }

    public function testValidAllUseByIsExpired()
    {
        $url = self::URL.'?use-by='.$this->getDateAllWillExpired();
        $content = $this->getClientContent($url);
    	$this->assertEquals(0, $content['total']);
    }

    public function testValidLunch()
    {
        // get response from test
        $appPath = $this->getAppPath();
        $fileIngredient = $appPath.DIRECTORY_SEPARATOR.self::PATH_INGREDIENT;
        $indexIngredient = self::INDEX_INGREDIENT;
        $jsonIngredient = $this->getJsonResult(
            $fileIngredient, $indexIngredient
        );
        $fileRecipe = $appPath.DIRECTORY_SEPARATOR.self::PATH_RECIPE;
        $indexRecipe = self::INDEX_RECIPE;
        $jsonRecipe = $this->getJsonResult(
            $fileRecipe, $indexRecipe
        );
        $newJson = [];
        $castJsonIngredient = array_column($jsonIngredient, 'title');
        foreach ($jsonRecipe as $key => $val) {
            $diff = array_diff($val[$indexIngredient], $castJsonIngredient);
            if (!$diff) {
                $newJson[] = $val;
            }
        }
        $resJson = $this->getResponseWithData($newJson);

        // get response from client
        $url = self::URL;
        $content = $this->getClientContent($url);

        $this->assertEquals($resJson, $content);
    }

    public function testValidContainLunchByBestBefore()
    {
        return $this->validLunchContainBy('best-before');
    }

    public function testValidContainLunchByUseBy()
    {
        return $this->validLunchContainBy('use-by');
    }

    public function testValidLunchByBestBefore()
    {
        return $this->validLunchEqualBy('best-before');
    }

    public function testValidLunchByUseBy()
    {
        return $this->validLunchEqualBy('use-by');
    }

    private function validLunchContainBy($by)
    {
        $shortestRecipe = $this->getRecipeWithShortestDate($by);
        // get response from client
        $url = self::URL.'?'.$by.'='.$shortestRecipe['shortestDate'];
        $content = $this->getClientContent($url);
        $recipeTitle = $shortestRecipe['recipe']['title'];

        $contentData = $content['data'];
        $titles = array_column($contentData, 'title');
        $this->assertContains($recipeTitle, $titles);
    }

    private function validLunchEqualBy($by)
    {
        $indexIngredient = self::INDEX_INGREDIENT;
        $shortestRecipe = $this->getRecipeWithShortestDate($by);
        $ingredients = array_column($shortestRecipe[$indexIngredient], 'title');

        $newJson = [];
        foreach ($shortestRecipe['recipes'] as $key => $val) {
            $diff = array_diff($val[$indexIngredient], $ingredients);
            if (!$diff) {
                $newJson[] = $val;
            }
        }
        // get response from client
        $url = self::URL.'?'.$by.'='.$shortestRecipe['shortestDate'];
        $content = $this->getClientContent($url);
        $resJson = $this->getResponseWithData($newJson);
        $this->assertEquals($resJson, $content);
    }

    private function getRecipeWithShortestDate($indexName)
    {
        // get response from test first
        $indexIngredient = self::INDEX_INGREDIENT;

        $appPath = $this->getAppPath();
        $fileRecipe = $appPath.DIRECTORY_SEPARATOR.self::PATH_RECIPE;
        $indexRecipe = self::INDEX_RECIPE;
        $jsonRecipe = $this->getJsonResult(
            $fileRecipe, $indexRecipe
        );
        // get index whatever you want
        // this is the key
        $recipe = $jsonRecipe[0];
        // get all containt ingredients
        $ingredients = [];
        foreach ($recipe[$indexIngredient] as $value) {
            $ingredients[] = $value;
        }

        $appPath = $this->getAppPath();
        $fileIngredient = $appPath.DIRECTORY_SEPARATOR.self::PATH_INGREDIENT;
        $indexIngredient = self::INDEX_INGREDIENT;
        $jsonIngredient = $this->getJsonResult(
            $fileIngredient, $indexIngredient
        );
        // get shortest best-before
        $shortestDate = 0;
        foreach ($jsonIngredient as $key => $value) {
            $time = strtotime($value[$indexName]);
            if (empty($shortestDate)) {
                $shortestDate = $time;
            } else {
                if ($shortestDate > $time) {
                    $shortestDate = $time;
                }
            }
        }
        $shortestDate = date('Y-m-d', $shortestDate);
        // cast $recipe into array, due client respose will multidimensional
        $response = [
            'recipe' => $recipe,
            'recipes' => $jsonRecipe,
            'ingredients' => $jsonIngredient,
            'shortestDate' => $shortestDate
        ];
        return $response;
    }

    private function getClientContent($url)
    {
        $client = static::createClient();
        $client->request('GET', $url);
        $response = $client->getResponse();
    	$content = json_decode($response->getContent(), true);
        return $content;
    }

    private function getResponseWithData($data = [])
    {
        $response = [
            'success' => true,
            'code' => self::RESPONSE_CODE,
            'data' => $data,
            'total' => sizeof($data)
        ];
        return $response;
    }

    private function getJsonResult($filePath, $indexName)
    {
        $jsonResult = [];
        if (file_exists($filePath)) {
            $contents = file_get_contents($filePath);
            $jsonResult = json_decode($contents, true);
            if (isset($jsonResult[$indexName])) {
                $jsonResult = $jsonResult[$indexName];
            }
        }
        return $jsonResult;
    }

    private function getAppPath()
    {
        self::bootKernel();
        $container = self::$kernel;
        return $container->getProjectDir();
    }

    private function getDateAllWillExpired()
    {
        return '2999-12-31';
    }
}
