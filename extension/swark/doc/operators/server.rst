server
------

Summary
~~~~~~~
Returns content of the server variable identified by the parameter. By server variables
we understand information such as headers, paths, and script locations, variables
defined in CGI specification etc. For more information, see
http://php.net/reserved.variables.server

Usage
~~~~~
::

    server( variable_name )

Note: Variable name is case insensitive, the operator will capitalize it before obtaining its value.

Parameters
~~~~~~~~~~
    ============= ============================================================= ======== =======
    Name          Description                                                   Required Default
    ============= ============================================================= ======== =======
    variable_name Name of the server variable to return                         Yes
    ============= ============================================================= ======== =======

Examples
~~~~~~~~
::

    {server( 'remote_addr' )}

Returns the client's IP address.
::

    {server( 'http_referer' )}

Returns from which URL the client got to the current page.
::

    {server( 'http_user_agent' )}

Returns the identification of client's browser.
