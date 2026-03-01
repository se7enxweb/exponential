range
-----

Summary
~~~~~~~
Returns an array of integers within the given range.

Usage
~~~~~
::

    range( min, max [, step] )

Parameters
~~~~~~~~~~
    =========== =============================================================== ======== =======
    Name        Description                                                     Required Default
    =========== =============================================================== ======== =======
    min         The minimum value, inclusive                                    Yes
    max         The maximum value, inclusive                                    Yes
    step        Increment between elements                                      No       1
    =========== =============================================================== ======== =======

Note: min and max might be interchanged. The sign of step is not important.

Examples
~~~~~~~~
::

    {range( 1, 10 )}

Returns array of integers from 1 to 10 inclusive.
::

    {range( 1, 12, 3 )}

Returns array( 1, 4, 7, 10 ).
::

    {range( 5, 2 )}

Returns array( 5, 4, 3, 2 ).
::

    {range( 9, 2, 2 )}

or::

    {range( 9, 2, -2 )}

Returns array( 9, 7, 5, 3 ).
