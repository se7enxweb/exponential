{if fetch( 'user', 'has_access_to', hash( 'module', 'content', 'function', 'bookmark' ) )}

<div id="bookmarks">
{* DESIGN: Header START *}<div class="box-header"><div class="box-ml">

    {if and( ne( $ui_context, 'edit' ), ne( $ui_context, 'browse' ) )}
        <h4>{'Bookmarks'|i18n( 'design/admin/pagelayout' )}</h4>
    {else}
     {if eq( $ui_context, 'edit' )}
        <h4><span class="disabled">{'Bookmarks'|i18n( 'design/admin/pagelayout' )}</span></h4>
     {else}
        <h4>{'Bookmarks'|i18n( 'design/admin/pagelayout' )}</h4>
     {/if}
    {/if}

{* DESIGN: Header END *}</div></div>

{* DESIGN: Content START *}<div class="box-bc"><div class="box-ml"><div class="box-content">


    {def $bookmark_rows = fetch( 'content', 'bookmark_rows', hash() )
         $bookmark_folders = fetch( 'content', 'bookmark_folders', hash() )}
    {if $bookmark_rows}
    <div class="exp-bm-scroll">
    {include uri='design:content/bookmark_tree.tpl' mode='sidebar' rows=$bookmark_rows ui_context=$ui_context}
    </div>
    {/if}


    <div class="block">
    {* Show "Add to bookmarks" button if we're viewing an actual node. *}
    {if and( is_set( $module_result.content_info.node_id ), $ui_context|ne( 'edit' ), $ui_context|ne( 'browse' ) )}
	    <form method="post" action={'content/action'|ezurl}>
	    <input type="hidden" name="ContentNodeID" value="{$module_result.content_info.node_id}" />
	    {if $bookmark_folders}
	    <label class="exp-bm-sr" for="exp-bm-add-folder">{'Folder'|i18n( 'design/admin/content/bookmark' )}</label>
	    <select name="BookmarkFolderID" id="exp-bm-add-folder" class="exp-bm-add-folder">
	        <option value="0">{'Top level'|i18n( 'design/admin/content/bookmark' )}</option>
	        {foreach $bookmark_folders as $folder}
	        <option value="{$folder.id}">{'&nbsp;&nbsp;'|repeat( $folder.depth )}{$folder.name|wash}</option>
	        {/foreach}
	    </select>
	    {/if}
	    <input class="button" type="submit" name="ActionAddToBookmarks" value="{'Add to bookmarks'|i18n( 'design/admin/pagelayout' )}" title="{'Add the current item to your bookmarks.'|i18n( 'design/admin/pagelayout' )}" />
	    </form>
    {else}
	    <form method="post" action={'content/action'|ezurl}>
	    <input class="button-disabled" type="submit" value="{'Add to bookmarks'|i18n( 'design/admin/pagelayout' )}" disabled="disabled" />
	    </form>
	{/if}
    </div>
    {undef $bookmark_rows $bookmark_folders}

{* DESIGN: Content END *}</div></div></div>                     
</div>

{/if}