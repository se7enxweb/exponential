array_search
------------

Summary
~~~~~~~
Searches the input array for a given value and returns the corresponding key.

Usage
~~~~~
::

    input|array_search( search [, not_found_key] )

Parameters
~~~~~~~~~~
    ============= =============================================================== ======== =======
    Name          Description                                                     Required Default
    ============= =============================================================== ======== =======
    search        Searched value                                                  Yes
    not_found_key Value to return in the case the searched value is not found     No       -1
    ============= =============================================================== ======== =======

Examples
~~~~~~~~
::

    {array( 'apple', 'pear', 'pineapple', 'orange' )|array_search( 'pear' )}

Returns 1.
::

    {array( 'apple', 'pear', 'pineapple', 'orange' )|array_search( 'carrot' )}

Returns -1.
::

    {array( 'apple', 'pear', 'pineapple', 'orange' )|array_search( 'carrot', 'not_found' )}

Returns 'not_found'.

::

    {hash( 'apple', 'red',
           'pear', 'green',
           'pineapple', 'yellow',
           'orange', 'orange' )|array_search( 'yellow' )}

Returns 'pineapple'.
