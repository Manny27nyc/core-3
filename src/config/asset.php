<?php return [

    'build_path'    => public_path() . '/builds/assets',

    //
    // Asset will be process for caching, basically all asset will be cache
    // but with no further processing. Environment configure for cache will
    // be minify and concatenate into single file automatically base on
    // namespace.
    //
    'cache'         => [
        'environment'   => ['staging','production']
    ],

    //
    // Asset loader will be scanning through folder for the file types
    //
    'mimes'         => [
        'stylesheet'    => ['css','less','scss'],
        'javascript'    => ['js','coffee']
    ],

    //
    // Default interpolation of path prefixes and value attributes
    //
    'register' => array(
        'prefixes' => array(
            'component'  => public_path() . '/components/'
        ),
        'attributes' => array(
            'appbase'   => app('config')->get('app.url')
        )
    ),

    //
    // Default assets
    //
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