{* My bookmarks (content/bookmark): the bookmarks of the user grouped by folder.

   What bookmarks are for, an overview, the folders with their counts beside the list, the folder that is shown
   with Rename, New folder, Move, the order and Remove (each opens in place and says what happens to the bookmarks),
   a search and an order, then one card per bookmark with its type, where it is, when it was last modified and
   whether it is hidden or no longer there, and a bar to move or remove the selected bookmarks.

   Everything comes from the view (bookmark_page, see expBookmarkPage) and works without javascript: in the user's own
   order each card has Move up, Move down and a position field. The script adds Select all, the selection count and
   arranging by drag and drop: a grip per card and per folder (dragged, or the up and down arrow keys on it) saves
   the new order of one folder with one POST (BookmarkOrderButton, OrderType, OrderFolderID, OrderIDs), and dropping
   a bookmark on a folder of the folder list moves it there. Every POST name of the page is the one it always had
   (RemoveButton, AddButton, DeleteIDArray, MoveSelectedButton, FolderID, BookmarkFolderAction ...), with
   BookmarkShiftButton, BookmarkOrderButton and BookmarkPosition* added. The same file is in design/admin and
   design/admin4. Guide: doc/guides/bookmarks.md *}
{include uri='design:content/bookmark_exp_style.tpl'}
{def $bp = $bookmark_page
     $summary = $bp.summary
     $current = $bp.current
     $folder_icon = '<svg width="16" height="16" viewBox="0 0 16 16" aria-hidden="true" focusable="false"><path fill="currentColor" d="M1.5 3A1.5 1.5 0 0 1 3 1.5h3.1c.4 0 .78.16 1.06.44L8.2 3H13a1.5 1.5 0 0 1 1.5 1.5v8A1.5 1.5 0 0 1 13 14H3a1.5 1.5 0 0 1-1.5-1.5V3Zm1.5-.5a.5.5 0 0 0-.5.5v9.5a.5.5 0 0 0 .5.5h10a.5.5 0 0 0 .5-.5v-8a.5.5 0 0 0-.5-.5H8a.5.5 0 0 1-.35-.15L6.45 2.65a.5.5 0 0 0-.35-.15H3Z"/></svg>'
     $scope_title = ''
     $group_open = false()
     $group_info = false()
     $card_id = ''}
{switch match=$bp.scope_key}
{case match='top'}{set $scope_title = 'Not in a folder'|i18n( 'design/admin/content/bookmark' )}{/case}
{case match='folder'}{set $scope_title = $current.name}{/case}
{case}{set $scope_title = 'All bookmarks'|i18n( 'design/admin/content/bookmark' )}{/case}
{/switch}

<div class="context-block exp-bm">

<div class="box-header"><div class="box-ml">
<h1 class="context-title">{'My bookmarks (%bookmark_count)'|i18n( 'design/admin/content/bookmark',, hash( '%bookmark_count', $summary.bookmarks ) )}</h1>
</div></div>

<div class="box-bc"><div class="box-ml"><div class="box-content">

<p class="exp-intro">{'Bookmarks take you back to the items you work on: they are personal, nobody else sees them, and removing one never changes the item. Sort them into folders; the Bookmarks box at the side and the browse dialog show the same folders.'|i18n( 'design/admin/content/bookmark' )}</p>

{if is_set( $bookmark_notice )}
<div class="exp-feedback {if eq( $bookmark_notice.level, 'error' )}is-bad{else}is-ok{/if}" role="{if eq( $bookmark_notice.level, 'error' )}alert{else}status{/if}">{$bookmark_notice.text|wash}</div>
{/if}

<section aria-labelledby="bm-overview-title">
<h2 class="exp-sr" id="bm-overview-title">{'Overview'|i18n( 'design/admin/content/bookmark' )}</h2>
<ul class="exp-figures">
    <li class="exp-figure{if eq( $bp.scope_key, 'all' )} is-current{/if}"><a href={'content/bookmark'|ezurl}><strong>{$summary.bookmarks}</strong> <span>{'Bookmarks'|i18n( 'design/admin/content/bookmark' )}</span></a></li>
    <li class="exp-figure"><strong>{$summary.folders}</strong> <span>{'Folders'|i18n( 'design/admin/content/bookmark' )}</span></li>
    <li class="exp-figure{if eq( $bp.scope_key, 'top' )} is-current{/if}"><a href={'content/bookmark/(folder)/top'|ezurl}><strong>{$summary.unfiled}</strong> <span>{'Not in a folder'|i18n( 'design/admin/content/bookmark' )}</span></a></li>
    <li class="exp-figure"><strong>{$summary.hidden}</strong> <span>{'Hidden items'|i18n( 'design/admin/content/bookmark' )}</span></li>
    <li class="exp-figure{if $summary.gone|gt( 0 )} is-attention{/if}"><strong>{$summary.gone}</strong> <span>{'No longer available'|i18n( 'design/admin/content/bookmark' )}</span></li>
</ul>
</section>

<div class="exp-bm-layout">

