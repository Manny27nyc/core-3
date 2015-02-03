<?php

namespace Atlantis\Core\Model;

use Illuminate\Database\Eloquent\Model as Eloquent;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Config;
use Underscore\Types\Arrays;


class BaseModel extends Eloquent {

    /**
     * getAttribute override
     *
     * @param string $key
     * @return mixed|null
     */
    public function getAttribute($key){
        $value_original = null;

        #i: Parent original value
        if( parent::getAttribute($key) ){
            $value_original = parent::getAttribute($key);
        }

        #i: Context Reaction
        if( class_exists('\\Atlantis\\Context\\ContextServiceProvider') ){
            $context_value = $this->getContextReaction($key);
            if( $context_value ) $value_original = $context_value;
        }

        return $value_original;
    }


    /**
     * Check for Context Reaction on Model
     *
     * @param $key
     * @return null
     */
    protected function getContextReaction($key){
        #i: Get context
        $contexts = app('context');

        #i: Check if Reaction Contexts exist for this model
        $context = Arrays::find($contexts->all(), function($value){
            return $value->reaction_parameters->model == get_called_class();
        });

        #i: Inspect reaction context for this model
        if($context){
            #i: Inspect context
            $value = $contexts->reactionInspect($context->reaction_provider,[$this,$key,$context->reaction_parameters]);

            #i: Override value
            if($value) return $value;
        }

        return null;
    }

}