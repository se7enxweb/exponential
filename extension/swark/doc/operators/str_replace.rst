str_replace
-----------

Summary
~~~~~~~
Replaces all occurrences of the search string with the replacement string.

Usage
~~~~~
::

    input|str_replace( search, replace )

Parameters
~~~~~~~~~~
    =========== =============================================================== ========
    Name        Description                                                     Required
    =========== =============================================================== ========
    search      The string to search for (or array of such strings)             Yes
    replace     Replacement string (or array of such strings)                   Yes
    =========== =============================================================== ========

Examples
~~~~~~~~
::

    {'Hello, World!'|str_replace( 'World', 'Earth' )}

returns 'Hello, Earth!'.
