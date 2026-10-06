{* Confirming that API keys are revoked (oauthadmin/keyaction). Names every key with its owner and scopes; Revoke
   posts the same keys again with ConfirmRevoke, Cancel goes back to the list the keys were chosen on.
   Styles: parts/style.tpl. *}
{include uri='design:oauthadmin/parts/style.tpl'}

<div class="context-block exp-oauth">

<div class="box-header"><div class="box-ml">
<h1 class="context-title">{'Confirm revocation'|i18n( 'design/admin/oauthadmin' )}</h1>
</div></div>

<div class="box-bc"><div class="box-ml"><div class="box-content">

<form method="post" action={'oauthadmin/keyaction'|ezurl}>
<div class="exp-confirm" role="alertdialog" aria-labelledby="oauth-revoke-title" aria-describedby="oauth-revoke-text">
    <h2 class="exp-h2" id="oauth-revoke-title">{if $keys|count|eq(1)}{'Revoke this API key?'|i18n( 'design/admin/oauthadmin' )}{else}{'Revoke these %count API keys?'|i18n( 'design/admin/oauthadmin',, hash( '%count', $keys|count ) )}{/if}</h2>
    <p id="oauth-revoke-text">{'Every request with a revoked key is refused at once, and the key cannot be made valid again. The owner sees that it was revoked and can make a new one if they still may.'|i18n( 'design/admin/oauthadmin' )}</p>
    <ul>
    {foreach $keys as $key}
        <li>
            <strong>{$key.name|wash}</strong> <code>{$key.key_prefix|wash}_&hellip;</code>
            &middot; {if $key.owner_name|ne('')}{$key.owner_name|wash}{else}{'Removed user %id'|i18n( 'design/admin/oauthadmin',, hash( '%id', $key.user_id ) )}{/if}
            &middot; <span class="exp-meta">{$key.scope_list|implode( ', ' )|wash}</span>
            <input type="hidden" name="RevokeKeyIDArray[]" value="{$key.id}" />
        </li>
    {/foreach}
    </ul>
    <input type="hidden" name="ConfirmRevoke" value="1" />
    <input type="hidden" name="RedirectURI" value="{$redirect_uri|wash}" />
    <div class="exp-actions">
        <button class="exp-btn exp-btn-danger" type="submit" name="RevokeKeyListButton" value="1">{'Revoke'|i18n( 'design/admin/oauthadmin' )}</button>
        <a class="exp-btn" href={$redirect_uri|ezurl}>{'Cancel'|i18n( 'design/admin/oauthadmin' )}</a>
    </div>
</div>
</form>

</div></div></div>

</div>
