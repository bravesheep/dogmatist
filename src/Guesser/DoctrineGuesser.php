<?php

namespace Bravesheep\Dogmatist\Guesser;

use Bravesheep\Dogmatist\Builder;
use Bravesheep\Dogmatist\Util;
use Doctrine\Common\Persistence\ManagerRegistry;
use Doctrine\Common\Persistence\Mapping\ClassMetadata;

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
        if (Util::isUserClass($builder->getClass())) {
            $em = $this->registry->getManagerForClass($builder->getClass());
            if (null !== $em) {
                $metadata = $em->getClassMetadata($builder->getClass());
                if ($metadata instanceof \Doctrine\ORM\Mapping\ClassMetadata) {
                    $this->processFull($metadata, $builder);
                } else {
                    $this->processBasic($metadata, $builder);
                }

            }
        }
    }

    private function processFull(\Doctrine\ORM\Mapping\ClassMetadata $metadata, Builder $builder)
    {
        foreach ($metadata->getFieldNames() as $fname) {
            if ($metadata->isIdentifier($fname)) {
                $builder->none($fname);
            } else {
                $type = $metadata->getTypeOfField($fname);
            }
        }

        foreach ($metadata->getAssociationNames() as $aname) {

        }
    }

    private function processBasic(ClassMetadata $metadata, Builder $builder)
    {
        foreach ($metadata->getFieldNames() as $fname) {
            if ($metadata->isIdentifier($fname)) {
                $builder->none($fname);
            } else {
                $type = $metadata->getTypeOfField($fname);
            }
        }

        foreach ($metadata->getAssociationNames() as $aname) {

        }
    }
}
