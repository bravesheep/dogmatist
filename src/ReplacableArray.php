<?php

namespace Bravesheep\Dogmatist;

class ReplacableArray
{
    /**
     * @var ReplacableLink[]
     */
    public $data;

    /**
     * @param ReplacableLink[] $data
     */
    public function __construct(array $data)
    {
        $this->data = $data;
    }
}
