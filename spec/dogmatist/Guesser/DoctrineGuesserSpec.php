<?php

use Bravesheep\Dogmatist\Factory;
use Bravesheep\Dogmatist\Guesser\DoctrineGuesser;
use Bravesheep\Spec\Entity\BasicFieldTest;
use Doctrine\Common\Persistence\AbstractManagerRegistry;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Tools\Setup;
use kahlan\plugin\Stub;

describe("DoctrineGuesser", function () {
    before(function () {
        $paths = [realpath(__DIR__ . '/../../example/Entity')];
        $config = Setup::createAnnotationMetadataConfiguration($paths, true, null, null, false);

        $conn = [
            'driver' => 'pdo_sqlite',
            'path' => __DIR__ . '/../../db.sqlite',
        ];
        $this->em = EntityManager::create($conn, $config);

        $this->registry = Stub::create([
            'extends' => AbstractManagerRegistry::class,
            'params' => [null, [], [], null, null, null]
        ]);
        Stub::on($this->registry)->method('getManagerForClass')->andReturn($this->em);

        $this->filler = new DoctrineGuesser($this->registry);
        $this->dogmatist = Factory::create(\Faker\Factory::DEFAULT_LOCALE, $this->filler);
    });

    it("should map the basic fields of an entity", function () {
        $builder = $this->dogmatist->create(BasicFieldTest::class);

        expect($builder->getFields())->toHaveLength(3);
    });
});