{* ---- The folders ---- *}
<div class="exp-bm-side">
<nav class="exp-panel" aria-labelledby="bm-folders-title">
    <div class="exp-panel-head"><h2 class="exp-h2" id="bm-folders-title">{'Folders'|i18n( 'design/admin/content/bookmark' )}</h2></div>
    <ul class="exp-bm-nav" id="bm-folder-nav" data-drop-label="{'Move into this folder'|i18n( 'design/admin/content/bookmark' )|wash}">
        <li>{if eq( $bp.scope_key, 'all' )}<span class="current" aria-current="page">{else}<a href={concat( 'content/bookmark', cond( $bp.sort|ne( 'own' ), concat( '/(sort)/', $bp.sort ), '' ), $bp.search_suffix )|ezurl}>{/if}<span class="exp-bm-navname">{'All bookmarks'|i18n( 'design/admin/content/bookmark' )}</span> <span class="exp-count">{$summary.bookmarks}</span>{if eq( $bp.scope_key, 'all' )}</span>{else}</a>{/if}</li>
        <li data-folder="0">{if eq( $bp.scope_key, 'top' )}<span class="current" aria-current="page">{else}<a href={concat( 'content/bookmark/(folder)/top', cond( $bp.sort|ne( 'own' ), concat( '/(sort)/', $bp.sort ), '' ), $bp.search_suffix )|ezurl}>{/if}<span class="exp-bm-navname">{'Not in a folder'|i18n( 'design/admin/content/bookmark' )}</span> <span class="exp-count">{$summary.unfiled}</span>{if eq( $bp.scope_key, 'top' )}</span>{else}</a>{/if}</li>
        {if $bp.folders}<li class="is-sep" role="presentation"></li>{/if}
        {foreach $bp.folders as $folder}
        <li class="d{min( $folder.depth, 4 )}" data-folder="{$folder.id}" data-parent="{$folder.parent_id}"><span class="exp-grip exp-bm-fgrip" hidden draggable="true" data-folder="{$folder.id}" aria-label="{'Arrange folder %name: drag it, or press the up and down arrow keys'|i18n( 'design/admin/content/bookmark',, hash( '%name', $folder.name ) )|wash}" title="{'Drag to arrange, or use the up and down arrow keys'|i18n( 'design/admin/content/bookmark' )}" role="button" tabindex="0"><span aria-hidden="true">&#10303;</span></span>{if and( $current, eq( $current.id, $folder.id ) )}<span class="current" aria-current="page">{else}<a href={concat( 'content/bookmark/(folder)/', $folder.id, cond( $bp.sort|ne( 'own' ), concat( '/(sort)/', $bp.sort ), '' ), $bp.search_suffix )|ezurl}>{/if}{$folder_icon}<span class="exp-bm-navname">{$folder.name|wash}</span> <span class="exp-count" title="{'%count bookmarks, with the folders inside'|i18n( 'design/admin/content/bookmark',, hash( '%count', $folder.count ) )}">{$folder.count}</span>{if and( $current, eq( $current.id, $folder.id ) )}</span>{else}</a>{/if}</li>
        {/foreach}
    </ul>
    {if $bp.folders|not}<p class="exp-help exp-mt">{'No folders yet. Create one below, then move bookmarks into it.'|i18n( 'design/admin/content/bookmark' )}</p>{/if}
    <p class="exp-help exp-bm-draghint" hidden>{'Tip: drag a bookmark onto a folder here to move it.'|i18n( 'design/admin/content/bookmark' )}</p>
</nav>

<details class="exp-disclosure"{if $bp.folders|not} open{/if}>
    <summary>{'New folder'|i18n( 'design/admin/content/bookmark' )}</summary>
    <div>
    <form method="post" action={$bp.here|ezurl} class="exp-inline-form">
        <input type="hidden" name="BookmarkFolderAction" value="create" />
        <div class="exp-field">
            <label for="bm-new-name">{'Name'|i18n( 'design/admin/content/bookmark' )}</label>
            <input type="text" id="bm-new-name" name="FolderName" maxlength="255" required="required" autocomplete="off" />
        </div>
        <div class="exp-field">
            <label for="bm-new-parent">{'Inside'|i18n( 'design/admin/content/bookmark' )}</label>
            <select name="ParentFolderID" id="bm-new-parent">
                <option value="0">{'No folder (top level)'|i18n( 'design/admin/content/bookmark' )}</option>
                {foreach $bp.targets as $target}
                <option value="{$target.id}"{if and( $current, eq( $current.id, $target.id ) )} selected="selected"{/if}>{$target.label|wash}</option>
                {/foreach}
            </select>
        </div>
        <div class="exp-actions"><button type="submit" class="exp-btn exp-btn-primary">{'Create folder'|i18n( 'design/admin/content/bookmark' )}</button></div>
    </form>
    </div>
</details>
</div>

<div class="exp-bm-main">

