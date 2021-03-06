<?php

use Bravesheep\Dogmatist\Exception\BuilderException;
use Bravesheep\Dogmatist\Exception\SampleException;
use Bravesheep\Dogmatist\Factory;
use Bravesheep\Dogmatist\Field;
use Bravesheep\Spec\NonReqConstrExample;

describe("ConstructorBuilder", function () {
    before(function () {
        $this->dogmatist = Factory::create();
        $this->sampler = $this->dogmatist->getSampler();
    });

    it("should return the base builder as a parent", function () {
        $builder = $this->dogmatist->create('Bravesheep\Spec\ConstrExample');
        expect($builder->constructor()->done())->toBe($builder);
    });

    it("should not have been created in the parent constructor by default", function () {
        $builder = $this->dogmatist->create('Bravesheep\Spec\ConstrExample');
        expect($builder->hasConstructor())->toBe(false);
    });

    it("should not be possible to add a constructor to a constructor", function () {
        $task = function () {
            $this->dogmatist->create('Bravesheep\Spec\ConstrExample')->constructor()->constructor();
        };
        expect($task)->toThrow(new BuilderException());
    });

    it("should not be possible to save a constructor as a builder in the manager", function () {
        $task = function () {
            $this->dogmatist->create('Bravesheep\Spec\ConstrExample')->constructor()->save('example', 1);
        };
        expect($task)->toThrow(new BuilderException());
    });

    it("should not be possible to add an event listener to the constructor", function () {
        $task = function () {
            $this->dogmatist->create('Bravesheep\Spec\ConstrExample')->constructor()->onCreate(function () {});
        };
        expect($task)->toThrow(new BuilderException());
    });

    it("should not be possible to add a positional argument if named arguments exists", function () {
        $task = function () {
            $this->dogmatist->create('Bravesheep\Spec\ConstrExample')->constructor()
                ->fake('req1', 'randomNumber')
                ->argFake('randomNumber');
        };
        expect($task)->toThrow(new BuilderException());
    });

    it("should not be possible to add an explicit positional argument if named arguments exist", function () {
        $task = function () {
            $this->dogmatist->create('Bravesheep\Spec\ConstrExample')->constructor()
                ->fake('req1', 'randomNumber')
                ->fake(0, 'randomNumber');
        };
        expect($task)->toThrow(new BuilderException());
    });

    it("should not be possible to add a named argument if a positional argument exists", function () {
        $task = function () {
            $this->dogmatist->create('Bravesheep\Spec\ConstrExample')->constructor()
                ->argFake('randomNumber')
                ->fake('req1', 'randomNumber');
        };
        expect($task)->toThrow(new BuilderException());
    });

    describe("working with positional arguments", function () {
        beforeEach(function () {
            $this->builder = $this->dogmatist->create('Bravesheep\Spec\ConstrExample');
        });

        it("should add a positional faked argument", function () {
            $this->builder->constructor()->argFake('randomNumber');
            $field = $this->builder->constructor()->get(0);
            expect($field->getName())->toBe(0);
            expect($field->getType())->toBe(Field::TYPE_FAKE);
            expect($field->isType(Field::TYPE_FAKE))->toBe(true);
            expect($field->getFakedType())->toBe('randomNumber');
        });

        it("should add a selection positional argument", function () {
            $this->builder->constructor()->argSelect([1, 2, 3, 4, 5]);
            $field = $this->builder->constructor()->get(0);
            expect($field->getName())->toBe(0);
            expect($field->getType())->toBe(Field::TYPE_SELECT);
            expect($field->isType(Field::TYPE_SELECT))->toBe(true);
            expect($field->getSelection())->toBe([1, 2, 3, 4, 5]);
        });

        it("should add a value positional argument", function () {
            $this->builder->constructor()->argValue('test');
            $field = $this->builder->constructor()->get(0);

            expect($field->getName())->toBe(0);
            expect($field->getType())->toBe(Field::TYPE_VALUE);
            expect($field->isType(Field::TYPE_VALUE))->toBe(true);
            expect($field->getSelection())->toBe(['test']);
        });

        it("should add a link positional argument", function () {
            $this->builder->constructor()->argLink('other');
            $field = $this->builder->constructor()->get(0);

            expect($field->getName())->toBe(0);
            expect($field->getType())->toBe(Field::TYPE_LINK);
            expect($field->isType(Field::TYPE_LINK))->toBe(true);
            expect($field->getLinkTarget())->toBe('other');
        });

        it("should add a relation positional argument", function () {
            $this->builder->constructor()->argRelation('object')->fake('item', 'randomNumber');
            $field = $this->builder->constructor()->get(0);

            expect($field->getName())->toBe(0);
            expect($field->getType())->toBe(Field::TYPE_RELATION);
            expect($field->isType(Field::TYPE_RELATION))->toBe(true);
            expect($field->getRelated())->toBeAnInstanceOf('Bravesheep\Dogmatist\Builder');
            expect($field->getRelated()->done())->toBe($this->builder->constructor());
        });

        it("should add a positional callback", function () {
            $cb = function () { return 0; };
            $this->builder->constructor()->argCallback($cb);
            $field = $this->builder->constructor()->get(0);

            expect($field->getName())->toBe(0);
            expect($field->getType())->toBe(Field::TYPE_CALLBACK);
            expect($field->isType(Field::TYPE_CALLBACK))->toBe(true);
            expect($field->getCallback())->toBe($cb);
        });
    });

    describe("working with a constructor with optionals", function () {
        beforeEach(function () {
            $this->builder = $this->dogmatist->create('Bravesheep\Spec\NonReqConstrExample');
        });

        it("should generate samples when no constructor is specified", function () {
            $sample = $this->sampler->sample($this->builder);
            expect($sample)->toBeAnInstanceOf('Bravesheep\Spec\NonReqConstrExample');
            expect($sample->opt1)->toBe(NonReqConstrExample::OPT1_DEFAULT);
            expect($sample->opt2)->toBe(NonReqConstrExample::OPT2_DEFAULT);
        });

        it("should generate faked values for the named constructor arguments specified", function () {
            $this->builder->constructor()->fake('opt2', 'randomLetter');
            $sample = $this->sampler->sample($this->builder);
            expect($sample->opt1)->toBe(NonReqConstrExample::OPT1_DEFAULT);
            expect($sample->opt2)->toBeA('string');
        });

        it("should generate faked values for the positional constructor arugments specified", function () {
            $this->builder->constructor()->argFake('randomLetter');
            $sample = $this->sampler->sample($this->builder);
            expect($sample->opt1)->toBeA('string');
            expect($sample->opt2)->toBe(NonReqConstrExample::OPT2_DEFAULT);
        });
    });

    describe("working with a constructor with required items", function () {
        beforeEach(function () {
            $this->builder = $this->dogmatist->create('Bravesheep\Spec\ConstrExample');
        });

        it("should fail without calling the constructor in strict mode", function () {
            $task = function () {
                $this->sampler->sample($this->builder);
            };
            expect($task)->toThrow(new SampleException());
        });

        it("should not fail without calling the constructor in non-strict mode", function () {
            $this->builder->setStrict(false);
            $sample = $this->sampler->sample($this->builder);
            expect($sample->opt1)->toBe(null);
            expect($sample->opt2)->toBe(null);
            expect($sample->req1)->toBe(null);
            expect($sample->req2)->toBe(null);
        });

        it("should fail if not enough required arguments are provided", function () {
            $this->builder->constructor()->argFake('randomNumber');
            $task = function () {
                $this->sampler->sample($this->builder);
            };
            expect($task)->toThrow(new SampleException());
        });

        it("should fail if not enough required named arguments are provided", function () {
            $this->builder->constructor()->fake('req2', 'randomNumber');
            $task = function () {
                $this->sampler->sample($this->builder);
            };
            expect($task)->toThrow(new SampleException());
        });
    });
});
