{* Check user accounts (user/hygiene): every user account, or those below a user group, by one filter (tabs with their
   counts: all, never signed in, not seen for N days, disabled, locked, orphaned, shared e-mail address), a search and
   the page size (a GET form), ordered by the column headers and paged. One row per account: name (its node), login,
   e-mail (only with role/assign), state, last visit, sign-ins, groups and roles.

   Disable, enable and unlock the ticked accounts: each button shows a confirmation with the counts, whose Confirm
   does it; every account is checked again then (expUserHygiene::decide()). The anonymous user, the administrator
   account and you are never ticked. Export as CSV sends the whole filtered list. Every change and the CSV are POSTs.
   Works without javascript; the script only adds Select all and the selection count. The same file is in
   design/admin and design/admin4. Guide: doc/features/6.0/user-hygiene.md *}

{def $uri = 'user/hygiene'
     $form_uri = concat( $uri, $hygiene_query )
     $state_levels = hash( 'active', 'is-ok', 'disabled', 'is-bad', 'unactivated', 'is-warn', 'locked', 'is-warn',
                           'no_object', 'is-bad', 'draft_only', 'is-bad', 'no_location', 'is-bad', 'never', 'is-info' )
     $state_names = hash( 'active', 'Active'|i18n( 'design/admin/user/hygiene' ),
                          'disabled', 'Disabled'|i18n( 'design/admin/user/hygiene' ),
                          'unactivated', 'Not activated'|i18n( 'design/admin/user/hygiene' ),
                          'locked', 'Locked'|i18n( 'design/admin/user/hygiene' ),
                          'no_object', 'No user object'|i18n( 'design/admin/user/hygiene' ),
                          'draft_only', 'Never published'|i18n( 'design/admin/user/hygiene' ),
                          'no_location', 'No location'|i18n( 'design/admin/user/hygiene' ),
                          'never', 'Never signed in'|i18n( 'design/admin/user/hygiene' ) )
     $reason_names = hash( 'protected', 'the anonymous user, the administrator account or you'|i18n( 'design/admin/user/hygiene' ),
                           'gone', 'not found, removed meanwhile'|i18n( 'design/admin/user/hygiene' ),
                           'no_object', 'without a user object'|i18n( 'design/admin/user/hygiene' ),
                           'denied', 'you may not edit them'|i18n( 'design/admin/user/hygiene' ),
                           'unactivated', 'never activated: activate them under Unactivated users'|i18n( 'design/admin/user/hygiene' ),
                           'unchanged', 'already in that state'|i18n( 'design/admin/user/hygiene' ) )
     $action_names = hash( 'disable', 'Disable'|i18n( 'design/admin/user/hygiene' ),
                           'enable', 'Enable'|i18n( 'design/admin/user/hygiene' ),
                           'unlock', 'Unlock'|i18n( 'design/admin/user/hygiene' ) )}

<div class="context-block a4-page">
<div class="box-header"><div class="box-ml">
<h1 class="context-title">{'Check user accounts'|i18n( 'design/admin/user/hygiene' )}</h1>
</div></div>
<div class="box-bc"><div class="box-ml"><div class="box-content">

{if $hygiene_supported|not}
<div class="a4-notice is-warn" role="status">{'This page needs an SQL database; it is not available on MongoDB.'|i18n( 'design/admin/user/hygiene' )}</div>

