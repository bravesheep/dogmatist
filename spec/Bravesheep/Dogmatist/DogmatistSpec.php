<?php

use Bravesheep\Dogmatist\Builder;
use Bravesheep\Dogmatist\Exception\NoSuchIndexException;
use Bravesheep\Dogmatist\Exception\SampleException;
use Bravesheep\Dogmatist\Factory;
use Bravesheep\Dogmatist\Filler\FillerInterface;
use Bravesheep\Dogmatist\LinkManager;
use Bravesheep\Dogmatist\Sampler;

describe("Dogmatist", function () {
    beforeEach(function () {
       $this->dogmatist = Factory::create();
    });

    it("should be able to create builders", function () {
        $builder = $this->dogmatist->create('object');
        expect($builder)->toBeAnInstanceOf(Builder::class);
    });

    it("should retrieve the link manager", function () {
        expect($this->dogmatist->getLinkManager())->toBeAnInstanceOf(LinkManager::class);
    });

    it("should retrieve the sampler", function () {
        expect($this->dogmatist->getSampler())->toBeAnInstanceOf(Sampler::class);
    });

    it("should retrieve the filler", function () {
        expect($this->dogmatist->getFiller())->toBeAnInstanceOf(FillerInterface::class);
    });

    it("should retrieve the faker instance", function () {
        expect($this->dogmatist->getFaker())->toBeAnInstanceOf(\Faker\Generator::class);
    });

    describe("working with saved builders", function () {
        beforeEach(function () {
            $this->builder = $this->dogmatist->create('object')->fake('example', 'name')->save('example', 1);
        });

        it("should create a sample from a saved builder", function () {
            expect($this->dogmatist->sample('example'))->toBeAnInstanceOf(stdClass::class);
        });

        it("should retrieve the saved builder", function () {
            expect($this->dogmatist->retrieve('example'))->toBe($this->builder);
        });

        it("should not generate more examples than requested", function () {
            $sample1 = $this->dogmatist->sample('example');
            $sample2 = $this->dogmatist->sample('example');
            expect($sample1)->toBe($sample2);
        });

        it("should not be possible to retrieve more than the available number of samples", function () {
            expect(function () { $this->dogmatist->samples('example', 2); })->toThrow(new SampleException());
        });

        it("should generate fresh samples", function () {
            $sample = $this->dogmatist->sample('example');
            expect($this->dogmatist->freshSample('example'))->not->toBe($sample);
            expect($this->dogmatist->freshSamples('example', 1))->not->toContain($sample);
        });

        it("should be allowed to generate more fresh samples", function () {
            expect($this->dogmatist->freshSamples('example', 10))->toHaveLength(10);
        });

        it("should generate multiple unique samples", function () {
            $this->dogmatist->create('object')->fake('example', 'name')->save('subexample', 2);
            $samples = $this->dogmatist->samples('subexample', 2);
            expect($samples)->toHaveLength(2);
            expect($samples[0])->not->toBe($samples[1]);
        });

        it("should fail on retrieving a non-existant builder", function () {
            $task = function () {
                $this->dogmatist->sample('non-existant');
            };
            expect($task)->toThrow(new NoSuchIndexException());
        });

        it("should clear samples for overwritten builders", function () {
            $sample = $this->dogmatist->sample('example');
            $this->dogmatist->create('array')->fake('item', 'randomNumber')->save('example', 1);
            $new_sample = $this->dogmatist->sample('example');
            expect($sample)->not->toBe($new_sample);
        });
    });
});
