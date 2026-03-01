remove_array_element
--------------------

Summary
~~~~~~~
Removes an array element in the input array and returns modified array.

Usage
~~~~~
::

    input|remove_array_element( key )

Parameters
~~~~~~~~~~
    =========== =============================================================== ======== =======
    Name        Description                                                     Required Default
    =========== =============================================================== ======== =======
    key         Key of the array element to remove                              Yes
    =========== =============================================================== ======== =======

Examples
~~~~~~~~
::

    {hash( 'a', 10, 'c', 2, 'd', 15, 'b', 8, 'e', 6 )|remove_array_element( 'c' )}

Returns array( 'a' => 10, 'd' => 15, 'b' => 8, 'e' => 6 ).