{elseif is_set( $hygiene_confirm )}
{* The confirmation of a bulk action *}
<form method="post" action={$form_uri|ezurl}>
<section class="a4-section" aria-labelledby="hygiene-confirm-title">
    <h2 class="a4-h2" id="hygiene-confirm-title">{switch match=$hygiene_confirm.action}
        {case match='disable'}{'Disable %count accounts?'|i18n( 'design/admin/user/hygiene',, hash( '%count', $hygiene_confirm.result.done|count ) )}{/case}
        {case match='enable'}{'Enable %count accounts?'|i18n( 'design/admin/user/hygiene',, hash( '%count', $hygiene_confirm.result.done|count ) )}{/case}
        {case}{'Unlock %count accounts?'|i18n( 'design/admin/user/hygiene',, hash( '%count', $hygiene_confirm.result.done|count ) )}{/case}
    {/switch}</h2>
    <p>{switch match=$hygiene_confirm.action}
        {case match='disable'}{'A disabled account cannot sign in, and its sessions end now. Its content, groups and roles stay; enable it again at any time.'|i18n( 'design/admin/user/hygiene' )}{/case}
        {case match='enable'}{'The accounts can sign in again.'|i18n( 'design/admin/user/hygiene' )}{/case}
        {case}{'The count of failed sign-ins goes back to zero, so the accounts can sign in again.'|i18n( 'design/admin/user/hygiene' )}{/case}
    {/switch}
    {'%selected selected; each is checked again when you confirm.'|i18n( 'design/admin/user/hygiene',, hash( '%selected', $hygiene_confirm.ids|count ) )}</p>
    {if $hygiene_confirm.names}
    <ul>{foreach $hygiene_confirm.names as $name}<li>{$name|wash}</li>{/foreach}{if $hygiene_confirm.result.done|count|gt( $hygiene_confirm.names|count )}<li>{'and %count more'|i18n( 'design/admin/user/hygiene',, hash( '%count', sub( $hygiene_confirm.result.done|count, $hygiene_confirm.names|count ) ) )}</li>{/if}</ul>
    {/if}
    {if $hygiene_confirm.result.skipped}
    <div class="a4-notice is-warn" role="status">
        <p>{'Left out:'|i18n( 'design/admin/user/hygiene' )}</p>
        <ul>{foreach $hygiene_confirm.result.skipped as $reason => $skipped_ids}<li>{'%count %reason'|i18n( 'design/admin/user/hygiene',, hash( '%count', $skipped_ids|count, '%reason', first_set( $reason_names[$reason], $reason ) ) )|wash}</li>{/foreach}</ul>
    </div>
    {/if}
    {foreach $hygiene_confirm.ids as $id}<input type="hidden" name="UserIDArray[]" value="{$id|wash}" />{/foreach}
    <input type="hidden" name="HygieneAction" value="{$hygiene_confirm.action|wash}" />
    <div class="a4-actions">
        {if $hygiene_confirm.result.done}
        <button class="a4-btn {if eq( $hygiene_confirm.action, 'disable' )}a4-btn-danger{else}a4-btn-primary{/if}" type="submit" name="ConfirmButton" value="1">{$action_names[$hygiene_confirm.action]|wash} ({$hygiene_confirm.result.done|count})</button>
        {/if}
        <a class="a4-btn" href={$form_uri|ezurl}>{'Cancel'|i18n( 'design/admin/user/hygiene' )}</a>
    </div>
</section>
</form>

{else}
<p class="a4-intro">{'Find accounts nobody uses or that need a look: never signed in, not seen for a long time, disabled, locked after failed sign-ins, without a user object or location, or sharing an e-mail address. Disable, enable or unlock them in bulk, or export the list.'|i18n( 'design/admin/user/hygiene' )}</p>

{* The result of the last action *}
{if is_set( $hygiene_action_result )}
    {def $done = $hygiene_action_result.result.done|count}
    <div class="a4-notice {if $done|gt( 0 )}is-ok{else}is-warn{/if}" role="status">
        <p><strong>{switch match=$hygiene_action_result.action}
            {case match='disable'}{'%count accounts disabled.'|i18n( 'design/admin/user/hygiene',, hash( '%count', $done ) )}{/case}
            {case match='enable'}{'%count accounts enabled.'|i18n( 'design/admin/user/hygiene',, hash( '%count', $done ) )}{/case}
            {case}{'%count accounts unlocked.'|i18n( 'design/admin/user/hygiene',, hash( '%count', $done ) )}{/case}
        {/switch}</strong></p>
        {if or( $hygiene_action_result.result.skipped, $hygiene_action_result.result.failed )}
        <ul>
            {foreach $hygiene_action_result.result.skipped as $reason => $skipped_ids}<li>{'%count left out: %reason'|i18n( 'design/admin/user/hygiene',, hash( '%count', $skipped_ids|count, '%reason', first_set( $reason_names[$reason], $reason ) ) )|wash}</li>{/foreach}
            {if $hygiene_action_result.result.failed}<li>{'%count could not be changed'|i18n( 'design/admin/user/hygiene',, hash( '%count', $hygiene_action_result.result.failed|count ) )}</li>{/if}
        </ul>
        {/if}
    </div>
    {undef $done}
{elseif is_set( $hygiene_nothing_selected )}
    <div class="a4-notice is-warn" role="alert">{'No account was selected. Tick the accounts first.'|i18n( 'design/admin/user/hygiene' )}</div>
{/if}

