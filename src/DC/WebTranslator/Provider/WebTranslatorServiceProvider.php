<?php

namespace DC\WebTranslator\Provider;

use Silex\Application;
use Silex\ServiceProviderInterface;

class WebTranslatorServiceProvider implements ServiceProviderInterface
{

    public function register(Application $app)
    {
        $app['webtranslator.controller'] = $app->share(function ($app) {
            $controller = new \DC\WebTranslator\Controller\WebTranslatorController();
            return $controller;
        });

    }


    public function boot(Application $app)
    {
        // Add twig template path.
        if (isset($app['twig.loader.filesystem'])) {
            $app['twig.loader.filesystem']->addPath(__DIR__ . '/../views/', 'webtranslator');
        }

    }
}