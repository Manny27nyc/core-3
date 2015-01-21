<?php return [

    /*
    |--------------------------------------------------------------------------
    | Default repository module to use
    |--------------------------------------------------------------------------
    | If all, all repository will be use in cascade starting from Eloquent to
    | this config file. Eloquent > Config
    |
    */
    'default' => 'config',

    /*
    |--------------------------------------------------------------------------
    | Module base path
    |--------------------------------------------------------------------------
    |
    |
    */
    'base' => base_path() . '/modules/',

    /*
    |--------------------------------------------------------------------------
    | Module repositories
    |--------------------------------------------------------------------------
    |
    |
    */
    'repositories' => [

        'config' => [
            'group' => 'core',
            'namespace' => 'module.modules'
        ],

        'eloquent' => [
            'model' => 'module'
        ]

    ],

    /*
    |--------------------------------------------------------------------------
    | Initial & default module to load
    |--------------------------------------------------------------------------
    |
    |
    */
    'modules' => [
        /*'advance' => [
            'enable' => true,
            'provider' => 'AdvanceServiceProvider'
        ]*/
    ]

];
