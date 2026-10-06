{* The API access page of the user's account: apikey/list (doc/guides/api-keys.md, "For site users").

   Signed out: what the page is for, a sign in form that comes back here, and the way to register.
   Signed in: the key just made (its full value, this once, with a Copy button), the user's keys with Revoke (a
   confirmation first), the form to make a key (name, scopes the user may give, lifetime) for a user with
   apikey/create, and how to use a key. Every form posts to apikey/list with the form token; nothing needs
   javascript, which only adds the Copy buttons. Framed by parts/page_start.tpl and parts/page_end.tpl, which a
   design overrides to place the page in its own layout. *}
{include uri='design:apikey/parts/page_start.tpl'
         title='API access'|i18n( 'design/standard/apikey' )
         intro='Personal API keys let your own scripts and tools publish and read through the REST interface as you, without your password. Each key has a name, the scopes it may use and an end date, and you can revoke it at any time.'|i18n( 'design/standard/apikey' )}

{if $keys_enabled|not}
<div class="ak-notice ak-notice-warn" role="status"><p>{'API keys are switched off on this site: none can be made, and existing keys are not accepted.'|i18n( 'design/standard/apikey' )}</p></div>
{/if}

{if $signed_in|not}
{* ------------------------------------------------------------------ signed out *}
<div class="ak-two">
    <section class="ak-card" aria-labelledby="ak-signin-title">
        <h2 id="ak-signin-title">{'Sign in to manage your keys'|i18n( 'design/standard/apikey' )}</h2>
        <p class="ak-lead">{'Keys belong to an account and act as it, so you need to be signed in. You come back to this page afterwards.'|i18n( 'design/standard/apikey' )}</p>
        <form class="ak-form" method="post" action={'user/login'|ezurl}>
            <div class="ak-field">
                <label for="ak-login">{'Username or e-mail'|i18n( 'design/standard/apikey' )}</label>
                <input type="text" id="ak-login" name="Login" autocomplete="username" required="required" />
            </div>
            <div class="ak-field">
                <label for="ak-password">{'Password'|i18n( 'design/standard/apikey' )}</label>
                <input type="password" id="ak-password" name="Password" autocomplete="current-password" required="required" />
            </div>
            <input type="hidden" name="RedirectURI" value="/apikey/list" />
            <div class="ak-actions">
                <button type="submit" class="ak-btn ak-btn-primary" name="LoginButton" value="1">{'Sign in'|i18n( 'design/standard/apikey' )}</button>
                <a href={'user/forgotpassword'|ezurl}>{'Forgot your password?'|i18n( 'design/standard/apikey' )}</a>
            </div>
        </form>
    </section>
    <section class="ak-card" aria-labelledby="ak-register-title">
        <h2 id="ak-register-title">{'No account yet?'|i18n( 'design/standard/apikey' )}</h2>
        <p class="ak-lead">{'Register first. Once your account is active and allowed to use the API, open My account and then API access to make your first key.'|i18n( 'design/standard/apikey' )}</p>
        <div class="ak-actions"><a class="ak-btn" href={'user/register'|ezurl}>{'Create an account'|i18n( 'design/standard/apikey' )}</a></div>
    </section>
</div>
{else}
{* ------------------------------------------------------------------ signed in *}

{if $message}
<div class="ak-notice ak-notice-ok" role="status"><p>{$message|wash}</p></div>
{/if}
{if $errors|count|gt(0)}
<div class="ak-notice ak-notice-bad" role="alert">
    <p><strong>{'The key was not made:'|i18n( 'design/standard/apikey' )}</strong></p>
    <ul>{foreach $errors as $error}<li>{$error|wash}</li>{/foreach}</ul>
</div>
{/if}

{if $new_token}
<section class="ak-card ak-new" aria-labelledby="ak-new-title" tabindex="-1" id="ak-new">
    <h2 id="ak-new-title">{'Your new key "%name"'|i18n( 'design/standard/apikey',, hash( '%name', $new_key.name ) )|wash}</h2>
    <p><strong>{'Copy it now. It is shown this once and is not stored anywhere: if you lose it, revoke it and make a new one.'|i18n( 'design/standard/apikey' )}</strong></p>
    <div class="ak-token">
        <code class="ak-token-value" id="ak-token-value">{$new_token|wash}</code>
        <button type="button" class="ak-btn ak-btn-primary ak-copy" data-copy="#ak-token-value" data-done="{'The key is on the clipboard.'|i18n( 'design/standard/apikey' )}" hidden>{'Copy key'|i18n( 'design/standard/apikey' )}</button>
    </div>
    <p class="ak-muted" aria-live="polite" id="ak-copy-status"></p>
    <p>{'Try it:'|i18n( 'design/standard/apikey' )}</p>
<pre><code>curl -H "Authorization: Bearer {$new_token|wash}" \
     {$example_url|wash}</code></pre>
</section>
{/if}

