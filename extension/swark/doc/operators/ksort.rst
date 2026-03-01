ksort
-----

Summary
~~~~~~~
Sorts and returns the input array by key.

Usage
~~~~~
::

    input|ksort

Parameters
~~~~~~~~~~
None.

Examples
~~~~~~~~
::

    {hash( 'a', 10, 'c', 2, 'd', 15, 'b', 8, 'e', 6 )|ksort}

Returns array( 'a' => 10, 'b' => 8, 'c' => 2, 'd' => 15, 'e' => 6 ).
