{* design:menu/my_section_left.tpl: the pages of the first-level page the visitor is in
   ($module_result.path[1]; ezwebin's $pagedata.path_array holds the same path), of the
   classes in menu.ini [MenuContentSettings] LeftIdentifierList. Nothing on the start page. *}
{def $section_id = first_set( $module_result.path[1].node_id, 0 )}
{if $section_id}
{def $section = fetch( 'content', 'node', hash( 'node_id', $section_id ) )
     $items = fetch( 'content', 'list', hash( 'parent_node_id', $section_id,
                                              'sort_by', $section.sort_array,
                                              'class_filter_type', 'include',
                                              'class_filter_array', ezini( 'MenuContentSettings', 'LeftIdentifierList', 'menu.ini' ) ) )
     $here = first_set( $module_result.node_id, 0 )}
<h2><a href={$section.url_alias|ezurl}>{$section.name|wash}</a></h2>
{if $items}
<ul class="menu-left">
{foreach $items as $item}
    <li><a href={$item.url_alias|ezurl}{if eq( $item.node_id, $here )} aria-current="page"{/if}>{$item.name|wash}</a></li>
{/foreach}
</ul>
{/if}
{undef $section $items $here}
{/if}
{undef $section_id}
