{* The confirmation of a destructive action of Dashboard > Recent content (moderation/action): moving to the trash or
   deleting the selected items. Confirm asks "Verify it's you" (site.ini [ReauthGroup_delete]) and then runs it.

   Variables: form (expModerationAction::validate()), action_name, objects (id, name), background (it runs as a job),
   return_url. The same file is in design/admin and design/admin4. *}
<div class="a4-page a4-moderation" id="exp-moderation-confirm">

<div class="a4-pagehead">
    <div class="a4-pagehead-main">
        <h1 class="a4-pagehead-title context-title">{'%action: %count items'|i18n( 'design/admin/moderation',, hash( '%action', $action_name, '%count', $form.ids|count ) )|wash}</h1>
        <p class="a4-pagehead-sub">{if eq( $form.action, 'delete' )}{'Deleting removes the items for good, with all their versions and translations. It cannot be undone.'|i18n( 'design/admin/moderation' )}{else}{'The items go to the trash, from where they can be restored.'|i18n( 'design/admin/moderation' )}{/if}</p>
    </div>
</div>

<div class="a4-notice{if eq( $form.action, 'delete' )} is-bad{else} is-warn{/if}" role="alert">
    <p>{'Each item is checked against your own permissions; an item with sub items is left as it is (remove it in the content structure). You will be asked to verify that it is you.'|i18n( 'design/admin/moderation' )}</p>
    {if $background}<p>{'This runs in the background: its progress page opens, and the result per item is shown when it is done.'|i18n( 'design/admin/moderation' )}</p>{/if}
</div>

<form method="post" action={'moderation/action'|ezurl}>
    <input type="hidden" name="ReturnURL" value="{$return_url|wash}" />
    <input type="hidden" name="ModerationAction" value="{$form.action|wash}" />
    {foreach $form.ids as $id}<input type="hidden" name="ObjectIDList[]" value="{$id}" />{/foreach}

    <section class="a4-card a4-section" aria-labelledby="moderation-confirm-items">
        <div class="a4-card-head"><h2 class="a4-h3 a4-card-title" id="moderation-confirm-items">{'The selected items'|i18n( 'design/admin/moderation' )}</h2></div>
        <div class="a4-card-body">
            <ul class="a4-plain">
            {foreach $objects as $object max 50}<li>{if $object.name}{$object.name|wash}{else}<span class="a4-muted">{'(no name)'|i18n( 'design/admin/moderation' )}</span>{/if} <span class="a4-meta">{'ID %id'|i18n( 'design/admin/moderation',, hash( '%id', $object.id ) )}</span></li>{/foreach}
            </ul>
            {if $objects|count|gt( 50 )}<p class="a4-meta">{'and %count more'|i18n( 'design/admin/moderation',, hash( '%count', sub( $objects|count, 50 ) ) )}</p>{/if}
        </div>
    </section>

    <div class="a4-actions">
        <button type="submit" class="a4-btn a4-btn-danger" name="ConfirmDestructiveButton" value="1">{$action_name|wash}</button>
        <button type="submit" class="a4-btn" name="CancelButton" value="1">{'Cancel'|i18n( 'design/admin/moderation' )}</button>
    </div>
</form>

</div>
