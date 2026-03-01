{* Folder - Full view *}
{set scope=global persistent_variable=hash('left_menu', false(),
                                           'extra_menu', false())}

{def $rss_export = fetch( 'rss', 'export_by_node', hash( 'node_id', $node.node_id ) )}

<section class="content-view-full">
    <div class="class-folder row">
        <div class="span8">
            {if $rss_export}
            <div class="attribute-rss-icon">
                <a href="{concat( '/rss/feed/', $rss_export.access_url )|ezurl( 'no' )}" title="{$rss_export.title|wash()}"><img src="{'rss-icon.gif'|ezimage( 'no' )}" alt="{$rss_export.title|wash()}" /></a>
            </div>
            {/if}

            <div class="attribute-header">
                <h1>{attribute_view_gui attribute=$node.data_map.name}</h1>
            </div>

            {if eq( ezini( 'folder', 'SummaryInFullView', 'content.ini' ), 'enabled' )}
                {if $node.object.data_map.short_description.has_content}
                    <div class="attribute-short">
                        {attribute_view_gui attribute=$node.data_map.short_description}
                    </div>
                {/if}
            {/if}

            {if $node.object.data_map.description.has_content}
                <div class="attribute-long">
                    {attribute_view_gui attribute=$node.data_map.description}
                </div>
            {/if}

                <div class="attribute-tags">
                    {attribute_view_gui attribute=$node.data_map.tags}
                </div>

            {if $node.object.data_map.show_children.data_int}
                {def $page_limit = 10
                     $classes = ezini( 'MenuContentSettings', 'ExtraIdentifierList', 'menu.ini' )
                     $children = array()
                     $children_count = ''}

                {if le( $node.depth, '3')}
                    {set $classes = $classes|merge( ezini( 'ChildrenNodeList', 'ExcludedClasses', 'content.ini' ) )}
                {/if}

                {set $children_count=fetch_alias( 'children_count', hash( 'parent_node_id', $node.node_id,
                                                                          'class_filter_type', 'exclude',
                                                                          'class_filter_array', $classes ) )}

                <section class="content-view-children">
                    {if $children_count}
                        {foreach fetch_alias( 'children', hash( 'parent_node_id', $node.node_id,
                                                                'offset', $view_parameters.offset,
                                                                'sort_by', $node.sort_array,
                                                                'class_filter_type', 'exclude',
                                                                'class_filter_array', $classes,
                                                                'limit', $page_limit ) ) as $child }
                            {node_view_gui view='line' content_node=$child}
                        {/foreach}
                    {/if}
                </section>

                {include name=navigator
                         uri='design:navigator/google.tpl'
                         page_uri=$node.url_alias
                         item_count=$children_count
                         view_parameters=$view_parameters
                         item_limit=$page_limit}

            {/if}
        </div>
        <div class="span4">
            <aside>
                {def $root_node = fetch( 'content', 'node', hash( 'node_id', $node.path_array[$node.depth] ) )
                     $menu_items = fetch( 'content', 'list', hash( 'parent_node_id', $root_node.node_id,
                                                                   'sort_by', $root_node.sort_array,
                                                                   'load_data_map', false(),
                                                                   'class_filter_type', 'include',
                                                                   'class_filter_array', ezini( 'MenuContentSettings', 'RightIdentifierList', 'menu.ini' ) ) )
                     $menu_items_count = $menu_items|count()}
                <section class="subnavigation row nav-collapse">
                    <div class="attribute-header">
                        <h2>{$node.name|wash()}</h2>
                    </div>
                    {if $menu_items_count}
                    <ul class="span4">
                        {foreach $menu_items as $key => $item}
                            {if eq( $item.class_identifier, 'link')}
                                <li><a href={$item.data_map.location.content|ezurl}{if and( is_set( $item.data_map.open_in_new_window ), $item.data_map.open_in_new_window.data_int )} target="_blank"{/if} title="{$item.data_map.location.data_text|wash}" class="menu-item-link" rel={$item.url_alias|ezurl}>{if $item.data_map.location.data_text}{$item.data_map.location.data_text|wash()}{else}{$item.name|wash()}{/if}</a>
                            {else}
                                <li><a href="{$item.url_alias|ezurl('no')}">{$item.name|wash()}</a>
                            {/if}
                            {def $sub_menu_items = fetch( 'content', 'list', hash( 'parent_node_id', $item.node_id,
                                                                                                  'sort_by', $item.sort_array,
                                                                                                  'load_data_map', false(),
                                                                                                  'class_filter_type', 'include',
                                                                                                  'class_filter_array', ezini( 'MenuContentSettings', 'RightIdentifierList', 'menu.ini' ) ) )
                                 $sub_menu_items_count = $sub_menu_items|count}
                            {if $sub_menu_items_count}
                                <ul>
                                    {foreach $sub_menu_items as $subkey => $subitem}
                                        {if eq( $subitem.class_identifier, 'link')}
                                            <li><a href={$subitem.data_map.location.content|ezurl}{if and( is_set( $subitem.data_map.open_in_new_window ), $subitem.data_map.open_in_new_window.data_int )} target="_blank"{/if} title="{$subitem.data_map.location.data_text|wash}" class="menu-item-link" rel={$subitem.url_alias|ezurl}>{if $subitem.data_map.location.data_text}{$subitem.data_map.location.data_text|wash()}{else}{$subitem.name|wash()}{/if}</a></li>
                                        {else}
                                            <li><a href="{$subitem.url_alias|ezurl( 'no' )}">{$subitem.name|wash()}</a></li>
                                        {/if}
                                    {/foreach}
                                </ul>
                            {/if}
                            {undef $sub_menu_items $sub_menu_items_count}
                            </li>
                        {/foreach}
                    </ul>
                    {/if}
                    {undef $root_node $menu_items $menu_items_count}
                </section>
                <section class="content-view-aside">
                    <div class="attribute-call-for-action">
                        {attribute_view_gui attribute=$node.data_map.call_for_action}
                    </div>
                </section>
            </aside>
        </div>
    </div>
</section>
