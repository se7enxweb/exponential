{* The workflow process list.

   A workflow process is a workflow that has started for one piece of content and has not finished: it waits for an
   approver, for the workflow cronjob, for the user, or it has stopped. The page leads with what is waiting and
   whether the cronjob that resumes it runs, then an overview, the controls, and one card per process grouped by
   the trigger that started it, each saying in words what it waits for. A process can be cancelled, in two steps:
   the first button only shows what cancelling would do.

   The same file is in design/admin and design/admin4, so every administration design resolves it. Its styling is
   scoped to .exp-processes and takes admin4's tokens where they exist (--a4-*), with values of its own for the older
   designs. Everything works without javascript: the filters are links, the cancel buttons submit the form, the
   folding sections are <details>. The script below only adds the search and "select all".
   User guide: doc/guides/workflows.md *}

{literal}
<style>
.exp-processes {
    --wf-ink: var(--a4-ink, #1f2430);
    --wf-muted: var(--a4-muted, #5d6573);
    --wf-line: var(--a4-line, #e3e6eb);
    --wf-soft: var(--a4-soft, #f6f7f9);
    --wf-card: #fff;
    --wf-accent: #c2410c;          /* white text on it is 5.2:1 */
    --wf-accent-hover: #9a3412;
    --wf-ring: rgba(194, 65, 12, 0.45);
    --wf-ok: #166534;   --wf-ok-bg: #e7f5ea;
    --wf-warn: #8a4b00; --wf-warn-bg: #fff3df;
    --wf-bad: #b91c1c;  --wf-bad-bg: #fdecec;
    --wf-info: #1e4fa8; --wf-info-bg: #e8effd;
    --wf-radius: 12px;
    --wf-mono: ui-monospace, SFMono-Regular, Menlo, Consolas, monospace;
    color: var(--wf-ink);
    font-size: 14px;
    line-height: 1.5;
}
.exp-processes *, .exp-processes *::before, .exp-processes *::after { box-sizing: border-box; }
.exp-processes [hidden] { display: none !important; }
.exp-processes .box-content { padding-bottom: 24px; }
.exp-processes h1.context-title { margin: 0; }
.exp-processes h2.exp-h2 { margin: 0; padding: 0; border: 0; background: none; font-size: 16px; font-weight: 650; color: var(--wf-ink); }
.exp-processes h3 { margin: 0; padding: 0; border: 0; background: none; font-size: 15px; font-weight: 650; color: var(--wf-ink); overflow-wrap: anywhere; }
.exp-processes p { margin: 0; }
.exp-processes code { font-family: var(--wf-mono); font-size: 12.5px; overflow-wrap: anywhere; }
.exp-processes a { color: var(--wf-accent-hover); }
.exp-processes a:hover { color: var(--wf-accent); }
.exp-processes :focus-visible { outline: 3px solid var(--wf-ring); outline-offset: 2px; }
.exp-processes .exp-sr { position: absolute; width: 1px; height: 1px; margin: -1px; padding: 0; overflow: hidden; clip: rect(0 0 0 0); white-space: nowrap; border: 0; }
.exp-processes .exp-muted { color: var(--wf-muted); }

.exp-processes .exp-intro { margin: 10px 0 18px; max-width: 76ch; color: var(--wf-muted); }
.exp-processes .exp-section { margin: 0 0 26px; }
.exp-processes .exp-section-head { display: flex; flex-wrap: wrap; align-items: baseline; gap: 4px 12px; margin: 0 0 10px; }

/* Messages */
.exp-processes .exp-feedback { margin: 0 0 14px; padding: 10px 14px; border: 1px solid; border-left-width: 4px; border-radius: 10px; }
.exp-processes .exp-feedback.is-ok { border-color: #b7dfc1; border-left-color: var(--wf-ok); background: var(--wf-ok-bg); color: var(--wf-ok); }
.exp-processes .exp-feedback.is-bad { border-color: #f1b4b4; border-left-color: var(--wf-bad); background: var(--wf-bad-bg); color: var(--wf-bad); }
.exp-processes .exp-feedback.is-warn { border-color: #f0d3a6; border-left-color: var(--wf-warn); background: var(--wf-warn-bg); color: var(--wf-warn); }
.exp-processes .exp-feedback a { color: inherit; font-weight: 650; }

/* What is waiting, and the cronjob that resumes it */
.exp-processes .exp-statusbar {
    display: flex; flex-wrap: wrap; align-items: center; justify-content: space-between; gap: 12px 20px;
    margin: 0 0 18px; padding: 14px 16px; border: 1px solid var(--wf-line); border-radius: var(--wf-radius); background: var(--wf-soft);
}
.exp-processes .exp-status { display: flex; flex-direction: column; gap: 4px; min-width: 0; flex: 1 1 320px; }
.exp-processes .exp-pill { display: inline-flex; align-items: center; gap: 8px; font-weight: 700; color: var(--wf-ink); }
.exp-processes .exp-dot { flex: 0 0 auto; width: 10px; height: 10px; border-radius: 50%; background: #8a919c; }
.exp-processes .exp-pill.is-ok .exp-dot { background: var(--wf-ok); }
.exp-processes .exp-pill.is-info .exp-dot { background: var(--wf-info); box-shadow: 0 0 0 4px rgba(30, 79, 168, 0.16); }
.exp-processes .exp-pill.is-bad .exp-dot { background: var(--wf-bad); box-shadow: 0 0 0 4px rgba(185, 28, 28, 0.16); }
.exp-processes .exp-pill.is-warn .exp-dot { background: #d97706; box-shadow: 0 0 0 4px rgba(217, 119, 6, 0.18); }
.exp-processes .exp-meta { color: var(--wf-muted); font-size: 13px; overflow-wrap: anywhere; }
.exp-processes .exp-meta strong { color: var(--wf-ink); font-weight: 650; }
.exp-processes .exp-status-actions { display: flex; flex-wrap: wrap; gap: 8px; }

/* Overview figures */
.exp-processes .exp-figures { display: grid; grid-template-columns: repeat(auto-fit, minmax(128px, 1fr)); gap: 10px; margin: 0 0 22px; padding: 0; list-style: none; }
.exp-processes .exp-figure { display: flex; flex-direction: column; gap: 2px; margin: 0; padding: 12px 14px; border: 1px solid var(--wf-line); border-radius: var(--wf-radius); background: var(--wf-card); }
.exp-processes .exp-figure strong { font-size: 22px; line-height: 1.15; font-weight: 700; font-variant-numeric: tabular-nums; color: var(--wf-ink); }
.exp-processes .exp-figure span { font-size: 12.5px; color: var(--wf-muted); }
.exp-processes .exp-figure.is-attention strong { color: var(--wf-bad); }
.exp-processes .exp-figure.is-last { grid-column: span 2; }
.exp-processes .exp-figure.is-last strong { font-size: 15px; line-height: 1.35; overflow-wrap: anywhere; }
@media (max-width: 520px) { .exp-processes .exp-figure.is-last { grid-column: 1 / -1; } }

/* Buttons */
.exp-processes .exp-btn {
    display: inline-flex; align-items: center; justify-content: center; gap: 6px; min-height: 36px; margin: 0;
    padding: 6px 14px; border: 1px solid #c9ced6; border-radius: 9px; background: #fff; color: var(--wf-ink);
    font: 600 13.5px/1.2 inherit; font-family: inherit; cursor: pointer; white-space: nowrap; text-decoration: none;
}
.exp-processes .exp-btn:hover:not([disabled]) { border-color: var(--wf-accent); color: var(--wf-accent-hover); }
.exp-processes .exp-btn[disabled] { opacity: .5; cursor: not-allowed; }
.exp-processes .exp-btn-primary { border-color: var(--wf-accent); background: var(--wf-accent); color: #fff; }
.exp-processes .exp-btn-primary:hover:not([disabled]) { border-color: var(--wf-accent-hover); background: var(--wf-accent-hover); color: #fff; }
.exp-processes .exp-btn-danger { border-color: var(--wf-bad); background: var(--wf-bad); color: #fff; }
.exp-processes .exp-btn-danger:hover:not([disabled]) { border-color: #8f1414; background: #8f1414; color: #fff; }
.exp-processes .exp-btn-outline-danger { border-color: #e3a5a5; color: var(--wf-bad); }
.exp-processes .exp-btn-outline-danger:hover:not([disabled]) { border-color: var(--wf-bad); background: var(--wf-bad-bg); color: #8f1414; }
.exp-processes .exp-btn-small { min-height: 30px; padding: 4px 10px; font-size: 12.5px; }

/* Controls */
.exp-processes .exp-toolbar {
    display: grid; grid-template-columns: repeat(auto-fit, minmax(min(100%, 230px), 1fr)); gap: 14px 16px; align-items: end;
    margin: 0 0 22px; padding: 16px; border: 1px solid var(--wf-line); border-radius: var(--wf-radius); background: var(--wf-card);
}
.exp-processes .exp-field { display: flex; flex-direction: column; gap: 5px; min-width: 0; margin: 0; padding: 0; border: 0; }
.exp-processes .exp-field > label, .exp-processes .exp-field > .exp-field-label { padding: 0; font-size: 12.5px; font-weight: 650; color: var(--wf-ink); }
.exp-processes .exp-field input[type="search"] {
    width: 100%; min-height: 36px; margin: 0; padding: 6px 10px; border: 1px solid #b9bfc9; border-radius: 9px;
    background: #fff; color: var(--wf-ink); font: inherit; font-size: 14px;
}
.exp-processes .exp-field input[type="search"]:focus { border-color: var(--wf-accent); outline: 3px solid var(--wf-ring); outline-offset: 0; }
.exp-processes .exp-field-wide { grid-column: 1 / -1; }
.exp-processes .exp-chips { display: flex; flex-wrap: wrap; gap: 6px; margin: 0; padding: 0; list-style: none; }
.exp-processes .exp-chips li { margin: 0; }
.exp-processes .exp-chip {
    display: inline-flex; align-items: center; gap: 6px; min-height: 32px; padding: 4px 12px; border: 1px solid #c9ced6; border-radius: 999px;
    background: #fff; color: var(--wf-ink); font-size: 13px; text-decoration: none;
}
.exp-processes a.exp-chip:hover { border-color: var(--wf-accent); color: var(--wf-accent-hover); }
.exp-processes .exp-chip[aria-current="page"] { border-color: var(--wf-accent); background: var(--wf-accent); color: #fff; font-weight: 650; }
.exp-processes .exp-chip-count { font-variant-numeric: tabular-nums; }
.exp-processes .exp-selectbar { grid-column: 1 / -1; display: flex; flex-wrap: wrap; align-items: center; gap: 10px 16px; padding-top: 14px; border-top: 1px solid var(--wf-line); }
.exp-processes .exp-check { display: inline-flex; align-items: center; gap: 8px; margin: 0; font-size: 13px; font-weight: 600; color: var(--wf-ink); cursor: pointer; }
.exp-processes .exp-check input { width: 18px; height: 18px; margin: 0; accent-color: var(--wf-accent); }
.exp-processes .exp-filter-count { grid-column: 1 / -1; margin: 0; font-size: 13px; color: var(--wf-muted); }
.exp-processes .exp-pagesize { display: flex; margin: 0; padding: 0; border: 0; background: none; box-shadow: none; flex-wrap: wrap; align-items: center; gap: 6px; font-size: 13px; color: var(--wf-muted); }
.exp-processes .exp-pagesize a, .exp-processes .exp-pagesize span.current {
    display: inline-flex; align-items: center; justify-content: center; min-width: 34px; min-height: 30px; padding: 2px 8px;
    border: 1px solid #c9ced6; border-radius: 8px; background: #fff; color: var(--wf-accent-hover); text-decoration: none; font-weight: 600;
}
.exp-processes .exp-pagesize span.current { border-color: var(--wf-ink); color: var(--wf-ink); }

/* The groups and the processes */
.exp-processes .exp-group { margin: 0 0 24px; }
.exp-processes .exp-group-head { display: flex; flex-wrap: wrap; align-items: baseline; gap: 4px 12px; margin: 0 0 10px; }
.exp-processes .exp-group-key { color: var(--wf-muted); }
.exp-processes .exp-group-note { margin: -4px 0 10px; max-width: 76ch; }
.exp-processes .exp-cards { display: grid; grid-template-columns: minmax(0, 1fr); gap: 12px; margin: 0; padding: 0; list-style: none; }
.exp-processes .exp-card {
    min-width: 0; margin: 0; padding: 14px 16px; border: 1px solid var(--wf-line); border-radius: var(--wf-radius); background: var(--wf-card);
    box-shadow: 0 1px 2px rgba(16, 24, 40, 0.04);
}
.exp-processes .exp-card.is-bad { box-shadow: inset 4px 0 0 var(--wf-bad), 0 1px 2px rgba(16, 24, 40, 0.04); }
.exp-processes .exp-card.is-warn { box-shadow: inset 4px 0 0 #d97706, 0 1px 2px rgba(16, 24, 40, 0.04); }
.exp-processes .exp-card-head { display: flex; flex-wrap: wrap; align-items: flex-start; justify-content: space-between; gap: 10px 16px; }
.exp-processes .exp-card-title { display: flex; align-items: flex-start; gap: 10px; min-width: 0; flex: 1 1 300px; }
.exp-processes .exp-card-title .exp-check { margin-top: 2px; }
.exp-processes .exp-card-name { display: flex; flex-direction: column; gap: 4px; min-width: 0; }
.exp-processes .exp-card-actions { display: flex; flex-wrap: wrap; gap: 6px; }
.exp-processes .exp-badges { display: inline-flex; flex-wrap: wrap; gap: 6px; margin: 0; padding: 0; list-style: none; }
.exp-processes .exp-badge { display: inline-flex; align-items: center; gap: 5px; margin: 0; padding: 2px 9px; border-radius: 999px; font-size: 12px; font-weight: 650; line-height: 1.5; white-space: nowrap; background: var(--wf-soft); color: var(--wf-muted); }
.exp-processes .exp-badge.is-ok { background: var(--wf-ok-bg); color: var(--wf-ok); }
.exp-processes .exp-badge.is-warn { background: var(--wf-warn-bg); color: var(--wf-warn); }
.exp-processes .exp-badge.is-bad { background: var(--wf-bad-bg); color: var(--wf-bad); }
.exp-processes .exp-badge.is-info { background: var(--wf-info-bg); color: var(--wf-info); }
.exp-processes .exp-badge.is-muted { background: var(--wf-soft); color: var(--wf-muted); }
.exp-processes .exp-waiting { margin: 10px 0 0; padding: 8px 12px; border-radius: 9px; background: var(--wf-soft); color: var(--wf-ink); }
.exp-processes .exp-facts { display: grid; grid-template-columns: repeat(auto-fit, minmax(min(100%, 200px), 1fr)); gap: 8px 18px; margin: 12px 0 0; }
.exp-processes .exp-facts > div { min-width: 0; }
.exp-processes .exp-facts dt { margin: 0; font-size: 12px; font-weight: 650; text-transform: uppercase; letter-spacing: .04em; color: var(--wf-muted); }
.exp-processes .exp-facts dd { margin: 2px 0 0; overflow-wrap: anywhere; }
.exp-processes .exp-facts dd .exp-muted { font-size: 13px; }
.exp-processes .exp-tech { margin: 12px 0 0; border-top: 1px solid var(--wf-line); }
.exp-processes .exp-tech > summary { display: flex; gap: 8px; align-items: baseline; padding: 10px 0 0; cursor: pointer; font-size: 13px; color: var(--wf-ink); list-style: none; }
.exp-processes .exp-tech > summary::-webkit-details-marker { display: none; }
.exp-processes .exp-tech > summary::before { content: "\25B8"; flex: 0 0 auto; color: var(--wf-muted); }
.exp-processes .exp-tech[open] > summary::before { content: "\25BE"; }
.exp-processes .exp-tech > summary:hover { color: var(--wf-accent-hover); }
.exp-processes .exp-tech .exp-facts dd { font-size: 13px; }
.exp-processes .exp-empty { margin: 0 0 22px; padding: 22px 18px; border: 1px dashed #c9ced6; border-radius: var(--wf-radius); background: var(--wf-card); color: var(--wf-muted); text-align: center; }
.exp-processes .exp-empty strong { display: block; margin: 0 0 4px; font-size: 15px; color: var(--wf-ink); }

/* Folding help */
.exp-processes .exp-fold { min-width: 0; margin: 0 0 22px; border: 1px solid var(--wf-line); border-radius: var(--wf-radius); background: var(--wf-card); }
.exp-processes .exp-fold > summary { display: flex; flex-wrap: wrap; align-items: baseline; gap: 4px 12px; padding: 12px 16px; cursor: pointer; list-style: none; border-radius: var(--wf-radius); }
.exp-processes .exp-fold > summary::-webkit-details-marker { display: none; }
.exp-processes .exp-fold > summary::before { content: "\25B8"; flex: 0 0 auto; width: 1em; color: var(--wf-muted); }
.exp-processes .exp-fold[open] > summary::before { content: "\25BE"; }
.exp-processes .exp-fold > summary:hover h2 { color: var(--wf-accent-hover); }
.exp-processes .exp-fold-body { min-width: 0; padding: 0 16px 16px; }
.exp-processes .exp-legend { display: grid; grid-template-columns: minmax(0, 1fr); gap: 8px; margin: 0; }
.exp-processes .exp-legend > div { display: grid; grid-template-columns: minmax(150px, 220px) minmax(0, 1fr); gap: 4px 14px; align-items: baseline; }
.exp-processes .exp-legend dt { margin: 0; }
.exp-processes .exp-legend dd { margin: 0; color: var(--wf-ink); }
@media (max-width: 600px) { .exp-processes .exp-legend > div { grid-template-columns: minmax(0, 1fr); } }

/* The confirmation step */
.exp-processes .exp-confirm { margin: 0 0 22px; padding: 18px; border: 1px solid #f1b4b4; border-left: 4px solid var(--wf-bad); border-radius: var(--wf-radius); background: var(--wf-card); }
.exp-processes .exp-confirm h2.exp-h2 { font-size: 18px; margin: 0 0 6px; }
.exp-processes .exp-confirm-list { margin: 14px 0; padding: 0; list-style: none; border: 1px solid var(--wf-line); border-radius: 10px; }
.exp-processes .exp-confirm-list li { margin: 0; padding: 10px 12px; border-top: 1px solid var(--wf-line); overflow-wrap: anywhere; }
.exp-processes .exp-confirm-list li:first-child { border-top: 0; }
.exp-processes .exp-consequences { margin: 6px 0 16px; padding-left: 20px; }
.exp-processes .exp-consequences li { margin: 0 0 6px; }
.exp-processes .exp-confirm-actions { display: flex; flex-wrap: wrap; gap: 10px; }

.exp-processes .exp-pager { margin: 14px 0 0; }
/* The shared page navigator, in this page's colours: its own grey and orange fall short of 4.5:1 on white. */
.exp-processes .exp-pager a { color: var(--wf-accent-hover); }
.exp-processes .exp-pager span.text, .exp-processes .exp-pager span.text a { color: var(--wf-accent-hover); }
.exp-processes .exp-pager span.disabled, .exp-processes .exp-pager span.text.disabled { color: var(--wf-muted); }
.exp-processes .exp-pager span.current { color: var(--wf-ink); }

@media (max-width: 600px) {
    .exp-processes .exp-statusbar { padding: 12px; }
    .exp-processes .exp-status-actions, .exp-processes .exp-status-actions .exp-btn { width: 100%; }
    .exp-processes .exp-card { padding: 12px; }
    .exp-processes .exp-card-actions, .exp-processes .exp-card-actions .exp-btn { width: 100%; }
    .exp-processes .exp-confirm-actions .exp-btn { width: 100%; }
}
</style>
{/literal}

{def $wfp_ctx = 'design/admin/workflow/processlist'
     $wfp_base = 'workflow/processlist'
     $wfp_self = cond( eq( $wfp_status_filter, 'waiting' ), 'workflow/processlist', concat( 'workflow/processlist/(status)/', $wfp_status_filter ) )
     $wfp_listed = 0}
{foreach $wfp_groups as $wfp_group}{set $wfp_listed = sum( $wfp_listed, $wfp_group.count )}{/foreach}

<div class="context-block exp-processes" data-shown="{'Showing %shown of %total processes on this page'|i18n( $wfp_ctx )|wash}">

<div class="box-header"><div class="box-ml">
<h1 class="context-title">{'Workflow processes'|i18n( $wfp_ctx )}</h1>
</div></div>

<div class="box-bc"><div class="box-ml"><div class="box-content">

<p class="exp-intro">{'A workflow process is one run of a workflow for one piece of content that has not finished yet: it waits for an approver, for the workflow cronjob or for the user, or it has stopped. This page shows each one, what it is waiting for, and lets you cancel one that is stuck.'|i18n( $wfp_ctx )}</p>

{foreach $wfp_feedback as $wfp_message}
<div class="exp-feedback {if $wfp_message.ok}is-ok{else}is-bad{/if}" role="{if $wfp_message.ok}status{else}alert{/if}">{$wfp_message.message|wash}</div>
{/foreach}

{if $wfp_confirm_list}
{* ---- The confirmation step: nothing has been cancelled yet. ---- *}
<form method="post" action={$wfp_self|ezurl}>
<section class="exp-confirm" aria-labelledby="wfp-confirm-title">
    <h2 class="exp-h2" id="wfp-confirm-title">{if eq( $wfp_confirm_list|count, 1 )}{'Cancel this workflow process?'|i18n( $wfp_ctx )}{else}{'Cancel these %count workflow processes?'|i18n( $wfp_ctx,, hash( '%count', $wfp_confirm_list|count ) )}{/if}</h2>
    <p class="exp-muted">{'Nothing has been changed yet.'|i18n( $wfp_ctx )}</p>
    <ul class="exp-confirm-list">
    {foreach $wfp_confirm_list as $wfp_p}
        <li>
            <input type="hidden" name="ProcessIDList[]" value="{$wfp_p.id}" />
            <strong>{if $wfp_p.object.name|ne('')}{$wfp_p.object.name|wash}{else}{'Process %id'|i18n( $wfp_ctx,, hash( '%id', $wfp_p.id ) )}{/if}</strong>
            {if $wfp_p.object.version|gt(0)}<span class="exp-muted">&middot; {'version %version'|i18n( $wfp_ctx,, hash( '%version', $wfp_p.object.version ) )}{if $wfp_p.object.version_status_text|ne('')} ({$wfp_p.object.version_status_text|wash}){/if}</span>{/if}
            <br /><span class="exp-muted">{'Process %id'|i18n( $wfp_ctx,, hash( '%id', $wfp_p.id ) )} &middot; {$wfp_p.workflow.name|wash} &middot; {$wfp_p.status.label|wash}{if $wfp_p.user.name|ne('')} &middot; {'started by %user'|i18n( $wfp_ctx,, hash( '%user', $wfp_p.user.name|wash ) )}{/if}</span>
        </li>
    {/foreach}
    </ul>
    <h3>{'What cancelling does'|i18n( $wfp_ctx )}</h3>
    <ul class="exp-consequences">
        <li>{'The workflow stops where it is. Its remaining steps never run, and the operation it held back (publishing, for example) does not happen.'|i18n( $wfp_ctx )}</li>
        <li>{'A version that is waiting to be published becomes a draft again. The author finds it among their drafts and can publish it again, which starts the workflow anew.'|i18n( $wfp_ctx )}</li>
        <li>{"An approval request for it is closed in the approvers' collaboration inbox."|i18n( $wfp_ctx )}</li>
        <li>{'No content is deleted. The cancel cannot be undone.'|i18n( $wfp_ctx )}</li>
    </ul>
    <div class="exp-confirm-actions">
        <button type="submit" class="exp-btn exp-btn-danger" name="CancelProcessesButton" value="1">{if eq( $wfp_confirm_list|count, 1 )}{'Yes, cancel the process'|i18n( $wfp_ctx )}{else}{'Yes, cancel %count processes'|i18n( $wfp_ctx,, hash( '%count', $wfp_confirm_list|count ) )}{/if}</button>
        <button type="submit" class="exp-btn" name="KeepProcessesButton" value="1">{'No, keep them'|i18n( $wfp_ctx )}</button>
    </div>
</section>
</form>

{else}

<form method="post" action={$wfp_self|ezurl}>

{* ---- What is waiting now, and whether the cronjob that resumes it runs. ---- *}
<section class="exp-statusbar" aria-labelledby="wfp-status-title">
    <h2 class="exp-sr" id="wfp-status-title">{'Now'|i18n( $wfp_ctx )}</h2>
    <div class="exp-status">
        {if $wfp_summary.waiting|gt(0)}
        <span class="exp-pill {if $wfp_summary.stuck}is-warn{else}is-info{/if}"><span class="exp-dot" aria-hidden="true"></span>{'Processes waiting: %count'|i18n( $wfp_ctx,, hash( '%count', $wfp_summary.waiting ) )}</span>
        <span class="exp-meta">{'The oldest started %age ago (%time).'|i18n( $wfp_ctx,, hash( '%age', $wfp_summary.oldest_age, '%time', $wfp_summary.oldest|l10n( shortdatetime ) ) )}</span>
        {else}
        <span class="exp-pill is-ok"><span class="exp-dot" aria-hidden="true"></span>{'Nothing is waiting'|i18n( $wfp_ctx )}</span>
        {/if}
        {if $wfp_cronjob.found}
        <span class="exp-meta">
            {'Resumed by the workflow cronjob (%script) in the %part part:'|i18n( $wfp_ctx,, hash( '%script', 'workflow.php', '%part', $wfp_cronjob.label|wash ) )}
            {if $wfp_cronjob.scheduled}
                <strong>{if $wfp_cronjob.schedule_text|ne('')}{$wfp_cronjob.schedule_text|wash}{else}{'scheduled'|i18n( $wfp_ctx )}{/if}</strong>{if $wfp_cronjob.next_run|gt(0)}, {'next run %time'|i18n( $wfp_ctx,, hash( '%time', $wfp_cronjob.next_run|l10n( shortdatetime ) ) )}{/if}.
            {else}
                <strong>{'not scheduled in the crontab'|i18n( $wfp_ctx )}</strong>.
            {/if}
        </span>
        {/if}
    </div>
    {if $wfp_cronjob.found}
    <div class="exp-status-actions">
        <a class="exp-btn" href="{'setup/cronjobs'|ezurl( 'no' )}#{$wfp_cronjob.anchor|wash}">{'Open the cronjob'|i18n( $wfp_ctx )}</a>
    </div>
    {/if}
</section>

{if $wfp_cronjob.found|not}
<div class="exp-feedback is-bad" role="alert">{'No cronjob part runs workflow.php, so processes waiting for the workflow cronjob never move on. Add Scripts[]=workflow.php to a part in cronjob.ini, for example [CronjobPart-frequent].'|i18n( $wfp_ctx )}</div>
{elseif and( $wfp_cronjob.scheduled|not, $wfp_summary.cron|gt(0) )}
<div class="exp-feedback is-warn" role="status">{'%count processes wait for the workflow cronjob, but the crontab does not run the %part part. They move on only when it runs: schedule it, or run it now from the cronjobs page.'|i18n( $wfp_ctx,, hash( '%count', $wfp_summary.cron, '%part', $wfp_cronjob.label|wash ) )} <a href="{'setup/cronjobs'|ezurl( 'no' )}#{$wfp_cronjob.anchor|wash}">{'Go to the cronjobs page'|i18n( $wfp_ctx )}</a></div>
{/if}

{* ---- The overview ---- *}
<section aria-labelledby="wfp-overview-title">
<h2 class="exp-sr" id="wfp-overview-title">{'Overview'|i18n( $wfp_ctx )}</h2>
<ul class="exp-figures">
    <li class="exp-figure"><strong>{$wfp_summary.waiting}</strong><span>{'Waiting'|i18n( $wfp_ctx )}</span></li>
    <li class="exp-figure"><strong>{$wfp_summary.cron}</strong><span>{'For the workflow cronjob'|i18n( $wfp_ctx )}</span></li>
    <li class="exp-figure"><strong>{$wfp_summary.person}</strong><span>{'For the user'|i18n( $wfp_ctx )}</span></li>
    <li class="exp-figure"><strong>{$wfp_summary.parent}</strong><span>{'For a parent workflow'|i18n( $wfp_ctx )}</span></li>
    <li class="exp-figure{if $wfp_summary.failed|gt(0)} is-attention{/if}"><strong>{$wfp_summary.failed}</strong><span>{'Failed'|i18n( $wfp_ctx )}</span></li>
    <li class="exp-figure{if $wfp_summary.stopped|gt( $wfp_summary.failed )} is-attention{/if}"><strong>{$wfp_summary.stopped}</strong><span>{'Stopped, will not move on'|i18n( $wfp_ctx )}</span></li>
    <li class="exp-figure is-last">
        {if $wfp_summary.oldest|gt(0)}
        <strong>{'%age ago'|i18n( $wfp_ctx,, hash( '%age', $wfp_summary.oldest_age ) )}</strong>
        <span>{'Oldest waiting, since %time'|i18n( $wfp_ctx,, hash( '%time', $wfp_summary.oldest|l10n( shortdatetime ) ) )}</span>
        {else}
        <strong>&mdash;</strong>
        <span>{'Oldest waiting'|i18n( $wfp_ctx )}</span>
        {/if}
    </li>
</ul>
</section>

{* ---- The controls: which processes, a search, the page size, and cancelling several at once. ---- *}
<section aria-labelledby="wfp-controls-title">
<h2 class="exp-sr" id="wfp-controls-title">{'Filter and select'|i18n( $wfp_ctx )}</h2>
<div class="exp-toolbar">
    <nav class="exp-field exp-field-wide" aria-labelledby="wfp-show-label">
        <span class="exp-field-label" id="wfp-show-label">{'Show'|i18n( $wfp_ctx )}</span>
        <ul class="exp-chips">
            <li><a class="exp-chip" href={$wfp_base|ezurl}{if eq( $wfp_status_filter, 'waiting' )} aria-current="page"{/if}>{'Waiting'|i18n( $wfp_ctx )} <span class="exp-chip-count">({$wfp_summary.waiting})</span></a></li>
            <li><a class="exp-chip" href={concat( $wfp_base, '/(status)/stopped' )|ezurl}{if eq( $wfp_status_filter, 'stopped' )} aria-current="page"{/if}>{'Stopped'|i18n( $wfp_ctx )} <span class="exp-chip-count">({$wfp_summary.stopped})</span></a></li>
            <li><a class="exp-chip" href={concat( $wfp_base, '/(status)/all' )|ezurl}{if eq( $wfp_status_filter, 'all' )} aria-current="page"{/if}>{'All'|i18n( $wfp_ctx )} <span class="exp-chip-count">({$wfp_summary.total})</span></a></li>
        </ul>
    </nav>
    <div class="exp-field exp-js-only" hidden>
        <label for="wfp-search">{'Find a process'|i18n( $wfp_ctx )}</label>
        <input type="search" id="wfp-search" autocomplete="off" spellcheck="false" aria-controls="wfp-list" aria-describedby="wfp-filter-count" placeholder="{'Content, user, workflow or status'|i18n( $wfp_ctx )}" />
    </div>
    <div class="exp-field">
        <span class="exp-field-label" id="wfp-pagesize-label">{'Processes per page'|i18n( $wfp_ctx )}</span>
        {* The sizes come from admininterface.ini [PaginationSettings] and the preference stores the position in
           that list, so a site can offer the sizes its own editors want. *}
        <p class="exp-pagesize table-preferences" role="group" aria-labelledby="wfp-pagesize-label">
        {foreach $limit_choices as $limit_index => $limit_option}
            {if eq( $limit_index|inc, $limit_choice )}
            <span class="current" aria-current="true">{$limit_option}</span>
            {else}
            <a href={concat( '/user/preferences/set/admin_workflow_processlist_limit/', $limit_index|inc )|ezurl} title="{'Show %count items per page.'|i18n( 'design/admin/node/view/full',, hash( '%count', $limit_option ) )}">{$limit_option}</a>
            {/if}
        {/foreach}
        </p>
    </div>
    {if $wfp_listed|gt(0)}
    <div class="exp-selectbar">
        <label class="exp-check exp-js-only" hidden><input type="checkbox" id="wfp-select-all" aria-controls="wfp-list" /> {'Select all on this page'|i18n( $wfp_ctx )}</label>
        <button type="submit" class="exp-btn exp-btn-outline-danger" id="wfp-cancel-selected" name="ConfirmCancelSelectedButton" value="1">{'Cancel selected processes...'|i18n( $wfp_ctx )}</button>
        <span class="exp-muted">{'You confirm on the next page before anything happens.'|i18n( $wfp_ctx )}</span>
    </div>
    {/if}
    <p class="exp-filter-count exp-js-only" id="wfp-filter-count" aria-live="polite" hidden></p>
</div>
</section>

{* ---- The processes, by the trigger that started them ---- *}
<div id="wfp-list">
{if $wfp_groups}
{foreach $wfp_groups as $wfp_gi => $wfp_group}
<section class="exp-group" aria-labelledby="wfp-group-{$wfp_gi}-title" data-group="1">
    <div class="exp-group-head">
        <h2 class="exp-h2" id="wfp-group-{$wfp_gi}-title">{$wfp_group.label|wash}</h2>
        {if $wfp_group.key|ne('')}<code class="exp-group-key" title="{'The trigger: module, operation and when'|i18n( $wfp_ctx )}">{$wfp_group.key|wash}</code>{/if}
        <span class="exp-badge">{'%count processes'|i18n( $wfp_ctx,, hash( '%count', $wfp_group.count ) )}</span>
    </div>
    {if $wfp_group.key|eq('')}<p class="exp-muted exp-group-note">{'These processes no longer say which trigger started them: their memento is gone, or the trigger was removed since. The workflow cronjob cannot resume them by their operation.'|i18n( $wfp_ctx )}</p>{/if}
    <ul class="exp-cards">
    {foreach $wfp_group.processes as $wfp_p}
    {def $wfp_title = cond( $wfp_p.object.name|ne(''), $wfp_p.object.name, 'Process %id'|i18n( $wfp_ctx,, hash( '%id', $wfp_p.id ) ) )}
    <li class="exp-card{if eq( $wfp_p.status.tone, 'bad' )} is-bad{elseif or( eq( $wfp_p.status.tone, 'warn' ), $wfp_p.stuck )} is-warn{/if}" id="wfp-process-{$wfp_p.id}" data-search="{$wfp_p.search|wash}">
        <div class="exp-card-head">
            <div class="exp-card-title">
                <label class="exp-check"><input type="checkbox" name="SelectedProcessIDList[]" value="{$wfp_p.id}" /><span class="exp-sr">{'Select process %id (%name)'|i18n( $wfp_ctx,, hash( '%id', $wfp_p.id, '%name', $wfp_title|wash ) )}</span></label>
                <div class="exp-card-name">
                    <h3>{if and( $wfp_p.object.exists, $wfp_p.object.version|gt(0) )}<a href={concat( 'content/versionview/', $wfp_p.object.id, '/', $wfp_p.object.version )|ezurl} title="{'View the version this process runs for'|i18n( $wfp_ctx )}">{$wfp_title|wash}</a>{else}{$wfp_title|wash}{/if}</h3>
                    <ul class="exp-badges">
                        <li class="exp-badge is-{$wfp_p.status.tone}">{$wfp_p.status.label|wash}</li>
                        {if $wfp_p.stuck}<li class="exp-badge is-warn">{'No change for %age'|i18n( $wfp_ctx,, hash( '%age', $wfp_p.idle ) )}</li>{/if}
                        {if $wfp_p.collaboration_id|gt(0)}<li class="exp-badge is-info">{'Approval'|i18n( $wfp_ctx )}</li>{/if}
                    </ul>
                </div>
            </div>
            <div class="exp-card-actions">
                <button type="submit" class="exp-btn exp-btn-small exp-btn-outline-danger" name="ConfirmCancelProcessButton" value="{$wfp_p.id}"
                        aria-label="{'Cancel process %id (%name)...'|i18n( $wfp_ctx,, hash( '%id', $wfp_p.id, '%name', $wfp_title|wash ) )}">{'Cancel...'|i18n( $wfp_ctx )}</button>
            </div>
        </div>

        <p class="exp-waiting">{$wfp_p.waiting_for|wash}{if $wfp_p.collaboration_id|gt(0)} <a href={concat( 'collaboration/item/full/', $wfp_p.collaboration_id )|ezurl}>{'Open the approval request'|i18n( $wfp_ctx )}</a>{/if}</p>

        <dl class="exp-facts">
            <div>
                <dt>{'Content'|i18n( $wfp_ctx )}</dt>
                <dd>{if $wfp_p.object.id|gt(0)}{if $wfp_p.object.name|ne('')}{$wfp_p.object.name|wash}{else}{'Object %id'|i18n( $wfp_ctx,, hash( '%id', $wfp_p.object.id ) )}{/if}{if $wfp_p.object.version|gt(0)}<br /><span class="exp-muted">{'version %version'|i18n( $wfp_ctx,, hash( '%version', $wfp_p.object.version ) )}{if $wfp_p.object.version_status_text|ne('')}, {$wfp_p.object.version_status_text|wash}{/if}</span>{/if}{if $wfp_p.object.exists|not}<br /><span class="exp-muted">{'The object no longer exists.'|i18n( $wfp_ctx )}</span>{/if}{else}<span class="exp-muted">{'None'|i18n( $wfp_ctx )}</span>{/if}</dd>
            </div>
            <div>
                <dt>{'Started by'|i18n( $wfp_ctx )}</dt>
                <dd>{if $wfp_p.user.name|ne('')}{if $wfp_p.user.node_url|ne('')}<a href={$wfp_p.user.node_url|ezurl}>{$wfp_p.user.name|wash}</a>{else}{$wfp_p.user.name|wash}{/if}{elseif $wfp_p.user.id|gt(0)}{'User %id'|i18n( $wfp_ctx,, hash( '%id', $wfp_p.user.id ) )}{else}<span class="exp-muted">{'Unknown'|i18n( $wfp_ctx )}</span>{/if}</dd>
            </div>
            <div>
                <dt>{'Workflow'|i18n( $wfp_ctx )}</dt>
                <dd>{if $wfp_p.workflow.name|ne('')}<a href={concat( $module.functions.view.uri, '/', $wfp_p.workflow.id )|ezurl}>{$wfp_p.workflow.name|wash}</a>{else}{'Workflow %id (missing)'|i18n( $wfp_ctx,, hash( '%id', $wfp_p.workflow.id ) )}{/if}</dd>
            </div>
            <div>
                <dt>{'Current step'|i18n( $wfp_ctx )}</dt>
                <dd>{if $wfp_p.current_event.type_name|ne('')}{if $wfp_p.workflow.event_count|gt(0)}{'Step %position of %count'|i18n( $wfp_ctx,, hash( '%position', $wfp_p.current_event.position, '%count', $wfp_p.workflow.event_count ) )}: {/if}{$wfp_p.current_event.type_name|wash}{if $wfp_p.current_event.description|ne('')} <span class="exp-muted">&ldquo;{$wfp_p.current_event.description|wash}&rdquo;</span>{/if}{else}<span class="exp-muted">{'None'|i18n( $wfp_ctx )}</span>{/if}</dd>
            </div>
            <div>
                <dt>{'Started'|i18n( $wfp_ctx )}</dt>
                <dd>{$wfp_p.created|l10n( shortdatetime )}<br /><span class="exp-muted">{'%age ago'|i18n( $wfp_ctx,, hash( '%age', $wfp_p.age ) )}</span></dd>
            </div>
            <div>
                <dt>{'Last change'|i18n( $wfp_ctx )}</dt>
                <dd>{if $wfp_p.modified|gt(0)}{$wfp_p.modified|l10n( shortdatetime )}{else}{$wfp_p.created|l10n( shortdatetime )}{/if}<br /><span class="exp-muted">{'%age ago'|i18n( $wfp_ctx,, hash( '%age', $wfp_p.idle ) )}</span></dd>
            </div>
        </dl>

        <details class="exp-tech">
            <summary>{'Technical details'|i18n( $wfp_ctx )}</summary>
            <dl class="exp-facts">
                <div><dt>{'Process ID'|i18n( $wfp_ctx )}</dt><dd>{$wfp_p.id}</dd></div>
                <div><dt>{'Process status'|i18n( $wfp_ctx )}</dt><dd>({$wfp_p.status.code}) {$wfp_p.status.name|wash}</dd></div>
                <div><dt>{'Current event'|i18n( $wfp_ctx )}</dt><dd>{if $wfp_p.current_event.id|gt(0)}{'status : (%event_status)'|i18n( $wfp_ctx,, hash( '%event_status', $wfp_p.current_event.status.code ) )} {$wfp_p.current_event.status.name|wash}<br />{'event type :'|i18n( $wfp_ctx )} {$wfp_p.current_event.type|wash}{if $wfp_p.current_event.information|ne('')}<br />{'information :'|i18n( $wfp_ctx )} {$wfp_p.current_event.information|wash}{/if}{else}-{/if}</dd></div>
                <div><dt>{'Last event'|i18n( $wfp_ctx )}</dt><dd>{if $wfp_p.last_event.id|gt(0)}{'status : (%last_event_status)'|i18n( $wfp_ctx,, hash( '%last_event_status', $wfp_p.last_event.status.code ) )} {$wfp_p.last_event.status.name|wash}<br />{'event type :'|i18n( $wfp_ctx )} {$wfp_p.last_event.type|wash}{if $wfp_p.last_event.description|ne('')}<br />{'description :'|i18n( $wfp_ctx )} {$wfp_p.last_event.description|wash}{/if}{else}-{/if}</dd></div>
                <div><dt>{'Memento key'|i18n( $wfp_ctx )}</dt><dd>{if $wfp_p.memento_key|ne('')}<code>{$wfp_p.memento_key|wash}</code>{else}-{/if}</dd></div>
            </dl>
        </details>
    </li>
    {undef $wfp_title}
    {/foreach}
    </ul>
</section>
{/foreach}
<p class="exp-empty exp-js-only" id="wfp-no-match" hidden><strong>{'No process matches the search.'|i18n( $wfp_ctx )}</strong>{'Only the processes on this page are searched.'|i18n( $wfp_ctx )}</p>
{else}
<div class="exp-empty">
    {if eq( $wfp_status_filter, 'waiting' )}
    <strong>{'No processes are waiting.'|i18n( $wfp_ctx )}</strong>
    {'That is normal: a process exists only while a workflow waits for someone or something, and is removed when it finishes.'|i18n( $wfp_ctx )}
    {if $wfp_summary.stopped|gt(0)}<br /><a href={concat( $wfp_base, '/(status)/stopped' )|ezurl}>{'Show the %count stopped processes'|i18n( $wfp_ctx,, hash( '%count', $wfp_summary.stopped ) )}</a>{/if}
    {elseif eq( $wfp_status_filter, 'stopped' )}
    <strong>{'No stopped processes.'|i18n( $wfp_ctx )}</strong>
    {'Nothing has failed or been left behind.'|i18n( $wfp_ctx )}
    {else}
    <strong>{'There are no workflow processes.'|i18n( $wfp_ctx )}</strong>
    {/if}
</div>
{/if}
</div>

<div class="exp-pager">
{include name=navigator
         uri='design:navigator/google.tpl'
         page_uri='/workflow/processlist'
         item_count=$list_count
         view_parameters=$view_parameters
         item_limit=$page_limit}
</div>

</form>

{* ---- What the statuses mean ---- *}
<details class="exp-fold" id="wfp-legend">
    <summary><h2 class="exp-h2">{'What the statuses mean'|i18n( $wfp_ctx )}</h2></summary>
    <div class="exp-fold-body">
        <dl class="exp-legend">
            <div><dt><span class="exp-badge is-info">{'Waiting for the workflow cronjob'|i18n( $wfp_ctx )}</span></dt><dd>{'A step asked to be run again later, for example an approval that has not been decided yet. The workflow cronjob runs it each time it comes round.'|i18n( $wfp_ctx )}</dd></div>
            <div><dt><span class="exp-badge is-warn">{'Waiting for the user'|i18n( $wfp_ctx )}</span></dt><dd>{'A step showed the user a page or sent them elsewhere and waits for them. If they never come back, the process stays; cancel it when you are sure.'|i18n( $wfp_ctx )}</dd></div>
            <div><dt><span class="exp-badge is-info">{'Waiting for its parent workflow'|i18n( $wfp_ctx )}</span></dt><dd>{'Started by a multiplexer step of another workflow; it moves on with that one.'|i18n( $wfp_ctx )}</dd></div>
            <div><dt><span class="exp-badge is-bad">{'Failed'|i18n( $wfp_ctx )}</span></dt><dd>{'A step rejected the content or went wrong. Nothing runs it again; cancel it to tidy up and to give a held-back version back to its author.'|i18n( $wfp_ctx )}</dd></div>
            <div><dt><span class="exp-badge is-warn">{'Marked as running'|i18n( $wfp_ctx )}</span></dt><dd>{'Only true while a request runs it. Hours later it means the run was cut off.'|i18n( $wfp_ctx )}</dd></div>
            <div><dt><span class="exp-badge is-muted">{'Cancelled'|i18n( $wfp_ctx )}, {'Reset'|i18n( $wfp_ctx )}, {'Done'|i18n( $wfp_ctx )}</span></dt><dd>{'Finished one way or another; such a process is normally removed at once or by the next cronjob run.'|i18n( $wfp_ctx )}</dd></div>
        </dl>
    </div>
</details>

{/if}

</div></div></div>

</div>

<script type="text/javascript">
{literal}
(function () {
    var root = document.querySelector( '.exp-processes' );
    if ( !root ) return;
    var each = function ( selector, fn ) { Array.prototype.forEach.call( root.querySelectorAll( selector ), fn ); };
    each( '.exp-js-only', function ( el ) { el.hidden = false; } );

    var boxes = function () { return root.querySelectorAll( 'input[name="SelectedProcessIDList[]"]' ); };
    var all = root.querySelector( '#wfp-select-all' );
    var cancelSelected = root.querySelector( '#wfp-cancel-selected' );
    var label = cancelSelected ? cancelSelected.textContent : '';
    var update = function () {
        var list = boxes(), checked = 0, visible = 0;
        Array.prototype.forEach.call( list, function ( box ) {
            if ( !box.closest( '.exp-card' ).hidden ) { visible++; if ( box.checked ) checked++; }
        } );
        if ( all ) { all.checked = visible > 0 && checked === visible; all.indeterminate = checked > 0 && checked < visible; }
        if ( cancelSelected ) cancelSelected.textContent = checked > 0 ? label.replace( /\.\.\.$/, '' ) + ' (' + checked + ')...' : label;
    };
    if ( all ) all.addEventListener( 'change', function () {
        Array.prototype.forEach.call( boxes(), function ( box ) { if ( !box.closest( '.exp-card' ).hidden ) box.checked = all.checked; } );
        update();
    } );
    root.addEventListener( 'change', function ( e ) { if ( e.target && e.target.name === 'SelectedProcessIDList[]' ) update(); } );

    var search = root.querySelector( '#wfp-search' );
    var count = root.querySelector( '#wfp-filter-count' );
    var noMatch = root.querySelector( '#wfp-no-match' );
    if ( search ) search.addEventListener( 'input', function () {
        var words = search.value.toLowerCase().split( /\s+/ ).filter( Boolean ), shown = 0, total = 0;
        each( '.exp-card', function ( card ) {
            var text = card.getAttribute( 'data-search' ) || '';
            var hit = words.every( function ( w ) { return text.indexOf( w ) !== -1; } );
            card.hidden = !hit; total++; if ( hit ) shown++;
        } );
        each( '.exp-group', function ( group ) { group.hidden = !group.querySelector( '.exp-card:not([hidden])' ); } );
        if ( noMatch ) noMatch.hidden = shown > 0;
        if ( count ) count.textContent = words.length ? root.getAttribute( 'data-shown' ).replace( '%shown', shown ).replace( '%total', total ) : '';
        update();
    } );
})();
{/literal}
</script>
