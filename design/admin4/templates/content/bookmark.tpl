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
{def $bp = $bookmark_page
     $summary = $bp.summary
     $current = $bp.current
     $folder_icon = '<span class="a4-icon a4-bi-folder is-medium" aria-hidden="true"></span>'
     $scope_title = ''
     $group_open = false()
     $group_info = false()
     $card_id = ''}
{switch match=$bp.scope_key}
{case match='top'}{set $scope_title = 'Not in a folder'|i18n( 'design/admin/content/bookmark' )}{/case}
{case match='folder'}{set $scope_title = $current.name}{/case}
{case}{set $scope_title = 'All bookmarks'|i18n( 'design/admin/content/bookmark' )}{/case}
{/switch}

<div class="context-block a4-page a4-content-bookmark">

<div class="box-header"><div class="box-ml">
<h1 class="context-title">{'My bookmarks (%bookmark_count)'|i18n( 'design/admin/content/bookmark',, hash( '%bookmark_count', $summary.bookmarks ) )}</h1>
</div></div>

<div class="box-bc"><div class="box-ml"><div class="box-content">

<p class="a4-intro">{'Bookmarks take you back to the items you work on: they are personal, nobody else sees them, and removing one never changes the item. Sort them into folders; the Bookmarks box at the side and the browse dialog show the same folders.'|i18n( 'design/admin/content/bookmark' )}</p>

{if is_set( $bookmark_notice )}
<div class="a4-notice {if eq( $bookmark_notice.level, 'error' )}is-bad{else}is-ok{/if}" role="{if eq( $bookmark_notice.level, 'error' )}alert{else}status{/if}">{$bookmark_notice.text|wash}</div>
{/if}

<section aria-labelledby="bm-overview-title">
<h2 class="a4-sr-only" id="bm-overview-title">{'Overview'|i18n( 'design/admin/content/bookmark' )}</h2>
<ul class="a4-figures">
    <li class="a4-figure{if eq( $bp.scope_key, 'all' )} is-current{/if}"><a href={'content/bookmark'|ezurl}><strong>{$summary.bookmarks}</strong> <span>{'Bookmarks'|i18n( 'design/admin/content/bookmark' )}</span></a></li>
    <li class="a4-figure"><strong>{$summary.folders}</strong> <span>{'Folders'|i18n( 'design/admin/content/bookmark' )}</span></li>
    <li class="a4-figure{if eq( $bp.scope_key, 'top' )} is-current{/if}"><a href={'content/bookmark/(folder)/top'|ezurl}><strong>{$summary.unfiled}</strong> <span>{'Not in a folder'|i18n( 'design/admin/content/bookmark' )}</span></a></li>
    <li class="a4-figure"><strong>{$summary.hidden}</strong> <span>{'Hidden items'|i18n( 'design/admin/content/bookmark' )}</span></li>
    <li class="a4-figure{if $summary.gone|gt( 0 )} is-attention{/if}"><strong>{$summary.gone}</strong> <span>{'No longer available'|i18n( 'design/admin/content/bookmark' )}</span></li>
</ul>
</section>

<div class="a4-content-bookmark-layout">

