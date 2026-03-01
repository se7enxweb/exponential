preg_replace
------------

Summary
~~~~~~~
Performs a regular expression search and replace.

Usage
~~~~~
::

    input|preg_replace( search, replace )

Parameters
~~~~~~~~~~
    =========== =============================================================== ========
    Name        Description                                                     Required
    =========== =============================================================== ========
    search      The pattern (Perl-compatible regular expression) to search for  Yes
                (or array of such patterns)
    replace     Replacement string (or array of such strings)                   Yes
    =========== =============================================================== ========

For more information see http://php.net/preg_replace.

Examples
~~~~~~~~
::

    {'Hello, John and Peter!'|preg_replace( array( '/John/', '/Peter/' ), array( 'Jane', 'Petra' ) )}

returns 'Hello, Jane and Petra!'.

::

    {'John Doe'|preg_replace( '/(\w+) (\w+)/', '$2, $1' )}

returns 'Doe, John'.
