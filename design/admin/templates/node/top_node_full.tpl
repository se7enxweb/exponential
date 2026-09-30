{* Full view of the top node (node 1) of the content structure.

   The top node is not a content object: its ezcontentobject_tree row has
   contentobject_id 0 and there is no object, version, name or language
   behind it. The kernel hands the templates a placeholder folder object for
   it, so the generic full view showed an object that is not there: the Unix
   epoch as its modification date, an empty object ID, an unknown language
   flag, object tabs with nothing in them and Edit, Move, Remove and version
   actions that cannot work on it.

   This view shows what the top node really has - its name, node ID, the
   time of the latest change below it, its details and the ordering of its
   children, which the kernel stores on the node itself - and the same sub
   items list as every other container. *}
{def $top_languages  = fetch( 'content', 'prioritized_languages' )
     $top_language   = cond( $top_languages|count|gt( 0 ), $top_languages[0], false() )
     $top_children_count = fetch( 'content', 'list_count', hash( 'parent_node_id', $node.node_id ) )
     $node_url_alias = concat( 'content/view/full/', $node.node_id )
     $admin_navigation_content_pref = ezpreference( 'admin_navigation_content' )
     $tabs_disabled  = false()
     $default_tab    = 'view'
     $node_tab_index = first_set( $view_parameters.tab, $default_tab )
     $read_open_tab_by_cookie = true()}
{if $admin_navigation_content_pref|is_string}{set $tabs_disabled = $admin_navigation_content_pref|not}{/if}
{if array( 'view', 'details', 'ordering' )|contains( $node_tab_index )|not()}
    {set $node_tab_index = $default_tab}
{elseif is_set( $view_parameters.tab )}
    {set $tabs_disabled = false()
         $read_open_tab_by_cookie = false()}
{/if}
<div class="content-view-full">
 <div class="class-root">

<div class="content-navigation">

{* Content window. *}
<div class="context-block">

{* DESIGN: Header START *}<div class="box-header">

<h1 class="context-title">{'folder'|class_icon( normal, 'Top node'|i18n( 'design/admin/node/view/full' ) )}&nbsp;{$node.name|wash}&nbsp;[{'Top node'|i18n( 'design/admin/node/view/full' )}]</h1>

{* DESIGN: Mainline *}<div class="header-mainline"></div>

{* DESIGN: Header END *}</div>


{* DESIGN: Content START *}<div class="box-content">

<div class="context-information">
<p class="left modified">{'Last modified'|i18n( 'design/admin/node/view/full' )}: {$node.modified_subnode|l10n( shortdatetime )} ({'Node ID'|i18n( 'design/admin/node/view/full' )}: {$node.node_id})</p>
{if $top_language}
<p class="right translation">{$top_language.locale_object.intl_language_name|wash}&nbsp;<img src="{$top_language.locale|flag_icon}" width="18" height="12" alt="{$top_language.locale|wash}" style="vertical-align: middle;" /></p>
{/if}
<div class="break"></div>
</div>

<div id="window-controls" class="tab-block">

{if $tabs_disabled}
    <div class="button-left"><a id="maincontent-show" class="show-hide-tabs" href={'/user/preferences/set/admin_navigation_content/1'|ezurl} title="{'Enable &quot;Tabs&quot; by default while browsing content.'|i18n( 'design/admin/parts/my/menu' )}">&nbsp;</a></div>
{else}
    <div class="button-left"><a id="maincontent-hide" class="show-hide-tabs" href={'/user/preferences/set/admin_navigation_content/0'|ezurl} title="{'Disable &quot;Tabs&quot; by default while browsing content.'|i18n( 'design/admin/parts/my/menu' )}">&nbsp;</a></div>
{/if}

