{* The parts of the class page below its attributes: the chips that show or hide them (class/window_controls.tpl,
   remembered as user preferences, as before) and the parts themselves: the class groups, the translations and the
   override templates of the class. The same file is in design/admin and design/admin4. *}
{include uri='design:class/window_controls.tpl'}

{* Class groups window. *}
{if ezpreference( 'admin_navigation_class_groups' )}
    {include uri='design:class/groups.tpl'}
{/if}

{* Class translations window. *}
{if ezpreference( 'admin_navigation_class_translations' )}
    {include uri='design:class/translations.tpl'}
{/if}

{* Class override templates window. *}
{if ezpreference( 'admin_navigation_class_temlates' )}
    {include uri='design:class/templates.tpl'}
{/if}
