redirect
--------

Summary
~~~~~~~
Stops execution and redirects to the given URL.

Usage
~~~~~
::

    redirect( url [, status] )

or

::

    url|redirect

Parameters
~~~~~~~~~~
    =========== =============================================================== ======== =======
    Name        Description                                                     Required Default
    =========== =============================================================== ======== =======
    url         Address to redirect to                                          Yes
    status      HTTP status code                                                No       302
    =========== =============================================================== ======== =======

Examples
~~~~~~~~
::

    {redirect( 'book/article' )}

Stops execution and redirects to the node represented by the (eZ Publish) path string book/article.

::

    {'http://www.seeds.no'|redirect}

Stops execution and redirects to http://www.seeds.no.

::

    {redirect( $node.parent.url, 301 )}

Stops execution and redirects to the parent node of $node, returning status code 301 (permanent redirection).
