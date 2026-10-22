{* The workflow event "Mirror to data tables" in a workflow's view. *}
{if $event.selected_syncs|count}
{foreach $event.syncs as $s}{if $event.selected_syncs|contains( $s.id )}{$s.name|wash}{delimiter}, {/delimiter}{/if}{/foreach}
{else}
{'The synchronisations of the published object\'s class'|i18n( 'design/admin/datatable' )}
{/if}
