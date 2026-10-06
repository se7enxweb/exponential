{* The chips that show or hide the parts of the class page: class groups, override templates and translations. Each is
   a link that stores the user preference it always stored (admin_navigation_class_groups,
   admin_navigation_class_temlates, admin_navigation_class_translations). The same file is in design/admin and
   design/admin4. *}
<nav class="exp-panel exp-windows" aria-label="{'Show on this page'|i18n( 'design/admin/class/view' )}">
<p class="exp-sortby">
    <span>{'Show on this page'|i18n( 'design/admin/class/view' )}:</span>
    {if ezpreference( 'admin_navigation_class_groups' )}
    <a class="current" aria-pressed="true" href={'/user/preferences/set/admin_navigation_class_groups/0'|ezurl} title="{'Hide class groups.'|i18n( 'design/admin/class/view' )}">{'Class groups'|i18n( 'design/admin/class/view' )}</a>
    {else}
    <a aria-pressed="false" href={'/user/preferences/set/admin_navigation_class_groups/1'|ezurl} title="{'Show class groups.'|i18n( 'design/admin/class/view' )}">{'Class groups'|i18n( 'design/admin/class/view' )}</a>
    {/if}
    {if ezpreference( 'admin_navigation_class_temlates' )}
    <a class="current" aria-pressed="true" href={'/user/preferences/set/admin_navigation_class_temlates/0'|ezurl} title="{'Hide override templates.'|i18n( 'design/admin/class/view' )}">{'Override templates'|i18n( 'design/admin/class/view' )}</a>
    {else}
    <a aria-pressed="false" href={'/user/preferences/set/admin_navigation_class_temlates/1'|ezurl} title="{'Show override templates.'|i18n( 'design/admin/class/view' )}">{'Override templates'|i18n( 'design/admin/class/view' )}</a>
    {/if}
    {if fetch( content, translation_list )|count|gt( 1 )}
    {if ezpreference( 'admin_navigation_class_translations' )}
    <a class="current" aria-pressed="true" href={'/user/preferences/set/admin_navigation_class_translations/0'|ezurl} title="{'Hide available translations.'|i18n( 'design/admin/class/view' )}">{'Translations'|i18n( 'design/admin/class/view' )}</a>
    {else}
    <a aria-pressed="false" href={'/user/preferences/set/admin_navigation_class_translations/1'|ezurl} title="{'Show available translations.'|i18n( 'design/admin/class/view' )}">{'Translations'|i18n( 'design/admin/class/view' )}</a>
    {/if}
    {/if}
</p>
</nav>
