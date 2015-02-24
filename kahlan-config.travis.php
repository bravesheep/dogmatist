<?php

use filter\Filter;
use kahlan\reporter\Coverage;
use kahlan\reporter\coverage\exporter\CodeClimate;

$args = $this->args();
$args->argument('coverage', 'default', 3);

Filter::register('kahlan.coverage-exporter', function ($chain) {
    $reporter = $this->reporters()->get('coverage');

    if (!$reporter || !getenv('CODECLIMATE_REPO_TOKEN')) {
        return $chain->next();
    }

    CodeClimate::write([
        'collector' => $reporter,
        'file' => 'codeclimate.json',
        'branch' => getenv('TRAVIS_BRANCH') ?: null,
        'repo_token' => getenv('CODECLIMATE_REPO_TOKEN')
    ]);

    return $chain->next();
});

Filter::apply($this, 'reporting', 'kahlan.coverage-exporter');

require_once __DIR__ . '/spec/init.php';