{* ---- The folders ---- *}
<div class="a4-content-bookmark-side">
<nav class="a4-card" aria-labelledby="bm-folders-title">
    <div class="a4-content-bookmark-panel-head"><h2 class="a4-h2" id="bm-folders-title">{'Folders'|i18n( 'design/admin/content/bookmark' )}</h2></div>
    <ul class="a4-content-bookmark-nav" id="bm-folder-nav" data-drop-label="{'Move into this folder'|i18n( 'design/admin/content/bookmark' )|wash}">
        <li>{if eq( $bp.scope_key, 'all' )}<span class="current" aria-current="page">{else}<a href={concat( 'content/bookmark', cond( $bp.sort|ne( 'own' ), concat( '/(sort)/', $bp.sort ), '' ), $bp.search_suffix )|ezurl}>{/if}<span class="a4-content-bookmark-navname">{'All bookmarks'|i18n( 'design/admin/content/bookmark' )}</span> <span class="a4-count">{$summary.bookmarks}</span>{if eq( $bp.scope_key, 'all' )}</span>{else}</a>{/if}</li>
        <li data-folder="0">{if eq( $bp.scope_key, 'top' )}<span class="current" aria-current="page">{else}<a href={concat( 'content/bookmark/(folder)/top', cond( $bp.sort|ne( 'own' ), concat( '/(sort)/', $bp.sort ), '' ), $bp.search_suffix )|ezurl}>{/if}<span class="a4-content-bookmark-navname">{'Not in a folder'|i18n( 'design/admin/content/bookmark' )}</span> <span class="a4-count">{$summary.unfiled}</span>{if eq( $bp.scope_key, 'top' )}</span>{else}</a>{/if}</li>
        {if $bp.folders}<li class="is-sep" aria-hidden="true"></li>{/if}
        {foreach $bp.folders as $folder}
        <li class="d{min( $folder.depth, 4 )}" data-folder="{$folder.id}" data-parent="{$folder.parent_id}"><span class="a4-grip a4-content-bookmark-fgrip" hidden draggable="true" data-folder="{$folder.id}" aria-label="{'Arrange folder %name: drag it, or press the up and down arrow keys'|i18n( 'design/admin/content/bookmark',, hash( '%name', $folder.name ) )|wash}" title="{'Drag to arrange, or use the up and down arrow keys'|i18n( 'design/admin/content/bookmark' )}" role="button" tabindex="0"><span aria-hidden="true">&#10303;</span></span>{if and( $current, eq( $current.id, $folder.id ) )}<span class="current" aria-current="page">{else}<a href={concat( 'content/bookmark/(folder)/', $folder.id, cond( $bp.sort|ne( 'own' ), concat( '/(sort)/', $bp.sort ), '' ), $bp.search_suffix )|ezurl}>{/if}{$folder_icon}<span class="a4-content-bookmark-navname">{$folder.name|wash}</span> <span class="a4-count" title="{'%count bookmarks, with the folders inside'|i18n( 'design/admin/content/bookmark',, hash( '%count', $folder.count ) )}">{$folder.count}</span>{if and( $current, eq( $current.id, $folder.id ) )}</span>{else}</a>{/if}</li>
        {/foreach}
    </ul>
    {if $bp.folders|not}<p class="a4-help a4-card-body">{'No folders yet. Create one below, then move bookmarks into it.'|i18n( 'design/admin/content/bookmark' )}</p>{/if}
    <p class="a4-help a4-content-bookmark-draghint" hidden>{'Tip: drag a bookmark onto a folder here to move it.'|i18n( 'design/admin/content/bookmark' )}</p>
</nav>

<details class="a4-fold"{if $bp.folders|not} open{/if}>
    <summary>{'New folder'|i18n( 'design/admin/content/bookmark' )}</summary>
    <div>
    <form method="post" action={$bp.here|ezurl} class="a4-content-bookmark-inline-form">
        <input type="hidden" name="BookmarkFolderAction" value="create" />
        <div class="a4-field">
            <label for="bm-new-name">{'Name'|i18n( 'design/admin/content/bookmark' )}</label>
            <input type="text" id="bm-new-name" name="FolderName" maxlength="255" required="required" autocomplete="off" />
        </div>
        <div class="a4-field">
            <label for="bm-new-parent">{'Inside'|i18n( 'design/admin/content/bookmark' )}</label>
            <select name="ParentFolderID" id="bm-new-parent">
                <option value="0">{'No folder (top level)'|i18n( 'design/admin/content/bookmark' )}</option>
                {foreach $bp.targets as $target}
                <option value="{$target.id}"{if and( $current, eq( $current.id, $target.id ) )} selected="selected"{/if}>{$target.label|wash}</option>
                {/foreach}
            </select>
        </div>
        <div class="a4-actions"><button type="submit" class="a4-btn a4-btn-primary">{'Create folder'|i18n( 'design/admin/content/bookmark' )}</button></div>
    </form>
    </div>
</details>
</div>

<div class="a4-content-bookmark-main">

