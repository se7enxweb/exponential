{* The "Looking for…?" box of an import or export tool's main page (the transfer registry,
   doc/eng-us/features/6.0/transfer-registry.md): the other tools the user may use, the round-trip partners first
   (they read what this tool writes, or write what it reads), and the page "Which import or export tool do I need?".
   Nothing when there is no other tool. Shared admin components only (a4-notice, a4-tag), no CSS of its own.
   Parameters: $tool (the tool's id, its short name or its module), optional $limit (default 6). *}
{def $transfer_related = transfer_related( $tool )
     $transfer_limit = first_set( $limit, 6 )}
{if $transfer_related|count}
<aside class="a4-notice is-info" data-transfer="looking-for" aria-labelledby="transfer-looking-for-title">
    <p class="a4-notice-title" id="transfer-looking-for-title"><strong>{'Looking for another import or export?'|i18n( 'design/admin/transfer' )}</strong></p>
    <ul>
    {foreach $transfer_related as $transfer_entry max $transfer_limit}
        <li>
            <a href={$transfer_entry.url|ezurl}>{$transfer_entry.name|wash}</a>
            {if eq( $transfer_entry.direction, 'import' )}<span class="a4-tag">{'Import'|i18n( 'design/admin/transfer' )}</span>{elseif eq( $transfer_entry.direction, 'export' )}<span class="a4-tag">{'Export'|i18n( 'design/admin/transfer' )}</span>{else}<span class="a4-tag">{'Import and export'|i18n( 'design/admin/transfer' )}</span>{/if}
            {if $transfer_entry.partner}<span class="a4-tag is-accent">{'Works with this tool'|i18n( 'design/admin/transfer' )}</span>{/if}
            {if $transfer_entry.description}<br />{$transfer_entry.description|wash}{/if}
        </li>
    {/foreach}
    </ul>
    <p><a href={'exphelp/viewer/help/import-export-tools.md'|ezurl}>{'Which import or export tool do I need?'|i18n( 'design/admin/transfer' )}</a></p>
</aside>
{/if}
{undef $transfer_related $transfer_limit}
