arsort
------

Summary
~~~~~~~
Sorts and returns the input array in reverse order, maintaining index associations.

Usage
~~~~~
::

    input|arsort

Parameters
~~~~~~~~~~
None.

Examples
~~~~~~~~
::

    {hash( 'a', 10, 'c', 2, 'd', 15, 'b', 8, 'e', 6 )|arsort}

Returns array( 'd' => 15, 'a' => 10, 'b' => 8, 'e' => 6, 'c' => 2 ).
