{* The cronjobs console.

   One page for running cronjobs and seeing how they are doing: what is running now, a short overview, the
   controls, then one card per cronjob part with its schedule, its next and last run, the command that runs it
   from a shell and its scripts with what each one does. Below the parts: scripts no part names, the recent runs,
   and the crontab.

   The same file is in design/admin and design/admin4, so every administration design resolves it. It uses the
   shared admin components (components.css) and stylesheets/modules/setup.css (.a4-setup-cron), in light and dark;
   everything works without javascript: every run, stop and clear button submits the form, the
   folding sections are <details>. The script below adds running in place, following the output as it is
   written, the filters and the copy buttons. *}


<div class="context-block a4-page a4-setup-cron">

<div class="box-header"><div class="box-ml">
<h1 class="context-title">{'Cronjobs'|i18n( 'design/admin/setup/cronjobs' )}</h1>
</div></div>

<div class="box-bc"><div class="box-ml"><div class="box-content">

<p class="a4-intro">{'Runs a cronjob part now, without waiting for the scheduler. The job is started as a separate process and keeps running after this page is closed, so nothing is lost if the browser goes away.'|i18n( 'design/admin/setup/cronjobs' )}</p>

{foreach $cronjob_feedback as $cronjob_message}
<div class="a4-notice {if $cronjob_message.ok}is-ok{else}is-bad{/if}" role="{if $cronjob_message.ok}status{else}alert{/if}">{$cronjob_message.message|wash}</div>
{/foreach}

{* What an action says when the console runs it in place. Empty and hidden until there is something to put in it. *}
<div class="a4-notice" id="cronjob-feedback" role="status" aria-live="polite" hidden></div>

{if $cronjob_php_binary|eq('')}
<div class="a4-notice is-bad" role="alert">
    {'No php command line binary could be found, so nothing can be launched from here. Set cronjob.ini [AdminSettings] PhpCliPath to its full path.'|i18n( 'design/admin/setup/cronjobs' )}
</div>
{/if}

{* A pause of every part is shown above everything else: nothing the page offers runs while it holds. *}
{if and( $cronjob_pause_enabled, $cronjob_pause_all.paused )}
<div class="a4-notice is-bad" role="alert" id="cronjob-paused-all">
    {if $cronjob_pause_all.by}{'All cronjob parts are paused since %since by %user. Nothing runs, from here or from the crontab, until they are resumed.'|i18n( 'design/admin/setup/cronjobs',, hash( '%since', $cronjob_pause_all.since|l10n( shortdatetime ), '%user', $cronjob_pause_all.by.login|wash ) )}{else}{'All cronjob parts are paused since %since. Nothing runs, from here or from the crontab, until they are resumed.'|i18n( 'design/admin/setup/cronjobs',, hash( '%since', $cronjob_pause_all.since|l10n( shortdatetime ) ) )}{/if}
</div>
{/if}

<form method="post" action={'setup/cronjobs'|ezurl}>

{* Everything that changes when a job starts or ends is addressable, so the console can bring the page up to
   date without fetching it again. *}
<section class="a4-toolbar" aria-labelledby="cronjob-status-title">
    <h2 class="a4-sr-only" id="cronjob-status-title">{'Now'|i18n( 'design/admin/setup/cronjobs' )}</h2>
    <div class="a4-row" id="cronjob-status" aria-live="polite">
        <span class="a4-status{if $cronjob_status.running} is-running{/if}" id="cronjob-pill">
            <span class="a4-dot" aria-hidden="true"></span>
            <span class="a4-setup-pill-text">{if $cronjob_status.running}{'Running: %part'|i18n( 'design/admin/setup/cronjobs',, hash( '%part', $cronjob_status.part|wash ) )}{else}{'Idle'|i18n( 'design/admin/setup/cronjobs' )}{/if}</span>
        </span>
        {if $cronjob_status.running}
        <span class="a4-meta a4-setup-running-meta">{'Site: %siteaccess'|i18n( 'design/admin/setup/cronjobs',, hash( '%siteaccess', $cronjob_status.siteaccess|wash ) )}</span>
        <span class="a4-meta a4-setup-running-meta">{'Process: %pid'|i18n( 'design/admin/setup/cronjobs',, hash( '%pid', $cronjob_status.pid ) )}</span>
        <span class="a4-meta a4-setup-running-meta" id="cronjob-elapsed">{'Elapsed: %elapsed'|i18n( 'design/admin/setup/cronjobs',, hash( '%elapsed', concat( $cronjob_status.elapsed, 's' ) ) )}</span>
        {/if}
    </div>
    <div class="a4-actions">
        <button type="submit" class="a4-btn" id="cronjob-stop" name="StopCronjobButton" value="1"{if $cronjob_status.running|not} disabled="disabled"{/if}>{'Stop running job'|i18n( 'design/admin/setup/cronjobs' )}</button>
        <button type="submit" class="a4-btn" id="cronjob-clear" name="ClearCronjobLogButton" value="1">{'Clear logs'|i18n( 'design/admin/setup/cronjobs' )}</button>
        {if $cronjob_pause_enabled}
        {if $cronjob_pause_all.paused}
        <button type="submit" class="a4-btn" id="cronjob-resume-all" name="ResumeCronjobButton" value="all">{'Resume all parts'|i18n( 'design/admin/setup/cronjobs' )}</button>
        {else}
        <button type="submit" class="a4-btn" id="cronjob-pause-all" name="PauseCronjobButton" value="all" title="{'Leave every cronjob part out until resumed, whoever starts it'|i18n( 'design/admin/setup/cronjobs' )|wash}">{'Pause all parts'|i18n( 'design/admin/setup/cronjobs' )}</button>
        {/if}
        {/if}
    </div>
</section>
<p class="a4-setup-paths">
    <span>{'Log'|i18n( 'design/admin/setup/cronjobs' )}: <code>{$cronjob_log_file|wash}</code></span>
    {if $cronjob_php_binary|ne('')}<span>php: <code>{$cronjob_php_binary|wash}</code></span>{/if}
</p>

{* The overview: how many parts there are, how many the crontab runs, how many need a look, and the last run. *}
<section aria-labelledby="cronjob-overview-title">
<h2 class="a4-sr-only" id="cronjob-overview-title">{'Overview'|i18n( 'design/admin/setup/cronjobs' )}</h2>
<ul class="a4-figures">
    <li class="a4-figure"><strong>{$cronjob_summary.parts}</strong><span>{'Cronjob parts, %count scripts'|i18n( 'design/admin/setup/cronjobs',, hash( '%count', $cronjob_summary.scripts ) )}</span></li>
    <li class="a4-figure"><strong>{$cronjob_summary.scheduled}</strong><span>{'Scheduled in the crontab'|i18n( 'design/admin/setup/cronjobs' )}</span></li>
    <li class="a4-figure"><strong>{$cronjob_summary.unscheduled}</strong><span>{'Not scheduled'|i18n( 'design/admin/setup/cronjobs' )}</span></li>
    <li class="a4-figure{if $cronjob_summary.attention|gt(0)} is-attention{/if}"><strong>{$cronjob_summary.attention}</strong><span>{'Need attention'|i18n( 'design/admin/setup/cronjobs' )}</span></li>
    <li class="a4-figure is-last">
        {if $cronjob_last_run}
        <strong>{$cronjob_last_run.label|wash}</strong>
        <span>{'Last run %time'|i18n( 'design/admin/setup/cronjobs',, hash( '%time', $cronjob_last_run.started|l10n( shortdatetime ) ) )}
            &middot; {if $cronjob_last_run.running}{'running'|i18n( 'design/admin/setup/cronjobs' )}{elseif $cronjob_last_run.errors|gt(0)}{'%count issues'|i18n( 'design/admin/setup/cronjobs',, hash( '%count', $cronjob_last_run.errors ) )}{else}{'OK'|i18n( 'design/admin/setup/cronjobs' )}{/if}</span>
        {else}
        <strong>&mdash;</strong>
        <span>{'Nothing has been run from here yet.'|i18n( 'design/admin/setup/cronjobs' )}</span>
        {/if}
    </li>
</ul>
</section>

{* The controls. The site applies to every run button on the page; the search, the state and the part narrow
   the list; the part also decides what the Run beside it runs. Folded by default, so the page opens on the
   overview and the list; the summary still names the site the run buttons use. *}
<details class="a4-fold" id="cronjob-controls">
<summary>
    <h2 class="a4-h2" id="cronjob-controls-title">{'Run and filter'|i18n( 'design/admin/setup/cronjobs' )}</h2>
    <span class="a4-meta">{'Site %siteaccess · find a part or script · run every part'|i18n( 'design/admin/setup/cronjobs',, hash( '%siteaccess', $cronjob_default_siteaccess|wash ) )}</span>
