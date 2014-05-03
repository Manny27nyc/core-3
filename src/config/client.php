<?php

return [

    'javascript' => array(
        /*
        |--------------------------------------------------------------------------
        | View to Bind JavaScript Vars To
        |--------------------------------------------------------------------------
        |
        | Set this value to the name of the view (or partial) that
        | you want to prepend the JavaScript variables to.
        |
        */
        'bind' => 'admin::layouts.common',

        /*
        |--------------------------------------------------------------------------
        | JavaScript Namespace
        |--------------------------------------------------------------------------
        |
        | By default, we'll add variables to the global window object.
        | It's recommended that you change this to some namespace - anything.
        |
        */
        'namespace' => 'window'
    )
];