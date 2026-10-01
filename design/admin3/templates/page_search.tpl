{* The header search: the field, the search button and the scope popup (section, where to search, advanced search).
   Included twice by page_topmenu.tpl, in the header and in the narrow screen menu: $search_box ('' or '-mobile')
   keeps the ids of the two copies apart, and the script works on the copy it belongs to, never on an id. *}
{def $search_node_id = first_set( $search_subtree_array[0], $module_result.path[0].node_id, 1 )
     $search_title = "Search in all content"|i18n( 'design/admin/pagelayout' )
     $section_id = -1
     $box = first_set( $search_box, '' )}
{if ezhttp_hasvariable( 'SectionID', 'get' )}
    {set $section_id = ezhttp( 'SectionID', 'get' )}
{/if}
<div class="searchblock" data-searchblock="1">
<form action={'/content/search/'|ezurl} method="get" role="search">
    {if $ui_context_edit}
        <div class="searchtextwrapper">
            <input id="searchtext{$box}" class="form-control searchtext disabled" name="SearchText" type="search" size="20" value="{if is_set( $search_text )}{$search_text|wash}{/if}" disabled="disabled" title="{$search_title|wash}" placeholder="{'Search...'|i18n( 'design/admin/pagelayout' )}" />
        </div>
        <button id="searchbutton{$box}" class="searchbuttonfield disabled" name="SearchButton" type="submit" value="{'Search'|i18n( 'design/admin/pagelayout' )}" disabled="disabled" title="{'Search'|i18n( 'design/admin/pagelayout' )}"><span class="hide">{'Search'|i18n( 'design/admin/pagelayout' )}</span></button>
    {else}
        {if $search_node_id|gt( 1 )}
            {set $search_title = "Search in '%node'"|i18n( 'design/admin/pagelayout',, hash( '%node', fetch( 'content', 'node', hash( 'node_id', $search_node_id ) ).name ) )}
        {/if}
        {def $disabled = false()
             $nd = 1
             $current_loc = true()}
        {if eq( $ui_context, 'edit' )}
            {set $disabled = true()}
        {else}
            {if is_set( $module_result.node_id )}
                {set $nd = $module_result.node_id}
            {else}
                {if is_set( $search_subtree_array )}
                    {if count( $search_subtree_array )|eq( 1 )}
                        {if $search_subtree_array[0]|ne( 1 )}
                            {set $nd = $search_subtree_array[0]}
                        {else}
                            {set $disabled = true()}
                        {/if}
                        {set $current_loc = false()}
                    {else}
                        {set $disabled = true()}
                    {/if}
                {else}
                    {set $disabled = true()}
                {/if}
            {/if}
        {/if}
        <div class="searchtextwrapper">
            <button type="button" class="searchscope" id="searchscope{$box}" aria-haspopup="true" aria-expanded="false" aria-controls="searchscope-pane{$box}" title="{'Search scope'|i18n( 'design/admin/pagelayout' )}"><span class="hide">{'Search scope'|i18n( 'design/admin/pagelayout' )}</span></button>
            <input id="searchtext{$box}" class="form-control searchtext" name="SearchText" type="search" size="20" value="{if is_set( $search_text )}{$search_text|wash}{/if}" title="{$search_title|wash}" placeholder="{$search_title|wash}" aria-label="{$search_title|wash}" />
        </div>
        <button id="searchbutton{$box}" class="searchbuttonfield" name="SearchButton" type="submit" value="{'Search'|i18n( 'design/admin/pagelayout' )}" title="{'Search'|i18n( 'design/admin/pagelayout' )}"><span class="hide">{'Search'|i18n( 'design/admin/pagelayout' )}</span></button>
        {if eq( $ui_context, 'browse' )}
            <input name="Mode" type="hidden" value="browse" />
            <input name="BrowsePageLimit" type="hidden" value="{min( ezpreference( 'admin_list_limit' ), 3)|choose( 10, 10, 25, 50 )}" />
        {/if}

        <div class="searchscope-pane" id="searchscope-pane{$box}" role="dialog" aria-label="{'Search scope'|i18n( 'design/admin/pagelayout' )}" hidden="hidden">
            <div class="searchscope-title">
                {'Search scope'|i18n( 'design/admin/pagelayout' )}
                <button type="button" class="close" title="{'Close'|i18n( 'design/admin/pagelayout' )}"><span class="hide">{'Close'|i18n( 'design/admin/pagelayout' )}</span></button>
            </div>
            <div class="searchscope-body">
                <label class="searchscope-section">{'Section'|i18n( 'design/admin/pagelayout' )}
                <select name="SectionID"{if $disabled} disabled="disabled"{/if}>
                    <option value="-1">{'All'|i18n( 'design/admin/pagelayout' )}</option>
                    {foreach fetch( 'section', 'list' ) as $section}
                    <option value="{$section.id}"{if eq( $section.id, $section_id )} selected="selected"{/if}>{$section.name|wash()}</option>
                    {/foreach}
                </select>
                </label>
                <fieldset>
                    <legend>{'Where'|i18n( 'design/admin/pagelayout' )}</legend>
                    <label><input type="radio" name="SubTreeArray" value="{$search_node_id|wash}" checked="checked" data-label="{$search_title|wash}" />{$search_title|wash}</label>
                    {if $search_node_id|ne( 1 )}
                    <label{if $disabled} class="disabled"{/if}><input type="radio" name="SubTreeArray" value="1"{if $disabled} disabled="disabled"{/if} data-label="{'Search in all content'|i18n( 'design/admin/pagelayout' )|wash}" />{'Search in all content'|i18n( 'design/admin/pagelayout' )}</label>
                    {/if}
                    {if and( $nd|ne( 1 ), $nd|ne( $search_node_id ) )}
                    <label{if $disabled} class="disabled"{/if}><input type="radio" name="SubTreeArray" value="{$nd}"{if $disabled} disabled="disabled"{/if} data-label="{if $current_loc}{'Search only from the current location'|i18n( 'design/admin/pagelayout' )|wash}{else}{'The same location'|i18n( 'design/admin/pagelayout' )|wash}{/if}" />{if $current_loc}{'Current location'|i18n( 'design/admin/pagelayout' )}{else}{'The same location'|i18n( 'design/admin/pagelayout' )}{/if}</label>
                    {/if}
                </fieldset>
                {if ne( $ui_context, 'browse' )}
                <p class="searchscope-advanced"><a href={'/content/advancedsearch'|ezurl} title="{'Advanced search.'|i18n( 'design/admin/pagelayout' )}">{'Advanced search'|i18n( 'design/admin/pagelayout' )}</a></p>
                {/if}
            </div>
        </div>
        {undef $disabled $nd $current_loc}
    {/if}
