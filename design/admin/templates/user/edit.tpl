{* The account page (user/edit for the signed in user's own account, user/edit/<id> for another user's).

   An overview of the account (name, login, e-mail, groups, roles, last sign-in, when the password was last changed
   and the account's state), the security hints that apply (disabled or locked, failed sign-ins, two-step sign-in
   off, a password stored with an old method or not changed for a year, never signed in), then a card for each page
   the viewer may go on to: the profile, the password, the account settings, two-step sign-in, API keys, bookmarks,
   notifications and e-mail preferences. The overview is expUserAccountOverview's ($account_overview).

   Every name the view reads is kept (EditButton, ChangePasswordButton, ChangeSettingButton, CancelButton,
   ContentObjectLanguageCode, RedirectIfDiscarded) and every template variable is still set. Cancel goes back to
   the page the editor came from (doc/features/6.0/safe-redirects.md). This is an edit view, which admin4 draws
   without its main card; .exp-standalone gives the page its own. The same file is in design/admin and
   design/admin4. Guide: doc/guides/security-and-audit.md, section 6 *}
{include uri='design:user/exp_style.tpl'}

{def $ov = first_set( $account_overview, false() )
     $acc = cond( $ov, $ov.account, hash( 'user_id', $userID, 'login', $userAccount.login, 'email', $userAccount.email,
                                           'name', $userAccount.contentobject.name, 'node_id', $userAccount.contentobject.main_node_id,
                                           'own', eq( $userID, fetch( 'user', 'current_user' ).contentobject_id ),
                                           'groups', array(), 'roles', array(), 'last_visit', false(), 'login_count', 0,
                                           'password_changed', false(), 'password_age_days', false() ) )
     $own = $acc.own
     $state_text = hash( 'active', 'Active'|i18n( 'design/admin/user/edit' ),
                         'disabled', 'Disabled'|i18n( 'design/admin/user/edit' ),
                         'locked', 'Locked'|i18n( 'design/admin/user/edit' ) )}

<form name="Edit" method="post" action={concat( $module.functions.edit.uri, '/', $userID )|ezurl} class="exp-account exp-standalone">

<div class="context-block">

<div class="box-header"><div class="box-ml">
<div class="exp-title-row">
<h1 class="context-title">{if $own}{'My account'|i18n( 'design/admin/user/edit' )}{else}{'User account: %name'|i18n( 'design/admin/user/edit',, hash( '%name', $acc.name ) )|wash}{/if}</h1>
<span class="exp-meta">{'ID %id'|i18n( 'design/admin/user/edit',, hash( '%id', $acc.user_id ) )}</span>
</div>
</div></div>

<div class="box-bc"><div class="box-ml"><div class="box-content">

<div class="exp-actionbar">
    <p class="exp-meta">{if $own}{'Your account at a glance, and every page to change it from.'|i18n( 'design/admin/user/edit' )}{else}{'The account of this user at a glance, and every page to change it from.'|i18n( 'design/admin/user/edit' )}{/if}</p>
    <div class="exp-actions">
        <button class="exp-btn exp-btn-primary" type="submit" name="EditButton" value="1">{'Edit profile'|i18n( 'design/admin/user/edit' )}</button>
        {if $acc.node_id}<a class="exp-btn" href={concat( 'content/view/full/', $acc.node_id )|ezurl}>{'Show in the content tree'|i18n( 'design/admin/user/edit' )}</a>{/if}
        <button class="exp-btn" type="submit" name="CancelButton" value="1">{'Cancel'|i18n( 'design/admin/user/edit' )}</button>
    </div>
</div>

