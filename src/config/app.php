<?php return [


    /*
    |--------------------------------------------------------------------------
    | Atlantis Config
    |--------------------------------------------------------------------------
    |
    |
    */
    'config' => array(
        'enable'        => true,
        'setting_path'  => public_path() . '/settings'
    ),

    /*
    |--------------------------------------------------------------------------
    | Atlantis Components
    |--------------------------------------------------------------------------
    |
    |
    */
    'component' => array(
        'base_path'     => public_path() . '/components'
    ),

    /*
    |--------------------------------------------------------------------------
    | Atlantis Components
    |--------------------------------------------------------------------------
    |
    |
    */
    'theme' => array(
        'default'       => 'default',
        'base_path'     => base_path() . '/themes'
    ),

    'copyright' => '<a href="http://atlantis.nematix.com">Nematix Corporation</a>',

];