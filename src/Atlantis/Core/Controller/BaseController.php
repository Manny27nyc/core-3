<?php namespace Atlantis\Core\Controller;

use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Route;
use Illuminate\Routing\Controller;


class BaseController extends Controller {
    /**-----------------------------------------------------------------------------------------------------------------
     * Traits
    -----------------------------------------------------------------------------------------------------------------*/
    use \Atlantis\Core\Theme\Traits\Controller;

    /**-----------------------------------------------------------------------------------------------------------------
     * Global Overridable Attributes
     -----------------------------------------------------------------------------------------------------------------*/
    protected $theme;
    protected $layout = 'core::layouts.common';

    /**-----------------------------------------------------------------------------------------------------------------
     * Global Attributes
    -----------------------------------------------------------------------------------------------------------------*/
    private $environment;
    private $route_name;


    public function __construct(){
        #i: Get controller provider
        $this->environment = App::make('atlantis.controller');

        #i: Boot a theme
        $this->themeBoot($this->theme);
    }


	/*******************************************************************************************************************
	 * Setup the layout used by the controller.
	 *
	 * @return void
     ******************************************************************************************************************/
	protected function setupLayout()
	{
		#i: Default layout
        if ( ! is_null($this->layout))
		{
			$this->layout = View::make($this->layout);
            $this->layout->page = false;
		}

        #i: Current route name
        $this->route_name = Route::currentRouteName();
	}


    /*******************************************************************************************************************
     *
     *
     * @return mixed
     ******************************************************************************************************************/
    public function __call($name, $arguments){
        if( $this->environment->extension($name) ){
            return call_user_func_array($name, $arguments);
        }

        return null;
    }


    /*******************************************************************************************************************
     *
     *
     * @return mixed
     ******************************************************************************************************************/
    public function __get($name){
        if( $this->environment->attribute($name) ){
            return $this->environment->attribute($name);
        }

        return null;
    }

}