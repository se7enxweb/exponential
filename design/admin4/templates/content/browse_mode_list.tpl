{if and( eq( $browse.action_name, 'SwapNode' ), is_set( $browse.persistent_data.ContentNodeID ) )}
    {def $swap_node = fetch( 'content', 'node', hash( 'node_id', $browse.persistent_data.ContentNodeID ) )}
    {if is_null( $swap_node )|not}
        {def $swap_node_class = get_class( $swap_node )}
    {else}
        {def $swap_node_class = false()}
    {/if}
{/if}

<table class="list" cellspacing="0">
<tr>
    <th scope="col" class="tight">
    {if eq( $select_type, 'checkbox' )}
        <button type="button" class="a4-icon-btn" title="{'Invert selection.'|i18n( 'design/admin/content/browse' )|wash}" aria-label="{'Invert selection.'|i18n( 'design/admin/content/browse' )|wash}" onclick="ezjs_toggleCheckboxes( document.browse, '{$select_name}[]' ); return false;"><span class="a4-icon a4-bi-check2-square is-medium" aria-hidden="true"></span></button>
    {else}
        &nbsp;
    {/if}
    </th>
    <th scope="col" class="wide">
    {'Name'|i18n( 'design/admin/content/browse' )}
    </th>
    <th scope="col" class="tight">
    {'Type'|i18n( 'design/admin/content/browse' )}
    </th>
</tr>

{include uri='design:content/browse_current_node.tpl' mode='list'}

{section var=Nodes loop=$node_array sequence=array( bglight, bgdark )}
  <tr class="{$Nodes.sequence}">
    <td>
    {* Note: The tpl code for $ignore_nodes_merge with the eq, unique and count
             is just a replacement for a missing template operator.
             If there are common elements the unique array will have less elements
             than the merged one
             In the future this should be replaced with a  new template operator that checks
             one array against another and returns true if elements in the first
             exists in the other *}
     {let ignore_nodes_merge=merge( $browse.ignore_nodes_select_subtree, $Nodes.item.path_array )
          browse_permission = true()}
     {if $browse.permission}
        {if $browse.permission.contentclass_id}
            {if is_array( $browse.permission.contentclass_id )}
                {foreach $browse.permission.contentclass_id as $contentclass_id}
                    {set $browse_permission = fetch( 'content', 'access', hash( 'access', $browse.permission.access,
                                                                       'contentobject',   $Nodes.item,
                                                                       'contentclass_id', $contentclass_id ) )}
                    {if $browse_permission|not}{break}{/if}
                {/foreach}
            {else}
                {set $browse_permission = fetch( 'content', 'access', hash( 'access', $browse.permission.access,
                                                                   'contentobject',   $Nodes.item,
                                                                   'contentclass_id', $browse.permission.contentclass_id ) )}
            {/if}
        {else}
            {set $browse_permission = fetch( 'content', 'access', hash( 'access', $browse.permission.access,
                                                               'contentobject',   $Nodes.item ) )}
        {/if}
     {/if}
     {if and( $browse_permission,
                           $browse.ignore_nodes_select|contains( $Nodes.item.node_id )|not,
                           eq( $ignore_nodes_merge|count,
                               $ignore_nodes_merge|unique|count ) )}
        {if is_array( $browse.class_array )}
            {if $browse.class_array|contains( $Nodes.item.object.content_class.identifier )}
                <input type="{$select_type}" aria-label="{$Nodes.item.name|wash}" name="{$select_name}[]" value="{$Nodes.item[$select_attribute]}" />
            {else}
                <input type="{$select_type}" aria-label="{$Nodes.item.name|wash}" name="_Disabled" value="" disabled="disabled" />
            {/if}
        {else}
            {*
              Do not allow node selection if
              - Action is move, copy or add node, and item is not a container
              - Action is swap node, and node to swap has children and item is not a container, and vice versa
            *}
            {if and( or( eq( $browse.action_name, 'MoveNode' ), eq( $browse.action_name, 'CopyNode' ), eq( $browse.action_name, 'AddNodeAssignment' ) ), $Nodes.item.object.content_class.is_container|not )}
                <input type="{$select_type}" aria-label="{$Nodes.item.name|wash}" name="{$select_name}[]" value="{$Nodes.item[$select_attribute]}" disabled="disabled" />
            {elseif and( eq( $browse.action_name, 'SwapNode' ),
                         eq( $swap_node_class, 'ezcontentobjecttreenode' ),
                         or( and( $swap_node.children_count|gt(0), $Nodes.item.object.content_class.is_container|not ),
                             and( $swap_node.is_container|not, $Nodes.item.children_count|gt(0) ) ) )}
                <input type="{$select_type}" aria-label="{$Nodes.item.name|wash}" name="{$select_name}[]" value="{$Nodes.item[$select_attribute]}" disabled="disabled" />
            {else}
                <input type="{$select_type}" aria-label="{$Nodes.item.name|wash}" name="{$select_name}[]" value="{$Nodes.item[$select_attribute]}" />
            {/if}
        {/if}
    {else}
        <input type="{$select_type}" aria-label="{$Nodes.item.name|wash}" name="_Disabled" value="" disabled="disabled" />
    {/if}
    {/let}
    </td>
    <td>
    {* a hidden node, or one hidden by a node above it, gets its status mark before the icon; visible ones none *}
    {include uri='design:parts/visibility_status.tpl' status_node=$Nodes.item status_badge=false() status_quiet=true()}

    {* Replaces node_view_gui... *}
    {* Note: The tpl code for $ignore_nodes_merge with the eq, unique and count
             is just a replacement for a missing template operator.
             If there are common elements the unique array will have less elements
             than the merged one
             In the future this should be replaced with a  new template operator that checks
             one array against another and returns true if elements in the first
             exists in the other *}
    {let ignore_nodes_merge=merge( $browse.ignore_nodes_click, $Nodes.item.path_array )}
    {if eq( $ignore_nodes_merge|count,
                      $ignore_nodes_merge|unique|count )}
        {if and( or( ne( $browse.action_name, 'MoveNode' ), ne( $browse.action_name, 'CopyNode' ) ), $Nodes.item.object.content_class.is_container )}
            {$Nodes.item.object.class_identifier|class_icon( small, $Nodes.item.object.class_name )}&nbsp;<a href={concat( '/content/browse/', $Nodes.item.node_id )|ezurl}>{$Nodes.item.name|wash}</a>
        {else}
            {$Nodes.item.object.class_identifier|class_icon( small, $Nodes.item.object.class_name )}&nbsp;{$Nodes.item.name|wash}
        {/if}
    {else}
        {$Nodes.item.object.class_identifier|class_icon( small, $Nodes.item.object.class_name )}&nbsp;{$Nodes.item.name|wash}
    {/if}
    {/let}

    </td>
    <td class="class nowrap">
    {$Nodes.item.object.content_class.name|wash}
    </td>
 </tr>
{/section}
</table>