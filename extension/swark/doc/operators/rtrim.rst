rtrim
-----

Summary
~~~~~~~
Strips whitespaces (or characters in the parameter, if given) from the end of the input string.

Usage
~~~~~
::

    input|rtrim( [ charlist] )

Parameters
~~~~~~~~~~
    =========== =============================================================== ======== =======
    Name        Description                                                     Required Default
    =========== =============================================================== ======== =======
    charlist    String containing characters to be removed                      No
    =========== =============================================================== ======== =======

Examples
~~~~~~~~
::

    {'   -- Hello, world! --   '|rtrim}

Returns '   -- Hello, world! --'.
::

    {'   -- Hello, world! --   '|rtrim( ' -' )}

Returns '   -- Hello, world!'.
