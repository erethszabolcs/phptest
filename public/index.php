<?php
declare(strict_types=1);

use Phalcon\Di\FactoryDefault;
use Phalcon\Events\Event;
use Phalcon\Events\Manager;
use Models\Users;
use Phalcon\Http\Response;

error_reporting(E_ALL);

define('BASE_PATH', dirname(__DIR__));
define('APP_PATH', BASE_PATH . '/app');

try {
    /**
     * The FactoryDefault Dependency Injector automatically registers
     * the services that provide a full stack framework.
     */
    $di = new FactoryDefault();

    /**
     * Read services
     */
    include APP_PATH . '/config/services.php';

    /**
     * Handle routes
     */
    include APP_PATH . '/config/router.php';

    /**
     * Get config service for use in inline setup below
     */
    $config = $di->getConfig();

    /**
     * Include Autoloader
     */
    include APP_PATH . '/config/loader.php';

    /**
     * Bind Products model to certain routes according to productID
     */
    foreach([$productDetailRoute, $productUpdateRoute, $rateRoute] as $route) {
        $route->convert(
            'productID',
            function ($productID) {
                return Models\Products::findFirstById($productID);
            }
        );
    }

    /**
     * Turn off built-in notNullValidation to use our custom validation on certain models
     */
    \Phalcon\Mvc\Model::setup(array(    
        'notNullValidations' => false
    ));

    /**
     * Handle the request
     */
    $application = new \Phalcon\Mvc\Application($di);

    $manager = new Manager();

    $application->setEventsManager($manager);

    /**
     * Implementing pseudo-authentication
     */
    $manager->attach(
        'application',
        function (Event $event, $application) {

            // Looking for a User with the same id as in the request header parameter 'userID'
            $user = Users::findFirst((int) $application->request->getHeader('userID'));

            // If there's none we send response with 404 status code as 'User not found'
            if(!$user) {
                $response = new Response();
                $response->setStatusCode(404, 'User Not Found');
                $response->setJsonContent(['message' => 'User not found', 'data' => null]);
                $response->send();
                die();
            }
        }
    );

    echo $application->handle($_SERVER['REQUEST_URI'])->getContent();
} catch (\Exception $e) {

    // We send response with 500 status code if there was an exception
    $response = new Response();
    $response->setStatusCode(500);
    $response->setJsonContent(['message' => 'Something went wrong', 'data' => null]);
    $response->send();
    die();

    /* echo $e->getMessage() . '<br>';
    echo '<pre>' . $e->getTraceAsString() . '</pre>';
    */
}
