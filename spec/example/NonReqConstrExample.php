<?php

namespace Bravesheep\Spec;

class NonReqConstrExample
{
    const OPT1_DEFAULT = 101;

    const OPT2_DEFAULT = 102;

    public $opt1;

    public $opt2;

    public function __construct($opt1 = self::OPT1_DEFAULT, $opt2 = self::OPT2_DEFAULT)
    {
        $this->opt1 = $opt1;
        $this->opt2 = $opt2;
    }
}
