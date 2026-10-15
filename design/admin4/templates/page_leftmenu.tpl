<div id="leftmenu" class="sidebar left" role="navigation" aria-label="{'Side menu'|i18n( 'design/admin/pagelayout' )|wash}">
    {* The width grip: drag it, or focus it and use the arrow keys (ezadmin_menubar.js sets its value) *}
    <div id="leftmenu-resize-handle" class="leftmenu-resize-handle" role="separator" aria-orientation="vertical" aria-controls="leftmenu" tabindex="0" aria-label="{'Width of the left column'|i18n( 'design/admin/pagelayout' )|wash}"></div>
    <div id="leftmenu-design">
        {* the page's navigation part, or Setup's for a page of a tab the Setup menu stands in for *}
        {def $navigation_part_name = ''
             $a4_own_part = first_set( $navigation_part.identifier, '' )}
        {set-block variable=navigation_part_name}{include uri='design:parts/a4_navigation_part.tpl'}{/set-block}
        {set $navigation_part_name = $navigation_part_name|trim}
        {if $a4_own_part|eq( '' )}{set $a4_own_part = first_set( $module_result.navigation_part, '' )}{/if}
        {* A view's own left menu ($module_result.left_menu, the audit pages' design:audit/menu.tpl) is drawn, unless
           the page belongs to a tab the Setup menu stands in for: then the Setup menu with the tab's group is, as the
           Setup tab is marked (doc/features/6.0/admin4-design.md, "Moving a tab into Setup") *}
        {if and( is_set( $module_result.left_menu ),
                 or( $navigation_part_name|ne( 'ezsetupnavigationpart' ), $a4_own_part|eq( 'ezsetupnavigationpart' ) ) )}
            {include uri=$module_result.left_menu}
        {else}
            {* 
                    Get navigationpart identifier variable depends if the call is an contenobject
                    or a custom module 
                *}
            {* 
                    Include automatically the menu template for the $navigation_part_name
                    ez $part_name navigationpart =>  parts/$part_name/menu.tpl
                *}
            {def $extract_length = sub( count_chars( $navigation_part_name ), '14' ) 
                    $part_name = $navigation_part_name|extract( '2', $extract_length )}

            {* a kernel navigation part with the exp prefix, named in $kernel_exp_parts (a prefix test would turn an
               extension's "experiment..." into "eriment"): exp<name>navigationpart => parts/<name>/menu.tpl; an
               extension's part without the ez prefix: <name>navigationpart => parts/<name>/menu.tpl (cutting two
               letters off it named a directory nobody has, and the left column stayed an empty card) *}
            {def $kernel_exp_parts = hash( 'expauditnavigationpart', 'audit' )}
            {if is_set( $kernel_exp_parts[ $navigation_part_name ] )}{set $part_name = $kernel_exp_parts[ $navigation_part_name ]}
            {elseif $navigation_part_name|begins_with( 'ez' )|not}{set $part_name = $navigation_part_name|explode( 'navigationpart' )[0]}{/if}
            {undef $kernel_exp_parts}
            {include uri=concat( 'design:parts/', $part_name, '/menu.tpl' )}

            {undef $extract_length $part_name}
        {/if}
        {undef $navigation_part_name $a4_own_part}
    </div>
</div>