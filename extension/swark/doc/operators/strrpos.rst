strrpos
-------

Summary
~~~~~~~
Returns position of the last occurrence of the parameter in the input string.

Usage
~~~~~
::

    input|strrpos( needle [, offset] )

Parameters
~~~~~~~~~~
    =========== =============================================================== ======== =======
    Name        Description                                                     Required Default
    =========== =============================================================== ======== =======
    needle      Searched string                                                 Yes
    =========== =============================================================== ======== =======

Examples
~~~~~~~~
::

    {'Does the quick brown fox jumps over the lazy dog?'|strrpos( 'the' )}

Returns 36.
