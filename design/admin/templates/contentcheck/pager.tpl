{* The pagination of a result list of the content checks (shared by every check): the page sizes and the page links,
   the filters kept in the address (page_uri_suffix). Placed after the table and the bulk-action bar.

     {include uri='design:contentcheck/pager.tpl' page_uri=$page_uri page_uri_suffix=$page_uri_suffix item_count=$count
              view_parameters=$view_parameters limit=$limit limit_choices=$limit_choices limit_preference=$limit_preference}

   Variables: page_uri, page_uri_suffix (starting with "?"), item_count, view_parameters (with the offset), limit,
   limit_choices, limit_preference (the user preference of the page size), offset_name (default offset: a page with
   two lists gives the second its own, e.g. file_offset). A list paged by position without a total (item_count=-1)
   passes start_url (empty on the first page) and next_url (empty on the last) instead of the page links; size_title
   is the title of a page size link (default "Show %count items per page."). The same file is in design/admin and
   design/admin4. *}
{default offset_name='offset' start_url='' next_url='' size_title='Show %count items per page.'|i18n( 'kernel/contentcheck' )}
<div class="a4-listfoot">
    <p class="a4-sizes">
        <span>{'Per page'|i18n( 'kernel/contentcheck' )}:</span>
    {foreach $limit_choices as $limit_index => $limit_option}
        {if eq( $limit_option, $limit )}
        <span class="current" aria-current="true">{$limit_option}</span>
        {else}
        <a href={concat( '/user/preferences/set/', $limit_preference, '/', $limit_index|inc )|ezurl} title="{$size_title|wash|explode( '%count' )|implode( $limit_option )}">{$limit_option}</a>
        {/if}
    {/foreach}
    </p>
    <div class="a4-pager">
    {if $item_count|lt( 0 )}
        {if $start_url|ne( '' )}<a class="a4-btn" href={$start_url|ezurl}>{'From the start'|i18n( 'kernel/contentcheck' )}</a>{/if}
        {if $next_url|ne( '' )}<a class="a4-btn" href={$next_url|ezurl}>{'Next page'|i18n( 'kernel/contentcheck' )}</a>{/if}
    {else}
    {include name=navigator
             uri='design:navigator/google.tpl'
             page_uri=$page_uri
             page_uri_suffix=$page_uri_suffix
             item_count=$item_count
             view_parameters=$view_parameters
             item_limit=$limit
             offset_name=$offset_name}
    {/if}
    </div>
</div>
{/default}
