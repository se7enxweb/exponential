krsort
------

Summary
~~~~~~~
Sorts and returns the input array by key in reverse order.

Usage
~~~~~
::

    input|krsort

Parameters
~~~~~~~~~~
None.

Examples
~~~~~~~~
::

    {hash( 'a', 10, 'c', 2, 'd', 15, 'b', 8, 'e', 6 )|krsort}

Returns array( 'e' => 6, 'd' => 15, 'c' => 2, 'b' => 8, 'a' => 10 ).
