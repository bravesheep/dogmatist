<?php

namespace Bravesheep\Dogmatist\Filler;

use Bravesheep\Dogmatist\Builder;
use Bravesheep\Dogmatist\Util;
use Doctrine\Common\Annotations\AnnotationReader;
use Doctrine\Common\Annotations\Reader;

class AnnotationFiller implements FillerInterface
{
    private $reader;

    /**
     * @param Reader $reader
     */
    public function __construct(Reader $reader = null)
    {
        if (null === $reader) {
            $reader = new AnnotationReader();
        }
        $this->reader = $reader;
    }

    /**
     * {@inheritdoc}
     */
    public function fill(Builder $builder)
    {
        $class = $builder->getClass();
        if (Util::isUserClass($class) && class_exists($class, true)) {
            $refl = new \ReflectionClass($class);
            /** @var Annotations\Dogma $annot */
            $annot = $this->reader->getClassAnnotation($refl, Annotations\Dogma::class);
            if (null !== $annot) {
                $builder->setStrict($annot->strict);
            }

            $this->processConstructor($refl, $builder);
            $this->processFields($refl, $builder);
        }
    }

    /**
     * @param \ReflectionClass $refl
     * @param Builder          $builder
     */
    private function processFields(\ReflectionClass $refl, Builder $builder)
    {
        $properties = $refl->getProperties();
        foreach ($properties as $prop) {
            $this->processField($prop, $builder);
        }
    }

    /**
     * @param \ReflectionProperty $prop
     * @param Builder             $builder
     */
    private function processField(\ReflectionProperty $prop, Builder $builder)
    {
        $type = null;
        $count = null;
        foreach ($this->reader->getPropertyAnnotations($prop) as $annot) {
            if ($annot instanceof Annotations\FieldInterface) {
                $type = $annot;
            }

            if ($annot instanceof Annotations\QuantityInterface) {
                $count = $annot;
            }

            if ($annot instanceof Annotations\Field) {
                $type = $annot->type;
                $count = $annot->count;
            }
        }

        if (null !== $type) {
            $this->addField($prop->getName(), $type, $count, $builder);
        }
    }

    /**
     * @param string                        $name
     * @param Annotations\FieldInterface    $annot
     * @param Annotations\QuantityInterface $count
     * @param Builder                       $builder
     */
    private function addField($name, Annotations\FieldInterface $annot, $count, Builder $builder)
    {
        if ($annot instanceof Annotations\Fake) {
            $builder->fake($name, $annot->type, $annot->args);
        } elseif ($annot instanceof Annotations\Link) {
            $builder->link($name, $annot->target);
        } elseif ($annot instanceof Annotations\None) {
            $builder->none($name);
        } elseif ($annot instanceof Annotations\Select) {
            $builder->select($name, $annot->values);
        } elseif ($annot instanceof Annotations\Value) {
            $builder->value($name, $annot->value);
        } elseif ($annot instanceof Annotations\Relation) {
            $sub = $builder->relation($name, $annot->type);
            $this->processSub($annot->description, $sub);
        }

        if ($count instanceof Annotations\Multiple) {
            $builder->multiple($name, $count->min, $count->max);
        } else {
            $builder->single($name);
        }
    }

    /**
     * @param Annotations\Description $descr
     * @param Builder                 $builder
     */
    private function processSub(Annotations\Description $descr, Builder $builder)
    {
        foreach ($descr->fields as $name => $field) {
            $this->addField($name, $field->type, $field->count, $builder);
        }

        if ($descr->constructor instanceof Annotations\Constructor) {
            $this->addConstructor($descr->constructor, $builder);
        }
    }

    /**
     * @param \ReflectionClass $refl
     * @param Builder          $builder
     */
    private function processConstructor(\ReflectionClass $refl, Builder $builder)
    {
        $constructor = $refl->getConstructor();
        if (null !== $constructor) {
            $annot = $this->reader->getMethodAnnotation($constructor, Annotations\Constructor::class);
            if (null !== $annot) {
                $this->addConstructor($annot, $builder);
            }
        }
    }

    /**
     * @param Annotations\Constructor $constr
     * @param Builder                 $builder
     */
    private function addConstructor(Annotations\Constructor $constr, Builder $builder)
    {
        $cb = $builder->constructor();
        foreach ($constr->args as $name => $arg) {
            $this->addField($name, $arg->type, $arg->count, $cb);
        }
    }
}
