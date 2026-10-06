{* The REST applications (OAuth clients): oauthadmin/list.

   An overview (applications, the users who authorized them, the tokens still valid, the personal API keys), then
   one card per application with its client identifier, owner, last change, endpoint and use, and the controls of
   the old page: the selection to remove (DeleteIDArray[], DeleteApplicationListButton) and New application
   (NewApplicationButton), both posted to oauthadmin/action. Works without javascript; the script only adds
   "select all". Styles: parts/style.tpl. *}
{include uri='design:oauthadmin/parts/style.tpl'}

<form name="ApplicationList" method="post" action={'oauthadmin/action'|ezurl}>

<div class="context-block exp-oauth">

<div class="box-header"><div class="box-ml">
<h1 class="context-title">{'REST applications (%applications_count)'|i18n( 'extension/oauthadmin',, hash( '%applications_count', $application_count ) )|wash}</h1>
</div></div>

<div class="box-bc"><div class="box-ml"><div class="box-content">

<p class="exp-intro">{'Applications sign users in with OAuth and act for them through the REST interface. Personal API keys are the other way in: a user makes one on the site for a script or an integration of their own.'|i18n( 'design/admin/oauthadmin' )}</p>

{def $oa_key_total = false()}
{if $key_counts}{set $oa_key_total = $key_counts.total}{/if}
{include uri='design:oauthadmin/parts/tabs.tpl' current='applications' application_count=$application_count key_count=$oa_key_total}
{undef $oa_key_total}

<section aria-labelledby="oauth-overview-title">
<h2 class="exp-sr" id="oauth-overview-title">{'Overview'|i18n( 'design/admin/oauthadmin' )}</h2>
<ul class="exp-figures">
    <li class="exp-figure"><strong>{$application_count}</strong><span>{'Applications'|i18n( 'design/admin/oauthadmin' )}</span></li>
    <li class="exp-figure"><strong>{$authorized_total}</strong><span>{'Authorizations by users'|i18n( 'design/admin/oauthadmin' )}</span></li>
    <li class="exp-figure"><strong>{$tokens_total}</strong><span>{'Tokens still valid'|i18n( 'design/admin/oauthadmin' )}</span></li>
{if $key_counts}
    <li><a class="exp-figure is-good" href={'oauthadmin/keys/(status)/active'|ezurl}><strong>{$key_counts.active}</strong><span>{'Active API keys'|i18n( 'design/admin/oauthadmin' )}</span></a></li>
    <li><a class="exp-figure{if $key_counts.expiring_soon|gt(0)} is-attention{/if}" href={'oauthadmin/keys/(status)/active'|ezurl}><strong>{$key_counts.expiring_soon}</strong><span>{'API keys expiring within 7 days'|i18n( 'design/admin/oauthadmin' )}</span></a></li>
{/if}
</ul>
</section>

<section class="exp-section" aria-labelledby="oauth-apps-title">
<div class="exp-section-head">
    <h2 class="exp-h2" id="oauth-apps-title">{'Applications'|i18n( 'design/admin/oauthadmin' )}</h2>
    {if $application_count|gt( $limit )}
    <span class="exp-meta">{'%from to %to of %count'|i18n( 'design/admin/oauthadmin',, hash( '%from', sum( $view_parameters.offset, 1 ), '%to', min( sum( $view_parameters.offset, $limit ), $application_count ), '%count', $application_count ) )}</span>
    {/if}
    <label class="exp-meta exp-js-only" hidden><input type="checkbox" id="oauth-select-all" /> {'Select all on this page'|i18n( 'design/admin/oauthadmin' )}</label>
</div>

{if $application_rows|count|eq(0)}
<p class="exp-empty">{'No REST application is registered yet. An application is what a mobile app, a partner site or a service uses to sign users in and call the REST interface for them: create one with New application.'|i18n( 'design/admin/oauthadmin' )}</p>
{else}
<ul class="exp-cards">
{foreach $application_rows as $row}
    {def $application = $row.application
         $modified = cond( $application.updated|ne(0), $application.updated, $application.created )
         $app_id = concat( 'oauth-app-', $application.id )}
