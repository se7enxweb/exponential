preg_match
----------

Summary
~~~~~~~
Returns number of matches of a given (Perl-style) regular expression in the input string.

Usage
~~~~~
::

    input|preg_match( pattern )

Parameters
~~~~~~~~~~
    =========== =============================================================== ======== =======
    Name        Description                                                     Required Default
    =========== =============================================================== ======== =======
    pattern     Regular expression to search for                                Yes
    =========== =============================================================== ======== =======

Examples
~~~~~~~~
::

    {'The quick brown fox jumps over the lazy dog'|preg_match( '/the/i' )}

Returns 2.
