{* Confirming the removal of REST applications (oauthadmin/action). The form posts ConfirmDelete, DeleteIDArray[]
   and DeleteApplicationListButton as before; Cancel goes back to the list.

   The same file is in design/admin and design/admin4. It uses the shared admin components (components.css) and
   stylesheets/modules/setup.css (.a4-setup-oauth), in light and dark. *}

<div class="context-block a4-page a4-setup-oauth">

<div class="box-header"><div class="box-ml">
<h1 class="context-title">{'Confirm removal'|i18n( 'extension/oauthadmin' )|wash}</h1>
</div></div>

<div class="box-bc"><div class="box-ml"><div class="box-content">

{include uri='design:oauthadmin/parts/tabs.tpl' current='applications' subpage=true()}

<div class="a4-notice is-bad" role="alert">
    <h2 class="a4-h2">{if $applications|count|eq(1)}{'Are you sure you want to remove this application?'|i18n( 'extension/oauthadmin' )}{else}{'Are you sure you want to remove these applications?'|i18n( 'extension/oauthadmin' )}{/if}</h2>
    <p>{'Its users can no longer sign in through it: the authorizations and tokens it was given end with it. This cannot be undone.'|i18n( 'design/admin/oauthadmin' )}</p>
    <ul class="a4-setup-confirm-list">
    {foreach $applications as $application}
        <li><strong>{$application.name|wash}</strong> <code>{$application.client_id|wash}</code></li>
    {/foreach}
    </ul>
</div>

<div class="a4-toolbar">
    <div class="a4-actions">
        <form method="post" action={$module.functions.action.uri|ezurl}>
            <input type="hidden" name="ConfirmDelete" value="1" />
            {foreach $applications as $application}
            <input type="hidden" name="DeleteIDArray[]" value="{$application.id}" />
            {/foreach}
            <button class="a4-btn a4-btn-danger" type="submit" name="DeleteApplicationListButton" value="{'Confirm'|i18n( 'extension/oauthadmin' )}" title="{'Confirm removal of these applications.'|i18n( 'extension/oauthadmin' )}"><span class="a4-icon a4-bi-trash" aria-hidden="true"></span>{'Confirm'|i18n( 'extension/oauthadmin' )}</button>
        </form>
        <form method="get" action={$module.functions.list.uri|ezurl}>
            <button class="a4-btn" type="submit" name="Cancel" value="{'Cancel'|i18n( 'extension/oauthadmin' )}">{'Cancel'|i18n( 'extension/oauthadmin' )}</button>
        </form>
    </div>
</div>

</div></div></div>

</div>
