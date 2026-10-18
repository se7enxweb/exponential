{* One REST application: oauthadmin/view/<id>.

   What the application is, its client identifier and secret (the secret folded away until asked for), its
   endpoint, the users who authorized it and the tokens it holds. The buttons of the old page are kept: Edit
   (EditButton to oauthadmin/edit/<id>), Delete (DeleteIDArray[] and DeleteApplicationListButton to
   oauthadmin/action, which asks to confirm) and Back.

   The same file is in design/admin and design/admin4. It uses the shared admin components (components.css) and
   stylesheets/modules/setup.css (.a4-setup-oauth), in light and dark. *}
{def $modified = cond( $application.updated|ne(0), $application.updated, $application.created )
     $owner_name = '?'}
{if $application.owner}{set $owner_name = $application.owner.contentobject.name}{/if}

<div class="context-block a4-page a4-setup-oauth">

<div class="box-header"><div class="box-ml">
<h1 class="context-title">{'Application <%application_name>'|i18n( 'extension/oauthadmin',, hash( '%application_name', $application.name ) )|wash}</h1>
</div></div>

<div class="box-bc"><div class="box-ml"><div class="box-content">

{include uri='design:oauthadmin/parts/tabs.tpl' current='applications' subpage=true()}

<p class="a4-intro">{'Last modified: %modified by %owner'|i18n( 'extension/oauthadmin',, hash( '%modified', $modified|l10n( shortdatetime ), '%owner', $owner_name ) )|wash}</p>

<section class="a4-section" aria-labelledby="oauth-app-facts-title">
<div class="a4-card">
    <div class="a4-card-head">
        <h2 class="a4-h2" id="oauth-app-facts-title">{'How the application signs in'|i18n( 'design/admin/oauthadmin' )}</h2>
        {if $application.description|ne('')}<p>{$application.description|wash}</p>{/if}
    </div>
    <dl class="a4-facts">
        <div>
            <dt id="oauth-client-id-label">{'Client identifier'|i18n( 'extension/oauthadmin' )}</dt>
            <dd><code class="a4-setup-oauth-value" aria-labelledby="oauth-client-id-label">{$application.client_id|wash}</code></dd>
        </div>
        <div>
            <dt id="oauth-client-secret-label">{'Client secret'|i18n( 'extension/oauthadmin' )}</dt>
            <dd>
                <details class="a4-fold a4-setup-oauth-reveal">
                    <summary>{'Show the secret'|i18n( 'design/admin/oauthadmin' )}</summary>
                    <div class="a4-fold-body"><code class="a4-setup-oauth-value" aria-labelledby="oauth-client-secret-label">{$application.client_secret|wash}</code></div>
                </details>
            </dd>
        </div>
        <div>
            <dt>{'Endpoint URI'|i18n( 'extension/oauthadmin' )}</dt>
            <dd>{if $application.endpoint_uri|ne('')}<code>{$application.endpoint_uri|wash}</code>{else}<span class="a4-muted">{'None: the authorization page cannot send anyone back to the application.'|i18n( 'design/admin/oauthadmin' )}</span>{/if}</dd>
        </div>
        <div>
            <dt>{'Created'|i18n( 'design/admin/oauthadmin' )}</dt>
            <dd>{$application.created|l10n( shortdatetime )}</dd>
        </div>
    </dl>
</div>
</section>

<section class="a4-section" aria-labelledby="oauth-app-use-title">
<div class="a4-section-head">
    <h2 class="a4-h2" id="oauth-app-use-title">{'Use'|i18n( 'design/admin/oauthadmin' )}</h2>
    <span class="a4-meta">{'%count tokens still valid'|i18n( 'design/admin/oauthadmin',, hash( '%count', $active_tokens ) )}</span>
</div>
{if $authorizations|count|eq(0)}
<p class="a4-empty">{'No user has authorized this application yet.'|i18n( 'design/admin/oauthadmin' )}</p>
{else}
<div class="a4-table-wrap">
<table class="a4-table">
    <caption class="a4-sr-only">{'Users who authorized this application'|i18n( 'design/admin/oauthadmin' )}</caption>
    <thead><tr><th scope="col">{'User'|i18n( 'design/admin/oauthadmin' )}</th><th scope="col" class="a4-num">{'Authorized'|i18n( 'design/admin/oauthadmin' )}</th></tr></thead>
    <tbody>
    {foreach $authorizations as $authorization}
    <tr>
        <td>{if $authorization.name|ne('')}{$authorization.name|wash}{else}<span class="a4-muted">{'Removed user %id'|i18n( 'design/admin/oauthadmin',, hash( '%id', $authorization.user_id ) )}</span>{/if}</td>
        <td class="a4-num">{if $authorization.created|gt(0)}{$authorization.created|l10n( shortdatetime )}{else}&mdash;{/if}</td>
    </tr>
    {/foreach}
    </tbody>
</table>
</div>
{/if}
</section>

<div class="a4-toolbar">
    <form method="get" action={$module.functions.list.uri|ezurl}>
        <button class="a4-btn" type="submit" name="Cancel" value="{'Back'|i18n( 'extension/oauthadmin' )}">{'Back'|i18n( 'extension/oauthadmin' )}</button>
    </form>
    <div class="a4-actions">
        <form method="post" action={$module.functions.action.uri|ezurl}>
            <input type="hidden" name="DeleteIDArray[]" value="{$application.id}" />
            <button class="a4-btn" type="submit" name="DeleteApplicationListButton" value="{'Delete'|i18n( 'extension/oauthadmin' )}" title="{'Delete this application.'|i18n( 'extension/oauthadmin' )}"><span class="a4-icon a4-bi-trash" aria-hidden="true"></span>{'Delete'|i18n( 'extension/oauthadmin' )}</button>
        </form>
        <form method="post" action={concat( $module.functions.edit.uri, '/', $application.id )|ezurl}>
            <button class="a4-btn a4-btn-primary" type="submit" name="EditButton" value="{'Edit'|i18n( 'extension/oauthadmin' )}" title="{'Edit this application.'|i18n( 'extension/oauthadmin' )}"><span class="a4-icon a4-bi-pencil" aria-hidden="true"></span>{'Edit'|i18n( 'extension/oauthadmin' )}</button>
        </form>
    </div>
</div>

</div></div></div>

</div>
{undef $modified $owner_name}
