Getting started
===============
This document contains a quick introduction on how to get started with some very
basic code examples.

Requirements
------------
Dogmatist requires PHP_ 5.5 or higher. Generally for installing it you should
be using Composer_.

Installing
----------

Using Composer
~~~~~~~~~~~~~~
To install Dogmatist as a composer dependency of your project, use::

    composer require bravesheep/dogmatist=dev-master

To install Dogmatist as a development dependency, which is what you will most
likely be using Dogmatist for, you can use this command::

    composer require --dev bravesheep/dogmatist=dev-master

Note that this installs the master branch variant of Dogmatist. If you prefer
to use a stable release, you can choose to omit the ``=dev-master`` part of the
command which should ensure that you get the latest stable release.

Cloning from git
~~~~~~~~~~~~~~~~
You can also clone and install the project from git using::

    git clone https://github.com/bravesheep/dogmatist.git
    cd dogmatist
    composer install

.. _Composer: http://getcomposer.org
.. _PHP: http://php.net

Constructing dogmatist
----------------------
In order to work with Dogmatist, you will need an instance of the base Dogmatist
generator class. You can either manually construct an instance, or (preferred)
use the ``Factory`` class provided to create an instance. When you're using
composer, also make sure you have included the autoloader, so all classes get
automatically loaded from that point on:

.. code-block:: php

    <?php

    require "vendor/autoload.php";

    $dogmatist = \Bravesheep\Dogmatist\Factory::create();

.. note:: Future code fragments will omit things like the PHP open tag and
          ``require`` statement. In general ``$dogmatist`` will be an instance
          of ``Dogmatist`` created using this factory method.

The ``Factory::create()`` method may be provided with an optional set of
arguments. Three things can be provided by the programmer:

1. An instance of ``Faker\Generator`` or a string specifying the language of
   the generator which should be constructed.
2. An instance of ``Bravesheep\Dogmatist\Filler\FillerInterface``. A filler can
   be used to automatically determine what should be generated.
3. An instance of ``Symfony\Component\PropertyAccess\PropertyAccessorInterface``
   for setting properties. One will be constructed for you if you don't provide
   any, however this may be used to provide an already existing instance.

So a Dogmatist instance that would be using French providers, could be
constructed using this for example:

.. code-block:: php

    $dogmatist = \Bravesheep\Dogmatist\Factory::create('fr_FR');

Now that we have a dogmatist example we can start generating builders and
samples from them:

.. code-block:: php

    $dogmatist = \Bravesheep\Dogmatist\Factory::create();
    $dogmatist
        ->create('object')
            ->fake('name', 'name')
            ->value('count', 1)
            ->relation('address', 'object')
                ->fake('address', 'streetAddress')
                ->fake('city', 'city')
            ->done()
            ->multiple('address', 1, 3)
            ->save('person', 5)
        ->done()
    ;
    $persons = $dogmatist->freshSamples('person', 10);


Now that you've seen an example take a look at the rest of the documentation to
learn about the interface that Dogmatist provides for generating objects and
entities.
