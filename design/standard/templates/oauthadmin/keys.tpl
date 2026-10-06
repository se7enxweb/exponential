{* The personal API keys of every user: oauthadmin/keys, and one user's keys at oauthadmin/keys/(user)/<id>.

   Figures by status (each a filter), the search and status filter (a GET form; the view turns it into its own
   parameters), then one row per key: name and prefix, owner, scopes, created and expiry, last use and address,
   status. Revoking (one row, or the checked rows) goes to oauthadmin/keyaction, which asks to confirm. The secret
   of a key is never here: only its owner saw it, once. Works without javascript. Styles: parts/style.tpl. *}
{include uri='design:oauthadmin/parts/style.tpl'}

{def $oa_base = 'oauthadmin/keys'
     $oa_user_part = ''}
{if $filter_user_id|gt(0)}{set $oa_user_part = concat( '/(user)/', $filter_user_id )}{/if}

<div class="context-block exp-oauth">

<div class="box-header"><div class="box-ml">
<h1 class="context-title">{if $filter_user_id|gt(0)}{'API keys of %name'|i18n( 'design/admin/oauthadmin',, hash( '%name', $filter_user_name ) )|wash}{else}{'API keys'|i18n( 'design/admin/oauthadmin' )}{/if}</h1>
</div></div>

<div class="box-bc"><div class="box-ml"><div class="box-content">

<p class="exp-intro">{'Users make these keys themselves on the API access page of their account. A key acts as its owner, within the scopes it was given, so it can never do more than the owner may. Revoke a key that is no longer needed, leaked, or belongs to someone who should not publish any more.'|i18n( 'design/admin/oauthadmin' )}</p>

{include uri='design:oauthadmin/parts/tabs.tpl' current='keys' key_count=$key_counts.total}

{if $revoked_count|gt(0)}
<div class="exp-feedback is-ok" role="status">{'%count keys revoked. Requests with them are refused from now on.'|i18n( 'design/admin/oauthadmin',, hash( '%count', $revoked_count ) )}</div>
{/if}
{if $keys_enabled|not}
<div class="exp-feedback is-warn" role="status">{'API keys are switched off (rest.ini [ApiKeySettings] ApiKeys=disabled): no key is accepted and none can be made.'|i18n( 'design/admin/oauthadmin' )}</div>
{/if}
{if and( $rate_limit|gt(0), $rate_limit_available|not )}
<div class="exp-feedback is-warn" role="status">{'The rate limit of %limit requests per minute is not enforced here: it is counted in APCu, which this PHP does not have.'|i18n( 'design/admin/oauthadmin',, hash( '%limit', $rate_limit ) )}</div>
{/if}

{if $filter_user_id|gt(0)}
<div class="exp-bar" style="margin: 0 0 16px;">
    <span>{if $filter_user}{'Only the keys of %name (%login) are shown.'|i18n( 'design/admin/oauthadmin',, hash( '%name', $filter_user_name, '%login', $filter_user.login ) )|wash}{else}{'The user %id no longer exists; these are the keys that were theirs.'|i18n( 'design/admin/oauthadmin',, hash( '%id', $filter_user_id ) )}{/if}</span>
    <div class="exp-actions">
        {if $filter_user}<a class="exp-btn exp-btn-small" href={concat( 'user/setting/', $filter_user_id )|ezurl}>{'User settings'|i18n( 'design/admin/oauthadmin' )}</a>{/if}
        <a class="exp-btn exp-btn-small" href={concat( $oa_base, cond( $status|ne(''), concat( '/(status)/', $status ), '' ) )|ezurl}>{'Show every user'|i18n( 'design/admin/oauthadmin' )}</a>
    </div>
</div>
{/if}

<section aria-labelledby="oauth-key-overview-title">
<h2 class="exp-sr" id="oauth-key-overview-title">{'Overview'|i18n( 'design/admin/oauthadmin' )}</h2>
<ul class="exp-figures">
    <li><a class="exp-figure is-good" href={concat( $oa_base, '/(status)/active', $oa_user_part )|ezurl}{if $status|eq('active')} aria-current="true"{/if}><strong>{$key_counts.active}</strong><span>{'Active'|i18n( 'design/admin/oauthadmin' )}</span></a></li>
    <li class="exp-figure{if $key_counts.expiring_soon|gt(0)} is-attention{/if}"><strong>{$key_counts.expiring_soon}</strong><span>{'Expiring within 7 days'|i18n( 'design/admin/oauthadmin' )}</span></li>
    <li class="exp-figure"><strong>{$key_counts.never_used}</strong><span>{'Active, never used'|i18n( 'design/admin/oauthadmin' )}</span></li>
    <li><a class="exp-figure" href={concat( $oa_base, '/(status)/expired', $oa_user_part )|ezurl}{if $status|eq('expired')} aria-current="true"{/if}><strong>{$key_counts.expired}</strong><span>{'Expired'|i18n( 'design/admin/oauthadmin' )}</span></a></li>
    <li><a class="exp-figure" href={concat( $oa_base, '/(status)/revoked', $oa_user_part )|ezurl}{if $status|eq('revoked')} aria-current="true"{/if}><strong>{$key_counts.revoked}</strong><span>{'Revoked'|i18n( 'design/admin/oauthadmin' )}</span></a></li>
