{* Confirming that API keys are revoked (oauthadmin/keyaction). Names every key with its owner and scopes; Revoke
   posts the same keys again with ConfirmRevoke, Cancel goes back to the list the keys were chosen on.

   The same file is in design/admin and design/admin4. It uses the shared admin components (components.css) and
   stylesheets/modules/setup.css (.a4-setup-oauth), in light and dark. *}

<div class="context-block a4-page a4-setup-oauth">

<div class="box-header"><div class="box-ml">
<h1 class="context-title">{'Confirm revocation'|i18n( 'design/admin/oauthadmin' )}</h1>
</div></div>

<div class="box-bc"><div class="box-ml"><div class="box-content">

{include uri='design:oauthadmin/parts/tabs.tpl' current='keys' subpage=true()}

<form method="post" action={'oauthadmin/keyaction'|ezurl}>
<div class="a4-notice is-bad" role="alert">
    <h2 class="a4-h2">{if $keys|count|eq(1)}{'Revoke this API key?'|i18n( 'design/admin/oauthadmin' )}{else}{'Revoke these %count API keys?'|i18n( 'design/admin/oauthadmin',, hash( '%count', $keys|count ) )}{/if}</h2>
    <p>{'Every request with a revoked key is refused at once, and the key cannot be made valid again. The owner sees that it was revoked and can make a new one if they still may.'|i18n( 'design/admin/oauthadmin' )}</p>
    <ul class="a4-setup-confirm-list">
    {foreach $keys as $key}
        <li>
            <strong>{$key.name|wash}</strong> <code>{$key.key_prefix|wash}_&hellip;</code>
            &middot; {if $key.owner_name|ne('')}{$key.owner_name|wash}{else}{'Removed user %id'|i18n( 'design/admin/oauthadmin',, hash( '%id', $key.user_id ) )}{/if}
            &middot; {$key.scope_list|implode( ', ' )|wash}
            <input type="hidden" name="RevokeKeyIDArray[]" value="{$key.id}" />
        </li>
    {/foreach}
    </ul>
</div>
<input type="hidden" name="ConfirmRevoke" value="1" />
<input type="hidden" name="RedirectURI" value="{$redirect_uri|wash}" />
<div class="a4-toolbar">
    <div class="a4-actions">
        <button class="a4-btn a4-btn-danger" type="submit" name="RevokeKeyListButton" value="1"><span class="a4-icon a4-bi-x-circle-fill" aria-hidden="true"></span>{'Revoke'|i18n( 'design/admin/oauthadmin' )}</button>
        <a class="a4-btn" href={$redirect_uri|ezurl}>{'Cancel'|i18n( 'design/admin/oauthadmin' )}</a>
    </div>
</div>
</form>

</div></div></div>

</div>
