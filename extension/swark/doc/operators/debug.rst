debug
-----

Summary
~~~~~~~
Adds a notice to the debug output.

Usage
~~~~~
::

    input|debug( [ header ] )

Parameters
~~~~~~~~~~
    =========== =============================================================== ======== ==============
    Name        Description                                                     Required Default
    =========== =============================================================== ======== ==============
    header      Header/title of a notice                                        No       Debug operator
    =========== =============================================================== ======== ==============

Examples
~~~~~~~~
::

    {concat( 'Node ID =', $node.node_id )|debug}

Adds a notice about the node ID of $node to the debug output.
::

    {$variable|debug( 'Content of $variable' )}

Dumps $variable to the debug output as a notice titled 'Content of $variable'. See also the debug_attributes operator.