{if $confirm_key}
<section class="ak-card" aria-labelledby="ak-confirm-title" style="border-color: var(--ak-bad-line); border-left: 4px solid var(--ak-bad);">
    <h2 id="ak-confirm-title">{'Revoke the key "%name"?'|i18n( 'design/standard/apikey',, hash( '%name', $confirm_key.name ) )|wash}</h2>
    <p class="ak-lead">{'Every program that uses it stops working at once, and it cannot be made valid again.'|i18n( 'design/standard/apikey' )} <code>{$confirm_key.key_prefix|wash}_&hellip;</code></p>
    <form method="post" action={'apikey/list'|ezurl}>
        <input type="hidden" name="RevokeKeyID" value="{$confirm_key.id}" />
        <div class="ak-actions">
            <button type="submit" class="ak-btn ak-btn-danger" name="ConfirmRevokeKeyButton" value="1">{'Revoke the key'|i18n( 'design/standard/apikey' )}</button>
            <a class="ak-btn ak-btn-quiet" href={'apikey/list'|ezurl}>{'Keep it'|i18n( 'design/standard/apikey' )}</a>
        </div>
    </form>
</section>
{/if}

<section class="ak-card" aria-labelledby="ak-keys-title">
    <h2 id="ak-keys-title">{'Your keys'|i18n( 'design/standard/apikey' )}</h2>
{if $keys|count|eq(0)}
    <p class="ak-lead">{'You have no API keys yet.'|i18n( 'design/standard/apikey' )}</p>
{else}
    <p class="ak-lead">{'%active of %max keys in use. The full value of a key was shown only when it was made; here it is known by its first characters.'|i18n( 'design/standard/apikey',, hash( '%active', $active_count, '%max', cond( $max_keys|gt(0), $max_keys, '&infin;' ) ) )}</p>
    <form method="post" action={'apikey/list'|ezurl}>
    <ul class="ak-keys">
    {foreach $keys as $key}
        {def $ak_status = $key.status}
        <li class="ak-key is-{$ak_status}">
            <div class="ak-key-head">
                <div class="ak-key-title">
                    <h3>{$key.name|wash}</h3>
                    <ul class="ak-badges">
                    {if $ak_status|eq('active')}
                        <li class="ak-badge ak-badge-ok">{'Active'|i18n( 'design/standard/apikey' )}</li>
                        {if $key.expiring_soon}<li class="ak-badge ak-badge-warn">{'Expires soon'|i18n( 'design/standard/apikey' )}</li>{/if}
                    {elseif $ak_status|eq('expired')}
                        <li class="ak-badge">{'Expired'|i18n( 'design/standard/apikey' )}</li>
                    {else}
                        <li class="ak-badge ak-badge-bad">{'Revoked'|i18n( 'design/standard/apikey' )}</li>
                    {/if}
                    </ul>
                </div>
                {if $ak_status|eq('active')}
                <button type="submit" class="ak-btn ak-btn-small" name="RevokeKeyButton" value="{$key.id}"
                        aria-label="{'Revoke the key %name'|i18n( 'design/standard/apikey',, hash( '%name', $key.name ) )|wash}">{'Revoke'|i18n( 'design/standard/apikey' )}</button>
                {/if}
            </div>
            <dl class="ak-facts">
                <div><dt>{'Key'|i18n( 'design/standard/apikey' )}</dt><dd><code>{$key.key_prefix|wash}_&hellip;</code></dd></div>
                <div><dt>{'Scopes'|i18n( 'design/standard/apikey' )}</dt><dd>
                    <ul class="ak-badges">{foreach $key.scope_list as $scope}<li class="ak-badge ak-badge-info">{if is_set( $scope_catalogue[$scope] )}{$scope_catalogue[$scope].name|wash}{else}{$scope|wash}{/if}</li>{/foreach}</ul>
                </dd></div>
                <div><dt>{'Made'|i18n( 'design/standard/apikey' )}</dt><dd>{$key.created|l10n( shortdatetime )}</dd></div>
                <div><dt>{'Last used'|i18n( 'design/standard/apikey' )}</dt><dd>{if $key.last_used|gt(0)}{$key.last_used|l10n( shortdatetime )}{if $key.last_ip|ne('')}<br /><span class="ak-muted">{'from %address'|i18n( 'design/standard/apikey',, hash( '%address', $key.last_ip ) )|wash}</span>{/if}{else}<span class="ak-muted">{'Never'|i18n( 'design/standard/apikey' )}</span>{/if}</dd></div>
                <div><dt>{if $ak_status|eq('revoked')}{'Revoked'|i18n( 'design/standard/apikey' )}{else}{'Expires'|i18n( 'design/standard/apikey' )}{/if}</dt><dd>
                    {if $ak_status|eq('revoked')}{$key.revoked|l10n( shortdatetime )}
                    {elseif $key.expires|gt(0)}{$key.expires|l10n( shortdate )}
                    {else}<span class="ak-muted">{'Never'|i18n( 'design/standard/apikey' )}</span>{/if}</dd></div>
            </dl>
        </li>
        {undef $ak_status}
    {/foreach}
    </ul>
    </form>
{/if}
</section>

