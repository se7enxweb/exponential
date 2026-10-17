<!-- Edit menu -->

<div class="popupmenu" id="EditSubmenu" data-menu-depth="1">
    <template id="edit-languages-template"><a href={"/content/edit/%objectID%/f/%locale%"|ezurl}>%name%</a></template>
    <div id="edit-languages" data-menu-variable="%languages%" data-menu-template="edit-languages-template"></div>
    <hr />
    <a id="edit-languages-another" data-menu-url={"/content/edit/%objectID%/a"|ezurl} href="#">{'New translation'|i18n( 'design/admin/popupmenu' )}</a>
</div>