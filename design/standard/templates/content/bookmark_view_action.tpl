{* "Bookmark this page": the bookmark of a system page (a page of a module view such as setup/systemupgrade or
   audit/console) instead of a node. Shown by the administration designs next to the breadcrumb (mode='toolbar') and in
   the Bookmarks box (mode='box') on every page that is not a node, while the user may use bookmarks.

   One button that opens a small form in place (<details>, works without javascript): the name (the page's title,
   editable), the folder, and Add bookmark. A page that is bookmarked already says so ("Bookmarked") and the form saves
   its name and folder. The form posts BookmarkViewAction=add with the page's address (BookmarkViewURI, the module, the
   view and its parameters, without the siteaccess) to content/bookmark, which stores it (expBookmarkView) and comes back
   to this page (RedirectURI). What is shown comes from fetch( 'content', 'view_bookmark', ... ), which says
   available=false where system pages cannot be bookmarked (no database update yet, no content/bookmark policy, an
   address that is not a module view).

   Parameters: mode ('toolbar' or 'box'), ui_context. Guide: doc/eng-us/guides/bookmarks.md ("Bookmarking a system
   page"); feature: doc/eng-us/features/6.0/bookmarks-of-system-pages.md *}
{default mode='toolbar' ui_context=''}
{if and( is_set( $module_result.content_info.node_id )|not, is_set( $module_result.errorType )|not,
         ne( $ui_context, 'edit' ), ne( $ui_context, 'browse' ) )}
{def $vb = fetch( 'content', 'view_bookmark', hash( 'uri', first_set( $requested_uri_string, $module_result.uri, '' ),
                                                    'path', first_set( $module_result.path, array() ) ) )}
{if $vb.available}
{def $vb_folders = fetch( 'content', 'bookmark_folders', hash() )
     $vb_id = concat( 'exp-vb-', $mode )}
<details class="a4-content-bookmark-pageaction is-{$mode}{if $vb.bookmark} is-bookmarked{/if}" data-exp-vb="{$mode}">
    <summary class="a4-btn a4-btn-small" title="{if $vb.bookmark}{'This page is in your bookmarks. Open to change its name or folder.'|i18n( 'design/admin/content/bookmark' )|wash}{else}{'Add this page to your bookmarks.'|i18n( 'design/admin/content/bookmark' )|wash}{/if}">
        <span class="a4-icon a4-bi-bookmark" aria-hidden="true"></span>
        <span>{if $vb.bookmark}{'Bookmarked'|i18n( 'design/admin/content/bookmark' )}{else}{'Bookmark this page'|i18n( 'design/admin/content/bookmark' )}{/if}</span>
    </summary>
    <form method="post" action={'content/bookmark'|ezurl} class="a4-content-bookmark-pageform" aria-label="{'Bookmark this page'|i18n( 'design/admin/content/bookmark' )|wash}">
        <input type="hidden" name="BookmarkViewAction" value="add" />
        <input type="hidden" name="BookmarkViewURI" value="{$vb.uri|wash}" />
        <input type="hidden" name="RedirectURI" value="/{$vb.uri|wash}" />
        <div class="a4-field">
            <label for="{$vb_id}-name">{'Name'|i18n( 'design/admin/content/bookmark' )}</label>
            <input type="text" id="{$vb_id}-name" name="BookmarkViewName" value="{$vb.name|wash}" maxlength="255" required="required" autocomplete="off" />
        </div>
        <div class="a4-field">
            <label for="{$vb_id}-folder">{'Folder'|i18n( 'design/admin/content/bookmark' )}</label>
            <select name="BookmarkFolderID" id="{$vb_id}-folder">
                <option value="0">{'No folder (top level)'|i18n( 'design/admin/content/bookmark' )}</option>
                {foreach $vb_folders as $vb_folder}
                <option value="{$vb_folder.id}"{if eq( $vb_folder.id, $vb.folder_id )} selected="selected"{/if}>{'&nbsp;&nbsp;'|repeat( $vb_folder.depth )}{$vb_folder.name|wash}</option>
                {/foreach}
            </select>
        </div>
        <p class="a4-help">{'The bookmark keeps the address of this page with its parameters: /%uri'|i18n( 'design/admin/content/bookmark',, hash( '%uri', $vb.uri ) )|wash}</p>
        <div class="a4-actions">
            <button type="submit" class="a4-btn a4-btn-primary a4-btn-small">{if $vb.bookmark}{'Save bookmark'|i18n( 'design/admin/content/bookmark' )}{else}{'Add bookmark'|i18n( 'design/admin/content/bookmark' )}{/if}</button>
            <a class="a4-btn a4-btn-small" href={'content/bookmark'|ezurl}>{'My bookmarks'|i18n( 'design/admin/content/bookmark' )}</a>
        </div>
    </form>
</details>
{undef $vb_folders $vb_id}
{/if}
{undef $vb}
{/if}
{/default}
