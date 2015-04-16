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

    /**
     * @var int
     */
    private $unique_tries;

    /**
     * @var array
     */
    private $sampled;

    public function __construct(Dogmatist $dogmatist, PropertyAccessorInterface $accessor, $unique_tries = 128)
    {
        $this->dogmatist = $dogmatist;
        $this->accessor = $accessor;
        $this->unique_tries = $unique_tries;
        $this->sampled = [];
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
        $data = new UniqueArrayObject();
        $faker = $this->dogmatist->getFaker();
        foreach ($builder->getFields() as $field) {
            if (!$field->isType(Field::TYPE_NONE)) {
                $generate = $field->isSingular() ? 1 : $faker->numberBetween($field->getMin(), $field->getMax());
                $samples = [];
                $mark = false;

                for ($i = 0; $i < $generate; $i++) {
                    if ($field->isUnique()) {
                        $val = $this->sampleUniqueField($field, $data, $builder);
                    } else {
                        $val = $this->sampleField($field, $data);
                    }

                    if ($val instanceof ReplacableLink) {
                        $mark = true;
                    }
                    $samples[] = $val;
                }

                if ($field->isSingular()) {
                    $samples = $samples[0];
                } elseif ($mark) {
                    $samples = new ReplacableArray($samples);
                }

                $data[$field->getName()] = $samples;
            }
        }

        if ($builder instanceof ConstructorBuilder) {
            return $data->getArrayCopy();
        } else {
            $result = $this->insertInObject($data->getArrayCopy(), $builder);
            foreach ($builder->getListeners() as $listener) {
                call_user_func($listener, $result);
            }

            return $result;
        }
    }

    /**
     * @param Field             $field
     * @param UniqueArrayObject $data
     * @param Builder           $builder
     * @return mixed
     * @throws SampleException
     */
    private function sampleUniqueField(Field $field, UniqueArrayObject $data, Builder $builder)
    {
        // create a generation store for unique values
        if ($field->isType(Field::TYPE_LINK)) {
            // For links we only want uniqueness within the current object
            // For unique relations with other objects across all samples a relation should be used.
            $id = spl_object_hash($field) . $data->getId();
        } else {
            $id = spl_object_hash($field);
        }

        if (!isset($this->sampled[$id])) {
            $this->sampled[$id] = [];
        }

        // try to iteratively generate a unique value
        $rounds = 0;
        do {
            if ($rounds === $this->unique_tries) {
                $name = $field->getName();
                $type = $builder->getType();
                throw new SampleException(
                    "Tried to get unique value for field {$name} in {$type}, but none could be generated"
                );
            }

            $value = $this->sampleField($field, $data);
            $rounds += 1;
        } while (in_array($value, $this->sampled[$id], true));

        // store the generated value for later testing
        $this->sampled[$id][] = $value;

        return $value;
    }

    /**
     * @param Field             $field
     * @param UniqueArrayObject $data
     * @return mixed
     * @throws SampleException
     */
    private function sampleField(Field $field, UniqueArrayObject $data)
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
            $related = $field->getRelated();
            $value = $this->sample($related);
            if ($related->hasLinkWithParent()) {
                $value = new ReplacableLink($value, $related->getLinkParent());
            }
        } elseif (Field::TYPE_LINK === $type) {
            $target = $field->getLinkTarget();
            if (is_array($target)) {
                $target = $faker->randomElement($target);
            }

            if ($this->dogmatist->getLinkManager()->hasUnlimitedSamples($target)) {
                $value = $this->dogmatist->sample($target);
            } else {
                $samples = $this->dogmatist->getLinkManager()->samples($target);
                $value = $faker->randomElement($samples);
            }
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
            return $this->replaceLinksInArray($data, $builder);
        }

        // special case for generic objects (objects of type stdClass): just cast as an object
        if ($builder->getClass() === 'object' || $builder->getClass() === 'stdClass') {
            return $this->replaceLinksInObject((object) $data, $builder);
        }

        $refl = new \ReflectionClass($builder->getClass());
        $obj = $this->constructObject($refl, $builder);

        foreach ($data as $key => $val) {
            $this->setObjectProperty($obj, $key, $val, $builder);
        }

        return $obj;
    }

    /**
     * @param array   $data
     * @param Builder $builder
     * @return array
     * @throws SampleException
     */
    private function replaceLinksInArray(array $data, Builder $builder)
    {
        foreach ($data as $key => &$value) {
            if ($value instanceof ReplacableArray) {
                foreach ($value->data as &$subval) {
                    $subval = $this->setObjectProperty($subval->value, $subval->field, $data, $builder);
                }
                $value = $value->data;
            }

            if ($value instanceof ReplacableLink) {
                $value = $this->setObjectProperty($value->value, $value->field, $data, $builder);
            }
        }
        return $data;
    }

    /**
     * @param object  $obj
     * @param Builder $builder
     * @return object
     * @throws SampleException
     */
    private function replaceLinksInObject($obj, Builder $builder)
    {
        foreach (get_object_vars($obj) as $key => $value) {
            if ($value instanceof ReplacableArray) {
                foreach ($value->data as &$subval) {
                    $subval = $this->setObjectProperty($subval->value, $subval->field, $obj, $builder);
                }
                $value = $value->data;
                $obj->$key = $value;
            }

            if ($value instanceof ReplacableLink) {
                $obj->$key = $this->setObjectProperty($value->value, $value->field, $obj, $builder);
            }
        }
        return $obj;
    }

    /**
     * @param object|array     $obj
     * @param string|int       $key
     * @param mixed            $value
     * @param \ReflectionClass $refl
     * @param Builder          $builder
     * @return object|array
     * @throws SampleException
     */
    private function setObjectProperty($obj, $key, $value, Builder $builder)
    {
        if ($value instanceof ReplacableArray) {
            foreach ($value->data as &$subval) {
                $subval = $this->setObjectProperty($subval->value, $subval->field, $obj, $builder);
            }
            $value = $value->data;
        }

        if ($value instanceof ReplacableLink) {
            $value = $this->setObjectProperty($value->value, $value->field, $obj, $builder);
        }

        try {
            $this->accessor->setValue($obj, $key, $value);
        } catch (NoSuchPropertyException $e) {
            if (is_array($obj)) {
                $obj[$key] = $value;
            } elseif ($obj instanceof \stdClass) {
                $obj->$key = $value;
            } elseif ($builder->isStrict()) {
                $type = get_class($obj);
                throw new SampleException("Could not set value for '{$key}' in object of type '{$type}'", 0, $e);
            } else {
                $refl = new \ReflectionClass($builder->getClass());
                if ($refl->hasProperty($key)) {
                    $prop = $refl->getProperty($key);
                    $prop->setAccessible(true);
                    $prop->setValue($obj, $value);
                    $prop->setAccessible(false);
                } else {
                    $obj->{$key} = $value;
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
            $aligned = $data;
        } else {
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
        }

        if (count($aligned) < $constructor->getNumberOfRequiredParameters()) {
            throw new SampleException("Not enough arguments provided for constructing the object");
        }

        return $aligned;
    }
}
