{* The cronjobs console.

   Lives in the admin design rather than admin3 so every administration design
   resolves it - admin3 lists admin2 and admin as its fallbacks, so it is found
   there too - but it is written to admin3's idiom: no nested box-tl/box-tc
   scaffolding, no spacer images, one grid of cards, and a console that fills in
   as the job runs. The styling is scoped to .exp-cronjobs and carries its own
   values so it looks the same under admin, admin2 and admin3. *}

{literal}
<style>
.exp-cronjobs {
    /* One spacing rhythm for the page, so nothing sits on top of
       anything else. --exp-gap is the space between sections. */
    --exp-gap: 2.75rem;
    --exp-cell-y: .85rem;
    --exp-cell-x: .75rem;
    --exp-bg: #ffffff;
    --exp-line: #e2e2e5;
    --exp-muted: #6b6b70;
    --exp-ink: #1f1f23;
    --exp-accent: #ff5500;
    --exp-ok: #2e7d32;
    --exp-warn: #a4741a;
    --exp-bad: #b3261e;
    --exp-radius: 10px;
    color: var(--exp-ink);
    font-size: 0.9rem;
}
.exp-cronjobs * { box-sizing: border-box; }
.exp-cronjobs { padding-bottom: 1.5rem; }
.exp-cronjobs h1.context-title { margin: 0; }

.exp-head {
    display: flex; flex-wrap: wrap; gap: 1rem;
    align-items: center; justify-content: space-between;
    padding: .5rem 0 1.5rem 0;
}
.exp-head p { margin: .4rem 0 0 0; line-height: 1.55; color: var(--exp-muted); max-width: 60ch; }

