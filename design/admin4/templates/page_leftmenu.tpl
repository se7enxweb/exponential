<div id="leftmenu" class="sidebar left" role="navigation" aria-label="{'Side menu'|i18n( 'design/admin/pagelayout' )|wash}">
    {* The width grip: drag it, or focus it and use the arrow keys (ezadmin_menubar.js sets its value) *}
    <div id="leftmenu-resize-handle" class="leftmenu-resize-handle" role="separator" aria-orientation="vertical" aria-controls="leftmenu" tabindex="0" aria-label="{'Width of the left column'|i18n( 'design/admin/pagelayout' )|wash}"></div>
    <div id="leftmenu-design">
        {if is_set( $module_result.left_menu )}
            {include uri=$module_result.left_menu}
        {else}
            {* 
                    Get navigationpart identifier variable depends if the call is an contenobject
                    or a custom module 
                *}
            {* the page's navigation part, or Setup's for a page of a tab the Setup menu stands in for *}
            {def $navigation_part_name = ''}
            {set-block variable=navigation_part_name}{include uri='design:parts/a4_navigation_part.tpl'}{/set-block}
            {set $navigation_part_name = $navigation_part_name|trim}
            {* 
                    Include automatically the menu template for the $navigation_part_name
                    ez $part_name navigationpart =>  parts/$part_name/menu.tpl
                *}
            {def $extract_length = sub( count_chars( $navigation_part_name ), '14' ) 
                    $part_name = $navigation_part_name|extract( '2', $extract_length )}

            {* a kernel navigation part with the exp prefix: exp<name>navigationpart => parts/<name>/menu.tpl *}
            {if $navigation_part_name|begins_with( 'exp' )}{set $part_name = $navigation_part_name|explode( 'navigationpart' )[0]|extract( 3 )}{/if}
            {include uri=concat( 'design:parts/', $part_name, '/menu.tpl' )}

            {undef $extract_length $part_name $navigation_part_name}
        {/if}
    </div>
</div>