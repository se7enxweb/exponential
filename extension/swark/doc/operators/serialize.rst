serialize
---------

Summary
~~~~~~~
Returns a storable representation of the input using PHP function serialize().

Usage
~~~~~
::

    input|serialize

Parameters
~~~~~~~~~~
None.

Examples
~~~~~~~~
Using serialize and unserialize for caching value of variables::

    {def $children=false()}

    {set-block variable=$children_ser}
    {cache-block expiry="0" subtree_expiry="content/view/full/2"}
    {set $children=fetch( 'content', 'list', hash( 'parent_node_id', 2, 'as_object', false() ) )}
    {$children|serialize}
    {/cache-block}
    {/set-block}

    {if $children|not}
    {set $children=$children_ser|trim|unserialize}
    {/if}