{* ---- Security hints ---- *}
{if and( $ov, $ov.hints|count )}
<ul class="exp-hints" aria-label="{'Security hints'|i18n( 'design/admin/user/edit' )}">
{foreach $ov.hints as $hint}
    <li class="exp-feedback is-{$hint.level|wash}">
    {switch match=$hint.key}
    {case match='disabled'}<strong>{'Disabled.'|i18n( 'design/admin/user/edit' )}</strong> {'This account is disabled: its user cannot sign in. It is enabled again under Account settings.'|i18n( 'design/admin/user/edit' )}{/case}
    {case match='locked'}<strong>{'Locked.'|i18n( 'design/admin/user/edit' )}</strong> {'This account is locked after too many failed sign-ins. Resetting the count under Account settings lets its user sign in again.'|i18n( 'design/admin/user/edit' )}{/case}
    {case match='failed_attempts'}<strong>{'Failed sign-ins.'|i18n( 'design/admin/user/edit' )}</strong> {if $hint.max|gt( 0 )}{'%count failed sign-ins since the last successful one; the account is locked after %max.'|i18n( 'design/admin/user/edit',, hash( '%count', $hint.count, '%max', $hint.max ) )}{else}{'%count failed sign-ins since the last successful one.'|i18n( 'design/admin/user/edit',, hash( '%count', $hint.count ) )}{/if}{/case}
    {case match='twofactor_off'}<strong>{'Two-step sign-in is off.'|i18n( 'design/admin/user/edit' )}</strong> {if $own}{'A code from your phone at sign-in keeps your account safe even if your password leaks.'|i18n( 'design/admin/user/edit' )}{else}{'This user signs in with the password alone.'|i18n( 'design/admin/user/edit' )}{/if}{/case}
    {case match='no_password'}<strong>{'No password.'|i18n( 'design/admin/user/edit' )}</strong> {'This account has no password set: nobody can sign in with it until one is set.'|i18n( 'design/admin/user/edit' )}{/case}
    {case match='old_hash'}<strong>{'Old password storage.'|i18n( 'design/admin/user/edit' )}</strong> {'The password is stored with an older method. Changing it stores it with the current, stronger one.'|i18n( 'design/admin/user/edit' )}{/case}
    {case match='password_old'}<strong>{'Old password.'|i18n( 'design/admin/user/edit' )}</strong> {'The password was last changed %days days ago.'|i18n( 'design/admin/user/edit',, hash( '%days', $hint.days ) )}{/case}
    {case match='never_signed_in'}<strong>{'Never signed in.'|i18n( 'design/admin/user/edit' )}</strong> {'This account has not been used to sign in yet.'|i18n( 'design/admin/user/edit' )}{/case}
    {case}{/case}
    {/switch}
    </li>
{/foreach}
</ul>
{/if}

{* ---- The overview ---- *}
<section class="exp-section" aria-labelledby="account-overview-title">
<div class="exp-panel">
    <div class="exp-who">
        <span class="exp-avatar" aria-hidden="true">{$acc.name|extract( 0, 1 )|upcase|wash}</span>
        <div class="exp-who-text">
            <h2 id="account-overview-title">{$acc.name|wash}</h2>
        </div>
        {if $ov}<span class="exp-badge is-{$ov.state.level|wash}">{$state_text[$ov.state.key]|wash}</span>{/if}
    </div>
    <dl class="exp-facts">
        <div><dt>{'Login'|i18n( 'design/admin/user/edit' )}</dt><dd>{$acc.login|wash}</dd></div>
        <div><dt>{'E-mail'|i18n( 'design/admin/user/edit' )}</dt><dd>{$acc.email|wash( email )}</dd></div>
        {if $ov}
        <div><dt>{'Groups'|i18n( 'design/admin/user/edit' )}</dt><dd>
            {if $acc.groups|count}<ul class="exp-inline">{foreach $acc.groups as $group}<li><a href={concat( 'content/view/full/', $group.node_id )|ezurl}>{$group.name|wash}</a></li>{/foreach}</ul>
            {else}<span class="exp-meta">{'None'|i18n( 'design/admin/user/edit' )}</span>{/if}</dd></div>
        <div><dt>{'Roles'|i18n( 'design/admin/user/edit' )}</dt><dd>
            {if $acc.roles|count}<ul class="exp-inline">{foreach $acc.roles as $role}<li>{if $ov.can_view_roles}<a href={concat( 'role/view/', $role.id )|ezurl}>{$role.name|wash}</a>{else}{$role.name|wash}{/if}</li>{/foreach}</ul>
            {else}<span class="exp-meta">{'None'|i18n( 'design/admin/user/edit' )}</span>{/if}</dd></div>
        <div><dt>{'Last sign-in'|i18n( 'design/admin/user/edit' )}</dt><dd>
            {if $acc.last_visit}{$acc.last_visit|l10n( 'shortdatetime' )}<span class="exp-meta">{'%count sign-ins in all'|i18n( 'design/admin/user/edit',, hash( '%count', $acc.login_count ) )}</span>
            {else}{'Never'|i18n( 'design/admin/user/edit' )}{/if}</dd></div>
        <div><dt>{'Password changed'|i18n( 'design/admin/user/edit' )}</dt><dd>
            {if $acc.password_changed}{$acc.password_changed|l10n( 'shortdate' )}<span class="exp-meta">{if $acc.password_age_days|eq( 0 )}{'today'|i18n( 'design/admin/user/edit' )}{else}{'%days days ago'|i18n( 'design/admin/user/edit',, hash( '%days', $acc.password_age_days ) )}{/if}</span>
            {else}<span class="exp-meta">{'Not recorded'|i18n( 'design/admin/user/edit' )}</span>{/if}</dd></div>
        {/if}
    </dl>
