<?php

namespace Bravesheep\Dogmatist;

use Bravesheep\Dogmatist\Exception\BuilderException;

class ConstructorBuilder extends Builder
{
    const CONSTRUCTOR_TYPE = '__construct';

    private $positional = false;

    /**
     * @param Dogmatist $dogmatist
     * @param Builder   $object
     */
    public function __construct(Dogmatist $dogmatist, Builder $object)
    {
        parent::__construct(self::CONSTRUCTOR_TYPE, $dogmatist, $object, $object->isStrict());
    }

    /**
     * @param string|int $field
     *
     */
    protected function checkState($field)
    {
        if (is_int($field)) {
            $this->preparePositional();
        }

        if (count($this->getFields()) > 0) {
            if (is_string($field) && $this->isPositional()) {
                throw new BuilderException("Cannot add named argument to positional arguments");
            } else {
                parent::checkState($field);
            }
        }
    }

    /**
     * @return int
     */
    private function nextPositional()
    {
        return count($this->getFields());
    }

    /**
     * @throws BuilderException
     */
    private function preparePositional()
    {
        if (!$this->isPositional() && count($this->getFields()) > 0) {
            throw new BuilderException("Cannot add positional arguments to named arguments");
        }
        $this->positional = true;
    }

    /**
     * @param string $type
     * @param array  $options
     * @return $this
     * @throws BuilderException
     */
    public function argFake($type, array $options = [])
    {
        return $this->fake($this->nextPositional(), $type, $options);
    }

    /**
     * @param array $options
     * @return $this
     * @throws BuilderException
     */
    public function argSelect(array $options)
    {
        return $this->select($this->nextPositional(), $options);
    }

    /**
     * @param mixed $value
     * @return $this
     * @throws BuilderException
     */
    public function argValue($value)
    {
        return $this->value($this->nextPositional(), $value);
    }

    /**
     * @param string $type
     * @return Builder
     * @throws BuilderException
     */
    public function argRelation($type)
    {
        return $this->relation($this->nextPositional(), $type);
    }

    /**
     * @param string $target
     * @return $this
     * @throws BuilderException
     */
    public function argLink($target)
    {
        return $this->link($this->nextPositional(), $target);
    }

    /**
     * @param callback $callback
     * @return $this
     */
    public function argCallback($callback)
    {
        return $this->callback($this->nextPositional(), $callback);
    }

    /**
     * {@inheritdoc}
     */
    public function constructor($new = false)
    {
        throw new BuilderException("Cannot create constructor for constructor");
    }

    /**
     * {@inheritdoc}
     */
    public function save($name = null, $generate = 1)
    {
        throw new BuilderException("Cannot save constructor builder");
    }

    /**
     * {@inheritdoc}
     */
    public function onCreate($callback)
    {
        throw new BuilderException("Create events will only be called on the constructed object");
    }

    /**
     * @return bool
     */
    public function isPositional()
    {
        return $this->positional;
    }

    /**
     * {@inheritdoc}
     */
    public function copy($type = null)
    {
        if ($type !== null && $type !== self::CONSTRUCTOR_TYPE) {
            throw new BuilderException("Cannot change the type of a constructor builder");
        }

        $builder = new ConstructorBuilder($this->dogmatist, $this->parent);
        $this->copyData($builder);

        return $builder;
    }
}
