{* Setup > Sessions (setup/session) when the session handler keeps no session table (ezpSessionHandlerPHP, the
   default: PHP's own session files). Says so and why, lists who signed in recently from ezuservisit (kept by every
   handler) with a time window, a search, sorting and paging, and explains how to switch to session administration.

   The same file is in design/admin and design/admin4; design/standard keeps the one-line notice. Guide:
   doc/guides/sessions.md *}
{include uri='design:setup/session_exp_style.tpl'}

{def $summary = first_set( $visit_summary, false() )
     $window = first_set( $visit_window, 'session' )
     $search = first_set( $session_search, '' )
     $sort = first_set( $session_sort, 'last' )
     $order = first_set( $session_order, 'desc' )
     $me = first_set( $current_user_id, 0 )
     $list = first_set( $visit_list, array() )
     $count = first_set( $visit_count, 0 )
     $limit = first_set( $page_limit, 50 )
     $vp = first_set( $view_parameters, hash( 'offset', 0 ) )}

<form name="sessionvisits" method="post" action={'/setup/session'|ezurl}>

<div class="context-block exp-sess">

<div class="box-header"><div class="box-ml">
<h1 class="context-title">{'Session administration'|i18n( 'design/admin/setup/session' )}</h1>
</div></div>

<div class="box-bc"><div class="box-ml"><div class="box-content">

<div class="exp-feedback is-info" role="status">
    <p><strong>{'Your current session handler does not support session administration.'|i18n( 'design/standard/setup/session' )}</strong></p>
    <p>{'Sessions are kept by %handler (PHP session storage: %storage), not in a table this page can read, so they cannot be counted or removed one by one here. Each session ends by itself %lifetime after its last use.'|i18n( 'design/admin/setup/session',, hash( '%handler', concat( '<code>', first_set( $session_handler, '' )|wash, '</code>' ), '%storage', concat( '<code>', first_set( $php_save_handler, '' )|wash, '</code>' ), '%lifetime', first_set( $session_timeout_text, '' )|wash ) )}</p>
</div>

{if $summary}
<section aria-labelledby="visit-overview-title">
<h2 class="exp-sr" id="visit-overview-title">{'Overview'|i18n( 'design/admin/setup/session' )}</h2>
<ul class="exp-figures">
    <li class="exp-figure"><strong>{$summary.hour}</strong><span>{'Signed in within the last %time'|i18n( 'design/admin/setup/session',, hash( '%time', first_set( $activity_timeout_text, '' )|wash ) )}</span></li>
    <li class="exp-figure"><strong>{$summary.day}</strong><span>{'Signed in within the last day'|i18n( 'design/admin/setup/session' )}</span></li>
    <li class="exp-figure"><strong>{$summary.session}</strong><span>{'Signed in within the session lifetime (%time)'|i18n( 'design/admin/setup/session',, hash( '%time', first_set( $session_timeout_text, '' )|wash ) )}</span></li>
</ul>
</section>
{/if}

<section aria-labelledby="visit-filter-title">
<h2 class="exp-sr" id="visit-filter-title">{'Filter'|i18n( 'design/admin/setup/session' )}</h2>
<div class="exp-toolbar">
    <fieldset class="exp-field exp-field-wide">
        <legend>{'Signed in within'|i18n( 'design/admin/setup/session' )}</legend>
        <div class="exp-chips">
            <label class="exp-chip"><input type="radio" name="VisitWindow" value="hour"{if eq( $window, 'hour' )} checked="checked"{/if} /><span>{'the last %time'|i18n( 'design/admin/setup/session',, hash( '%time', first_set( $activity_timeout_text, '' )|wash ) )}</span></label>
            <label class="exp-chip"><input type="radio" name="VisitWindow" value="day"{if eq( $window, 'day' )} checked="checked"{/if} /><span>{'the last day'|i18n( 'design/admin/setup/session' )}</span></label>
            <label class="exp-chip"><input type="radio" name="VisitWindow" value="session"{if eq( $window, 'session' )} checked="checked"{/if} /><span>{'the session lifetime'|i18n( 'design/admin/setup/session' )}</span></label>
        </div>
    </fieldset>
    <div class="exp-field">
        <label for="session-search">{'Find a user'|i18n( 'design/admin/setup/session' )}</label>
        <input type="search" id="session-search" name="SessionSearch" value="{$search|wash}" maxlength="100" autocomplete="off" spellcheck="false" aria-describedby="session-search-help" />
        <span class="exp-help" id="session-search-help">{'Part of a name, login or e-mail address.'|i18n( 'design/admin/setup/session' )}</span>
    </div>
    <div class="exp-field">
        <div class="exp-actions">
            <button class="exp-btn exp-btn-primary" type="submit" name="ChangeFilterButton" value="{'Update list'|i18n( 'design/admin/setup/session' )}">{'Update list'|i18n( 'design/admin/setup/session' )}</button>
            {if $search|ne( '' )}<button class="exp-btn" type="submit" name="ClearSessionSearchButton" value="1">{'Clear search'|i18n( 'design/admin/setup/session' )}</button>{/if}
        </div>
    </div>
</div>
</section>

<section class="exp-section" aria-labelledby="visit-list-title">
<div class="exp-section-head">
    <h2 class="exp-h2" id="visit-list-title">{'Recently signed in'|i18n( 'design/admin/setup/session' )} ({$count})</h2>
    {if $count|gt( $limit )}
    <span class="exp-meta">{'%from to %to of %count'|i18n( 'design/admin/setup/session',, hash( '%from', sum( $vp.offset, 1 ), '%to', min( sum( $vp.offset, $limit ), $count ), '%count', $count ) )}</span>
    {/if}
    <p>{'The last sign-in of each user, from the visit records every session handler keeps. A user listed here may still have an open session; one who signed out or whose browser forgot the session has none.'|i18n( 'design/admin/setup/session' )}</p>
