{* Deactivate and move / Reactivate and move back (user/deactivate): the confirmation page (each account, the groups it
   is in now and where it goes; the others counted by why they are left out) and the result. Reached by POST from a
   user's page, the context menu or Users > Check user accounts; every account is checked again on Confirm
   (expUserDeactivation::decide()). Names are shown only of accounts and groups you may read. The same file is in
   design/admin and design/admin4. Guide: doc/features/6.0/user-hygiene.md, section "Deactivate and move" *}

{def $reason_names = hash( 'protected', 'the anonymous user, the administrator account or you'|i18n( 'design/admin/user/deactivate' ),
                           'gone', 'not found, or not visible to you'|i18n( 'design/admin/user/deactivate' ),
                           'no_object', 'not found, or not visible to you'|i18n( 'design/admin/user/deactivate' ),
                           'not_user', 'not a user account'|i18n( 'design/admin/user/deactivate' ),
                           'denied', 'you may not edit, move or remove them, or not place them where they go'|i18n( 'design/admin/user/deactivate' ),
                           'no_location', 'without a location'|i18n( 'design/admin/user/deactivate' ),
                           'has_children', 'a location has sub-items'|i18n( 'design/admin/user/deactivate' ),
                           'no_group', 'the group of deactivated users is missing'|i18n( 'design/admin/user/deactivate' ),
                           'unactivated', 'never activated: activate them under Unactivated users'|i18n( 'design/admin/user/deactivate' ),
                           'no_target', 'neither a former location nor the default user group can take them'|i18n( 'design/admin/user/deactivate' ),
                           'unchanged', 'already in that state'|i18n( 'design/admin/user/deactivate' ) )
     $group_problems = hash( 'configured_missing', 'The node set in site.ini [UserSettings] DeactivatedUserGroupNodeID does not exist. Correct the setting or leave it empty.'|i18n( 'design/admin/user/deactivate' ),
                             'unpublished', 'An object with the remote id of the group of deactivated users exists but has no published location. Publish it, or set [UserSettings] DeactivatedUserGroupNodeID.'|i18n( 'design/admin/user/deactivate' ),
                             'create_denied', 'The group of deactivated users does not exist yet, and you may not create it (a user group below the Users node). Ask an administrator to create it, for instance with bin/php/ezcreatedeactivatedusergroup.php.'|i18n( 'design/admin/user/deactivate' ),
                             'create_failed', 'The group of deactivated users could not be created. Nothing was changed.'|i18n( 'design/admin/user/deactivate' ),
                             'group_roles', 'Roles are assigned to the group of deactivated users, so the accounts moved into it would get them. Remove those role assignments first. Nothing was changed.'|i18n( 'design/admin/user/deactivate' ) )
     $back = $deactivate_redirect}

<div class="context-block a4-page">
<div class="box-header"><div class="box-ml">
<h1 class="context-title">{'Deactivate and move'|i18n( 'design/admin/user/deactivate' )}</h1>
</div></div>
<div class="box-bc"><div class="box-ml"><div class="box-content">

{if is_set( $deactivate_confirm )}
{def $done = $deactivate_confirm.result.done|count
     $is_deactivate = eq( $deactivate_confirm.action, 'deactivate_move' )}
<form method="post" action={'user/deactivate'|ezurl}>
<section class="a4-section" aria-labelledby="deactivate-confirm-title">
    <h2 class="a4-h2" id="deactivate-confirm-title">{if $is_deactivate}{'Deactivate and move %count accounts?'|i18n( 'design/admin/user/deactivate',, hash( '%count', $done ) )}{else}{'Reactivate and move back %count accounts?'|i18n( 'design/admin/user/deactivate',, hash( '%count', $done ) )}{/if}</h2>
    <p>{if $is_deactivate}{'Each account is disabled, so it cannot sign in, and its sessions end. Its locations are brought together in the group of deactivated users, which has no roles and is hidden; where it was is remembered. Its content and the roles assigned to the account itself stay. Nothing is removed.'|i18n( 'design/admin/user/deactivate' )}{else}{'Each account is enabled and moved back to the locations it had. A former location that no longer exists or that you may not use is left out; when the main one is, the account goes to the default user group.'|i18n( 'design/admin/user/deactivate' )}{/if}
    {'%selected selected; each is checked again when you confirm.'|i18n( 'design/admin/user/deactivate',, hash( '%selected', $deactivate_confirm.ids|count ) )}</p>

    {if ne( $deactivate_group_problem, '' )}
    <div class="a4-notice is-bad" role="alert">{first_set( $group_problems[$deactivate_group_problem], $deactivate_group_problem )|wash}</div>
    {/if}

    {if $deactivate_confirm.accounts}
    <div class="a4-table-wrap">
    <table class="a4-table" id="deactivate-list">
    <caption class="a4-sr-only">{'Accounts'|i18n( 'design/admin/user/deactivate' )}</caption>
    <thead><tr>
        <th scope="col">{'Name'|i18n( 'design/admin/user/deactivate' )}</th>
        <th scope="col">{'In the groups now'|i18n( 'design/admin/user/deactivate' )}</th>
        <th scope="col"><span aria-hidden="true">&#8594;</span> {'Goes to'|i18n( 'design/admin/user/deactivate' )}</th>
    </tr></thead>
    <tbody>
    {foreach $deactivate_confirm.accounts as $account}
    <tr>
        <td>{if $account.name|ne( '' )}{$account.name|wash}{else}<span class="a4-muted">{'(no name)'|i18n( 'design/admin/user/deactivate' )}</span>{/if}</td>
        <td>{$account.groups|implode( ', ' )|wash}</td>
        <td>{$account.target|implode( ', ' )|wash}
            {if $account.fallback}<br /><span class="a4-badge is-warn">{'default user group: the former main location is gone or not yours to use'|i18n( 'design/admin/user/deactivate' )}</span>{/if}
            {if $account.was_disabled}<br /><span class="a4-badge is-info">{'was disabled already before; enabled now'|i18n( 'design/admin/user/deactivate' )}</span>{/if}
            {if $account.not_restored|gt( 0 )}<br /><span class="a4-badge is-warn">{'%count former locations left out (gone or not yours to use)'|i18n( 'design/admin/user/deactivate',, hash( '%count', $account.not_restored ) )}</span>{/if}</td>
    </tr>
    {/foreach}
    </tbody>
    </table>
    </div>
    {/if}

    {if $deactivate_confirm.result.skipped}
    <div class="a4-notice is-warn" role="status">
        <p>{'Left out:'|i18n( 'design/admin/user/deactivate' )}</p>
        <ul>{foreach $deactivate_confirm.result.skipped as $reason => $skipped_ids}<li>{'%count %reason'|i18n( 'design/admin/user/deactivate',, hash( '%count', $skipped_ids|count, '%reason', first_set( $reason_names[$reason], $reason ) ) )|wash}</li>{/foreach}</ul>
    </div>
    {/if}

    {foreach $deactivate_confirm.ids as $id}<input type="hidden" name="UserIDArray[]" value="{$id|wash}" />{/foreach}
    <input type="hidden" name="DeactivateAction" value="{$deactivate_confirm.action|wash}" />
    <input type="hidden" name="DeactivateToken" value="{$deactivate_confirm.token|wash}" />
    <input type="hidden" name="RedirectURI" value="{$back|wash}" />
    <div class="a4-actions">
        {if $deactivate_confirm.can_confirm}
        <button class="a4-btn {if $is_deactivate}a4-btn-danger{else}a4-btn-primary{/if}" type="submit" name="ConfirmButton" value="1">{if $is_deactivate}{'Deactivate and move'|i18n( 'design/admin/user/deactivate' )}{else}{'Reactivate and move back'|i18n( 'design/admin/user/deactivate' )}{/if} ({$done})</button>
        {/if}
        <a class="a4-btn" href={$back|ezurl}>{'Cancel'|i18n( 'design/admin/user/deactivate' )}</a>
    </div>
