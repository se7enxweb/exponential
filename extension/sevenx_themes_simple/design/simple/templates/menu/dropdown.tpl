{* DropDown Menu : START *}

{def $root_node = fetch( 'content', 'node', hash( 'node_id', $indexpage ) )
     $top_menu_class_filter = ezini( 'MenuContentSettings', 'TopIdentifierList', 'menu.ini' )
     $top_menu_items = fetch( 'content', 'list', hash('parent_node_id', $root_node.node_id,
                                                      'sort_by', $root_node.sort_array,
						      'class_filter_type', 'include', 'class_filter_array', $top_menu_class_filter, 'ignore_visibility', false() ))
     $top_menu_items_count = $top_menu_items|count()
     $item_class = array()
     $level_2_items = 0
     $top_level_exclude_items = array( 180 )
     $level_2_limit = 7
     $current_node_in_path = first_set($module_result.node_id, 0 )
     $current_node_in_path_2 = first_set($module_result.node_id, 0 )}
<ul class="navbar-nav">
{if $top_menu_items_count}
{foreach $top_menu_items as $key => $item}
{set $item_class = array()
     $item_dropdown_class = array()
     $level_2_items = fetch('content', 'list', hash( 'parent_node_id', $item.node_id, 'sort_by', array( 'priority', false() ), 'limit', $level_2_limit))}
{set $item_class = $item_class|append("nav-link")}
{if $current_node_in_path|eq($item.node_id)}
{set $item_class = $item_class|append("active")}
{/if}
{if $key|eq(0)}
{set $item_class = $item_class|append("firstli")}
{/if}
{if $top_menu_items_count|eq($key|inc)}
{set $item_class = $item_class|append("lastli")}
{/if}
{if $item.node_id|eq($current_node_id)}
{set $item_class = $item_class|append("current")}
{/if}

{if eq( $item.class_identifier, 'link')}

<li class="dropdown" id="node_id_{$item.node_id}">
<a 
  {if eq( $ui_context, 'browse' )}
href={concat("content/browse/", $item.node_id)|ezurl}
  {else}
href={$item.data_map.location.content|ezurl}
{if and( is_set( $item.data_map.open_in_new_window ), $item.data_map.open_in_new_window.data_int )}
 target="_blank"
{/if}
  {/if}
  {if $pagedata.is_edit}
onclick="return false;"
  {/if}
  title="{$item.data_map.location.data_text|wash}"
  {if $item_class}class="{$item_class|implode(" ")}"{/if}
  rel={$item.url_alias|ezurl}>
{if $item.data_map.location.data_text}{$item.data_map.location.data_text|wash()}{else}{$item.name|wash()}{/if}</a>
  </li>
{else}
{* $item|attribute(show,1)}<hr /> *}
{if or( $item.children_count|eq(0), $item.node_id|eq( 180 ) )}
{*set $item_class=array()*}
<li class="dropdown">
  <a role="button" href={if eq( $ui_context, 'browse' )}{concat("content/browse/", $item.node_id)|ezurl}{else}{$item.url_alias|ezurl}{/if}
 {if $item_class}class="{$item_class|implode(" ")}"{/if}
 {if $pagedata.is_edit}
  onclick="return false;"
 {/if}>{$item.name|wash()}</a>
{else}
<li class="dropdown">
  <div class="dropdown-button">
    <a href={$item.url_alias|ezurl} role="button">{$item.name|wash()}</a>
    <button type="button" class="dropdown-toggle" aria-label="dropdown toggler button"></button>
  </div>

{/if}
{/if}

{if $top_level_exclude_items|contains( $item.node_id )}{continue}{/if}

{if $level_2_items|count()}
<div class="dropdown-menu">
<div>
<ul class="dropdown-nav">

{foreach $level_2_items as $key => $item}
 {set $item_class = array()}
 {set $item_class = $item_class|append("dropdown-link")}

 {if $current_node_in_path_2|eq($item.node_id)}
  {set $item_class = $item_class|append("active")}
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
<li id="node_id_{$item.node_id}">
<a
{if eq( $ui_context, 'browse' )} href={concat("content/browse/", $item.node_id)|ezurl}{else} href={$item.data_map.location.content|ezurl}
{if and( is_set( $item.data_map.open_in_new_window ), $item.data_map.open_in_new_window.data_int )} target="_blank" {/if}
{/if}
{if $pagedata.is_edit} onclick="return false;" {/if}
title="{$item.data_map.location.data_text|wash}"
role="button"
{if $item_class} class="{$item_class|implode(" ")}"{/if}
rel={$item.url_alias|ezurl}>{if $item.data_map.location.data_text}{$item.data_map.location.data_text|wash()}{else}{$item.name|wash()}{/if}</a>
  </li>
{else}
<li>
<a
href={if eq( $ui_context, 'browse' )}{concat("content/browse/", $item.node_id)|ezurl}{else}{$item.url_alias|ezurl}{/if}
{if $item_class} class="{$item_class|implode(" ")}"{/if}
{if $pagedata.is_edit} onclick="return false;"{/if}>{$item.name|wash()}</a>
</li>
 {/if}
{/foreach}
</ul>
 </div>
</div>
</li>
 {/if}
{/foreach}
  {/if}
 </ul>
</div>

{undef $level_2_items $root_node $top_menu_items $item_class $top_menu_items_count $current_node_in_path $current_node_in_path_2}

{* Top menu content: END *}