<?php

namespace Bravesheep\Dogmatist;
use Bravesheep\Dogmatist\Exception\BuilderException;

/**
 * Builds up the structure for generating samples.
 */
class Builder
{
    const DEFAULT_MIN = 0;
    const DEFAULT_MAX = 10;

    /**
     * @var string
     */
    private $type;

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
     * @var string|int
     */
    private $last_field;

    /**
     * @var string|int
     */
    private $link_parent;

    /**
     * @param string    $type
     * @param Dogmatist $dogmatist
     * @param Builder   $parent
     * @param bool      $strict
     */
    public function __construct($type, Dogmatist $dogmatist, Builder $parent = null, $strict = true)
    {
        $this->type = $type;
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
     * @param bool $new If true and a constructor already exists, it will be discarded and a new one will be created.
     * @return ConstructorBuilder
     */
    public function constructor($new = false)
    {
        if (null === $this->constr || $new === true) {
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
        if (!$this->has($field)) {
            $this->fields[$field] = new Field($field);
        }

        $this->last_field = $field;
        return $this->fields[$field];
    }

    /**
     * @param string|int $field
     * @return bool
     */
    public function has($field)
    {
        return isset($this->fields[$field]);
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
    public function multiple($field, $min = self::DEFAULT_MIN, $max = self::DEFAULT_MAX)
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
     * @param string|int     $field
     * @param string|Builder $builder
     * @return Builder
     */
    public function relationFromCopy($field, $builder)
    {
        $copy = $this->dogmatist->copy($builder);
        $copy->setStrict($this->isStrict());
        $copy->setParent($this);

        $field = $this->get($field);
        $field->setRelation($copy);
        return $copy;
    }

    /**
     * @param string|int $relation
     * @return Builder
     */
    public function in($relation)
    {
        if ($this->get($relation)->isType(Field::TYPE_RELATION)) {
            return $this->get($relation)->getRelated();
        }
        throw new BuilderException("The field {$relation} is not of type relation");
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
     * @param string|int   $field
     * @param string|array $target
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
     * @param string $field
     * @param bool   $unique
     * @return $this
     */
    public function unique($field, $unique = true)
    {
        $field = $this->get($field);
        $field->setUnique($unique);
        return $this;
    }

    /**
     * @param string|int $field
     * @return $this
     */
    public function linkParent($field)
    {
        if ($this->parent instanceof ConstructorBuilder) {
            throw new BuilderException("Cannot link to parent when the parent is a constructor");
        }

        if ($this->parent === null) {
            throw new BuilderException("There is no parent to link back to");
        }

        if ($this instanceof ConstructorBuilder) {
            throw new BuilderException("Cannot link back to the parent inside the constructor");
        }

        $this->link_parent = $field;

        return $this;
    }

    /**
     * @return string|int
     */
    public function getLinkParent()
    {
        return $this->link_parent;
    }

    /**
     * @return bool
     */
    public function hasLinkWithParent()
    {
        return $this->link_parent !== null;
    }

    /**
     * @param bool $unique
     * @return $this
     */
    public function withUnique($unique = true)
    {
        if ($this->has($this->last_field)) {
            $this->unique($this->last_field, $unique);
        }
        return $this;
    }

    /**
     * @return $this
     */
    public function withSingle()
    {
        if ($this->has($this->last_field)) {
            $this->single($this->last_field);
        }
        return $this;
    }

    /**
     * @param int $min
     * @param int $max
     * @return $this
     */
    public function withMultiple($min = self::DEFAULT_MIN, $max = self::DEFAULT_MAX)
    {
        if ($this->has($this->last_field)) {
            $this->multiple($this->last_field, $min, $max);
        }
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
     * If no name is given, the typename is used.
     * If a builder already exists with the given name, then this method fails. Either
     * use `save()` on your Dogmatist instance directly, or choose a different name.
     * @param string $name     The name under which the Builder should be stored.
     * @param int    $generate The number of samples to generate, if less than
     *                         or equal to zero, unlimited samples will be generated.
     * @return $this
     */
    public function save($name = null, $generate = -1)
    {
        if (null === $name) {
            $name = $this->getType();
        }

        if ($this->dogmatist->getLinkManager()->has($name)) {
            throw new BuilderException("A builder with the name '{$name}' already exists");
        }

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
     * @deprecated
     */
    public function getClass()
    {
        return $this->type;
    }

    /**
     * Retrieve the type of objects this builder should generate samples for.
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Set the type of objects this builder should generate samples for.
     * @param string $type
     * @return $this
     */
    public function setType($type)
    {
        $this->type = $type;

        return $this;
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

    /**
     * Returns a clone for the current builder
     * @param string $type The new type of the cloned builder.
     * @return $this
     */
    public function copy($type = null)
    {
        $builder = new Builder($this->type, $this->dogmatist, $this->parent, $this->strict);
        foreach ($this->fields as $field) {
            $new = clone $field;
            if ($new->isType(Field::TYPE_RELATION)) {

                $new->getRelated()->setParent($builder);
            }
            $builder->fields[$new->getName()] = $new;
        }

        $builder->listeners = $this->listeners;
        if ($this->constr !== null) {
            $builder->constr = clone $this->constr;
            $builder->constr->setParent($builder);
        }

        $builder->link_parent = $this->link_parent;

        if ($type !== null) {
            $builder->setType($type);
        }

        return $builder;
    }
}
