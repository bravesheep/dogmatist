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
            if (!$field->isNone()) {
                $n = $field->isSingular() ? 1 : $faker->numberBetween($field->getMin(), $field->getMax());
                $samples = [];
                for ($i = 0; $i < $n; $i++) {
                    $samples[] = $this->sampleField($field);
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
     * @param Field $field
     * @return mixed
     * @throws SampleException
     */
    private function sampleField(Field $field)
    {
        $faker = $this->dogmatist->getFaker();
        switch ($field->getType()) {
            case Field::TYPE_FAKED:
                try {
                    return $faker->format($field->getFakedType(), $field->getFakedOptions());
                } catch (\Exception $e) {
                    throw new SampleException("Could not fake value of type {$field->getFakedType()}", 0, $e);
                }
                break;
            case Field::TYPE_SELECT:
                return $faker->randomElement($field->getSelection());
                break;
            case Field::TYPE_RELATION:
                return $this->sample($field->getRelated());
                break;
            case Field::TYPE_LINK:
                $samples = $this->dogmatist->getLinkManager()->samples($field->getLinkTarget());
                return $faker->randomElement($samples);
                break;
            default:
                throw new SampleException("Could not generate data for field of type {$field->getType()}");
        }
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
        if ($builder->getClass() === 'array') {
            return $data;
        }

        // special case for generic objects (objects of type stdClass): just cast as an object
        if ($builder->getClass() === 'object' || $builder->getClass() === 'stdClass') {
            return (object) $data;
        }

        $refl = new \ReflectionClass($builder->getClass());
        $constructor = $refl->getConstructor();
        if ($constructor === null || $constructor->getNumberOfRequiredParameters() === 0) {
            $obj = $refl->newInstance();
        } else {
            // TODO: allow user to define construction parameters
            $obj = $refl->newInstanceWithoutConstructor();
        }

        foreach ($data as $key => $val) {
            try {
                $this->accessor->setValue($obj, $key, $val);
            } catch (NoSuchPropertyException $e) {
                if ($this->dogmatist->isStrict()) {
                    throw new SampleException("Could not set value", 0, $e);
                } else {
                    $obj->{$key} = $val;
                }
            }
        }

        return $obj;
    }
}
