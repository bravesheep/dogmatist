<?php

namespace Bravesheep\Dogmatist;
use Bravesheep\Dogmatist\Exception\BuilderException;

/**
 * Builds up the structure for generating samples.
 */
class Builder
{
    /**
     * @var string
     */
    private $class;

    /**
     * @var Dogmatist
     */
    private $dogmatist;

    /**
     * @var Builder|null
     */
    private $parent;

    /**
     * @var Field[]
     */
    private $fields = [];

    /**
     * @var callback[]
     */
    private $listeners = [];

    /**
     * @var bool
     */
    private $strict;

    /**
     * @var ConstructorBuilder|null
     */
    private $constr;

    /**
     * @param string    $class
     * @param Dogmatist $dogmatist
     * @param Builder   $parent
     * @param bool      $strict
     */
    public function __construct($class, Dogmatist $dogmatist, Builder $parent = null, $strict = true)
    {
        $this->class = $class;
        $this->dogmatist = $dogmatist;
        $this->parent = $parent;
        $this->strict = $strict;
    }

    /**
     * @param bool $strict
     * @return $this
     */
    public function setStrict($strict)
    {
        $this->strict = $strict;
        if (null !== $this->constr) {
            $this->constr->setStrict($strict);
        }

        foreach ($this->fields as $field) {
            if ($field->isType(Field::TYPE_RELATION)) {
                $field->getRelated()->setStrict($strict);
            }
        }
        return $this;
    }

    /**
     * @return bool
     */
    public function isStrict()
    {
        return $this->strict;
    }

    /**
     * @return ConstructorBuilder
     */
    public function constructor()
    {
        if (null === $this->constr) {
            $this->constr = new ConstructorBuilder($this->dogmatist, $this);
        }
        return $this->constr;
    }

    /**
     * @return bool
     */
    public function hasConstructor()
    {
        return null !== $this->constr;
    }

    /**
     * @param Builder $builder
     * @return $this
     */
    public function setParent(Builder $builder = null)
    {
        $this->parent = $builder;
        return $this;
    }

    /**
     * @param string|int $field
     */
    protected function checkState($field)
    {
        if (!is_string($field) && !is_int($field)) {
            $type = gettype($field);
            throw new BuilderException("Invalid field key type, must use integer or string, got {$type}");
        }
    }

    /**
     * @param string|int $field
     * @return Field
     */
    public function get($field)
    {
        $this->checkState($field);
        if (!isset($this->fields[$field])) {
            $this->fields[$field] = new Field($field);
        }

        return $this->fields[$field];
    }

    /**
     * @param string|int $field
     * @return $this
     */
    public function single($field)
    {
        $field = $this->get($field);
        $field->setSingular();
        return $this;
    }

    /**
     * @param string|int $field
     * @param int        $min
     * @param int        $max
     * @return $this
     */
    public function multiple($field, $min = 0, $max = 10)
    {
        $field = $this->get($field);
        $field->setMultiple($min, $max);
        return $this;
    }

    /**
     * Fake the contents of a field.
     * @param string|int      $field
     * @param string|callback $type
     * @param array           $options
     * @return $this
     */
    public function fake($field, $type, array $options = [])
    {
        $field = $this->get($field);
        $field->setFake($type, $options);
        return $this;
    }

    /**
     * @param string|int $field
     * @param string     $type
     * @return Builder
     */
    public function relation($field, $type)
    {
        $child = $this->dogmatist->create($type);
        $child->setStrict($this->isStrict());
        $child->setParent($this);

        $field = $this->get($field);
        $field->setRelation($child);
        return $child;
    }

    /**
     * @param string|int $field
     * @return $this
     */
    public function none($field)
    {
        $field = $this->get($field);
        $field->setNone();
        return $this;
    }

    /**
     * @param string|int $field
     * @param mixed      $value
     * @return $this
     */
    public function value($field, $value)
    {
        $field = $this->get($field);
        $field->setValue($value);
        return $this;
    }

    /**
     * @param string|int $field
     * @param array      $values
     * @return $this
     */
    public function select($field, array $values)
    {
        $field = $this->get($field);
        $field->setSelect($values);
        return $this;
    }

    /**
     * @param string|int $field
     * @param string     $target
     * @return $this
     */
    public function link($field, $target)
    {
        $field = $this->get($field);
        $field->setLink($target);
        return $this;
    }

    /**
     * @param string   $field
     * @param callback $callback
     * @return $this
     */
    public function callback($field, $callback)
    {
        $field = $this->get($field);
        $field->setCallback($callback);
        return $this;
    }

    /**
     * Return the parent Builder instance, or if there is no parent return the
     * attached Dogmatist instance.
     * @return Builder|Dogmatist
     */
    public function done()
    {
        if (null !== $this->parent) {
            return $this->parent;
        }
        return $this->dogmatist;
    }

    /**
     * Save this builder as a named builder in the attached Dogmatist instance.
     * @param string $name     The name under which the Builder should be stored.
     * @param int    $generate The number of samples to generate, if less than
     *                         or equal to zero, unlimited samples will be generated.
     * @return $this
     */
    public function save($name, $generate = -1)
    {
        $this->dogmatist->save($this, $name, $generate);
        return $this;
    }

    /**
     * Retrieve the fields that are described by this builder.
     * @return Field[]
     */
    public function getFields()
    {
        return $this->fields;
    }

    /**
     * Retrieve the type of objects this builder should generate samples for.
     * @return string
     */
    public function getClass()
    {
        return $this->class;
    }

    /**
     * Provide a callback function which should be called whenever a new sample
     * is created using this Builder.
     * @param callback $callback
     * @return $this
     */
    public function onCreate($callback)
    {
        $this->listeners[] = $callback;
        return $this;
    }

    /**
     * Retrieve the list of listeners which should be called when a new sample is
     * generated.
     * @return callback[]
     */
    public function getListeners()
    {
        return $this->listeners;
    }
}
