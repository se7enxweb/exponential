return
------

Summary
~~~~~~~
Terminates execution returning input as the response.

Usage
~~~~~
::

    input|return( [ content_type ] )

Parameters
~~~~~~~~~~
    ============ =============================================================== ======== =======
    Name         Description                                                     Required Default
    ============ =============================================================== ======== =======
    content_type Content type of HTTP response                                   No
    ============ =============================================================== ======== =======

Examples
~~~~~~~~
::

    {$variable|json_encode|return( 'application/json' )}

Returns value of $variable as JSON document suitable for processing by Javascript.
