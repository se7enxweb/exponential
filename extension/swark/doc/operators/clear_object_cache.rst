clear_object_cache
------------------

Summary
~~~~~~~
Clears the content objects memory cache.

Usage
~~~~~
::

    clear_object_cache()

Parameters
~~~~~~~~~~
None.

Examples
~~~~~~~~
::

    {clear_object_cache()}

Using this operator might be useful when processing huge number of content objects. Instead of fetching
all objects at once and risking to exhaust all available memory, use a cycle to fetch a small slice of
objects and to process them. Clear the memory cache using the operator before continuing with a next
iteration.