</summary>
<div class="a4-fold-body">
<div class="a4-filterbar">
    <div class="a4-field">
        <label for="cronjob-siteaccess">{'Run for site'|i18n( 'design/admin/setup/cronjobs' )}</label>
        {* A cronjob acts on content, and which content depends on the siteaccess it runs under, so this is asked
           rather than assumed. *}
        <select id="cronjob-siteaccess" name="CronjobSiteAccess">
        {foreach $cronjob_siteaccess_list as $cronjob_siteaccess}
            <option value="{$cronjob_siteaccess|wash}"{if eq( $cronjob_siteaccess, $cronjob_default_siteaccess )} selected="selected"{/if}>{$cronjob_siteaccess|wash}</option>
        {/foreach}
        </select>
    </div>
    <div class="a4-field a4-setup-js-only" hidden>
        <label for="cronjob-search">{'Find a part or script'|i18n( 'design/admin/setup/cronjobs' )}</label>
        <input type="search" id="cronjob-search" autocomplete="off" spellcheck="false" aria-controls="{if ezpreference( 'admin_cronjobs_view' )|eq( 'cards' )}cronjob-list{else}cronjob-table{/if}" aria-describedby="cronjob-filter-count" />
    </div>
    <fieldset class="a4-field a4-field-wide a4-setup-js-only" hidden>
        <legend>{'Show'|i18n( 'design/admin/setup/cronjobs' )}</legend>
        <div class="a4-chips">
            <label class="a4-chip"><input type="radio" name="CronjobFilterState" value="" checked="checked" /><span>{'All'|i18n( 'design/admin/setup/cronjobs' )}</span></label>
            <label class="a4-chip"><input type="radio" name="CronjobFilterState" value="scheduled" /><span>{'Scheduled'|i18n( 'design/admin/setup/cronjobs' )}</span></label>
            <label class="a4-chip"><input type="radio" name="CronjobFilterState" value="unscheduled" /><span>{'Not scheduled'|i18n( 'design/admin/setup/cronjobs' )}</span></label>
            <label class="a4-chip"><input type="radio" name="CronjobFilterState" value="attention" /><span>{'Need attention'|i18n( 'design/admin/setup/cronjobs' )}</span></label>
        </div>
    </fieldset>
    <div class="a4-setup-runbox a4-setup-js-only" hidden>
        <div class="a4-field">
            <label for="cronjob-part-filter">{'Cronjob part'|i18n( 'design/admin/setup/cronjobs' )}</label>
            {* Every part, not only the ones on this page: it is also how a part is chosen to run, and running "all
               parts" runs every one of them that may be run. *}
            <select id="cronjob-part-filter" aria-describedby="cronjob-run-hint">
                <option value="">{'All parts'|i18n( 'design/admin/setup/cronjobs' )}</option>
            {foreach $cronjob_parts as $cronjob_option}
                <option value="{$cronjob_option.name|wash}" data-blocked="{if or( $cronjob_option.forbidden, $cronjob_option.missing|ge( $cronjob_option.scripts|count ), $cronjob_php_binary|eq(''), and( $cronjob_pause_enabled, or( $cronjob_option.pause.paused, $cronjob_pause_all.paused ) ) )}1{else}0{/if}">{$cronjob_option.label|wash}</option>
            {/foreach}
            </select>
        </div>
        <button type="button" class="a4-btn a4-btn-primary" id="cronjob-run-selected" data-blocked="{if and( $cronjob_pause_enabled, $cronjob_pause_all.paused )}1{else}0{/if}"{if or( $cronjob_status.running, and( $cronjob_pause_enabled, $cronjob_pause_all.paused ) )} disabled="disabled"{/if}><svg width="14" height="14" viewBox="0 0 16 16" aria-hidden="true" focusable="false"><path fill="currentColor" d="M4 2.5v11l9-5.5z"/></svg>{'Run'|i18n( 'design/admin/setup/cronjobs' )}</button>
        <span class="a4-meta" id="cronjob-run-hint">{'Runs every part, one after another.'|i18n( 'design/admin/setup/cronjobs' )}</span>
    </div>
    <p class="a4-filter-count a4-setup-js-only" id="cronjob-filter-count" aria-live="polite" hidden></p>
</div>
</div>
</details>

{* The output: what the running job, or the last one, wrote. Open while a job runs; the console opens it when it
   starts one. *}
<details class="a4-fold a4-setup-output" id="cronjob-output"{if $cronjob_status.running} open="open"{/if}>
    <summary>
        <h2 class="a4-h2">{'Output'|i18n( 'design/admin/setup/cronjobs' )}</h2>
        <span class="a4-meta" id="cronjob-stream-status">{'The last run, from %file'|i18n( 'design/admin/setup/cronjobs',, hash( '%file', $cronjob_log_file|wash ) )}</span>
    </summary>
    <div class="a4-fold-body">
        <pre class="a4-setup-console" id="cronjob-console" tabindex="0" aria-label="{'Output'|i18n( 'design/admin/setup/cronjobs' )}" data-empty="{'No output yet.'|i18n( 'design/admin/setup/cronjobs' )}">{$cronjob_log|wash}{if $cronjob_errors|ne('')}
{$cronjob_errors|wash}{/if}</pre>
    </div>
</details>

{* One card per cronjob part, or in the compact view one table row. A part is run whole from its card or row, one
   script from its row on the card, the whole list in turn from the Run above. The view is a preference of the user
   (admin_cronjobs_view: compact, the default, or cards), so it holds across visits and needs no javascript. *}
{def $cronjob_view = cond( ezpreference( 'admin_cronjobs_view' )|eq( 'cards' ), 'cards', 'compact' )}
<section class="a4-section" aria-labelledby="cronjob-parts-title">
<div class="a4-section-head">
    <h2 class="a4-h2" id="cronjob-parts-title">{'Cronjob parts'|i18n( 'design/admin/setup/cronjobs' )}</h2>
    {* The view is written by a POST to user/preferences/set, as every admin4 preference switch (no GET link writes
       one): the two buttons send this form there instead of to setup/cronjobs, with its form token, and the
       preference view returns to this page. The current view is the pressed button. *}
    <span class="a4-setup-view">
    <input type="hidden" name="Key" value="admin_cronjobs_view" />
    <input type="hidden" name="RedirectURIAfterSet" value="setup/cronjobs" />
    <span class="a4-tabs is-segmented" role="group" aria-label="{'View'|i18n( 'design/admin/setup/cronjobs' )|wash}">
        <button type="submit" formaction={'/user/preferences/set'|ezurl} name="Value" value="compact" aria-pressed="{if $cronjob_view|eq('compact')}true{else}false{/if}">{'Compact'|i18n( 'design/admin/setup/cronjobs' )}</button>
        <button type="submit" formaction={'/user/preferences/set'|ezurl} name="Value" value="cards" aria-pressed="{if $cronjob_view|eq('cards')}true{else}false{/if}">{'Detailed'|i18n( 'design/admin/setup/cronjobs' )}</button>
    </span>
    </span>
    {if and( $cronjob_view|eq('cards'), $cronjob_parts_count|gt( $limit ) )}
    <span class="a4-meta">{'%from to %to of %count'|i18n( 'design/admin/setup/cronjobs',, hash( '%from', sum( $view_parameters.offset, 1 ), '%to', min( sum( $view_parameters.offset, $limit ), $cronjob_parts_count ), '%count', $cronjob_parts_count ) )}</span>
    {/if}
</div>

{if $cronjob_parts|count|eq(0)}
<p class="a4-empty">{'No cronjob parts are defined. Parts are the [CronjobSettings] and [CronjobPart-...] groups of cronjob.ini.'|i18n( 'design/admin/setup/cronjobs' )}</p>
{elseif $cronjob_view|eq('compact')}
{* The compact view: one row per part, every part on one page. The same row attributes as the cards, so the search,
   the filter and the console treat a row like a card. *}
<div class="a4-table-wrap a4-setup-compact" role="region" aria-labelledby="cronjob-parts-title" tabindex="0">
<table class="a4-table" id="cronjob-table">
<thead>
<tr>
    <th scope="col">{'Part'|i18n( 'design/admin/setup/cronjobs' )}</th>
    <th scope="col">{'Status'|i18n( 'design/admin/setup/cronjobs' )}</th>
    <th scope="col">{'Schedule'|i18n( 'design/admin/setup/cronjobs' )}</th>
    <th scope="col">{'Last run'|i18n( 'design/admin/setup/cronjobs' )}</th>
    <th scope="col"><span class="a4-sr-only">{'Actions'|i18n( 'design/admin/setup/cronjobs' )}</span></th>
</tr>
</thead>
<tbody>
{foreach $cronjob_parts as $cronjob_part}
    {def $cronjob_paused = and( $cronjob_pause_enabled, or( $cronjob_part.pause.paused, $cronjob_pause_all.paused ) )
         $cronjob_blocked = or( $cronjob_part.forbidden,
                                $cronjob_part.missing|ge( $cronjob_part.scripts|count ),
                                $cronjob_php_binary|eq(''),
                                $cronjob_paused )
         $cronjob_runnable = and( $cronjob_blocked|not, $cronjob_status.running|not )}
<tr class="a4-setup-part-row{if and( $cronjob_status.running, eq( $cronjob_status.part, $cronjob_part.name ) )} is-current{/if}{if $cronjob_part.attention} is-attention{/if}{if $cronjob_paused} is-paused{/if}"
    id="cronjob-part-{$cronjob_part.name|wash}" data-part="{$cronjob_part.name|wash}"
    data-state="{if $cronjob_part.scheduled}scheduled{else}unscheduled{/if}" data-attention="{if $cronjob_part.attention}1{else}0{/if}"
    data-search="{$cronjob_part.search|wash}">
    {* The part, the key as its title, and its scripts folded below it. Without javascript the list opens in this cell;
       with it, in the row below the part across the whole table, so several parts can be open at once. *}
    <th scope="row" title="{$cronjob_part.name|wash}"><span class="a4-setup-compact-part">{$cronjob_part.label|wash}</span>
        <details class="a4-setup-compact-scripts"><summary>{if $cronjob_part.scripts|count|eq(1)}{'1 script'|i18n( 'design/admin/setup/cronjobs' )}{else}{'%count scripts'|i18n( 'design/admin/setup/cronjobs',, hash( '%count', $cronjob_part.scripts|count ) )}{/if}</summary>
            <ul class="a4-setup-compact-inline">{foreach $cronjob_part.scripts as $cronjob_script}
                <li><span class="a4-setup-compact-origin">{if $cronjob_script.origin|eq('kernel')}<span class="a4-badge is-info">{'Kernel'|i18n( 'design/admin/setup/cronjobs' )}</span>{elseif $cronjob_script.origin|ne('')}<span class="a4-badge">{$cronjob_script.origin|wash}</span>{/if}</span><span class="a4-setup-compact-file">{if $cronjob_script.path|ne(false())}<span class="a4-setup-compact-path">{$cronjob_script.directory|wash}/</span>{/if}<code>{$cronjob_script.name|wash}</code>{if $cronjob_script.path|eq(false())} <span class="a4-badge is-bad">{'missing'|i18n( 'design/admin/setup/cronjobs' )}</span>{/if}</span><span class="a4-setup-compact-desc">{$cronjob_script.description|wash}{if $cronjob_script.documentation|ne('')} <a class="a4-setup-doc-link" href="{$cronjob_script.documentation|wash}" target="_blank" rel="noopener noreferrer" aria-label="{'Documentation of %script (opens in a new window)'|i18n( 'design/admin/setup/cronjobs',, hash( '%script', $cronjob_script.name ) )|wash}">{'Docs'|i18n( 'design/admin/setup/cronjobs' )}</a>{/if}</span></li>
            {/foreach}</ul></details></th>
    <td><ul class="a4-badges">
        {if $cronjob_part.scheduled}<li class="a4-badge is-ok">{'Scheduled'|i18n( 'design/admin/setup/cronjobs' )}</li>{else}<li class="a4-badge is-warn">{'Not scheduled'|i18n( 'design/admin/setup/cronjobs' )}</li>{/if}
        {if $cronjob_part.forbidden}<li class="a4-badge">{'Blocked'|i18n( 'design/admin/setup/cronjobs' )}</li>{elseif $cronjob_part.missing|gt(0)}<li class="a4-badge is-bad">{'%count missing'|i18n( 'design/admin/setup/cronjobs',, hash( '%count', $cronjob_part.missing ) )}</li>{/if}
        {if and( $cronjob_pause_enabled, $cronjob_part.pause.paused )}<li class="a4-badge is-bad" title="{if $cronjob_part.pause.by}{'Since %since by %user'|i18n( 'design/admin/setup/cronjobs',, hash( '%since', $cronjob_part.pause.since|l10n( shortdatetime ), '%user', $cronjob_part.pause.by.login|wash ) )}{else}{'Since %since'|i18n( 'design/admin/setup/cronjobs',, hash( '%since', $cronjob_part.pause.since|l10n( shortdatetime ) ) )}{/if}">{'Paused'|i18n( 'design/admin/setup/cronjobs' )}</li>{/if}
    </ul></td>
    {* The schedule and, below it, the next run: one column, so the table fits beside the menus. *}
    <td>{if $cronjob_part.schedule_active|ne('')}<span title="{if $cronjob_part.scheduled|not}{'Suggested schedule'|i18n( 'design/admin/setup/cronjobs' )|wash}: {/if}{$cronjob_part.schedule_active|wash}">{$cronjob_part.schedule_text|wash}</span>{else}&mdash;{/if}
        <span class="a4-setup-compact-sub">{if $cronjob_part.next_run|gt(0)}{'Next run: %time'|i18n( 'design/admin/setup/cronjobs',, hash( '%time', $cronjob_part.next_run|l10n( shortdatetime ) ) )}{else}{'Only when run by hand'|i18n( 'design/admin/setup/cronjobs' )}{/if}</span></td>
    <td>{if $cronjob_part.last_run}{$cronjob_part.last_run.started|l10n( shortdatetime )}
        {if $cronjob_part.last_run.running}<span class="a4-badge is-info">{'running'|i18n( 'design/admin/setup/cronjobs' )}</span>{elseif $cronjob_part.last_run.errors|gt(0)}<span class="a4-badge is-bad">{'%count issues'|i18n( 'design/admin/setup/cronjobs',, hash( '%count', $cronjob_part.last_run.errors ) )}</span>{else}<span class="a4-badge is-ok">{'OK'|i18n( 'design/admin/setup/cronjobs' )}</span>{/if}{else}&mdash;{/if}</td>
    <td class="a4-nowrap">
        {if $cronjob_pause_enabled}
        {if $cronjob_part.pause.paused}
        <button type="submit" class="a4-btn a4-btn-small" name="ResumeCronjobButton" value="{$cronjob_part.name|wash}"
                title="{'Resume the %part part'|i18n( 'design/admin/setup/cronjobs',, hash( '%part', $cronjob_part.label ) )|wash}">{'Resume'|i18n( 'design/admin/setup/cronjobs' )}</button>
        {else}
        <button type="submit" class="a4-btn a4-btn-small" name="PauseCronjobButton" value="{$cronjob_part.name|wash}"
                title="{'Leave the %part part out until resumed, whoever starts it'|i18n( 'design/admin/setup/cronjobs',, hash( '%part', $cronjob_part.label ) )|wash}">{'Pause'|i18n( 'design/admin/setup/cronjobs' )}</button>
        {/if}
        {/if}
        <button type="submit" class="a4-btn a4-btn-small a4-setup-run" name="LaunchCronjobButton"
                data-blocked="{if $cronjob_blocked}1{else}0{/if}"
                value="{$cronjob_part.name|wash}"{if $cronjob_runnable|not} disabled="disabled"{/if}
                title="{'Run the whole %part part now'|i18n( 'design/admin/setup/cronjobs',, hash( '%part', $cronjob_part.label ) )|wash}">{'Run'|i18n( 'design/admin/setup/cronjobs' )}</button>
    </td>
</tr>
<tr class="a4-setup-compact-detail" data-part="{$cronjob_part.name|wash}" hidden>
    <td colspan="5"><ul role="list">{foreach $cronjob_part.scripts as $cronjob_script}
                <li role="listitem"><span class="a4-setup-compact-origin">{if $cronjob_script.origin|eq('kernel')}<span class="a4-badge is-info">{'Kernel'|i18n( 'design/admin/setup/cronjobs' )}</span>{elseif $cronjob_script.origin|ne('')}<span class="a4-badge">{$cronjob_script.origin|wash}</span>{/if}</span><span class="a4-setup-compact-file">{if $cronjob_script.path|ne(false())}<span class="a4-setup-compact-path">{$cronjob_script.directory|wash}/</span>{/if}<code>{$cronjob_script.name|wash}</code>{if $cronjob_script.path|eq(false())} <span class="a4-badge is-bad">{'missing'|i18n( 'design/admin/setup/cronjobs' )}</span>{/if}</span><span class="a4-setup-compact-desc">{$cronjob_script.description|wash}{if $cronjob_script.documentation|ne('')} <a class="a4-setup-doc-link" href="{$cronjob_script.documentation|wash}" target="_blank" rel="noopener noreferrer" aria-label="{'Documentation of %script (opens in a new window)'|i18n( 'design/admin/setup/cronjobs',, hash( '%script', $cronjob_script.name ) )|wash}">{'Docs'|i18n( 'design/admin/setup/cronjobs' )}</a>{/if}</span></li>
            {/foreach}</ul></td>
</tr>
    {undef $cronjob_paused $cronjob_blocked $cronjob_runnable}
{/foreach}
</tbody>
</table>
</div>
<p class="a4-empty" id="cronjob-no-match" hidden>{'No cronjob part matches.'|i18n( 'design/admin/setup/cronjobs' )}</p>
{else}
<ul class="a4-cards" id="cronjob-list">
{foreach $cronjob_parts_page as $cronjob_part}
    {* Blocked for good - forbidden, scripts missing, no php - is not the same as blocked because something else
       is running, which stops being true the moment the console sees the job finish. Each button records which
       it is so the console knows what it may re-enable. *}
    {def $cronjob_paused = and( $cronjob_pause_enabled, or( $cronjob_part.pause.paused, $cronjob_pause_all.paused ) )
         $cronjob_blocked = or( $cronjob_part.forbidden,
                                $cronjob_part.missing|ge( $cronjob_part.scripts|count ),
                                $cronjob_php_binary|eq(''),
                                $cronjob_paused )
         $cronjob_runnable = and( $cronjob_blocked|not, $cronjob_status.running|not )
         $cronjob_part_id = concat( 'cronjob-part-', $cronjob_part.name )}
<li class="a4-card a4-setup-part-row{if and( $cronjob_status.running, eq( $cronjob_status.part, $cronjob_part.name ) )} is-current{/if}{if $cronjob_part.attention} is-attention{/if}"
    id="{$cronjob_part_id|wash}" data-part="{$cronjob_part.name|wash}"
    data-state="{if $cronjob_part.scheduled}scheduled{else}unscheduled{/if}" data-attention="{if $cronjob_part.attention}1{else}0{/if}"
    data-search="{$cronjob_part.search|wash}">
    <div class="a4-card-head">
        <div class="a4-card-title">
            <h3 id="{$cronjob_part_id|wash}-title">{$cronjob_part.label|wash}</h3>
            <code class="a4-muted">{$cronjob_part.name|wash}</code>
            <ul class="a4-badges">
                {* What the crontab actually says about this part, not what it could say. *}
                {if $cronjob_part.scheduled}
                <li class="a4-badge is-ok" title="{'A crontab entry for this installation runs this part'|i18n( 'design/admin/setup/cronjobs' )}">{'Scheduled'|i18n( 'design/admin/setup/cronjobs' )}</li>
                {else}
                <li class="a4-badge is-warn" title="{'Nothing in the crontab runs this part'|i18n( 'design/admin/setup/cronjobs' )}">{'Not scheduled'|i18n( 'design/admin/setup/cronjobs' )}</li>
                {/if}
                {if $cronjob_part.forbidden}
                <li class="a4-badge" title="{'Blocked by cronjob.ini ForbiddenParts'|i18n( 'design/admin/setup/cronjobs' )}">{'Blocked'|i18n( 'design/admin/setup/cronjobs' )}</li>
                {elseif $cronjob_part.missing|gt(0)}
                <li class="a4-badge is-bad">{'%count missing'|i18n( 'design/admin/setup/cronjobs',, hash( '%count', $cronjob_part.missing ) )}</li>
                {else}
                <li class="a4-badge is-ok">{'Activated'|i18n( 'design/admin/setup/cronjobs' )}</li>
                {/if}
                {if and( $cronjob_pause_enabled, $cronjob_part.pause.paused )}
                <li class="a4-badge is-bad" title="{'Left out by runcronjobs.php until resumed'|i18n( 'design/admin/setup/cronjobs' )|wash}">{'Paused'|i18n( 'design/admin/setup/cronjobs' )}</li>
                {/if}
            </ul>
        </div>
        {* Pause and resume carry the part name as their value, like the run button. *}
        {if $cronjob_pause_enabled}
        {if $cronjob_part.pause.paused}
        <button type="submit" class="a4-btn" name="ResumeCronjobButton" value="{$cronjob_part.name|wash}"
                title="{'Resume the %part part'|i18n( 'design/admin/setup/cronjobs',, hash( '%part', $cronjob_part.label ) )|wash}">{'Resume'|i18n( 'design/admin/setup/cronjobs' )}</button>
        {else}
        <button type="submit" class="a4-btn" name="PauseCronjobButton" value="{$cronjob_part.name|wash}"
                title="{'Leave the %part part out until resumed, whoever starts it'|i18n( 'design/admin/setup/cronjobs',, hash( '%part', $cronjob_part.label ) )|wash}">{'Pause'|i18n( 'design/admin/setup/cronjobs' )}</button>
        {/if}
        {/if}
        {* The button carries the part name as its own value, so one form serves every part and launching needs no
           javascript. *}
        <button type="submit" class="a4-btn a4-btn a4-setup-run" name="LaunchCronjobButton"
                data-blocked="{if $cronjob_blocked}1{else}0{/if}"
                value="{$cronjob_part.name|wash}"{if $cronjob_runnable|not} disabled="disabled"{/if}
                aria-label="{'Run the whole %part part now'|i18n( 'design/admin/setup/cronjobs',, hash( '%part', $cronjob_part.label ) )|wash}"
                title="{'Run the whole %part part now'|i18n( 'design/admin/setup/cronjobs',, hash( '%part', $cronjob_part.label ) )|wash}"><svg width="14" height="14" viewBox="0 0 16 16" aria-hidden="true" focusable="false"><path fill="currentColor" d="M4 2.5v11l9-5.5z"/></svg>{'Run part'|i18n( 'design/admin/setup/cronjobs' )}</button>
    </div>

    <dl class="a4-facts">
        {if and( $cronjob_pause_enabled, $cronjob_part.pause.paused )}
        <div>
            <dt>{'Paused'|i18n( 'design/admin/setup/cronjobs' )}</dt>
            <dd>{if $cronjob_part.pause.by}{'Since %since by %user'|i18n( 'design/admin/setup/cronjobs',, hash( '%since', $cronjob_part.pause.since|l10n( shortdatetime ), '%user', $cronjob_part.pause.by.login|wash ) )}{else}{'Since %since'|i18n( 'design/admin/setup/cronjobs',, hash( '%since', $cronjob_part.pause.since|l10n( shortdatetime ) ) )}{/if}</dd>
        </div>
        {/if}
        <div>
            <dt>{if $cronjob_part.scheduled}{'Schedule'|i18n( 'design/admin/setup/cronjobs' )}{else}{'Suggested schedule'|i18n( 'design/admin/setup/cronjobs' )}{/if}</dt>
            <dd>{if $cronjob_part.schedule_active|ne('')}{$cronjob_part.schedule_text|wash}{if $cronjob_part.schedule_text|ne( $cronjob_part.schedule_active )} <code>{$cronjob_part.schedule_active|wash}</code>{/if}{else}{'Not read from the crontab line'|i18n( 'design/admin/setup/cronjobs' )}{/if}</dd>
        </div>
        <div>
            <dt>{'Next run'|i18n( 'design/admin/setup/cronjobs' )}</dt>
            <dd>{if $cronjob_part.next_run|gt(0)}{$cronjob_part.next_run|l10n( shortdatetime )}{elseif $cronjob_part.scheduled}&mdash;{else}<span class="a4-muted">{'Only when run by hand'|i18n( 'design/admin/setup/cronjobs' )}</span>{/if}</dd>
        </div>
        <div>
            <dt>{'Last run from here'|i18n( 'design/admin/setup/cronjobs' )}</dt>
            <dd>{if $cronjob_part.last_run}
                {$cronjob_part.last_run.started|l10n( shortdatetime )}
                {if $cronjob_part.last_run.running}<span class="a4-badge is-info">{'running'|i18n( 'design/admin/setup/cronjobs' )}</span>
                {else}&middot; {$cronjob_part.last_run.seconds}s
                    {if $cronjob_part.last_run.errors|gt(0)}<span class="a4-badge is-bad">{'%count issues'|i18n( 'design/admin/setup/cronjobs',, hash( '%count', $cronjob_part.last_run.errors ) )}</span>{else}<span class="a4-badge is-ok">{'OK'|i18n( 'design/admin/setup/cronjobs' )}</span>{/if}
                {/if}
                {if $cronjob_part.last_run.script|ne('')}<br /><span class="a4-meta">{$cronjob_part.last_run.script|wash}</span>{/if}
            {else}<span class="a4-muted">{'Not yet'|i18n( 'design/admin/setup/cronjobs' )}</span>{/if}</dd>
        </div>
    </dl>

    <div class="a4-setup-cmd-row">
        <span class="a4-setup-cmd-label" id="{$cronjob_part_id|wash}-cmd-label">{'Shell command'|i18n( 'design/admin/setup/cronjobs' )}</span>
        <code class="a4-setup-cmd" aria-labelledby="{$cronjob_part_id|wash}-cmd-label">{$cronjob_part.command.head|wash}<span class="a4-setup-cmd-sa">{$cronjob_default_siteaccess|wash}</span>{$cronjob_part.command.tail|wash}</code>
        <button type="button" class="a4-btn a4-btn-small a4-setup-copy a4-setup-js-only" hidden
                data-cmd-head="{$cronjob_part.command.head|wash}" data-cmd-tail="{$cronjob_part.command.tail|wash}"
                aria-label="{'Copy the command that runs %part'|i18n( 'design/admin/setup/cronjobs',, hash( '%part', $cronjob_part.label ) )|wash}">{'Copy'|i18n( 'design/admin/setup/cronjobs' )}</button>
    </div>

    {* Folded to one line of script names; open, it says what each script does and runs one on its own. *}
    <details class="a4-setup-scripts">
        <summary><span class="a4-setup-scripts-count">{'Scripts (%count)'|i18n( 'design/admin/setup/cronjobs',, hash( '%count', $cronjob_part.scripts|count ) )}</span> <span class="a4-setup-scripts-names">{$cronjob_part.script_names|wash}</span></summary>
        <ul class="a4-setup-script-list">
        {foreach $cronjob_part.scripts as $cronjob_script}
            <li class="a4-setup-script a4-setup-script-row" data-part="{$cronjob_part.name|wash}">
                <div class="a4-setup-script-main">
                    <code class="a4-setup-script-name">{$cronjob_script.name|wash}</code>
                    {if $cronjob_script.description|ne('')}<p class="a4-setup-script-desc">{$cronjob_script.description|wash}</p>{/if}
                    {if $cronjob_script.documentation|ne('')}<p class="a4-setup-script-doc"><a class="a4-setup-doc-link" href="{$cronjob_script.documentation|wash}" target="_blank" rel="noopener noreferrer" aria-label="{'Documentation of %script (opens in a new window)'|i18n( 'design/admin/setup/cronjobs',, hash( '%script', $cronjob_script.name ) )|wash}">{'Docs'|i18n( 'design/admin/setup/cronjobs' )}</a></p>{/if}
                    {if $cronjob_script.path|eq(false())}
                    <p class="a4-setup-script-where is-missing">{'not found in any cronjob directory'|i18n( 'design/admin/setup/cronjobs' )}</p>
                    {else}
                    <p class="a4-setup-script-where">{$cronjob_script.directory|wash}</p>
                    {/if}
                </div>
                <div class="a4-setup-script-actions">
                    {if $cronjob_script.path|ne(false())}
                    <button type="button" class="a4-btn a4-btn-small a4-btn-ghost a4-setup-copy a4-setup-js-only" hidden
                            data-cmd-head="{$cronjob_script.command.head|wash}" data-cmd-tail="{$cronjob_script.command.tail|wash}"
                            aria-label="{'Copy the command that runs %script on its own'|i18n( 'design/admin/setup/cronjobs',, hash( '%script', $cronjob_script.name ) )|wash}">{'Copy command'|i18n( 'design/admin/setup/cronjobs' )}</button>
                    {/if}
                    {if and( $cronjob_script.path|ne(false()), $cronjob_part.forbidden|not )}
                    <button type="submit" class="a4-btn a4-btn-small a4-setup-run" name="LaunchCronjobScriptButton"
                            data-blocked="{if or( $cronjob_php_binary|eq(''), $cronjob_paused )}1{else}0{/if}"
                            value="{$cronjob_part.name|wash}|{$cronjob_script.name|wash}"{if or( $cronjob_status.running, $cronjob_php_binary|eq(''), $cronjob_paused )} disabled="disabled"{/if}
                            aria-label="{'Run %script on its own'|i18n( 'design/admin/setup/cronjobs',, hash( '%script', $cronjob_script.name ) )|wash}"
                            title="{'Run %script on its own'|i18n( 'design/admin/setup/cronjobs',, hash( '%script', $cronjob_script.name ) )|wash}">{'Run'|i18n( 'design/admin/setup/cronjobs' )}</button>
                    {else}
                    <span class="a4-meta">{'This script cannot be run from here'|i18n( 'design/admin/setup/cronjobs' )}</span>
                    {/if}
                </div>
            </li>
        {/foreach}
        </ul>
    </details>
</li>
    {undef $cronjob_runnable $cronjob_blocked $cronjob_paused $cronjob_part_id}
{/foreach}
</ul>
<p class="a4-empty" id="cronjob-no-match" hidden>{'No cronjob part on this page matches.'|i18n( 'design/admin/setup/cronjobs' )}</p>
{/if}

{* Paged; the size is admininterface.ini [PaginationSettings]. The compact view shows every part. *}
{if and( $cronjob_view|eq('cards'), $cronjob_parts_count|gt( $limit ) )}
<div class="context-toolbar a4-pager">
{include name=CronjobNavigator
         uri='design:navigator/google.tpl'
         page_uri='/setup/cronjobs'
         item_count=$cronjob_parts_count
         view_parameters=$view_parameters
         item_limit=$limit}
</div>
{/if}
</section>
{undef $cronjob_view}

</form>

{* On disk, named by no part, so nothing ever runs them. *}
{if $cronjob_available_scripts|count|gt(0)}
<details class="a4-fold" id="cronjob-available">
    <summary>
        <h2 class="a4-h2">{'Available but not activated'|i18n( 'design/admin/setup/cronjobs' )}</h2>
        <span class="a4-meta">{'%count scripts no part names, so they never run'|i18n( 'design/admin/setup/cronjobs',, hash( '%count', $cronjob_available_scripts|count ) )}</span>
    </summary>
    <div class="a4-fold-body">
        <p class="a4-meta">{'These scripts exist but no cronjob part names them, so they never run.'|i18n( 'design/admin/setup/cronjobs' )} {'Add it to a part in cronjob.ini to run it'|i18n( 'design/admin/setup/cronjobs' )}.</p>
        <ul class="a4-setup-script-list">
        {foreach $cronjob_available_scripts as $cronjob_spare}
            <li class="a4-setup-script a4-setup-spare-row">
                <div class="a4-setup-script-main">
                    <code class="a4-setup-script-name">{$cronjob_spare.name|wash}</code>
                    {if $cronjob_spare.description|ne('')}<p class="a4-setup-script-desc">{$cronjob_spare.description|wash}</p>{/if}
                    {if $cronjob_spare.documentation|ne('')}<p class="a4-setup-script-doc"><a class="a4-setup-doc-link" href="{$cronjob_spare.documentation|wash}" target="_blank" rel="noopener noreferrer" aria-label="{'Documentation of %script (opens in a new window)'|i18n( 'design/admin/setup/cronjobs',, hash( '%script', $cronjob_spare.name ) )|wash}">{'Docs'|i18n( 'design/admin/setup/cronjobs' )}</a></p>{/if}
                    <p class="a4-setup-script-where">{$cronjob_spare.directory|wash}</p>
                </div>
                <span class="a4-badge is-warn">{'Available'|i18n( 'design/admin/setup/cronjobs' )}</span>
            </li>
        {/foreach}
        </ul>
    </div>
</details>
{/if}

{* What has run, when, and whether it complained. *}
<details class="a4-fold" id="cronjob-history" open="open">
    <summary>
        <h2 class="a4-h2" id="cronjob-history-title">{'Recent runs'|i18n( 'design/admin/setup/cronjobs' )}</h2>
        <span class="a4-meta">{'Started from this page, newest first'|i18n( 'design/admin/setup/cronjobs' )}</span>
    </summary>
    <div class="a4-fold-body">
    {if $cronjob_history|count|gt(0)}
    <div class="a4-table-wrap" role="region" aria-labelledby="cronjob-history-title" tabindex="0">
    <table class="a4-table">
    <thead>
    <tr>
        <th scope="col">{'Cronjob'|i18n( 'design/admin/setup/cronjobs' )}</th>
        <th scope="col">{'Site'|i18n( 'design/admin/setup/cronjobs' )}</th>
        <th scope="col">{'Started'|i18n( 'design/admin/setup/cronjobs' )}</th>
        <th scope="col" class="a4-num">{'Took'|i18n( 'design/admin/setup/cronjobs' )}</th>
        <th scope="col">{'Result'|i18n( 'design/admin/setup/cronjobs' )}</th>
    </tr>
    </thead>
    <tbody>
    {foreach $cronjob_history as $cronjob_run}
    <tr>
        <td>{$cronjob_run.label|wash}</td>
        <td>{$cronjob_run.siteaccess|wash}</td>
        <td class="a4-nowrap">{$cronjob_run.started|l10n( shortdatetime )}</td>
        <td class="a4-num">{if $cronjob_run.running}&mdash;{else}{$cronjob_run.seconds}s{/if}</td>
        <td>{if $cronjob_run.running}<span class="a4-badge is-info">{'running'|i18n( 'design/admin/setup/cronjobs' )}</span>{elseif $cronjob_run.errors|gt(0)}<span class="a4-badge is-bad">{'%count issues'|i18n( 'design/admin/setup/cronjobs',, hash( '%count', $cronjob_run.errors ) )}</span>{else}<span class="a4-badge is-ok">{'OK'|i18n( 'design/admin/setup/cronjobs' )}</span>{/if}</td>
    </tr>
    {/foreach}
    </tbody>
    </table>
    </div>
    <p class="a4-meta a4-setup-gap">{'Issues are the log lines of a run that mention an error, a failure or a warning.'|i18n( 'design/admin/setup/cronjobs' )}</p>
    {else}
    <p class="a4-empty">{'Nothing has been run from here yet.'|i18n( 'design/admin/setup/cronjobs' )}</p>
    {/if}
    </div>
</details>

{* Who paused and resumed what, newest first. Changes made to the pause files by other means (a shell, the
   cjw_admincockpit page) have no entry here; the card still shows since when a part is paused. *}
{if $cronjob_pause_enabled}
<details class="a4-fold" id="cronjob-pauses"{if $cronjob_pause_history|count|gt(0)} open="open"{/if}>
    <summary>
        <h2 class="a4-h2" id="cronjob-pauses-title">{'Pauses'|i18n( 'design/admin/setup/cronjobs' )}</h2>
        <span class="a4-meta">{'Paused and resumed from this page, newest first'|i18n( 'design/admin/setup/cronjobs' )}</span>
    </summary>
    <div class="a4-fold-body">
    {if $cronjob_pause_history|count|gt(0)}
    <div class="a4-table-wrap" role="region" aria-labelledby="cronjob-pauses-title" tabindex="0">
    <table class="a4-table">
    <thead>
    <tr>
        <th scope="col">{'When'|i18n( 'design/admin/setup/cronjobs' )}</th>
        <th scope="col">{'Cronjob'|i18n( 'design/admin/setup/cronjobs' )}</th>
        <th scope="col">{'Action'|i18n( 'design/admin/setup/cronjobs' )}</th>
        <th scope="col">{'User'|i18n( 'design/admin/setup/cronjobs' )}</th>
    </tr>
    </thead>
    <tbody>
    {foreach $cronjob_pause_history as $cronjob_pause}
    <tr>
        <td class="a4-nowrap">{$cronjob_pause.time|l10n( shortdatetime )}</td>
        <td>{if $cronjob_pause.part|eq('')}{'Global'|i18n( 'design/admin/setup/cronjobs' )}{elseif $cronjob_pause.part|eq('all')}{'All parts'|i18n( 'design/admin/setup/cronjobs' )}{else}{$cronjob_pause.part|wash}{/if}</td>
        <td>{if $cronjob_pause.action|eq('pause')}<span class="a4-badge is-bad">{'Paused'|i18n( 'design/admin/setup/cronjobs' )}</span>{else}<span class="a4-badge is-ok">{'Resumed'|i18n( 'design/admin/setup/cronjobs' )}</span>{/if}</td>
        <td>{$cronjob_pause.login|wash}</td>
    </tr>
    {/foreach}
    </tbody>
    </table>
    </div>
    {else}
    <p class="a4-empty">{'Nothing has been paused from here yet.'|i18n( 'design/admin/setup/cronjobs' )}</p>
    {/if}
    </div>
</details>
{/if}

{* Last on the page and folded away: useful when setting the schedule up, noise every other time. *}
<details class="a4-fold" id="cronjob-crontab-block">
    <summary>
        <h2 class="a4-h2">{'Crontab'|i18n( 'design/admin/setup/cronjobs' )}</h2>
        <span class="a4-meta">{'What is scheduled now, and the lines that would schedule the rest'|i18n( 'design/admin/setup/cronjobs' )}</span>
    </summary>
    <div class="a4-fold-body">
        {* What is really in the crontab, read from it. *}
        <h3>{'In the crontab now'|i18n( 'design/admin/setup/cronjobs' )}</h3>
        <p class="a4-meta">{'Read from crontab -l for the user this site runs as.'|i18n( 'design/admin/setup/cronjobs' )}</p>
        {if $cronjob_crontab.available}
            {if $cronjob_crontab.lines|count|gt(0)}
        <pre class="a4-setup-crontab" tabindex="0">{$cronjob_crontab_current|wash}</pre>
            {else}
        <p class="a4-meta">{'The crontab is empty.'|i18n( 'design/admin/setup/cronjobs' )}</p>
            {/if}
        {else}
        <p class="a4-meta">{$cronjob_crontab.note|wash}</p>
        {/if}

        {* And what would schedule the parts that nothing schedules. Generated here from this installation's own
           paths and php binary - not read from anywhere. *}
        <h3>{'Suggested entries'|i18n( 'design/admin/setup/cronjobs' )}</h3>
        <p class="a4-meta">{'Written by this page from the paths below, for the parts nothing currently runs. Nothing adds them for you.'|i18n( 'design/admin/setup/cronjobs' )}</p>
        {if $cronjob_crontab_suggested|ne('')}
        <pre class="a4-setup-crontab" id="cronjob-crontab-suggested" tabindex="0">{$cronjob_crontab_suggested|wash}</pre>
        <button type="button" class="a4-btn a4-btn-small a4-setup-copy a4-setup-js-only" data-copy-from="cronjob-crontab-suggested" hidden>{'Copy the suggested entries'|i18n( 'design/admin/setup/cronjobs' )}</button>
        {else}
        <p class="a4-meta">{'Every part is scheduled.'|i18n( 'design/admin/setup/cronjobs' )}</p>
        {/if}
        <p class="a4-meta a4-setup-gap">{'Installation'|i18n( 'design/admin/setup/cronjobs' )}: <code>{$cronjob_root|wash}</code></p>
    </div>
</details>

<p class="a4-sr-only" id="cronjob-copy-status" role="status" aria-live="polite"></p>

{* Template values are read before the literal block, because everything inside one is passed through untouched -
   which is the point, since javascript is full of braces the template engine would otherwise try to parse. *}
<script type="text/javascript">
var expCronjobActionUrl = {'setup/cronjobs'|ezurl()};
var expCronjobTokenField = '{$cronjob_form_field|wash( javascript )}';
var expCronjobToken      = '{$cronjob_form_token|wash( javascript )}';
var expCronjobStreamUrl = {$cronjob_stream_url|ezurl()};
var expCronjobRunning   = {if $cronjob_status.running}true{else}false{/if};
var expCronjobOffset    = {$cronjob_log_offset};
{* What the console writes itself, translated here; %name is replaced in the script. *}
var expCronjobText = {ldelim}
    idle: '{'Idle'|i18n( 'design/admin/setup/cronjobs' )|wash( javascript )}',
    running: '{'Running: %part'|i18n( 'design/admin/setup/cronjobs' )|wash( javascript )}',
    site: '{'Site: %siteaccess'|i18n( 'design/admin/setup/cronjobs' )|wash( javascript )}',
    process: '{'Process: %pid'|i18n( 'design/admin/setup/cronjobs' )|wash( javascript )}',
    elapsed: '{'Elapsed: %elapsed'|i18n( 'design/admin/setup/cronjobs' )|wash( javascript )}',
    following: '{'following…'|i18n( 'design/admin/setup/cronjobs' )|wash( javascript )}',
    finished: '{'finished'|i18n( 'design/admin/setup/cronjobs' )|wash( javascript )}',
    streamClosed: '{'stream closed'|i18n( 'design/admin/setup/cronjobs' )|wash( javascript )}',
    noConnection: '{'The request did not reach the server. Check the connection and try again.'|i18n( 'design/admin/setup/cronjobs' )|wash( javascript )}',
    answered: '{'The server answered %status: %text'|i18n( 'design/admin/setup/cronjobs' )|wash( javascript )}',
    answeredNothing: '{'The server answered %status. It sent nothing that could be read.'|i18n( 'design/admin/setup/cronjobs' )|wash( javascript )}',
    failed: '{'The request failed before the server could answer.'|i18n( 'design/admin/setup/cronjobs' )|wash( javascript )}',
    timeout: '{'The server did not answer in time. The job may still have started; reload to see.'|i18n( 'design/admin/setup/cronjobs' )|wash( javascript )}',
    notSent: '{'The request could not be sent: %error'|i18n( 'design/admin/setup/cronjobs' )|wash( javascript )}',
    next: '{'Next: %part'|i18n( 'design/admin/setup/cronjobs' )|wash( javascript )}',
    runsEvery: '{'Runs every part, one after another.'|i18n( 'design/admin/setup/cronjobs' )|wash( javascript )}',
    runsOne: '{'Runs the %part part.'|i18n( 'design/admin/setup/cronjobs' )|wash( javascript )}',
    nothingToRun: '{'There is no part that can be run.'|i18n( 'design/admin/setup/cronjobs' )|wash( javascript )}',
    runningList: '{'Running %count parts, one after another.'|i18n( 'design/admin/setup/cronjobs' )|wash( javascript )}',
    shown: '{'%shown of %count parts on this page shown'|i18n( 'design/admin/setup/cronjobs' )|wash( javascript )}',
    allShown: '{'%count parts on this page'|i18n( 'design/admin/setup/cronjobs' )|wash( javascript )}',
    copy: '{'Copy'|i18n( 'design/admin/setup/cronjobs' )|wash( javascript )}',
    copied: '{'Copied'|i18n( 'design/admin/setup/cronjobs' )|wash( javascript )}',
    copiedLong: '{'The command is on the clipboard.'|i18n( 'design/admin/setup/cronjobs' )|wash( javascript )}',
    copyFailed: '{'Could not copy. Select the text and copy it by hand.'|i18n( 'design/admin/setup/cronjobs' )|wash( javascript )}'
{rdelim};
{literal}
(function () {
    var consoleEl = document.getElementById( 'cronjob-console' );
    var outputEl  = document.getElementById( 'cronjob-output' );
    var statusEl  = document.getElementById( 'cronjob-stream-status' );
    var stopBtn   = document.getElementById( 'cronjob-stop' );
    var clearBtn  = document.getElementById( 'cronjob-clear' );
    var saEl      = document.getElementById( 'cronjob-siteaccess' );
    var feedEl    = document.getElementById( 'cronjob-feedback' );
    var runSelectedEl = document.getElementById( 'cronjob-run-selected' );

    function each( selector, fn ) {
        var nodes = document.querySelectorAll( selector ), i;
        for ( i = 0; i < nodes.length; i++ ) fn( nodes[i], i );
    }

    // A translated console string with its %name placeholders filled in.
    function tr( name, values ) {
        var s = expCronjobText[name], key;
        for ( key in values || {} ) s = s.split( '%' + key ).join( values[key] );
        return s;
    }

    function text( el, value ) { if ( el ) el.textContent = value; }

    // ---- What works with javascript alone: the filters, the copy buttons, the site in the commands ----------

    each( '.a4-setup-cron .a4-setup-js-only', function ( el ) { el.hidden = false; } );

    var searchEl = document.getElementById( 'cronjob-search' );
    var filterEl = document.getElementById( 'cronjob-part-filter' );
    var runHintEl = document.getElementById( 'cronjob-run-hint' );
    var countEl = document.getElementById( 'cronjob-filter-count' );
    var noMatchEl = document.getElementById( 'cronjob-no-match' );

    function chosenState() {
        var picked = document.querySelector( 'input[name="CronjobFilterState"]:checked' );
        return picked ? picked.value : '';
    }

    // The search, the state and the part together decide which cards are shown.
    function applyFilter() {
        var words = searchEl ? searchEl.value.toLowerCase().split( /\s+/ ).filter( Boolean ) : [];
        var state = chosenState();
        var part = filterEl ? filterEl.value : '';
        var shown = 0, total = 0;
        each( '#cronjob-list > .a4-card, #cronjob-table .a4-setup-part-row', function ( card ) {
            total++;
            var haystack = card.getAttribute( 'data-search' ) || '';
            var ok = ( part === '' || card.getAttribute( 'data-part' ) === part );
            if ( ok && state === 'attention' ) ok = card.getAttribute( 'data-attention' ) === '1';
            else if ( ok && state !== '' ) ok = card.getAttribute( 'data-state' ) === state;
            for ( var i = 0; ok && i < words.length; i++ ) ok = haystack.indexOf( words[i] ) !== -1;
            card.hidden = !ok;
            if ( ok ) shown++;
            // The scripts row of a compact row goes with it.
            var detail = card.nextElementSibling;
            if ( detail && detail.classList.contains( 'a4-setup-compact-detail' ) ) {
                var folded = card.querySelector( '.a4-setup-compact-scripts' );
                detail.hidden = !ok || !( folded && folded.open );
            }
            // A search may match a script rather than the part, so the scripts of a match are opened while it
            // lasts; the ones opened that way close again when the search is cleared.
            var scripts = card.querySelector( '.a4-setup-scripts, .a4-setup-compact-scripts' );
            if ( scripts ) {
                if ( ok && words.length ) {
                    if ( !scripts.open ) { scripts.open = true; scripts.setAttribute( 'data-auto-open', '1' ); }
                } else if ( scripts.getAttribute( 'data-auto-open' ) ) {
                    scripts.open = false;
                    scripts.removeAttribute( 'data-auto-open' );
                }
            }
        } );
        if ( noMatchEl ) noMatchEl.hidden = !( total > 0 && shown === 0 );
        if ( countEl ) text( countEl, shown === total ? tr( 'allShown', { count: total } ) : tr( 'shown', { shown: shown, count: total } ) );
        if ( runHintEl && filterEl )
            text( runHintEl, part === '' ? tr( 'runsEvery' ) : tr( 'runsOne', { part: filterEl.options[filterEl.selectedIndex].text } ) );
    }
    if ( searchEl ) searchEl.addEventListener( 'input', applyFilter );
    if ( filterEl ) filterEl.addEventListener( 'change', applyFilter );
    each( 'input[name="CronjobFilterState"]', function ( radio ) { radio.addEventListener( 'change', applyFilter ); } );
    applyFilter();

    // The commands name the site chosen above.
    function siteaccess() { return saEl ? saEl.value : ''; }
    if ( saEl ) saEl.addEventListener( 'change', function () {
        each( '.a4-setup-cmd-sa', function ( el ) { text( el, siteaccess() ); } );
    } );

    var copyStatusEl = document.getElementById( 'cronjob-copy-status' );
    function copyText( value, button ) {
        function done( ok ) {
            text( copyStatusEl, ok ? tr( 'copiedLong' ) : tr( 'copyFailed' ) );
            if ( !ok ) return;
            var label = button.textContent;
            text( button, tr( 'copied' ) );
            window.setTimeout( function () { text( button, label ); }, 1600 );
        }
        if ( navigator.clipboard && window.isSecureContext ) {
            navigator.clipboard.writeText( value ).then( function () { done( true ); }, function () { done( fallback() ); } );
            return;
        }
        done( fallback() );
        function fallback() {
            var area = document.createElement( 'textarea' );
            area.value = value; area.setAttribute( 'readonly', '' );
            area.style.position = 'fixed'; area.style.opacity = '0';
            document.body.appendChild( area ); area.select();
            var ok = false;
            try { ok = document.execCommand( 'copy' ); } catch ( e ) {}
            document.body.removeChild( area );
            button.focus();
            return ok;
        }
    }
    each( '.a4-setup-copy', function ( button ) {
        button.addEventListener( 'click', function () {
            var from = button.getAttribute( 'data-copy-from' );
            var value = from ? ( document.getElementById( from ) || {} ).textContent || ''
                             : ( button.getAttribute( 'data-cmd-head' ) || '' ) + siteaccess() + ( button.getAttribute( 'data-cmd-tail' ) || '' );
            copyText( value, button );
        } );
    } );

    // ---- Running in place and following the output; without EventSource the form posts on its own ------------

    if ( !consoleEl || typeof window.EventSource === 'undefined' ) {
        if ( runSelectedEl ) { runSelectedEl.hidden = true; if ( runHintEl ) runHintEl.hidden = true; }
        return;
    }

    var source = null;
    var elapsedTimer = null;
    var offset = expCronjobOffset;

    function write( type, line ) {
        var atBottom = consoleEl.scrollTop + consoleEl.clientHeight >= consoleEl.scrollHeight - 8;
        var el = document.createElement( 'div' );
        // each kind of line has its colour in modules/setup.css; an ordinary line of output is plain text
        el.className = 'a4-setup-console-' + ( /^(phase|warn|error|info|done)$/.test( type ) ? type : 'text' );
        el.appendChild( document.createTextNode( line ) );
        consoleEl.appendChild( el );
        if ( atBottom ) consoleEl.scrollTop = consoleEl.scrollHeight;
    }

    function say( value ) { text( statusEl, value ); }

    function feedback( ok, message ) {
        if ( !feedEl ) return;
        feedEl.className = 'a4-notice ' + ( ok ? 'is-ok' : 'is-bad' );
        feedEl.setAttribute( 'role', ok ? 'status' : 'alert' );
        feedEl.hidden = false;
        text( feedEl, message );
    }

    function pillText( value, running ) {
        var pill = document.getElementById( 'cronjob-pill' );
        if ( !pill ) return;
        pill.className = 'a4-status' + ( running ? ' is-running' : '' );
        text( pill.querySelector( '.a4-setup-pill-text' ), value );
    }

    // The page is never fetched again, in either direction. Everything an action or a finished job changes is on
    // this page already, so it is changed here. A reload repeats the request that produced the page, so the form
    // post that started a job was offered for resending, and confirming launched it again - and again.
    function markIdle() {
        pillText( tr( 'idle' ), false );
        each( '.a4-setup-running-meta', function ( el ) { el.parentNode.removeChild( el ); } );
        each( '.a4-setup-part-row.is-current', function ( el ) { el.classList.remove( 'is-current' ); } );
        if ( stopBtn ) stopBtn.disabled = true;
        if ( runSelectedEl && runSelectedEl.getAttribute( 'data-blocked' ) !== '1' ) runSelectedEl.disabled = false;
        // Only the ones that were waiting on this job. A part that is forbidden, missing its scripts, or has no php
        // to run with stays disabled.
        each( '.a4-setup-run', function ( el ) {
            if ( el.getAttribute( 'data-blocked' ) !== '1' ) el.disabled = false;
        } );
        if ( elapsedTimer ) { window.clearInterval( elapsedTimer ); elapsedTimer = null; }
    }

    function markRunning( part, siteaccessName, pid ) {
        pillText( tr( 'running', { part: part } ), true );

        var strip = document.getElementById( 'cronjob-status' );
        if ( strip ) {
            each( '.a4-setup-running-meta', function ( el ) { el.parentNode.removeChild( el ); } );
            var meta = [ [ tr( 'site', { siteaccess: siteaccessName } ), null ],
                         [ tr( 'process', { pid: pid } ), null ],
                         [ tr( 'elapsed', { elapsed: '0s' } ), 'cronjob-elapsed' ] ];
            for ( var i = 0; i < meta.length; i++ ) {
                var span = document.createElement( 'span' );
                span.className = 'a4-meta a4-setup-running-meta';
                if ( meta[i][1] ) span.id = meta[i][1];
                span.appendChild( document.createTextNode( meta[i][0] ) );
                strip.appendChild( span );
            }
        }

        each( '.a4-setup-run', function ( el ) { el.disabled = true; } );
        if ( runSelectedEl ) runSelectedEl.disabled = true;
        if ( stopBtn ) stopBtn.disabled = false;
        each( '.a4-setup-part-row', function ( el ) {
            if ( el.getAttribute( 'data-part' ) === part ) el.classList.add( 'is-current' );
        } );

        startElapsed( 0 );
        follow();
    }

    function startElapsed( from ) {
        if ( elapsedTimer ) window.clearInterval( elapsedTimer );
        var el = document.getElementById( 'cronjob-elapsed' );
        if ( !el ) return;
        var openedAt = new Date().getTime();
        elapsedTimer = window.setInterval( function () {
            text( el, tr( 'elapsed', { elapsed: ( from + Math.round( ( new Date().getTime() - openedAt ) / 1000 ) ) + 's' } ) );
        }, 1000 );
    }

    function follow() {
        if ( source ) return;
        say( tr( 'following' ) );
        source = new EventSource( expCronjobStreamUrl + '?Offset=' + offset );

        source.onmessage = function ( event ) {
            var payload;
            try { payload = JSON.parse( event.data ); }
            catch ( e ) { return; }
            if ( typeof payload.offset === 'number' ) offset = payload.offset;
            write( payload.type, payload.message );
            if ( payload.type === 'done' ) { close( tr( 'finished' ) ); afterFinished(); }
        };

        source.addEventListener( 'end', function () { close( tr( 'finished' ) ); afterFinished(); } );

        // EventSource reconnects by itself, which would start the tail over from the offset this page was rendered
        // with and reprint it all.
        source.onerror = function () { close( tr( 'streamClosed' ) ); };
    }

    function close( message ) {
        if ( source ) { source.close(); source = null; }
        say( message );
    }

    // Reads an answer out of whatever came back. Anything appended to the json - a debug block, a warning - would
    // otherwise make the reply unparseable, so the first balanced object in the text is taken if parsing the lot
    // fails.
    function readAnswer( body ) {
        if ( !body ) return null;
        try { return JSON.parse( body ); } catch ( e ) {}
        var start = body.indexOf( '{' );
        while ( start !== -1 ) {
            var depth = 0, i;
            for ( i = start; i < body.length; i++ ) {
                if ( body.charAt( i ) === '{' ) depth++;
                else if ( body.charAt( i ) === '}' ) {
                    depth--;
                    if ( depth === 0 ) {
                        try { return JSON.parse( body.substring( start, i + 1 ) ); } catch ( e ) {}
                        break;
                    }
                }
            }
            start = body.indexOf( '{', start + 1 );
        }
        return null;
    }

    // When there is no json at all the page that came back is shown, trimmed of markup: it usually says exactly
    // what went wrong.
    function describeFailure( request ) {
        if ( request.status === 0 ) return tr( 'noConnection' );
        var body = ( request.responseText || '' )
                       .replace( /<script[\s\S]*?<\/script>/gi, ' ' )
                       .replace( /<(?:style)[\s\S]*?<\/style>/gi, ' ' )
                       .replace( /<[^>]*>/g, ' ' )
                       .replace( /\s+/g, ' ' ).trim();
        if ( body.length > 240 ) body = body.substring( 0, 240 ) + '…';
        return body ? tr( 'answered', { status: request.status, text: body } )
                    : tr( 'answeredNothing', { status: request.status } );
    }

    // An action asks for its answer rather than a new page. The same view does the same work either way.
    function act( fields, onDone ) {
        var body = 'Ajax=1', key;
        for ( key in fields )
            body += '&' + encodeURIComponent( key ) + '=' + encodeURIComponent( fields[key] );

        // ezformtoken refuses a post from a logged in user that does not carry its token, so it is sent both
        // ways it accepts: in the field it looks for, and in the header it offers for requests that are not forms.
        if ( expCronjobToken && expCronjobTokenField )
            body += '&' + encodeURIComponent( expCronjobTokenField ) + '=' + encodeURIComponent( expCronjobToken );

        var request = new XMLHttpRequest();
        request.open( 'POST', expCronjobActionUrl, true );
        request.setRequestHeader( 'Content-Type', 'application/x-www-form-urlencoded' );
        request.setRequestHeader( 'X-Requested-With', 'XMLHttpRequest' );
        if ( expCronjobToken ) request.setRequestHeader( 'X-CSRF-Token', expCronjobToken );

        var finished = false;
        function settle( answer ) {
            if ( finished ) return;
            finished = true;
            feedback( answer.ok, answer.message );
            try { onDone( answer ); } catch ( e ) {}
            // Whatever happened, the controls must not be left disabled with nothing running.
            if ( !answer.ok && !answer.running ) markIdle();
        }

        request.onreadystatechange = function () {
            if ( request.readyState !== 4 ) return;
            var answer = readAnswer( request.responseText );
            if ( answer && typeof answer.ok !== 'undefined' ) { settle( answer ); return; }
            settle( { ok: false, running: false, message: describeFailure( request ) } );
        };
        request.onerror = function () { settle( { ok: false, running: false, message: tr( 'failed' ) } ); };
        request.ontimeout = function () { settle( { ok: false, running: false, message: tr( 'timeout' ) } ); };
        request.timeout = 120000;

        try { request.send( body ); }
        catch ( e ) { settle( { ok: false, running: false, message: tr( 'notSent', { error: e.message } ) } ); }
    }

    // With no token there is nothing to prove the request with, and posting in the background would only be
    // refused. The buttons are then left to submit the form, which carries a token of its own.
    var canPostInBackground = !!expCronjobToken;
    if ( !canPostInBackground && runSelectedEl ) { runSelectedEl.hidden = true; if ( runHintEl ) runHintEl.hidden = true; }

    // What is still to be run, when a whole list of parts was asked for.
    var queue = [];

    function showOutput() {
        if ( !outputEl ) return;
        outputEl.hidden = false;
        outputEl.open = true;
    }

    // Starts one thing: a part on its own, or one script of a part.
    function launch( fields, part ) {
        showOutput();
        consoleEl.scrollTop = consoleEl.scrollHeight;
        if ( saEl ) fields.CronjobSiteAccess = saEl.value;
        act( fields, function ( answer ) {
            if ( !answer.ok ) { queue = []; return; }
            if ( typeof answer.offset === 'number' ) offset = answer.offset;
            markRunning( answer.part || part, answer.siteaccess, answer.pid );
        } );
    }

    // Called when a job ends. If a list was asked for, the next one starts.
    function afterFinished() {
        markIdle();
        if ( !queue.length ) return;
        var next = queue.shift();
        write( 'phase', tr( 'next', { part: next } ) );
        window.setTimeout( function () { launch( { LaunchCronjobButton: next }, next ); }, 400 );
    }

    each( '.a4-setup-run', function ( button ) {
        button.addEventListener( 'click', function ( event ) {
            if ( !canPostInBackground ) return;
            event.preventDefault();
            queue = [];
            var value = button.value, fields = {}, part = value;
            if ( button.name === 'LaunchCronjobScriptButton' ) {
                fields.LaunchCronjobScriptButton = value;
                part = value.split( '|' )[0];
            } else {
                fields.LaunchCronjobButton = value;
            }
            launch( fields, part );
        } );
    } );

    if ( runSelectedEl ) {
        runSelectedEl.addEventListener( 'click', function ( event ) {
            event.preventDefault();
            if ( !canPostInBackground ) return;
            var wanted = filterEl ? filterEl.value : '';
            queue = [];
            if ( wanted !== '' ) { launch( { LaunchCronjobButton: wanted }, wanted ); return; }

            // Every part it is allowed to run - on every page, not only this one - one after another, so only one
            // cronjob is ever going at a time.
            each( '#cronjob-part-filter option', function ( option ) {
                if ( option.value !== '' && option.getAttribute( 'data-blocked' ) !== '1' ) queue.push( option.value );
            } );
            if ( !queue.length ) { feedback( false, tr( 'nothingToRun' ) ); return; }
            showOutput();
            write( 'phase', tr( 'runningList', { count: queue.length } ) );
            var first = queue.shift();
            launch( { LaunchCronjobButton: first }, first );
        } );
    }

    if ( stopBtn ) {
        stopBtn.addEventListener( 'click', function ( event ) {
            if ( !canPostInBackground ) return;
            event.preventDefault();
            queue = [];
            act( { StopCronjobButton: '1' }, function () {} );   // The stream sees the job end and settles the rest.
        } );
    }

    if ( clearBtn ) {
        clearBtn.addEventListener( 'click', function ( event ) {
            if ( !canPostInBackground ) return;
            event.preventDefault();
            act( { ClearCronjobLogButton: '1' }, function () { consoleEl.textContent = ''; offset = 0; } );
        } );
    }

    consoleEl.scrollTop = consoleEl.scrollHeight;
    // The compact view opens a part's scripts in the row below it, across the table, so several can be open.
    var compactTable = document.getElementById( 'cronjob-table' );
    if ( compactTable ) {
        compactTable.classList.add( 'is-scripted' );
        each( '#cronjob-table .a4-setup-compact-scripts', function ( folded ) {
            folded.addEventListener( 'toggle', function () {
                var row = folded.closest( 'tr' );
                var detail = row ? row.nextElementSibling : null;
                if ( detail && detail.classList.contains( 'a4-setup-compact-detail' ) ) detail.hidden = !folded.open || row.hidden;
            } );
        } );
    }

    if ( outputEl ) outputEl.addEventListener( 'toggle', function () {
        if ( outputEl.open ) consoleEl.scrollTop = consoleEl.scrollHeight;
    } );

    // Only follow when something is actually running. With nothing running the page already shows what the log
    // holds, and opening a stream would hold a php worker for no reason.
    if ( expCronjobRunning ) {
        showOutput();
        var el = document.getElementById( 'cronjob-elapsed' );
        var from = 0;
        if ( el ) {
            from = parseInt( ( el.textContent || '' ).replace( /[^0-9]/g, '' ), 10 );
            if ( isNaN( from ) ) from = 0;
        }
        startElapsed( from );
        follow();
    }
})();
{/literal}
</script>

</div></div></div>
</div>
