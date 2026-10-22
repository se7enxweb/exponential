{* The secrets/list view: the kernel's secret store.
   Variables: secrets (name, source, scope, description, readable, key_id, modified), scope, installed, key (source,
   file, relative_file, exists, id, previous_id, previous_source, supported, problem), messages, form (name, scope,
   description), stale. *}
<div class="a4-page a4-secrets" id="exp-secrets-list">

<div class="a4-pagehead">
    <div class="a4-pagehead-main">
        <h1 class="a4-pagehead-title context-title">{'Secrets'|i18n( 'design/admin/secrets' )}</h1>
        <p class="a4-pagehead-sub">{'Passwords, tokens and API keys of the tools: database connections, import sources, remote servers. A tool keeps the name of a secret; the value is stored here, encrypted, and never shown again.'|i18n( 'design/admin/secrets' )}</p>
    </div>
</div>

{foreach $messages as $m}
<div class="a4-notice is-{if eq( $m.kind, 'ok' )}ok{elseif eq( $m.kind, 'warn' )}warn{else}bad{/if}" role="{if eq( $m.kind, 'bad' )}alert{else}status{/if}"><p>{$m.text|wash}</p></div>
{/foreach}

<section class="a4-card a4-section{if $key.problem} is-bad{/if}" aria-labelledby="secrets-key">
    <div class="a4-card-head"><h2 class="a4-h3 a4-card-title" id="secrets-key">{'The key'|i18n( 'design/admin/secrets' )}</h2></div>
    <div class="a4-card-body a4-stack">
        {if $key.problem}<p class="a4-bad-text">{$key.problem|wash}</p>{/if}
        <dl class="a4-facts is-rows">
            <dt>{'Comes from'|i18n( 'design/admin/secrets' )}</dt>
            <dd>{if eq( $key.source, 'env' )}{'the environment variable EXP_SECRET_KEY'|i18n( 'design/admin/secrets' )}{else}{'the key file %file'|i18n( 'design/admin/secrets',, hash( '%file', $key.relative_file ) )|wash}{if $key.exists|not} <span class="a4-meta">{'(made when the first secret is stored)'|i18n( 'design/admin/secrets' )}</span>{/if}{/if}</dd>
            {if $key.id}<dt>{'Key id'|i18n( 'design/admin/secrets' )}</dt><dd><code class="a4-code">{$key.id|wash}</code></dd>{/if}
            {if $key.previous_id}<dt>{'Previous key'|i18n( 'design/admin/secrets' )}</dt><dd><code class="a4-code">{$key.previous_id|wash}</code> ({if eq( $key.previous_source, 'env' )}EXP_SECRET_KEY_PREVIOUS{else}{'the .previous file'|i18n( 'design/admin/secrets' )}{/if})</dd>{/if}
            <dt>{'Encryption'|i18n( 'design/admin/secrets' )}</dt><dd>XChaCha20-Poly1305 (sodium)</dd>
        </dl>
        <div class="a4-notice is-warn" role="note">
            <p class="a4-notice-title">{'A backup of the database alone cannot decrypt the secrets'|i18n( 'design/admin/secrets' )}</p>
            <p>{'The key is not in the database. Keep a copy of the key file (or of EXP_SECRET_KEY) with every backup, in a safe place of its own. Without it the secrets of a restored database cannot be read and have to be set again.'|i18n( 'design/admin/secrets' )}</p>
        </div>
        {if $stale}<p class="a4-warn-text">{'%count secrets are encrypted with another key than the current one.'|i18n( 'design/admin/secrets',, hash( '%count', $stale ) )|wash}</p>{/if}
    </div>
    {if and( $installed, $key.supported )}
    <form method="post" action={'secrets/list'|ezurl} class="a4-card-foot a4-actions">
        {if eq( $key.source, 'file' )}
        <details class="a4-confirm">
            <summary class="a4-btn">{'Rotate the key'|i18n( 'design/admin/secrets' )}</summary>
            <div class="a4-fold-body is-warn">
                <p>{'A new key is made, every secret is encrypted again with it, and the old key is kept as the .previous file. Back up the new key file afterwards.'|i18n( 'design/admin/secrets' )}</p>
                <label class="a4-check"><input type="checkbox" name="ConfirmRotate" value="1" /> {'Yes, rotate the key'|i18n( 'design/admin/secrets' )}</label>
                <button type="submit" class="a4-btn a4-btn-primary" name="RotateKeyButton" value="1">{'Rotate'|i18n( 'design/admin/secrets' )}</button>
            </div>
        </details>
        {else}
        <p class="a4-meta">{'To rotate a key from the environment: set the new key as EXP_SECRET_KEY and the old one as EXP_SECRET_KEY_PREVIOUS, then encrypt every secret again here.'|i18n( 'design/admin/secrets' )}</p>
        {/if}
        <button type="submit" class="a4-btn" name="ReencryptButton" value="1">{'Encrypt every secret again with the current key'|i18n( 'design/admin/secrets' )}</button>
    </form>
    {/if}
</section>

{if $installed|not}
<div class="a4-notice is-warn" role="status">
    <p class="a4-notice-title">{'The table exp_secret is missing'|i18n( 'design/admin/secrets' )}</p>
    <p>{'Until it is created, only the secrets of secrets.ini and environment variables (env:NAME) can be used.'|i18n( 'design/admin/secrets' )}</p>
    <form method="post" action={'secrets/list'|ezurl}><button type="submit" class="a4-btn a4-btn-primary" name="CreateTableButton" value="1">{'Create the table'|i18n( 'design/admin/secrets' )}</button></form>
</div>
{/if}