</form>
</div>

{if $box|eq( '' )}
<script type="text/javascript">
{literal}
/* One handler for every header search on the page (the header's and the narrow screen menu's): each works on its
   own field and popup. The popup opens from the scope button, under the field; Escape, a click outside it or the
   close button shut it; choosing where to search shows in the field's hint, the typed text stays. */
(function() {
    if ( window.expHeaderSearch ) return;
    window.expHeaderSearch = true;
    function blockOf( el ) { return el && el.closest ? el.closest( '[data-searchblock]' ) : null; }
    function pane( block ) { return block.querySelector( '.searchscope-pane' ); }
    function toggle( block ) { return block.querySelector( '.searchscope' ); }
    function close( block, focusToggle ) {
        var p = pane( block ), t = toggle( block );
        if ( !p || p.hidden ) return;
        p.hidden = true;
        if ( t ) { t.setAttribute( 'aria-expanded', 'false' ); if ( focusToggle ) t.focus(); }
    }
    function closeAll( except ) {
        document.querySelectorAll( '[data-searchblock]' ).forEach( function( b ) { if ( b !== except ) close( b, false ); } );
    }
    function open( block ) {
        var p = pane( block ), t = toggle( block );
        if ( !p ) return;
        closeAll( block );
        p.hidden = false;
        if ( t ) t.setAttribute( 'aria-expanded', 'true' );
        var first = p.querySelector( 'input:checked:not([disabled]), select:not([disabled]), input:not([disabled])' );
        if ( first ) first.focus();
    }
    document.addEventListener( 'click', function( e ) {
        var block = blockOf( e.target );
        if ( e.target.closest && e.target.closest( '.searchscope' ) && block ) {
            e.preventDefault();
            if ( pane( block ).hidden ) open( block ); else close( block, true );
            return;
        }
        if ( e.target.closest && e.target.closest( '.searchscope-pane .close' ) && block ) {
            e.preventDefault();
            close( block, true );
            return;
        }
        if ( !block || !( e.target.closest && e.target.closest( '.searchscope-pane' ) ) )
            closeAll( null );
    } );
    document.addEventListener( 'keydown', function( e ) {
        if ( e.key !== 'Escape' ) return;
        var block = blockOf( document.activeElement );
        if ( block ) close( block, true ); else closeAll( null );
    } );
    document.addEventListener( 'change', function( e ) {
        if ( !e.target.matches || !e.target.matches( '[data-searchblock] input[name=SubTreeArray]' ) ) return;
        var field = blockOf( e.target ).querySelector( 'input.searchtext' ), label = e.target.getAttribute( 'data-label' );
        if ( field && label ) { field.placeholder = label; field.title = label; field.setAttribute( 'aria-label', label ); }
    } );
})();
{/literal}
</script>
{/if}
{undef $search_node_id $section_id $box}
