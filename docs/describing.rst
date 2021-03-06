Describing objects
==================
In Dogmatist, you describe objects using a simple fluent syntax. This
descriptive method results in a ``Builder`` object being created. Feeding this
``Builder`` into the sampler will allow Dogmatist to create an instance of that
object.

Starting a description
----------------------

To start an object description, use the ``create()`` method on the ``Dogmatist``
instance, like so for creating a description for an array:

.. code-block:: php

    // $dogmatist is an instance of ``Dogmatist``
    $builder = $dogmatist->create('array');

The first (and only) argument of the ``create()`` method specifies the type of
object that should be created. You can specify the type as follows:

* Using ``array``. This will not create an object, but a native PHP array
  instead.
* Using ``object`` (or ``stdClass``): this will cast the array as previously
  specified to an object, and thus will return an instance of ``stdClass``.
* Using a fully namespaced class name. Note that the class name needs to be
  loadable using some sort of autoloader and that it should be possible to
  create an instance. Dogmatist does not work with interfaces, traits or
  abstract classes, only concrete (or final) classes can be used.


.. note:: This documentation often uses the PHP 5.5+ syntax of ``::class``. This
          syntax allows you to get the full namespaced classname when all you
          have is an imported symbol name of some class. This prevents having to
          write down the class name as a string, which is error-prone and does
          not allow for easy refactoring.

Say we have a Doctrine2 ORM entity in our project called ``Acme\Entity\Person``
then we might for example use this to create a builder for that class:

.. code-block:: php

    use Acme\Entity\Person;

    $builder = $dogmatist->create(Person::class);

Builder options
---------------
A Builder instance has some methods to manipulate and retrieve its state:

``isStrict()``
    When a Builder is set to strict mode, then generating a field which doesn't
    exist in the target class, or cannot be reached because it is protected or
    private and has not accessor function, then the Builder will trigger an
    error. In non-strict mode, the Builder is allowed to use reflection to write
    any property to an object.

``setStrict(strictness)``
    Set the strictness (a boolean ``true`` or ``false``). Note that this also
    sets strictness of the child-builders and constructor builder.

``setParent(parent)``
    Sets the parent of this Builder. You don't need to call this method
    directly, as the Builder will automatically set up parent-child relations.

``constructor()``
    Used to retrieve the constructor Builder. You can read more about the
    constructor builder `in the section below <#constructor>`__.

``hasConstructor()``
    Returns whether or not the Builder actually has a constructor.

``get(field)``
    Retrieves a field with a specific name. More about setting up fields is
    described `in the section below <#fields>`__.

``done()``
    Returns the parent Builder for this one, indicating that the builder is
    complete. If a Builder has no parent, then the associated instance of
    Dogmatist is returned. This allows you to chain another call to ``create()``
    to start

``save(name[, count])``
    Saves the builder in dogmatist, to be used for linking or for generating
    samples directly from dogmatist using a named descriptor. The count
    parameter is used to indicate the maximum number of samples that should be
    generated. If this count is set to any number equal to or less than zero
    then an unlimited number of samples may be generated.

``getFields()``
    Retrieves the fields that have been defined for this builder. You can read
    more about fields `in the section below <#fields>`__.

``getType()``
    Retrieves the type name for which this Builder will generate samples.

``onCreate(callback)``
    Provide a callback function that will be called using a newly created
    sample. Note that you can call this method multiple times with different
    callback functions which will all be called when a new samples is created.

``getListeners()``
    Retrieve a list of callbacks that should be called when a new samples is
    created.

``copy([type])``
    Creates a deep copy of the builder, if the specified type is given, the
    type of the builder will be adjusted to the new type.

``setType(type)``
    Set a new type for the builder. The builder is then configured for
    constructing objects of that type. Note that you still have to make sure
    all fields are available.

.. _fields:

Describing fields
-----------------
Once a Builder instance is retrieved using the create method you have several
methods to describe the fields contained in that class. The methods available
are described below:

``none(field)``
    A field described using this method will ensure that the field will never
    be touched when sampling. By default all fields in an object won't be
    touched. So this method is mainly used to remove fields which have
    previously been added.

``fake(field, type[, options])``
    The fake method is used to describe a field in your object which should get
    some random value generated by Faker_. The type should be a type that is
    available in the ``Faker\Generator`` instance that Dogmatist has. You can
    use one of the default providers Faker_ provides, such as ``randomNumber``
    or ``country``, or you can create your own Providers and register them in
    the Generator instance to have more ways of specifying field values.

    Note that you can specify an array of options that should be passed on to
    the generator. If you were to call a faker method directly these would
    normally be the arguments entered here.

