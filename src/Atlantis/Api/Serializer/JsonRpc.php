<?php namespace Atlantis\Api\Serializer;

use Dingo\Api\Http\ResponseFormat\ResponseFormat as BaseResponseFormat;


class JsonRpc extends BaseResponseFormat {
    const SUCCESS_PROPERTY = 'result';
    const ERROR_PROPERTY = 'error';

    public $id;
    public $jsonrpc = '2.0';


    /**
     * Format an Eloquent model.
     *
     * @param  \Illuminate\Database\Eloquent\Model  $model
     * @return string
     */
    public function formatEloquentModel($model)
    {
        return $this->encode($model->toArray());
    }

    /**
     * Format an Eloquent collection.
     *
     * @param  \Illuminate\Database\Eloquent\Collection  $collection
     * @return string
     */
    public function formatEloquentCollection($collection)
    {
        if ($collection->isEmpty()) {
            return $this->encode([]);
        }

        $key = str_plural($collection->first()->getTable());

        return $this->encode($collection->toArray());
    }


    /**
     * Format other response type such as a string or integer.
     *
     * @param  string  $content
     * @return string
     */
    public function formatOther($content)
    {
        return $content;
    }


    /**
     * Format an array or instance implementing ArrayableInterface.
     *
     * @param  array|\Illuminate\Support\Contracts\ArrayableInterface  $content
     * @return string
     */
    public function formatArray($content)
    {
        $content = $this->morphToArray($content);

        array_walk_recursive($content, function (&$value) {
            $value = $this->morphToArray($value);
        });

        return $this->encode($content);
    }


    /**
     * Get the response content type.
     *
     * @return string
     */
    public function getContentType()
    {
        return 'application/json';
    }

}