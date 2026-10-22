{* The import and export entries of a context as a list (the transfer registry,
   doc/eng-us/features/6.0/transfer-registry.md): the class page shows the "class" actions of every tool the user may
   use. Nothing when there is no entry. Shared admin components only (a4-card), no CSS of its own.
   Parameters: $context (node, container, class, setup), $values (hash of placeholders, '%classID%' => 2),
   optional $direction (import or export), optional $title. *}
{def $transfer_context_actions = transfer_actions( $context, first_set( $values, hash() ), first_set( $direction, '' ) )}
{if $transfer_context_actions|count}
<section class="a4-card" data-transfer="actions" aria-labelledby="transfer-actions-{$context|wash}">
    <div class="a4-card-head"><h2 class="a4-card-title" id="transfer-actions-{$context|wash}">{first_set( $title, 'Import and export'|i18n( 'design/admin/transfer' ) )|wash}</h2></div>
    <div class="a4-card-body">
    <ul class="a4-plain">
    {foreach $transfer_context_actions as $transfer_context_action}
        <li><a href={$transfer_context_action.url|ezurl}>{$transfer_context_action.label|wash}</a> <span class="a4-tag">{$transfer_context_action.name|wash}</span></li>
    {/foreach}
    </ul>
    <p><a href={'exphelp/viewer/help/import-export-tools.md'|ezurl}>{'Which import or export tool do I need?'|i18n( 'design/admin/transfer' )}</a></p>
    </div>
</section>
{/if}
{undef $transfer_context_actions}
