<?php

namespace Bravesheep\Dogmatist;

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
     * @param string    $class
     * @param Dogmatist $dogmatist
     * @param Builder   $parent
     */
    public function __construct($class, Dogmatist $dogmatist, Builder $parent = null)
    {
        $this->class = $class;
        $this->dogmatist = $dogmatist;
        $this->parent = $parent;
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
     * @param string $field
     * @return Field
     */
    private function getField($field)
    {
        if (!isset($this->fields[$field])) {
            $this->fields[$field] = new Field($field);
        }

        return $this->fields[$field];
    }

    /**
     * @param string $field
     * @return $this
     */
    public function single($field)
    {
        $field = $this->getField($field);
        $field->setSingular();
        return $this;
    }

    /**
     * @param string $field
     * @param int    $min
     * @param int    $max
     * @return $this
     */
    public function multiple($field, $min = 0, $max = 10)
    {
        $field = $this->getField($field);
        $field->setMultiple($min, $max);
        return $this;
    }

    /**
     * Fake the contents of a field.
     * @param string $field
     * @param string $type
     * @param array  $options
     * @return $this
     */
    public function faked($field, $type, array $options = [])
    {
        $field = $this->getField($field);
        $field->setFaked($type, $options);
        return $this;
    }

    /**
     * @param string $field
     * @param string $type
     * @return Builder
     */
    public function relation($field, $type)
    {
        $child = $this->dogmatist->create($type);
        $child->setParent($this);

        $field = $this->getField($field);
        $field->setRelation($child);
        return $child;
    }

    /**
     * @param string $field
     * @return $this
     */
    public function none($field)
    {
        $field = $this->getField($field);
        $field->setNone();
        return $this;
    }

    /**
     * @param string $field
     * @param mixed  $value
     * @return $this
     */
    public function value($field, $value)
    {
        $this->select($field, [$value]);
        return $this;
    }

    /**
     * @param string $field
     * @param array  $values
     * @return $this
     */
    public function select($field, array $values)
    {
        $field = $this->getField($field);
        $field->setSelect($values);
        return $this;
    }

    /**
     * @param string $field
     * @param string $target
     * @return $this
     */
    public function link($field, $target)
    {
        $field = $this->getField($field);
        $field->setLink($target);
        return $this;
    }

    /**
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
     * @param string $name
     * @param int    $generate
     * @return $this
     */
    public function save($name, $generate = 1)
    {
        $this->dogmatist->save($this, $name, $generate);
        return $this;
    }

    /**
     * @return Field[]
     */
    public function getFields()
    {
        return $this->fields;
    }

    /**
     * @return string
     */
    public function getClass()
    {
        return $this->class;
    }

    /**
     * @param callback $callback
     * @return $this
     */
    public function onCreate($callback)
    {
        $this->listeners[] = $callback;
        return $this;
    }

    /**
     * @return callback[]
     */
    public function getListeners()
    {
        return $this->listeners;
    }
}
