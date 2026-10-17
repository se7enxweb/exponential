<!-- Bookmark popup menu -->

<div class="popupmenu" id="BookmarkMenu" data-menu-depth="0" data-menu-header="bookmark-header">
    <div class="popupmenuheader"><h3 id="bookmark-header">XXX</h3>
{*        <div class="window-close" onclick="ezpopmenu_hide( 'BookmarkMenu' )"><p>X</p></div> *}
        <div class="break"></div>
    </div>
    <a id="bookmark-view" data-menu-url={"/content/view/full/%nodeID%"|ezurl} href="#">{"View"|i18n("design/admin/popupmenu")}</a>
{if $multilingual_site}
    <a id="bookmark-edit-in" class="more" href="#" data-menu-submenu="EditSubmenu">{'Edit in'|i18n( 'design/admin/popupmenu' )}</a>
{else}
    <a id="bookmark-edit" data-menu-url={"/content/edit/%objectID%"|ezurl} href="#">{"Edit"|i18n("design/admin/popupmenu")}</a>
{/if}
    <hr />
    <a id="bookmark-remove" href="#" data-menu-form="menu-form-removebookmark">{"Remove bookmark"|i18n("design/admin/popupmenu")}</a>

    {* Include additional bookmark menu items  based on .ini settings *}
    {foreach ezini( 'AdditionalMenuSettings', 'BookmarkMenuTemplateArray', 'admininterface.ini' ) as $template}
        {include uri=concat('design:', $template )}
    {/foreach}
</div>

{* Remove bookmark *}
<form id="menu-form-removebookmark" method="post" action={"/content/bookmark"|ezurl}>
  <input type="hidden" name="DeleteIDArray[]" value="%bookmarkID%" />
  <input type="hidden" name="RemoveButton" value="x" />
  <input type="hidden" name="NeedRedirectBack" value="x" />
</form>