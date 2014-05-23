<?php namespace Atlantis\Helpers;

use Illuminate\Support\Facades\Request;


Class String {

    public function url_base(){
        $url_full = Request::url();
        $url_path = Request::path();
        $url_root = parse_url($url_full, PHP_URL_SCHEME).'://'.parse_url($url_full, PHP_URL_HOST);;

        $base = str_replace($url_path,'',$url_full);
        $base = str_replace($url_root,'',$base);

        return rtrim($base,'/');
    }


    public function people_name($name){
        $result = array(
            'first_name' => '',
            'last_name' => '',
            'gender' => '',
            'prefix' => '',
            'middle' => '',
        );

        $namePrefixes = array(
            'encik',
            'en',
            'en.',
            'sdr',
            'sdr.',
            'saudara',
            'yb',
            'yab',
            'ybhg',
            'ym'
        );

        $nameTitles = array(
            'dato',
            'datuk',
            'senator dato',
            'senator',
        );

        $middleGender = array(
            'bin' => 'male',
            'binti' => 'female'
        );

        $prefixGender = array(
            'haji' => 'male',
            'hj' => 'male',
            'hajjah' => 'female',
            'hjh' => 'female',
        );

        $name = strtolower($name);
        $name = trim($name);
        $name = str_replace('bt. ','binti ',$name);
        $name = str_replace('bt ','binti ',$name);
        $name = str_replace('bte ','binti ',$name);
        $name = str_replace('b. ','bin ',$name);
        $name = str_replace('b ','bin ',$name);
        $name = preg_replace('/\s\s*/', ' ',$name);
        //.stripPunctuation().s;

        //[i] Gender detection by middle
        if( strpos($name, ' bin ') ){
            $names = explode(' bin ', $name);

            $result['first_name'] = $names[0];
            $result['last_name'] = $names[1];
            $result['gender'] = 'male';

        }else if( strpos($name, ' binti ') ){
            $names = explode(' binti ', $name);

            $result['first_name'] = $names[0];
            $result['last_name'] = $names[1];
            $result['gender'] = 'female';
        }else{
            $result['first_name'] = $name;
        }


        //[i] Gender detection by prefix
        foreach($prefixGender as $gender => $prefix){
            if( $this->starts_with($name, $prefix.' ') ){
                $result['middle'] = $prefix;
                $result['gender'] = $gender;
            }
        };

        /*
        //[i] Removing common name prefixes(panggilan)
        _.each(namePrefixes,function(prefix){
            if( _(people.name).startsWith(prefix+' ') ){
                people.name = S(people.name).chompLeft(prefix).trim().s;
            }
        });

        //[i] Separating legal titles
        _.each(nameTitles,function(prefix){
            if( _(people.name).startsWith(prefix) ){
                people.name = S(people.name).chompLeft(prefix).trim().s;
                people.prefix = _(prefix).titleize();
            }
        });*/

        return $result;
    }


    public function starts_with($haystack, $needle){
        $length = strlen($needle);
        return (substr($haystack, 0, $length) === $needle);
    }


    public function ends_with($haystack, $needle){
        $length = strlen($needle);
        if ($length == 0) {
            return true;
        }

        return (substr($haystack, -$length) === $needle);
    }
}