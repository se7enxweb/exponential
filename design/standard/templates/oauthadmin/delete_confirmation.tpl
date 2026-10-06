{* Confirming the removal of REST applications (oauthadmin/action). The form posts ConfirmDelete, DeleteIDArray[]
   and DeleteApplicationListButton as before; Cancel goes back to the list. Styles: parts/style.tpl. *}
{include uri='design:oauthadmin/parts/style.tpl'}

<div class="context-block exp-oauth">

<div class="box-header"><div class="box-ml">
<h1 class="context-title">{'Confirm removal'|i18n( 'extension/oauthadmin' )|wash}</h1>
</div></div>

<div class="box-bc"><div class="box-ml"><div class="box-content">

<div class="exp-confirm" role="alertdialog" aria-labelledby="oauth-delete-title" aria-describedby="oauth-delete-text">
    <h2 class="exp-h2" id="oauth-delete-title">{if $applications|count|eq(1)}{'Are you sure you want to remove this application?'|i18n( 'extension/oauthadmin' )}{else}{'Are you sure you want to remove these applications?'|i18n( 'extension/oauthadmin' )}{/if}</h2>
    <p id="oauth-delete-text">{'Its users can no longer sign in through it: the authorizations and tokens it was given end with it. This cannot be undone.'|i18n( 'design/admin/oauthadmin' )}</p>
    <ul>
    {foreach $applications as $application}
        <li><strong>{$application.name|wash}</strong> <code>{$application.client_id|wash}</code></li>
    {/foreach}
    </ul>
    <div class="exp-actions">
        <form method="post" action={$module.functions.action.uri|ezurl}>
            <input type="hidden" name="ConfirmDelete" value="1" />
            {foreach $applications as $application}
            <input type="hidden" name="DeleteIDArray[]" value="{$application.id}" />
            {/foreach}
            <button class="exp-btn exp-btn-danger" type="submit" name="DeleteApplicationListButton" value="{'Confirm'|i18n( 'extension/oauthadmin' )}" title="{'Confirm removal of these applications.'|i18n( 'extension/oauthadmin' )}">{'Confirm'|i18n( 'extension/oauthadmin' )}</button>
        </form>
        <form method="get" action={$module.functions.list.uri|ezurl}>
            <button class="exp-btn" type="submit" name="Cancel" value="{'Cancel'|i18n( 'extension/oauthadmin' )}">{'Cancel'|i18n( 'extension/oauthadmin' )}</button>
        </form>
    </div>
</div>

</div></div></div>

</div>
