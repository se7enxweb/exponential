modify_view_parameter
---------------------

Summary
~~~~~~~
Adds, removes or changes a view (user) parameter in the input path.

Usage
~~~~~
::

    input|modify_view_parameter( parameter, value )

Parameters
~~~~~~~~~~
    =========== =============================================================== ======== =======
    Name        Description                                                     Required Default
    =========== =============================================================== ======== =======
    parameter   Name of view (user) parameter to add, remove or change          Yes
    value       New value or false to remove the parameter                      Yes
    =========== =============================================================== ======== =======

Examples
~~~~~~~~
::

    {'/path/to/node/(sort)/name/(offset)/10'|modify_view_parameter( 'sort', 'modified' )}

Returns '/path/to/node/(sort)/modified/(offset)/10'.
::

    {'/path/to/node/(sort)/name/(offset)/10'|modify_view_parameter( 'sort', false() )}

Returns '/path/to/node/(offset)/10'.
::

    {'/path/to/node/(sort)/name/(offset)/10'|modify_view_parameter( 'sort_order', 'desc' )}

Returns '/path/to/node/(sort)/name/(offset)/10/(sort_order)/desc'.
