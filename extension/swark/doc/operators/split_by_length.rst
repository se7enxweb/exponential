split_by_length
---------------

Summary
~~~~~~~
Converts the input string to an array.

Usage
~~~~~
::

    input|split_by_length( [ length ] )

Parameters
~~~~~~~~~~
    =========== =============================================================== ======== =======
    Name        Description                                                     Required Default
    =========== =============================================================== ======== =======
    length      Chunk length or lengths (if array)                              No       1
    =========== =============================================================== ======== =======

Examples
~~~~~~~~
::

    {'abcdef'|split_by_length}

Returns array( 'a', 'b', 'c', 'd', 'e', 'f' ).
::

    {'abcdef'|split_by_length( 2 )}

Returns array( 'ab', 'cd', 'ef' ).
::

    {'abcdef'|split_by_length( array( 1, 2, 3 ) )}

Returns array( 'a', 'bc', 'def' ).
::

    {'abcdefgh'|split_by_length( array( 1, 2 ) )}

Returns array( 'a', 'bc', 'd', 'ef', 'g', 'h' ).