<ul class="tabs{if $tabs_disabled} disabled{/if}{if $read_open_tab_by_cookie} tabs-by-cookie{/if}">
{foreach hash( 'view',     hash( 'label', 'View'|i18n( 'design/admin/node/view/full' ),     'title', 'Show simplified view of content.'|i18n( 'design/admin/node/view/full' ), 'class', 'first' ),
               'details',  hash( 'label', 'Details'|i18n( 'design/admin/node/view/full' ),  'title', 'Show details.'|i18n( 'design/admin/node/view/full' ), 'class', 'middle' ),
               'ordering', hash( 'label', 'Ordering'|i18n( 'design/admin/node/view/full' ), 'title', 'Show published ordering overview.'|i18n( 'design/admin/node/view/full' ), 'class', 'last' ) ) as $tab => $tab_info}
    <li id="node-tab-{$tab}" class="{$tab_info.class}{if $node_tab_index|eq( $tab )} selected{/if}">
        {if $tabs_disabled}
            <span class="disabled" title="{'Tab is disabled, enable with toggler to the left of these tabs.'|i18n( 'design/admin/node/view/full' )}">{$tab_info.label}</span>
        {else}
            <a href={concat( $node_url_alias, '/(tab)/', $tab )|ezurl} title="{$tab_info.title}">{$tab_info.label}</a>
        {/if}
    </li>
{/foreach}
</ul>
<div class="float-break"></div>

{if $tabs_disabled}
<div class="tabs-content disabled"></div>
{else}
<div class="tabs-content">

{* (Pre)view window *}
<div id="node-tab-view-content" class="tab-content{if $node_tab_index|ne( 'view' )} hide{else} selected{/if}">
<div class="block">
<p>{'This is the top node of the content structure. It is not a content object: it has no class, attributes, translations, versions or other locations, and it cannot be edited, moved or removed. The top-level nodes it holds are listed under Sub items.'|i18n( 'design/admin/node/view/full' )}</p>
</div>
<div class="break"></div>
</div>

{* Details window *}
<div id="node-tab-details-content" class="tab-content{if $node_tab_index|ne( 'details' )} hide{else} selected{/if}">
<div class="block">
<table class="list" cellspacing="0" summary="{'Details of the top node: its node ID, depth and path, the number of sub items and the time of the latest change below it.'|i18n( 'design/admin/node/view/full' )}">
<tr>
    <th class="tight">{'Node ID'|i18n( 'design/admin/node/view/full' )}</th>
    <th class="tight">{'Depth'|i18n( 'design/admin/node/view/full' )}</th>
    <th>{'Path String'|i18n( 'design/admin/node/view/full' )}</th>
    <th class="tight">{'Sub items'|i18n( 'design/admin/node/view/full' )}</th>
    <th>{'Last modified'|i18n( 'design/admin/node/view/full' )}</th>
</tr>
<tr class="bglight">
    <td class="number" align="right">{$node.node_id}</td>
    <td class="number" align="right">{$node.depth}</td>
    <td>{$node.path_string|wash}</td>
    <td class="number" align="right">{$top_children_count}</td>
    <td>{$node.modified_subnode|l10n( shortdatetime )}</td>
</tr>
</table>
</div>

<div class="block">
<table class="list" cellspacing="0" summary="{'Node Remote ID'|i18n( 'design/admin/node/view/full' )}">
    <tr>
        <th>{'Node Remote ID'|i18n( 'design/admin/node/view/full' )}</th>
    </tr>
    <tr>
         <td>{$node.remote_id|wash}</td>
    </tr>
</table>
</div>
<div class="break"></div>
</div>

{* Published ordering window: the kernel keeps the sorting of the top-level nodes on the top node itself. *}
<div id="node-tab-ordering-content" class="tab-content{if $node_tab_index|ne( 'ordering' )} hide{else} selected{/if}">
    {include uri='design:ordering.tpl'}
<div class="break"></div>
</div>

</div>
{/if}

{ezscript_require( 'node_tabs.js' )}

</div>

{* DESIGN: Content END *}</div>

</div>

{* Children window.*}
<div id="content-view-children">
    {include uri='design:children.tpl'}
</div>

</div>

 </div>
</div>
{undef $top_languages $top_language $top_children_count $node_url_alias $admin_navigation_content_pref $tabs_disabled $default_tab $node_tab_index $read_open_tab_by_cookie}