{* ---- The folder that is shown ---- *}
{if $current}
<section class="a4-card a4-content-bookmark-folderhead" aria-labelledby="bm-folder-title">
    <ol class="a4-content-bookmark-crumbs" aria-label="{'Folder path'|i18n( 'design/admin/content/bookmark' )}">
        <li><a href={'content/bookmark'|ezurl}>{'All bookmarks'|i18n( 'design/admin/content/bookmark' )}</a></li>
        {foreach $current.path as $crumb}<li>{$crumb|wash}</li>{/foreach}
    </ol>
    <h2 class="a4-h2" id="bm-folder-title">{$current.name|wash}</h2>
    <dl class="a4-facts a4-content-bookmark-folderfacts">
        <div><dt>{'Bookmarks in it'|i18n( 'design/admin/content/bookmark' )}</dt><dd>{$current.direct}</dd></div>
        <div><dt>{'Including subfolders'|i18n( 'design/admin/content/bookmark' )}</dt><dd>{$current.count}</dd></div>
        <div><dt>{'Folders inside'|i18n( 'design/admin/content/bookmark' )}</dt><dd>{$current.subfolders}</dd></div>
    </dl>

    <div class="a4-content-bookmark-folderacts">
        <details class="a4-fold">
            <summary>{'Rename'|i18n( 'design/admin/content/bookmark' )}</summary>
            <div>
            <form method="post" action={$bp.here|ezurl} class="a4-content-bookmark-inline-form">
                <input type="hidden" name="BookmarkFolderAction" value="rename" />
                <input type="hidden" name="FolderID" value="{$current.id}" />
                <div class="a4-field">
                    <label for="bm-rename">{'New name'|i18n( 'design/admin/content/bookmark' )}</label>
                    <input type="text" id="bm-rename" name="FolderName" value="{$current.name|wash}" maxlength="255" required="required" autocomplete="off" />
                </div>
                <div class="a4-actions"><button type="submit" class="a4-btn a4-btn-primary">{'Save name'|i18n( 'design/admin/content/bookmark' )}</button></div>
            </form>
            </div>
        </details>
        <details class="a4-fold">
            <summary>{'Move'|i18n( 'design/admin/content/bookmark' )}</summary>
            <div>
            <form method="post" action={$bp.here|ezurl} class="a4-content-bookmark-inline-form">
                <input type="hidden" name="BookmarkFolderAction" value="move_folder" />
                <input type="hidden" name="FolderID" value="{$current.id}" />
                <div class="a4-field">
                    <label for="bm-move-folder">{'Put this folder inside'|i18n( 'design/admin/content/bookmark' )}</label>
                    <select name="ParentFolderID" id="bm-move-folder">
                        <option value="0"{if eq( $current.parent_id, 0 )} selected="selected"{/if}>{'No folder (top level)'|i18n( 'design/admin/content/bookmark' )}</option>
                        {foreach $bp.current_targets as $target}
                        <option value="{$target.id}"{if eq( $current.parent_id, $target.id )} selected="selected"{/if}>{$target.label|wash}</option>
                        {/foreach}
                    </select>
                    <span class="a4-help">{'Its bookmarks and folders move with it.'|i18n( 'design/admin/content/bookmark' )}</span>
                </div>
                <div class="a4-actions"><button type="submit" class="a4-btn a4-btn-primary">{'Move folder'|i18n( 'design/admin/content/bookmark' )}</button></div>
            </form>
            {if or( $bp.current_siblings.first|not, $bp.current_siblings.last|not )}
            <form method="post" action={$bp.here|ezurl} class="a4-content-bookmark-inline-form a4-card-body">
                <span class="a4-label">{'Order among its neighbours'|i18n( 'design/admin/content/bookmark' )}</span>
                <div class="a4-actions a4-content-bookmark-actions-tight">
                    {if $bp.current_siblings.first|not}<button type="submit" class="a4-btn a4-btn-small" name="BookmarkShiftButton" value="fup-{$current.id}">{'Move up'|i18n( 'design/admin/content/bookmark' )}</button>{/if}
                    {if $bp.current_siblings.last|not}<button type="submit" class="a4-btn a4-btn-small" name="BookmarkShiftButton" value="fdown-{$current.id}">{'Move down'|i18n( 'design/admin/content/bookmark' )}</button>{/if}
                </div>
            </form>
            {/if}
            </div>
        </details>
        <details class="a4-fold is-danger">
            <summary>{'Remove folder'|i18n( 'design/admin/content/bookmark' )}</summary>
            <div>
            <form method="post" action={$bp.here|ezurl} class="a4-content-bookmark-inline-form">
                <input type="hidden" name="BookmarkFolderAction" value="delete" />
                <input type="hidden" name="FolderID" value="{$current.id}" />
                <fieldset class="a4-content-bookmark-choice">
                    <legend>{'What happens to what is inside?'|i18n( 'design/admin/content/bookmark' )}</legend>
                    <label class="a4-check"><input type="radio" name="DeleteBookmarks" value="0" checked="checked" />
                        <span>{'Keep what is inside: it moves to %target (bookmarks: %count, folders: %folders).'|i18n( 'design/admin/content/bookmark',, hash( '%count', $current.direct, '%folders', $current.children|count, '%target', cond( $bp.current_parent, concat( '“', $bp.current_parent.name, '”' ), 'the top level'|i18n( 'design/admin/content/bookmark' ) ) ) )|wash}</span></label>
                    <label class="a4-check"><input type="radio" name="DeleteBookmarks" value="1" />
                        <span>{'Remove everything inside with it (bookmarks: %all, folders: %folders).'|i18n( 'design/admin/content/bookmark',, hash( '%all', $current.count, '%folders', $current.subfolders ) )}</span></label>
                </fieldset>
                <p class="a4-help">{'Only bookmarks are removed, never the items they point to.'|i18n( 'design/admin/content/bookmark' )}</p>
                <div class="a4-actions"><button type="submit" class="a4-btn a4-btn-danger">{'Remove folder'|i18n( 'design/admin/content/bookmark' )}</button></div>
            </form>
            </div>
        </details>
    </div>
