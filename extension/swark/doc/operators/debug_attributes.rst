debug_attributes
----------------

Summary
~~~~~~~
Shows attribute values in the debug output. This operator is equivalent to "attribute( show )" but uses the debug output.

Usage
~~~~~
::

    input|debug_attributes( [header] [, depth] )

Parameters
~~~~~~~~~~
    =========== =============================================================== ======== ================
    Name        Description                                                     Required Default
    =========== =============================================================== ======== ================
    header      Header/title of a debug notice                                  No       Debug attributes
                                                                                         operator
    depth       Number of levels to show                                        No       2
    =========== =============================================================== ======== ================

For convenience, the order of parameters can be interchanged.

Examples
~~~~~~~~
::

    {$node|debug_attributes}

Shows attribute values of $node.
::

    {$node|debug_attributes( 'Dump of $node' )}

Shows attribute values of $node as a notice titled 'Dump of $node'.
::

    {$node|debug_attributes( 'Dump of $node', 1 )}

or::

    {$node|debug_attributes( 1, 'Dump of $node' )}

Shows attributes values of $node (but not attributes of object attributes) as a notice titled 'Dump of $node'.
