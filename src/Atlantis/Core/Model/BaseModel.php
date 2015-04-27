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

        /** Parent original value */
        if( parent::getAttribute($key) ){
            $value_original = parent::getAttribute($key);
        }

        /** Context Reaction, AttributeSet */
        if( class_exists('\\Atlantis\\Context\\ContextServiceProvider') ){
            $context_value = $this->getContextReaction($key,$value_original);
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
    protected function getContextReaction($key,$value){
        /** Get context */
        $contexts = app('context')->all();

        /** Check if Reaction Contexts exist for this model */
        $context = Arrays::find($contexts, function($value){
            if( $value->status == 0 ) return false;

            return $value->reaction_parameters->model == get_called_class();
        });


        /** Inspect reaction context for this model */
        if($context){
            /** Inspect context */
            $value = app('context')->reactionInspect($context->reaction_provider,[$this,$key,$value,$context->reaction_parameters]);

            /** Override value */
            if($value) return $value;
        }

        return null;
    }


    /**
     * Scope Filtering
     *
     * @param $query
     * @param array $columns
     */
    public function scopeFiltering($query,$columns=array()){
        foreach($columns as $column => $value){
            $relations =  explode('.',$column);
            $field = array_pop($relations);
            $relation = head($relations);

            if( count($relations) > 0 ){
                $query->whereHas($relation, function($q) use($relations,$field,$value){
                    $relation = array_pop($relations);
                    if( count($relations) == 0 ){
                        $q->where($field,'LIKE',$value.'%');

                    }else{
                        /*array_reduce($array_column, function(&$collector,$item) use($field_last, $field, $value){
                            $collector->whereHas($item, function($query) use(&$collector, $item, $field_last, $field, $value){
                                if($item == $field_last){
                                    $query->where($field,'LIKE',$value.'%');
                                }
                                $collector = $query;
                            });

                            return $collector;
                        },$query);*/

                        $q->whereHas($relation, function($q) use($relations,$field,$value){
                            $relation = array_pop($relations);
                            if( count($relations) == 0 ){
                                $q->where($field,'LIKE',$value.'%');

                            }else{
                                $q->whereHas($relation, function($q) use($relations,$field,$value){
                                    $relation = array_pop($relations);
                                    if( count($relations) == 0 ){
                                        $q->where($field,'LIKE',$value.'%');
                                    }
                                });
                            }
                        });
                    }
                });

            }else{
                /** Filtering normal columns */
                $query->where($field,'LIKE',$value.'%');
            }
        }
    }


    /**
     * Meta Attribute
     *
     * @param $value
     * @return mixed
     */
    public function getMetaAttribute($value){
        if( empty($value) ) $value = '{}';
        return json_decode($value);
    }


    /**
     * Meta Attribute
     * @param $value
     */
    public function setMetaAttribute($value){
        if( is_array($value) || is_object($value) ){
            $this->attributes['meta'] = json_encode($value);
        }else{
            $this->attributes['meta'] = $value;
        }
    }

}