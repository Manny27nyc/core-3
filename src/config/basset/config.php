<?php return array(

    'collections' => array(
        'common' => function($collection){},
        'application' => function($collection){},
        'public' => function($collection){},
        'user' => function($collection){},
        'admin' => function($collection){},
    ),

    'production' => array('production', 'staging'),

    'build_path' => 'builds',

    'debug' => false,

    'node_paths' => array(
        public_path().'/node_modules'
    ),

    'gzip' => true,

    'aliases' => array(

        'assets' => array(),

        'filters' => array(

            'Less' => array('LessphpFilter', function($filter)
            {
                $filter->whenAssetIs('.*\.less')->findMissingConstructorArgs();
            }),

            'Sass' => array('ScssphpFilter', function($filter)
            {
                $filter->whenAssetIs('.*\.(sass|scss)')->findMissingConstructorArgs();
            }),

            'CoffeeScript' => array('CoffeeScriptFilter', function($filter)
            {
                $filter->whenAssetIs('.*\.coffee')->whenClassExists('CoffeeScript')->findMissingConstructorArgs();
            }),

            'CssMin' => array('CssMinFilter', function($filter)
            {
                $filter->whenAssetIsStylesheet()->whenProductionBuild()->whenClassExists('CssMin');
            }),

            'JsMin' => array('JSMinFilter', function($filter)
            {
                $filter->whenAssetIsJavascript()->whenProductionBuild()->whenClassExists('JSMin');
            }),

            'UriRewriteFilter' => array('UriRewriteFilter', function($filter)
            {
                $filter->setArguments(public_path())->whenAssetIsStylesheet();
            })

        )

    )

);
