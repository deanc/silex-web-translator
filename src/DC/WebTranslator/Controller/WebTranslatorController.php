<?php

namespace DC\WebTranslator\Controller;


use DC\WebTranslator\Utility\TranslationHelper;
use Silex\Application;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Translation\Dumper\YamlFileDumper;
use Symfony\Component\Translation\Loader\ArrayLoader;
use Symfony\Component\Translation\MessageCatalogue;
use Symfony\Component\Yaml\Yaml;
use Symfony\Component\Validator\Constraints as Assert;

class WebTranslatorController {

    public function __construct() {}

    public function indexAction(Application $app, Request $request)
    {
        $translator = $app['translator'];

        $locale = $app['locale'];

        $locales = array_unique(array_merge(array($translator->getLocale()), $translator->getFallbackLocales()));

        $totalTranslations = TranslationHelper::getRealCatalogueSize($app['locale'], $app['translator']);
        $totalUntranslated = TranslationHelper::getTotalUntranslated($app['locale'], $app['translator']);

        return $app['twig']->render('@webtranslator/index.twig', array(
            'locale' => $locale
            ,'locales' => $locales
            ,'totalUntranslated' => $totalUntranslated
            ,'totalTranslations' => $totalTranslations
        ));
    }

    public function localesListAction(Application $app, Request $request) {
        return $app['twig']->render('@webtranslator/locales/list.twig', array(

        ));
    }

    public function translationsListAction(Application $app, Request $request, $targetLocale) {


        $locale = $app['locale'];

        if(!empty($targetLocale)) {
            $locale = $targetLocale;
        }


        $primaryCatalogue = $app['translator']->getCatalogue($app['locale'])->all();
        $translatedCatalogue = $app['translator']->getCatalogue($locale)->all();

        if($request->getMethod() == 'POST') {

            $newTranslations = $request->get('translations')[$locale];
            foreach($newTranslations AS $domain => $translations) {
                $domainTranslations = self::unflattenTranslationArray($translations);
                $str = Yaml::dump($domainTranslations, 10, 4, false, false);
                file_put_contents($app['webtranslator.options']['translator_file_path'] . $domain .'.' . $locale . '.yml', $str);
            }

            return $app->redirect($app['url_generator']->generate('webtranslator.translations.list', array('targetLocale' => $locale)));
        }

        return $app['twig']->render('@webtranslator/translations/list.twig', array(
            'locale' => $locale
            ,'primaryCatalogue' => $primaryCatalogue
            ,'translatedCatalogue' => $translatedCatalogue
            ,'locales' => array_unique(array_merge(array($app['translator']->getLocale()), $app['translator']->getFallbackLocales()))
            ,'missingCount' => TranslationHelper::compareTotalTranslations($app['translator'], $app['locale'], $locale)
        ));
    }

    public function importerAction(Application $app, Request $request) {

        $form = $app['form.factory']->createBuilder('form')
            ->add('parsers', 'choice', array(
                'required' => false
                ,'expanded' => true
                ,'multiple' => true
                ,'label' => 'Import translations from:'
                ,'constraints' => array(
                    new Assert\NotBlank()
                )
                ,'choices' => array(
                    'php' => 'PHP files'
                    ,'twig' => 'Twig templates'
                )
                ,'data' => array('php', 'twig')
            ))
            ->getForm();

        return $app['twig']->render('@webtranslator/importer.twig', array(
            'form' => $form->createView()
        ));
    }

    public static function unflattenTranslationArray($translationArray) {
        $res = [];
        foreach( $translationArray as $key => $value ) {
            $key = explode('.', $key);
            $res = array_merge_recursive($res, self::flatten_yaml_key_value($key, $value));
        }
        return $res;
    }

    public static function flatten_yaml_key_value($arr, $inner='')
    {
        $key = array_pop($arr);
        if (empty($arr)) {
            return [$key => $inner];
        } else {
            return self::flatten_yaml_key_value($arr, [$key => $inner]);
        }
    }

}