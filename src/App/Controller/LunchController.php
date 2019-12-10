<?php

namespace App\Controller;

use App\Controller\ApiController;
use App\Providers\IngredientProvider;
use App\Providers\RecipeProvider;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\KernelInterface;

class LunchController extends ApiController
{

    /**
     * Matches /ingredient exactly
     *
     * @Route("/ingredient", name="lunch_list")
     *
     * @param  \Symfony\Component\HttpKernel\KernelInterface
     * @param  \Symfony\Component\HttpFoundation\Request
     * @return Json
     */
    public function index(KernelInterface $kernel, Request $request)
    {
        $args = $request->query->all();
        $ingProvider = new IngredientProvider($kernel);
        $resJson = $ingProvider->readJson()
            ->freshFilter($args)
            ->getFormatedJsonResult();

        $recipeProvider = new RecipeProvider($kernel);
        $resJson = $recipeProvider->readJson()
            ->byIngredients($resJson)
            ->getJsonResult();

        $code = 200;
        $response = [
            'success' => true,
            'code' => $code,
            'data' => $resJson,
            'total' => sizeof($resJson)
        ];
        return $this->respondJson($response);
    }
}
