<!-- Top menu content: START -->
<div class="navbar-inner">
    <div class="container">

        <a class="btn btn-navbar" data-action="toggleclass" data-class="nav-collapse" data-target=".main-navi .nav-collapse">{'Navigation'|i18n( 'design/ezdemo/menu' )}</a>

        <div class="nav-collapse">
            <ul class="nav">
                {def $root_node = fetch( 'content', 'node', hash( 'node_id', $pagedata.root_node ) )
                     $top_menu_class_filter = ezini( 'MenuContentSettings', 'TopIdentifierList', 'menu.ini' )
                     $top_menu_items = fetch( 'content', 'list', hash( 'parent_node_id', $root_node.node_id,
                                                                       'sort_by', $root_node.sort_array,
                                                                       'class_filter_type', 'include',
                                                                       'class_filter_array', $top_menu_class_filter ) )
                     $top_menu_items_count = $top_menu_items|count()
                     $item_class = array()
                     $level_2_items = 0
                     $current_node_in_path = first_set($pagedata.path_array[1].node_id, 0  )
                     $current_node_in_path_2 = first_set($pagedata.path_array[2].node_id, 0  )}

                {if $top_menu_items_count}
                   {foreach $top_menu_items as $key => $item}
                        {set $item_class = array()}
                        {if $current_node_in_path|eq($item.node_id)}
                            {set $item_class = array("active")
                                 $level_2_items = fetch( 'content', 'list', hash( 'parent_node_id', $item.node_id,
                                                                                  'sort_by', $item.sort_array,
                                                                                  'class_filter_type', 'include',
                                                                                  'class_filter_array', $top_menu_class_filter ) )}
                        {/if}
                        {if $key|eq(0)}
                            {set $item_class = $item_class|append("firstli")}
                        {/if}
                        {if $top_menu_items_count|eq( $key|inc )}
                            {set $item_class = $item_class|append("lastli")}
                        {/if}
                        {if $item.node_id|eq( $current_node_id )}
                            {set $item_class = $item_class|append("current")}
                        {/if}

                        {if eq( $item.class_identifier, 'link')}
                            <li id="node_id_{$item.node_id}"{if $item_class} class="{$item_class|implode(" ")}"{/if}><a {if eq( $ui_context, 'browse' )}href={concat("content/browse/", $item.node_id)|ezurl}{else}href={$item.data_map.location.content|ezurl}{if and( is_set( $item.data_map.open_in_new_window ), $item.data_map.open_in_new_window.data_int )} target="_blank"{/if}{/if}{if $pagedata.is_edit} onclick="return false;"{/if} title="{$item.data_map.location.data_text|wash}" class="menu-item-link" rel={$item.url_alias|ezurl}>{if $item.data_map.location.data_text}{$item.data_map.location.data_text|wash()}{else}{$item.name|wash()}{/if}</a></li>
                        {else}
                            <li id="node_id_{$item.node_id}"{if $item_class} class="{$item_class|implode(" ")}"{/if}><a href={if eq( $ui_context, 'browse' )}{concat("content/browse/", $item.node_id)|ezurl}{else}{$item.url_alias|ezurl}{/if}{if $pagedata.is_edit} onclick="return false;"{/if}>{$item.name|wash()}</a></li>
                        {/if}
                    {/foreach}
                {/if}
            </ul>
        </div>

    </div>
</div>

{if $level_2_items|count()}
<div class="navbar-inner sublevel">
    <div class="container">

        <div class="nav-collapse">
            <ul class="nav">
                {foreach $level_2_items as $key => $item}
                    {set $item_class = array()}
                    {if $current_node_in_path_2|eq($item.node_id)}
                        {set $item_class = array("active")}
                    {/if}
                    {if $key|eq(0)}
                        {set $item_class = $item_class|append("firstli")}
                    {/if}
                    {if $top_menu_items_count|eq( $key|inc )}
                        {set $item_class = $item_class|append("lastli")}
                    {/if}
                    {if $item.node_id|eq( $current_node_id )}
                        {set $item_class = $item_class|append("current")}
                    {/if}

                    {if eq( $item.class_identifier, 'link')}
                        <li id="node_id_{$item.node_id}"{if $item_class} class="{$item_class|implode(" ")}"{/if}><a {if eq( $ui_context, 'browse' )}href={concat("content/browse/", $item.node_id)|ezurl}{else}href={$item.data_map.location.content|ezurl}{if and( is_set( $item.data_map.open_in_new_window ), $item.data_map.open_in_new_window.data_int )} target="_blank"{/if}{/if}{if $pagedata.is_edit} onclick="return false;"{/if} title="{$item.data_map.location.data_text|wash}" class="menu-item-link" rel={$item.url_alias|ezurl}>{if $item.data_map.location.data_text}{$item.data_map.location.data_text|wash()}{else}{$item.name|wash()}{/if}</a></li>
                    {else}
                        <li id="node_id_{$item.node_id}"{if $item_class} class="{$item_class|implode(" ")}"{/if}><a href={if eq( $ui_context, 'browse' )}{concat("content/browse/", $item.node_id)|ezurl}{else}{$item.url_alias|ezurl}{/if}{if $pagedata.is_edit} onclick="return false;"{/if}>{$item.name|wash()}</a></li>
                    {/if}
                {/foreach}
            </ul>
        </div>
    </div>
</div>
{/if}
{undef $level_2_items $root_node $top_menu_items $item_class $top_menu_items_count $current_node_in_path $current_node_in_path_2}
<!-- Top menu content: END -->
