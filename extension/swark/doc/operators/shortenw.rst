shortenw
--------

Summary
~~~~~~~
Returns a shortened version of the input string (without breaking words).

Note that 'w' at the end of the operator name stands for word.

Usage
~~~~~
::

    input|shortenw( length [, ellipsis] )

Parameters
~~~~~~~~~~
    =========== =============================================================== ======== =======
    Name        Description                                                     Required Default
    =========== =============================================================== ======== =======
    length      Maximum length of the returned string (including ellipsis)      Yes
    ellipsis    Ellipsis                                                        No       ...
    =========== =============================================================== ======== =======

Examples
~~~~~~~~
::

    {'The quick brown fox jumps over the lazy dog'|shortenw( 16 )}

Returns 'The quick...'.
::

    {'The quick brown fox jumps over the lazy dog'|shortenw( 16, '-' )}

Returns 'The quick brown-'.
::

    {'Textwithnospaces'|shortenw( 10 )}

Returns 'Textwit...'.
