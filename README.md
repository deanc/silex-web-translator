## Requirements

* Make sure your YML files are of the format `<locale>.yml` and just one YML file per locale. e.g. `en_GB.yml`

## Installation

* Configuration the default Silex TranslationServiceProvider in a similar way:

        $app['locale'] = 'en_GB';
        $app->register(new Silex\Provider\TranslationServiceProvider(), array(
            'locale_fallbacks' => array('en_GB', 'fr_FR', 'fi_FI'),
        ));
        
        $app['translator'] = $app->share($app->extend('translator', function($translator, $app) {
        
            $translator->addLoader('yaml', new Symfony\Component\Translation\Loader\YamlFileLoader());
        
            $translator->addResource('yaml', __DIR__.'/../translations/en_GB.yml', 'en_GB');
            $translator->addResource('yaml', __DIR__.'/../translations/fr_FR.yml', 'fr_FR');
            $translator->addResource('yaml', __DIR__.'/../translations/fi_FI.yml', 'fi_FI');
        
            return $translator;
        }));

* Make sure you are using and have registered the following service providers:

        $app->register(new Silex\Provider\ServiceControllerServiceProvider());
        $app->register(new Silex\Provider\UrlGeneratorServiceProvider());
        $app->register(new Silex\Provider\FormServiceProvider());
        $app->register(new Silex\Provider\ValidatorServiceProvider());
        $app->register(new Silex\Provider\TwigServiceProvider());
        
* Register the service provider and configure the options

        $app['webtranslator.options'] = array(
            'translator_file_path' => __DIR__ . '/../translations/'
        );
        $app->register(new DC\WebTranslator\Provider\WebTranslatorServiceProvider());
        
* Mount the controller provider

        $app->mount('/translations', new DC\WebTranslator\Controller\WebTranslatorControllerProvider());

## FAQ

How to protect the interface with user authentication?

- You can protect the route you mount the controller on, as you would any other route, using the Symfony 
security component.

## Known issues

* If you import nested YML files with pretty new line (pipe) syntax, it will replace new lines with \r\n
* Does not support multiple translation files per locale

## Thanks/Credits

* Big thanks to StartBootstrap for a few styles for the admin: https://github.com/IronSummitMedia/startbootstrap-sb-admin