{* ---- The folder that is shown ---- *}
{if $current}
<section class="exp-panel exp-bm-folderhead" aria-labelledby="bm-folder-title">
    <ol class="exp-bm-crumbs" aria-label="{'Folder path'|i18n( 'design/admin/content/bookmark' )}">
        <li><a href={'content/bookmark'|ezurl}>{'All bookmarks'|i18n( 'design/admin/content/bookmark' )}</a></li>
        {foreach $current.path as $crumb}<li>{$crumb|wash}</li>{/foreach}
    </ol>
    <h2 class="exp-h2" id="bm-folder-title">{$current.name|wash}</h2>
    <dl class="exp-facts exp-bm-folderfacts">
        <div><dt>{'Bookmarks in it'|i18n( 'design/admin/content/bookmark' )}</dt><dd>{$current.direct}</dd></div>
        <div><dt>{'Including subfolders'|i18n( 'design/admin/content/bookmark' )}</dt><dd>{$current.count}</dd></div>
        <div><dt>{'Folders inside'|i18n( 'design/admin/content/bookmark' )}</dt><dd>{$current.subfolders}</dd></div>
    </dl>

    <div class="exp-bm-folderacts">
        <details class="exp-disclosure">
            <summary>{'Rename'|i18n( 'design/admin/content/bookmark' )}</summary>
            <div>
            <form method="post" action={$bp.here|ezurl} class="exp-inline-form">
                <input type="hidden" name="BookmarkFolderAction" value="rename" />
                <input type="hidden" name="FolderID" value="{$current.id}" />
                <div class="exp-field">
                    <label for="bm-rename">{'New name'|i18n( 'design/admin/content/bookmark' )}</label>
                    <input type="text" id="bm-rename" name="FolderName" value="{$current.name|wash}" maxlength="255" required="required" autocomplete="off" />
                </div>
                <div class="exp-actions"><button type="submit" class="exp-btn exp-btn-primary">{'Save name'|i18n( 'design/admin/content/bookmark' )}</button></div>
            </form>
            </div>
        </details>
        <details class="exp-disclosure">
            <summary>{'Move'|i18n( 'design/admin/content/bookmark' )}</summary>
            <div>
            <form method="post" action={$bp.here|ezurl} class="exp-inline-form">
                <input type="hidden" name="BookmarkFolderAction" value="move_folder" />
                <input type="hidden" name="FolderID" value="{$current.id}" />
                <div class="exp-field">
                    <label for="bm-move-folder">{'Put this folder inside'|i18n( 'design/admin/content/bookmark' )}</label>
                    <select name="ParentFolderID" id="bm-move-folder">
                        <option value="0"{if eq( $current.parent_id, 0 )} selected="selected"{/if}>{'No folder (top level)'|i18n( 'design/admin/content/bookmark' )}</option>
                        {foreach $bp.current_targets as $target}
                        <option value="{$target.id}"{if eq( $current.parent_id, $target.id )} selected="selected"{/if}>{$target.label|wash}</option>
                        {/foreach}
                    </select>
                    <span class="exp-help">{'Its bookmarks and folders move with it.'|i18n( 'design/admin/content/bookmark' )}</span>
                </div>
                <div class="exp-actions"><button type="submit" class="exp-btn exp-btn-primary">{'Move folder'|i18n( 'design/admin/content/bookmark' )}</button></div>
            </form>
            {if or( $bp.current_siblings.first|not, $bp.current_siblings.last|not )}
            <form method="post" action={$bp.here|ezurl} class="exp-inline-form exp-mt">
                <span class="exp-field-label">{'Order among its neighbours'|i18n( 'design/admin/content/bookmark' )}</span>
                <div class="exp-actions exp-actions-tight">
                    {if $bp.current_siblings.first|not}<button type="submit" class="exp-btn exp-btn-small" name="BookmarkShiftButton" value="fup-{$current.id}">{'Move up'|i18n( 'design/admin/content/bookmark' )}</button>{/if}
                    {if $bp.current_siblings.last|not}<button type="submit" class="exp-btn exp-btn-small" name="BookmarkShiftButton" value="fdown-{$current.id}">{'Move down'|i18n( 'design/admin/content/bookmark' )}</button>{/if}
                </div>
            </form>
            {/if}
            </div>
        </details>
        <details class="exp-disclosure is-danger">
            <summary>{'Remove folder'|i18n( 'design/admin/content/bookmark' )}</summary>
            <div>
            <form method="post" action={$bp.here|ezurl} class="exp-inline-form">
                <input type="hidden" name="BookmarkFolderAction" value="delete" />
                <input type="hidden" name="FolderID" value="{$current.id}" />
                <fieldset class="exp-choice">
                    <legend>{'What happens to what is inside?'|i18n( 'design/admin/content/bookmark' )}</legend>
                    <label class="exp-check"><input type="radio" name="DeleteBookmarks" value="0" checked="checked" />
                        <span>{'Keep what is inside: it moves to %target (bookmarks: %count, folders: %folders).'|i18n( 'design/admin/content/bookmark',, hash( '%count', $current.direct, '%folders', $current.children|count, '%target', cond( $bp.current_parent, concat( '“', $bp.current_parent.name, '”' ), 'the top level'|i18n( 'design/admin/content/bookmark' ) ) ) )|wash}</span></label>
                    <label class="exp-check"><input type="radio" name="DeleteBookmarks" value="1" />
                        <span>{'Remove everything inside with it (bookmarks: %all, folders: %folders).'|i18n( 'design/admin/content/bookmark',, hash( '%all', $current.count, '%folders', $current.subfolders ) )}</span></label>
                </fieldset>
                <p class="exp-help">{'Only bookmarks are removed, never the items they point to.'|i18n( 'design/admin/content/bookmark' )}</p>
                <div class="exp-actions"><button type="submit" class="exp-btn exp-btn-danger">{'Remove folder'|i18n( 'design/admin/content/bookmark' )}</button></div>
            </form>
            </div>
        </details>
    </div>
</section>
{/if}

{* ---- Search and order ---- *}
<section aria-labelledby="bm-find-title">
<h2 class="exp-sr" id="bm-find-title">{'Find bookmarks'|i18n( 'design/admin/content/bookmark' )}</h2>
<form class="exp-toolbar" method="get" action={$bp.base_sorted|ezurl} role="search">
    <div class="exp-field">
        <label for="bm-search">{'Find a bookmark'|i18n( 'design/admin/content/bookmark' )}</label>
        <div class="exp-searchrow">
            <input type="search" id="bm-search" name="q" value="{$bp.search|wash}" autocomplete="off" maxlength="100" aria-describedby="bm-search-help" />
            <button type="submit" class="exp-btn exp-btn-primary">{'Search'|i18n( 'design/admin/content/bookmark' )}</button>
            {if $bp.search|ne( '' )}<a class="exp-btn" href={$bp.base_sorted|ezurl}>{'Clear search'|i18n( 'design/admin/content/bookmark' )}</a>{/if}
        </div>
        <span class="exp-help" id="bm-search-help">{if eq( $bp.scope_key, 'all' )}{'Searches the names, types, locations and folders of all your bookmarks.'|i18n( 'design/admin/content/bookmark' )}{else}{'Searches this folder only. Choose All bookmarks to search everywhere.'|i18n( 'design/admin/content/bookmark' )}{/if}</span>
    </div>
    <div class="exp-field">
        <span class="exp-field-label" id="bm-sort-label">{'Order'|i18n( 'design/admin/content/bookmark' )}</span>
        <ul class="exp-tabs" aria-labelledby="bm-sort-label">
        {foreach array( hash( 'sort', 'own', 'text', 'Your order'|i18n( 'design/admin/content/bookmark' ) ),
                        hash( 'sort', 'name', 'text', 'Name A to Z'|i18n( 'design/admin/content/bookmark' ) ),
                        hash( 'sort', 'added', 'text', 'Recently added'|i18n( 'design/admin/content/bookmark' ) ),
                        hash( 'sort', 'type', 'text', 'Type'|i18n( 'design/admin/content/bookmark' ) ),
                        hash( 'sort', 'modified', 'text', 'Recently modified'|i18n( 'design/admin/content/bookmark' ) ) ) as $tab}
            <li>{if eq( $tab.sort, $bp.sort )}<span class="current" aria-current="true">{$tab.text|wash}</span>{else}<a href={concat( $bp.base, cond( $tab.sort|ne( 'own' ), concat( '/(sort)/', $tab.sort ), '' ), $bp.search_suffix )|ezurl}>{$tab.text|wash}</a>{/if}</li>
        {/foreach}
        </ul>
        <span class="exp-help">{'Bookmarks stay grouped by folder in every order. Your order is the one of the Bookmarks box.'|i18n( 'design/admin/content/bookmark' )}</span>
    </div>
