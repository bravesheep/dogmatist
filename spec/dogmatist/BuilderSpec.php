<?php

use Bravesheep\Dogmatist\Builder;
use Bravesheep\Dogmatist\Exception\BuilderException;
use Bravesheep\Dogmatist\Factory;
use Bravesheep\Dogmatist\Field;
use Bravesheep\Dogmatist\Guesser\NoneGuesser;

describe("Builder", function () {
    beforeEach(function () {
        $this->dogmatist = Factory::create(\Faker\Factory::DEFAULT_LOCALE, new NoneGuesser());
        $this->builder = new Builder('object', $this->dogmatist);
    });

    it("should have a field defined", function () {
        $this->builder->fake('example', 'number');
        expect($this->builder->has('example'))->toBe(true);
    });

    it("should create a faked field", function () {
        $this->builder->fake('example', 'number');
        $field = $this->builder->get('example');
        expect($field->getType())->toBe(Field::TYPE_FAKE);
        expect($field->isType(Field::TYPE_FAKE))->toBe(true);
        expect($field->getName())->toBe('example');
        expect($field->getFakedOptions())->toBe([]);
        expect($field->getFakedType())->toBe('number');
    });

    it("should update a field to none", function () {
        $this->builder->fake('example', 'number');
        $this->builder->none('example');
        $field = $this->builder->get('example');
        expect($field->getType())->toBe(Field::TYPE_NONE);
        expect($field->isType(Field::TYPE_NONE))->toBe(true);
    });

    it("should create a selection field", function () {
        $this->builder->select('example', [1, 2, 3, 4, 5]);
        $field = $this->builder->get('example');
        expect($field->getType())->toBe(Field::TYPE_SELECT);
        expect($field->isType(Field::TYPE_SELECT))->toBe(true);
        expect($field->getName())->toBe('example');
        expect($field->getSelection())->toBe([1, 2, 3, 4, 5]);
    });

    it("should create a selection field for a single value", function () {
        $this->builder->value('example', 'value');
        $field = $this->builder->get('example');
        expect($field->getType())->toBe(Field::TYPE_VALUE);
        expect($field->isType(Field::TYPE_VALUE))->toBe(true);
        expect($field->getName())->toBe('example');
        expect($field->getSelection())->toBe(['value']);
    });

    it("should create a relation to another builder", function () {
        $other = $this->builder->relation('example', 'array')->fake('field', 'string');
        $field = $this->builder->get('example');

        expect($other)->not->toBe($this->builder);
        expect($field->getType())->toBe(Field::TYPE_RELATION);
        expect($field->isType(Field::TYPE_RELATION))->toBe(true);
        expect($field->getName())->toBe('example');
        expect($field->getRelated())->toBe($other);
        expect($other->getClass())->toBe('array');
        expect($other->done())->toBe($this->builder);
        expect($other->hasParentLinks())->toBe(false);
    });

    it("should create a link to another builder", function () {
        $this->builder->link('example', 'another');
        $field = $this->builder->get('example');

        expect($field->getType())->toBe(Field::TYPE_LINK);
        expect($field->isType(Field::TYPE_LINK))->toBe(true);
        expect($field->getName())->toBe('example');
        expect($field->getLinkTarget())->toBe('another');
    });

    it("should create a callback field", function () {
        $cb = function () { return 0; };
        $this->builder->callback('example', $cb);
        $field = $this->builder->get('example');

        expect($field->getType())->toBe(Field::TYPE_CALLBACK);
        expect($field->isType(Field::TYPE_CALLBACK))->toBe(true);
        expect($field->getName())->toBe('example');
        expect($field->getCallback())->toBe($cb);
    });

    it("should return the dogmatist instance when done", function () {
        expect($this->builder->done())->toBe($this->dogmatist);
    });

    it("should set a field to produce multiple items", function () {
        $this->builder->fake('example', 'number');
        $this->builder->multiple('example', 2, 5);
        $field = $this->builder->get('example');

        expect($field->isMultiple())->toBe(true);
        expect($field->isSingular())->toBe(false);
        expect($field->getMin())->toBe(2);
        expect($field->getMax())->toBe(5);
    });

    it("should set a field to produce a single item", function () {
        $this->builder->fake('example', 'number');
        $this->builder->multiple('example', 2, 5);
        $this->builder->single('example');
        $field = $this->builder->get('example');

        expect($field->isMultiple())->toBe(false);
        expect($field->isSingular())->toBe(true);
    });

    it("should set a field to produce a single item as an array if set via multiple", function () {
        $this->builder->fake('example', 'number');
        $this->builder->multiple('example', 1, 1);
        $field = $this->builder->get('example');

        expect($field->isMultiple())->toBe(true);
        expect($field->isSingular())->toBe(false);
    });

    it("should not be possible to add a field to a builder that is not keyed using int or string", function () {
        $task = function () {
            $this->builder->fake(5.3, 'number');
        };
        expect($task)->toThrow(new BuilderException());
    });

    it("should also set strictness to created children and constructors", function () {
        $this->builder->relation('example', 'array')->fake('example', 'string');
        $this->builder->constructor()->argValue(10);
        $this->builder->setStrict(false);
        expect($this->builder->constructor()->isStrict())->toBe(false);
        expect($this->builder->get('example')->getRelated()->isStrict())->toBe(false);
    });

    it("should not have a field that was never defined", function () {
        expect($this->builder->has('field_not_used'))->toBe(false);
    });

    it("should create a unique field", function () {
        $this->builder->fake('example', 'number');
        $this->builder->unique('example');
        $field = $this->builder->get('example');

        expect($field->isUnique())->toBe(true);
    });

    it("should take the previously accessed field for withUnique", function () {
        $this->builder->fake('example', 'number')->withUnique();
        $field = $this->builder->get('example');

        expect($field->isUnique())->toBe(true);
    });

    it("should take the previously accessed field for withSingle", function () {
        $this->builder->fake('example', 'number')->withSingle();
        $field = $this->builder->get('example');

        expect($field->isSingular())->toBe(true);
    });

    it("should take the previously accessed field for withMultiple", function () {
        $this->builder->fake('example', 'number')->withMultiple(1, 5);
        $field = $this->builder->get('example');

        expect($field->isMultiple())->toBe(true);
        expect($field->getMin())->toBe(1);
        expect($field->getMax())->toBe(5);
    });

    it("should remove uniqueness from a field", function () {
        $this->builder->fake('example', 'number');
        $this->builder->unique('example');
        $this->builder->unique('example', false);
        $field = $this->builder->get('example');

        expect($field->isUnique())->toBe(false);
    });

    it("should be possible to set a link back to a parent for relations", function () {
        $other = $this->builder->relation('example', 'array')->linkParent('test');

        expect($other->hasParentLinks())->toBe(true);
        expect($other->getParentLinks())->toBe(['test']);
    });

    it("should be possible to unlink to the parent for relations", function () {
        $other = $this->builder->relation('example', 'array')->linkParent('test')->unlinkParent('test');
        expect($other->hasParentLinks())->toBe(false);
        expect($other->getParentLinks())->toBe([]);
    });

    it("should not be possible to create a link back to the parent in the constructor builder", function () {
        $task = function () {
            $this->builder->constructor()->linkParent('blaat');
        };
        expect($task)->toThrow(new BuilderException());
    });

    it("should not be possible to create a link back to the parent in an argument of the constructor", function () {
        $task = function () {
            $this->builder->constructor()->argRelation('object')->linkParent('blaat');
        };
        expect($task)->toThrow(new BuilderException());
    });

    it("should not be possible to create a link back to the parent in a root builder", function () {
        $task = function () {
            $this->builder->linkParent('blaat');
        };
        expect($task)->toThrow(new BuilderException());
    });

    it("should create a new constructor builder", function () {
        $constr = $this->builder->constructor()->argFake('number');
        $other = $this->builder->constructor(true)->argFake('number');

        expect($other)->not->toBe($constr);
    });

    it("should allow me to visit a related builder again", function () {
        $related = $this->builder->relation('rel', 'object');
        expect($this->builder->in('rel'))->toBe($related);
    });

    it("should not allow me to visit a related builder if a field is not of that type", function () {
        $task = function () {
            $this->builder->link('num', 'random');
            $this->builder->in('num');
        };

        expect($task)->toThrow(new BuilderException());
    });

    it("should not be possible to overwrite an existing builder", function () {
        $task = function () {
            $this->dogmatist->create('array')->fake('num', 'randomNumber')->save('a');
            $this->dogmatist->create('array')->fake('other', 'randomNumber')->save('a');
        };
        expect($task)->toThrow(new BuilderException());
    });

    it("should save with the typename if no name is given", function () {
        $builder = $this->dogmatist->create('array')->fake('num', 'randomNumber')->save();

        expect($this->dogmatist->retrieve('array'))->toBe($builder);
    });

    it("can sample directly from the builder", function () {
        $sample = $this->dogmatist->create('object')->fake('num', 'randomNumber')->sample();

        expect($sample)->toBeAnInstanceOf('stdClass');
        expect($sample->num)->toBeA('int');
    });

    it("can create multiple samples directly from the builder", function () {
        $samples = $this->dogmatist->create('object')->fake('num', 'randomNumber')->samples(2);

        expect($samples)->toHaveLength(2);
        expect($samples[0])->toBeAnInstanceOf('stdClass');
        expect($samples[1])->toBeAnInstanceOf('stdClass');
    });

    describe("copying the builder", function () {
        it("should create a copy of the builder", function () {
            $clone = $this->builder->copy();
            expect($clone)->not->toBe($this->builder);
        });

        it("should copy all fields of a builder", function () {
            $callback = function ($a, $b) { return 'a'; };

            /** @var Builder $builder */
            $builder = $this->builder;
            $builder
                ->fake('fake', 'number')
                ->callback('callback', $callback)
                ->value('value', 'value')
                ->select('select', [1, 2])
                ->link('link', 'other');

            $clone = $builder->copy();
            expect($clone->get('fake')->getFakedType())->toBe('number');
            expect($clone->get('callback')->getCallback())->toBe($callback);
            expect($clone->get('value')->getSelection())->toBe(['value']);
            expect($clone->get('select')->getSelection())->toBe([1, 2]);
            expect($clone->get('link')->getLinkTarget())->toBe('other');
        });

        it("should copy a related builder and update the parent", function () {
            $this->builder->relation('rel', 'object')->fake('faked', 'randomNumber')->done();
            $clone = $this->builder->copy();

            expect($clone->get('rel')->getRelated()->done())->toBe($clone);
            expect($clone->get('rel')->getRelated()->done())->not->toBe($this->builder);
        });

        it("should set the new copy type when requested", function () {
            $builder = $this->dogmatist->create('object')->copy('array');
            expect($builder->getType())->toBe('array');
        });

        it("should not change the copy type when not set", function () {
            $builder = $this->dogmatist->create('object')->copy();
            expect($builder->getType())->toBe('object');
        });

        it("should copy and update the constructor builder", function () {
            $original = $this->dogmatist->create('object')->constructor()->argFake('randomNumber')->done();
            $builder = $original->copy();
            expect($builder->constructor()->done())->toBe($builder);
            expect($builder->constructor())->not->toBe($original->constructor());
        });

        it("should copy a relation from a saved builder", function () {
            $builder = $this->dogmatist->create('object')->fake('num', 'randomNumber');
            $related = $this->builder->relationFromCopy('test', $builder);

            expect($related)->not->toBe($builder);
            expect($related->get('num')->getType())->toBe(Field::TYPE_FAKE);
        });
    });
});
