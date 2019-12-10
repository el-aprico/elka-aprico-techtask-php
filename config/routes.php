<?php

// config/routes.php
use \App\Controller\IngredientController;
use Symfony\Component\Routing\RouteCollection;
use Symfony\Component\Routing\Route;

$routes = new RouteCollection();

$routes->add('lunch', new Route('/api/v1/lunch', [
    '_controller' => [ \App\Controller\LunchController::class, 'index']
]));

return $routes;
