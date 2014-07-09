<?php namespace Atlantis\Core\Theme;

use Illuminate\Support\Facades\App;


class Environment {
    protected $config;
    protected $view;
    protected $files;
    protected $assets = [];
    protected $themes_base_path;

    protected $theme_prefixes = [];
    protected $theme_attributes = [];
    protected $theme_stylesheets = [];
    protected $theme_javascripts = [];

    protected $app_locale;

    const PREFIX_PATTERN = '/\w+[!]/';

    protected $theme_rules = array(
        'info'  => 'required|array',
        'register' => 'array',
        'assets' => 'array'
    );


    /**
     * Class Constructor
     * @param $config
     * @param $view
     * @param $files
     * @param $assets
     */
    public function __construct($config,$view,$files,$assets){
        $this->config = $config;
        $this->view = $view;
        $this->files = $files;
        $this->assets = $assets;

        $this->themes_base_path = $this->config->get('core::app.theme.base_path');
        $this->locale = $config->get('app.locale');
    }


    /**
     * Load theme
     * @param String $theme
     * @return bool
     */
    public function load($theme=null){
        #i: Get default theme if not set
        if(empty($theme)) {
            $theme = $this->config->get('core::app.theme.default');
        }

        #i: Construct theme config file path
        $theme_config_path = "{$this->themes_base_path}/$theme/config.php";

        #i: Check theme with config file exist, if not load default
        if( !$this->files->exists($theme_config_path) ) {
            return $this->load($this->config->get('core::app.theme.default'));
        }

        #i: Add theme to config path, for later flexible access
        $this->config->addNamespace("themes/$theme","{$this->themes_base_path}/$theme/");

        #i: Add theme view to global namespace
        $this->view->addNamespace("themes/$theme","{$this->themes_base_path}/$theme/views/");

        #i: Fetch theme configs
        $configs = $this->config->get("themes/$theme::config");

        #i: Validate theme config structure
        // Later !!

        #i: Template inheritance
        if( isset($configs['info']['inherit']) ){
            $this->load($configs['info']['inherit']);
        }

        #i: Load prefixes
        if( isset($configs['register']['prefixes']) ){
            $this->theme_prefixes[$theme] = $configs['register']['prefixes'];
            $configs['assets'] = $this->applyPrefixes($configs['assets'],$theme);
        }

        #i: Load attributes
        if( isset($configs['register']['attributes']) ) $this->theme_attributes[$theme] = $configs['register']['attributes'];

        #i: Registering current theme assets
        $this->assets->extend($theme,$configs['assets']);

        return true;
    }


    /**
     * Apply theme prefixes
     *
     * @param           $collection
     * @param string    $theme
     * @return mixed
     */
    public function applyPrefixes($collection,$theme=null){
        #i: Get default theme name if not supplied
        if(!isset($theme)) $theme =  $this->config->get('core::app.theme.default');

        foreach($collection as &$item){
            if( is_array($item) ){
                $item = $this->applyPrefixes($item);
                continue;
            }

            #i: Apply prefix
            $item = $this->applyPrefix($item,$theme);
        }

        return $collection;
    }


    /**
     * Detect and apply prefix to string value
     * @param $value
     * @param null $theme
     * @return mixed
     */
    public function applyPrefix($value,$theme=null){
        if(empty($theme)) $theme =  $this->config->get('core::app.theme.default');

        #i: Get registered prefixes for theme
        $prefixes = $this->theme_prefixes[$theme];

        #i: Find all prefix in value string
        preg_match_all(self::PREFIX_PATTERN,$value,$matches);

        #i: Replace all matched with prefix value
        array_walk($matches[0], function($item) use($prefixes,&$value){
            if( isset($prefixes[rtrim($item,'!')]) ) $value = str_replace($item,$prefixes[rtrim($item,'!')],$value);
        });

        return $value;
    }


    public function base_path(){
        return $this->themes_base_path;
    }
}