</ul>
</section>

<section aria-labelledby="oauth-key-filter-title">
<h2 class="exp-sr" id="oauth-key-filter-title">{'Find keys'|i18n( 'design/admin/oauthadmin' )}</h2>
<form class="exp-toolbar" method="get" action={$oa_base|ezurl} role="search">
    <div class="exp-field">
        <label for="oauth-key-search">{'Name, prefix, login or e-mail'|i18n( 'design/admin/oauthadmin' )}</label>
        <input type="search" id="oauth-key-search" name="q" value="{$search|wash}" maxlength="100" autocomplete="off" spellcheck="false" />
    </div>
    <div class="exp-field">
        <label for="oauth-key-status">{'Status'|i18n( 'design/admin/oauthadmin' )}</label>
        <select id="oauth-key-status" name="Status">
            <option value=""{if $status|eq('')} selected="selected"{/if}>{'All'|i18n( 'design/admin/oauthadmin' )}</option>
            <option value="active"{if $status|eq('active')} selected="selected"{/if}>{'Active'|i18n( 'design/admin/oauthadmin' )}</option>
            <option value="expired"{if $status|eq('expired')} selected="selected"{/if}>{'Expired'|i18n( 'design/admin/oauthadmin' )}</option>
            <option value="revoked"{if $status|eq('revoked')} selected="selected"{/if}>{'Revoked'|i18n( 'design/admin/oauthadmin' )}</option>
        </select>
    </div>
    {if $filter_user_id|gt(0)}<input type="hidden" name="UserID" value="{$filter_user_id}" />{/if}
    <div class="exp-actions">
        <button type="submit" class="exp-btn exp-btn-primary">{'Show'|i18n( 'design/admin/oauthadmin' )}</button>
        {if or( $search|ne(''), $status|ne('') )}<a class="exp-btn" href={concat( $oa_base, $oa_user_part )|ezurl}>{'Clear'|i18n( 'design/admin/oauthadmin' )}</a>{/if}
    </div>
</form>
</section>

<form method="post" action={'oauthadmin/keyaction'|ezurl}>
<input type="hidden" name="RedirectURI" value="{$redirect_uri|wash}" />
<section class="exp-section" aria-labelledby="oauth-keys-title">
<div class="exp-section-head">
    <h2 class="exp-h2" id="oauth-keys-title">{'Keys'|i18n( 'design/admin/oauthadmin' )}</h2>
    <span class="exp-meta" role="status">{if $key_count|gt(0)}{'%from to %to of %count'|i18n( 'design/admin/oauthadmin',, hash( '%from', sum( $view_parameters.offset, 1 ), '%to', min( sum( $view_parameters.offset, $limit ), $key_count ), '%count', $key_count ) )}{/if}{if $search|ne('')} &middot; {'matching "%search"'|i18n( 'design/admin/oauthadmin',, hash( '%search', $search ) )|wash}{/if}</span>
</div>

