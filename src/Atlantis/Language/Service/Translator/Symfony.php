<?php

namespace Atlantis\Language\Service\Translator;

use Atlantis\Language\Factory\Translator as TranslatorFactory;
use Symfony\Component\Translation\Loader\ArrayLoader;
use Symfony\Component\Translation\Translator;
use Symfony\Component\Translation\MessageSelector;


class Symfony implements TranslatorFactory{

    protected $translator;


    public function __construct($locale, $catalogs){
        $this->translator = new Translator($locale, new MessageSelector());
        $this->translator->addLoader('array', new ArrayLoader());
        $this->translator->addResource('array', $catalogs, $locale);

    }


    public function translate($subject,$params=[]){
        return $this->translator->trans($subject,$params);
    }

}