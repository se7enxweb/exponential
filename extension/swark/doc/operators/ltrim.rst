ltrim
-----

Summary
~~~~~~~
Strips whitespaces (or characters in the parameter, if given) from the beginning of the input string.

Usage
~~~~~
::

    input|ltrim( [ charlist] )

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

    {'   -- Hello, world! --   '|ltrim}

Returns '-- Hello, world! --   '.
::

    {'   -- Hello, world! --   '|ltrim( ' -' )}

Returns 'Hello, world! --   '.