</form>
</section>

{* ---- The bookmarks ---- *}
<form name="bookmarkaction" id="bm-list-form" method="post" action={$bp.here|ezurl}>
{* the first button of the form: Enter in a position field moves that bookmark, never adds items *}
<button type="submit" class="exp-sr" name="BookmarkPositionDefault" value="1" tabindex="-1" aria-hidden="true">{'Move'|i18n( 'design/admin/content/bookmark' )}</button>
<section aria-labelledby="bm-list-title">
<div class="exp-section-head">
    <h2 class="exp-h2" id="bm-list-title">{if $bp.search|ne( '' )}{'Bookmarks matching “%search” in %scope'|i18n( 'design/admin/content/bookmark',, hash( '%search', $bp.search, '%scope', $scope_title ) )|wash}{else}{$scope_title|wash}{/if}</h2>
    {if $bp.count|gt( 0 )}
    <span class="exp-meta">{'%from to %to of %count'|i18n( 'design/admin/content/bookmark',, hash( '%from', sum( $bp.offset, 1 ), '%to', min( sum( $bp.offset, $bp.limit ), $bp.count ), '%count', $bp.count ) )}</span>
    <label class="exp-meta exp-bm-selectall" hidden><input type="checkbox" id="bm-select-all" /> {'Select all on this page'|i18n( 'design/admin/content/bookmark' )}</label>
    <button type="submit" class="exp-btn exp-btn-primary exp-btn-small exp-bm-addtop" name="AddButton" value="1" title="{if ne( $bp.scope_key, 'all' )}{'Items you add go into this folder.'|i18n( 'design/admin/content/bookmark' )}{else}{'Add items to your personal bookmark list.'|i18n( 'design/admin/content/bookmark' )}{/if}">{'Add items'|i18n( 'design/admin/content/bookmark' )}</button>
    {/if}
</div>
{if $bp.count|gt( 0 )}
{if $bp.order_buttons}
<p class="exp-help exp-bm-arrange" id="bm-grip-help">{'To arrange, drag a bookmark by its grip to a new place in its folder, or drop it on a folder in the folder list to move it there. On a focused grip the up and down arrow keys move it one place; the position field moves it anywhere in its folder, also across pages.'|i18n( 'design/admin/content/bookmark' )}</p>
{elseif $bp.search|ne( '' )}
<p class="exp-help exp-bm-arrange">{'Clear the search to arrange your bookmarks.'|i18n( 'design/admin/content/bookmark' )} <a href={$bp.own_path|ezurl}>{'Your order'|i18n( 'design/admin/content/bookmark' )}</a></p>
{else}
<p class="exp-help exp-bm-arrange"><a href={$bp.own_path|ezurl}>{'Switch to Your order to arrange'|i18n( 'design/admin/content/bookmark' )}</a>. {'Dropping a bookmark on a folder in the folder list moves it there in every order.'|i18n( 'design/admin/content/bookmark' )}</p>
{/if}
{/if}

{if $bp.count|eq( 0 )}
<div class="exp-empty">
{if $bp.search|ne( '' )}
    <strong>{'No bookmark matches this search.'|i18n( 'design/admin/content/bookmark' )}</strong>
    {'Check the spelling, search for a shorter part of the name, or search all bookmarks.'|i18n( 'design/admin/content/bookmark' )}
    <div class="exp-actions"><a class="exp-btn" href={$bp.base_sorted|ezurl}>{'Clear search'|i18n( 'design/admin/content/bookmark' )}</a>{if ne( $bp.scope_key, 'all' )} <a class="exp-btn" href={concat( 'content/bookmark', $bp.search_suffix )|ezurl}>{'Search all bookmarks'|i18n( 'design/admin/content/bookmark' )}</a>{/if}</div>
{elseif $summary.bookmarks|eq( 0 )}
    <strong>{'You have no bookmarks yet.'|i18n( 'design/admin/content/bookmark' )}</strong>
    {'Add items here, or choose Add to bookmarks in the menu of any item in the content tree.'|i18n( 'design/admin/content/bookmark' )}
    <div class="exp-actions"><button type="submit" class="exp-btn exp-btn-primary" name="AddButton" value="1">{'Add items'|i18n( 'design/admin/content/bookmark' )}</button></div>
{elseif eq( $bp.scope_key, 'top' )}
    <strong>{'Every bookmark is in a folder.'|i18n( 'design/admin/content/bookmark' )}</strong>
{else}
    <strong>{'This folder is empty.'|i18n( 'design/admin/content/bookmark' )}</strong>
    {'Add items to it, or select bookmarks in another folder and move them here.'|i18n( 'design/admin/content/bookmark' )}
    <div class="exp-actions"><button type="submit" class="exp-btn exp-btn-primary" name="AddButton" value="1">{'Add items'|i18n( 'design/admin/content/bookmark' )}</button></div>
{/if}
</div>
{else}
<div id="bm-list">
{foreach $bp.items as $item}
    {if $item.group_start}
        {if $group_open}</ul></section>{/if}
        {set $group_open = true()}
        {if $item.group|gt( 0 )}{set $group_info = $bp.folder_names[$item.group]}{else}{set $group_info = false()}{/if}
