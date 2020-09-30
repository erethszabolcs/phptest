<?php

use Models\Products;

$router = $di->getRouter();

/**
 * Adding routes with correct method
 */
$productListRoute = $router->addGet(
    '/products',
    [
        'controller' => 'api',
        'action'     => 'productList',
    ]
);

$productDetailRoute = $router->addGet(
    '/product/{productID}',
    [
        'controller' => 'api',
        'action'     => 'productDetail',
    ]
);

$productUpdateRoute = $router->addPut(
    '/product/{productID}',
    [
        'controller' => 'api',
        'action'     => 'productUpdate',
    ]
);

$rateRoute = $router->addPost(
    '/rate/{productID}',
    [
        'controller' => 'api',
        'action'     => 'rate',
    ]
);

$router->handle($_SERVER['REQUEST_URI']);