<form method="post" action={'secrets/list'|ezurl}>
<section class="a4-card is-flush a4-section" aria-labelledby="secrets-names">
    <div class="a4-card-head"><h2 class="a4-h3 a4-card-title" id="secrets-names">{'Names'|i18n( 'design/admin/secrets' )}</h2>
        {if $scope}<p class="a4-meta">{'Only the tool %scope.'|i18n( 'design/admin/secrets',, hash( '%scope', $scope ) )|wash} <a href={'secrets/list'|ezurl}>{'All'|i18n( 'design/admin/secrets' )}</a></p>{/if}</div>
    {if $secrets|count}
    <div class="a4-table-wrap" role="region" aria-labelledby="secrets-names" tabindex="0">
    <table class="a4-table">
        <thead><tr>
            <th scope="col">{'Name'|i18n( 'design/admin/secrets' )}</th>
            <th scope="col">{'Tool'|i18n( 'design/admin/secrets' )}</th>
            <th scope="col">{'Description'|i18n( 'design/admin/secrets' )}</th>
            <th scope="col">{'State'|i18n( 'design/admin/secrets' )}</th>
            <th scope="col">{'Changed'|i18n( 'design/admin/secrets' )}</th>
            <th scope="col"><span class="a4-sr-only">{'Remove'|i18n( 'design/admin/secrets' )}</span></th>
        </tr></thead>
        <tbody>
        {foreach $secrets as $s}
        <tr>
            <td><code class="a4-code">{$s.name|wash}</code></td>
            <td>{if $s.scope}<a href={concat( 'secrets/list/(scope)/', $s.scope )|ezurl}>{$s.scope|wash}</a>{else}<span class="a4-muted">-</span>{/if}</td>
            <td>{$s.description|wash}</td>
            <td>{if eq( $s.source, 'settings' )}<span class="a4-badge is-info">{'secrets.ini'|i18n( 'design/admin/secrets' )}</span>{elseif $s.readable}<span class="a4-badge is-ok">{'encrypted'|i18n( 'design/admin/secrets' )}</span>{else}<span class="a4-badge is-bad">{'cannot be decrypted here'|i18n( 'design/admin/secrets' )}</span>{/if}</td>
            <td>{if $s.modified}{$s.modified|l10n( 'shortdatetime' )}{else}<span class="a4-muted">-</span>{/if}</td>
            <td>{if eq( $s.source, 'store' )}
                <details class="a4-confirm">
                    <summary class="a4-btn a4-btn-small a4-btn-outline-danger">{'Remove'|i18n( 'design/admin/secrets' )}<span class="a4-sr-only"> {$s.name|wash}</span></summary>
                    <div class="a4-fold-body is-warn">
                        <label class="a4-check"><input type="checkbox" name="ConfirmRemove[{$s.name|wash}]" value="1" /> {'Yes, remove %name'|i18n( 'design/admin/secrets',, hash( '%name', $s.name ) )|wash}</label>
                        <button type="submit" class="a4-btn a4-btn-small a4-btn-danger" name="RemoveButton[{$s.name|wash}]" value="1">{'Remove'|i18n( 'design/admin/secrets' )}</button>
                    </div>
                </details>{/if}</td>
        </tr>
        {/foreach}
        </tbody>
    </table>
    </div>
    {else}
    <div class="a4-card-body"><p class="a4-empty">{'No secret yet.'|i18n( 'design/admin/secrets' )}</p></div>
    {/if}
</section>
</form>

{if and( $installed, $key.supported )}
<form method="post" action={'secrets/list'|ezurl} class="a4-form" autocomplete="off">
<section class="a4-card a4-section" aria-labelledby="secrets-set">
    <div class="a4-card-head"><h2 class="a4-h3 a4-card-title" id="secrets-set">{'Set a secret'|i18n( 'design/admin/secrets' )}</h2>
        <p>{'A new name, or the name of an existing secret to replace its value. Empty value: only the description changes.'|i18n( 'design/admin/secrets' )}</p></div>
    <div class="a4-card-body a4-grid">
        <div class="a4-field">
            <label class="a4-label" for="secret-name">{'Name'|i18n( 'design/admin/secrets' )} <span class="a4-required-mark" aria-hidden="true">*</span></label>
            <input class="a4-input" id="secret-name" name="SecretName" required="required" maxlength="128" pattern="[A-Za-z0-9_.:\-]{ldelim}1,128{rdelim}" value="{$form.name|wash}" />
        </div>
        <div class="a4-field">
            <label class="a4-label" for="secret-scope">{'Tool'|i18n( 'design/admin/secrets' )}</label>
            <input class="a4-input" id="secret-scope" name="SecretScope" maxlength="64" value="{$form.scope|wash}" aria-describedby="secret-scope-help" />
            <p class="a4-help" id="secret-scope-help">{'Who uses it, e.g. datatable, sqliimport, ezodf.'|i18n( 'design/admin/secrets' )}</p>
        </div>
        <div class="a4-field">
            <label class="a4-label" for="secret-value">{'Value'|i18n( 'design/admin/secrets' )}</label>
            <input class="a4-input" id="secret-value" name="SecretValue" type="password" autocomplete="new-password" value="" />
        </div>
        <div class="a4-field a4-field-wide">
            <label class="a4-label" for="secret-description">{'Description'|i18n( 'design/admin/secrets' )}</label>
            <input class="a4-input" id="secret-description" name="SecretDescription" maxlength="255" value="{$form.description|wash}" />
        </div>
    </div>
    <div class="a4-card-foot a4-actions"><button type="submit" class="a4-btn a4-btn-primary" name="SaveSecretButton" value="1">{'Store'|i18n( 'design/admin/secrets' )}</button></div>
</section>
</form>
{/if}

</div>
