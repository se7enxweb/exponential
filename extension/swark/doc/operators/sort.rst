sort
----

Summary
~~~~~~~
Sorts and returns the input array.

Usage
~~~~~
::

    input|sort

Note that this operator assigns new keys to the output array.

Parameters
~~~~~~~~~~
None.

Examples
~~~~~~~~
::

    {hash( 'a', 10, 'c', 2, 'd', 15, 'b', 8, 'e', 6 )|sort}

Returns array( 2, 6, 8, 10, 15 ).