<section class="exp-bm-group" aria-labelledby="bm-group-{$item.group}">
    <div class="exp-bm-grouphead">
        {if $group_info}{$folder_icon}{/if}
        <h3 class="exp-h3" id="bm-group-{$item.group}">{if $group_info}{$group_info.name|wash}{else}{'Not in a folder'|i18n( 'design/admin/content/bookmark' )}{/if}</h3>
        <span class="exp-badge">{if $item.group_count|eq( 1 )}{'1 bookmark'|i18n( 'design/admin/content/bookmark' )}{else}{'%count bookmarks'|i18n( 'design/admin/content/bookmark',, hash( '%count', $item.group_count ) )}{/if}</span>
        {if $item.group_continued}<span class="exp-meta">{'continued from the previous page'|i18n( 'design/admin/content/bookmark' )}</span>{/if}
        {if and( $group_info, $group_info.path )}<p class="exp-bm-grouppath">{'in %path'|i18n( 'design/admin/content/bookmark',, hash( '%path', $group_info.path|implode( ' / ' ) ) )|wash}</p>{/if}
    </div>
    <ul class="exp-cards" data-folder="{$item.group}">
    {/if}
    {set $card_id = concat( 'bm-', $item.id )}
    <li class="exp-card{if or( eq( $item.state, 'gone' ), eq( $item.state, 'denied' ) )} is-bad{elseif or( eq( $item.state, 'hidden' ), eq( $item.state, 'invisible' ) )} is-attention{/if}" id="{$card_id}" data-bookmark="{$item.id}">
        <div class="exp-card-head">
            <div class="exp-card-title">
                {if $bp.order_buttons}<span class="exp-grip" hidden draggable="true" data-bookmark="{$item.id}" aria-describedby="bm-grip-help" aria-label="{'Arrange %name: drag it, or press the up and down arrow keys'|i18n( 'design/admin/content/bookmark',, hash( '%name', $item.name ) )|wash}" title="{'Drag to arrange, or use the up and down arrow keys'|i18n( 'design/admin/content/bookmark' )}" role="button" tabindex="0"><span aria-hidden="true">&#10303;</span></span>{/if}
                <label class="exp-select" title="{'Select this bookmark.'|i18n( 'design/admin/content/bookmark' )}">
                    <input type="checkbox" name="DeleteIDArray[]" value="{$item.id}" aria-label="{'Select %name'|i18n( 'design/admin/content/bookmark',, hash( '%name', $item.name ) )|wash}" />
                </label>
                {if $item.class_identifier}{$item.class_identifier|class_icon( small, $item.class_name )}{/if}
                <h4 id="{$card_id}-title">{if or( eq( $item.state, 'gone' ), eq( $item.state, 'denied' ) )}{$item.name|wash}{else}<a href={concat( 'content/view/full/', $item.node_id )|ezurl}>{$item.name|wash}</a>{/if}</h4>
                <ul class="exp-badges">
                    {if $item.class_name}<li class="exp-badge">{$item.class_name|wash}</li>{/if}
                    {switch match=$item.state}
                    {case match='hidden'}<li class="exp-badge is-warn" title="{'Hidden: visitors of the site do not see it.'|i18n( 'design/admin/content/bookmark' )}">{'Hidden'|i18n( 'design/admin/content/bookmark' )}</li>{/case}
                    {case match='invisible'}<li class="exp-badge is-warn" title="{'An item above it is hidden, so visitors of the site do not see it.'|i18n( 'design/admin/content/bookmark' )}">{'Hidden by a parent'|i18n( 'design/admin/content/bookmark' )}</li>{/case}
                    {case match='gone'}<li class="exp-badge is-bad">{'Not found'|i18n( 'design/admin/content/bookmark' )}</li>{/case}
                    {case match='denied'}<li class="exp-badge is-bad">{'No access'|i18n( 'design/admin/content/bookmark' )}</li>{/case}
                    {case}{/case}
                    {/switch}
                </ul>
            </div>
            <div class="exp-actions exp-actions-tight">
                {if or( eq( $item.state, 'gone' ), eq( $item.state, 'denied' ) )|not}
                <a class="exp-btn exp-btn-small" href={concat( 'content/view/full/', $item.node_id )|ezurl} aria-describedby="{$card_id}-title">{'View'|i18n( 'design/admin/content/bookmark' )}</a>
                {if $item.can_edit}<a class="exp-btn exp-btn-small" href={concat( 'content/edit/', $item.contentobject_id )|ezurl} aria-describedby="{$card_id}-title">{'Edit'|i18n( 'design/admin/content/bookmark' )}</a>{/if}
                {/if}
                {if $bp.order_buttons}
                <button type="submit" class="exp-btn exp-btn-small exp-btn-icon" name="BookmarkShiftButton" value="up-{$item.id}" title="{'Move up'|i18n( 'design/admin/content/bookmark' )}" aria-label="{'Move %name up'|i18n( 'design/admin/content/bookmark',, hash( '%name', $item.name ) )|wash}"{if $bp.first_last.first|contains( $item.id )} disabled="disabled"{/if}><svg width="14" height="14" viewBox="0 0 16 16" aria-hidden="true" focusable="false"><path fill="currentColor" d="M8 3.5 13 9l-1.06 1.06L8.75 6.8V13h-1.5V6.8l-3.19 3.26L3 9z"/></svg></button>
                <button type="submit" class="exp-btn exp-btn-small exp-btn-icon" name="BookmarkShiftButton" value="down-{$item.id}" title="{'Move down'|i18n( 'design/admin/content/bookmark' )}" aria-label="{'Move %name down'|i18n( 'design/admin/content/bookmark',, hash( '%name', $item.name ) )|wash}"{if $bp.first_last.last|contains( $item.id )} disabled="disabled"{/if}><svg width="14" height="14" viewBox="0 0 16 16" aria-hidden="true" focusable="false"><path fill="currentColor" d="M8 12.5 3 7l1.06-1.06 3.19 3.26V3h1.5v6.2l3.19-3.26L13 7z"/></svg></button>
                {/if}
            </div>
        </div>
        <div class="exp-bm-cardfoot">
        {if eq( $item.state, 'gone' )}
        <p class="exp-bm-note">{'The item was removed, is in the trash, or is not in a language of this site. Remove the bookmark, or restore the item.'|i18n( 'design/admin/content/bookmark' )}</p>
        {elseif eq( $item.state, 'denied' )}
        <p class="exp-bm-note">{'You may no longer read this item. Remove the bookmark, or ask an administrator for access.'|i18n( 'design/admin/content/bookmark' )}</p>
        {else}
        <dl class="exp-facts">
            <div>
                <dt>{'Location'|i18n( 'design/admin/content/bookmark' )}</dt>
                <dd>{if $item.path}{$item.path|implode( ' / ' )|wash}{else}{'Top of the tree'|i18n( 'design/admin/content/bookmark' )}{/if}</dd>
            </div>
            <div>
                <dt>{'Modified'|i18n( 'design/admin/content/bookmark' )}</dt>
                <dd>{if $item.modified|gt( 0 )}{$item.modified|l10n( shortdatetime )}{else}{'Unknown'|i18n( 'design/admin/content/bookmark' )}{/if}</dd>
            </div>
        </dl>
        {/if}
        {if and( $bp.order_buttons, $item.folder_count|gt( 1 ) )}
        <div class="exp-bm-position">
            <label for="{$card_id}-position">{'Position in its folder'|i18n( 'design/admin/content/bookmark' )}</label>
            <input type="number" id="{$card_id}-position" name="BookmarkPosition[{$item.id}]" value="{$item.folder_position}" min="1" max="{$item.folder_count}" inputmode="numeric" aria-describedby="{$card_id}-of" />
            <input type="hidden" name="BookmarkPositionShown[{$item.id}]" value="{$item.folder_position}" />
            <span class="exp-meta" id="{$card_id}-of">{'of %count'|i18n( 'design/admin/content/bookmark',, hash( '%count', $item.folder_count ) )}</span>
            <button type="submit" class="exp-btn exp-btn-small" name="BookmarkPositionButton" value="{$item.id}" aria-describedby="{$card_id}-title" title="{'Move the bookmark to this position in its folder, also across pages'|i18n( 'design/admin/content/bookmark' )}">{'Move'|i18n( 'design/admin/content/bookmark' )}</button>
        </div>
        {/if}
        </div>
    </li>
{/foreach}
{if $group_open}</ul></section>{/if}
</div>
{/if}