<section class="ak-card" aria-labelledby="ak-create-title">
    <h2 id="ak-create-title">{'Make a new key'|i18n( 'design/standard/apikey' )}</h2>
{if $can_create|not}
    <p class="ak-lead">{'Your account may not make API keys. If you need one, ask the site administrator to allow it for you.'|i18n( 'design/standard/apikey' )}</p>
{elseif $allowed_scopes|count|eq(0)}
    <p class="ak-lead">{'Your account may make keys, but none of the scopes a key can have is open to you, so there is nothing a key could do. Ask the site administrator.'|i18n( 'design/standard/apikey' )}</p>
{elseif and( $max_keys|gt(0), $active_count|ge( $max_keys ) )}
    <p class="ak-lead">{'You have %count active keys, the most allowed. Revoke one you no longer use to make another.'|i18n( 'design/standard/apikey',, hash( '%count', $max_keys ) )}</p>
{else}
    <p class="ak-lead">{'Give each program its own key, so you can revoke one without stopping the others. Choose only the scopes it needs.'|i18n( 'design/standard/apikey' )}</p>
    <form class="ak-form" method="post" action={'apikey/list'|ezurl}>
        <input type="hidden" name="ApiKeyNonce" value="{$nonce|wash}" />
        <div class="ak-field">
            <label for="ak-name">{'Name'|i18n( 'design/standard/apikey' )}</label>
            <input type="text" id="ak-name" name="ApiKeyName" maxlength="100" required="required" value="{$form.name|wash}" placeholder="{'e.g. Newsroom import script'|i18n( 'design/standard/apikey' )}" aria-describedby="ak-name-hint" autocomplete="off" />
            <span class="ak-hint" id="ak-name-hint">{'Where the key is used, so you recognise it later.'|i18n( 'design/standard/apikey' )}</span>
        </div>
        <fieldset class="ak-field">
            <legend>{'Scopes'|i18n( 'design/standard/apikey' )}</legend>
            <ul class="ak-scopes">
            {foreach $scope_catalogue as $scope_id => $scope}
                {if $allowed_scopes|contains( $scope_id )}
                <li class="ak-scope">
                    <input type="checkbox" id="ak-scope-{$scope_id|wash}" name="ApiKeyScopes[]" value="{$scope_id|wash}"{if $form.scopes|contains( $scope_id )} checked="checked"{/if} />
                    <label for="ak-scope-{$scope_id|wash}"><strong>{$scope.name|wash}</strong> <span class="ak-hint">{'Needs your %policy permission; the key never gets more than you have.'|i18n( 'design/standard/apikey',, hash( '%policy', $scope.policy ) )|wash}</span></label>
                </li>
                {/if}
            {/foreach}
            </ul>
        </fieldset>
        <div class="ak-field">
            <label for="ak-expiry">{'Valid for'|i18n( 'design/standard/apikey' )}</label>
            <select id="ak-expiry" name="ApiKeyExpiry">
            {foreach $expiry_choices as $days}
                <option value="{$days}"{if eq( $form.expiry, $days )} selected="selected"{/if}>{if $days|eq(0)}{'No end date'|i18n( 'design/standard/apikey' )}{elseif $days|eq(1)}{'1 day'|i18n( 'design/standard/apikey' )}{else}{'%count days'|i18n( 'design/standard/apikey',, hash( '%count', $days ) )}{/if}</option>
            {/foreach}
            </select>
            <span class="ak-hint">{'The key stops working on its end date; make a new one before then.'|i18n( 'design/standard/apikey' )}</span>
        </div>
        <div class="ak-actions">
            <button type="submit" class="ak-btn ak-btn-primary" name="CreateKeyButton" value="1">{'Make the key'|i18n( 'design/standard/apikey' )}</button>
        </div>
    </form>
{/if}
</section>

<section class="ak-card" aria-labelledby="ak-use-title">
    <h2 id="ak-use-title">{'Using a key'|i18n( 'design/standard/apikey' )}</h2>
    <p class="ak-lead">{'Send it in the Authorization header of every request, never in the address: addresses end up in logs.'|i18n( 'design/standard/apikey' )}</p>
<pre><code>curl -H "Authorization: Bearer expk_..." \
     {$example_url|wash}</code></pre>
    <p class="ak-lead">{'A key works until its end date or until you revoke it. If a key may have leaked, revoke it here at once and make a new one.'|i18n( 'design/standard/apikey' )}</p>
</section>
{/if}

{include uri='design:apikey/parts/page_end.tpl'}

{literal}
<script>
(function () {
    var buttons = document.querySelectorAll('.ak .ak-copy');
    var status = document.getElementById('ak-copy-status');
    for (var i = 0; i < buttons.length; i++) {
        (function (button) {
            var source = document.querySelector(button.getAttribute('data-copy'));
            if (!source || !navigator.clipboard || !window.isSecureContext) { return; }
            button.hidden = false;
            button.addEventListener('click', function () {
                navigator.clipboard.writeText(source.textContent.trim()).then(function () {
                    if (status) { status.textContent = button.getAttribute('data-done') || button.textContent + ': OK'; }
                }, function () {
                    if (status) { status.textContent = ''; }
                });
            });
        }(buttons[i]));
    }
    var fresh = document.getElementById('ak-new');
    if (fresh && fresh.focus) { fresh.focus(); }
}());
</script>
{/literal}
