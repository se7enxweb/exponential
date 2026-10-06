{* Setup > Sessions (setup/session) for a session handler that keeps sessions in the database.

   What a session is and what removing one does, the figures (sessions, signed in users, anonymous sessions, timed
   out sessions), the actions that act on all sessions, a filter with a search, then one card per user (or, for one
   user, per session) with the name, login, e-mail, last activity and idle time, sorting and paging. Removing asks
   first, on a page of its own; your own current session is never removed by a selection.

   Session keys never reach the page: a card carries a short reference (SessionRefArray[]) and the first four
   characters of the key. The view still accepts SessionKeyArray[] from older overrides.

   The same file is in design/admin and design/admin4. Every field and button name of the old page is kept, and the
   page works without javascript. Guide: doc/guides/sessions.md *}
{include uri='design:setup/session_exp_style.tpl'}

{def $base_uri = concat( '/setup/session', cond( $user_id, concat( '/', $user_id ), '' ) )
     $summary = first_set( $session_summary, false() )
     $feedback = first_set( $session_feedback, false() )
     $sort = first_set( $session_sort, 'idle' )
     $order = first_set( $session_order, 'desc' )
     $search = first_set( $session_search, '' )
     $me = first_set( $current_user_id, 0 )
     $session_user = false()}
{if $user_id}{set $session_user = fetch( content, object, hash( 'object_id', $user_id ) )}{/if}

<form name="trashaction" method="post" action={concat( '/setup/session/', cond( $user_id, concat( $user_id, '/' ), '' ), cond( $view_parameters.offset|gt( 0 ), concat( '(offset)/', $view_parameters.offset ), '' ) )|ezurl}>

<div class="context-block exp-sess">

<div class="box-header"><div class="box-ml">
<h1 class="context-title">{if $session_user}{'Sessions of %name'|i18n( 'design/admin/setup/session',, hash( '%name', $session_user.name|wash ) )}{else}{'Session administration'|i18n( 'design/admin/setup/session' )}{/if}</h1>
</div></div>

<div class="box-bc"><div class="box-ml"><div class="box-content">

<p class="exp-intro">{'A session is what keeps a browser signed in: one is opened at the first request that needs it and kept for %lifetime after the last activity. Removing a session signs that browser out; an anonymous visitor only loses a basket or a choice kept for the visit.'|i18n( 'design/admin/setup/session',, hash( '%lifetime', first_set( $session_timeout_text, '' )|wash ) )}</p>

{* Messages *}
{if $sessions_removed}
    {if $gc_sessions_completed}
<div class="exp-feedback is-ok" role="status">
    <p><strong>{'The sessions were successfully removed.'|i18n( 'design/admin/setup/session' )}</strong>
    {if $feedback}
        {if eq( $feedback.type, 'all' )} {'%count sessions were removed; everybody, you included, signs in again.'|i18n( 'design/admin/setup/session',, hash( '%count', $feedback.count ) )}
        {elseif eq( $feedback.type, 'timed_out' )} {'%count timed out sessions were removed.'|i18n( 'design/admin/setup/session',, hash( '%count', $feedback.count ) )}
        {elseif eq( $feedback.type, 'users' )} {'%count sessions of %users users were removed.'|i18n( 'design/admin/setup/session',, hash( '%count', $feedback.count, '%users', $feedback.users ) )}
        {elseif eq( $feedback.type, 'sessions' )} {'%count sessions were removed.'|i18n( 'design/admin/setup/session',, hash( '%count', $feedback.count ) )}
        {/if}
        {if and( is_set( $feedback.kept_own ), $feedback.kept_own )} {'The session you are using now was kept.'|i18n( 'design/admin/setup/session' )}{/if}
    {/if}
    </p>
</div>
    {else}
<div class="exp-feedback is-warn" role="alert">
    <p><strong>{'Not all timed out sessions were successfully removed.'|i18n( 'design/admin/setup/session' )}</strong></p>
    <p>{'The operation was cut short in order to avoid execution timeout.'|i18n( 'design/admin/setup/session' )} {'Your alternatives are to:'|i18n( 'design/admin/setup/session' )}</p>
    <ul>
        <li>{'Repeat the operation several times to complete it.'|i18n( 'design/admin/setup/session' )}</li>
        <li>{'Clear the timed out session data from command-line using: &gt;php bin/php/ezsessiongc.php'|i18n( 'design/admin/setup/session' )}</li>
        <li>{"Install the session cleanup cronjob 'session_gc.php' and run on nightly intervals (see cronjob.ini or doc for how)"|i18n( 'design/admin/setup/session' )}</li>
    </ul>
