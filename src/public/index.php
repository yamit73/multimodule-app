<?php

error_reporting(E_ALL);

use Phalcon\Loader;
use Phalcon\Mvc\Router;
use Phalcon\DI\FactoryDefault;
use Phalcon\Mvc\Application as BaseApplication;

class Application extends BaseApplication
{
    /**
     * Register the services here to make them general or register in the ModuleDefinition to make them module-specific
     */
    protected function registerServices()
    {

        $di = new FactoryDefault();

        $loader = new Loader();

        /**
         * We're a registering a set of directories taken from the configuration file
         */
        $loader
            ->registerDirs([__DIR__ . '/../app/library/'])
            ->register();

        // Registering a router
        $di->set('router', function () {

            $router = new Router();

            $router->setDefaultModule("frontend");

            $router->add('/:controller/:action', [
                'module'     => 'frontend',
                'controller' => 2,
                'action'     => 2,
            ])->setName('frontend');

            $router->add("/login", [
                'module'     => 'backend',
                'controller' => 'login',
                'action'     => 'index',
            ])->setName('backend-login');

            $router->add("/admin/product/:action", [
                'module'     => 'backend',
                'controller' => 'product',
                'action'     => 1,
            ])->setName('backend-product');

            $router->add("/product/:action", [
                'module'     => 'frontend',
                'controller' => 'product',
                'action'     => 'index',
            ])->setName('frontend-product');

            return $router;
        });

        $this->setDI($di);
    }

    public function main()
    {

        $this->registerServices();

        // Register the installed modules
        $this->registerModules([
            'frontend' => [
                'className' => 'Multiple\Frontend\Module',
                'path'      => '../app/frontend/Module.php'
            ],
            'backend'  => [
                'className' => 'Multiple\Backend\Module',
                'path'      => '../app/backend/Module.php'
            ]
        ]);

        $response = $this->handle($_SERVER["REQUEST_URI"]);

        $response->send();
    }
}

$application = new Application();
$application->main();
