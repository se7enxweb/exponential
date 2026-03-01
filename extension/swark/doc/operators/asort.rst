asort
-----

Summary
~~~~~~~
Sorts and returns the input array, maintaining index associations.

Usage
~~~~~
::

    input|asort

Parameters
~~~~~~~~~~
None.

Examples
~~~~~~~~
::

    {hash( 'a', 10, 'c', 2, 'd', 15, 'b', 8, 'e', 6 )|asort}

Returns array( 'c' => 2, 'e' => 6, 'b' => 8, 'a' => 10, 'd' => 15 ).
