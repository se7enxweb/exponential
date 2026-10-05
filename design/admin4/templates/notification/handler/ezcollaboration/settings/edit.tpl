{* Collaboration notification: which kinds of collaboration items send me e-mail. The checkboxes keep their names
   (CollaborationHandlerSelection_ezcollaboration[]); CollaborationHandlerSelection tells the handler the form was sent. *}
{def $handlers = $handler.collaboration_handlers
     $selection = $handler.collaboration_selections
     $shown = 0}
<form class="nf-card" id="nf-collab" method="post" action={'notification/settings'|ezurl}>
    <h2>{'Collaboration notification'|i18n( 'design/admin/notification/settings' )}</h2>
    <p class="nf-lead">{'Choose which collaboration items you want to get notifications for. An item is, for example, content that waits for your approval, and the comments about it.'|i18n( 'design/admin/notification/settings' )}</p>
    <input type="hidden" name="CollaborationHandlerSelection" value="1" />
    <div class="nf-types">
{foreach $handlers as $item}
    {def $types = $item.notification_types}
    {if or( $types, $types|gt( 0 ) )}
        {set $shown = $shown|inc}
        {if is_array( $types )}
        <b>{$item.info.type-name|wash}</b>
            {foreach $types as $type}
        <label><input type="checkbox" name="CollaborationHandlerSelection_{$handler.id_string}[]" value="{$item.info.type-identifier}_{$type.value}"{if $selection|contains( concat( $item.info.type-identifier, '_', $type.value ) )} checked="checked"{/if} /> {$type.name|wash}</label>
            {/foreach}
        {else}
        <label><input type="checkbox" name="CollaborationHandlerSelection_{$handler.id_string}[]" value="{$item.info.type-identifier}"{if $selection|contains( $item.info.type-identifier )} checked="checked"{/if} /> {$item.info.type-name|wash}</label>
        {/if}
    {/if}
    {undef $types}
{/foreach}
    </div>
{if $shown|eq( 0 )}
    <p class="nf-hint">{'No kind of collaboration item sends notifications on this site.'|i18n( 'design/admin/notification/settings' )}</p>
{else}
    <div class="nf-actions"><input class="nf-btn primary" type="submit" name="SaveCollaboration" value="{'Save collaboration settings'|i18n( 'design/admin/notification/settings' )}" /></div>
{/if}
</form>
