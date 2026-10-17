<!-- Subitems icon click popup menu -->

<div class="popupmenu" id="SubitemsContextMenu" data-menu-depth="0" data-menu-header="child-menu-header">
    <div class="popupmenuheader"><h3 id="child-menu-header">XXX</h3>
{*        <div class="window-close" onclick="ezpopmenu_hide( 'SubitemsContextMenu' )"><p>X</p></div> *}
        <div class="break"></div>
    </div>
{*    <a id="child-menu-view" href="#">{"View"|i18n("design/admin/popupmenu")}</a> *}
    <a id="child-menu-preview" data-menu-url={"/content/versionview/%objectID%/%version%"|ezurl} href="#">{"Preview"|i18n("design/admin/popupmenu")}</a>
{if $multilingual_site}
    <a id="child-menu-edit-in" class="more" href="#" data-menu-submenu="EditSubmenu">{'Edit in'|i18n( 'design/admin/popupmenu' )}</a>
{else}
    <a id="child-menu-edit" data-menu-url={"/content/edit/%objectID%"|ezurl} href="#">{"Edit"|i18n("design/admin/popupmenu")}</a>
{/if}
    <hr />
    <a id="child-menu-copy" data-menu-url={"/content/copy/%objectID%"|ezurl} href="#">{"Copy"|i18n("design/admin/popupmenu")}</a>
    <a id="child-menu-copy-subtree" data-menu-url={"/content/copysubtree/%nodeID%"|ezurl} href="#">{"Copy subtree"|i18n("design/admin/popupmenu")}</a>
    <a id="child-menu-move" href="#" data-menu-form="menu-form-move">{"Move"|i18n("design/admin/popupmenu")}</a>
    <a id="child-menu-remove" href="#" data-menu-form="menu-form-remove">{"Remove"|i18n("design/admin/popupmenu")}</a>
    <a id="child-menu-advanced" class="more" href="#" data-menu-submenu="Advanced">{'Advanced'|i18n( 'design/admin/popupmenu' )}</a>
    <hr />
    <a id="child-menu-bookmark" href="#" data-menu-form="menu-form-addbookmark">{"Add to my bookmarks"|i18n("design/admin/popupmenu")}</a>
    <a id="child-menu-notify" href="#" data-menu-form="menu-form-notify">{"Add to my notifications"|i18n("design/admin/popupmenu")}</a>

    <a id="child-menu-create-here" data-menu-disabled-class="menu-item-disabled" class="more" href="#" data-menu-submenu="CreateHereMenu">{'Create here'|i18n( 'design/admin/popupmenu' )}</a>

    {* Include additional subitems menu items  based on .ini settings *}
    {section var=template loop=ezini( 'AdditionalMenuSettings', 'SubitemsContextMenuTemplateArray', 'admininterface.ini' )}
        {include uri=concat('design:', $template )}
    {/section}
</div>