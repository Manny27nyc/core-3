<?php

namespace Atlantis\Language;


class Environment {

    protected $configs;

    /*
     * $knowledge = interpret | translate
     *
     */

    public function __construct($configs=[]){
        $this->configs = $configs;
    }


    /**
     * Process a language subject
     *
     * @param $subject
     * @param null $knowledge
     * @param array $params
     * @return string
     */
    public function lang($subject, $params=[], $knowledge=null){

        if( $knowledge == 'interpret' ){
            $subject = $this->interpret($subject,$params);

        } elseif( $knowledge == 'translate' ){
            $subject = $this->translate($subject,$params);

        } else {
            $subject = $this->auto($subject,$params);
        }

        return $subject;
    }


    /**
     * Interpret a subject
     *
     * @param $subject
     * @param array $params
     */
    protected function interpret($subject,$params=[]){
        $interpreter = 'Atlantis\\Language\\Service\\Interpreter\\'.studly_case($this->configs['default']['interpreter']);

        if( class_exists($interpreter) ){
            $interpreter = new $interpreter();
            $subject = $interpreter->interpret($subject,$params);
        }

        return $subject;
    }


    /**
     * Translate a subject
     *
     * @param $subject
     * @param array $params
     */
    protected function translate($subject,$params=[]){
        $repos = app('config');
        $translator = 'Atlantis\\Language\\Service\\Translator\\'.studly_case($this->configs['default']['translator']);

        if( class_exists($translator) ){
            $translator = new $translator($repos->get('app.locale'),$this->configs['translator']['Symfony']['catalogs']);
            $subject = $translator->translate($subject,$params);
        }

        return $subject;
    }


    /**
     * Automatically decide what to do
     *
     * @param $subject
     * @param array $params
     * @return string
     */
    protected function auto($subject,$params=[]){
        // Dummy detection mechanism

        $interpreted = $this->interpret($subject,$params);
        if( $interpreted != $subject ){
            return $this->interpret($subject,$params);
        }

        $translated = $this->translate($subject,$params);
        if( $translated != $subject){
            return $translated;
        }

        return $subject;
    }
}