</section>
{/if}

{* ---- Search and order ---- *}
<section aria-labelledby="bm-find-title">
<h2 class="a4-sr-only" id="bm-find-title">{'Find bookmarks'|i18n( 'design/admin/content/bookmark' )}</h2>
<form class="a4-filterbar" method="get" action={$bp.base_sorted|ezurl} role="search">
    <div class="a4-field">
        <label for="bm-search">{'Find a bookmark'|i18n( 'design/admin/content/bookmark' )}</label>
        <div class="a4-searchrow">
            <input type="search" id="bm-search" name="q" value="{$bp.search|wash}" autocomplete="off" maxlength="100" aria-describedby="bm-search-help" />
            <button type="submit" class="a4-btn a4-btn-primary">{'Search'|i18n( 'design/admin/content/bookmark' )}</button>
            {if $bp.search|ne( '' )}<a class="a4-btn" href={$bp.base_sorted|ezurl}>{'Clear search'|i18n( 'design/admin/content/bookmark' )}</a>{/if}
        </div>
        <span class="a4-help" id="bm-search-help">{if eq( $bp.scope_key, 'all' )}{'Searches the names, types, locations and folders of all your bookmarks.'|i18n( 'design/admin/content/bookmark' )}{else}{'Searches this folder only. Choose All bookmarks to search everywhere.'|i18n( 'design/admin/content/bookmark' )}{/if}</span>
    </div>
    <div class="a4-field">
        <span class="a4-label" id="bm-sort-label">{'Order'|i18n( 'design/admin/content/bookmark' )}</span>
        <ul class="a4-tabs" aria-labelledby="bm-sort-label">
        {foreach array( hash( 'sort', 'own', 'text', 'Your order'|i18n( 'design/admin/content/bookmark' ) ),
                        hash( 'sort', 'name', 'text', 'Name A to Z'|i18n( 'design/admin/content/bookmark' ) ),
                        hash( 'sort', 'added', 'text', 'Recently added'|i18n( 'design/admin/content/bookmark' ) ),
                        hash( 'sort', 'type', 'text', 'Type'|i18n( 'design/admin/content/bookmark' ) ),
                        hash( 'sort', 'modified', 'text', 'Recently modified'|i18n( 'design/admin/content/bookmark' ) ) ) as $tab}
            <li>{if eq( $tab.sort, $bp.sort )}<span class="current" aria-current="true">{$tab.text|wash}</span>{else}<a href={concat( $bp.base, cond( $tab.sort|ne( 'own' ), concat( '/(sort)/', $tab.sort ), '' ), $bp.search_suffix )|ezurl}>{$tab.text|wash}</a>{/if}</li>
        {/foreach}
        </ul>
        <span class="a4-help">{'Bookmarks stay grouped by folder in every order. Your order is the one of the Bookmarks box.'|i18n( 'design/admin/content/bookmark' )}</span>
    </div>
</form>
</section>

