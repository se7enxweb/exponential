{* The staticpublish/keys view: the deploy key of a target. Variables: target (expStaticAdmin::overview()), messages, key
   (expStaticAdmin::deployKey(): secret, exists, public_key, fingerprint, store), generated (a key was made by this
   request), uses_key (the target's Credential names the key), https_remote (the git address is an https one), view. *}
<div class="a4-page a4-setup-publish" id="exp-staticpublish-keys">

<div class="a4-pagehead">
    <div class="a4-pagehead-main">
        <h1 class="a4-pagehead-title context-title">{'Deploy key of %name'|i18n( 'design/admin/staticpublish',, hash( '%name', $target.name ) )|wash}</h1>
        <p class="a4-pagehead-sub">{'A deploy key lets this installation push to one repository over SSH and nowhere else. Its private half stays in Setup > Secrets; only the public half is shown.'|i18n( 'design/admin/staticpublish' )}</p>
    </div>
    <div class="a4-pagehead-actions"><a class="a4-btn" href={'exphelp/viewer/guides/static-publishing-deploy-keys.md'|ezurl}>{'Help'|i18n( 'design/admin/staticpublish' )}</a></div>
</div>

{include uri='design:staticpublish/parts/tabs.tpl' target=$target view=$view}

{foreach $messages as $m}<div class="a4-notice is-{if eq( $m.kind, 'ok' )}ok{else}bad{/if}" role="{if eq( $m.kind, 'ok' )}status{else}alert{/if}"><p>{$m.text|wash}</p></div>{/foreach}
{if $key.store|not}<div class="a4-notice is-warn" role="status"><p>{'The secret store is not set up yet (./console exp:upgrade --apply makes its table), so no key can be kept.'|i18n( 'design/admin/staticpublish' )}</p></div>{/if}

<section class="a4-card a4-section" aria-labelledby="sp-key">
    <div class="a4-card-head"><h2 class="a4-h3 a4-card-title" id="sp-key">{'The key'|i18n( 'design/admin/staticpublish' )}</h2>
        <p class="a4-meta">{'Kept as the secret %secret.'|i18n( 'design/admin/staticpublish',, hash( '%secret', $key.secret ) )|wash}</p></div>
    <div class="a4-card-body a4-stack">
        {if $key.exists}
        <p>{if $generated}{'Add this public key to the repository with write access. GitHub: Settings > Deploy keys > Add deploy key, tick Allow write access.'|i18n( 'design/admin/staticpublish' )}{else}{'The public half of the key:'|i18n( 'design/admin/staticpublish' )}{/if}</p>
        <pre class="a4-pre" tabindex="0" aria-label="{'Public key'|i18n( 'design/admin/staticpublish' )|wash}">{$key.public_key|wash}</pre>
        <dl class="a4-facts is-rows">
            <dt>{'Fingerprint'|i18n( 'design/admin/staticpublish' )}</dt><dd><code class="a4-code">{$key.fingerprint|wash}</code></dd>
            <dt>{'Setting'|i18n( 'design/admin/staticpublish' )}</dt><dd><code class="a4-code">Credential=sshkey:{$key.secret|wash}</code></dd>
        </dl>
        {else}
        <p class="a4-muted">{'This target has no deploy key yet.'|i18n( 'design/admin/staticpublish' )}</p>
        {/if}
        {if and( $key.exists, $uses_key|not )}<p class="a4-warn-text">{'The target does not sign in with this key yet: choose "A deploy key made for this target" in its settings.'|i18n( 'design/admin/staticpublish' )}</p>{/if}
        {if and( $uses_key, $https_remote )}<p class="a4-warn-text">{'A deploy key signs in over SSH: change the git address to the SSH one (git@github.com:owner/repo.git).'|i18n( 'design/admin/staticpublish' )}</p>{/if}
    </div>
    <form method="post" action={concat( 'staticpublish/keys/', $target.id )|ezurl} class="a4-card-foot a4-actions">
        {if $key.exists}<label class="a4-check"><input type="checkbox" name="ReplaceKey" value="1" /> <span>{'Replace the key: the old one stops working'|i18n( 'design/admin/staticpublish' )}</span></label>{/if}
        <button type="submit" class="a4-btn{if $key.exists|not} a4-btn-primary{/if}" name="GenerateButton" value="1"{if $key.store|not} disabled="disabled"{/if}>{if $key.exists}{'Make a new key'|i18n( 'design/admin/staticpublish' )}{else}{'Make the key'|i18n( 'design/admin/staticpublish' )}{/if}</button>
    </form>
</section>

</div>
