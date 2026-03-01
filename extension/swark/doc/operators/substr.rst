substr
------

Summary
~~~~~~~
Returns part of the input string.

Usage
~~~~~
::

    input|substr( start [, length] )
    

Parameters
~~~~~~~~~~
    =========== =============================================================== ======== =======
    Name        Description                                                     Required Default
    =========== =============================================================== ======== =======
    start       Position in the input where to start the extraction. If         Yes
                negative, the position will be counted from the end of the
                input string.
    length      Number of characters to extract, if 0 or not specified,         No       0
                the rest of the string is returned. If negative, it is
                the number of characters that will be omitted from the
                end of the input string.
    =========== =============================================================== ======== =======

Examples
~~~~~~~~
::

    {'The quick brown fox jumps over the lazy dog'|substr( 4 )}

Returns 'quick brown fox jumps over the lazy dog'.
::

    {'The quick brown fox jumps over the lazy dog'|substr( 4, 11 )}

Returns 'quick brown'.
::

    {'The quick brown fox jumps over the lazy dog'|substr( 4, -3 )}

Returns 'quick brown fox jumps over the lazy'.
::

    {'The quick brown fox jumps over the lazy dog'|substr( -8 )}

Returns 'lazy dog'.
::

    {'The quick brown fox jumps over the lazy dog'|substr( -8, 4 )}

Returns 'lazy'.