{* The filters, each with its count *}
<nav aria-label="{'Filter'|i18n( 'design/admin/user/hygiene' )}">
<ul class="a4-tabs">
{foreach hash( 'all', 'All accounts'|i18n( 'design/admin/user/hygiene' ),
               'never', 'Never signed in'|i18n( 'design/admin/user/hygiene' ),
               'inactive', 'Not seen for %days days'|i18n( 'design/admin/user/hygiene',, hash( '%days', $hygiene_filters.days ) ),
               'disabled', 'Disabled'|i18n( 'design/admin/user/hygiene' ),
               'locked', 'Locked'|i18n( 'design/admin/user/hygiene' ),
               'orphaned', 'Orphaned'|i18n( 'design/admin/user/hygiene' ),
               'duplicate_email', 'Shared e-mail address'|i18n( 'design/admin/user/hygiene' ) ) as $key => $text}
    {if is_set( $hygiene_filter_queries[$key] )}<li><a href={concat( $uri, $hygiene_filter_queries[$key] )|ezurl}{if eq( $key, $hygiene_filters.filter )} class="current" aria-current="page"{/if}>{$text|wash} <span class="a4-badge">{first_set( $hygiene_filter_counts[$key], 0 )}</span></a></li>{/if}
{/foreach}
</ul>
</nav>

<section aria-labelledby="hygiene-find-title">
<h2 class="a4-sr-only" id="hygiene-find-title">{'Narrow the list'|i18n( 'design/admin/user/hygiene' )}</h2>
<form class="a4-filterbar" method="get" action={$uri|ezurl} role="search">
    <input type="hidden" name="filter" value="{$hygiene_filters.filter|wash}" />
    <input type="hidden" name="sort" value="{$hygiene_filters.sort|wash}" />
    <input type="hidden" name="order" value="{$hygiene_filters.order|wash}" />
    <div class="a4-field">
        <label for="hygiene-search">{'Find an account'|i18n( 'design/admin/user/hygiene' )}</label>
        <input type="search" id="hygiene-search" name="q" value="{$hygiene_filters.q|wash}" autocomplete="off" maxlength="100" aria-describedby="hygiene-search-help" />
        <span class="a4-help" id="hygiene-search-help">{if $hygiene_can_see_email}{'Any part of the name, the login or the e-mail address.'|i18n( 'design/admin/user/hygiene' )}{else}{'Any part of the name or the login.'|i18n( 'design/admin/user/hygiene' )}{/if}</span>
    </div>
    <div class="a4-field">
        <label for="hygiene-group">{'User group'|i18n( 'design/admin/user/hygiene' )}</label>
        <select id="hygiene-group" name="group">
            <option value="0">{'Every group'|i18n( 'design/admin/user/hygiene' )}</option>
            {foreach $hygiene_groups as $group}
            <option value="{$group.node_id}"{if eq( $group.node_id, $hygiene_filters.group )} selected="selected"{/if}>{'&nbsp;&nbsp;'|repeat( max( 0, sub( $group.depth, 2 ) ) )}{$group.name|wash}</option>
            {/foreach}
        </select>
    </div>
    {if eq( $hygiene_filters.filter, 'inactive' )}
    <div class="a4-field">
        <label for="hygiene-days">{'Not seen for at least (days)'|i18n( 'design/admin/user/hygiene' )}</label>
        <input type="number" id="hygiene-days" name="days" value="{$hygiene_filters.days|wash}" min="1" max="36500" step="1" inputmode="numeric" />
    </div>
    {/if}
    <div class="a4-field">
        <label for="hygiene-limit">{'Per page'|i18n( 'design/admin/user/hygiene' )}</label>
        <select id="hygiene-limit" name="limit">
            {foreach array( 25, 50, 100 ) as $size}<option value="{$size}"{if eq( $size, $hygiene_filters.limit )} selected="selected"{/if}>{$size}</option>{/foreach}
        </select>
    </div>
    <div class="a4-field">
        <div class="a4-actions">
            <button type="submit" class="a4-btn a4-btn-primary">{'Show'|i18n( 'design/admin/user/hygiene' )}</button>
            {if or( $hygiene_filters.q|ne( '' ), $hygiene_filters.group|gt( 0 ) )}<a class="a4-btn" href={concat( $uri, $hygiene_clear_query )|ezurl}>{'Clear'|i18n( 'design/admin/user/hygiene' )}</a>{/if}
        </div>
    </div>
