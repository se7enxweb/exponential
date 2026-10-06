{* The cronjobs console.

   One page for running cronjobs and seeing how they are doing: what is running now, a short overview, the
   controls, then one card per cronjob part with its schedule, its next and last run, the command that runs it
   from a shell and its scripts with what each one does. Below the parts: scripts no part names, the recent runs,
   and the crontab.

   The same file is in design/admin and design/admin4, so every administration design resolves it. Its styling
   is scoped to .exp-cronjobs and takes admin4's tokens where they exist (--a4-*), with values of its own for the
   older designs. Everything works without javascript: every run, stop and clear button submits the form, the
   folding sections are <details>. The script below adds running in place, following the output as it is
   written, the filters and the copy buttons. *}

{literal}
<style>
.exp-cronjobs {
    --cj-ink: var(--a4-ink, #1f2430);
    --cj-muted: var(--a4-muted, #5d6573);
    --cj-line: var(--a4-line, #e3e6eb);
    --cj-soft: var(--a4-soft, #f6f7f9);
    --cj-card: #fff;
    --cj-accent: #c2410c;          /* white text on it is 5.2:1 */
    --cj-accent-hover: #9a3412;
    --cj-ring: rgba(194, 65, 12, 0.45);
    --cj-ok: #166534;   --cj-ok-bg: #e7f5ea;
    --cj-warn: #8a4b00; --cj-warn-bg: #fff3df;
    --cj-bad: #b91c1c;  --cj-bad-bg: #fdecec;
    --cj-info: #1e4fa8; --cj-info-bg: #e8effd;
    --cj-radius: 12px;
    --cj-mono: ui-monospace, SFMono-Regular, Menlo, Consolas, monospace;
    color: var(--cj-ink);
    font-size: 14px;
    line-height: 1.5;
}
.exp-cronjobs *, .exp-cronjobs *::before, .exp-cronjobs *::after { box-sizing: border-box; }
.exp-cronjobs [hidden] { display: none !important; }
.exp-cronjobs .box-content { padding-bottom: 24px; }
.exp-cronjobs h1.context-title { margin: 0; }
.exp-cronjobs h2.exp-h2 { margin: 0; padding: 0; border: 0; background: none; font-size: 16px; font-weight: 650; color: var(--cj-ink); }
.exp-cronjobs h3 { margin: 0; padding: 0; border: 0; background: none; font-size: 15px; font-weight: 650; color: var(--cj-ink); }
.exp-cronjobs p { margin: 0; }
.exp-cronjobs code { font-family: var(--cj-mono); font-size: 12.5px; overflow-wrap: anywhere; }
.exp-cronjobs :focus-visible { outline: 3px solid var(--cj-ring); outline-offset: 2px; }
.exp-sr { position: absolute; width: 1px; height: 1px; margin: -1px; padding: 0; overflow: hidden; clip: rect(0 0 0 0); white-space: nowrap; border: 0; }
.exp-muted { color: var(--cj-muted); }

.exp-intro { margin: 10px 0 18px; max-width: 72ch; color: var(--cj-muted); }
.exp-section { margin: 0 0 26px; }
.exp-section-head { display: flex; flex-wrap: wrap; align-items: baseline; gap: 4px 12px; margin: 0 0 10px; }

/* Messages */
.exp-feedback { margin: 0 0 14px; padding: 10px 14px; border: 1px solid; border-left-width: 4px; border-radius: 10px; }
.exp-feedback.is-ok { border-color: #b7dfc1; border-left-color: var(--cj-ok); background: var(--cj-ok-bg); color: var(--cj-ok); }
.exp-feedback.is-bad { border-color: #f1b4b4; border-left-color: var(--cj-bad); background: var(--cj-bad-bg); color: var(--cj-bad); }

/* What is running now */
.exp-statusbar {
    display: flex; flex-wrap: wrap; align-items: center; justify-content: space-between; gap: 12px 20px;
    margin: 0 0 14px; padding: 14px 16px; border: 1px solid var(--cj-line); border-radius: var(--cj-radius); background: var(--cj-soft);
}
.exp-status { display: flex; flex-wrap: wrap; align-items: center; gap: 6px 18px; min-width: 0; }
.exp-pill { display: inline-flex; align-items: center; gap: 8px; font-weight: 700; }
.exp-dot { flex: 0 0 auto; width: 10px; height: 10px; border-radius: 50%; background: #8a919c; }
.exp-pill.is-running { color: var(--cj-ok); }
.exp-pill.is-running .exp-dot { background: var(--cj-ok); box-shadow: 0 0 0 4px rgba(22, 101, 52, 0.18); animation: exp-pulse 1.4s ease-in-out infinite; }
@keyframes exp-pulse { 0%, 100% { opacity: 1; } 50% { opacity: .35; } }
@media (prefers-reduced-motion: reduce) { .exp-pill.is-running .exp-dot { animation: none; } }
.exp-meta { color: var(--cj-muted); font-size: 13px; }
.exp-status-actions { display: flex; flex-wrap: wrap; gap: 8px; }
.exp-paths { display: flex; flex-wrap: wrap; gap: 4px 18px; margin: -6px 0 18px; font-size: 12.5px; color: var(--cj-muted); }
.exp-paths code { overflow-wrap: anywhere; color: var(--cj-ink); }

/* Overview figures */
.exp-figures { display: grid; grid-template-columns: repeat(auto-fit, minmax(118px, 1fr)); gap: 10px; margin: 0 0 22px; padding: 0; list-style: none; }
.exp-figure { display: flex; flex-direction: column; gap: 2px; margin: 0; padding: 12px 14px; border: 1px solid var(--cj-line); border-radius: var(--cj-radius); background: var(--cj-card); }
.exp-figure strong { font-size: 22px; line-height: 1.15; font-weight: 700; font-variant-numeric: tabular-nums; color: var(--cj-ink); }
.exp-figure span { font-size: 12.5px; color: var(--cj-muted); }
.exp-figure.is-attention strong { color: var(--cj-bad); }
.exp-figure.is-last { grid-column: span 2; }
.exp-figure.is-last strong { font-size: 15px; line-height: 1.35; overflow-wrap: anywhere; }
@media (max-width: 520px) { .exp-figure.is-last { grid-column: 1 / -1; } }

/* Buttons */
.exp-btn {
    display: inline-flex; align-items: center; justify-content: center; gap: 6px; min-height: 36px; margin: 0;
    padding: 6px 14px; border: 1px solid #c9ced6; border-radius: 9px; background: #fff; color: var(--cj-ink);
    font: 600 13.5px/1.2 inherit; font-family: inherit; cursor: pointer; white-space: nowrap; text-decoration: none;
}
.exp-btn:hover:not([disabled]) { border-color: var(--cj-accent); color: var(--cj-accent-hover); }
.exp-btn[disabled] { opacity: .5; cursor: not-allowed; }
.exp-btn-primary { border-color: var(--cj-accent); background: var(--cj-accent); color: #fff; }
.exp-btn-primary:hover:not([disabled]) { border-color: var(--cj-accent-hover); background: var(--cj-accent-hover); color: #fff; }
.exp-btn-outline { border-color: var(--cj-accent); color: var(--cj-accent-hover); }
.exp-btn-outline:hover:not([disabled]) { background: var(--cj-accent); color: #fff; }
.exp-btn-small { min-height: 30px; padding: 4px 10px; font-size: 12.5px; }
.exp-btn-quiet { border-color: transparent; background: transparent; color: var(--cj-accent-hover); }
.exp-btn-quiet:hover:not([disabled]) { border-color: var(--cj-line); background: var(--cj-soft); }
.exp-btn svg { flex: 0 0 auto; }

/* Controls */
.exp-toolbar {
    display: grid; grid-template-columns: repeat(auto-fit, minmax(min(100%, 210px), 1fr)); gap: 14px 16px; align-items: end;
    margin: 0 0 22px; padding: 16px; border: 1px solid var(--cj-line); border-radius: var(--cj-radius); background: var(--cj-card);
}
.exp-field { display: flex; flex-direction: column; gap: 5px; min-width: 0; margin: 0; padding: 0; border: 0; }
.exp-cronjobs fieldset.exp-field { background: none; box-shadow: none; border-radius: 0; }
.exp-cronjobs fieldset.exp-field > legend { float: left; width: 100%; margin: 0 0 5px; background: none; }
.exp-cronjobs fieldset.exp-field > legend + * { clear: both; }
.exp-field > label, .exp-field > legend { padding: 0; font-size: 12.5px; font-weight: 650; color: var(--cj-ink); }
.exp-cronjobs .exp-field select,
.exp-cronjobs .exp-field input[type="search"] {
    width: 100%; min-height: 36px; margin: 0; padding: 6px 10px; border: 1px solid #b9bfc9; border-radius: 9px;
    background: #fff; color: var(--cj-ink); font: inherit; font-size: 14px;
}
.exp-cronjobs .exp-field select:focus, .exp-cronjobs .exp-field input[type="search"]:focus { border-color: var(--cj-accent); outline: 3px solid var(--cj-ring); outline-offset: 0; }
.exp-field-wide { grid-column: 1 / -1; }
.exp-chips { display: flex; flex-wrap: wrap; gap: 6px; }
.exp-chip { position: relative; display: inline-flex; }
.exp-chip input { position: absolute; opacity: 0; width: 1px; height: 1px; }
.exp-chip span { display: inline-flex; align-items: center; min-height: 32px; padding: 4px 12px; border: 1px solid #c9ced6; border-radius: 999px; background: #fff; font-size: 13px; cursor: pointer; }
.exp-chip input:checked + span { border-color: var(--cj-accent); background: var(--cj-accent); color: #fff; font-weight: 650; }
.exp-chip input:focus-visible + span { outline: 3px solid var(--cj-ring); outline-offset: 2px; }
.exp-runbox { grid-column: 1 / -1; display: flex; flex-wrap: wrap; align-items: end; gap: 10px 12px; padding-top: 14px; border-top: 1px solid var(--cj-line); }
.exp-runbox .exp-field { flex: 1 1 220px; }
.exp-runbox .exp-meta { flex: 1 1 100%; }
.exp-filter-count { grid-column: 1 / -1; margin: 0; font-size: 13px; color: var(--cj-muted); }

/* Output */
.exp-fold { min-width: 0; margin: 0 0 26px; border: 1px solid var(--cj-line); border-radius: var(--cj-radius); background: var(--cj-card); }
.exp-fold > summary {
    display: flex; flex-wrap: wrap; align-items: baseline; gap: 4px 12px; padding: 12px 16px; cursor: pointer; list-style: none;
    border-radius: var(--cj-radius);
}
.exp-fold > summary::-webkit-details-marker { display: none; }
.exp-fold > summary::before { content: "\25B8"; flex: 0 0 auto; width: 1em; color: var(--cj-muted); }
.exp-fold[open] > summary::before { content: "\25BE"; }
.exp-fold > summary:hover h2 { color: var(--cj-accent-hover); }
.exp-fold-body { min-width: 0; max-width: 100%; padding: 0 16px 16px; contain: inline-size; }
.exp-console {
    margin: 0; height: 22em; max-height: 60vh; overflow: auto; padding: 12px 14px; border-radius: 10px;
    max-width: 100%; background: #16161a; color: #e4e4e7; white-space: pre-wrap; word-break: break-word; overflow-wrap: anywhere;
    font: 12px/1.55 var(--cj-mono);
}
.exp-console:empty::before { content: attr(data-empty); color: #b4b4bb; }
.exp-crontab {
    margin: 0 0 12px; padding: 12px 14px; border: 1px solid var(--cj-line); border-radius: 10px; background: var(--cj-soft);
    font: 12px/1.8 var(--cj-mono); white-space: pre-wrap; overflow-wrap: anywhere; color: var(--cj-ink);
}
.exp-fold-body h3 { margin: 14px 0 4px; font-size: 14px; }
.exp-fold-body h3:first-child { margin-top: 0; }
.exp-fold-body p { margin: 0 0 8px; }

/* The parts */
.exp-parts { display: grid; grid-template-columns: minmax(0, 1fr); gap: 12px; margin: 0; padding: 0; list-style: none; }
.exp-part {
    min-width: 0; margin: 0; padding: 14px 16px; border: 1px solid var(--cj-line); border-radius: var(--cj-radius); background: var(--cj-card);
    box-shadow: 0 1px 2px rgba(16, 24, 40, 0.04);
}
.exp-part.is-current { border-color: var(--cj-ok); box-shadow: inset 4px 0 0 var(--cj-ok), 0 1px 2px rgba(16, 24, 40, 0.04); }
.exp-part.is-attention { box-shadow: inset 4px 0 0 var(--cj-bad), 0 1px 2px rgba(16, 24, 40, 0.04); }
.exp-part-head { display: flex; flex-wrap: wrap; align-items: flex-start; justify-content: space-between; gap: 10px 16px; }
.exp-part-title { display: flex; flex-wrap: wrap; align-items: center; gap: 6px 10px; min-width: 0; }
.exp-part-key { color: var(--cj-muted); }
.exp-badges { display: inline-flex; flex-wrap: wrap; gap: 6px; margin: 0; padding: 0; list-style: none; }
.exp-badge { display: inline-flex; align-items: center; gap: 5px; margin: 0; padding: 2px 9px; border-radius: 999px; font-size: 12px; font-weight: 650; line-height: 1.5; white-space: nowrap; background: var(--cj-soft); color: var(--cj-muted); }
.exp-badge.is-ok { background: var(--cj-ok-bg); color: var(--cj-ok); }
.exp-badge.is-warn { background: var(--cj-warn-bg); color: var(--cj-warn); }
.exp-badge.is-bad { background: var(--cj-bad-bg); color: var(--cj-bad); }
.exp-badge.is-info { background: var(--cj-info-bg); color: var(--cj-info); }
.exp-facts { display: grid; grid-template-columns: repeat(auto-fit, minmax(min(100%, 190px), 1fr)); gap: 8px 18px; margin: 12px 0 0; }
.exp-facts > div { min-width: 0; }
.exp-facts dt { margin: 0; font-size: 12px; font-weight: 650; text-transform: uppercase; letter-spacing: .04em; color: var(--cj-muted); }
.exp-facts dd { margin: 2px 0 0; overflow-wrap: anywhere; }
.exp-facts dd code { color: var(--cj-muted); }
.exp-cmd-row { display: flex; align-items: center; gap: 8px; margin: 12px 0 0; }
.exp-cmd {
    flex: 1 1 auto; min-width: 0; padding: 6px 10px; border: 1px solid var(--cj-line); border-radius: 8px; background: var(--cj-soft);
    color: var(--cj-ink); overflow-wrap: anywhere; user-select: all; -webkit-user-select: all;
}
.exp-cmd-row .exp-cmd-label { flex: 0 0 auto; font-size: 12px; font-weight: 650; color: var(--cj-muted); }
.exp-scripts { margin: 12px 0 0; border-top: 1px solid var(--cj-line); }
.exp-scripts > summary { display: flex; gap: 8px; align-items: baseline; padding: 10px 0 0; cursor: pointer; font-size: 13px; color: var(--cj-ink); list-style: none; }
.exp-scripts > summary::-webkit-details-marker { display: none; }
.exp-scripts > summary::before { content: "\25B8"; flex: 0 0 auto; color: var(--cj-muted); }
.exp-scripts[open] > summary::before { content: "\25BE"; }
.exp-scripts-count { flex: 0 0 auto; font-weight: 650; }
.exp-scripts-names { min-width: 0; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; font-family: var(--cj-mono); font-size: 12.5px; color: var(--cj-muted); }
.exp-scripts > summary:hover .exp-scripts-count { color: var(--cj-accent-hover); }
.exp-scripts[open] .exp-scripts-names { visibility: hidden; }
.exp-scripts[open] > summary { padding-bottom: 4px; }
.exp-script-list { margin: 0; padding: 0; list-style: none; }
.exp-script { display: flex; flex-wrap: wrap; align-items: center; justify-content: space-between; gap: 6px 14px; margin: 0; padding: 8px 0; border-top: 1px dashed var(--cj-line); }
.exp-script:first-child { border-top: 0; }
.exp-script-main { flex: 1 1 260px; min-width: 0; }
.exp-script-name { font-weight: 650; color: var(--cj-ink); overflow-wrap: anywhere; }
.exp-script-desc { margin: 2px 0 0; }
.exp-script-where { margin: 2px 0 0; font-size: 12.5px; color: var(--cj-muted); overflow-wrap: anywhere; }
.exp-script-where.is-missing { color: var(--cj-bad); font-weight: 650; }
.exp-script-actions { display: flex; flex-wrap: wrap; gap: 6px; }
.exp-empty { margin: 0; padding: 18px 16px; border: 1px dashed #c9ced6; border-radius: var(--cj-radius); color: var(--cj-muted); text-align: center; }

/* Tables */
.exp-table-wrap { overflow-x: auto; border: 1px solid var(--cj-line); border-radius: var(--cj-radius); background: var(--cj-card); }
.exp-table { width: 100%; margin: 0; border-collapse: collapse; }
.exp-cronjobs .exp-table th, .exp-cronjobs .exp-table td { padding: 9px 12px; border: 0; border-bottom: 1px solid var(--cj-line); text-align: left; vertical-align: top; background: transparent; color: var(--cj-ink); }
.exp-cronjobs .exp-table th { font-size: 12px; font-weight: 650; text-transform: uppercase; letter-spacing: .04em; color: var(--cj-muted); background: var(--cj-soft); white-space: nowrap; }
.exp-cronjobs .exp-table tr:last-child td { border-bottom: 0; }
.exp-table .exp-num { text-align: right; font-variant-numeric: tabular-nums; white-space: nowrap; }
.exp-table .exp-nowrap { white-space: nowrap; }

.exp-pager { margin: 14px 0 0; }
/* The shared page navigator, in this page's colours: its own grey and orange fall short of 4.5:1 on white. */
.exp-cronjobs .exp-pager a { color: var(--cj-accent-hover); }
.exp-cronjobs .exp-pager span.text, .exp-cronjobs .exp-pager span.text a { color: var(--cj-accent-hover); }
.exp-cronjobs .exp-pager span.disabled, .exp-cronjobs .exp-pager span.text.disabled { color: var(--cj-muted); }
.exp-cronjobs .exp-pager span.current { color: var(--cj-ink); }

@media (max-width: 600px) {
    .exp-statusbar { padding: 12px; }
    .exp-status-actions, .exp-status-actions .exp-btn { width: 100%; }
    .exp-status-actions .exp-btn { flex: 1 1 0; }
    .exp-part { padding: 12px; }
    .exp-part-head .exp-run { width: 100%; }
    .exp-cmd-row { flex-wrap: wrap; }
    .exp-cmd-row .exp-cmd-label { flex-basis: 100%; }
}
</style>
{/literal}

<div class="context-block exp-cronjobs">

<div class="box-header"><div class="box-ml">
<h1 class="context-title">{'Cronjobs'|i18n( 'design/admin/setup/cronjobs' )}</h1>
</div></div>

<div class="box-bc"><div class="box-ml"><div class="box-content">

<p class="exp-intro">{'Runs a cronjob part now, without waiting for the scheduler. The job is started as a separate process and keeps running after this page is closed, so nothing is lost if the browser goes away.'|i18n( 'design/admin/setup/cronjobs' )}</p>

{foreach $cronjob_feedback as $cronjob_message}
<div class="exp-feedback {if $cronjob_message.ok}is-ok{else}is-bad{/if}" role="{if $cronjob_message.ok}status{else}alert{/if}">{$cronjob_message.message|wash}</div>
{/foreach}

{* What an action says when the console runs it in place. Empty and hidden until there is something to put in it. *}
<div class="exp-feedback" id="cronjob-feedback" role="status" aria-live="polite" hidden></div>

{if $cronjob_php_binary|eq('')}
<div class="exp-feedback is-bad" role="alert">
    {'No php command line binary could be found, so nothing can be launched from here. Set cronjob.ini [AdminSettings] PhpCliPath to its full path.'|i18n( 'design/admin/setup/cronjobs' )}
</div>
{/if}

<form method="post" action={'setup/cronjobs'|ezurl}>

{* Everything that changes when a job starts or ends is addressable, so the console can bring the page up to
   date without fetching it again. *}
<section class="exp-statusbar" aria-labelledby="cronjob-status-title">
    <h2 class="exp-sr" id="cronjob-status-title">{'Now'|i18n( 'design/admin/setup/cronjobs' )}</h2>
    <div class="exp-status" id="cronjob-status" aria-live="polite">
        <span class="exp-pill{if $cronjob_status.running} is-running{/if}" id="cronjob-pill">
            <span class="exp-dot" aria-hidden="true"></span>
            <span class="exp-pill-text">{if $cronjob_status.running}{'Running: %part'|i18n( 'design/admin/setup/cronjobs',, hash( '%part', $cronjob_status.part|wash ) )}{else}{'Idle'|i18n( 'design/admin/setup/cronjobs' )}{/if}</span>
        </span>
        {if $cronjob_status.running}
        <span class="exp-meta exp-running-meta">{'Site: %siteaccess'|i18n( 'design/admin/setup/cronjobs',, hash( '%siteaccess', $cronjob_status.siteaccess|wash ) )}</span>
        <span class="exp-meta exp-running-meta">{'Process: %pid'|i18n( 'design/admin/setup/cronjobs',, hash( '%pid', $cronjob_status.pid ) )}</span>
        <span class="exp-meta exp-running-meta" id="cronjob-elapsed">{'Elapsed: %elapsed'|i18n( 'design/admin/setup/cronjobs',, hash( '%elapsed', concat( $cronjob_status.elapsed, 's' ) ) )}</span>
        {/if}
    </div>
    <div class="exp-status-actions">
        <button type="submit" class="exp-btn" id="cronjob-stop" name="StopCronjobButton" value="1"{if $cronjob_status.running|not} disabled="disabled"{/if}>{'Stop running job'|i18n( 'design/admin/setup/cronjobs' )}</button>
        <button type="submit" class="exp-btn" id="cronjob-clear" name="ClearCronjobLogButton" value="1">{'Clear logs'|i18n( 'design/admin/setup/cronjobs' )}</button>
    </div>
</section>
<p class="exp-paths">
    <span>{'Log'|i18n( 'design/admin/setup/cronjobs' )}: <code>{$cronjob_log_file|wash}</code></span>
    {if $cronjob_php_binary|ne('')}<span>php: <code>{$cronjob_php_binary|wash}</code></span>{/if}
</p>

{* The overview: how many parts there are, how many the crontab runs, how many need a look, and the last run. *}
<section aria-labelledby="cronjob-overview-title">
<h2 class="exp-sr" id="cronjob-overview-title">{'Overview'|i18n( 'design/admin/setup/cronjobs' )}</h2>
<ul class="exp-figures">
    <li class="exp-figure"><strong>{$cronjob_summary.parts}</strong><span>{'Cronjob parts, %count scripts'|i18n( 'design/admin/setup/cronjobs',, hash( '%count', $cronjob_summary.scripts ) )}</span></li>
    <li class="exp-figure"><strong>{$cronjob_summary.scheduled}</strong><span>{'Scheduled in the crontab'|i18n( 'design/admin/setup/cronjobs' )}</span></li>
    <li class="exp-figure"><strong>{$cronjob_summary.unscheduled}</strong><span>{'Not scheduled'|i18n( 'design/admin/setup/cronjobs' )}</span></li>
    <li class="exp-figure{if $cronjob_summary.attention|gt(0)} is-attention{/if}"><strong>{$cronjob_summary.attention}</strong><span>{'Need attention'|i18n( 'design/admin/setup/cronjobs' )}</span></li>
    <li class="exp-figure is-last">
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
   the list; the part also decides what the Run beside it runs. *}
<section aria-labelledby="cronjob-controls-title">
<h2 class="exp-sr" id="cronjob-controls-title">{'Run and filter'|i18n( 'design/admin/setup/cronjobs' )}</h2>
<div class="exp-toolbar">
    <div class="exp-field">
        <label for="cronjob-siteaccess">{'Run for site'|i18n( 'design/admin/setup/cronjobs' )}</label>
        {* A cronjob acts on content, and which content depends on the siteaccess it runs under, so this is asked
           rather than assumed. *}
        <select id="cronjob-siteaccess" name="CronjobSiteAccess">
        {foreach $cronjob_siteaccess_list as $cronjob_siteaccess}
            <option value="{$cronjob_siteaccess|wash}"{if eq( $cronjob_siteaccess, $cronjob_default_siteaccess )} selected="selected"{/if}>{$cronjob_siteaccess|wash}</option>
        {/foreach}
        </select>
    </div>
    <div class="exp-field exp-js-only" hidden>
        <label for="cronjob-search">{'Find a part or script'|i18n( 'design/admin/setup/cronjobs' )}</label>
        <input type="search" id="cronjob-search" autocomplete="off" spellcheck="false" aria-controls="cronjob-list" aria-describedby="cronjob-filter-count" />
    </div>
    <fieldset class="exp-field exp-field-wide exp-js-only" hidden>
        <legend>{'Show'|i18n( 'design/admin/setup/cronjobs' )}</legend>
        <div class="exp-chips">
            <label class="exp-chip"><input type="radio" name="CronjobFilterState" value="" checked="checked" /><span>{'All'|i18n( 'design/admin/setup/cronjobs' )}</span></label>
            <label class="exp-chip"><input type="radio" name="CronjobFilterState" value="scheduled" /><span>{'Scheduled'|i18n( 'design/admin/setup/cronjobs' )}</span></label>
            <label class="exp-chip"><input type="radio" name="CronjobFilterState" value="unscheduled" /><span>{'Not scheduled'|i18n( 'design/admin/setup/cronjobs' )}</span></label>
            <label class="exp-chip"><input type="radio" name="CronjobFilterState" value="attention" /><span>{'Need attention'|i18n( 'design/admin/setup/cronjobs' )}</span></label>
        </div>
    </fieldset>
    <div class="exp-runbox exp-js-only" hidden>
        <div class="exp-field">
            <label for="cronjob-part-filter">{'Cronjob part'|i18n( 'design/admin/setup/cronjobs' )}</label>
            {* Every part, not only the ones on this page: it is also how a part is chosen to run, and running "all
               parts" runs every one of them that may be run. *}
            <select id="cronjob-part-filter" aria-describedby="cronjob-run-hint">
                <option value="">{'All parts'|i18n( 'design/admin/setup/cronjobs' )}</option>
            {foreach $cronjob_parts as $cronjob_option}
                <option value="{$cronjob_option.name|wash}" data-blocked="{if or( $cronjob_option.forbidden, $cronjob_option.missing|ge( $cronjob_option.scripts|count ), $cronjob_php_binary|eq('') )}1{else}0{/if}">{$cronjob_option.label|wash}</option>
            {/foreach}
            </select>
        </div>
        <button type="button" class="exp-btn exp-btn-primary" id="cronjob-run-selected"{if $cronjob_status.running} disabled="disabled"{/if}><svg width="14" height="14" viewBox="0 0 16 16" aria-hidden="true" focusable="false"><path fill="currentColor" d="M4 2.5v11l9-5.5z"/></svg>{'Run'|i18n( 'design/admin/setup/cronjobs' )}</button>
        <span class="exp-meta" id="cronjob-run-hint">{'Runs every part, one after another.'|i18n( 'design/admin/setup/cronjobs' )}</span>
    </div>
    <p class="exp-filter-count exp-js-only" id="cronjob-filter-count" aria-live="polite" hidden></p>
</div>
</section>

{* The output: what the running job, or the last one, wrote. Open while a job runs; the console opens it when it
   starts one. *}
<details class="exp-fold exp-output" id="cronjob-output"{if $cronjob_status.running} open="open"{/if}>
    <summary>
        <h2 class="exp-h2">{'Output'|i18n( 'design/admin/setup/cronjobs' )}</h2>
        <span class="exp-meta" id="cronjob-stream-status">{'The last run, from %file'|i18n( 'design/admin/setup/cronjobs',, hash( '%file', $cronjob_log_file|wash ) )}</span>
    </summary>
    <div class="exp-fold-body">
        <pre class="exp-console" id="cronjob-console" tabindex="0" aria-label="{'Output'|i18n( 'design/admin/setup/cronjobs' )}" data-empty="{'No output yet.'|i18n( 'design/admin/setup/cronjobs' )}">{$cronjob_log|wash}{if $cronjob_errors|ne('')}
{$cronjob_errors|wash}{/if}</pre>
    </div>
</details>

{* One card per cronjob part. A part is run whole from its card, one script from its row, the whole list in turn
   from the Run above. *}
<section class="exp-section" aria-labelledby="cronjob-parts-title">
<div class="exp-section-head">
    <h2 class="exp-h2" id="cronjob-parts-title">{'Cronjob parts'|i18n( 'design/admin/setup/cronjobs' )}</h2>
    {if $cronjob_parts_count|gt( $limit )}
    <span class="exp-meta">{'%from to %to of %count'|i18n( 'design/admin/setup/cronjobs',, hash( '%from', sum( $view_parameters.offset, 1 ), '%to', min( sum( $view_parameters.offset, $limit ), $cronjob_parts_count ), '%count', $cronjob_parts_count ) )}</span>
    {/if}
</div>

{if $cronjob_parts_page|count|eq(0)}
<p class="exp-empty">{'No cronjob parts are defined. Parts are the [CronjobSettings] and [CronjobPart-...] groups of cronjob.ini.'|i18n( 'design/admin/setup/cronjobs' )}</p>
{else}
<ul class="exp-parts" id="cronjob-list">
{foreach $cronjob_parts_page as $cronjob_part}
    {* Blocked for good - forbidden, scripts missing, no php - is not the same as blocked because something else
       is running, which stops being true the moment the console sees the job finish. Each button records which
       it is so the console knows what it may re-enable. *}
    {def $cronjob_blocked = or( $cronjob_part.forbidden,
                                $cronjob_part.missing|ge( $cronjob_part.scripts|count ),
                                $cronjob_php_binary|eq('') )
         $cronjob_runnable = and( $cronjob_blocked|not, $cronjob_status.running|not )
         $cronjob_part_id = concat( 'cronjob-part-', $cronjob_part.name )}
<li class="exp-part exp-part-row{if and( $cronjob_status.running, eq( $cronjob_status.part, $cronjob_part.name ) )} is-current{/if}{if $cronjob_part.attention} is-attention{/if}"
    id="{$cronjob_part_id|wash}" data-part="{$cronjob_part.name|wash}"
    data-state="{if $cronjob_part.scheduled}scheduled{else}unscheduled{/if}" data-attention="{if $cronjob_part.attention}1{else}0{/if}"
    data-search="{$cronjob_part.search|wash}">
    <div class="exp-part-head">
        <div class="exp-part-title">
            <h3 id="{$cronjob_part_id|wash}-title">{$cronjob_part.label|wash}</h3>
            <code class="exp-part-key">{$cronjob_part.name|wash}</code>
            <ul class="exp-badges">
                {* What the crontab actually says about this part, not what it could say. *}
                {if $cronjob_part.scheduled}
                <li class="exp-badge is-ok" title="{'A crontab entry for this installation runs this part'|i18n( 'design/admin/setup/cronjobs' )}">{'Scheduled'|i18n( 'design/admin/setup/cronjobs' )}</li>
                {else}
                <li class="exp-badge is-warn" title="{'Nothing in the crontab runs this part'|i18n( 'design/admin/setup/cronjobs' )}">{'Not scheduled'|i18n( 'design/admin/setup/cronjobs' )}</li>
                {/if}
                {if $cronjob_part.forbidden}
                <li class="exp-badge" title="{'Blocked by cronjob.ini ForbiddenParts'|i18n( 'design/admin/setup/cronjobs' )}">{'Blocked'|i18n( 'design/admin/setup/cronjobs' )}</li>
                {elseif $cronjob_part.missing|gt(0)}
                <li class="exp-badge is-bad">{'%count missing'|i18n( 'design/admin/setup/cronjobs',, hash( '%count', $cronjob_part.missing ) )}</li>
                {else}
                <li class="exp-badge is-ok">{'Activated'|i18n( 'design/admin/setup/cronjobs' )}</li>
                {/if}
            </ul>
        </div>
        {* The button carries the part name as its own value, so one form serves every part and launching needs no
           javascript. *}
        <button type="submit" class="exp-btn exp-btn-outline exp-run" name="LaunchCronjobButton"
                data-blocked="{if $cronjob_blocked}1{else}0{/if}"
                value="{$cronjob_part.name|wash}"{if $cronjob_runnable|not} disabled="disabled"{/if}
                aria-describedby="{$cronjob_part_id|wash}-title"
                title="{'Run the whole %part part now'|i18n( 'design/admin/setup/cronjobs',, hash( '%part', $cronjob_part.label ) )|wash}"><svg width="14" height="14" viewBox="0 0 16 16" aria-hidden="true" focusable="false"><path fill="currentColor" d="M4 2.5v11l9-5.5z"/></svg>{'Run part'|i18n( 'design/admin/setup/cronjobs' )}</button>
    </div>

    <dl class="exp-facts">
        <div>
            <dt>{if $cronjob_part.scheduled}{'Schedule'|i18n( 'design/admin/setup/cronjobs' )}{else}{'Suggested schedule'|i18n( 'design/admin/setup/cronjobs' )}{/if}</dt>
            <dd>{if $cronjob_part.schedule_active|ne('')}{$cronjob_part.schedule_text|wash}{if $cronjob_part.schedule_text|ne( $cronjob_part.schedule_active )} <code>{$cronjob_part.schedule_active|wash}</code>{/if}{else}{'Not read from the crontab line'|i18n( 'design/admin/setup/cronjobs' )}{/if}</dd>
        </div>
        <div>
            <dt>{'Next run'|i18n( 'design/admin/setup/cronjobs' )}</dt>
            <dd>{if $cronjob_part.next_run|gt(0)}{$cronjob_part.next_run|l10n( shortdatetime )}{elseif $cronjob_part.scheduled}&mdash;{else}<span class="exp-muted">{'Only when run by hand'|i18n( 'design/admin/setup/cronjobs' )}</span>{/if}</dd>
        </div>
        <div>
            <dt>{'Last run from here'|i18n( 'design/admin/setup/cronjobs' )}</dt>
            <dd>{if $cronjob_part.last_run}
                {$cronjob_part.last_run.started|l10n( shortdatetime )}
                {if $cronjob_part.last_run.running}<span class="exp-badge is-info">{'running'|i18n( 'design/admin/setup/cronjobs' )}</span>
                {else}&middot; {$cronjob_part.last_run.seconds}s
                    {if $cronjob_part.last_run.errors|gt(0)}<span class="exp-badge is-bad">{'%count issues'|i18n( 'design/admin/setup/cronjobs',, hash( '%count', $cronjob_part.last_run.errors ) )}</span>{else}<span class="exp-badge is-ok">{'OK'|i18n( 'design/admin/setup/cronjobs' )}</span>{/if}
                {/if}
                {if $cronjob_part.last_run.script|ne('')}<br /><span class="exp-meta">{$cronjob_part.last_run.script|wash}</span>{/if}
            {else}<span class="exp-muted">{'Not yet'|i18n( 'design/admin/setup/cronjobs' )}</span>{/if}</dd>
        </div>
    </dl>

    <div class="exp-cmd-row">
        <span class="exp-cmd-label" id="{$cronjob_part_id|wash}-cmd-label">{'Shell command'|i18n( 'design/admin/setup/cronjobs' )}</span>
        <code class="exp-cmd" aria-labelledby="{$cronjob_part_id|wash}-cmd-label">{$cronjob_part.command.head|wash}<span class="exp-cmd-sa">{$cronjob_default_siteaccess|wash}</span>{$cronjob_part.command.tail|wash}</code>
        <button type="button" class="exp-btn exp-btn-small exp-copy exp-js-only" hidden
                data-cmd-head="{$cronjob_part.command.head|wash}" data-cmd-tail="{$cronjob_part.command.tail|wash}"
                aria-label="{'Copy the command that runs %part'|i18n( 'design/admin/setup/cronjobs',, hash( '%part', $cronjob_part.label ) )|wash}">{'Copy'|i18n( 'design/admin/setup/cronjobs' )}</button>
    </div>

    {* Folded to one line of script names; open, it says what each script does and runs one on its own. *}
    <details class="exp-scripts">
        <summary><span class="exp-scripts-count">{'Scripts (%count)'|i18n( 'design/admin/setup/cronjobs',, hash( '%count', $cronjob_part.scripts|count ) )}</span> <span class="exp-scripts-names">{$cronjob_part.script_names|wash}</span></summary>
        <ul class="exp-script-list">
        {foreach $cronjob_part.scripts as $cronjob_script}
            <li class="exp-script exp-script-row" data-part="{$cronjob_part.name|wash}">
                <div class="exp-script-main">
                    <code class="exp-script-name">{$cronjob_script.name|wash}</code>
                    {if $cronjob_script.description|ne('')}<p class="exp-script-desc">{$cronjob_script.description|wash}</p>{/if}
                    {if $cronjob_script.path|eq(false())}
                    <p class="exp-script-where is-missing">{'not found in any cronjob directory'|i18n( 'design/admin/setup/cronjobs' )}</p>
                    {else}
                    <p class="exp-script-where">{$cronjob_script.directory|wash}</p>
                    {/if}
                </div>
                <div class="exp-script-actions">
                    {if $cronjob_script.path|ne(false())}
                    <button type="button" class="exp-btn exp-btn-small exp-btn-quiet exp-copy exp-js-only" hidden
                            data-cmd-head="{$cronjob_script.command.head|wash}" data-cmd-tail="{$cronjob_script.command.tail|wash}"
                            aria-label="{'Copy the command that runs %script on its own'|i18n( 'design/admin/setup/cronjobs',, hash( '%script', $cronjob_script.name ) )|wash}">{'Copy command'|i18n( 'design/admin/setup/cronjobs' )}</button>
                    {/if}
                    {if and( $cronjob_script.path|ne(false()), $cronjob_part.forbidden|not )}
                    <button type="submit" class="exp-btn exp-btn-small exp-run" name="LaunchCronjobScriptButton"
                            data-blocked="{if $cronjob_php_binary|eq('')}1{else}0{/if}"
                            value="{$cronjob_part.name|wash}|{$cronjob_script.name|wash}"{if or( $cronjob_status.running, $cronjob_php_binary|eq('') )} disabled="disabled"{/if}
                            aria-label="{'Run %script on its own'|i18n( 'design/admin/setup/cronjobs',, hash( '%script', $cronjob_script.name ) )|wash}"
                            title="{'Run %script on its own'|i18n( 'design/admin/setup/cronjobs',, hash( '%script', $cronjob_script.name ) )|wash}">{'Run'|i18n( 'design/admin/setup/cronjobs' )}</button>
                    {else}
                    <span class="exp-meta">{'This script cannot be run from here'|i18n( 'design/admin/setup/cronjobs' )}</span>
                    {/if}
                </div>
            </li>
        {/foreach}
        </ul>
    </details>
</li>
    {undef $cronjob_runnable $cronjob_blocked $cronjob_part_id}
{/foreach}
</ul>
<p class="exp-empty" id="cronjob-no-match" hidden>{'No cronjob part on this page matches.'|i18n( 'design/admin/setup/cronjobs' )}</p>
{/if}

{* Paged; the size is admininterface.ini [PaginationSettings]. *}
{if $cronjob_parts_count|gt( $limit )}
<div class="context-toolbar exp-pager">
{include name=CronjobNavigator
         uri='design:navigator/google.tpl'
         page_uri='/setup/cronjobs'
         item_count=$cronjob_parts_count
         view_parameters=$view_parameters
         item_limit=$limit}
</div>
{/if}
</section>

</form>

{* On disk, named by no part, so nothing ever runs them. *}
{if $cronjob_available_scripts|count|gt(0)}
<details class="exp-fold" id="cronjob-available">
    <summary>
        <h2 class="exp-h2">{'Available but not activated'|i18n( 'design/admin/setup/cronjobs' )}</h2>
        <span class="exp-meta">{'%count scripts no part names, so they never run'|i18n( 'design/admin/setup/cronjobs',, hash( '%count', $cronjob_available_scripts|count ) )}</span>
    </summary>
    <div class="exp-fold-body">
        <p class="exp-meta">{'These scripts exist but no cronjob part names them, so they never run.'|i18n( 'design/admin/setup/cronjobs' )} {'Add it to a part in cronjob.ini to run it'|i18n( 'design/admin/setup/cronjobs' )}.</p>
        <ul class="exp-script-list">
        {foreach $cronjob_available_scripts as $cronjob_spare}
            <li class="exp-script exp-spare-row">
                <div class="exp-script-main">
                    <code class="exp-script-name">{$cronjob_spare.name|wash}</code>
                    {if $cronjob_spare.description|ne('')}<p class="exp-script-desc">{$cronjob_spare.description|wash}</p>{/if}
                    <p class="exp-script-where">{$cronjob_spare.directory|wash}</p>
                </div>
                <span class="exp-badge is-warn">{'Available'|i18n( 'design/admin/setup/cronjobs' )}</span>
            </li>
        {/foreach}
        </ul>
    </div>
</details>
{/if}

{* What has run, when, and whether it complained. *}
<details class="exp-fold" id="cronjob-history" open="open">
    <summary>
        <h2 class="exp-h2" id="cronjob-history-title">{'Recent runs'|i18n( 'design/admin/setup/cronjobs' )}</h2>
        <span class="exp-meta">{'Started from this page, newest first'|i18n( 'design/admin/setup/cronjobs' )}</span>
    </summary>
    <div class="exp-fold-body">
    {if $cronjob_history|count|gt(0)}
    <div class="exp-table-wrap" role="region" aria-labelledby="cronjob-history-title" tabindex="0">
    <table class="exp-table">
    <thead>
    <tr>
        <th scope="col">{'Cronjob'|i18n( 'design/admin/setup/cronjobs' )}</th>
        <th scope="col">{'Site'|i18n( 'design/admin/setup/cronjobs' )}</th>
        <th scope="col">{'Started'|i18n( 'design/admin/setup/cronjobs' )}</th>
        <th scope="col" class="exp-num">{'Took'|i18n( 'design/admin/setup/cronjobs' )}</th>
        <th scope="col">{'Result'|i18n( 'design/admin/setup/cronjobs' )}</th>
    </tr>
    </thead>
    <tbody>
    {foreach $cronjob_history as $cronjob_run}
    <tr>
        <td>{$cronjob_run.label|wash}</td>
        <td>{$cronjob_run.siteaccess|wash}</td>
        <td class="exp-nowrap">{$cronjob_run.started|l10n( shortdatetime )}</td>
        <td class="exp-num">{if $cronjob_run.running}&mdash;{else}{$cronjob_run.seconds}s{/if}</td>
        <td>{if $cronjob_run.running}<span class="exp-badge is-info">{'running'|i18n( 'design/admin/setup/cronjobs' )}</span>{elseif $cronjob_run.errors|gt(0)}<span class="exp-badge is-bad">{'%count issues'|i18n( 'design/admin/setup/cronjobs',, hash( '%count', $cronjob_run.errors ) )}</span>{else}<span class="exp-badge is-ok">{'OK'|i18n( 'design/admin/setup/cronjobs' )}</span>{/if}</td>
    </tr>
    {/foreach}
    </tbody>
    </table>
    </div>
    <p class="exp-meta" style="margin-top: 8px;">{'Issues are the log lines of a run that mention an error, a failure or a warning.'|i18n( 'design/admin/setup/cronjobs' )}</p>
    {else}
    <p class="exp-empty">{'Nothing has been run from here yet.'|i18n( 'design/admin/setup/cronjobs' )}</p>
    {/if}
    </div>
</details>

{* Last on the page and folded away: useful when setting the schedule up, noise every other time. *}
<details class="exp-fold" id="cronjob-crontab-block">
    <summary>
        <h2 class="exp-h2">{'Crontab'|i18n( 'design/admin/setup/cronjobs' )}</h2>
        <span class="exp-meta">{'What is scheduled now, and the lines that would schedule the rest'|i18n( 'design/admin/setup/cronjobs' )}</span>
    </summary>
    <div class="exp-fold-body">
        {* What is really in the crontab, read from it. *}
        <h3>{'In the crontab now'|i18n( 'design/admin/setup/cronjobs' )}</h3>
        <p class="exp-meta">{'Read from crontab -l for the user this site runs as.'|i18n( 'design/admin/setup/cronjobs' )}</p>
        {if $cronjob_crontab.available}
            {if $cronjob_crontab.lines|count|gt(0)}
        <pre class="exp-crontab" tabindex="0">{$cronjob_crontab_current|wash}</pre>
            {else}
        <p class="exp-meta">{'The crontab is empty.'|i18n( 'design/admin/setup/cronjobs' )}</p>
            {/if}
        {else}
        <p class="exp-meta">{$cronjob_crontab.note|wash}</p>
        {/if}

        {* And what would schedule the parts that nothing schedules. Generated here from this installation's own
           paths and php binary - not read from anywhere. *}
        <h3>{'Suggested entries'|i18n( 'design/admin/setup/cronjobs' )}</h3>
        <p class="exp-meta">{'Written by this page from the paths below, for the parts nothing currently runs. Nothing adds them for you.'|i18n( 'design/admin/setup/cronjobs' )}</p>
        {if $cronjob_crontab_suggested|ne('')}
        <pre class="exp-crontab" id="cronjob-crontab-suggested" tabindex="0">{$cronjob_crontab_suggested|wash}</pre>
        <button type="button" class="exp-btn exp-btn-small exp-copy exp-js-only" data-copy-from="cronjob-crontab-suggested" hidden>{'Copy the suggested entries'|i18n( 'design/admin/setup/cronjobs' )}</button>
        {else}
        <p class="exp-meta">{'Every part is scheduled.'|i18n( 'design/admin/setup/cronjobs' )}</p>
        {/if}
        <p class="exp-meta" style="margin-top: 10px;">{'Installation'|i18n( 'design/admin/setup/cronjobs' )}: <code>{$cronjob_root|wash}</code></p>
    </div>
</details>

<p class="exp-sr" id="cronjob-copy-status" role="status" aria-live="polite"></p>

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

    each( '.exp-cronjobs .exp-js-only', function ( el ) { el.hidden = false; } );

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
        each( '#cronjob-list > .exp-part', function ( card ) {
            total++;
            var haystack = card.getAttribute( 'data-search' ) || '';
            var ok = ( part === '' || card.getAttribute( 'data-part' ) === part );
            if ( ok && state === 'attention' ) ok = card.getAttribute( 'data-attention' ) === '1';
            else if ( ok && state !== '' ) ok = card.getAttribute( 'data-state' ) === state;
            for ( var i = 0; ok && i < words.length; i++ ) ok = haystack.indexOf( words[i] ) !== -1;
            card.hidden = !ok;
            if ( ok ) shown++;
            // A search may match a script rather than the part, so the scripts of a match are opened while it
            // lasts; the ones opened that way close again when the search is cleared.
            var scripts = card.querySelector( '.exp-scripts' );
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
        each( '.exp-cmd-sa', function ( el ) { text( el, siteaccess() ); } );
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
    each( '.exp-copy', function ( button ) {
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

    var colours = { phase: '#7fd1ff', ok: '#e4e4e7', warn: '#e8c765', error: '#ff9b93', info: '#b4b4bb', done: '#7fd1ff' };
    var source = null;
    var elapsedTimer = null;
    var offset = expCronjobOffset;

    function write( type, line ) {
        var atBottom = consoleEl.scrollTop + consoleEl.clientHeight >= consoleEl.scrollHeight - 8;
        var el = document.createElement( 'div' );
        el.style.color = colours[type] || '#e4e4e7';
        if ( type === 'phase' || type === 'done' ) el.style.fontWeight = 'bold';
        el.appendChild( document.createTextNode( line ) );
        consoleEl.appendChild( el );
        if ( atBottom ) consoleEl.scrollTop = consoleEl.scrollHeight;
    }

    function say( value ) { text( statusEl, value ); }

    function feedback( ok, message ) {
        if ( !feedEl ) return;
        feedEl.className = 'exp-feedback ' + ( ok ? 'is-ok' : 'is-bad' );
        feedEl.setAttribute( 'role', ok ? 'status' : 'alert' );
        feedEl.hidden = false;
        text( feedEl, message );
    }

    function pillText( value, running ) {
        var pill = document.getElementById( 'cronjob-pill' );
        if ( !pill ) return;
        pill.className = 'exp-pill' + ( running ? ' is-running' : '' );
        text( pill.querySelector( '.exp-pill-text' ), value );
    }

    // The page is never fetched again, in either direction. Everything an action or a finished job changes is on
    // this page already, so it is changed here. A reload repeats the request that produced the page, so the form
    // post that started a job was offered for resending, and confirming launched it again - and again.
    function markIdle() {
        pillText( tr( 'idle' ), false );
        each( '.exp-running-meta', function ( el ) { el.parentNode.removeChild( el ); } );
        each( '.exp-part-row.is-current', function ( el ) { el.classList.remove( 'is-current' ); } );
        if ( stopBtn ) stopBtn.disabled = true;
        if ( runSelectedEl ) runSelectedEl.disabled = false;
        // Only the ones that were waiting on this job. A part that is forbidden, missing its scripts, or has no php
        // to run with stays disabled.
        each( '.exp-run', function ( el ) {
            if ( el.getAttribute( 'data-blocked' ) !== '1' ) el.disabled = false;
        } );
        if ( elapsedTimer ) { window.clearInterval( elapsedTimer ); elapsedTimer = null; }
    }

    function markRunning( part, siteaccessName, pid ) {
        pillText( tr( 'running', { part: part } ), true );

        var strip = document.getElementById( 'cronjob-status' );
        if ( strip ) {
            each( '.exp-running-meta', function ( el ) { el.parentNode.removeChild( el ); } );
            var meta = [ [ tr( 'site', { siteaccess: siteaccessName } ), null ],
                         [ tr( 'process', { pid: pid } ), null ],
                         [ tr( 'elapsed', { elapsed: '0s' } ), 'cronjob-elapsed' ] ];
            for ( var i = 0; i < meta.length; i++ ) {
                var span = document.createElement( 'span' );
                span.className = 'exp-meta exp-running-meta';
                if ( meta[i][1] ) span.id = meta[i][1];
                span.appendChild( document.createTextNode( meta[i][0] ) );
                strip.appendChild( span );
            }
        }

        each( '.exp-run', function ( el ) { el.disabled = true; } );
        if ( runSelectedEl ) runSelectedEl.disabled = true;
        if ( stopBtn ) stopBtn.disabled = false;
        each( '.exp-part-row', function ( el ) {
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
                       .replace( /<style[\s\S]*?<\/style>/gi, ' ' )
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

    each( '.exp-run', function ( button ) {
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