{* ---- The bookmarks ---- *}
<form name="bookmarkaction" id="bm-list-form" method="post" action={$bp.here|ezurl}>
{* the first button of the form: Enter in a position field moves that bookmark, never adds items *}
<button type="submit" class="a4-sr-only" name="BookmarkPositionDefault" value="1" tabindex="-1" aria-hidden="true">{'Move'|i18n( 'design/admin/content/bookmark' )}</button>
<section aria-labelledby="bm-list-title">
<div class="a4-section-head">
    <h2 class="a4-h2" id="bm-list-title">{if $bp.search|ne( '' )}{'Bookmarks matching “%search” in %scope'|i18n( 'design/admin/content/bookmark',, hash( '%search', $bp.search, '%scope', $scope_title ) )|wash}{else}{$scope_title|wash}{/if}</h2>
    {if $bp.count|gt( 0 )}
    <span class="a4-meta">{'%from to %to of %count'|i18n( 'design/admin/content/bookmark',, hash( '%from', sum( $bp.offset, 1 ), '%to', min( sum( $bp.offset, $bp.limit ), $bp.count ), '%count', $bp.count ) )}</span>
    <label class="a4-meta a4-content-bookmark-selectall" hidden><input type="checkbox" id="bm-select-all" /> {'Select all on this page'|i18n( 'design/admin/content/bookmark' )}</label>
    <button type="submit" class="a4-btn a4-btn-primary a4-btn-small a4-content-bookmark-addtop" name="AddButton" value="1" title="{if ne( $bp.scope_key, 'all' )}{'Items you add go into this folder.'|i18n( 'design/admin/content/bookmark' )}{else}{'Add items to your personal bookmark list.'|i18n( 'design/admin/content/bookmark' )}{/if}">{'Add items'|i18n( 'design/admin/content/bookmark' )}</button>
    {/if}
</div>
{if $bp.count|gt( 0 )}
{if $bp.order_buttons}
<p class="a4-help a4-content-bookmark-arrange" id="bm-grip-help">{'To arrange, drag a bookmark by its grip to a new place in its folder, or drop it on a folder in the folder list to move it there. On a focused grip the up and down arrow keys move it one place; the position field moves it anywhere in its folder, also across pages.'|i18n( 'design/admin/content/bookmark' )}</p>
{elseif $bp.search|ne( '' )}
<p class="a4-help a4-content-bookmark-arrange">{'Clear the search to arrange your bookmarks.'|i18n( 'design/admin/content/bookmark' )} <a href={$bp.own_path|ezurl}>{'Your order'|i18n( 'design/admin/content/bookmark' )}</a></p>
{else}
<p class="a4-help a4-content-bookmark-arrange"><a href={$bp.own_path|ezurl}>{'Switch to Your order to arrange'|i18n( 'design/admin/content/bookmark' )}</a>. {'Dropping a bookmark on a folder in the folder list moves it there in every order.'|i18n( 'design/admin/content/bookmark' )}</p>
{/if}
{/if}

{if $bp.count|eq( 0 )}
<div class="a4-empty">
{if $bp.search|ne( '' )}
    <strong>{'No bookmark matches this search.'|i18n( 'design/admin/content/bookmark' )}</strong>
    {'Check the spelling, search for a shorter part of the name, or search all bookmarks.'|i18n( 'design/admin/content/bookmark' )}
    <div class="a4-actions"><a class="a4-btn" href={$bp.base_sorted|ezurl}>{'Clear search'|i18n( 'design/admin/content/bookmark' )}</a>{if ne( $bp.scope_key, 'all' )} <a class="a4-btn" href={concat( 'content/bookmark', $bp.search_suffix )|ezurl}>{'Search all bookmarks'|i18n( 'design/admin/content/bookmark' )}</a>{/if}</div>
{elseif $summary.bookmarks|eq( 0 )}
    <strong>{'You have no bookmarks yet.'|i18n( 'design/admin/content/bookmark' )}</strong>
    {'Add items here, or choose Add to bookmarks in the menu of any item in the content tree.'|i18n( 'design/admin/content/bookmark' )}
    <div class="a4-actions"><button type="submit" class="a4-btn a4-btn-primary" name="AddButton" value="1">{'Add items'|i18n( 'design/admin/content/bookmark' )}</button></div>
{elseif eq( $bp.scope_key, 'top' )}
    <strong>{'Every bookmark is in a folder.'|i18n( 'design/admin/content/bookmark' )}</strong>
{else}
    <strong>{'This folder is empty.'|i18n( 'design/admin/content/bookmark' )}</strong>
    {'Add items to it, or select bookmarks in another folder and move them here.'|i18n( 'design/admin/content/bookmark' )}
    <div class="a4-actions"><button type="submit" class="a4-btn a4-btn-primary" name="AddButton" value="1">{'Add items'|i18n( 'design/admin/content/bookmark' )}</button></div>
{/if}
</div>
{else}
<div id="bm-list">
{foreach $bp.items as $item}
    {if $item.group_start}
        {if $group_open}</ul></section>{/if}
        {set $group_open = true()}
        {if $item.group|gt( 0 )}{set $group_info = $bp.folder_names[$item.group]}{else}{set $group_info = false()}{/if}
