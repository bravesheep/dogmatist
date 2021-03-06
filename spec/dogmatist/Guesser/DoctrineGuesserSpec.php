<?php

use Bravesheep\Dogmatist\Factory;
use Bravesheep\Dogmatist\Field;
use Bravesheep\Dogmatist\Guesser\DoctrineGuesser;
use Bravesheep\Spec\Entity\BasicFieldTest;
use Doctrine\Common\Persistence\AbstractManagerRegistry;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Tools\Setup;

describe("DoctrineGuesser", function () {
    before(function () {
        $paths = [realpath(__DIR__ . '/../../example/Entity')];
        $config = Setup::createAnnotationMetadataConfiguration($paths, true, null, null, false);

        $conn = [
            'driver' => 'pdo_sqlite',
            'path' => __DIR__ . '/../../db.sqlite',
        ];
        $this->em = EntityManager::create($conn, $config);
    });

    beforeEach(function () {
        $this->registry = Mockery::mock('Doctrine\Common\Persistence\AbstractManagerRegistry');
        $this->registry->shouldReceive('getManagerForClass')->andReturn($this->em);

        $this->filler = new DoctrineGuesser($this->registry);
        $this->dogmatist = Factory::create(\Faker\Factory::DEFAULT_LOCALE, $this->filler);
    });

    it("should map the basic fields of an entity", function () {
        $builder = $this->dogmatist->create('Bravesheep\Spec\Entity\BasicFieldTest');

        expect($builder->getFields())->toHaveLength(11);
        expect($builder->get('id')->isType(Field::TYPE_NONE))->toBe(true);
        expect($builder->get('string')->isType(Field::TYPE_FAKE))->toBe(true);
        expect($builder->get('string')->getFakedType())->toBe('text');
        expect($builder->get('bool')->isType(Field::TYPE_FAKE))->toBe(true);
        expect($builder->get('bool')->getFakedType())->toBe('boolean');
        expect($builder->get('int')->isType(Field::TYPE_FAKE))->toBe(true);
        expect($builder->get('int')->getFakedType())->toBe('randomNumber');
        expect($builder->get('smallint')->isType(Field::TYPE_FAKE))->toBe(true);
        expect($builder->get('smallint')->getFakedType())->toBe('numberBetween');
        expect($builder->get('decimal')->isType(Field::TYPE_CALLBACK))->toBe(true);
        expect($builder->get('datetime')->isType(Field::TYPE_FAKE))->toBe(true);
        expect($builder->get('datetime')->getFakedType())->toBe('datetime');
        expect($builder->get('datetimetz')->isType(Field::TYPE_CALLBACK))->toBe(true);
        expect($builder->get('text')->isType(Field::TYPE_FAKE))->toBe(true);
        expect($builder->get('text')->getFakedType())->toBe('text');
        expect($builder->get('float')->isType(Field::TYPE_FAKE))->toBe(true);
        expect($builder->get('float')->getFakedType())->toBe('randomFloat');
        expect($builder->get('guid')->isType(Field::TYPE_FAKE))->toBe(true);
        expect($builder->get('guid')->getFakedType())->toBe('uuid');
    });

    it("should generate a correct sample for the basic fields of an entity", function () {
        $builder = $this->dogmatist->create('Bravesheep\Spec\Entity\BasicFieldTest');
        /** @var BasicFieldTest $sample */
        $sample = $this->dogmatist->sample($builder);

        expect($sample->getDatetime())->toBeAnInstanceOf('DateTime');
        expect($sample->getDatetimetz())->toBeAnInstanceOf('DateTime');
        expect(is_numeric($sample->getDecimal()))->toBe(true);
        expect($sample->getString())->toBeA('string');
    });

    it("should be constructable with a manager registry", function () {
        $registry = Mockery::mock('Doctrine\Common\Persistence\AbstractManagerRegistry');
        $registry->shouldReceive('getManagerForClass')->with('example')->once()->andReturn($this->em);

        $filler = new DoctrineGuesser($registry);
        $dogmatist = Factory::create(\Faker\Factory::DEFAULT_LOCALE, $filler);

        $dogmatist->create('example');
    });
});
