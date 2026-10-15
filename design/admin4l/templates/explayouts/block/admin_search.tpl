{* Admin block (explayouts). Markup copied from admin4 page_topmenu.tpl lines, keep in step with it. *}
{if is_unset( $ui_context_edit )}{def $ui_context_edit = eq( $ui_context, 'edit' )}{/if}

    <div id="header-search" class="header-search">
        {* the sections of the search popup, fetched once for both copies of the search (page_search.tpl reads them) *}
        {def $search_sections = cond( $ui_context_edit, array(), fetch( 'section', 'list' ) )}
        {include uri='design:page_search.tpl'}
    </div>
