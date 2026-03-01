add_view_parameters
-------------------

Summary
~~~~~~~
Returns the input path extended by the view parameters.

Usage
~~~~~
::

    input|add_view_parameters( view_parameters )

Parameters
~~~~~~~~~~
    =============== =========================================================== ======== =======
    Name            Description                                                 Required Default
    =============== =========================================================== ======== =======
    view_parameters View parameters to add to the input path                    Yes
    =============== =========================================================== ======== =======

Examples
~~~~~~~~
::

    {$node.url|add_view_parameters( $view_parameters )|ezurl}

In a node's full view template reconstructs the request URL. See also the modify_view_parameter operator.