.exp-status {
    display: flex; flex-wrap: wrap; gap: .7rem 2rem; align-items: center;
    border: 1px solid var(--exp-line); border-radius: var(--exp-radius);
    background: #fafafa; padding: 1rem 1.15rem; margin: 0 0 var(--exp-gap) 0;
}
.exp-pill {
    display: inline-flex; align-items: center; gap: .45rem;
    font-weight: 600; letter-spacing: .01em;
}
.exp-dot { width: .6rem; height: .6rem; border-radius: 50%; background: #bdbdbd; }
.exp-pill.is-running .exp-dot { background: var(--exp-ok); animation: exp-pulse 1.4s ease-in-out infinite; }
@keyframes exp-pulse { 0%,100% { opacity: 1; } 50% { opacity: .3; } }
.exp-meta { color: var(--exp-muted); font-size: .85em; }
.exp-meta code { background: #eee; border-radius: 4px; padding: .05rem .3rem; }

.exp-controls {
    display: flex; flex-wrap: wrap; gap: 1rem 1.25rem; align-items: flex-end;
    margin: 0 0 var(--exp-gap) 0; padding: .35rem 0;
}
.exp-field { display: flex; flex-direction: column; gap: .4rem; }
.exp-field label { font-size: .8em; color: var(--exp-muted); text-transform: uppercase; letter-spacing: .04em; }
.exp-cronjobs select {
    border: 1px solid var(--exp-line); border-radius: 6px;
    padding: .5rem .6rem; background: #fff; font: inherit; min-width: 14rem;
}

/* The output, above the list, out of the way until it is wanted. */
.exp-output { margin: 0 0 var(--exp-gap) 0; padding-bottom: .5rem; }
.exp-output-head {
    display: flex; align-items: center; gap: .7rem; margin: 0 0 .6rem 0;
}
.exp-output-head h2 { margin: 0; font-size: 1rem; }
.exp-output-toggle {
    display: inline-flex; align-items: center; justify-content: center;
    width: 1.25rem; height: 1.25rem; border: 1px solid var(--exp-line);
    border-radius: 4px; text-decoration: none; font-weight: 700;
    color: var(--exp-ink); background: #f4f4f5; line-height: 1;
}
.exp-output-toggle:hover { border-color: #9a9aa0; }
.exp-output.is-collapsed .exp-console,
.exp-output.is-collapsed .exp-collapse-body { display: none; }
.exp-collapse-body { padding-top: .35rem; }
.exp-collapse-body .exp-console-head { margin-top: 1.5rem; }
.exp-collapse-body > .exp-console-head:first-child { margin-top: 0; }

.exp-filter {
    display: flex; flex-wrap: wrap; align-items: center; gap: .75rem;
    margin: 0 0 1.25rem 0; padding: .6rem 0;
}
.exp-filter label { color: var(--exp-muted); }

/* The list. Row striping and the tight action columns come from the admin
   stylesheet; only what is particular to cronjobs is set here. */
/* The admin stylesheet packs list rows tight; these have two levels of
   heading and an icon column, so they need room to read. */
.exp-cronjobs table.list { margin: 0 0 var(--exp-gap) 0; }
.exp-cronjobs table.list td,
.exp-cronjobs table.list th { padding: var(--exp-cell-y) var(--exp-cell-x); vertical-align: middle; }
.exp-cronjobs table.list th { padding-top: .75rem; padding-bottom: .75rem; }
.exp-cronjobs table.list td.tight,
.exp-cronjobs table.list th.tight { white-space: nowrap; width: 1%; }
table.list.cronjobs td { vertical-align: middle; }
.exp-part-name { font-weight: 700; padding-top: 1.1rem !important; padding-bottom: 1.1rem !important; }
.exp-script-name { padding-left: 2.2rem !important; font-family: ui-monospace, SFMono-Regular, Menlo, monospace; font-size: .85em; }
.exp-part-row.is-current { box-shadow: inset 3px 0 0 var(--exp-accent); }
/* A part begins a group, so it is set off from the one that ended above it. */
.exp-cronjobs table.list tr.exp-part-row td { border-top: 2px solid var(--exp-line); }
.exp-cronjobs table.list tr.exp-script-row td { padding-top: .7rem; padding-bottom: .7rem; }
tr.exp-spare-row .exp-script-name { color: var(--exp-muted); }

.exp-state { font-size: .8em; white-space: nowrap; }
.exp-state.is-active { color: var(--exp-ok); }
.exp-state.is-blocked { color: var(--exp-muted); }
.exp-state.is-missing { color: var(--exp-bad); }
.exp-state.is-available { color: var(--exp-warn); }

.exp-icon-btn {
    border: 1px solid transparent; background: none; padding: .3rem .45rem;
    cursor: pointer; line-height: 0; border-radius: 4px;
}
.exp-icon-btn:hover:not([disabled]) { border-color: var(--exp-line); background: #f4f4f5; }
.exp-icon-btn[disabled] { opacity: .4; cursor: not-allowed; }
.exp-icon-btn img { vertical-align: middle; }

/* Section headings inside the list, so a change of subject reads as one. */
tr.exp-section-row th {
    background: #ececed; font-size: .95rem;
    padding-top: 1.25rem !important; padding-bottom: .5rem !important;
    border-top: 2px solid var(--exp-line);
}
tr.exp-section-note th {
    font-weight: 400; color: var(--exp-muted); font-size: .82em;
    background: #f6f6f7; padding-top: 0 !important; padding-bottom: .75rem !important;
}

.exp-crontab {
    background: #f6f6f7; border: 1px solid var(--exp-line);
    border-radius: var(--exp-radius); padding: 1rem 1.15rem; margin: 0 0 var(--exp-gap) 0;
    font: 12px/1.9 ui-monospace, SFMono-Regular, Menlo, monospace;
    white-space: pre-wrap; overflow-wrap: anywhere; color: #35353a;
}
.exp-note { color: var(--exp-muted); font-size: .8em; }

.exp-btn {
    font: inherit; font-weight: 600; cursor: pointer;
    border: 1px solid var(--exp-line); border-radius: 6px;
    background: #f4f4f5; color: var(--exp-ink); padding: .5rem 1.1rem;
}
.exp-btn:hover:not([disabled]) { border-color: #9a9aa0; background: #ececee; }
.exp-btn-primary { background: var(--exp-accent); border-color: var(--exp-accent); color: #fff; }
.exp-btn-primary:hover:not([disabled]) { background: #e64d00; border-color: #e64d00; }
.exp-btn[disabled] { opacity: .45; cursor: not-allowed; }

.exp-console {
    background: #16161a; color: #d8d8d8; border: 1px solid #35353a;
    border-radius: var(--exp-radius); padding: 1rem 1.15rem; margin: 0;
    height: 24em; overflow: auto; white-space: pre-wrap; word-break: break-word;
    font: 12px/1.55 ui-monospace, SFMono-Regular, Menlo, monospace;
}
.exp-console-head {
    display: flex; align-items: baseline; flex-wrap: wrap;
    gap: .35rem 1rem; margin: var(--exp-gap) 0 1rem 0;
    padding-bottom: .6rem; border-bottom: 1px solid var(--exp-line);
}
.exp-console-head h2 { margin: 0; font-size: 1rem; }
.exp-feedback { border-radius: var(--exp-radius); padding: .85rem 1.15rem; margin: 0 0 1.25rem 0; border: 1px solid; }
.exp-feedback.is-ok { border-color: #bfe3c1; background: #f1f9f1; color: var(--exp-ok); }
.exp-feedback.is-bad { border-color: #f0c4c0; background: #fdf3f2; color: var(--exp-bad); }

@media ( max-width: 40rem ) {
    .exp-head, .exp-controls, .exp-filter { flex-direction: column; align-items: stretch; }
}
</style>
{/literal}

<div class="context-block exp-cronjobs">

<div class="box-header"><div class="box-ml">
<h1 class="context-title">{'Cronjobs'|i18n( 'design/admin/setup/cronjobs' )}</h1>
</div></div>

<div class="box-bc"><div class="box-ml"><div class="box-content">

<div class="exp-head">
    <div>
        <p>{'Runs a cronjob part now, without waiting for the scheduler. The job is started as a separate process and keeps running after this page is closed, so nothing is lost if the browser goes away.'|i18n( 'design/admin/setup/cronjobs' )}</p>
    </div>
</div>

{foreach $cronjob_feedback as $cronjob_message}
<div class="exp-feedback {if $cronjob_message.ok}is-ok{else}is-bad{/if}">{$cronjob_message.message|wash}</div>
{/foreach}

{* What an action says when the console runs it in place. Empty and hidden
   until there is something to put in it. *}
<div class="exp-feedback" id="cronjob-feedback" style="display:none;"></div>

{if $cronjob_php_binary|eq('')}
<div class="exp-feedback is-bad">
    {'No php command line binary could be found, so nothing can be launched from here. Set cronjob.ini [AdminSettings] PhpCliPath to its full path.'|i18n( 'design/admin/setup/cronjobs' )}
</div>
{/if}

{* Everything that changes when a job starts or ends is addressable, so the
   console can bring the page up to date without fetching it again. *}
<div class="exp-status" id="cronjob-status">
    <span class="exp-pill{if $cronjob_status.running} is-running{/if}" id="cronjob-pill">
        <span class="exp-dot"></span>
        <span class="exp-pill-text">
        {if $cronjob_status.running}
            {'Running'|i18n( 'design/admin/setup/cronjobs' )}: {$cronjob_status.part|wash}
        {else}
            {'Idle'|i18n( 'design/admin/setup/cronjobs' )}
        {/if}
        </span>
    </span>
    {if $cronjob_status.running}
    <span class="exp-meta exp-running-meta">{'Site'|i18n( 'design/admin/setup/cronjobs' )}: {$cronjob_status.siteaccess|wash}</span>
    <span class="exp-meta exp-running-meta">{'Process'|i18n( 'design/admin/setup/cronjobs' )}: {$cronjob_status.pid}</span>
    <span class="exp-meta exp-running-meta" id="cronjob-elapsed">{'Elapsed'|i18n( 'design/admin/setup/cronjobs' )}: {$cronjob_status.elapsed}s</span>
    {/if}
    <span class="exp-meta">{'Log'|i18n( 'design/admin/setup/cronjobs' )}: <code>{$cronjob_log_file|wash}</code></span>
    {if $cronjob_php_binary|ne('')}
    <span class="exp-meta">php: <code>{$cronjob_php_binary|wash}</code></span>
    {/if}
</div>

<form method="post" action={'setup/cronjobs'|ezurl}>

<div class="exp-controls">
    <div class="exp-field">
        <label for="cronjob-siteaccess">{'Run for site'|i18n( 'design/admin/setup/cronjobs' )}</label>
        {* A cronjob acts on content, and which content depends on the siteaccess
           it runs under, so this is asked rather than assumed. *}
        <select id="cronjob-siteaccess" name="CronjobSiteAccess">
        {foreach $cronjob_siteaccess_list as $cronjob_siteaccess}
            <option value="{$cronjob_siteaccess|wash}"{if eq( $cronjob_siteaccess, $cronjob_default_siteaccess )} selected="selected"{/if}>{$cronjob_siteaccess|wash}</option>
        {/foreach}
        </select>
    </div>
    <button type="submit" class="exp-btn" id="cronjob-stop" name="StopCronjobButton" value="1"{if $cronjob_status.running|not} disabled="disabled"{/if}>{'Stop running job'|i18n( 'design/admin/setup/cronjobs' )}</button>
    <button type="submit" class="exp-btn" id="cronjob-clear" name="ClearCronjobLogButton" value="1">{'Clear logs'|i18n( 'design/admin/setup/cronjobs' )}</button>
</div>

{* The output, above the list and out of the way until it is wanted.
   Hidden until something runs, and collapsible once it is. *}
<div class="exp-output" id="cronjob-output" style="display:none;">
    <div class="exp-output-head">
        <a href="#" id="cronjob-output-toggle" class="exp-output-toggle" data-collapses="cronjob-output" title="{'Show or hide the output'|i18n( 'design/admin/setup/cronjobs' )}">&minus;</a>
        <h2>{'Output'|i18n( 'design/admin/setup/cronjobs' )}</h2>
        <span class="exp-meta" id="cronjob-stream-status"></span>
    </div>
    <pre class="exp-console" id="cronjob-console">{$cronjob_log|wash}{if $cronjob_errors|ne('')}
{$cronjob_errors|wash}{/if}</pre>
</div>

{* Which part to look at, and what the Run beside it will run. Choosing one
   narrows the list to it; "All parts" puts everything back and makes Run mean
   every part in turn. *}
<div class="context-toolbar exp-filter">
    <label for="cronjob-part-filter">{'Cronjob part'|i18n( 'design/admin/setup/cronjobs' )}:</label>
    <select id="cronjob-part-filter">
        <option value="">{'All parts'|i18n( 'design/admin/setup/cronjobs' )}</option>
    {foreach $cronjob_parts as $cronjob_option}
        <option value="{$cronjob_option.name|wash}">{$cronjob_option.label|wash}</option>
    {/foreach}
    </select>
    <button type="button" class="exp-btn exp-btn-primary" id="cronjob-run-selected"><img
            src={'run.png'|ezimage} width="16" height="16" alt="" />&nbsp;{'Run'|i18n( 'design/admin/setup/cronjobs' )}</button>
    <span class="exp-meta" id="cronjob-run-hint">{'Runs every part, one after another.'|i18n( 'design/admin/setup/cronjobs' )}</span>
</div>

{* One line per cronjob, grouped under the part that runs it. A part can be run
   whole from its own row, a script on its own from its row, and the whole lot
   in order from the control above. *}
<table class="list cronjobs" cellspacing="0" id="cronjob-list">
<tr>
    <th>{'Cronjob part / script'|i18n( 'design/admin/setup/cronjobs' )}</th>
    <th>{'Found in'|i18n( 'design/admin/setup/cronjobs' )}</th>
    <th class="tight">{'Crontab'|i18n( 'design/admin/setup/cronjobs' )}</th>
    <th class="tight">{'State'|i18n( 'design/admin/setup/cronjobs' )}</th>
    <th class="tight">{'Run'|i18n( 'design/admin/setup/cronjobs' )}</th>
</tr>
{foreach $cronjob_parts as $cronjob_part sequence array( bglight, bgdark ) as $cronjob_seq}
    {* Blocked for good - forbidden, scripts missing, no php - is not the same
       as blocked because something else is running, which stops being true the
       moment the console sees the job finish. Each row records which it is so
       the console knows what it may re-enable. *}
    {def $cronjob_blocked = or( $cronjob_part.forbidden,
                                $cronjob_part.missing|ge( $cronjob_part.scripts|count ),
                                $cronjob_php_binary|eq('') )
         $cronjob_runnable = and( $cronjob_blocked|not, $cronjob_status.running|not )}
<tr class="{$cronjob_seq} exp-part-row" data-part="{$cronjob_part.name|wash}">
    <td class="exp-part-name">{$cronjob_part.label|wash}</td>
    <td class="exp-meta">{$cronjob_part.scripts|count} {'scripts'|i18n( 'design/admin/setup/cronjobs' )}</td>
    {* What the crontab actually says about this part, not what it could say.
       The line to add is listed below the table, where it can be read. *}
    <td class="tight">
        {if $cronjob_part.scheduled}
            <span class="exp-state is-active" title="{'A crontab entry for this installation runs this part'|i18n( 'design/admin/setup/cronjobs' )}">{'scheduled'|i18n( 'design/admin/setup/cronjobs' )}</span>
        {else}
            <span class="exp-state is-available" title="{'Nothing in the crontab runs this part'|i18n( 'design/admin/setup/cronjobs' )}">{'not scheduled'|i18n( 'design/admin/setup/cronjobs' )}</span>
        {/if}
    </td>
    <td class="tight">
        {if $cronjob_part.forbidden}
            <span class="exp-state is-blocked" title="{'Blocked by cronjob.ini ForbiddenParts'|i18n( 'design/admin/setup/cronjobs' )}">{'Blocked'|i18n( 'design/admin/setup/cronjobs' )}</span>
        {elseif $cronjob_part.missing|gt(0)}
            <span class="exp-state is-missing">{$cronjob_part.missing} {'missing'|i18n( 'design/admin/setup/cronjobs' )}</span>
        {else}
            <span class="exp-state is-active">{'Activated'|i18n( 'design/admin/setup/cronjobs' )}</span>
        {/if}
    </td>
    <td class="tight">
        {* The button carries the part name as its own value, so one form serves
           every row and launching needs no javascript. *}
        <button type="submit" class="exp-icon-btn exp-run" name="LaunchCronjobButton"
                data-blocked="{if $cronjob_blocked}1{else}0{/if}"
                value="{$cronjob_part.name|wash}"{if $cronjob_runnable|not} disabled="disabled"{/if}
                title="{'Run the whole %part part now'|i18n( 'design/admin/setup/cronjobs',, hash( '%part', $cronjob_part.label ) )|wash}"><img
                src={'run.png'|ezimage} width="16" height="16" alt="{'Run'|i18n( 'design/admin/setup/cronjobs' )}" /></button>
    </td>
</tr>
    {foreach $cronjob_part.scripts as $cronjob_script}
<tr class="{$cronjob_seq} exp-script-row" data-part="{$cronjob_part.name|wash}">
    <td class="exp-script-name">{$cronjob_script.name|wash}</td>
    <td class="exp-meta">{if $cronjob_script.path|eq(false())}<span class="exp-state is-missing">{'not found in any cronjob directory'|i18n( 'design/admin/setup/cronjobs' )}</span>{else}{$cronjob_script.directory|wash}{/if}</td>
    <td class="tight">&nbsp;</td>
    <td class="tight"><span class="exp-meta">{if $cronjob_script.path|eq(false())}&mdash;{else}{'Activated'|i18n( 'design/admin/setup/cronjobs' )}{/if}</span></td>
    <td class="tight">
        {if and( $cronjob_script.path|ne(false()), $cronjob_part.forbidden|not )}
        <button type="submit" class="exp-icon-btn exp-run" name="LaunchCronjobScriptButton"
                data-blocked="{if $cronjob_php_binary|eq('')}1{else}0{/if}"
                value="{$cronjob_part.name|wash}|{$cronjob_script.name|wash}"{if or( $cronjob_status.running, $cronjob_php_binary|eq('') )} disabled="disabled"{/if}
                title="{'Run %script on its own'|i18n( 'design/admin/setup/cronjobs',, hash( '%script', $cronjob_script.name ) )|wash}"><img
                src={'run.png'|ezimage} width="16" height="16" alt="{'Run'|i18n( 'design/admin/setup/cronjobs' )}" /></button>
        {else}
        <img src={'run-disabled.png'|ezimage} width="16" height="16" alt="" title="{'This script cannot be run from here'|i18n( 'design/admin/setup/cronjobs' )}" />
        {/if}
    </td>
</tr>
    {/foreach}
    {undef $cronjob_runnable $cronjob_blocked}
{/foreach}

{* On disk, named by no part, so nothing ever runs them. *}
{if $cronjob_available_scripts|count|gt(0)}
<tr class="exp-section-row">
    <th colspan="5">{'Available but not activated'|i18n( 'design/admin/setup/cronjobs' )}</th>
</tr>
<tr class="exp-section-note">
    <th colspan="5">{'These scripts exist but no cronjob part names them, so they never run.'|i18n( 'design/admin/setup/cronjobs' )}</th>
</tr>
<tr>
    <th>{'Script'|i18n( 'design/admin/setup/cronjobs' )}</th>
    <th>{'Found in'|i18n( 'design/admin/setup/cronjobs' )}</th>
    <th class="tight">&nbsp;</th>
    <th class="tight">{'State'|i18n( 'design/admin/setup/cronjobs' )}</th>
    <th class="tight">&nbsp;</th>
</tr>
    {foreach $cronjob_available_scripts as $cronjob_spare sequence array( bglight, bgdark ) as $cronjob_spare_seq}
<tr class="{$cronjob_spare_seq} exp-spare-row">
    <td class="exp-script-name">{$cronjob_spare.name|wash}</td>
    <td class="exp-meta">{$cronjob_spare.directory|wash}</td>
    <td class="tight">&nbsp;</td>
    <td class="tight"><span class="exp-state is-available">{'Available'|i18n( 'design/admin/setup/cronjobs' )}</span></td>
    <td class="tight"><img src={'run-disabled.png'|ezimage} width="16" height="16" alt="" title="{'Add it to a part in cronjob.ini to run it'|i18n( 'design/admin/setup/cronjobs' )}" /></td>
</tr>
    {/foreach}
{/if}
</table>

</form>

{* What has run, when, and whether it complained. *}
<div class="exp-console-head">
    <h2>{'Recent runs'|i18n( 'design/admin/setup/cronjobs' )}</h2>
</div>
{if $cronjob_history|count|gt(0)}
<table class="list" cellspacing="0">
<tr>
    <th>{'Cronjob'|i18n( 'design/admin/setup/cronjobs' )}</th>
    <th>{'Site'|i18n( 'design/admin/setup/cronjobs' )}</th>
    <th>{'Started'|i18n( 'design/admin/setup/cronjobs' )}</th>
    <th class="tight">{'Took'|i18n( 'design/admin/setup/cronjobs' )}</th>
    <th class="tight">{'Errors'|i18n( 'design/admin/setup/cronjobs' )}</th>
</tr>
{foreach $cronjob_history as $cronjob_run sequence array( bglight, bgdark ) as $cronjob_run_seq}
<tr class="{$cronjob_run_seq}">
    <td>{$cronjob_run.label|wash}</td>
    <td class="exp-meta">{$cronjob_run.siteaccess|wash}</td>
    <td class="exp-meta">{$cronjob_run.started|l10n( shortdatetime )}</td>
    <td class="tight exp-meta">{if $cronjob_run.running}{'running'|i18n( 'design/admin/setup/cronjobs' )}{else}{$cronjob_run.seconds}s{/if}</td>
    <td class="tight">{if $cronjob_run.errors|gt(0)}<span class="exp-state is-missing">{$cronjob_run.errors}</span>{else}<span class="exp-meta">0</span>{/if}</td>
</tr>
{/foreach}
</table>
{else}
<p class="exp-meta">{'Nothing has been run from here yet.'|i18n( 'design/admin/setup/cronjobs' )}</p>
{/if}

{* Last on the page and folded away: useful when setting the schedule up, noise
   every other time. Same container and same toggle as the output above. *}
<div class="exp-output is-collapsed" id="cronjob-crontab-block">
    <div class="exp-output-head">
        <a href="#" class="exp-output-toggle" data-collapses="cronjob-crontab-block" title="{'Show or hide the crontab'|i18n( 'design/admin/setup/cronjobs' )}">+</a>
        <h2>{'Crontab'|i18n( 'design/admin/setup/cronjobs' )}</h2>
        <span class="exp-meta">{'What is scheduled now, and the lines that would schedule the rest'|i18n( 'design/admin/setup/cronjobs' )}</span>
    </div>
    <div class="exp-collapse-body">
        {* What is really in the crontab, read from it. *}
        <div class="exp-console-head">
            <h2>{'In the crontab now'|i18n( 'design/admin/setup/cronjobs' )}</h2>
            <span class="exp-meta">{'Read from crontab -l for the user this site runs as.'|i18n( 'design/admin/setup/cronjobs' )}</span>
        </div>
        {if $cronjob_crontab.available}
            {if $cronjob_crontab.lines|count|gt(0)}
        <pre class="exp-crontab">{$cronjob_crontab_current|wash}</pre>
            {else}
        <p class="exp-meta">{'The crontab is empty.'|i18n( 'design/admin/setup/cronjobs' )}</p>
            {/if}
        {else}
        <p class="exp-meta">{$cronjob_crontab.note|wash}</p>
        {/if}

        {* And what would schedule the parts that nothing schedules. Generated here from
           this installation's own paths and php binary - not read from anywhere. *}
        <div class="exp-console-head">
            <h2>{'Suggested entries'|i18n( 'design/admin/setup/cronjobs' )}</h2>
            <span class="exp-meta">{'Written by this page from the paths below, for the parts nothing currently runs. Nothing adds them for you.'|i18n( 'design/admin/setup/cronjobs' )}</span>
        </div>
        <pre class="exp-crontab">{$cronjob_crontab_suggested|wash}</pre>
        <p class="exp-meta">{'Installation'|i18n( 'design/admin/setup/cronjobs' )}: <code>{$cronjob_root|wash}</code></p>
    </div>
</div>

{* Template values are read before the literal block, because everything inside
   one is passed through untouched - which is the point, since javascript is
   full of braces the template engine would otherwise try to parse. *}
<script type="text/javascript">
var expCronjobActionUrl = {'setup/cronjobs'|ezurl()};
var expCronjobTokenField = '{$cronjob_form_field|wash}';
var expCronjobToken      = '{$cronjob_form_token|wash}';
var expCronjobStreamUrl = {$cronjob_stream_url|ezurl()};
var expCronjobRunning   = {if $cronjob_status.running}true{else}false{/if};
var expCronjobOffset    = {$cronjob_log_offset};
{literal}
(function () {
    var consoleEl = document.getElementById( 'cronjob-console' );
    var outputEl  = document.getElementById( 'cronjob-output' );
    var statusEl  = document.getElementById( 'cronjob-stream-status' );
    var stopBtn   = document.getElementById( 'cronjob-stop' );
    var clearBtn  = document.getElementById( 'cronjob-clear' );
    var saEl      = document.getElementById( 'cronjob-siteaccess' );
    var feedEl    = document.getElementById( 'cronjob-feedback' );
    if ( !consoleEl || typeof window.EventSource === 'undefined' )
        return;   // The form posts on its own; nothing below is needed.

    var colours = {
        phase: '#7fd1ff', ok: '#d8d8d8', warn: '#e8c765',
        error: '#ff8a80', info: '#9a9a9a', done: '#7fd1ff'
    };
    var source = null;
    var elapsedTimer = null;
    var offset = expCronjobOffset;

    function each( selector, fn ) {
        var nodes = document.querySelectorAll( selector ), i;
        for ( i = 0; i < nodes.length; i++ ) fn( nodes[i] );
    }

    function text( el, value ) {
        el.innerHTML = '';
        el.appendChild( document.createTextNode( value ) );
    }

    function write( type, line ) {
        var atBottom = consoleEl.scrollTop + consoleEl.clientHeight >= consoleEl.scrollHeight - 8;
        var el = document.createElement( 'div' );
        el.style.color = colours[type] || '#d8d8d8';
        if ( type === 'phase' || type === 'done' ) el.style.fontWeight = 'bold';
        el.appendChild( document.createTextNode( line ) );
        consoleEl.appendChild( el );
        if ( atBottom ) consoleEl.scrollTop = consoleEl.scrollHeight;
    }

    function say( value ) { text( statusEl, value ); }

    function feedback( ok, message ) {
        if ( !feedEl ) return;
        feedEl.className = 'exp-feedback ' + ( ok ? 'is-ok' : 'is-bad' );
        feedEl.style.display = 'block';
        text( feedEl, message );
    }

    function pillText( value, running ) {
        var pill = document.getElementById( 'cronjob-pill' );
        if ( !pill ) return;
        pill.className = 'exp-pill' + ( running ? ' is-running' : '' );
        var label = pill.getElementsByClassName( 'exp-pill-text' )[0];
        if ( label ) text( label, value );
    }

    // The page is never fetched again, in either direction. Everything an
    // action or a finished job changes is on this page already, so it is
    // changed here. Reloading was not just unnecessary: a reload repeats the
    // request that produced the page, so the form post that started a job was
    // offered for resending, and confirming launched it again - and again.
    function markIdle() {
        pillText( 'Idle', false );
        each( '.exp-running-meta', function ( el ) { el.parentNode.removeChild( el ); } );
        each( '.exp-note-busy', function ( el ) { el.parentNode.removeChild( el ); } );
        each( '.exp-part-row.is-current', function ( el ) {
            el.className = el.className.replace( / ?is-current/, '' );
        } );
        if ( stopBtn ) stopBtn.disabled = true;
        // Only the ones that were waiting on this job. A part that is forbidden,
        // missing its scripts, or has no php to run with stays disabled.
        each( '.exp-run', function ( el ) {
            if ( el.getAttribute( 'data-blocked' ) !== '1' ) el.disabled = false;
        } );
        if ( elapsedTimer ) { window.clearInterval( elapsedTimer ); elapsedTimer = null; }
    }

    function markRunning( part, siteaccess, pid ) {
        pillText( 'Running: ' + part, true );

        var strip = document.getElementById( 'cronjob-status' );
        if ( strip ) {
            each( '.exp-running-meta', function ( el ) { el.parentNode.removeChild( el ); } );
            var meta = [ [ 'Site: ' + siteaccess, null ],
                         [ 'Process: ' + pid, null ],
                         [ 'Elapsed: 0s', 'cronjob-elapsed' ] ];
            for ( var i = 0; i < meta.length; i++ ) {
                var span = document.createElement( 'span' );
                span.className = 'exp-meta exp-running-meta';
                if ( meta[i][1] ) span.id = meta[i][1];
                span.appendChild( document.createTextNode( meta[i][0] ) );
                strip.appendChild( span );
            }
        }

        each( '.exp-run', function ( el ) { el.disabled = true; } );
        if ( stopBtn ) stopBtn.disabled = false;

        each( '.exp-part-row', function ( el ) {
            if ( el.getAttribute( 'data-part' ) === part && el.className.indexOf( 'is-current' ) === -1 )
                el.className += ' is-current';
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
            text( el, 'Elapsed: ' + ( from + Math.round( ( new Date().getTime() - openedAt ) / 1000 ) ) + 's' );
        }, 1000 );
    }

    function follow() {
        if ( source ) return;
        say( 'following…' );
        source = new EventSource( expCronjobStreamUrl + '?Offset=' + offset );

        source.onmessage = function ( event ) {
            var payload;
            try { payload = JSON.parse( event.data ); }
            catch ( e ) { return; }
            if ( typeof payload.offset === 'number' ) offset = payload.offset;
            write( payload.type, payload.message );
            if ( payload.type === 'done' ) { close( 'finished' ); afterFinished(); }
        };

        source.addEventListener( 'end', function () { close( 'finished' ); afterFinished(); } );

        source.onerror = function () {
            // EventSource reconnects by itself, which would start the tail over
            // from the offset this page was rendered with and reprint it all.
            close( 'stream closed' );
        };
    }

    function close( message ) {
        if ( source ) { source.close(); source = null; }
        say( message );
    }

    // Reads an answer out of whatever came back.
    //
    // Anything appended to the json - a debug block, a warning, a notice -
    // would otherwise make the whole reply unparseable, so the first balanced
    // object in the text is taken if parsing the lot fails. When there is no
    // json at all the text itself is shown, trimmed of markup, because "the
    // server did not answer as expected" tells an operator nothing they can
    // act on while the page they were actually sent usually says exactly what
    // went wrong.
    function readAnswer( text ) {
        if ( !text ) return null;

        try { return JSON.parse( text ); } catch ( e ) {}

        var start = text.indexOf( '{' );
        while ( start !== -1 ) {
            var depth = 0, i;
            for ( i = start; i < text.length; i++ ) {
                if ( text.charAt( i ) === '{' ) depth++;
                else if ( text.charAt( i ) === '}' ) {
                    depth--;
                    if ( depth === 0 ) {
                        try { return JSON.parse( text.substring( start, i + 1 ) ); }
                        catch ( e ) {}
                        break;
                    }
                }
            }
            start = text.indexOf( '{', start + 1 );
        }

        return null;
    }

    function describeFailure( request ) {
        if ( request.status === 0 )
            return 'The request did not reach the server. Check the connection and try again.';

        var text = ( request.responseText || '' )
                       .replace( /<script[\s\S]*?<\/script>/gi, ' ' )
                       .replace( /<style[\s\S]*?<\/style>/gi, ' ' )
                       .replace( /<[^>]*>/g, ' ' )
                       .replace( /\s+/g, ' ' ).trim();
        if ( text.length > 240 ) text = text.substring( 0, 240 ) + '…';

        return 'The server answered ' + request.status +
               ( text ? ': ' + text : '. It sent nothing that could be read.' );
    }

    // An action asks for its answer rather than a new page. The same view does
    // the same work either way; only the reply differs.
    function act( fields, onDone ) {
        var body = 'Ajax=1', key;
        for ( key in fields )
            body += '&' + encodeURIComponent( key ) + '=' + encodeURIComponent( fields[key] );

        // ezformtoken refuses a post from a logged in user that does not carry
        // its token - by throwing, so the reply is an error page rather than an
        // answer. Sent both ways it accepts: in the field it looks for, and in
        // the header it offers for requests that are not forms.
        if ( expCronjobToken && expCronjobTokenField )
            body += '&' + encodeURIComponent( expCronjobTokenField ) + '=' + encodeURIComponent( expCronjobToken );

        var request = new XMLHttpRequest();
        request.open( 'POST', expCronjobActionUrl, true );
        request.setRequestHeader( 'Content-Type', 'application/x-www-form-urlencoded' );
        request.setRequestHeader( 'X-Requested-With', 'XMLHttpRequest' );
        if ( expCronjobToken )
            request.setRequestHeader( 'X-CSRF-Token', expCronjobToken );

        var finished = false;
        function settle( answer ) {
            if ( finished ) return;
            finished = true;
            feedback( answer.ok, answer.message );
            try { onDone( answer ); }
            catch ( e ) {}
            // Whatever happened, the controls must not be left disabled with
            // nothing running.
            if ( !answer.ok && !answer.running ) markIdle();
        }

        request.onreadystatechange = function () {
            if ( request.readyState !== 4 ) return;
            var answer = readAnswer( request.responseText );
            if ( answer && typeof answer.ok !== 'undefined' ) { settle( answer ); return; }
            settle( { ok: false, running: false, message: describeFailure( request ) } );
        };
        request.onerror = function () {
            settle( { ok: false, running: false,
                      message: 'The request failed before the server could answer.' } );
        };
        request.ontimeout = function () {
            settle( { ok: false, running: false,
                      message: 'The server did not answer in time. The job may still have started; reload to see.' } );
        };
        request.timeout = 120000;

        try { request.send( body ); }
        catch ( e ) {
            settle( { ok: false, running: false, message: 'The request could not be sent: ' + e.message } );
        }
    }

    // With no token there is nothing to prove the request with, and posting in
    // the background would only be refused. The button is left alone so it
    // submits the form, which carries a token of its own; the page comes back
    // through the redirect and shows what happened. Slower, never broken.
    var canPostInBackground = !!expCronjobToken;

    // What is still to be run, when a whole list of parts was asked for.
    var queue = [];

    function showOutput() {
        if ( outputEl ) {
            outputEl.style.display = 'block';
            outputEl.className = outputEl.className.replace( / ?is-collapsed/, '' );
            var outputToggle = document.getElementById( 'cronjob-output-toggle' );
            if ( outputToggle ) text( outputToggle, '\u2212' );
        }
    }

    // Starts one thing: a part on its own, or one script of a part.
    function launch( fields, part, thenQueue ) {
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
        write( 'phase', 'Next: ' + next );
        window.setTimeout( function () {
            launch( { LaunchCronjobButton: next }, next );
        }, 400 );
    }

    each( '.exp-run', function ( button ) {
        button.onclick = function ( event ) {
            if ( !canPostInBackground ) return true;
            if ( event && event.preventDefault ) event.preventDefault();

            queue = [];
            var value = button.value;
            var fields = {};
            var part = value;

            if ( button.name === 'LaunchCronjobScriptButton' ) {
                fields.LaunchCronjobScriptButton = value;
                part = value.split( '|' )[0];
            } else {
                fields.LaunchCronjobButton = value;
            }

            launch( fields, part );
            return false;
        };
    } );

    // The dropdown does two things: it narrows the list to one part, and it
    // decides what the Run beside it will run.
    var filterEl = document.getElementById( 'cronjob-part-filter' );
    var runSelectedEl = document.getElementById( 'cronjob-run-selected' );
    var runHintEl = document.getElementById( 'cronjob-run-hint' );

    function applyFilter() {
        var wanted = filterEl ? filterEl.value : '';
        each( '#cronjob-list tr[data-part]', function ( row ) {
            row.style.display = ( wanted === '' || row.getAttribute( 'data-part' ) === wanted ) ? '' : 'none';
        } );
        if ( runHintEl )
            text( runHintEl, wanted === ''
                ? 'Runs every part, one after another.'
                : 'Runs the ' + ( filterEl.options[filterEl.selectedIndex].text ) + ' part.' );
    }

    if ( filterEl ) {
        filterEl.onchange = applyFilter;
        applyFilter();
    }

    if ( runSelectedEl ) {
        runSelectedEl.onclick = function ( event ) {
            if ( event && event.preventDefault ) event.preventDefault();
            if ( !canPostInBackground ) return false;

            var wanted = filterEl ? filterEl.value : '';
            if ( wanted !== '' ) {
                queue = [];
                launch( { LaunchCronjobButton: wanted }, wanted );
                return false;
            }

            // Every part it is allowed to run, one after another, so only one
            // cronjob is ever going at a time.
            queue = [];
            each( '.exp-part-row', function ( row ) {
                var button = row.getElementsByClassName( 'exp-run' )[0];
                if ( button && button.getAttribute( 'data-blocked' ) !== '1' )
                    queue.push( row.getAttribute( 'data-part' ) );
            } );
            if ( !queue.length ) { feedback( false, 'There is no part that can be run.' ); return false; }

            showOutput();
            write( 'phase', 'Running ' + queue.length + ' parts, one after another.' );
            var first = queue.shift();
            launch( { LaunchCronjobButton: first }, first );
            return false;
        };
    }

    // Minimising the output leaves the heading, so it can be brought back.
    // Every toggle names the container it folds, so the output at the top and
    // the crontab at the bottom behave the same without two copies of this.
    var toggles = document.getElementsByTagName( 'a' );
    for ( var t = 0; t < toggles.length; t++ ) {
        var folds = toggles[t].getAttribute && toggles[t].getAttribute( 'data-collapses' );
        if ( !folds ) continue;
        ( function ( toggleEl, panelEl ) {
            if ( !panelEl ) return;
            toggleEl.onclick = function ( event ) {
                if ( event && event.preventDefault ) event.preventDefault();
                var collapsed = panelEl.className.indexOf( 'is-collapsed' ) !== -1;
                panelEl.className = collapsed
                    ? panelEl.className.replace( / ?is-collapsed/, '' )
                    : panelEl.className + ' is-collapsed';
                text( toggleEl, collapsed ? '\u2212' : '+' );
                return false;
            };
        } )( toggles[t], document.getElementById( folds ) );
    }

    if ( stopBtn ) {
        stopBtn.onclick = function ( event ) {
            if ( !canPostInBackground ) return true;
            if ( event && event.preventDefault ) event.preventDefault();
            act( { StopCronjobButton: '1' }, function () {} );
            return false;   // The stream sees the job end and settles the rest.
        };
    }

    if ( clearBtn ) {
        clearBtn.onclick = function ( event ) {
            if ( !canPostInBackground ) return true;
            if ( event && event.preventDefault ) event.preventDefault();
            act( { ClearCronjobLogButton: '1' }, function () {
                consoleEl.innerHTML = '';
                offset = 0;
            } );
            return false;
        };
    }

    consoleEl.scrollTop = consoleEl.scrollHeight;

    // Only follow when something is actually running. With nothing running the
    // page already shows what the log holds, and opening a stream would hold a
    // php worker for no reason.
    if ( expCronjobRunning ) {
        showOutput();
        var el = document.getElementById( 'cronjob-elapsed' );
        var from = 0;
        if ( el ) {
            from = parseInt( ( el.textContent || el.innerText ).replace( /[^0-9]/g, '' ), 10 );
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
