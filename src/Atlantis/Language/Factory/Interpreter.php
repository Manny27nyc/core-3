<?php

namespace Atlantis\Language\Factory;


interface Interpreter {

    public function interpret($subject,$params=[]);

}