</div>
    {/if}
{elseif $feedback}
    {if eq( $feedback.type, 'none_selected' )}
<div class="exp-feedback is-warn" role="alert"><p>{'Nothing was selected. Tick the users or sessions to remove first.'|i18n( 'design/admin/setup/session' )}</p></div>
    {elseif eq( $feedback.type, 'own_only' )}
<div class="exp-feedback is-warn" role="alert"><p>{'Only the session you are using now was selected, and it is never removed from this list. Sign out to end it.'|i18n( 'design/admin/setup/session' )}</p></div>
    {/if}
{/if}

{if $summary}
<section aria-labelledby="session-overview-title">
<h2 class="exp-sr" id="session-overview-title">{'Overview'|i18n( 'design/admin/setup/session' )}</h2>
<ul class="exp-figures">
    <li class="exp-figure"><strong>{$summary.total}</strong><span>{'Sessions in all'|i18n( 'design/admin/setup/session' )}</span></li>
    <li class="exp-figure"><strong>{$summary.users_active}</strong><span>{'Users active in the last %time'|i18n( 'design/admin/setup/session',, hash( '%time', first_set( $activity_timeout_text, '' )|wash ) )}</span></li>
    <li class="exp-figure"><strong>{$summary.registered}</strong><span>{'Sessions of signed in users'|i18n( 'design/admin/setup/session' )}</span></li>
    <li class="exp-figure"><strong>{$summary.anonymous}</strong><span>{'Anonymous sessions'|i18n( 'design/admin/setup/session' )}</span></li>
    <li class="exp-figure{if $summary.expired|gt( 0 )} is-attention{/if}"><strong>{$summary.expired}</strong><span>{'Timed out, not yet removed'|i18n( 'design/admin/setup/session' )}</span></li>
</ul>
</section>
{else}
<p class="exp-meta">{'Total number of sessions'|i18n( 'design/admin/setup/session' )}: {$sessions_active}</p>
{/if}

<div class="exp-actionbar">
    <p class="exp-meta">{'Removing timed out sessions is safe: nobody is signed out. Removing all sessions signs out everybody, you included.'|i18n( 'design/admin/setup/session' )}</p>
    <div class="exp-actions">
        <button class="exp-btn" type="submit" name="RemoveTimedOutSessionsButton" value="{'Remove timed out / old sessions'|i18n( 'design/admin/setup/session' )}"{if and( $summary, $summary.expired|eq( 0 ) )} title="{'There are no timed out sessions now.'|i18n( 'design/admin/setup/session' )}"{/if}>{'Remove timed out / old sessions'|i18n( 'design/admin/setup/session' )}</button>
        <button class="exp-btn exp-btn-outline-danger" type="submit" name="RemoveAllSessionsButton" value="{'Remove all sessions'|i18n( 'design/admin/setup/session' )}" title="{'Asks first.'|i18n( 'design/admin/setup/session' )}">{'Remove all sessions'|i18n( 'design/admin/setup/session' )}</button>
    </div>
</div>

<section aria-labelledby="session-filter-title">
<h2 class="exp-sr" id="session-filter-title">{'Filter'|i18n( 'design/admin/setup/session' )}</h2>
{if $user_id}
<div class="exp-actionbar">
    <p class="exp-meta">{if $session_user}{'Displaying sessions for %username'|i18n( 'design/admin/setup/session',, hash( '%username', $session_user.name|wash ) )}{/if} {'Each card is one browser or device this user is signed in with.'|i18n( 'design/admin/setup/session' )}</p>
    <div class="exp-actions">
        <button class="exp-btn" type="submit" name="ShowAllUsersButton" value="{'Sessions for all users'|i18n( 'design/admin/setup/session' )}">{'Sessions for all users'|i18n( 'design/admin/setup/session' )}</button>
    </div>