<li class="exp-card" id="{$app_id}">
    <div class="exp-card-head">
        <div class="exp-card-title">
            <input type="checkbox" name="DeleteIDArray[]" value="{$application.id}" id="{$app_id}-select"
                   aria-label="{'Select %name for removal'|i18n( 'design/admin/oauthadmin',, hash( '%name', $application.name ) )|wash}" />
            <h3 id="{$app_id}-title"><a href={concat( $module.functions.view.uri, '/', $application.id )|ezurl}>{$application.name|wash}</a></h3>
            <ul class="exp-badges">
                <li class="exp-badge{if $row.authorized|gt(0)} is-info{/if}">{'Authorized by: %count'|i18n( 'design/admin/oauthadmin',, hash( '%count', $row.authorized ) )}</li>
                <li class="exp-badge{if $row.tokens|gt(0)} is-ok{/if}">{'Valid tokens: %count'|i18n( 'design/admin/oauthadmin',, hash( '%count', $row.tokens ) )}</li>
            </ul>
        </div>
        <div class="exp-actions">
            <a class="exp-btn exp-btn-small" href={concat( $module.functions.view.uri, '/', $application.id )|ezurl}
               aria-label="{'View the application %name'|i18n( 'design/admin/oauthadmin',, hash( '%name', $application.name ) )|wash}">{'View'|i18n( 'design/admin/oauthadmin' )}</a>
            <a class="exp-btn exp-btn-small exp-btn-outline" href={concat( $module.functions.edit.uri, '/', $application.id )|ezurl}
               aria-label="{'Edit the <%application_name> application.'|i18n( 'extension/oauthadmin',, hash( '%application_name', $application.name ) )|wash}">{'Edit'|i18n( 'extension/oauthadmin' )}</a>
        </div>
    </div>
    <dl class="exp-facts">
        <div><dt>{'Client identifier'|i18n( 'extension/oauthadmin' )}</dt><dd><code>{$application.client_id|wash}</code></dd></div>
        <div><dt>{'Owner'|i18n( 'design/admin/oauthadmin' )}</dt><dd>{if $application.owner}{$application.owner.contentobject.name|wash}{else}<span class="exp-muted">{'Removed user'|i18n( 'design/admin/oauthadmin' )}</span>{/if}</dd></div>
        <div><dt>{'Modified'|i18n( 'extension/oauthadmin' )}</dt><dd>{$modified|l10n( shortdatetime )}</dd></div>
        <div><dt>{'Endpoint URI'|i18n( 'extension/oauthadmin' )}</dt><dd>{if $application.endpoint_uri|ne('')}<code>{$application.endpoint_uri|wash}</code>{else}<span class="exp-muted">{'None'|i18n( 'design/admin/oauthadmin' )}</span>{/if}</dd></div>
    </dl>
</li>
    {undef $application $modified $app_id}
{/foreach}
</ul>
{/if}

{if $application_count|gt( $limit )}
<div class="context-toolbar exp-pager">
{include name=OAuthNavigator
         uri='design:navigator/google.tpl'
         page_uri='/oauthadmin/list'
         item_count=$application_count
         view_parameters=$view_parameters
         item_limit=$limit}
</div>
{/if}

<div class="exp-bar">
    <span class="exp-meta">{'Removing an application also ends the authorizations and tokens it was given.'|i18n( 'design/admin/oauthadmin' )}</span>
    <div class="exp-actions">
        <button type="submit" class="exp-btn" name="DeleteApplicationListButton" value="{'Remove selected'|i18n( 'extension/oauthadmin' )}"{if $application_rows|count|eq(0)} disabled="disabled"{/if}
                title="{'Remove the selected applications.'|i18n( 'extension/oauthadmin' )}">{'Remove selected'|i18n( 'extension/oauthadmin' )}</button>
        <button type="submit" class="exp-btn exp-btn-primary" name="NewApplicationButton" value="{'New application'|i18n( 'extension/oauthadmin' )}"
                title="{'Create a new application.'|i18n( 'extension/oauthadmin' )}"><svg width="14" height="14" viewBox="0 0 16 16" aria-hidden="true" focusable="false"><path fill="currentColor" d="M7 2h2v5h5v2H9v5H7V9H2V7h5z"/></svg>{'New application'|i18n( 'extension/oauthadmin' )}</button>
    </div>
</div>
</section>

</div></div></div>

</div>

</form>

{literal}
<script>
(function () {
    var all = document.getElementById('oauth-select-all');
    if (!all) { return; }
    all.parentNode.hidden = false;
    all.addEventListener('change', function () {
        var boxes = document.querySelectorAll('.exp-oauth input[name="DeleteIDArray[]"]');
        for (var i = 0; i < boxes.length; i++) { boxes[i].checked = all.checked; }
    });
}());
</script>
{/literal}
