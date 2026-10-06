{* design:menu/my_top.tpl: the pages below the start page, of the classes in
   menu.ini [MenuContentSettings] TopIdentifierList, in the start page's sort order.
   fetch() leaves hidden pages out for visitors (ShowHiddenNodes=false). *}
{def $root_id = ezini( 'NodeSettings', 'RootNode', 'content.ini' )
     $root = fetch( 'content', 'node', hash( 'node_id', $root_id ) )
     $items = fetch( 'content', 'list', hash( 'parent_node_id', $root_id,
                                              'sort_by', $root.sort_array,
                                              'class_filter_type', 'include',
                                              'class_filter_array', ezini( 'MenuContentSettings', 'TopIdentifierList', 'menu.ini' ) ) )
     $in_path = first_set( $module_result.path[1].node_id, 0 )
     $here = first_set( $module_result.node_id, 0 )}
{if $items}
<ul class="menu-top">
{foreach $items as $item}
    <li{if eq( $item.node_id, $in_path )} class="selected"{/if}><a href={$item.url_alias|ezurl}{if eq( $item.node_id, $here )} aria-current="page"{/if}>{$item.name|wash}</a></li>
{/foreach}
</ul>
{/if}
{undef $root_id $root $items $in_path $here}