</form>
</section>

<form name="hygiene" method="post" action={$form_uri|ezurl}>
<section class="a4-section" aria-labelledby="hygiene-list-title">
<div class="a4-section-head">
    <h2 class="a4-h2" id="hygiene-list-title">{'Accounts'|i18n( 'design/admin/user/hygiene' )} ({$hygiene_account_count})</h2>
    {if $hygiene_account_count|gt( 0 )}
    <span class="a4-meta">{'%from to %to of %count'|i18n( 'design/admin/user/hygiene',, hash( '%from', sum( $view_parameters.offset, 1 ), '%to', min( sum( $view_parameters.offset, $hygiene_filters.limit ), $hygiene_account_count ), '%count', $hygiene_account_count ) )}</span>
    {/if}
</div>

{if $hygiene_accounts}
<div class="a4-table-wrap">
<table class="a4-table" id="hygiene-list">
<caption class="a4-sr-only">{'Accounts'|i18n( 'design/admin/user/hygiene' )}</caption>
<thead><tr>
    <th scope="col"><span class="a4-sr-only">{'Select'|i18n( 'design/admin/user/hygiene' )}</span><input type="checkbox" hidden id="hygiene-select-all" aria-label="{'Select all on this page'|i18n( 'design/admin/user/hygiene' )}" /></th>
    {foreach hash( 'name', 'Name'|i18n( 'design/admin/user/hygiene' ), 'login', 'Login'|i18n( 'design/admin/user/hygiene' ), 'email', 'E-mail'|i18n( 'design/admin/user/hygiene' ),
                   'state', 'Status'|i18n( 'design/admin/user/hygiene' ), 'last_visit', 'Last visit'|i18n( 'design/admin/user/hygiene' ),
                   'login_count', 'Sign-ins'|i18n( 'design/admin/user/hygiene' ), 'groups', 'Groups'|i18n( 'design/admin/user/hygiene' ),
                   'roles', 'Roles'|i18n( 'design/admin/user/hygiene' ) ) as $column => $text}
    {if and( eq( $column, 'email' ), $hygiene_can_see_email|not )}
    {elseif is_set( $hygiene_sort_queries[$column] )}
    <th scope="col"{if eq( $column, $hygiene_filters.sort )} aria-sort="{if eq( $hygiene_filters.order, 'asc' )}ascending{else}descending{/if}"{/if}{if eq( $column, 'login_count' )} class="a4-num"{/if}><a href={concat( $uri, $hygiene_sort_queries[$column] )|ezurl}{if eq( $column, $hygiene_filters.sort )} title="{'Reverse the order'|i18n( 'design/admin/user/hygiene' )}"{/if}>{$text|wash}{if eq( $column, $hygiene_filters.sort )} {if eq( $hygiene_filters.order, 'asc' )}&#8593;{else}&#8595;{/if}{/if}</a></th>
    {else}
    <th scope="col">{$text|wash}</th>
    {/if}
    {/foreach}
