<?php

namespace Bravesheep\Spec;

class ConstrExample
{
    const OPT1_DEFAULT = 101;

    const OPT2_DEFAULT = 102;

    public $req1;

    public $req2;

    public $opt1;

    public $opt2;

    public function __construct($req1, $req2, $opt1 = self::OPT1_DEFAULT, $opt2 = self::OPT2_DEFAULT)
    {
        $this->req1 = $req1;
        $this->req2 = $req2;
        $this->opt1 = $opt1;
        $this->opt2 = $opt2;
    }
}
