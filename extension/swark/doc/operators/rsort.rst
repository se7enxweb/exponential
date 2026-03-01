rsort
-----

Summary
~~~~~~~
Sorts and returns the input array in reverse order.

Usage
~~~~~
::

    input|rsort

Note that this operator assigns new keys to the output array.


Parameters
~~~~~~~~~~
None.

Examples
~~~~~~~~
::

    {hash( 'a', 10, 'c', 2, 'd', 15, 'b', 8, 'e', 6 )|rsort}

Returns array( 15, 10, 8, 6, 2 ).