</tr></thead>
<tbody>
{foreach $hygiene_accounts as $account}
{def $is_protected = $hygiene_protected_ids|contains( $account.id )}
<tr>
    <td>{if $is_protected}<input type="checkbox" disabled="disabled" id="hygiene-{$account.id}" title="{'Never changed here: the anonymous user, the administrator account or you.'|i18n( 'design/admin/user/hygiene' )}" />{else}<input type="checkbox" name="UserIDArray[]" id="hygiene-{$account.id}" value="{$account.id}" />{/if}</td>
    <td><label for="hygiene-{$account.id}">{if $account.node_id|gt( 0 )}<a href={concat( 'content/view/full/', $account.node_id )|ezurl}>{if $account.name|ne( '' )}{$account.name|wash}{else}{'(no name)'|i18n( 'design/admin/user/hygiene' )}{/if}</a>{elseif $account.name|ne( '' )}{$account.name|wash}{else}<span class="a4-muted">{'(no name)'|i18n( 'design/admin/user/hygiene' )}</span>{/if}</label></td>
    <td class="a4-nowrap"><code>{$account.login|wash}</code></td>
    {if $hygiene_can_see_email}<td>{$account.email|wash}{if $account.shared_email|gt( 1 )} <span class="a4-badge is-warn" title="{'%count accounts use this address.'|i18n( 'design/admin/user/hygiene',, hash( '%count', $account.shared_email ) )}">{'×%count'|i18n( 'design/admin/user/hygiene',, hash( '%count', $account.shared_email ) )}</span>{/if}</td>{/if}
    <td><span class="a4-badges">{foreach $account.states as $state}<span class="a4-badge {$state_levels[$state]}"{if eq( $state, 'locked' )} title="{'%count failed sign-ins; the limit is %max.'|i18n( 'design/admin/user/hygiene',, hash( '%count', $account.failed_attempts, '%max', $hygiene_max_failed_logins ) )}"{/if}>{if array( 'disabled', 'locked' )|contains( $state )}<span class="a4-status is-{$state}" aria-hidden="true"></span>{/if}{$state_names[$state]|wash}</span>{/foreach}</span></td>
    <td class="a4-nowrap">{if $account.last_visit}{$account.last_visit|l10n( 'shortdate' )}<br /><span class="a4-muted">{'%count days ago'|i18n( 'design/admin/user/hygiene',, hash( '%count', $account.idle_days ) )}</span>{else}<span class="a4-muted">{'Never'|i18n( 'design/admin/user/hygiene' )}</span>{/if}</td>
    <td class="a4-num">{$account.login_count}</td>
    <td>{foreach $account.groups as $group_index => $group}{if $group_index|gt( 0 )}, {/if}<a href={concat( 'content/view/full/', $group.node_id )|ezurl}>{$group.name|wash}</a>{/foreach}</td>
    <td>{$account.roles|implode( ', ' )|wash}</td>
</tr>
{undef $is_protected}
{/foreach}
</tbody>
</table>
</div>
{else}
<p class="a4-empty">{'No account matches.'|i18n( 'design/admin/user/hygiene' )}</p>
{/if}

<div class="a4-listfoot">
    <div class="a4-pager">
    {include name=navigator
             uri='design:navigator/google.tpl'
             page_uri=concat( '/', $uri )
             page_uri_suffix=$hygiene_query
             item_count=$hygiene_account_count
             view_parameters=$view_parameters
             item_limit=$hygiene_filters.limit}
    </div>
</div>
</section>

<div class="a4-toolbar is-bottom">
    <div class="a4-actions">
        {if $hygiene_accounts}
        <button class="a4-btn" type="submit" name="DisableButton" value="1">{'Disable the ticked accounts'|i18n( 'design/admin/user/hygiene' )}</button>
        <button class="a4-btn" type="submit" name="EnableButton" value="1">{'Enable the ticked accounts'|i18n( 'design/admin/user/hygiene' )}</button>
        <button class="a4-btn" type="submit" name="UnlockButton" value="1">{'Unlock the ticked accounts'|i18n( 'design/admin/user/hygiene' )}</button>
        {/if}
        <button class="a4-btn" type="submit" name="ExportButton" value="1"{if $hygiene_account_count|eq( 0 )} disabled="disabled"{/if}>{'Export the list as CSV (%count)'|i18n( 'design/admin/user/hygiene',, hash( '%count', $hygiene_account_count ) )}</button>
    </div>
    <p class="a4-meta" id="hygiene-selected-count" aria-live="polite"></p>
</div>
</form>

<script type="text/javascript">
var expHygieneText = {ldelim} selected: '{'%count selected.'|i18n( 'design/admin/user/hygiene' )|wash( javascript )}' {rdelim};
{literal}
(function () {
    var table = document.getElementById( 'hygiene-list' ), all = document.getElementById( 'hygiene-select-all' ), out = document.getElementById( 'hygiene-selected-count' );
    if ( !table ) return;
    function boxes() { return table.querySelectorAll( 'input[name="UserIDArray[]"]' ); }
    function update() {
        var b = boxes(), n = 0, i;
        for ( i = 0; i < b.length; i++ ) if ( b[i].checked ) n++;
        if ( out ) out.textContent = n ? expHygieneText.selected.split( '%count' ).join( n ) : '';
        if ( all ) { all.checked = n > 0 && n === b.length; all.indeterminate = n > 0 && n < b.length; }
    }
    if ( all ) { all.hidden = false; all.addEventListener( 'change', function () { var b = boxes(), i; for ( i = 0; i < b.length; i++ ) b[i].checked = all.checked; update(); } ); }
    table.addEventListener( 'change', update );
})();
{/literal}
</script>
{/if}

</div></div></div>
</div>
{undef $uri $form_uri $state_levels $state_names $reason_names $action_names}
