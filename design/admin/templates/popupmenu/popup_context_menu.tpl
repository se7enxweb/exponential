<!-- Treemenu icon click popup menu -->

<div class="popupmenu" id="ContextMenu" data-menu-depth="0" data-menu-header="menu-header">
    <div class="popupmenuheader"><h3 id="menu-header">XXX</h3>
        <div class="break"></div>
    </div>
    <!-- a id="menu-view" href="#">{"View"|i18n("design/admin/popupmenu")}</a -->
{if $multilingual_site}
    <a id="menu-edit-in" class="more" href="#" data-menu-submenu="EditSubmenu">{'Edit in'|i18n( 'design/admin/popupmenu' )}</a>
{else}
    <a id="menu-edit" data-menu-url={"/content/edit/%objectID%"|ezurl} href="#">{"Edit"|i18n("design/admin/popupmenu")}</a>
{/if}
    <hr />
    <a id="menu-copy" data-menu-url={"/content/copy/%objectID%"|ezurl} href="#">{"Copy"|i18n("design/admin/popupmenu")}</a>
    <a id="menu-copy-subtree" data-menu-url={"/content/copysubtree/%nodeID%"|ezurl} href="#">{"Copy subtree"|i18n("design/admin/popupmenu")}</a>
    <a id="menu-move" href="#" data-menu-form="menu-form-move">{"Move"|i18n("design/admin/popupmenu")}</a>
    <a id="menu-remove" href="#" data-menu-form="menu-form-remove">{"Remove"|i18n("design/admin/popupmenu")}</a>
    <a id="menu-advanced" class="more" href="#" data-menu-submenu="Advanced">{'Advanced'|i18n( 'design/admin/popupmenu' )}</a>
    <hr />
    <a id="menu-bookmark" href="#" data-menu-form="menu-form-addbookmark">{"Add to my bookmarks"|i18n("design/admin/popupmenu")}</a>
    <a id="menu-notify" href="#" data-menu-form="menu-form-notify">{"Add to my notifications"|i18n("design/admin/popupmenu")}</a>

    <a id="menu-create-here" data-menu-disabled-class="menu-item-disabled" class="more" href="#" data-menu-submenu="CreateHereMenu">{'Create here'|i18n( 'design/admin/popupmenu' )}</a>

    {* Include additional context menu items  based on .ini settings *}
    {section var=template loop=ezini( 'AdditionalMenuSettings', 'ContextMenuTemplateArray', 'admininterface.ini' )}
        {include uri=concat('design:', $template )}
    {/section}
</div>