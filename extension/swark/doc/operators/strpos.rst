strpos
------

Summary
~~~~~~~
Returns position of the first occurrence of the parameter in the input string.

Usage
~~~~~
::

    input|strpos( needle [, offset] )

Parameters
~~~~~~~~~~
    =========== =============================================================== ======== =======
    Name        Description                                                     Required Default
    =========== =============================================================== ======== =======
    needle      Searched string                                                 Yes
    offset      Allows to specify at which character in the input to start      No       0
                the search
    =========== =============================================================== ======== =======

Examples
~~~~~~~~
::

    {'The quick brown fox jumps over the lazy dog'|strpos( 'brown' )}

Returns 10.
::

    {'Does the quick brown fox jumps over the lazy dog?'|strpos( 'the', 6 )}

Returns 36.
