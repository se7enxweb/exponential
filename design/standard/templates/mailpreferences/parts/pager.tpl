{* Previous / next for the administrator's lists. Variables: page_uri (the list's address, with its filters as
   ordered parameters), total, offset, limit, query (a query string for the filters, with "?", or ''). *}
{if $total|gt( $limit )}
{def $query_string = first_set( $query, '' )}
<nav class="mp-pager" aria-label="{'Pages'|i18n( 'design/admin/mailpreferences' )}">
    <span>{'%from to %to of %total'|i18n( 'design/admin/mailpreferences',, hash( '%from', sum( $offset, 1 ), '%to', min( sum( $offset, $limit ), $total ), '%total', $total ) )}</span>
    <span>
{if $offset|gt( 0 )}
        <a class="mp-btn small" href={concat( $page_uri, '/(offset)/', max( 0, sub( $offset, $limit ) ), $query_string )|ezurl}>{'Previous'|i18n( 'design/admin/mailpreferences' )}</a>
{/if}
{if sum( $offset, $limit )|lt( $total )}
        <a class="mp-btn small" href={concat( $page_uri, '/(offset)/', sum( $offset, $limit ), $query_string )|ezurl}>{'Next'|i18n( 'design/admin/mailpreferences' )}</a>
{/if}
    </span>
</nav>
{undef $query_string}
{/if}
