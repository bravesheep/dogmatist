<?php

namespace Bravesheep\Dogmatist;

class Field 
{
    /**
     * Ignore this field.
     */
    const TYPE_NONE = 1;

    /**
     * Generate a value using faker.
     */
    const TYPE_FAKE = 2;

    /**
     * Create a relation to another builder.
     */
    const TYPE_RELATION = 4;

    /**
     * Select a value from a predetermined list of options.
     */
    const TYPE_SELECT = 8;

    /**
     * Link to one of the other generated values from another builder.
     */
    const TYPE_LINK = 16;

    /**
     * Select a value from a predetermined list, where the list only has one option.
     */
    const TYPE_VALUE = 32;

    /**
     * Generate a value by asking for one from a callback function.
     */
    const TYPE_CALLBACK = 64;

    /**
     * The name of the field.
     * @var string|int
     */
    private $name;

    /**
     * Type of the field (one of the TYPE_ constants).
     * @var int
     */
    private $type = self::TYPE_NONE;

    /**
     * Whether or not this field should generate multiple values (i.e. an array)
     * @var bool
     */
    private $multiple;

    /**
     * If the field is faked, the type faker should use.
     * Can also take a callback which will be provided with the faker instance used.
     * @var string|callback
     */
    private $faked_type;

    /**
     * If the field is faked, extra options for faking the field.
     * @var string
     */
    private $faked_options;

    /**
     * The target stored inside the linkmanager from which the value should be copied.
     * @see LinkManager
     * @var string|array
     */
    private $link_target;

    /**
     * The minimum number of items created (if the field will return multiple values).
     * @var int
     */
    private $min;

    /**
     * The maximum number of items created (if the field will return multiple values).
     * @var int
     */
    private $max;

    /**
     * The builder that is related to this field (if the type is relation).
     * @var Builder
     */
    private $related;

    /**
     * The selection of possible values if this field is set to select.
     * @var array
     */
    private $selection;

    /**
     * @var callback
     */
    private $callback;

    /**
     * @var bool
     */
    private $unique;

    /**
     * @param string|int $name
     */
    public function __construct($name)
    {
        $this->name = $name;
    }

    /**
     * @return string|int
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @return int
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @return bool
     */
    public function isMultiple()
    {
        return $this->multiple;
    }

    /**
     * @return bool
     */
    public function isSingular()
    {
        return !$this->isMultiple();
    }

    /**
     * @return string|callback
     */
    public function getFakedType()
    {
        return $this->faked_type;
    }

    /**
     * @return string
     */
    public function getFakedOptions()
    {
        return $this->faked_options;
    }

    /**
     * @return int
     */
    public function getMin()
    {
        return $this->min;
    }

    /**
     * @return int
     */
    public function getMax()
    {
        return $this->max;
    }

    /**
     * @return Builder
     */
    public function getRelated()
    {
        return $this->related;
    }

    /**
     * @return array
     */
    public function getSelection()
    {
        return $this->selection;
    }

    /**
     * @return string|array
     */
    public function getLinkTarget()
    {
        return $this->link_target;
    }

    /**
     * @return callback
     */
    public function getCallback()
    {
        return $this->callback;
    }

    /**
     * @param int $type
     * @return bool
     */
    public function isType($type)
    {
        return $this->type === $type;
    }

    /**
     * @return $this
     */
    public function setNone()
    {
        $this->type = self::TYPE_NONE;
        return $this;
    }

    /**
     * @param string|callback $type
     * @param array           $options
     * @return $this
     */
    public function setFake($type, array $options = [])
    {
        $this->type = self::TYPE_FAKE;
        $this->faked_type = $type;
        $this->faked_options = $options;
        return $this;
    }

    /**
     * @param Builder $builder
     * @return $this
     */
    public function setRelation(Builder $builder)
    {
        $this->type = self::TYPE_RELATION;
        $this->related = $builder;
        return $this;
    }

    /**
     * @param array $values
     * @return $this
     */
    public function setSelect(array $values)
    {
        $this->type = self::TYPE_SELECT;
        $this->selection = $values;
        return $this;
    }

    /**
     * @param mixed $value
     * @return $this
     */
    public function setValue($value)
    {
        $this->type = self::TYPE_VALUE;
        $this->selection = [$value];
        return $this;
    }

    /**
     * @param string|array $target
     * @return $this
     */
    public function setLink($target)
    {
        $this->type = self::TYPE_LINK;
        $this->link_target = $target;
        return $this;
    }

    /**
     * @param callback $callback
     * @return $this
     */
    public function setCallback($callback)
    {
        $this->type = self::TYPE_CALLBACK;
        $this->callback = $callback;
        return $this;
    }

    /**
     * @return $this
     */
    public function setSingular()
    {
        $this->multiple = false;
        $this->min = 1;
        $this->max = 1;
        return $this;
    }

    /**
     * @param int $min
     * @param int $max
     * @return $this
     */
    public function setMultiple($min, $max)
    {
        $this->multiple = true;
        $this->min = $min;
        $this->max = $max;
        return $this;
    }

    /**
     * @param bool $unique
     * @return $this
     */
    public function setUnique($unique = true)
    {
        $this->unique = $unique;
        return $this;
    }

    /**
     * @return bool
     */
    public function isUnique()
    {
        return $this->unique;
    }

    /**
     * @return Field
     */
    public function copy()
    {
        $field = new Field($this->name);
        $field->type = $this->type;
        $field->multiple = $this->multiple;
        $field->faked_type = $this->faked_type;
        $field->faked_options = $this->faked_options;
        $field->link_target = $this->link_target;
        $field->min = $this->min;
        $field->max = $this->max;
        if ($this->related !== null) {
            $field->related = $this->related->copy();
        }
        $field->selection = $this->selection;
        $field->callback = $this->callback;
        $field->unique = $this->unique;

        return $field;
    }
}
