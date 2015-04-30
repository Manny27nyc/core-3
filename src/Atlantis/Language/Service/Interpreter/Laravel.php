<?php

namespace Atlantis\Language\Service\Interpreter;

use Atlantis\Language\Factory\Interpreter;
use Illuminate\Support\Facades\Lang;

class Laravel implements Interpreter{

    public function interpret($subject,$param=[]){
        return Lang::trans($subject,$param);
    }

}