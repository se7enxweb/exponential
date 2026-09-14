{* One list's navigator: where you are, and the links to everywhere else.

   The page it belongs to shows two lists, so every link carries the pager's
   own offset name and the suffix holding the other list's position - paging
   one list must not send the other back to its first page. *}

{default pager=false()
         page_uri='/rss/list'
         name=''}

{if $pager}
<div class="context-toolbar rss-pagination">

<div class="rss-pagination-count">
{if $pager.count}
    {'Showing %from to %to of %count'|i18n( 'design/admin/rss/list',,
        hash( '%from', $pager.from, '%to', $pager.to, '%count', $pager.count ) )}
{else}
    {'Nothing to show'|i18n( 'design/admin/rss/list' )}
{/if}
</div>

{if $pager.needed}
<div class="pagenavigator rss-pagination-pages">
<p>
    {if $pager.has_previous}
    <span class="previous"><a
        href={concat( $page_uri, '/(', $pager.offset_name, ')/', $pager.first, $pager.suffix )|ezurl}
        title="{'First page'|i18n( 'design/admin/rss/list' )}">&laquo;</a></span>
    <span class="previous"><a
        href={concat( $page_uri, '/(', $pager.offset_name, ')/', $pager.previous, $pager.suffix )|ezurl}
        title="{'Previous page'|i18n( 'design/admin/rss/list' )}">&lsaquo;&nbsp;{'Previous'|i18n( 'design/admin/rss/list' )}</a></span>
    {else}
    <span class="previous disabled">&laquo;</span>
    <span class="previous disabled">&lsaquo;&nbsp;{'Previous'|i18n( 'design/admin/rss/list' )}</span>
    {/if}

    {foreach $pager.pages as $rss_page}
        {if $rss_page.current}<span class="current">{$rss_page.number}</span>
        {else}<a href={concat( $page_uri, '/(', $pager.offset_name, ')/', $rss_page.offset, $pager.suffix )|ezurl}>{$rss_page.number}</a>
        {/if}
    {/foreach}

    {if $pager.has_next}
    <span class="next"><a
        href={concat( $page_uri, '/(', $pager.offset_name, ')/', $pager.next, $pager.suffix )|ezurl}
        title="{'Next page'|i18n( 'design/admin/rss/list' )}">{'Next'|i18n( 'design/admin/rss/list' )}&nbsp;&rsaquo;</a></span>
    <span class="next"><a
        href={concat( $page_uri, '/(', $pager.offset_name, ')/', $pager.last, $pager.suffix )|ezurl}
        title="{'Last page'|i18n( 'design/admin/rss/list' )}">&raquo;</a></span>
    {else}
    <span class="next disabled">{'Next'|i18n( 'design/admin/rss/list' )}&nbsp;&rsaquo;</span>
    <span class="next disabled">&raquo;</span>
    {/if}
</p>
<p class="rss-pagination-of">{'Page %page of %pages'|i18n( 'design/admin/rss/list',,
    hash( '%page', $pager.page, '%pages', $pager.page_count ) )}</p>
</div>
{/if}

</div>
{/if}
