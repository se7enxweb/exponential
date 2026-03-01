variable_names
--------------

Summary
~~~~~~~
Shows or returns comma separated names of all available template variables. 
If the parameter is true or 1 or not given, the string is not returned but shown in the debug output.

Usage
~~~~~
::

    variable_names( [in_debug] )

Parameters
~~~~~~~~~~
    =========== =============================================================== ======== =======
    Name        Description                                                     Required Default
    =========== =============================================================== ======== =======
    in_debug    If true, uses debug output instead of returning the names       No       1
    =========== =============================================================== ======== =======

Examples
~~~~~~~~
::

    {variable_names()}
    
Placed in the pagelayout.tpl, it adds a debug notice '$module_result, $site, $ezinfo, $current_user, $anonymous_user_id, $access_type, $warning_list, $navigation_part, $uri_string, $requested_uri_string, $ui_context, $ui_component, $DesignKeys:used, $DesignKeys:matched'.
