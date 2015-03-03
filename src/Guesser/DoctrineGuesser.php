<?php

namespace Bravesheep\Dogmatist\Guesser;

use Bravesheep\Dogmatist\Builder;
use Bravesheep\Dogmatist\Dogmatist;
use Bravesheep\Dogmatist\Util;
use Doctrine\Common\Persistence\ManagerRegistry;
use Doctrine\Common\Persistence\Mapping\MappingException;
use Doctrine\ORM\Mapping\ClassMetadata;

class DoctrineGuesser implements GuesserInterface
{
    /**
     * @var ManagerRegistry
     */
    private $registry;

    public function __construct(ManagerRegistry $registry)
    {
        $this->registry = $registry;
    }

    public function fill(Builder $builder)
    {
        $manager = null;
        if (Util::isUserClass($builder->getClass())) {
            $manager = $this->registry->getManagerForClass($builder->getClass());
            if (null !== $manager) {
                try {
                    $metadata = $manager->getClassMetadata($builder->getClass());
                    if ($metadata instanceof ClassMetadata) {
                        $this->process($metadata, $builder);
                    }
                } catch (MappingException $e) {
                }
            }
        }
    }

    private function process(ClassMetadata $metadata, Builder $builder)
    {
        foreach ($metadata->getFieldNames() as $fname) {
            if ($metadata->isIdentifier($fname)) {
                $builder->none($fname);
            } elseif (!$builder->has($fname)) {
                $mapping = $metadata->fieldMappings[$fname];
                $this->makeField($mapping, $builder);
            }
        }
    }

    private function makeField(array $mapping, Builder $builder)
    {
        $field = $mapping['fieldName'];
        switch ($mapping['type']) {
            case 'string':
                $length = isset($mapping['length']) && is_int($mapping['length']) ? $mapping['length'] : 255;
                $builder->fake($field, 'text', [$length]);
                break;
            case 'integer':
            case 'bigint':
                $builder->fake($field, 'randomNumber');
                break;
            case 'smallint':
                $builder->fake($field, 'numberBetween', [0, 65535]);
                break;
            case 'boolean':
                $builder->fake($field, 'boolean');
                break;
            case 'decimal':
                $precision = isset($mapping['precision']) ? $mapping['precision'] : 2;
                $builder->callback($field, function ($data, Dogmatist $dogmatist) use ($precision) {
                    return $dogmatist->getFaker()->randomNumber($precision + 2) / 100;
                });
                break;
            case 'date':
            case 'time':
            case 'datetime':
                $builder->fake($field, 'datetime');
                break;
            case 'datetimetz':
                $builder->callback($field, function ($data, Dogmatist $dogmatist) {
                    $date = $dogmatist->getFaker()->dateTime;
                    $tz = $dogmatist->getFaker()->timezone;
                    return $date->setTimezone(new \DateTimeZone($tz));
                });
                break;
            case 'text':
                $builder->fake($field, 'text');
                break;
            case 'float':
                $builder->fake($field, 'randomFloat');
                break;
            case 'guid':
                $builder->fake($field, 'uuid');
                break;
        }
    }
}
