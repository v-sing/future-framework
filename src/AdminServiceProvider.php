<?php

namespace Future\Admin;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Blade;
use Future\Admin\Form\FormBuilder;
use Future\Admin\Form\HtmlBuilder;
use Illuminate\Support\Facades\Input;

class AdminServiceProvider extends ServiceProvider
{
    protected $namespace = 'Future\Admin\Controllers';
    /**
     * @var array
     */
    protected $commands = [
        Console\AdminCommand::class,
        Console\MakeCommand::class,
        Console\MenuCommand::class,
        Console\InstallCommand::class,
        Console\PublishCommand::class,
        Console\UninstallCommand::class,
        Console\ImportCommand::class,
        Console\CreateUserCommand::class,
        Console\ResetPasswordCommand::class,
        Console\ExtendCommand::class,
        Console\ExportSeedCommand::class,
        Console\DatabaseTableCommand::class
    ];

    protected $routeMiddleware = [
        'admin.init'            => Middleware\Initialization::class,
        'admin.session'         => Middleware\Session::class,
        'admin.auth'            => Middleware\AuthMiddleware::class,
        'admin.adminController' => Middleware\AdminControllerMiddleware::class,
    ];

    protected $middlewareGroups = [
        'admin' => [
            'admin.init',
            'admin.session',
            \Illuminate\Session\Middleware\StartSession::class,
            \Illuminate\View\Middleware\ShareErrorsFromSession::class,
            \App\Http\Middleware\VerifyCsrfToken::class,

        ]
    ];

    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {

        $this->loadAdminAuthConfig();
        $this->registerRouteMiddleware();
        $this->commands($this->commands);
    }

    /**
     * @throws \ReflectionException
     */
    public function boot()
    {
        if (config('admin.https') || config('admin.secure')) {
            \URL::forceScheme('https');
            $this->app['request']->server->set('HTTPS', true);
        }
        if ($this->app->runningInConsole()) {
            $this->publishes([__DIR__ . '/../config' => config_path()], 'future-admin-config');
            $this->publishes([__DIR__ . '/../database/migrations' => database_path('migrations')], 'future-admin-migrations');
            $this->publishes([__DIR__ . '/../database/data' => database_path('data')], 'future-admin-data');
            $this->publishes([__DIR__ . '/../resources/assets' => public_path('assets')], 'future-admin-assets');
        } else {
            $this->loadTranslationsFrom(__DIR__ . '/../resources/lang', 'admin_vendor');
            $this->loadTranslationsFrom(app_path(admin_base_path('Resources/lang')), 'admin');
            $this->loadViewsFrom(__DIR__ . '/../resources/views', 'admin');

            $this->loadViewsFrom(app_path(admin_base_path('Resources/views')), 'admin');
            if (file_exists($routes = admin_path('routes.php'))) {
                $this->registerAuthRoutes();
                $this->loadRoutesFrom($routes);
            }
        }
        //remove default feature of double encoding enable in laravel 5.6 or later.
        $bladeReflectionClass = new \ReflectionClass('\Illuminate\View\Compilers\BladeCompiler');
        if ($bladeReflectionClass->hasMethod('withoutDoubleEncoding')) {
            Blade::withoutDoubleEncoding();
        }

    }

    /**
     * Setup auth configuration.
     *
     * @return void
     */
    protected function loadAdminAuthConfig()
    {
        config(array_dot(config('admin.auth', []), 'auth.'));
    }

    /**
     * Register the route middleware.
     *
     * @return void
     */
    protected function registerRouteMiddleware()
    {
        // register route middleware.
        foreach ($this->routeMiddleware as $key => $middleware) {
            app('router')->aliasMiddleware($key, $middleware);
        }

        // register middleware group.
        foreach ($this->middlewareGroups as $key => $middleware) {
            app('router')->middlewareGroup($key, $middleware);
        }
    }

    /**
     * Register the auth routes.
     *
     * @return void
     */
    public function registerAuthRoutes()
    {
        $attributes = [
            'prefix'     => config('admin.route.prefix'),
            'middleware' => config('admin.route.middleware'),
        ];
        app('router')->group($attributes, function ($router) {
            /* @var \Illuminate\Routing\Router $router */
            $router->namespace($this->namespace)->group(function ($router) {
                $array = $this->loadRoutesFile(__DIR__ . '/../routes/admin');
                foreach ($array as $routes) {
                    require_once $routes;
                }
            });
            $router->namespace("Future\Admin\Test")->group(function ($router) {
                $router->get('form', 'TestController@form');
            });
            $router->namespace(config('admin.route.namespace'))->group(function ($router) {
                require_once app_path(admin_base_path('/routes.php'));
                $array = $this->loadRoutesFile(app_path(admin_base_path('/routes/')));
                foreach ($array as $routes) {
                    require_once $routes;
                }
            });
        });
    }

    /**
     * 递归文件
     * @param $path
     * @return array
     */
    protected function loadRoutesFile($path)
    {
        $allRoutesFilePath = array();
        foreach (glob($path) as $file) {
            if (is_dir($file)) {
                $allRoutesFilePath = array_merge($allRoutesFilePath, $this->loadRoutesFile($file . '/*'));
            } else {
                $allRoutesFilePath[] = $file;
            }
        }
        return $allRoutesFilePath;
    }

    /**
     * Register the HTML builder instance.
     *
     * @return void
     */
    protected function registerHtmlBuilder()
    {
        $this->app->singleton('html', function ($app) {
            return new HtmlBuilder($app['url']);
        });
    }

    /**
     * Register the form builder instance.
     *
     * @return void
     */
    protected function registerFormBuilder()
    {
        $this->app->singleton('form', function ($app) {
            $form = new FormBuilder($app['html'], $app['url'], $app['session.store']->token());

            return $form->setSessionStore($app['session.store']);
        });
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return array('html', 'form');
    }

}