{if $bp.count|gt( 0 )}
<div class="exp-listfoot">
    {* The sizes come from admininterface.ini [PaginationSettings] ItemsPerPageList_content_bookmark; the preference stores the position in that list. *}
    <p class="exp-sizes">
        <span>{'Per page'|i18n( 'design/admin/content/bookmark' )}:</span>
    {foreach $bp.limit_choices as $limit_index => $limit_option}
        {if eq( $limit_option, $bp.limit )}
        <span class="current" aria-current="true">{$limit_option}</span>
        {else}
        <a href={concat( '/user/preferences/set/admin_bookmark_list_limit/', $limit_index|inc )|ezurl} title="{'Show %count bookmarks per page.'|i18n( 'design/admin/content/bookmark',, hash( '%count', $limit_option ) )}">{$limit_option}</a>
        {/if}
    {/foreach}
    </p>
    <div class="exp-pager">
    {include name=navigator
             uri='design:navigator/google.tpl'
             page_uri='/content/bookmark'
             page_uri_suffix=$bp.search_suffix
             item_count=$bp.count
             view_parameters=$view_parameters
             item_limit=$bp.limit}
    </div>
</div>
{/if}
</section>

{if $bp.count|gt( 0 )}
<div class="exp-bottombar">
    <div class="exp-bm-movegroup">
        <div class="exp-field">
            <label for="bm-move-target">{'Move the selected bookmarks to'|i18n( 'design/admin/content/bookmark' )}</label>
            <select name="FolderID" id="bm-move-target">
                <option value="0">{'No folder (top level)'|i18n( 'design/admin/content/bookmark' )}</option>
                {foreach $bp.targets as $target}
                <option value="{$target.id}">{$target.label|wash}</option>
                {/foreach}
            </select>
        </div>
        <button type="submit" class="exp-btn" name="MoveSelectedButton" value="1">{'Move selected'|i18n( 'design/admin/content/bookmark' )}</button>
    </div>
    <details class="exp-confirm">
        <summary>{'Remove selected'|i18n( 'design/admin/content/bookmark' )}</summary>
        <div>
            <p>{'The selected bookmarks are removed from your list. The items they point to are not changed.'|i18n( 'design/admin/content/bookmark' )}</p>
            <button type="submit" class="exp-btn exp-btn-danger" name="RemoveButton" value="1">{'Remove the selected bookmarks'|i18n( 'design/admin/content/bookmark' )}</button>
        </div>
    </details>
    <p class="exp-meta" id="bm-selected-count" aria-live="polite">{'Tick bookmarks to move or remove them.'|i18n( 'design/admin/content/bookmark' )}</p>
</div>
{/if}
</form>

{* Dragging a bookmark onto a folder (javascript only) sends this form; the select and the buttons above do the same. *}
{* Arranging (javascript only) sends the new order of one folder with this form; moving with the keyboard sends the
   same Move up or Move down as the buttons. The buttons and the position fields do it all without javascript. *}
<form method="post" action={$bp.here|ezurl} id="bm-order-form" hidden>
    <input type="hidden" name="BookmarkOrderButton" value="1" />
    <input type="hidden" name="OrderType" value="bookmark" />
    <input type="hidden" name="OrderFolderID" value="" />
    <input type="hidden" name="OrderIDs" value="" />
</form>
<form method="post" action={$bp.here|ezurl} id="bm-shift-form" hidden>
    <input type="hidden" name="BookmarkShiftButton" value="" />
</form>
<form method="post" action={$bp.here|ezurl} id="bm-drop-form" hidden>
    <input type="hidden" name="BookmarkFolderAction" value="move_bookmark" />
    <input type="hidden" name="FolderID" value="" />
</form>

</div>{* exp-bm-main *}
</div>{* exp-bm-layout *}

</div></div></div>
</div>

