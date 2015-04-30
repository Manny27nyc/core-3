<?php

namespace Atlantis\Language\Factory;


interface Translator {

    public function translate($subject,$params=[]);

}