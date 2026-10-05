{* The bookmark tree: the virtual folders of the user's bookmarks, every bookmark one click away.
   One template for the three places that show it:
     mode='page'     the bookmark page (content/bookmark): checkboxes, folder and bookmark actions
     mode='sidebar'  the Bookmarks box of the right column
     mode='browse'   the bookmarks of the browse dialog (needs $browse, $select_name, $select_type, $select_attribute)
   The folders open and close in the browser (remembered, shared by the three places); without JavaScript
   the whole tree is open. Parameters: rows (the result of fetch content bookmark_rows, fetched when not given),
   ui_context (sidebar: edit or browse disables the context menu). *}
{default mode='sidebar'
         rows=false()
         ui_context=''
         browse=false()
         select_name='SelectedObjectIDArray'
         select_type='checkbox'
         select_attribute='contentobject_id'}
{def $tree_rows = $rows}
{if $tree_rows|not}
    {set $tree_rows = fetch( 'content', 'bookmark_rows', hash() )}
{/if}
<link rel="stylesheet" href={'stylesheets/exp_bookmarks.css'|ezdesign} />
<script type="text/javascript" src={'javascript/exp_bookmarks.js'|ezdesign} defer="defer"></script>
{def $node = false()}
<ul class="exp-bm-tree exp-bm-{$mode}" role="tree" data-exp-bm="{$mode}">
{foreach $tree_rows as $row}
  {if eq( $row.type, 'folder' )}
    <li class="exp-bm-row exp-bm-folder" role="treeitem" aria-expanded="true" aria-level="{$row.depth|inc}" style="--d:{$row.depth}"
        data-type="folder" data-id="{$row.id}" data-parent="{$row.parent_id}" data-name="{$row.name|wash}"{if eq( $mode, 'page' )} draggable="true"{/if}>
      <button type="button" class="exp-bm-toggle" aria-label="{'Open or close the folder'|i18n( 'design/admin/content/bookmark' )}" title="{'Open or close the folder'|i18n( 'design/admin/content/bookmark' )}"><span aria-hidden="true"></span></button>
      <span class="exp-bm-folder-name">{$row.name|wash}</span>
      <span class="exp-bm-count" title="{'%count bookmarks'|i18n( 'design/admin/content/bookmark',, hash( '%count', $row.count ) )}">{$row.count}</span>
    {if eq( $mode, 'page' )}
      <span class="exp-bm-actions exp-bm-js">
        <button type="button" class="exp-bm-act" data-action="up" title="{'Move up'|i18n( 'design/admin/content/bookmark' )}" aria-label="{'Move up'|i18n( 'design/admin/content/bookmark' )}">&uarr;</button>
        <button type="button" class="exp-bm-act" data-action="down" title="{'Move down'|i18n( 'design/admin/content/bookmark' )}" aria-label="{'Move down'|i18n( 'design/admin/content/bookmark' )}">&darr;</button>
        <button type="button" class="exp-bm-act" data-action="newsub" title="{'New folder'|i18n( 'design/admin/content/bookmark' )}">{'New folder'|i18n( 'design/admin/content/bookmark' )}</button>
        <button type="button" class="exp-bm-act" data-action="move" title="{'Move folder'|i18n( 'design/admin/content/bookmark' )}">{'Move'|i18n( 'design/admin/content/bookmark' )}</button>
        <button type="button" class="exp-bm-act" data-action="rename" title="{'Rename folder'|i18n( 'design/admin/content/bookmark' )}">{'Rename'|i18n( 'design/admin/content/bookmark' )}</button>
        <button type="button" class="exp-bm-act exp-bm-danger" data-action="delete" title="{'Delete folder'|i18n( 'design/admin/content/bookmark' )}">{'Delete'|i18n( 'design/admin/content/bookmark' )}</button>
      </span>
    {/if}
    </li>
  {else}
    {set $node = $row.bookmark.node}
    {if $node}
    <li class="exp-bm-row exp-bm-bookmark" role="treeitem" aria-level="{$row.depth|inc}" style="--d:{$row.depth}"
        data-type="bookmark" data-id="{$row.id}" data-parent="{$row.folder_id}" data-name="{$node.name|wash}"{if eq( $mode, 'page' )} draggable="true"{/if}>
      <span class="exp-bm-indent" aria-hidden="true"></span>
      {if eq( $mode, 'page' )}
        <input type="checkbox" name="DeleteIDArray[]" value="{$row.id}" title="{'Select bookmark for removal.'|i18n( 'design/admin/content/bookmark' )}" />
        {$node.class_identifier|class_icon( small, $node.class_name )}&nbsp;<a class="exp-bm-link" href={concat( '/content/view/full/', $row.bookmark.node_id, '/' )|ezurl}>{$node.name|wash}</a>
        <span class="exp-bm-meta">{$node.class_name|wash}{let section_object=fetch( section, object, hash( section_id, $node.object.section_id ) )}{if $section_object} &middot; {$section_object.name|wash}{/if}{/let}</span>
        <span class="exp-bm-actions">
          <span class="exp-bm-js">
            <button type="button" class="exp-bm-act" data-action="up" title="{'Move up'|i18n( 'design/admin/content/bookmark' )}" aria-label="{'Move up'|i18n( 'design/admin/content/bookmark' )}">&uarr;</button>
            <button type="button" class="exp-bm-act" data-action="down" title="{'Move down'|i18n( 'design/admin/content/bookmark' )}" aria-label="{'Move down'|i18n( 'design/admin/content/bookmark' )}">&darr;</button>
            <button type="button" class="exp-bm-act" data-action="move" title="{'Move bookmark'|i18n( 'design/admin/content/bookmark' )}">{'Move'|i18n( 'design/admin/content/bookmark' )}</button>
          </span>
          {if $node.object.can_edit}
            <a class="exp-bm-act" href={concat( 'content/edit/', $node.contentobject_id )|ezurl} title="{'Edit <%bookmark_name>.'|i18n( 'design/admin/content/bookmark',, hash( '%bookmark_name', $node.name ) )|wash}">{'Edit'|i18n( 'design/admin/content/bookmark' )}</a>
          {/if}
        </span>
      {elseif eq( $mode, 'browse' )}
        {if $browse.ignore_nodes_select|contains( $row.bookmark.node_id )|not()}
          {if is_array( $browse.class_array )}
            {if $browse.class_array|contains( $node.class_identifier )}
              <input type="{$select_type}" name="{$select_name}[]" value="{$node[$select_attribute]}" />
            {/if}
          {else}
            <input type="{$select_type}" name="{$select_name}[]" value="{$node[$select_attribute]}" />
          {/if}
        {/if}
        {$node.class_identifier|class_icon( small, $node.class_name )}&nbsp;
        {if and( $browse.ignore_nodes_click|contains( $row.bookmark.node_id )|not, $node.is_container )}
          <a class="exp-bm-link" href={concat( '/content/browse/', $row.bookmark.node_id )|ezurl}>{$node.name|wash}</a>
        {else}
          <span class="exp-bm-link">{$node.name|wash}</span>
        {/if}
      {else}
        {if ne( $ui_context, 'edit' )}
          {if ne( $ui_context, 'browse' )}
            <a href="#" class="exp-bm-menu" onclick="ezpopmenu_showTopLevel( event, 'BookmarkMenu', ez_createAArray( new Array( '%nodeID%', '{$row.bookmark.node_id}', '%objectID%', '{$node.contentobject_id}', '%bookmarkID%', '{$row.id}', '%languages%', {$node.object.language_js_array|wash} ) ) , '{$node.name|shorten(18)|wash(javascript)}'); return false;">{$node.class_identifier|class_icon( small, '[%classname] Click on the icon to display a context-sensitive menu.'|i18n( 'design/admin/pagelayout',, hash( '%classname', $node.class_name ) ) )}</a>&nbsp;<a class="exp-bm-link" href={if $node.url|eq('')}{concat('/content/view/full/', $row.bookmark.node_id)|ezurl}{else}{$node.url_alias|ezurl}{/if}>{$node.name|wash}</a>
          {else}
            {$node.class_identifier|class_icon( small, $node.class_name )}&nbsp;{if $node.is_container}<a class="exp-bm-link" href={concat( '/content/browse/', $node.node_id )|ezurl}>{$node.name|wash}</a>{else}<span class="exp-bm-link">{$node.name|wash}</span>{/if}
          {/if}
        {else}
          {$node.class_identifier|class_icon( ghost, $node.class_name )}&nbsp;<span class="disabled exp-bm-link">{$node.name|wash}</span>
        {/if}
      {/if}
    </li>
    {/if}
  {/if}
{/foreach}
</ul>
{undef $node $tree_rows}
{/default}
