{* The tools a search finds by task words (the transfer registry, doc/eng-us/features/6.0/transfer-registry.md): above
   the results of the administration's search, up to three import or export tools that match the words ("import csv",
   "copy to another site", "Formular exportieren"), each with its description. Nothing when no tool matches or the
   search text is empty. Shared admin components only (a4-notice), no CSS of its own.
   Parameters: $query (the search text), optional $limit (default 3). *}
{if and( is_set( $query ), $query|trim|ne( '' ) )}
{def $transfer_found = transfer_tools( hash( 'query', $query ) )}
{if $transfer_found|count}
<aside class="a4-notice" data-transfer="search" aria-labelledby="transfer-search-title">
    <p class="a4-notice-title" id="transfer-search-title"><strong>{'Import and export tools for this task'|i18n( 'design/admin/transfer' )}</strong></p>
    <ul>
    {foreach $transfer_found as $transfer_found_tool max first_set( $limit, 3 )}
        <li><a href={$transfer_found_tool.url|ezurl}>{$transfer_found_tool.name|wash}</a>{if $transfer_found_tool.description}: {$transfer_found_tool.description|wash}{/if}</li>
    {/foreach}
    </ul>
    <p><a href={'exphelp/viewer/help/import-export-tools.md'|ezurl}>{'Which import or export tool do I need?'|i18n( 'design/admin/transfer' )}</a></p>
</aside>
{/if}
{undef $transfer_found}
{/if}
