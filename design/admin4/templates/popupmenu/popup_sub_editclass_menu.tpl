<!-- Edit class submenu -->

<div class="popupmenu" id="EditClassSubmenu" data-menu-depth="1">
    <template id="edit-class-languages-template"><a href={"/class/edit/%classID%/(language)/%locale%"|ezurl}>%name%</a></template>
    <div id="edit-class-languages" data-menu-variable="%languages%" data-menu-template="edit-class-languages-template"></div>
    <hr />
    <a id="edit-class-another-language" data-menu-url={"/class/edit/%classID%"|ezurl} data-menu-disabled-class="menu-item-disabled" href="#">{'New translation'|i18n( 'design/admin/popupmenu' )}</a>
</div>