Examples
========
In this chapter are some example usages of Dogmatist, the Builder and how
to create samples.

Basic example
-------------

.. code-block:: php

    // start by creating a dogmatist instance
    $dogmatist = \Bravesheep\Dogmatist\Factory::create();

    // let's create two main builders: one for Person objects and one for
    // Post object. The Person object gets its own Address builder which will
    // generate specific instances per person
    $dogmatist
        ->create(Person::class)
            ->fake('name', 'name')
            ->fake('birthday', 'datetimeBetween', ['-100 years', '-15 years'])
            ->fake('company', 'company')
            ->relation('addresses', Address::class)
                ->fake('address', 'streetAddress')
                ->fake('postcode', 'postcode')
                ->fake('city', 'city')
                ->fake('country', 'country')
            ->done()
            ->multiple('addresses', 1, 5)
            ->select('gender', ['male', 'female'])
            ->save('person', 10)
        ->done()
        ->create(Post::class)
            ->link('poster', 'person')
            ->fake('title', 'sentence')
            ->fake('text', 'text', [5000])
            ->value('visible', true)
            ->save('post')
        ->done()
    ;

    // gets us one of the 10 persons that could be created
    $person = $dogmatist->sample('person');

    // gets us a random new post
    $post = $dogmatist->sample('post');

    try {
        // will throw an exception
        $people = $dogmatist->samples('person', 20);
    } catch (SampleException $e) {
        print $e->getMessage();
    }

    // this will generate an array of 20 Person objects
    $people = $dogmatist->freshSamples('person', 20);

Events
------

.. code-block:: php

    // start by creating a dogmatist instance
    $dogmatist = \Bravesheep\Dogmatist\Factory::create();

    // here we add a callback to append the slug, which has to base its value
    // on another faked value in the object
    $builder = $dogmatist
        ->create(Post::class)
            ->fake('title', 'person')
            ->fake('content', 'text', [5000])
            ->onCreate(function (&$value) {
                $obj->setSlug(Slugifier::slugize($obj->getTitle()));
            });

    $sample = $dogmatist->sample($builder);

    // so now we have a slug
    print $sample->getSlug();
