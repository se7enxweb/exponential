{* Setup > "Verify it's you" (setup/reauth[/<siteaccess>], doc/eng-us/guides/verify-its-you.md), admin and admin4: where
   the values apply (every siteaccess or one), the switch of the feature, the minutes a confirmation counts, a switch per
   group of actions and, for content, the user groups it asks. Only shared components of components.css; the same file
   is in design/admin. Variables: settings (enabled, minutes, scope, groups: name, enabled, rule_count, user_groups),
   scope, siteaccesses, action_uri, user_groups (id, name), user_group_pickers, message (kind, text). *}
{def $texts = hash( 'account', hash( 'title', 'Your own account'|i18n( 'design/admin/setup/reauth' ), 'text', 'Changing one\'s own password, e-mail address, passkeys, two-step sign-in, recovery codes or API keys, and signing out one\'s other devices.'|i18n( 'design/admin/setup/reauth' ) ),
                    'users', hash( 'title', 'Users, roles and policies'|i18n( 'design/admin/setup/reauth' ), 'text', 'Creating and editing users, their passwords and devices, account settings, approving or denying new user accounts, assigning roles and editing policies.'|i18n( 'design/admin/setup/reauth' ) ),
                    'system', hash( 'title', 'System settings and setup actions'|i18n( 'design/admin/setup/reauth' ), 'text', 'Editing settings, extensions, caches and maintenance, packages and updates, backups and the git manager.'|i18n( 'design/admin/setup/reauth' ) ),
                    'export', hash( 'title', 'Exporting data'|i18n( 'design/admin/setup/reauth' ), 'text', 'Exports of collected information, users, newsletter subscribers and the audit.'|i18n( 'design/admin/setup/reauth' ) ),
                    'delete', hash( 'title', 'Deleting data'|i18n( 'design/admin/setup/reauth' ), 'text', 'Emptying the trash, removing items from it and removing several items at once.'|i18n( 'design/admin/setup/reauth' ) ),
                    'content', hash( 'title', 'Removing, publishing and saving content'|i18n( 'design/admin/setup/reauth' ), 'text', 'Removing content (also to the trash), publishing and storing a draft, asked when the form is sent. Off by default.'|i18n( 'design/admin/setup/reauth' ) ) )}
<div class="context-block a4-page">

<div class="box-header"><div class="box-ml">
<h1 class="context-title"><span class="a4-icon a4-bi-shield-lock is-large" aria-hidden="true"></span> {"Verify it's you"|i18n( 'design/admin/setup/reauth' )}</h1>
</div></div>

<div class="box-bc"><div class="box-ml"><div class="box-content">

<p class="a4-intro">{'Before a sensitive action a signed-in user confirms who they are again: with their password and a passkey, or without a passkey with their password and the code of their authenticator app. Only the web pages ask: the REST interface, SOAP, WebDAV, the expservices login and the command line never do.'|i18n( 'design/admin/setup/reauth' )}</p>

{if $message}
<div class="a4-notice is-{if eq( $message.kind, 'ok' )}ok{else}bad{/if}" role="status">{$message.text|wash}</div>
{/if}

<form method="get" action={'setup/reauth'|ezurl} class="a4-filterbar" aria-label="{'Where the settings apply'|i18n( 'design/admin/setup/reauth' )|wash}">
    <div class="a4-field">
        <label for="reauth-scope">{'Settings for'|i18n( 'design/admin/setup/reauth' )}</label>
        <select id="reauth-scope" name="ReauthScope">
            <option value=""{if eq( $scope, '' )} selected="selected"{/if}>{'Every siteaccess'|i18n( 'design/admin/setup/reauth' )}</option>
            {foreach $siteaccesses as $siteaccess}
            <option value="{$siteaccess|wash}"{if eq( $scope, $siteaccess )} selected="selected"{/if}>{'Siteaccess %name'|i18n( 'design/admin/setup/reauth',, hash( '%name', $siteaccess ) )|wash}</option>
            {/foreach}
        </select>
    </div>
    <div class="a4-actions"><button class="a4-btn" type="submit">{'Open'|i18n( 'design/admin/setup/reauth' )}</button></div>
