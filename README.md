# Silex Web Translator

## Introduction

This Silex Provider allows you to easily manage the translation files for your app via a web-based interface. Current features include:

* Dashboard for a quick overview of the current status of translations for your app including total translations, total locales and total untranslated strings.

![dashboard](https://github.com/deanc/silex-web-translator/raw/master/screenshots/dashboard.png)

* A simple editor for translation strings, allowing you to quickly see which translation strings are missing for each locale

![translation screen 1](https://github.com/deanc/silex-web-translator/raw/master/screenshots/translations_primary.png)
![translation screen 2](https://github.com/deanc/silex-web-translator/raw/master/screenshots/translations_secondary.png)

## Requirements

* PHP 5.4+
* Using YAML files for your translations. They *must* be formatted as `<domain>.<locale>.yml`. If you aren't using the domains featured of the Symfony `Translation` component then make sure you name your translation files `messages.<locale>.yml`.
* For all usage of locales in your app use the ISO 639-1 language code, an underscore (_), then the ISO 3166-1 alpha-2 country code (e.g. fr_FR for French/France).
* Make sure you configure *all* locales as fallbacks when setting up the `TranslationProvider`.

## Installation

* Configuration the default Silex TranslationServiceProvider in a similar way:

```php
$app['locale'] = 'en_GB';
$app->register(new Silex\Provider\TranslationServiceProvider(), array(
    'locale_fallbacks' => array('en_GB', 'fr_FR', 'fi_FI'),
));

$app['translator'] = $app->share($app->extend('translator', function($translator, $app) {

    $translator->addLoader('yaml', new Symfony\Component\Translation\Loader\YamlFileLoader());

    $translator->addResource('yaml', __DIR__.'/../translations/messages.en_GB.yml', 'en_GB');
    $translator->addResource('yaml', __DIR__.'/../translations/messages.fr_FR.yml', 'fr_FR');
    $translator->addResource('yaml', __DIR__.'/../translations/messages.fi_FI.yml', 'fi_FI');
    $translator->addResource('yaml', __DIR__.'/../translations/rules.en_GB.yml', 'en_GB', 'rules');

    return $translator;
}));
```

* Make sure you are using and have registered the following service providers:

```php
$app->register(new Silex\Provider\ServiceControllerServiceProvider());
$app->register(new Silex\Provider\UrlGeneratorServiceProvider());
$app->register(new Silex\Provider\FormServiceProvider());
$app->register(new Silex\Provider\ValidatorServiceProvider());
$app->register(new Silex\Provider\TwigServiceProvider());
```
        
* Register the service provider and configure the options

```php
$app['webtranslator.options'] = array(
    'translator_file_path' => __DIR__ . '/../translations/'
);
$app->register(new DC\WebTranslator\Provider\WebTranslatorServiceProvider());
```
        
* Mount the controller provider

```php
$app->mount('/webtranslator', new DC\WebTranslator\Controller\WebTranslatorControllerProvider());
```

## FAQ

How to protect the interface with user authentication?

* You can protect the route you mount the controller on, as you would any other route, using the Symfony security component.

## Known issues

* If you import nested YML files with pretty new line (pipe) syntax, it will replace new lines with \r\n

## Thanks/Credits

* Big thanks to StartBootstrap for a few styles for the admin: https://github.com/IronSummitMedia/startbootstrap-sb-admin