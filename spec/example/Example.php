<?php

namespace Bravesheep\Spec;

class Example
{
    public $pub;

    private $priv_priv;

    private $priv_pub;

    public function getPrivPriv()
    {
        return $this->priv_priv;
    }

    public function getPrivPub()
    {
        return $this->priv_pub;
    }

    public function setPrivPub($priv_pub)
    {
        $this->priv_pub = $priv_pub;
    }
}