</form>

<form method="post" action={$action_uri|ezurl} class="a4-stack">

<section class="a4-card" aria-labelledby="reauth-general-title">
    <div class="a4-card-head"><h2 class="a4-h3" id="reauth-general-title">{'General'|i18n( 'design/admin/setup/reauth' )}</h2></div>
    <div class="a4-form">
        <label class="a4-check"><input type="checkbox" name="ReauthEnabled" value="1"{if $settings.enabled} checked="checked"{/if} />
            <span>{"Ask \"Verify it's you\" before the actions of the groups switched on below"|i18n( 'design/admin/setup/reauth' )}</span></label>
        <div class="a4-field">
            <label for="reauth-minutes">{'Minutes a confirmation counts'|i18n( 'design/admin/setup/reauth' )}</label>
            <input type="number" id="reauth-minutes" name="ReauthMinutes" min="1" max="1440" value="{$settings.minutes}" required="required" />
        </div>
    </div>
</section>

<section class="a4-card" aria-labelledby="reauth-groups-title">
    <div class="a4-card-head"><h2 class="a4-h3" id="reauth-groups-title">{'When to ask'|i18n( 'design/admin/setup/reauth' )}</h2></div>
    <fieldset class="a4-field">
        <legend class="a4-sr-only">{'When to ask'|i18n( 'design/admin/setup/reauth' )}</legend>
        {foreach $settings.groups as $group}
        <label class="a4-check" id="reauth-group-{$group.name|wash}"><input type="checkbox" name="ReauthGroups[]" value="{$group.name|wash}"{if $group.enabled} checked="checked"{/if} />
            <span><strong>{if is_set( $texts[$group.name] )}{$texts[$group.name].title|wash}{else}{$group.name|wash}{/if}</strong>
            <span class="a4-meta">{if is_set( $texts[$group.name] )}{$texts[$group.name].text|wash} {/if}{'%count rules in site.ini [ReauthGroup_%name].'|i18n( 'design/admin/setup/reauth',, hash( '%count', $group.rule_count, '%name', $group.name ) )|wash}</span></span></label>
        {/foreach}
    </fieldset>
</section>

{foreach $settings.groups as $group}{if $user_group_pickers|contains( $group.name )}
<section class="a4-card" aria-labelledby="reauth-usergroups-{$group.name|wash}">
    <div class="a4-card-head"><h2 class="a4-h3" id="reauth-usergroups-{$group.name|wash}">{'Who is asked: %group'|i18n( 'design/admin/setup/reauth',, hash( '%group', cond( is_set( $texts[$group.name] ), $texts[$group.name].title, $group.name ) ) )|wash}</h2></div>
    <input type="hidden" name="ReauthUserGroupsShown[]" value="{$group.name|wash}" />
    <fieldset class="a4-field">
        <legend class="a4-help">{'Tick user groups to ask only their members (editors, for example). None ticked: every user.'|i18n( 'design/admin/setup/reauth' )}</legend>
        {foreach $user_groups as $user_group}
        <label class="a4-check"><input type="checkbox" name="ReauthUserGroups[{$group.name|wash}][]" value="{$user_group.id}"{if $group.user_groups|contains( $user_group.id )} checked="checked"{/if} />
            <span>{$user_group.name|wash}</span></label>
        {/foreach}
    </fieldset>
</section>
{/if}{/foreach}

<div class="a4-toolbar">
    <p class="a4-meta">{if eq( $scope, '' )}{'Saved to settings/override/site.ini.append.php, for every siteaccess. Saving asks you to confirm it is you.'|i18n( 'design/admin/setup/reauth' )}{else}{'Saved to settings/siteaccess/%name/site.ini.append.php. A value in settings/override wins over it. Saving asks you to confirm it is you.'|i18n( 'design/admin/setup/reauth',, hash( '%name', $scope ) )|wash}{/if}</p>
    <div class="a4-actions"><button class="a4-btn a4-btn-primary" type="submit" name="SaveReauthButton" value="1">{'Save'|i18n( 'design/admin/setup/reauth' )}</button></div>
</div>

</form>

</div></div></div>

</div>
{undef $texts}