</div>

{if $list}
<p class="exp-sortbar">
    <span>{'Sort by'|i18n( 'design/admin/setup/session' )}:</span>
    {def $sort_names = hash( 'last', 'Last sign-in'|i18n( 'design/admin/setup/session' ), 'name', 'Full name'|i18n( 'design/admin/setup/session' ), 'login', 'Login'|i18n( 'design/admin/setup/session' ), 'email', 'Email'|i18n( 'design/admin/setup/session' ), 'logins', 'Sign-ins'|i18n( 'design/admin/setup/session' ) )
         $next = ''}
    {foreach array( 'last', 'name', 'login', 'email', 'logins' ) as $key}
        {if eq( $key, $sort )}
            {set $next = cond( eq( $order, 'asc' ), 'desc', 'asc' )}
            <a href={concat( '/setup/session/(sortby)/', $key, '/(order)/', $next )|ezurl} aria-current="true" class="current" title="{'Sorted; select to reverse the order.'|i18n( 'design/admin/setup/session' )}"><strong>{$sort_names[$key]|wash}</strong> {if eq( $order, 'asc' )}&uarr;{else}&darr;{/if}</a>
        {else}
            <a href={concat( '/setup/session/(sortby)/', $key )|ezurl}>{$sort_names[$key]|wash}</a>
        {/if}
    {/foreach}
    {undef $sort_names $next}
</p>

<ul class="exp-rows" id="visit-list">
{foreach $list as $visit}
    {def $user_object = fetch( content, object, hash( 'object_id', $visit.user_id ) )}
<li class="exp-row{if eq( $visit.user_id, $me )} is-self{/if}">
    <div class="exp-row-head">
        <div class="exp-row-title">
            <h3>{if and( $user_object, $user_object.main_node_id )}<a href={concat( 'content/view/full/', $user_object.main_node_id )|ezurl}>{$visit.name|wash}</a>{else}{$visit.name|wash}{/if}</h3>
            <code class="exp-title-key">{$visit.login|wash}</code>
            {if eq( $visit.user_id, $me )}<ul class="exp-badges"><li class="exp-badge is-info">{'You'|i18n( 'design/admin/setup/session' )}</li></ul>{/if}
        </div>
    </div>
    <dl class="exp-facts">
        <div><dt>{'Email'|i18n( 'design/admin/setup/session' )}</dt><dd>{if $visit.email|ne( '' )}<a href="mailto:{$visit.email|wash}">{$visit.email|wash}</a>{else}<span class="exp-muted">-</span>{/if}</dd></div>
        <div><dt>{'Last sign-in'|i18n( 'design/admin/setup/session' )}</dt><dd>{$visit.current_visit_timestamp|l10n( shortdatetime )}<br /><span class="exp-muted">{'%time ago'|i18n( 'design/admin/setup/session',, hash( '%time', $visit.idle_text|wash ) )}</span></dd></div>
        <div><dt>{'Sign-in before'|i18n( 'design/admin/setup/session' )}</dt><dd>{if $visit.last_visit_timestamp|gt( 0 )}{$visit.last_visit_timestamp|l10n( shortdatetime )}{else}<span class="exp-muted">-</span>{/if}</dd></div>
        <div><dt>{'Sign-ins'|i18n( 'design/admin/setup/session' )}</dt><dd>{$visit.login_count}</dd></div>
    </dl>
</li>
    {undef $user_object}
{/foreach}
</ul>
{else}
<p class="exp-empty">{if $search|ne( '' )}{'No user matches “%search”. Clear the search or choose a longer time.'|i18n( 'design/admin/setup/session',, hash( '%search', $search|wash ) )}{else}{'Nobody signed in within this time. Choose a longer time to see more.'|i18n( 'design/admin/setup/session' )}{/if}</p>
{/if}

<div class="exp-listfoot">
    {if and( is_set( $limit_choices ), $count|gt( 10 ) )}
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
             page_uri='/setup/session'
             item_count=$count
             view_parameters=$vp
             item_limit=$limit}
    </div>
</div>
</section>

<section class="exp-section" aria-labelledby="session-switch-title">
<details class="exp-panel">
<summary><h2 class="exp-h2" id="session-switch-title">{'How to administer sessions on this page'|i18n( 'design/admin/setup/session' )}</h2></summary>
<ol class="exp-steps">
    <li>{'Keep sessions in the database: in settings/override/site.ini.append.php set'|i18n( 'design/admin/setup/session' )}
<pre class="exp-code">[Session]
Handler=ezpSessionHandlerDB</pre></li>
    <li>{'Clear the INI cache and reload the PHP workers (or deploy). Everybody signs in again once, because the sessions kept by PHP are not carried over.'|i18n( 'design/admin/setup/session' )}</li>
    <li>{'This page then lists every session with its user and last activity, and removes the sessions of chosen users. Set ForceStart=enabled only if anonymous visitors should be counted too: it opens a session for every visitor.'|i18n( 'design/admin/setup/session' )}</li>
</ol>
<p class="exp-help">{'Timed out sessions of the PHP handler are removed by PHP itself (session.gc_maxlifetime), not by this page or the session_gc cronjob part.'|i18n( 'design/admin/setup/session' )}</p>
</details>
</section>

</div></div></div>
</div>

</form>
{undef $summary $window $search $sort $order $me $list $count $limit $vp}
