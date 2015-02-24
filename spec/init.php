<?php

use Doctrine\Common\Annotations\AnnotationRegistry;
use filter\Filter;
use jit\Interceptor;

Filter::register('doctrine.exclude.annotations', function ($chain) {
    $extra = [
        'Doctrine\\Common\\Annotations\\Annotation'
    ];

    $exclude = $this->args()->get('exclude');
    $exclude = is_array($exclude) ? $exclude + $extra : $extra;
    $this->args()->set('exclude', $exclude);

    return $chain->next();
});

Filter::register('doctrine.annotations.autoloader', function ($chain) {
    AnnotationRegistry::registerLoader(Interceptor::instance()->loader());
    return $chain->next();
});

Filter::apply($this, 'interceptor', 'doctrine.exclude.annotations');
Filter::apply($this, 'run', 'doctrine.annotations.autoloader');
