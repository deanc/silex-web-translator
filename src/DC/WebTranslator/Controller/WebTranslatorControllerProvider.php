<?php

namespace DC\WebTranslator\Controller;

use Silex\Application;
use Silex\ControllerProviderInterface;
use Silex\ServiceControllerResolver;

class WebTranslatorControllerProvider implements ControllerProviderInterface
{

    public function connect(Application $app)
    {
        if (!$app['resolver'] instanceof ServiceControllerResolver) {
            // using RuntimeException crashes PHP?!
            throw new \LogicException('You must enable the ServiceController service provider to be able to use these routes.');
        }

        /** @var ControllerCollection $controllers */
        $controllers = $app['controllers_factory'];

        $controllers->match('/importer', 'webtranslator.controller:importerAction')
            ->method('GET|POST')
            ->bind('webtranslator.importer');

        $controllers->get('/locales', 'webtranslator.controller:localesListAction')
            ->bind('webtranslator.locales.list');

        $controllers->match('/list/{targetLocale}', 'webtranslator.controller:translationsListAction')
            ->method('GET|POST')
            ->value('targetLocale', '')
            ->bind('webtranslator.translations.list');

        $controllers->get('/', 'webtranslator.controller:indexAction')
            ->bind('webtranslator.index');
        return $controllers;
    }


}