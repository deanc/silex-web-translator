<?php

namespace DC\WebTranslator\Controller;


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

        $messages = $translator->getMessages($locale)['messages'];

        $translationCount = sizeof($messages);
        $untranslated = 0;
        foreach($locales AS $l) {
            $lm = $translator->getMessages($l)['messages'];
            foreach($messages AS $mk => $mv) {
                if(!array_key_exists($mk, $lm)) {
                    $untranslated++;
                }
            }
        }

        return $app['twig']->render('@webtranslator/index.twig', array(
            'locale' => $locale
            ,'locales' => $locales
            ,'messages' => $messages
            ,'untranslated' => $untranslated
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


        $messages = $app['translator']->getMessages($app['locale'])['messages'];
        $translations = $app['translator']->getMessages($locale)['messages'];

        if($request->getMethod() == 'POST') {

            $newTranslations = $request->get('translations')[$locale];
            $unflattenedTranslations = self::unflattenTranslationArray($newTranslations);

            // split out
            $str = Yaml::dump($unflattenedTranslations, 10, 4, false, false);
            file_put_contents($app['webtranslator.options']['translator_file_path'] . $locale . '.yml', $str);
            return $app->redirect($app['url_generator']->generate('webtranslator.translations.list'));
        }

        return $app['twig']->render('@webtranslator/translations/list.twig', array(
            'locale' => $locale
            ,'messages' => $messages
            ,'translations' => $translations
            ,'locales' => array_unique(array_merge(array($app['translator']->getLocale()), $app['translator']->getFallbackLocales()))
            ,'missingCount' => sizeof($messages) - sizeof($translations)
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