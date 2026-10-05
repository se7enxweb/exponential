{* My bookmarks: the bookmarks of the user in a tree of virtual folders. The folders, moving, renaming and
   deleting work without JavaScript through the form at the end; with JavaScript the tree opens and closes, searches,
   and entries move by drag and drop or with the buttons (the keyboard does the same as the mouse). *}
{def $bookmark_list = fetch( 'content', 'bookmarks', hash() )
     $bookmark_rows = fetch( 'content', 'bookmark_rows', hash() )
     $bookmark_folders = fetch( 'content', 'bookmark_folders', hash() )
     $bookmark_node = 0}
<form name="bookmarkaction" id="exp-bm-form" action={concat( 'content/bookmark/' )|ezurl} method="post" >

<div class="context-block content-bookmark exp-bm-page" data-exp-bm-page="1">
{* DESIGN: Header START *}<div class="box-header"><div class="box-ml">
<h1 class="context-title">{'My bookmarks (%bookmark_count)'|i18n( 'design/admin/content/bookmark',, hash( '%bookmark_count', $bookmark_list|count ) )}</h1>

{* DESIGN: Mainline *}<div class="header-mainline"></div>

{* DESIGN: Header END *}</div></div>

{* DESIGN: Content START *}<div class="box-ml"><div class="box-mr"><div class="box-content">

{if is_set( $bookmark_notice )}
    <div class="message-{if eq( $bookmark_notice.level, 'error' )}error{else}feedback{/if}" role="status"><h2>{$bookmark_notice.text|wash}</h2></div>
{/if}

{if or( $bookmark_list, $bookmark_folders )}
<div class="exp-bm-toolbar exp-bm-js">
    <label class="exp-bm-search"><span class="exp-bm-sr">{'Search bookmarks'|i18n( 'design/admin/content/bookmark' )}</span>
        <input type="search" class="halfbox" id="exp-bm-search" placeholder="{'Search bookmarks'|i18n( 'design/admin/content/bookmark' )}" autocomplete="off" /></label>
    <button type="button" class="button" data-exp-bm-all="open">{'Expand all'|i18n( 'design/admin/content/bookmark' )}</button>
    <button type="button" class="button" data-exp-bm-all="close">{'Collapse all'|i18n( 'design/admin/content/bookmark' )}</button>
    <button type="button" class="button" data-exp-bm-new="0">{'New folder'|i18n( 'design/admin/content/bookmark' )}</button>
</div>
<p class="exp-bm-hint exp-bm-js">{'Drag a bookmark or folder onto a folder to move it; the buttons do the same from the keyboard.'|i18n( 'design/admin/content/bookmark' )}</p>
<div class="exp-bm-root-drop" data-exp-bm-root="1">{'Top level'|i18n( 'design/admin/content/bookmark' )}</div>
<p class="exp-bm-nomatch" hidden="hidden">{'No bookmarks match.'|i18n( 'design/admin/content/bookmark' )}</p>

{include uri='design:content/bookmark_tree.tpl' mode='page' rows=$bookmark_rows}
{else}
    <div class="block">
    <p>{'There are no bookmarks in the list.'|i18n( 'design/admin/content/bookmark' )}</p>
    </div>
{/if}

{* DESIGN: Content END *}</div></div></div>

<div class="controlbar">
{* DESIGN: Control bar START *}<div class="box-bc"><div class="box-ml">
<div class="block">

{if $bookmark_list}
<input class="button" type="submit" name="RemoveButton" value="{'Remove selected'|i18n( 'design/admin/content/bookmark' )}" title="{'Remove selected bookmarks.'|i18n( 'design/admin/content/bookmark' )}" />
{else}
<input class="button-disabled" type="submit" name="RemoveButton" value="{'Remove selected'|i18n( 'design/admin/content/bookmark' )}" disabled="disabled" />
{/if}

<input class="button" type="submit" name="AddButton" value="{'Add items'|i18n( 'design/admin/content/bookmark' )}" title="{'Add items to your personal bookmark list.'|i18n( 'design/admin/content/bookmark' )}" />
</div>
{if $bookmark_list}
<div class="block exp-bm-move-selected">
    <label for="exp-bm-move-target">{'Move selected to'|i18n( 'design/admin/content/bookmark' )}</label>
    <select name="FolderID" id="exp-bm-move-target">
        <option value="0">{'Top level'|i18n( 'design/admin/content/bookmark' )}</option>
        {foreach $bookmark_folders as $folder}
        <option value="{$folder.id}">{'&nbsp;&nbsp;'|repeat( $folder.depth )}{$folder.name|wash}</option>
        {/foreach}
    </select>
    <input type="hidden" name="BookmarkFolderActionDefault" value="move_bookmark" />
    <button class="button" type="submit" name="MoveSelectedButton" value="1" formaction={'content/bookmark/'|ezurl}>{'Move selected'|i18n( 'design/admin/content/bookmark' )}</button>
</div>
{/if}
{* DESIGN: Control bar END *}</div></div>
</div>

</div>

</form>

{include uri='design:content/bookmark_folder_forms.tpl' folders=$bookmark_folders}

{undef $bookmark_list $bookmark_rows $bookmark_folders $bookmark_node}
