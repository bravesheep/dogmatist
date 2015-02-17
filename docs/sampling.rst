Sampling
========
Once you have described your object you can use the sampler to create samples
for your object. To create samples you can use one of four methods inside the
``Dogmatist`` instance:

``sample(builder)``
    When builder is a name, a sample from the named builder is created. If no
    builder can be found then a ``NoSuchIndexException`` is thrown. When the
    named builder has a limited set of samples, then one of these samples is
    returned. If builder is a named builder that is allowed to generate
    unlimited samples, or if builder is an instance of ``Builder``, then a fresh
    sample is generated.

``samples(builder, count)``
    This method is similar in behavior to the ``sample()`` method, however this
    method returns a provided number of samples. Note that if a builder only has
    a limited set of samples it is allowed to generate and you ask for more,
    then the sampler will throw a ``SampleException`` indicating that not enough
    samples are available. For ``Builder`` instances or named builders that are
    allowed to generate unlimited samples, a sample will always be generated as
    long as the Builder is valid for the type it describes.

``freshSample(builder)``
    This method will always generate a fresh sample, even for builders which are
    only allowed to generate a limited set of samples.

``freshSamples(builder, count)``
    Similar to ``freshSample()``, this will always generate fresh samples.

.. note:: When a sample is fresh, this does not mean that it is unique from all
          previously generated samples when looked at the field level. However
          the sampler will have at least run it's course once more and in the
          case of objects, no freshly generated sample will ever be exactly
          equal (``===``) to any of the previously generated samples.

.. code-block:: php

    $builder = $dogmatist->create('object')->fake('num', 'randomNumber');
    $sample = $dogmatist->sample($builder);
    var_dump($sample);