<script type="text/javascript">
var expBookmarkPageText = {ldelim}
    selected: '{'%count selected.'|i18n( 'design/admin/content/bookmark' )|wash( javascript )}'
{rdelim};
{literal}
(function () {
    'use strict';
    var list = document.getElementById( 'bm-list' );
    var nav = document.getElementById( 'bm-folder-nav' );
    var dropForm = document.getElementById( 'bm-drop-form' ), orderForm = document.getElementById( 'bm-order-form' ), shiftForm = document.getElementById( 'bm-shift-form' );
    var arrange = !!document.getElementById( 'bm-grip-help' );
    var KEY = 'exp-bm-grip';
    function all( root, sel ) { return root ? Array.prototype.slice.call( root.querySelectorAll( sel ) ) : []; }
    function remember( value ) { try { sessionStorage.setItem( KEY, value ); } catch ( e ) {} }
    function submit( form ) { if ( form.requestSubmit ) form.requestSubmit(); else form.submit(); }

    // ---- Select all and the selection count ----
    var selectAll = document.getElementById( 'bm-select-all' );
    var countEl = document.getElementById( 'bm-selected-count' );
    var initialNote = countEl ? countEl.textContent : '';
    function boxes() { return all( list, 'input[name="DeleteIDArray[]"]' ); }
    function selected() { return boxes().filter( function ( b ) { return b.checked; } ); }
    function update() {
        var n = 0;
        boxes().forEach( function ( b ) {
            var card = b.closest( '.exp-card' );
            if ( card ) card.classList.toggle( 'is-selected', b.checked );
            if ( b.checked ) n++;
        } );
        if ( countEl ) countEl.textContent = n ? expBookmarkPageText.selected.split( '%count' ).join( n ) : initialNote;
        if ( selectAll ) { selectAll.checked = n > 0 && n === boxes().length; selectAll.indeterminate = n > 0 && n < boxes().length; }
    }
    if ( list ) {
        if ( selectAll ) selectAll.parentNode.hidden = false;
        list.addEventListener( 'change', update );
        if ( selectAll ) selectAll.addEventListener( 'change', function () { boxes().forEach( function ( b ) { b.checked = selectAll.checked; } ); update(); } );
        update();
    }
    if ( !nav || !dropForm || !orderForm || !( 'draggable' in document.createElement( 'span' ) ) ) return;

    // ---- The grips: visible with javascript; bookmark grips only in the user's own order ----
    all( list, '.exp-grip' ).forEach( function ( g ) { g.hidden = !arrange; } );
    all( nav, '.exp-bm-fgrip' ).forEach( function ( g ) { g.hidden = false; } );
    all( nav, 'li[data-folder]' ).forEach( function ( li ) { li.setAttribute( 'data-drop-label', nav.getAttribute( 'data-drop-label' ) || '' ); } );
    var hint = document.querySelector( '.exp-bm .exp-bm-draghint' );
    if ( hint ) hint.hidden = false;
    all( list, '.exp-card[data-bookmark]' ).forEach( function ( card ) { card.setAttribute( 'draggable', 'true' ); } );

    // ---- Dragging bookmarks: within their folder to arrange them, onto a folder of the list to move them into it ----
    var drag = null; // { card, ul, ids, start, reorder, dropped }
    function idsOf( ul ) { return all( ul, ':scope > .exp-card' ).map( function ( c ) { return c.getAttribute( 'data-bookmark' ); } ); }
    function clearNav() { all( nav, '.is-drop, .is-before, .is-after' ).forEach( function ( li ) { li.classList.remove( 'is-drop', 'is-before', 'is-after' ); } ); }
    if ( list ) {
        list.addEventListener( 'dragstart', function ( e ) {
            var card = e.target.closest ? e.target.closest( '.exp-card[data-bookmark]' ) : null;
            if ( !card ) return;
            var box = card.querySelector( 'input[name="DeleteIDArray[]"]' );
            var ids = ( box && box.checked ) ? selected().map( function ( b ) { return b.value; } ) : [ card.getAttribute( 'data-bookmark' ) ];
            var ul = card.parentNode;
            drag = { card: card, ul: ul, ids: ids, start: idsOf( ul ).join( ',' ), reorder: arrange && ids.length === 1, dropped: false };
            card.classList.add( 'is-dragged' );
            if ( drag.reorder ) ul.classList.add( 'is-arranging' );
            try { e.dataTransfer.effectAllowed = 'move'; e.dataTransfer.setData( 'text/plain', ids.join( ',' ) ); e.dataTransfer.setDragImage( card, 24, 20 ); } catch ( x ) {}
        } );
        list.addEventListener( 'dragover', function ( e ) {
            clearNav();
            if ( !drag || !drag.card || !drag.reorder ) return;
            var over = e.target.closest ? e.target.closest( '.exp-card[data-bookmark]' ) : null;
            if ( !over || over.parentNode !== drag.ul ) return;
            e.preventDefault();
            if ( over === drag.card ) return;
            var box = over.getBoundingClientRect();
            drag.ul.insertBefore( drag.card, e.clientY > box.top + box.height / 2 ? over.nextSibling : over );
        } );
        list.addEventListener( 'drop', function ( e ) { if ( drag && drag.reorder ) e.preventDefault(); } );
        list.addEventListener( 'dragend', function () {
            if ( !drag || !drag.card ) return;
            var d = drag; drag = null;
            d.card.classList.remove( 'is-dragged' );
            d.ul.classList.remove( 'is-arranging' );
            clearNav();
            if ( d.dropped || !d.reorder ) return;
            var now = idsOf( d.ul );
            if ( now.join( ',' ) === d.start ) return;
            orderForm.elements.OrderType.value = 'bookmark';
            orderForm.elements.OrderFolderID.value = d.ul.getAttribute( 'data-folder' );
            orderForm.elements.OrderIDs.value = now.join( ',' );
            remember( 'b:' + d.card.getAttribute( 'data-bookmark' ) );
            window.setTimeout( function () { submit( orderForm ); }, 30 );
        } );
        // the arrow keys on a grip: the card's own Move up or Move down button
        list.addEventListener( 'keydown', function ( e ) {
            var grip = e.target.closest ? e.target.closest( '.exp-grip' ) : null;
            if ( !grip || ( e.key !== 'ArrowUp' && e.key !== 'ArrowDown' ) ) return;
            e.preventDefault();
            var id = grip.getAttribute( 'data-bookmark' );
            var b = grip.closest( '.exp-card' ).querySelector( 'button[name="BookmarkShiftButton"][value="' + ( e.key === 'ArrowUp' ? 'up-' : 'down-' ) + id + '"]' );
            if ( !b || b.disabled ) return;
            remember( 'b:' + id );
            var form = document.getElementById( 'bm-list-form' );
            if ( form.requestSubmit ) form.requestSubmit( b ); else b.click();
        } );
    }

    // ---- The folder list: drop bookmarks into a folder; arrange folders among their neighbours ----
    var folderDrag = null; // { li, parent }
    function navItem( e ) { return e.target.closest ? e.target.closest( 'li[data-folder]' ) : null; }
    function siblings( parent ) { return all( nav, 'li[data-parent="' + parent + '"]' ); }
    nav.addEventListener( 'dragstart', function ( e ) {
        var grip = e.target.closest ? e.target.closest( '.exp-bm-fgrip' ) : null;
        if ( !grip ) return;
        var li = grip.closest( 'li[data-folder]' );
        folderDrag = { li: li, parent: li.getAttribute( 'data-parent' ) };
        li.classList.add( 'is-dragged' );
        try { e.dataTransfer.effectAllowed = 'move'; e.dataTransfer.setData( 'text/plain', 'folder:' + li.getAttribute( 'data-folder' ) ); } catch ( x ) {}
    } );
    nav.addEventListener( 'dragover', function ( e ) {
        var li = navItem( e );
        clearNav();
        if ( !li ) return;
        if ( drag ) {
            e.preventDefault();
            li.classList.add( 'is-drop' );
        } else if ( folderDrag && li !== folderDrag.li && li.getAttribute( 'data-parent' ) === folderDrag.parent ) {
            e.preventDefault();
            var box = li.getBoundingClientRect();
            li.classList.add( e.clientY > box.top + box.height / 2 ? 'is-after' : 'is-before' );
        }
    } );
    // leaving a child of the list also fires dragleave, often without relatedTarget (and at 0,0): clear only when
    // the pointer is known to be outside the list; dragging over the bookmarks or the end of a drag clears it too
    nav.addEventListener( 'dragleave', function ( e ) { if ( e.relatedTarget && !nav.contains( e.relatedTarget ) ) clearNav(); } );
    nav.addEventListener( 'drop', function ( e ) {
        var li = navItem( e );
        if ( !li ) return;
        if ( drag ) {
            e.preventDefault();
            drag.dropped = true;
            dropForm.elements.FolderID.value = li.getAttribute( 'data-folder' );
            drag.ids.forEach( function ( id ) {
                var input = document.createElement( 'input' );
                input.type = 'hidden'; input.name = 'BookmarkIDArray[]'; input.value = id;
                dropForm.appendChild( input );
            } );
            clearNav();
            window.setTimeout( function () { submit( dropForm ); }, 30 );
        } else if ( folderDrag && li !== folderDrag.li && li.getAttribute( 'data-parent' ) === folderDrag.parent ) {
            e.preventDefault();
            var after = li.classList.contains( 'is-after' );
            var ids = siblings( folderDrag.parent ).filter( function ( s ) { return s !== folderDrag.li; } ).map( function ( s ) { return s.getAttribute( 'data-folder' ); } );
            var at = ids.indexOf( li.getAttribute( 'data-folder' ) ) + ( after ? 1 : 0 );
            ids.splice( at, 0, folderDrag.li.getAttribute( 'data-folder' ) );
            orderForm.elements.OrderType.value = 'folder';
            orderForm.elements.OrderFolderID.value = folderDrag.parent;
            orderForm.elements.OrderIDs.value = ids.join( ',' );
            remember( 'f:' + folderDrag.li.getAttribute( 'data-folder' ) );
            clearNav();
            window.setTimeout( function () { submit( orderForm ); }, 30 );
        }
    } );
    nav.addEventListener( 'dragend', function () { if ( folderDrag ) folderDrag.li.classList.remove( 'is-dragged' ); folderDrag = null; clearNav(); } );
    nav.addEventListener( 'keydown', function ( e ) {
        var grip = e.target.closest ? e.target.closest( '.exp-bm-fgrip' ) : null;
        if ( !grip || ( e.key !== 'ArrowUp' && e.key !== 'ArrowDown' ) || !shiftForm ) return;
        e.preventDefault();
        var li = grip.closest( 'li[data-folder]' ), sib = siblings( li.getAttribute( 'data-parent' ) ), i = sib.indexOf( li );
        if ( ( e.key === 'ArrowUp' && i <= 0 ) || ( e.key === 'ArrowDown' && i === sib.length - 1 ) ) return;
        shiftForm.elements.BookmarkShiftButton.value = ( e.key === 'ArrowUp' ? 'fup-' : 'fdown-' ) + li.getAttribute( 'data-folder' );
        remember( 'f:' + li.getAttribute( 'data-folder' ) );
        submit( shiftForm );
    } );

    // ---- Back from a move: the moved entry's grip has the focus again ----
    var last = null;
    try { last = sessionStorage.getItem( KEY ); sessionStorage.removeItem( KEY ); } catch ( e ) {}
    if ( last ) {
        var kind = last.charAt( 0 ), id = last.slice( 2 ), target = null;
        if ( kind === 'b' && list ) target = list.querySelector( '.exp-grip[data-bookmark="' + id + '"]' );
        if ( kind === 'f' ) target = nav.querySelector( '.exp-bm-fgrip[data-folder="' + id + '"]' );
        if ( target && !target.hidden ) {
            target.focus();
            var holder = target.closest( '.exp-card, li[data-folder]' );
            if ( holder ) holder.classList.add( 'is-moved' );
        }
    }
})();
{/literal}
</script>
