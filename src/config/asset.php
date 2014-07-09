<?php return [

    'build_path'    => public_path() . '/builds/assets',

    'cache'         => [
        'environment'   => ['staging','production']
    ],

    'mimes'         => [
        'stylesheet'    => ['css','less','scss'],
        'javascript'    => ['js','coffee']
    ],

    'register' => array(
        'prefixes' => array(
            'component'  => public_path() . '/components/'
        ),
        'attributes' => array(
            'appbase'   => app('config')->get('app.url')
        )
    ),

    'assets' => array(
        'default' => array(
            'storage'   => 'file',
            'path'      => public_path() . '/assets'
        ),

        'stylesheet' => array(
            // May contain global stylesheets
        ),
        'javascript' => array(
            // May contain global scripts
        )
    )

];