``select(field, options)``
    The select field allows you to specify that a random value from a list of
    predetermined values should be picked. Note that this is equivalent to using
    ``fake($field, 'randomElement', [$options])``, however since select fields
    may occur quite often (such as in the case of Male/Female or true/false)
    they have been given a special descriptive function.

``value(field, value)``
    Set a predetermined value for a specific field. This means that all samples
    of the object will always have the same value for a specific field. You can
    for example use this when describing something like an active user, where
    that active flag is indicated using a boolean which should always be true
    for active users.

``link(field, value)``
    You can link one builder to another builder using this method. For a better
    description you should take a look at the section on saved builders. Note
    that you can also specify an array of linked builders, in which case one
    will be selected randomly for each sample created.

``relation(field, type)``
    This is the most complicated of all the available functions. The relation
    type of description allows you to describe a sub-builder for that specific
    property. For example take some User class which contains an Address.

``callback(field, callback)``
    Calls the function callback with as the first arguments an array of all
    fields generated up to this point, and as a second argument the associated
    instance of Dogmatist. This callback function should return a value to be
    used at that position. Note that you can set a callback field to multiple
    (as seen below), in that case the callback will be called multiple times.
    Also note that you don't have access to any fields described after having
    described this one, as they still have to be generated.

The previous methods all allow you to describe the type of the field. Every
field can either generate an array of values or just a single value. By default
all fields will be singular, but using the following two methods you can change
this behavior:

``singe(field)``
    Sets a field to only produce a single value when a sample is generated.

``multiple(field[, min, max])``
    Sets a field to produce at least min and at most max values. These values
    are combined as an array. If an add method is provided however, the results
    will be inserted one item at a time into the field. If no such method
    exists then the value will be inserted directly.

Every field can be set to generate only unique values. By the default the
sampler will try a limited number of times to try and generate a unique value.
If that proves to be impossible within that limit, the sampler will fail to
generate a new value. To mark a field for uniqueness, you can use the following
method:

``unique(field[, uniqueness])``
    When called will mark a field for uniqueness if called with one argument,
    otherwise a boolean may be provided as the second argument indicating the
    uniqueness of the field.

For the ``single``, ``multiple`` and ``unique`` calls you will often want to
apply these functions to the field you have just created. In order to help you
with this use case you can use these methods in camel-cased variants prefixed
with ``with`` to access the previously created field:

``withSingle()``
    Mark the previously accessed field as singular.

``withMultiple([min, max])``
    Mark the previously accessed field as multiple with the specified min and
    max.

``withUnique([uniqueness]``
    Mark the field as being unique.

.. _constructor:

Describing the constructor
--------------------------
Using the ``constructor()`` method, you can create a description for
constructing the object. Inside this ``ConstructorBuilder`` you can mostly use
the same methods as with a normal ``Builder`` object. However, you cannot save
a constructor, nor can you add a constructor to a constructor recursively, and
finally you cannot add listeners using the ``onCreate()`` method.

When describing the constructor you can choose one of two methods:

**Named**
    Describe using the names of the constructor arguments. This is done using
    the same methods as describing fields in a normal Builder.
**Positional**
    Using positional arguments with the ``arg*`` methods: ``argFake()``,
    ``argSelect()``, ``argValue()``, ``argLink()``, ``argRelation()`` and
    ``argCallback()``. These methods have the same signature as their named
    counterparts, except that you can leave out the name of the field.

To determine if a constructor is using named or positional arguments, you can
use the ``isPositional()`` method on the constructor builder.

.. note:: You cannot mix positional and named arguments in the constructor. If
          you try to do this, you will get a ``BuilderException``.

.. _relations:

Relating back to the parent object
----------------------------------
When creating a sub-builder using the ``relation()`` method, you can specify a
field that should be updated with the parent object. To do this, use these
special builder methods:

``linkParent(field)``
    Will insert the parent object in the specified field.

``hasLinkWithParent()``
    Returns whether or not this builder wants to create a link to the parent.

``getLinkParent()``
    Retrieves the field into which the parent should be inserted.

.. note:: Inserting the parent also works with ``stdClass`` objects and arrays.

.. _Faker: https://github.com/fzaninotto/Faker
