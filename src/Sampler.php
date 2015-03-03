<?php

namespace Bravesheep\Dogmatist;

use Bravesheep\Dogmatist\Exception\SampleException;
use Symfony\Component\PropertyAccess\Exception\NoSuchPropertyException;
use Symfony\Component\PropertyAccess\PropertyAccessorInterface;

class Sampler
{
    /**
     * @var Dogmatist
     */
    private $dogmatist;

    /**
     * @var PropertyAccessorInterface
     */
    private $accessor;

    public function __construct(Dogmatist $dogmatist, PropertyAccessorInterface $accessor)
    {
        $this->dogmatist = $dogmatist;
        $this->accessor = $accessor;
    }

    /**
     * @param Builder $builder
     * @param int     $count
     * @return array
     */
    public function samples(Builder $builder, $count)
    {
        $samples = [];
        for ($i = 0; $i < $count; $i++) {
            $samples[] = $this->sample($builder);
        }

        return $samples;
    }

    /**
     * @param Builder $builder
     * @return object|array
     */
    public function sample(Builder $builder)
    {
        $data = [];
        $faker = $this->dogmatist->getFaker();
        foreach ($builder->getFields() as $field) {
            if (!$field->isType(Field::TYPE_NONE)) {
                $generate = $field->isSingular() ? 1 : $faker->numberBetween($field->getMin(), $field->getMax());
                $samples = [];
                for ($i = 0; $i < $generate; $i++) {
                    $samples[] = $this->sampleField($field, $data);
                }

                if ($field->isSingular()) {
                    $samples = $samples[0];
                }
                $data[$field->getName()] = $samples;
            }
        }

        $result = $this->insertInObject($data, $builder);

        foreach ($builder->getListeners() as $listener) {
            call_user_func($listener, $result);
        }

        return $result;
    }

    /**
     * @param Field   $field
     * @param array   $data
     * @param Builder $builder
     * @return mixed
     * @throws SampleException
     */
    private function sampleField(Field $field, array $data)
    {
        $faker = $this->dogmatist->getFaker();
        $type = $field->getType();
        $value = null;

        if (Field::TYPE_FAKE === $type) {
            try {
                $value = $faker->format($field->getFakedType(), $field->getFakedOptions());
            } catch (\Exception $e) {
                throw new SampleException("Could not fake value of type {$field->getFakedType()}", 0, $e);
            }
        } elseif (Field::TYPE_VALUE === $type || Field::TYPE_SELECT === $type) {
            $value = $faker->randomElement($field->getSelection());
        } elseif (Field::TYPE_RELATION === $type) {
            $value = $this->sample($field->getRelated());
        } elseif (Field::TYPE_LINK === $type) {
            $samples = $this->dogmatist->getLinkManager()->samples($field->getLinkTarget());
            $value = $faker->randomElement($samples);
        } elseif (Field::TYPE_CALLBACK === $type) {
            $callback = $field->getCallback();
            $value = $callback($data, $this->dogmatist);
        } else {
            throw new SampleException("Could not generate data for field of type {$field->getType()}");
        }

        return $value;
    }

    /**
     * @param array   $data
     * @param Builder $builder
     * @return array|object
     * @throws SampleException
     */
    private function insertInObject(array $data, Builder $builder)
    {
        // special case for arrays: just return the array data
        if ($builder->getClass() === 'array' || $builder->getClass() === '__construct') {
            return $data;
        }

        // special case for generic objects (objects of type stdClass): just cast as an object
        if ($builder->getClass() === 'object' || $builder->getClass() === 'stdClass') {
            return (object) $data;
        }

        $refl = new \ReflectionClass($builder->getClass());
        $obj = $this->constructObject($refl, $builder);

        foreach ($data as $key => $val) {
            try {
                $this->accessor->setValue($obj, $key, $val);
            } catch (NoSuchPropertyException $e) {
                if ($builder->isStrict()) {
                    throw new SampleException("Could not set value", 0, $e);
                } else {
                    if ($refl->hasProperty($key)) {
                        $prop = $refl->getProperty($key);
                        $prop->setAccessible(true);
                        $prop->setValue($obj, $val);
                        $prop->setAccessible(false);
                    } else {
                        $obj->{$key} = $val;
                    }
                }
            }
        }

        return $obj;
    }

    /**
     * @param \ReflectionClass $refl
     * @param Builder          $builder
     * @return object
     * @throws SampleException
     */
    private function constructObject(\ReflectionClass $refl, Builder $builder)
    {
        $constructor = $refl->getConstructor();
        $obj = null;
        if (null === $constructor || $constructor->getNumberOfParameters() === 0) {
            $obj = $refl->newInstance();
        } elseif ($constructor) {
            if (!$builder->hasConstructor() && $constructor->getNumberOfRequiredParameters() === 0) {
                $obj = $refl->newInstance();
            } elseif ($builder->hasConstructor()) {
                $args = $this->alignArgs($constructor, $this->sample($builder->constructor()), $builder->constructor());
                $obj = $refl->newInstanceArgs($args);
            }
        }

        if (null === $obj && !$builder->isStrict()) {
            $obj = $refl->newInstanceWithoutConstructor();
        } elseif (null === $obj) {
            throw new SampleException("Constructor required for constructing {$builder->getClass()} in strict mode");
        }

        return $obj;
    }

    /**
     * @param \ReflectionMethod $constructor
     * @param array             $data
     * @return array
     * @throws SampleException
     */
    private function alignArgs(\ReflectionMethod $constructor, array $data, ConstructorBuilder $builder)
    {
        if ($builder->isPositional()) {
            return $data;
        }

        $aligned = [];
        foreach ($constructor->getParameters() as $param) {
            if (isset($data[$param->getName()])) {
                $aligned[] = $data[$param->getName()];
            } else {
                try {
                    $aligned[] = $param->getDefaultValue();
                } catch (\ReflectionException $e) {
                    throw new SampleException("No value provided for argument {$param->getName()}", 0, $e);
                }
            }
        }

        if (count($aligned) < $constructor->getNumberOfRequiredParameters()) {
            throw new SampleException("Not enough arguments provided for constructing the object");
        }

        return $aligned;
    }
}