</div>
</section>

{* ---- Where to go on ---- *}
<section class="exp-section" aria-labelledby="account-actions-title">
<div class="exp-section-head"><h2 class="exp-h2" id="account-actions-title">{if $own}{'Manage your account'|i18n( 'design/admin/user/edit' )}{else}{'Manage this account'|i18n( 'design/admin/user/edit' )}{/if}</h2></div>
<ul class="exp-cards">
{if $ov}
{foreach $ov.actions as $action}
    {def $title = '' $text = '' $label = '' $badge = '' $icon = ''}
    {switch match=$action.key}
    {case match='profile'}
        {set $title = 'Profile'|i18n( 'design/admin/user/edit' )
             $text = cond( $own, 'Your name, e-mail address, image and the other fields of your user.'|i18n( 'design/admin/user/edit' ), 'The name, e-mail address, image and the other fields of this user.'|i18n( 'design/admin/user/edit' ) )
             $label = 'Edit profile'|i18n( 'design/admin/user/edit' )
             $icon = '<circle cx="12" cy="8" r="4"/><path d="M4 21c0-4 4-6 8-6s8 2 8 6"/>'}
    {/case}
    {case match='password'}
        {set $title = 'Password'|i18n( 'design/admin/user/edit' )
             $text = cond( $own, 'Change the password you sign in with.'|i18n( 'design/admin/user/edit' ), 'Set a new password for this user.'|i18n( 'design/admin/user/edit' ) )
             $label = 'Change password'|i18n( 'design/admin/user/edit' )
             $icon = '<rect x="4" y="11" width="16" height="10" rx="2"/><path d="M8 11V7a4 4 0 0 1 8 0v4"/>'}
    {/case}
    {case match='settings'}
        {set $title = 'Account settings'|i18n( 'design/admin/user/edit' )
             $text = 'Enable or disable the account, reset failed sign-ins, and see its API keys.'|i18n( 'design/admin/user/edit' )
             $label = 'Open settings'|i18n( 'design/admin/user/edit' )
             $badge = $state_text[$action.status.key]
             $icon = '<circle cx="12" cy="12" r="3"/><path d="M19.4 15a1.7 1.7 0 0 0 .3 1.8l.1.1a2 2 0 1 1-2.8 2.8l-.1-.1a1.7 1.7 0 0 0-1.8-.3 1.7 1.7 0 0 0-1 1.5V21a2 2 0 1 1-4 0v-.1a1.7 1.7 0 0 0-1.1-1.5 1.7 1.7 0 0 0-1.8.3l-.1.1a2 2 0 1 1-2.8-2.8l.1-.1a1.7 1.7 0 0 0 .3-1.8 1.7 1.7 0 0 0-1.5-1H3a2 2 0 1 1 0-4h.1a1.7 1.7 0 0 0 1.5-1.1 1.7 1.7 0 0 0-.3-1.8l-.1-.1a2 2 0 1 1 2.8-2.8l.1.1a1.7 1.7 0 0 0 1.8.3H9a1.7 1.7 0 0 0 1-1.5V3a2 2 0 1 1 4 0v.1a1.7 1.7 0 0 0 1 1.5 1.7 1.7 0 0 0 1.8-.3l.1-.1a2 2 0 1 1 2.8 2.8l-.1.1a1.7 1.7 0 0 0-.3 1.8V9a1.7 1.7 0 0 0 1.5 1H21a2 2 0 1 1 0 4h-.1a1.7 1.7 0 0 0-1.5 1z"/>'}
    {/case}
    {case match='twofactor'}
        {set $title = 'Two-step sign-in'|i18n( 'design/admin/user/edit' )
             $text = cond( $own, 'A code from an authenticator app or by e-mail at sign-in, besides your password.'|i18n( 'design/admin/user/edit' ), 'Set in the profile of this user.'|i18n( 'design/admin/user/edit' ) )
             $label = cond( $own, cond( eq( $action.status.level, 'ok' ), 'Manage'|i18n( 'design/admin/user/edit' ), 'Turn on'|i18n( 'design/admin/user/edit' ) ), 'Edit profile'|i18n( 'design/admin/user/edit' ) )
             $badge = cond( eq( $action.status.key, 'on_app' ), 'On, authenticator app'|i18n( 'design/admin/user/edit' ),
                            eq( $action.status.key, 'on_email' ), 'On, e-mail codes'|i18n( 'design/admin/user/edit' ), 'Off'|i18n( 'design/admin/user/edit' ) )
             $icon = '<rect x="7" y="2" width="10" height="20" rx="2"/><path d="M11 18h2"/>'}
    {/case}
    {case match='apikeys'}
        {set $title = 'API keys'|i18n( 'design/admin/user/edit' )
             $text = cond( $own, 'Personal keys for your own scripts and tools to publish through the REST interface.'|i18n( 'design/admin/user/edit' ), 'Show and revoke the API keys of this user.'|i18n( 'design/admin/user/edit' ) )
             $label = cond( $own, 'Manage my API keys'|i18n( 'design/admin/user/edit' ), 'Show API keys'|i18n( 'design/admin/user/edit' ) )
             $badge = cond( $action.status, '%active active, %total in all'|i18n( 'design/admin/user/edit',, hash( '%active', $action.status.active, '%total', $action.status.total ) ), '' )
             $icon = '<circle cx="8" cy="15" r="4"/><path d="M10.8 12.2 20 3M16 7l3 3M14 9l2 2"/>'}
    {/case}
    {case match='bookmarks'}
        {set $title = 'Bookmarks'|i18n( 'design/admin/user/edit' )
             $text = 'Your bookmarks, in folders, for quick access to content.'|i18n( 'design/admin/user/edit' )
             $label = 'Open bookmarks'|i18n( 'design/admin/user/edit' )
             $icon = '<path d="M6 3h12v18l-6-4-6 4z"/>'}
    {/case}
    {case match='notifications'}
        {set $title = 'Notifications'|i18n( 'design/admin/user/edit' )
             $text = 'The content you follow, and how often you hear about changes to it.'|i18n( 'design/admin/user/edit' )
             $label = 'Notification settings'|i18n( 'design/admin/user/edit' )
             $icon = '<path d="M18 16V11a6 6 0 0 0-12 0v5l-2 2h16z"/><path d="M10 21h4"/>'}
    {/case}
    {case match='mail'}
        {set $title = 'E-mail preferences'|i18n( 'design/admin/user/edit' )
             $text = cond( $own, 'Choose which e-mail you get, download your e-mail data, or stop all optional e-mail.'|i18n( 'design/admin/user/edit' ), 'The e-mail preferences and consents of this user.'|i18n( 'design/admin/user/edit' ) )
             $label = 'Open e-mail preferences'|i18n( 'design/admin/user/edit' )
             $icon = '<rect x="3" y="5" width="18" height="14" rx="2"/><path d="m3 7 9 6 9-6"/>'}
    {/case}
    {case}{/case}
    {/switch}
    {if $title}
    <li class="exp-card{if and( $action.status, eq( $action.status.level, 'warn' ) )} is-attention{/if}" id="account-card-{$action.key|wash}">
        <div class="exp-card-head">
            <h3><svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true" focusable="false">{$icon}</svg>{$title|wash}</h3>
            {if $badge}<span class="exp-badge{if and( $action.status, ne( $action.status.level, 'none' ) )} is-{$action.status.level|wash}{/if}">{$badge|wash}</span>{/if}
        </div>
        <p>{$text|wash}</p>
        {if eq( $action.how, 'post' )}
        <button class="exp-btn" type="submit" name="{$action.button|wash}" value="1">{$label|wash}</button>
        {else}
        <a class="exp-btn" href={$action.url|ezurl}>{$label|wash}</a>
        {/if}
    </li>
    {/if}
    {undef $title $text $label $badge $icon}
{/foreach}
{else}
    <li class="exp-card"><div class="exp-card-head"><h3>{'Password'|i18n( 'design/admin/user/edit' )}</h3></div>
        <button class="exp-btn" type="submit" name="ChangePasswordButton" value="1">{'Change password'|i18n( 'design/admin/user/edit' )}</button></li>
    <li class="exp-card"><div class="exp-card-head"><h3>{'Account settings'|i18n( 'design/admin/user/edit' )}</h3></div>
        <button class="exp-btn" type="submit" name="ChangeSettingButton" value="1">{'Open settings'|i18n( 'design/admin/user/edit' )}</button></li>
{/if}
</ul>
</section>

</div></div></div>

</div>

<input type="hidden" name="ContentObjectLanguageCode" value="{$userAccount.contentobject.initial_language_code|wash}" />
{if and( is_set( $redirect_if_discarded ), $redirect_if_discarded )}<input type="hidden" name="RedirectIfDiscarded" value="{$redirect_if_discarded|wash}" />{/if}
</form>
{undef $ov $acc $own $state_text}