{if $keys|count|eq(0)}
<p class="exp-empty">{if or( $search|ne(''), $status|ne('') )}{'No key matches this filter.'|i18n( 'design/admin/oauthadmin' )}{else}{'No API key has been made yet. Users with the apikey/create policy make them on the API access page of their account.'|i18n( 'design/admin/oauthadmin' )}{/if}</p>
{else}
<ul class="exp-keys">
    <li class="exp-key is-head" aria-hidden="true">
        <span></span>
        <span>{'Key'|i18n( 'design/admin/oauthadmin' )}</span>
        <span>{'Owner'|i18n( 'design/admin/oauthadmin' )}</span>
        <span>{'Scopes'|i18n( 'design/admin/oauthadmin' )}</span>
        <span>{'Last used'|i18n( 'design/admin/oauthadmin' )}</span>
        <span>{'Expires'|i18n( 'design/admin/oauthadmin' )}</span>
        <span></span>
    </li>
{foreach $keys as $key}
    {def $key_status = $key.status
         $key_dom = concat( 'oauth-key-', $key.id )}
    <li class="exp-key is-{$key_status}" id="{$key_dom}">
        <span>{if $key_status|eq('revoked')}{else}<input type="checkbox" name="RevokeKeyIDArray[]" value="{$key.id}" id="{$key_dom}-select" aria-label="{'Select the key %name for revoking'|i18n( 'design/admin/oauthadmin',, hash( '%name', $key.name ) )|wash}" />{/if}</span>
        <div class="exp-key-main exp-key-cell">
            <span class="exp-key-name">{$key.name|wash}</span>
            <ul class="exp-badges">
                {if $key_status|eq('active')}
                    <li class="exp-badge is-ok">{'Active'|i18n( 'design/admin/oauthadmin' )}</li>
                    {if $key.expiring_soon}<li class="exp-badge is-warn">{'Expires soon'|i18n( 'design/admin/oauthadmin' )}</li>{/if}
                {elseif $key_status|eq('expired')}
                    <li class="exp-badge">{'Expired'|i18n( 'design/admin/oauthadmin' )}</li>
                {else}
                    <li class="exp-badge is-bad">{'Revoked'|i18n( 'design/admin/oauthadmin' )}</li>
                {/if}
            </ul>
            <br /><code>{$key.key_prefix|wash}_&hellip;</code>
            <br /><span class="exp-meta">{'Made %date'|i18n( 'design/admin/oauthadmin',, hash( '%date', $key.created|l10n( shortdatetime ) ) )}{if $key_status|eq('revoked')} &middot; {'revoked %date'|i18n( 'design/admin/oauthadmin',, hash( '%date', $key.revoked|l10n( shortdatetime ) ) )}{if $key.revoked_by|ne( $key.user_id )}{if $key.revoker_name|ne('')} {'by %name'|i18n( 'design/admin/oauthadmin',, hash( '%name', $key.revoker_name ) )|wash}{/if}{/if}{/if}</span>
        </div>
        <div class="exp-key-cell">
            <span class="exp-cell-label">{'Owner'|i18n( 'design/admin/oauthadmin' )}</span>
            {if $key.owner_name|ne('')}<a href={concat( 'oauthadmin/keys/(user)/', $key.user_id )|ezurl} title="{'Every key of %name'|i18n( 'design/admin/oauthadmin',, hash( '%name', $key.owner_name ) )|wash}">{$key.owner_name|wash}</a>
            {if $key.owner_login|ne('')}<br /><span class="exp-meta">{$key.owner_login|wash}</span>{/if}{else}<span class="exp-muted">{'Removed user %id'|i18n( 'design/admin/oauthadmin',, hash( '%id', $key.user_id ) )}</span>{/if}
        </div>
        <div class="exp-key-cell">
            <span class="exp-cell-label">{'Scopes'|i18n( 'design/admin/oauthadmin' )}</span>
            <ul class="exp-badges">
            {foreach $key.scope_list as $scope}
                <li class="exp-badge is-info" title="{if is_set( $scope_catalogue[$scope] )}{$scope_catalogue[$scope].name|wash} ({$scope_catalogue[$scope].policy|wash}){/if}">{$scope|wash}</li>
            {/foreach}
            </ul>
        </div>
        <div class="exp-key-cell">
            <span class="exp-cell-label">{'Last used'|i18n( 'design/admin/oauthadmin' )}</span>
            {if $key.last_used|gt(0)}{$key.last_used|l10n( shortdatetime )}{if $key.last_ip|ne('')}<br /><code>{$key.last_ip|wash}</code>{/if}{else}<span class="exp-muted">{'Never'|i18n( 'design/admin/oauthadmin' )}</span>{/if}
        </div>
        <div class="exp-key-cell">
            <span class="exp-cell-label">{'Expires'|i18n( 'design/admin/oauthadmin' )}</span>
            {if $key.expires|gt(0)}{$key.expires|l10n( shortdate )}{else}<span class="exp-muted">{'Never'|i18n( 'design/admin/oauthadmin' )}</span>{/if}
        </div>
        <div class="exp-key-actions">
            {if $key_status|ne('revoked')}
            <button type="submit" class="exp-btn exp-btn-small exp-btn-outline" name="RevokeOneKeyButton" value="{$key.id}"
                    aria-label="{'Revoke the key %name of %owner'|i18n( 'design/admin/oauthadmin',, hash( '%name', $key.name, '%owner', $key.owner_name ) )|wash}">{'Revoke'|i18n( 'design/admin/oauthadmin' )}</button>
            {/if}
        </div>
    </li>
    {undef $key_status $key_dom}
{/foreach}
</ul>
{/if}

{if $key_count|gt( $limit )}
<div class="context-toolbar exp-pager">
{include name=OAuthKeyNavigator
         uri='design:navigator/google.tpl'
         page_uri='/oauthadmin/keys'
         page_uri_suffix=$page_uri_suffix
         item_count=$key_count
         view_parameters=$view_parameters
         item_limit=$limit}
</div>
{/if}

<div class="exp-bar">
    <span class="exp-meta">{'Revoking cannot be undone: the owner makes a new key if one is still needed.'|i18n( 'design/admin/oauthadmin' )}</span>
    <div class="exp-actions">
        <button type="submit" class="exp-btn exp-btn-danger" name="RevokeKeyListButton" value="1"{if $keys|count|eq(0)} disabled="disabled"{/if}>{'Revoke selected'|i18n( 'design/admin/oauthadmin' )}</button>
    </div>
</div>
</section>
</form>

</div></div></div>

</div>
{undef $oa_base $oa_user_part}
