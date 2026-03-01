is_post_request
---------------

Summary
~~~~~~~
Returns true if the current request method is POST, false otherwise.

Usage
~~~~~
::

    is_post_request()

Parameters
~~~~~~~~~~
None.

Examples
~~~~~~~~
::

    This is a {if is_post_request()}POST{else}GET{/if} request.

Shows 'This is a POST request' if the current request is POST, 'This is a GET request' otherwise.
