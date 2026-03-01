set_array_element
-----------------

Summary
~~~~~~~
Sets an array element in the input array and returns the modified array.

Usage
~~~~~
::

    input|set_array_element( key, value )

Parameters
~~~~~~~~~~
    =========== =============================================================== ======== =======
    Name        Description                                                     Required Default
    =========== =============================================================== ======== =======
    key         Key of the array element to set                                 Yes
    value       Value to assign                                                 Yes
    =========== =============================================================== ======== =======

Examples
~~~~~~~~
::

    {hash( 'a', 10, 'c', 2, 'd', 15, 'b', 8, 'e', 6 )|set_array_element( 'c', 0 )}

Returns array( 'a' => 10, 'c' => 0, 'd' => 15, 'b' => 8, 'e' => 6 ).
::

    {hash( 'a', 10, 'c', 2, 'd', 15, 'b', 8, 'e', 6 )|set_array_element( 'f', 12 )}

Returns array( 'a' => 10, 'c' => 2, 'd' => 15, 'b' => 8, 'e' => 6, 'f' => 12 ).