</section>
</form>
{undef $done $is_deactivate}

{elseif is_set( $deactivate_result )}
{def $done = $deactivate_result.result.done|count
     $is_deactivate = eq( $deactivate_result.action, 'deactivate_move' )}
<div class="a4-notice {if $done|gt( 0 )}is-ok{else}is-warn{/if}" role="status">
    <p><strong>{if $is_deactivate}{'%count accounts deactivated and moved.'|i18n( 'design/admin/user/deactivate',, hash( '%count', $done ) )}{else}{'%count accounts reactivated and moved back.'|i18n( 'design/admin/user/deactivate',, hash( '%count', $done ) )}{/if}</strong></p>
    {if $deactivate_result.names}
    <ul>{foreach $deactivate_result.names as $name}<li>{$name|wash}</li>{/foreach}{if $done|gt( $deactivate_result.names|count )}<li>{'and %count more'|i18n( 'design/admin/user/deactivate',, hash( '%count', sub( $done, $deactivate_result.names|count ) ) )}</li>{/if}</ul>
    {/if}
    {if or( $deactivate_result.result.skipped, $deactivate_result.result.failed, $deactivate_result.fallback, $deactivate_result.not_restored )}
    <ul>
        {foreach $deactivate_result.result.skipped as $reason => $skipped_ids}<li>{'%count left out: %reason'|i18n( 'design/admin/user/deactivate',, hash( '%count', $skipped_ids|count, '%reason', first_set( $reason_names[$reason], $reason ) ) )|wash}</li>{/foreach}
        {if $deactivate_result.result.failed}<li>{'%count could not be changed; nothing was changed for them'|i18n( 'design/admin/user/deactivate',, hash( '%count', $deactivate_result.result.failed|count ) )}</li>{/if}
        {if $deactivate_result.fallback|gt( 0 )}<li>{'%count went to the default user group, as their former main location is gone or not yours to use'|i18n( 'design/admin/user/deactivate',, hash( '%count', $deactivate_result.fallback ) )}</li>{/if}
        {if $deactivate_result.not_restored|gt( 0 )}<li>{'%count former locations were left out (gone or not yours to use)'|i18n( 'design/admin/user/deactivate',, hash( '%count', $deactivate_result.not_restored ) )}</li>{/if}
    </ul>
    {/if}
</div>
<div class="a4-actions">
    <a class="a4-btn a4-btn-primary" href={$back|ezurl}>{'Back'|i18n( 'design/admin/user/deactivate' )}</a>
</div>
{undef $done $is_deactivate}

{else}
<p class="a4-intro">{'Retire user accounts instead of removing them: users never go to the trash, so removing one cannot be undone. Deactivate and move disables an account and brings its locations together in the group of deactivated users; Reactivate and move back puts it back where it was.'|i18n( 'design/admin/user/deactivate' )}</p>
{if $deactivate_nothing_selected}
<div class="a4-notice is-warn" role="alert">{'No account was selected. Tick the accounts first.'|i18n( 'design/admin/user/deactivate' )}</div>
{/if}
<p>{'Choose the accounts on Users > Check user accounts, or use the button on a user’s page.'|i18n( 'design/admin/user/deactivate' )}</p>
<div class="a4-actions">
    <a class="a4-btn a4-btn-primary" href={'user/hygiene'|ezurl}>{'Check user accounts'|i18n( 'design/admin/user/deactivate' )}</a>
</div>
{/if}

</div></div></div>
</div>
{undef $reason_names $group_problems $back}