</div>
{else}
<div class="exp-toolbar">
    <div class="exp-field">
        <label for="session-filter-type">{'Users'|i18n( 'design/admin/setup/session' )}</label>
        <select class="combobox" id="session-filter-type" name="FilterType">
            <option value="everyone"{cond( eq( $filter_type, 'everyone' ), ' selected="selected"', '' )}>{'Everyone'|i18n( 'design/admin/setup/session' )}</option>
            <option value="registered"{cond( eq( $filter_type, 'registered' ), ' selected="selected"', '' )}>{'Registered users'|i18n( 'design/admin/setup/session' )}</option>
            <option value="anonymous"{cond( eq( $filter_type, 'anonymous' ), ' selected="selected"', '' )}>{'Anonymous users'|i18n( 'design/admin/setup/session' )}</option>
        </select>
    </div>
    <div class="exp-field">
        <label for="session-search">{'Find a user'|i18n( 'design/admin/setup/session' )}</label>
        <input type="search" id="session-search" name="SessionSearch" value="{$search|wash}" maxlength="100" autocomplete="off" spellcheck="false" aria-describedby="session-search-help" />
        <span class="exp-help" id="session-search-help">{'Part of a name, login or e-mail address.'|i18n( 'design/admin/setup/session' )}</span>
    </div>
    <div class="exp-field">
        <label><input class="checkbox" type="checkbox" name="InactiveUsersCheck" id="InactiveUsersCheck"{cond( eq( $expiration_filter_type, 'all' ), ' checked="checked"', '' )} value="active" /> {'Include inactive users'|i18n( 'design/admin/setup/session' )}</label>
        <span class="exp-help">{'Also users whose last activity is more than %time ago.'|i18n( 'design/admin/setup/session',, hash( '%time', first_set( $activity_timeout_text, '' )|wash ) )}</span>
        <input type="hidden" name="InactiveUsersCheckExists" value="1" />
    </div>
    <div class="exp-field">
        <div class="exp-actions">
            <button class="exp-btn exp-btn-primary" type="submit" name="ChangeFilterButton" value="{'Update list'|i18n( 'design/admin/setup/session' )}">{'Update list'|i18n( 'design/admin/setup/session' )}</button>
            {if $search|ne( '' )}
            <button class="exp-btn" type="submit" name="ClearSessionSearchButton" value="1">{'Clear search'|i18n( 'design/admin/setup/session' )}</button>
            {/if}
        </div>
    </div>
    {if $search|ne( '' )}
    <p class="exp-filter-count">{'Showing users matching “%search”.'|i18n( 'design/admin/setup/session',, hash( '%search', $search|wash ) )}</p>
    {/if}
</div>
{/if}
</section>

<section class="exp-section" aria-labelledby="session-list-title">
<div class="exp-section-head">
    <h2 class="exp-h2" id="session-list-title">{if $user_id}{'Sessions'|i18n( 'design/admin/setup/session' )}{else}{'Filtered sessions'|i18n( 'design/admin/setup/session' )}{/if} ({$sessions_count})</h2>
    {if $sessions_count|gt( $page_limit )}
    <span class="exp-meta">{'%from to %to of %count'|i18n( 'design/admin/setup/session',, hash( '%from', sum( $view_parameters.offset, 1 ), '%to', min( sum( $view_parameters.offset, $page_limit ), $sessions_count ), '%count', $sessions_count ) )}</span>
    {/if}
    {if $sessions_list}
    <label class="exp-meta exp-js-only" hidden><input type="checkbox" id="session-select-all" /> {'Select all on this page'|i18n( 'design/admin/setup/session' )}</label>
    {/if}
</div>

{if $sessions_list}
<p class="exp-sortbar">
    <span>{'Sort by'|i18n( 'design/admin/setup/session' )}:</span>
    {def $sort_keys = cond( $user_id, array( 'idle' ), array( 'idle', 'name', 'login', 'email', 'count' ) )
         $sort_names = hash( 'idle', 'Last activity'|i18n( 'design/admin/setup/session' ), 'name', 'Full name'|i18n( 'design/admin/setup/session' ), 'login', 'Login'|i18n( 'design/admin/setup/session' ), 'email', 'Email'|i18n( 'design/admin/setup/session' ), 'count', 'Count'|i18n( 'design/admin/setup/session' ) )
         $next = ''}
    {foreach $sort_keys as $key}
        {if eq( $key, $sort )}
            {set $next = cond( eq( $order, 'asc' ), 'desc', 'asc' )}
            <a href={concat( $base_uri, '/(sortby)/', $key, '/(order)/', $next )|ezurl} aria-current="true" class="current" title="{'Sorted; select to reverse the order.'|i18n( 'design/admin/setup/session' )}"><strong>{$sort_names[$key]|wash}</strong> {if eq( $order, 'asc' )}&uarr;{else}&darr;{/if}</a>
        {else}
            <a href={concat( $base_uri, '/(sortby)/', $key )|ezurl}>{$sort_names[$key]|wash}</a>
        {/if}
    {/foreach}
    {undef $sort_keys $sort_names $next}
