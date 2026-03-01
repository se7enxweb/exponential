uri_path_segment
----------------

Summary
~~~~~~~
Returns a segment of the input URL's path.

Usage
~~~~~
::

    input|uri_path_segment( [ index ] )

Parameters
~~~~~~~~~~
    =========== ================================================================ ======== =======
    Name        Description                                                      Required Default
    =========== ================================================================ ======== =======
    index       Which path segement to return, if negative, counted from the end No       -1
    =========== ================================================================ ======== =======

Examples
~~~~~~~~
::

    {'http://www.seeds.no/nor/content/search?SearchText=publish#results'|uri_path_segment}

Returns 'search'.
::

    {$child.url|uri_path_segment}

Returns last path segment of $child's url which can be used as an anchor.