<section class="a4-content-bookmark-group" aria-labelledby="bm-group-{$item.group}">
    <div class="a4-content-bookmark-grouphead">
        {if $group_info}{$folder_icon}{/if}
        <h3 class="a4-h3" id="bm-group-{$item.group}">{if $group_info}{$group_info.name|wash}{else}{'Not in a folder'|i18n( 'design/admin/content/bookmark' )}{/if}</h3>
        <span class="a4-badge">{if $item.group_count|eq( 1 )}{'1 bookmark'|i18n( 'design/admin/content/bookmark' )}{else}{'%count bookmarks'|i18n( 'design/admin/content/bookmark',, hash( '%count', $item.group_count ) )}{/if}</span>
        {if $item.group_continued}<span class="a4-meta">{'continued from the previous page'|i18n( 'design/admin/content/bookmark' )}</span>{/if}
        {if and( $group_info, $group_info.path )}<p class="a4-content-bookmark-grouppath">{'in %path'|i18n( 'design/admin/content/bookmark',, hash( '%path', $group_info.path|implode( ' / ' ) ) )|wash}</p>{/if}
    </div>
    <ul class="a4-cards" data-folder="{$item.group}">
    {/if}
    {set $card_id = concat( 'bm-', $item.id )}
    <li class="a4-card{if or( eq( $item.state, 'gone' ), eq( $item.state, 'denied' ) )} is-bad{elseif or( eq( $item.state, 'hidden' ), eq( $item.state, 'invisible' ) )} is-attention{/if}" id="{$card_id}" data-bookmark="{$item.id}">
        <div class="a4-card-head">
            <div class="a4-card-title">
                {if $bp.order_buttons}<span class="a4-grip" hidden draggable="true" data-bookmark="{$item.id}" aria-describedby="bm-grip-help" aria-label="{'Arrange %name: drag it, or press the up and down arrow keys'|i18n( 'design/admin/content/bookmark',, hash( '%name', $item.name ) )|wash}" title="{'Drag to arrange, or use the up and down arrow keys'|i18n( 'design/admin/content/bookmark' )}" role="button" tabindex="0"><span aria-hidden="true">&#10303;</span></span>{/if}
                <label class="a4-pick" title="{'Select this bookmark.'|i18n( 'design/admin/content/bookmark' )}">
                    <input type="checkbox" name="DeleteIDArray[]" value="{$item.id}" aria-label="{'Select %name'|i18n( 'design/admin/content/bookmark',, hash( '%name', $item.name ) )|wash}" />
                </label>
                {if $item.class_identifier}{$item.class_identifier|class_icon( small, $item.class_name )}{/if}
                <h4 id="{$card_id}-title">{if or( eq( $item.state, 'gone' ), eq( $item.state, 'denied' ) )}{$item.name|wash}{else}<a href={concat( 'content/view/full/', $item.node_id )|ezurl}>{$item.name|wash}</a>{/if}</h4>
                <ul class="a4-badges">
                    {if $item.class_name}<li class="a4-badge">{$item.class_name|wash}</li>{/if}
                    {switch match=$item.state}
                    {case match='hidden'}<li class="a4-badge is-warn" title="{'Hidden: visitors of the site do not see it.'|i18n( 'design/admin/content/bookmark' )}">{'Hidden'|i18n( 'design/admin/content/bookmark' )}</li>{/case}
                    {case match='invisible'}<li class="a4-badge is-warn" title="{'An item above it is hidden, so visitors of the site do not see it.'|i18n( 'design/admin/content/bookmark' )}">{'Hidden by a parent'|i18n( 'design/admin/content/bookmark' )}</li>{/case}
                    {case match='gone'}<li class="a4-badge is-bad">{'Not found'|i18n( 'design/admin/content/bookmark' )}</li>{/case}
                    {case match='denied'}<li class="a4-badge is-bad">{'No access'|i18n( 'design/admin/content/bookmark' )}</li>{/case}
                    {case}{/case}
                    {/switch}
                </ul>
            </div>
            <div class="a4-actions a4-content-bookmark-actions-tight">
                {if or( eq( $item.state, 'gone' ), eq( $item.state, 'denied' ) )|not}
                <a class="a4-btn a4-btn-small" href={concat( 'content/view/full/', $item.node_id )|ezurl} aria-describedby="{$card_id}-title">{'View'|i18n( 'design/admin/content/bookmark' )}</a>
                {if $item.can_edit}<a class="a4-btn a4-btn-small" href={concat( 'content/edit/', $item.contentobject_id )|ezurl} aria-describedby="{$card_id}-title">{'Edit'|i18n( 'design/admin/content/bookmark' )}</a>{/if}
                {/if}
                {if $bp.order_buttons}
                <button type="submit" class="a4-btn a4-btn-small a4-btn-icon" name="BookmarkShiftButton" value="up-{$item.id}" title="{'Move up'|i18n( 'design/admin/content/bookmark' )|wash}" aria-label="{'Move %name up'|i18n( 'design/admin/content/bookmark',, hash( '%name', $item.name ) )|wash}"{if $bp.first_last.first|contains( $item.id )} disabled="disabled"{/if}><span class="a4-icon a4-bi-arrow-up" aria-hidden="true"></span></button>
                <button type="submit" class="a4-btn a4-btn-small a4-btn-icon" name="BookmarkShiftButton" value="down-{$item.id}" title="{'Move down'|i18n( 'design/admin/content/bookmark' )|wash}" aria-label="{'Move %name down'|i18n( 'design/admin/content/bookmark',, hash( '%name', $item.name ) )|wash}"{if $bp.first_last.last|contains( $item.id )} disabled="disabled"{/if}><span class="a4-icon a4-bi-arrow-down" aria-hidden="true"></span></button>
                {/if}
            </div>
        </div>
        <div class="a4-content-bookmark-cardfoot">
        {if eq( $item.state, 'gone' )}
        <p class="a4-content-bookmark-note">{'The item was removed, is in the trash, or is not in a language of this site. Remove the bookmark, or restore the item.'|i18n( 'design/admin/content/bookmark' )}</p>
        {elseif eq( $item.state, 'denied' )}
        <p class="a4-content-bookmark-note">{'You may no longer read this item. Remove the bookmark, or ask an administrator for access.'|i18n( 'design/admin/content/bookmark' )}</p>
        {else}
        <dl class="a4-facts">
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
        <div class="a4-content-bookmark-position">
            <label for="{$card_id}-position">{'Position in its folder'|i18n( 'design/admin/content/bookmark' )}</label>
            <input class="a4-input is-auto" type="number" id="{$card_id}-position" name="BookmarkPosition[{$item.id}]" value="{$item.folder_position}" min="1" max="{$item.folder_count}" inputmode="numeric" aria-describedby="{$card_id}-of" />
            <input type="hidden" name="BookmarkPositionShown[{$item.id}]" value="{$item.folder_position}" />
            <span class="a4-meta" id="{$card_id}-of">{'of %count'|i18n( 'design/admin/content/bookmark',, hash( '%count', $item.folder_count ) )}</span>
            <button type="submit" class="a4-btn a4-btn-small" name="BookmarkPositionButton" value="{$item.id}" aria-describedby="{$card_id}-title" title="{'Move the bookmark to this position in its folder, also across pages'|i18n( 'design/admin/content/bookmark' )}">{'Move'|i18n( 'design/admin/content/bookmark' )}</button>
        </div>
        {/if}
        </div>
    </li>
{/foreach}
{if $group_open}</ul></section>{/if}
</div>
{/if}