</p>

<ul class="exp-rows" id="session-list">
{foreach $sessions_list as $session}
    {def $row_id = concat( 'session-', cond( $user_id, $session.ref, $session.user_id ) )
         $is_self = first_set( $session.is_self, eq( $session.user_id, $me ) )
         $is_current = first_set( $session.is_current_session, false() )
         $display_name = first_set( $session.name, '' )}
    {if $display_name|eq( '' )}{set $display_name = $session.login}{/if}
<li class="exp-row{if $is_self} is-self{/if}{if first_set( $session.expired, false() )} is-expired{/if}" id="{$row_id}">
    <div class="exp-row-head">
        <div class="exp-row-title">
            <label class="exp-select" title="{if $is_current}{'The session you are using now cannot be removed here.'|i18n( 'design/admin/setup/session' )}{else}{'Select session for removal.'|i18n( 'design/admin/setup/session' )}{/if}">
            {if $user_id}
                <input type="checkbox" name="SessionRefArray[]" value="{$session.ref|wash}"{if $is_current} disabled="disabled"{/if} aria-label="{'Select the session %hint for removal'|i18n( 'design/admin/setup/session',, hash( '%hint', $session.key_hint ) )|wash}" />
            {else}
                <input type="checkbox" name="UserIDArray[]" value="{$session.user_id}" aria-label="{'Select the sessions of %name for removal'|i18n( 'design/admin/setup/session',, hash( '%name', $display_name ) )|wash}" />
            {/if}
            </label>
            {if $user_id}
            <h3 id="{$row_id}-title">{'Session'|i18n( 'design/admin/setup/session' )} <code>{$session.key_hint|wash}</code></h3>
            {else}
            <h3 id="{$row_id}-title"><a href={concat( 'setup/session/', $session.user_id )|ezurl} title="{'Show the sessions of this user one by one.'|i18n( 'design/admin/setup/session' )}">{$display_name|wash}</a></h3>
            <code class="exp-title-key">{$session.login|wash}</code>
            {/if}
            <ul class="exp-badges">
                {if $is_current}<li class="exp-badge is-info">{'The session you are using now'|i18n( 'design/admin/setup/session' )}</li>
                {elseif $is_self}<li class="exp-badge is-info">{'You'|i18n( 'design/admin/setup/session' )}</li>{/if}
                {if first_set( $session.is_anonymous, false() )}<li class="exp-badge">{'Anonymous visitors'|i18n( 'design/admin/setup/session' )}</li>{/if}
                {if and( $user_id|not, is_set( $session.count ) )}<li class="exp-badge{if $session.count|gt( 1 )} is-info{/if}">{if $session.count|gt( 1 )}{'%count sessions'|i18n( 'design/admin/setup/session',, hash( '%count', $session.count ) )}{else}{'1 session'|i18n( 'design/admin/setup/session' )}{/if}</li>{/if}
                {if first_set( $session.expired, false() )}<li class="exp-badge is-warn">{'Timed out'|i18n( 'design/admin/setup/session' )}</li>{/if}
            </ul>
        </div>
        {if $user_id|not}
        <div class="exp-actions">
            <a class="exp-btn exp-btn-small" href={concat( 'setup/session/', $session.user_id )|ezurl}>{'Sessions'|i18n( 'design/admin/setup/session' )}</a>
            {if first_set( $session.is_anonymous, false() )|not}
                {def $user_object = fetch( content, object, hash( 'object_id', $session.user_id ) )}
                {if and( $user_object, $user_object.main_node_id )}
            <a class="exp-btn exp-btn-small" href={concat( 'content/view/full/', $user_object.main_node_id )|ezurl}>{'User'|i18n( 'design/admin/setup/session' )}</a>
                {/if}
                {undef $user_object}
            {/if}
        </div>
        {/if}
    </div>
    <dl class="exp-facts">
        {if $user_id|not}
        <div><dt>{'Email'|i18n( 'design/admin/setup/session' )}</dt><dd>{if $session.email|ne( '' )}<a href="mailto:{$session.email|wash}">{$session.email|wash}</a>{else}<span class="exp-muted">-</span>{/if}</dd></div>
        {/if}
        <div><dt>{'Last activity'|i18n( 'design/admin/setup/session' )}</dt><dd>
            {if or( $session.idle.minute|lt( 0 ), $session.idle.hour|lt( 0 ) )}{'Time skew detected'|i18n( 'design/admin/setup/session' )}{else}{$session.idle_time|l10n( shortdatetime )}{/if}</dd></div>
        <div><dt>{'Idle time'|i18n( 'design/admin/setup/session' )}</dt><dd>{first_set( $session.idle_text, concat( $session.idle.hour, ':', $session.idle.minute, ':', $session.idle.second ) )|wash}</dd></div>
        <div><dt>{'Ends'|i18n( 'design/admin/setup/session' )}</dt><dd>{$session.expiration_time|l10n( shortdatetime )}</dd></div>
    </dl>
