<?php
namespace NwSilex\Provider;

use Silex\Application;
use Silex\ServiceProviderInterface;

class ConfigServiceProvider implements ServiceProviderInterface
{
    public function register(Application $app)
    {
        $app['app.config'] = $app->share(function ($app) {
            // Config
            $config = array();
            foreach (glob(APP_PATH."/config/*.php") as $path) {
                if(file_exists($path) and is_file($path)) {
                    $filename = pathinfo($path, PATHINFO_FILENAME);
                    $config[$filename] = isset($config[$filename]) ? $config[$filename] : array();
                    $config[$filename] = array_replace($config[$filename], (array) include $path);
                }
            }

            foreach (glob(APP_PATH."/config/".$app['app.env']."/*.php") as $path) {
                if(file_exists($path) and is_file($path)) {
                    $filename = pathinfo($path, PATHINFO_FILENAME);
                    $config[$filename] = isset($config[$filename]) ? $config[$filename] : array();
                    $config[$filename] = array_replace($config[$filename], (array) include $path);
                }
            }

            return $config;
        });
    }
    
    public function boot(Application $app)
    {
    }
}