{if $bp.count|gt( 0 )}
<div class="a4-listfoot">
    {* The sizes come from admininterface.ini [PaginationSettings] ItemsPerPageList_content_bookmark; the preference stores the position in that list. *}
    <p class="a4-sizes">
        <span>{'Per page'|i18n( 'design/admin/content/bookmark' )}:</span>
    {foreach $bp.limit_choices as $limit_index => $limit_option}
        {if eq( $limit_option, $bp.limit )}
        <span class="current" aria-current="true">{$limit_option}</span>
        {else}
        <a href={concat( '/user/preferences/set/admin_bookmark_list_limit/', $limit_index|inc )|ezurl} title="{'Show %count bookmarks per page.'|i18n( 'design/admin/content/bookmark',, hash( '%count', $limit_option ) )}">{$limit_option}</a>
        {/if}
    {/foreach}
    </p>
    <div class="a4-pager">
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
<div class="a4-toolbar is-bottom">
    <div class="a4-content-bookmark-movegroup">
        <div class="a4-field">
            <label for="bm-move-target">{'Move the selected bookmarks to'|i18n( 'design/admin/content/bookmark' )}</label>
            <select name="FolderID" id="bm-move-target">
                <option value="0">{'No folder (top level)'|i18n( 'design/admin/content/bookmark' )}</option>
                {foreach $bp.targets as $target}
                <option value="{$target.id}">{$target.label|wash}</option>
                {/foreach}
            </select>
        </div>
        <button type="submit" class="a4-btn" name="MoveSelectedButton" value="1">{'Move selected'|i18n( 'design/admin/content/bookmark' )}</button>
    </div>
    <details class="a4-confirm">
        <summary>{'Remove selected'|i18n( 'design/admin/content/bookmark' )}</summary>
        <div>
            <p>{'The selected bookmarks are removed from your list. The items they point to are not changed.'|i18n( 'design/admin/content/bookmark' )}</p>
            <button type="submit" class="a4-btn a4-btn-danger" name="RemoveButton" value="1">{'Remove the selected bookmarks'|i18n( 'design/admin/content/bookmark' )}</button>
        </div>
    </details>
    <p class="a4-meta" id="bm-selected-count" aria-live="polite">{'Tick bookmarks to move or remove them.'|i18n( 'design/admin/content/bookmark' )}</p>
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
            var card = b.closest( '.a4-card' );
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
    all( list, '.a4-grip' ).forEach( function ( g ) { g.hidden = !arrange; } );
    all( nav, '.a4-content-bookmark-fgrip' ).forEach( function ( g ) { g.hidden = false; } );
    all( nav, 'li[data-folder]' ).forEach( function ( li ) { li.setAttribute( 'data-drop-label', nav.getAttribute( 'data-drop-label' ) || '' ); } );
    var hint = document.querySelector( '.a4-content-bookmark .a4-content-bookmark-draghint' );
    if ( hint ) hint.hidden = false;
    all( list, '.a4-card[data-bookmark]' ).forEach( function ( card ) { card.setAttribute( 'draggable', 'true' ); } );

    // ---- Dragging bookmarks: within their folder to arrange them, onto a folder of the list to move them into it ----
    var drag = null; // { card, ul, ids, start, reorder, dropped }
    function idsOf( ul ) { return all( ul, ':scope > .a4-card' ).map( function ( c ) { return c.getAttribute( 'data-bookmark' ); } ); }
    function clearNav() { all( nav, '.is-drop, .is-before, .is-after' ).forEach( function ( li ) { li.classList.remove( 'is-drop', 'is-before', 'is-after' ); } ); }
    if ( list ) {
        list.addEventListener( 'dragstart', function ( e ) {
            var card = e.target.closest ? e.target.closest( '.a4-card[data-bookmark]' ) : null;
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
            var over = e.target.closest ? e.target.closest( '.a4-card[data-bookmark]' ) : null;
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
            var grip = e.target.closest ? e.target.closest( '.a4-grip' ) : null;
            if ( !grip || ( e.key !== 'ArrowUp' && e.key !== 'ArrowDown' ) ) return;
            e.preventDefault();
            var id = grip.getAttribute( 'data-bookmark' );
            var b = grip.closest( '.a4-card' ).querySelector( 'button[name="BookmarkShiftButton"][value="' + ( e.key === 'ArrowUp' ? 'up-' : 'down-' ) + id + '"]' );
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
        var grip = e.target.closest ? e.target.closest( '.a4-content-bookmark-fgrip' ) : null;
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
        var grip = e.target.closest ? e.target.closest( '.a4-content-bookmark-fgrip' ) : null;
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
        if ( kind === 'b' && list ) target = list.querySelector( '.a4-grip[data-bookmark="' + id + '"]' );
        if ( kind === 'f' ) target = nav.querySelector( '.a4-content-bookmark-fgrip[data-folder="' + id + '"]' );
        if ( target && !target.hidden ) {
            target.focus();
            var holder = target.closest( '.a4-card, li[data-folder]' );
            if ( holder ) holder.classList.add( 'is-moved' );
        }
    }
})();
{/literal}
</script>
