<?php

namespace Bravesheep\Dogmatist\Guesser;

use Bravesheep\Dogmatist\Builder;
use Bravesheep\Dogmatist\Field;
use Bravesheep\Dogmatist\Util;
use Symfony\Component\PropertyAccess\StringUtil;

class FieldNameGuesser implements GuesserInterface
{
    /**
     * {@inheritdoc}
     */
    public function fill(Builder $builder)
    {
        if (Util::isUserClass($builder->getClass())) {
            $refl = new \ReflectionClass($builder->getClass());
            $filter = \ReflectionProperty::IS_PRIVATE |
                \ReflectionProperty::IS_PUBLIC |
                \ReflectionProperty::IS_PROTECTED;
            foreach ($refl->getProperties($filter) as $property) {
                if ($this->isPropertyAccessible($property->getName(), $refl)) {
                    $this->makeField($property, $builder);
                }
            }
        }
    }

    /**
     * @param \ReflectionProperty $property
     * @param Builder             $builder
     */
    private function makeField(\ReflectionProperty $property, Builder $builder)
    {
        $field = $property->getName();
        $resolved = TypeResolver::resolve($field);

        if (false !== $resolved) {
            list($type, $multiple, $options) = $resolved;
            $this->addFieldOfType($field, $type, $multiple, $options, $builder);
        }
    }

    /**
     * @param string     $field
     * @param integer    $type
     * @param bool|array $multiple
     * @param array      $options
     * @param Builder    $builder
     */
    private function addFieldOfType($field, $type, $multiple, array $options, Builder $builder)
    {
        switch ($type) {
            case Field::TYPE_FAKE:
                $builder->fake($field, $options[0], isset($options[1]) ? $options[1] : []);
                break;
            case Field::TYPE_LINK:
                $builder->link($field, $options[0]);
                break;
            case Field::TYPE_RELATION:
                $builder->relation($field, $options[0]);
                break;
            case Field::TYPE_VALUE:
                $builder->value($field, $options[0]);
                break;
            case Field::TYPE_CALLBACK:
                $builder->callback($field, $options[0]);
                break;
            case Field::TYPE_NONE:
                $builder->none($field);
                break;
            case Field::TYPE_SELECT:
                $builder->select($field, $options[0]);
                break;
        }

        if (false === $multiple) {
            $builder->single($field);
        } else {
            $builder->multiple($field, $multiple[0], $multiple[1]);
        }
    }

    /**
     * Check whether or not a property is writable.
     *
     * Part of the Symfony package. Symfony license applies.
     *
     * @param string           $name
     * @param \ReflectionClass $class
     * @return bool
     */
    private function isPropertyAccessible($name, \ReflectionClass $class)
    {
        $camelized = $this->camelize($name);
        $setter = 'set'.$camelized;
        $getsetter = lcfirst($camelized); // jQuery style, e.g. read: last(), write: last($item)
        $classHasProperty = $class->hasProperty($name);

        if ($this->isMethodAccessible($class, $setter, 1)
            || $this->isMethodAccessible($class, $getsetter, 1)
            || $this->isMethodAccessible($class, '__set', 2)
            || ($classHasProperty && $class->getProperty($name)->isPublic())) {
            return true;
        }

        $singulars = (array) StringUtil::singularify($camelized);

        // Any of the two methods is required, but not yet known
        if (null !== $this->findAdderAndRemover($class, $singulars)) {
            return true;
        }

        return false;
    }

    /**
     * Searches for add and remove methods.
     *
     * Part of the Symfony package. Symfony license applies.
     *
     * @param \ReflectionClass $reflClass The reflection class for the given object
     * @param array            $singulars The singular form of the property name or null
     *
     * @return array|null An array containing the adder and remover when found, null otherwise
     */
    private function findAdderAndRemover(\ReflectionClass $reflClass, array $singulars)
    {
        foreach ($singulars as $singular) {
            $addMethod = 'add'.$singular;
            $removeMethod = 'remove'.$singular;

            $addMethodFound = $this->isMethodAccessible($reflClass, $addMethod, 1);
            $removeMethodFound = $this->isMethodAccessible($reflClass, $removeMethod, 1);

            if ($addMethodFound && $removeMethodFound) {
                return array($addMethod, $removeMethod);
            }
        }

        return null;
    }

    /**
     * Returns whether a method is public and has the number of required parameters.
     *
     * Part of the Symfony package. Symfony license applies.
     *
     * @param \ReflectionClass $class      The class of the method
     * @param string           $methodName The method name
     * @param int              $parameters The number of parameters
     *
     * @return bool Whether the method is public and has $parameters
     *              required parameters
     */
    private function isMethodAccessible(\ReflectionClass $class, $methodName, $parameters)
    {
        if ($class->hasMethod($methodName)) {
            $method = $class->getMethod($methodName);

            if ($method->isPublic()
                && $method->getNumberOfRequiredParameters() <= $parameters
                && $method->getNumberOfParameters() >= $parameters) {
                return true;
            }
        }

        return false;
    }

    /**
     * Camelizes a given string.
     *
     * Part of the Symfony package. Symfony license applies.
     *
     * @param string $string Some string
     *
     * @return string The camelized version of the string
     */
    private function camelize($string)
    {
        return strtr(ucwords(strtr($string, array('_' => ' '))), array(' ' => ''));
    }
}
