{* One REST application: oauthadmin/view/<id>.

   What the application is, its client identifier and secret (the secret folded away until asked for), its
   endpoint, the users who authorized it and the tokens it holds. The buttons of the old page are kept: Edit
   (EditButton to oauthadmin/edit/<id>), Delete (DeleteIDArray[] and DeleteApplicationListButton to
   oauthadmin/action, which asks to confirm) and Back. Styles: parts/style.tpl. *}
{include uri='design:oauthadmin/parts/style.tpl'}
{def $modified = cond( $application.updated|ne(0), $application.updated, $application.created )
     $owner_name = '?'}
{if $application.owner}{set $owner_name = $application.owner.contentobject.name}{/if}

<div class="context-block exp-oauth">

<div class="box-header"><div class="box-ml">
<h1 class="context-title">{'Application <%application_name>'|i18n( 'extension/oauthadmin',, hash( '%application_name', $application.name ) )|wash}</h1>
</div></div>

<div class="box-bc"><div class="box-ml"><div class="box-content">

{include uri='design:oauthadmin/parts/tabs.tpl' current='applications'}

<p class="exp-intro">{'Last modified: %modified by %owner'|i18n( 'extension/oauthadmin',, hash( '%modified', $modified|l10n( shortdatetime ), '%owner', $owner_name ) )|wash}</p>

<section class="exp-section" aria-labelledby="oauth-app-facts-title">
<div class="exp-card">
    <h2 class="exp-h2" id="oauth-app-facts-title">{'How the application signs in'|i18n( 'design/admin/oauthadmin' )}</h2>
    {if $application.description|ne('')}<p class="exp-intro" style="margin: 6px 0 0;">{$application.description|wash}</p>{/if}
    <dl class="exp-facts">
        <div>
            <dt id="oauth-client-id-label">{'Client identifier'|i18n( 'extension/oauthadmin' )}</dt>
            <dd class="exp-secret"><code aria-labelledby="oauth-client-id-label">{$application.client_id|wash}</code></dd>
        </div>
        <div>
            <dt id="oauth-client-secret-label">{'Client secret'|i18n( 'extension/oauthadmin' )}</dt>
            <dd>
                <details class="exp-reveal">
                    <summary>{'Show the secret'|i18n( 'design/admin/oauthadmin' )}</summary>
                    <div class="exp-secret"><code aria-labelledby="oauth-client-secret-label">{$application.client_secret|wash}</code></div>
                </details>
            </dd>
        </div>
        <div>
            <dt>{'Endpoint URI'|i18n( 'extension/oauthadmin' )}</dt>
            <dd>{if $application.endpoint_uri|ne('')}<code>{$application.endpoint_uri|wash}</code>{else}<span class="exp-muted">{'None: the authorization page cannot send anyone back to the application.'|i18n( 'design/admin/oauthadmin' )}</span>{/if}</dd>
        </div>
        <div>
            <dt>{'Created'|i18n( 'design/admin/oauthadmin' )}</dt>
            <dd>{$application.created|l10n( shortdatetime )}</dd>
        </div>
    </dl>
</div>
</section>

<section class="exp-section" aria-labelledby="oauth-app-use-title">
<div class="exp-section-head">
    <h2 class="exp-h2" id="oauth-app-use-title">{'Use'|i18n( 'design/admin/oauthadmin' )}</h2>
    <span class="exp-meta">{'%count tokens still valid'|i18n( 'design/admin/oauthadmin',, hash( '%count', $active_tokens ) )}</span>
</div>
{if $authorizations|count|eq(0)}
<p class="exp-empty">{'No user has authorized this application yet.'|i18n( 'design/admin/oauthadmin' )}</p>
{else}
<div class="exp-table-wrap">
<table class="exp-table">
    <caption class="exp-sr">{'Users who authorized this application'|i18n( 'design/admin/oauthadmin' )}</caption>
    <thead><tr><th scope="col">{'User'|i18n( 'design/admin/oauthadmin' )}</th><th scope="col">{'Authorized'|i18n( 'design/admin/oauthadmin' )}</th></tr></thead>
    <tbody>
    {foreach $authorizations as $authorization}
    <tr>
        <td>{if $authorization.name|ne('')}{$authorization.name|wash}{else}<span class="exp-muted">{'Removed user %id'|i18n( 'design/admin/oauthadmin',, hash( '%id', $authorization.user_id ) )}</span>{/if}</td>
        <td class="exp-num">{if $authorization.created|gt(0)}{$authorization.created|l10n( shortdatetime )}{else}&mdash;{/if}</td>
    </tr>
    {/foreach}
    </tbody>
</table>
</div>
{/if}
</section>

<div class="exp-bar">
    <form method="get" action={$module.functions.list.uri|ezurl}>
        <button class="exp-btn" type="submit" name="Cancel" value="{'Back'|i18n( 'extension/oauthadmin' )}">{'Back'|i18n( 'extension/oauthadmin' )}</button>
    </form>
    <div class="exp-actions">
        <form method="post" action={$module.functions.action.uri|ezurl}>
            <input type="hidden" name="DeleteIDArray[]" value="{$application.id}" />
            <button class="exp-btn" type="submit" name="DeleteApplicationListButton" value="{'Delete'|i18n( 'extension/oauthadmin' )}" title="{'Delete this application.'|i18n( 'extension/oauthadmin' )}">{'Delete'|i18n( 'extension/oauthadmin' )}</button>
        </form>
        <form method="post" action={concat( $module.functions.edit.uri, '/', $application.id )|ezurl}>
            <button class="exp-btn exp-btn-primary" type="submit" name="EditButton" value="{'Edit'|i18n( 'extension/oauthadmin' )}" title="{'Edit this application.'|i18n( 'extension/oauthadmin' )}">{'Edit'|i18n( 'extension/oauthadmin' )}</button>
        </form>
    </div>
</div>

</div></div></div>

</div>
{undef $modified $owner_name}
