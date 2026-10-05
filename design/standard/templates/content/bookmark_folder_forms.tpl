{* The folder forms and the dialog of the bookmark page. Parameter: folders (fetch content bookmark_folders) *}
{default folders=false()}
{def $bm_folder_list = $folders}
{* The folder actions without JavaScript, and the form the buttons and drag and drop fill (JavaScript) *}
<div class="context-block exp-bm-folder-forms">
<div class="box-ml"><div class="box-mr"><div class="box-content">
<h2>{'Folders'|i18n( 'design/admin/content/bookmark' )}</h2>
<form method="post" action={'content/bookmark/'|ezurl} class="exp-bm-form-create">
    <input type="hidden" name="BookmarkFolderAction" value="create" />
    <div class="block">
        <label for="exp-bm-new-name">{'New folder'|i18n( 'design/admin/content/bookmark' )}</label>
        <input type="text" class="halfbox" id="exp-bm-new-name" name="FolderName" maxlength="255" required="required" placeholder="{'Folder name'|i18n( 'design/admin/content/bookmark' )}" />
        <label for="exp-bm-new-parent">{'In folder'|i18n( 'design/admin/content/bookmark' )}</label>
        <select name="ParentFolderID" id="exp-bm-new-parent">
            <option value="0">{'Top level'|i18n( 'design/admin/content/bookmark' )}</option>
            {foreach $bm_folder_list as $folder}
            <option value="{$folder.id}">{'&nbsp;&nbsp;'|repeat( $folder.depth )}{$folder.name|wash}</option>
            {/foreach}
        </select>
        <input class="button" type="submit" value="{'Create folder'|i18n( 'design/admin/content/bookmark' )}" />
    </div>
</form>
{if $bm_folder_list}
<form method="post" action={'content/bookmark/'|ezurl} class="exp-bm-form-manage exp-bm-nojs">
    <div class="block">
        <label for="exp-bm-manage-folder">{'Folder'|i18n( 'design/admin/content/bookmark' )}</label>
        <select name="FolderID" id="exp-bm-manage-folder">
            {foreach $bm_folder_list as $folder}
            <option value="{$folder.id}">{'&nbsp;&nbsp;'|repeat( $folder.depth )}{$folder.name|wash}</option>
            {/foreach}
        </select>
        <input type="text" name="FolderName" maxlength="255" placeholder="{'New name'|i18n( 'design/admin/content/bookmark' )}" />
        <button class="button" type="submit" name="BookmarkFolderAction" value="rename">{'Rename'|i18n( 'design/admin/content/bookmark' )}</button>
        <label><input type="checkbox" name="DeleteBookmarks" value="1" /> {'Also delete the bookmarks and folders inside'|i18n( 'design/admin/content/bookmark' )}</label>
        <button class="button" type="submit" name="BookmarkFolderAction" value="delete">{'Delete'|i18n( 'design/admin/content/bookmark' )}</button>
    </div>
</form>
{/if}
</div></div></div>
</div>

{* The dialog of the JavaScript actions: rename, delete, move, new folder. It is a form of its own so that the form token is sent. *}
<dialog id="exp-bm-dialog" class="exp-bm-dialog" aria-labelledby="exp-bm-dialog-title">
  <form method="post" action={'content/bookmark/'|ezurl} id="exp-bm-action">
    <h2 id="exp-bm-dialog-title"></h2>
    <input type="hidden" name="BookmarkFolderAction" value="" />
    <input type="hidden" name="FolderID" value="" />
    <input type="hidden" name="BookmarkID" value="" />
    <input type="hidden" name="Type" value="" />
    <input type="hidden" name="ID" value="" />
    <input type="hidden" name="BeforeID" value="" />
    <p class="exp-bm-d exp-bm-d-text" hidden="hidden"></p>
    <label class="exp-bm-d exp-bm-d-name" hidden="hidden">{'Folder name'|i18n( 'design/admin/content/bookmark' )}
        <input type="text" name="FolderName" maxlength="255" /></label>
    <label class="exp-bm-d exp-bm-d-parent" hidden="hidden"><span class="exp-bm-d-parent-label">{'Move to'|i18n( 'design/admin/content/bookmark' )}</span>
        <select name="ParentFolderID">
            <option value="0">{'Top level'|i18n( 'design/admin/content/bookmark' )}</option>
            {foreach $bm_folder_list as $folder}
            <option value="{$folder.id}">{'&nbsp;&nbsp;'|repeat( $folder.depth )}{$folder.name|wash}</option>
            {/foreach}
        </select></label>
    <label class="exp-bm-d exp-bm-d-withbookmarks" hidden="hidden"><input type="checkbox" name="DeleteBookmarks" value="1" /> {'Also delete the bookmarks and folders inside'|i18n( 'design/admin/content/bookmark' )}</label>
    <div class="exp-bm-d-buttons">
        <button type="button" class="button" data-exp-bm-cancel="1">{'Cancel'|i18n( 'design/admin/content/bookmark' )}</button>
        <button type="submit" class="button defaultbutton" data-exp-bm-ok="1">{'Save'|i18n( 'design/admin/content/bookmark' )}</button>
    </div>
  </form>
  <p hidden="hidden" id="exp-bm-t" data-rename="{'Rename folder'|i18n( 'design/admin/content/bookmark' )}"
     data-delete="{'Delete folder'|i18n( 'design/admin/content/bookmark' )}"
     data-delete-text="{'Delete the folder "%name"? Its bookmarks and folders move up one level. No bookmark is deleted.'|i18n( 'design/admin/content/bookmark' )|wash}"
     data-move-folder="{'Move folder'|i18n( 'design/admin/content/bookmark' )}"
     data-move-bookmark="{'Move bookmark'|i18n( 'design/admin/content/bookmark' )}"
     data-new="{'New folder'|i18n( 'design/admin/content/bookmark' )}"
     data-confirm-delete="{'Delete'|i18n( 'design/admin/content/bookmark' )}"
     data-save="{'Save'|i18n( 'design/admin/content/bookmark' )}"
     data-create="{'Create folder'|i18n( 'design/admin/content/bookmark' )}"
     data-move="{'Move'|i18n( 'design/admin/content/bookmark' )}"></p>
</dialog>

{undef $bm_folder_list}
{/default}
