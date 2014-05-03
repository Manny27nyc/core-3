<?php namespace Atlantis\Core\View;

use Illuminate\Events\Dispatcher;
use Atlantis\Core\View\Interfaces\Binder as BinderInterface;


class Binder implements BinderInterface {

    /**
     * @var Dispatcher
     */
    private $event;

    /**
     * @var string
     */
    private $viewToBindVariables;

    /**
     * @param Dispatcher $event
     * @param $viewToBindVariables
     */
    function __construct(Dispatcher $event, $viewToBindVariables)
    {
        $this->event = $event;
        $this->viewToBindVariables = $viewToBindVariables;
    }

    /**
     * Bind the given JavaScript to the
     * view using Laravel event listeners
     *
     * @param $js The ready-to-go JS
     * @param $header Use header to pass data
     */
    public function javascript($js,$header=false)
    {
        if( $header ){
            \App::after(function($request, $response) use ($js){
                $response->headers->set('_javascript',$js);
            });

        }else{
            $this->event->listen("composing: {$this->viewToBindVariables}", function($view) use ($js)
            {
                $view->getEnvironment()->inject('_javascript',"<script>{$js}</script>");
            });
        }
    }

}