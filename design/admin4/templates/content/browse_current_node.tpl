{* Quick select of the current node: offers the node whose children are shown for selection,
   pre-selected, so that one click on "Select" picks it. It follows the same rules as the items
   of the list: permission, ignored nodes and subtrees, class constraints, containers only for
   move, copy and add location, swap compatibility. The value is the node or object ID, as the
   browse returns. Not shown for search results and the top level.
   Parameter: $mode 'list' (a table row) or 'thumbnail' (a thumbnail cell). *}
{if and( is_set( $main_node ), is_unset( $node_list ), $main_node.node_id|gt( 1 ) )}
{def $current_node_ignore = merge( $browse.ignore_nodes_select_subtree, $main_node.path_array )
     $current_node_selectable = and( $browse.ignore_nodes_select|contains( $main_node.node_id )|not,
                                     eq( $current_node_ignore|count, $current_node_ignore|unique|count ) )
     $current_node_swap = false()}
{if and( $current_node_selectable, $browse.permission )}
    {if $browse.permission.contentclass_id}
        {if is_array( $browse.permission.contentclass_id )}
            {foreach $browse.permission.contentclass_id as $contentclass_id}
                {set $current_node_selectable = fetch( 'content', 'access', hash( 'access', $browse.permission.access,
                                                                                  'contentobject',   $main_node,
                                                                                  'contentclass_id', $contentclass_id ) )}
                {if $current_node_selectable|not}{break}{/if}
            {/foreach}
        {else}
            {set $current_node_selectable = fetch( 'content', 'access', hash( 'access', $browse.permission.access,
                                                                              'contentobject',   $main_node,
                                                                              'contentclass_id', $browse.permission.contentclass_id ) )}
        {/if}
    {else}
        {set $current_node_selectable = fetch( 'content', 'access', hash( 'access', $browse.permission.access,
                                                                          'contentobject',   $main_node ) )}
    {/if}
{/if}
{if $current_node_selectable}
    {if is_array( $browse.class_array )}
        {set $current_node_selectable = $browse.class_array|contains( $main_node.class_identifier )}
    {elseif and( or( eq( $browse.action_name, 'MoveNode' ), eq( $browse.action_name, 'CopyNode' ), eq( $browse.action_name, 'AddNodeAssignment' ) ), $main_node.is_container|not )}
        {set $current_node_selectable = false()}
    {elseif and( eq( $browse.action_name, 'SwapNode' ), is_set( $browse.persistent_data.ContentNodeID ) )}
        {set $current_node_swap = fetch( 'content', 'node', hash( 'node_id', $browse.persistent_data.ContentNodeID ) )}
        {if and( $current_node_swap,
                 or( and( $current_node_swap.children_count|gt( 0 ), $main_node.is_container|not ),
                     and( $current_node_swap.is_container|not, $main_node.children_count|gt( 0 ) ) ) )}
            {set $current_node_selectable = false()}
        {/if}
    {/if}
{/if}
{if eq( $mode, 'thumbnail' )}
  <tr class="browse-current-node">
    <td width="25%">
    {node_view_gui view=browse_thumbnail content_node=$main_node show_link=false()}
    <div class="controls">
    {if $current_node_selectable}
        <input type="{$select_type}" name="{$select_name}[]" value="{$main_node[$select_attribute]}" id="browse-current-node" checked="checked" />
    {else}
        <input type="{$select_type}" name="_Disabled" value="" id="browse-current-node" disabled="disabled" />
    {/if}
    <p><label for="browse-current-node"><b>{'Current Location:'|i18n( 'design/admin/content/browse' )}</b> {$main_node.name|wash}</label></p>
    </div>
    </td>
  </tr>
{else}
  <tr class="bgdark browse-current-node">
    <td>
    {if $current_node_selectable}
        <input type="{$select_type}" name="{$select_name}[]" value="{$main_node[$select_attribute]}" id="browse-current-node" checked="checked" />
    {else}
        <input type="{$select_type}" name="_Disabled" value="" id="browse-current-node" disabled="disabled" />
    {/if}
    </td>
    <td>
    <label for="browse-current-node"><b>{'Current Location:'|i18n( 'design/admin/content/browse' )}</b> {$main_node.class_identifier|class_icon( small, $main_node.class_name )}&nbsp;{$main_node.name|wash}</label>
    </td>
    <td class="class nowrap">
    {$main_node.class_name|wash}
    </td>
  </tr>
{/if}
{undef $current_node_ignore $current_node_selectable $current_node_swap}
{/if}