</li>
    {undef $row_id $is_self $is_current $display_name}
{/foreach}
</ul>
{else}
<p class="exp-empty">
{if $search|ne( '' )}{'No user matches “%search”. Clear the search or include inactive users.'|i18n( 'design/admin/setup/session',, hash( '%search', $search|wash ) )}
{elseif $user_id}{'This user has no sessions now.'|i18n( 'design/admin/setup/session' )}
{else}{'There are no sessions matching the selected options.'|i18n( 'design/admin/setup/session' )} {'Choose Everyone or include inactive users to see more.'|i18n( 'design/admin/setup/session' )}{/if}
</p>
{/if}

<div class="exp-listfoot">
    {if and( is_set( $limit_choices ), $sessions_count|gt( 10 ) )}
    <p class="exp-sizes">
        <span>{'Per page'|i18n( 'design/admin/setup/session' )}:</span>
        {foreach $limit_choices as $limit_index => $limit_option}
            {if eq( $limit_index|inc, $limit_choice )}<span class="current">{$limit_option}</span>{else}<a href={concat( '/user/preferences/set/admin_session_list_limit/', $limit_index|inc )|ezurl} title="{'Show %count items per page.'|i18n( 'design/admin/setup/session',, hash( '%count', $limit_option ) )}">{$limit_option}</a>{/if}
        {/foreach}
    </p>
    {/if}
    <div class="exp-pager">
    {include name=navigator
             uri='design:navigator/google.tpl'
             page_uri=$base_uri
             item_count=$sessions_count
             view_parameters=$view_parameters
             item_limit=$page_limit}
    </div>
</div>

<div class="exp-bottombar">
    <p class="exp-meta" id="session-selection-count" aria-live="polite">{if $user_id}{'The ticked sessions are signed out. Asks first.'|i18n( 'design/admin/setup/session' )}{else}{'Every session of the ticked users is signed out; your own current session stays. Asks first.'|i18n( 'design/admin/setup/session' )}{/if}</p>
    <div class="exp-actions">
    {if $sessions_list}
        <button class="exp-btn exp-btn-outline-danger" type="submit" name="RemoveSelectedSessionsButton" value="{'Remove selected'|i18n( 'design/admin/setup/session' )}" title="{'Remove selected sessions.'|i18n( 'design/admin/setup/session' )}">{'Remove selected'|i18n( 'design/admin/setup/session' )}</button>
    {else}
        <button class="exp-btn" type="submit" name="RemoveSelectedSessionsButton" value="{'Remove selected'|i18n( 'design/admin/setup/session' )}" disabled="disabled">{'Remove selected'|i18n( 'design/admin/setup/session' )}</button>
    {/if}
    </div>
</div>
</section>

</div></div></div>
</div>

</form>

{literal}
<script>
(function () {
    var list = document.getElementById('session-list'), all = document.getElementById('session-select-all');
    if (!list || !all) return;
    var boxes = function () { return Array.prototype.filter.call(list.querySelectorAll('input[type=checkbox]'), function (b) { return !b.disabled; }); };
    all.parentNode.hidden = false;
    all.addEventListener('change', function () { boxes().forEach(function (b) { b.checked = all.checked; }); });
    list.addEventListener('change', function () { var b = boxes(); all.checked = b.length > 0 && b.every(function (x) { return x.checked; }); });
})();
</script>
{/literal}

