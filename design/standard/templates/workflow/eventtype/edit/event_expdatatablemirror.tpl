{* The workflow event "Mirror to data tables": which synchronisations the published object is written to (none
   chosen: those of the object's class). *}
<fieldset class="block">
    <legend>{'Synchronisations'|i18n( 'design/admin/datatable' )}</legend>
    {if $event.syncs|count}
    {foreach $event.syncs as $s}
    <label><input type="checkbox" name="WorkflowEvent_event_expdatatablemirror_syncs_{$event.id}[]" value="{$s.id}"{if $event.selected_syncs|contains( $s.id )} checked="checked"{/if} /> {$s.name|wash} ({$s.content_class|wash} &rarr; {$s.class_name|wash})</label><br />
    {/foreach}
    <p>{'With none ticked, the object is written to every synchronisation of its class whose subtree holds it.'|i18n( 'design/admin/datatable' )}</p>
    {else}
    <p>{'There is no data table synchronisation yet (Setup > Data tables > Synchronisations).'|i18n( 'design/admin/datatable' )}</p>
    {/if}
</fieldset>
