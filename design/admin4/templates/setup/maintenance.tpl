{* Setup > Maintenance: take the public site offline for a maintenance window and back (expMaintenance).

   The page leads with the state - online, or in maintenance with why, by whom, since and until when - and the one
   action that state calls for. Offline, it then says what visitors are shown and who still gets in; online, it is
   the form for the next window: the message, the expected duration, who still gets in, a preview of the page
   visitors will get, and a confirmation before the site goes offline. Below: the same from a shell, the recent
   changes from the audit, and how it works.

   The same file is in design/admin and design/admin4, so every administration design resolves it. Its styling is
   scoped to .exp-maintenance, in the look of the cronjobs page, and takes admin4's tokens where they exist (--a4-*).
   Everything works without javascript: both buttons submit the form, the confirmation is a required checkbox, the
   folding sections are <details>. The script below adds the duration presets, the live preview, the address check
   and the copy buttons. Form field names, button names and the action are those of the earlier page. *}

{* A view class from before the redesign (a server that has not loaded the new one yet) hands over only the
   state; the page then shows the state and the form, without the preview, the history and the commands. *}
{if is_set( $maintenance_info )|not}
    {def $maintenance_info = hash( 'on', $maintenance_on, 'reason', cond( and( $maintenance_on, is_set( $maintenance.reason ) ), $maintenance.reason, 'manual' ),
                                   'by', cond( is_set( $maintenance.by ), $maintenance.by, '' ),
                                   'since', cond( is_set( $maintenance.since ), $maintenance.since, 0 ),
                                   'until', cond( is_set( $maintenance.until ), $maintenance.until, 0 ),
                                   'lease', 0, 'running_text', '', 'remaining_text', '', 'overdue', false(), 'overdue_text', '',
                                   'message', cond( is_set( $maintenance.message ), $maintenance.message, '' ),
                                   'custom_message', and( is_set( $maintenance.message ), $maintenance.message|ne( '' ) ),
                                   'allow_ips', cond( is_set( $maintenance.allow_ips ), $maintenance.allow_ips, array() ),
                                   'allow_paths', cond( is_set( $maintenance.allow_paths ), $maintenance.allow_paths, array() ),
                                   'admin_open', true(), 'client_allowed', false(), 'run', '', 'page', '' )
         $maintenance_preview = false()
         $maintenance_history = false()
         $maintenance_commands = false()
         $maintenance_presets = array()
         $maintenance_max_minutes = 10080}
{/if}
{def $mi = $maintenance_info}

{literal}
<style>
.exp-maintenance {
    --mt-ink: var(--a4-ink, #1f2430);
    --mt-muted: var(--a4-muted, #5d6573);
    --mt-line: var(--a4-line, #e3e6eb);
    --mt-soft: var(--a4-soft, #f6f7f9);
    --mt-card: #fff;
    --mt-accent: #c2410c;          /* white text on it is 5.2:1 */
    --mt-accent-hover: #9a3412;
    --mt-ring: rgba(194, 65, 12, 0.45);
    --mt-ok: #166534;   --mt-ok-bg: #e7f5ea;   --mt-ok-line: #b7dfc1;
    --mt-warn: #8a4b00; --mt-warn-bg: #fff3df; --mt-warn-line: #f1c98a;
    --mt-bad: #b91c1c;  --mt-bad-bg: #fdecec;  --mt-bad-line: #f1b4b4;
    --mt-info: #1e4fa8; --mt-info-bg: #e8effd;
    --mt-radius: 12px;
    --mt-mono: ui-monospace, SFMono-Regular, Menlo, Consolas, monospace;
    color: var(--mt-ink);
    font-size: 14px;
    line-height: 1.5;
}
.exp-maintenance *, .exp-maintenance *::before, .exp-maintenance *::after { box-sizing: border-box; }
.exp-maintenance [hidden] { display: none !important; }
.exp-maintenance .box-content { padding-bottom: 24px; container: exp-mt / inline-size; }
.exp-maintenance h1.context-title { margin: 0; }
.exp-maintenance h2.exp-h2 { margin: 0; padding: 0; border: 0; background: none; font-size: 16px; font-weight: 650; color: var(--mt-ink); }
.exp-maintenance h3 { margin: 0; padding: 0; border: 0; background: none; font-size: 15px; font-weight: 650; color: var(--mt-ink); }
.exp-maintenance p { margin: 0; }
.exp-maintenance code { font-family: var(--mt-mono); font-size: 12.5px; overflow-wrap: anywhere; }
.exp-maintenance :focus-visible { outline: 3px solid var(--mt-ring); outline-offset: 2px; }
.exp-maintenance .exp-sr { position: absolute; width: 1px; height: 1px; margin: -1px; padding: 0; overflow: hidden; clip: rect(0 0 0 0); white-space: nowrap; border: 0; }
.exp-maintenance .exp-muted, .exp-maintenance .exp-meta { color: var(--mt-muted); }
.exp-maintenance .exp-meta { font-size: 13px; }

.exp-maintenance .exp-intro { margin: 10px 0 18px; max-width: 72ch; color: var(--mt-muted); }
.exp-maintenance label, .exp-maintenance .exp-check span { white-space: normal; }
.exp-maintenance .exp-meta, .exp-maintenance .exp-hint { overflow-wrap: anywhere; }

/* Messages */
.exp-maintenance .exp-feedback { margin: 0 0 14px; padding: 10px 14px; border: 1px solid; border-left-width: 4px; border-radius: 10px; }
.exp-maintenance .exp-feedback.is-ok { border-color: var(--mt-ok-line); border-left-color: var(--mt-ok); background: var(--mt-ok-bg); color: var(--mt-ok); }
.exp-maintenance .exp-feedback.is-bad { border-color: var(--mt-bad-line); border-left-color: var(--mt-bad); background: var(--mt-bad-bg); color: var(--mt-bad); }
.exp-maintenance .exp-feedback.is-warn { border-color: var(--mt-warn-line); border-left-color: var(--mt-warn); background: var(--mt-warn-bg); color: var(--mt-warn); }
.exp-maintenance .exp-feedback p + p { margin-top: 4px; }

/* The state */
.exp-maintenance .exp-state {
    display: grid; grid-template-columns: minmax(0, 1fr) auto; gap: 14px 24px; align-items: center;
    margin: 0 0 20px; padding: 18px 20px; border: 1px solid var(--mt-ok-line); border-radius: var(--mt-radius);
    background: var(--mt-ok-bg); box-shadow: inset 5px 0 0 var(--mt-ok);
}
.exp-maintenance .exp-state.is-on { border-color: var(--mt-warn-line); background: var(--mt-warn-bg); box-shadow: inset 5px 0 0 var(--mt-warn); }
.exp-maintenance .exp-state-head { display: flex; flex-wrap: wrap; align-items: center; gap: 6px 12px; }
.exp-maintenance .exp-state-title { flex: 1 1 14ch; min-width: 0; margin: 0; padding: 0; border: 0; background: none; font-size: 20px; line-height: 1.25; font-weight: 700; color: var(--mt-ok); }
.exp-maintenance .exp-state.is-on .exp-state-title { color: var(--mt-warn); }
.exp-maintenance .exp-state-text { margin: 4px 0 0; max-width: 70ch; color: var(--mt-ink); }
.exp-maintenance .exp-dot { flex: 0 0 auto; width: 12px; height: 12px; border-radius: 50%; background: var(--mt-ok); box-shadow: 0 0 0 4px rgba(22, 101, 52, 0.16); }
.exp-maintenance .exp-state.is-on .exp-dot { background: #c26a00; box-shadow: 0 0 0 4px rgba(194, 106, 0, 0.2); animation: exp-mt-pulse 1.6s ease-in-out infinite; }
@keyframes exp-mt-pulse { 0%, 100% { opacity: 1; } 50% { opacity: .4; } }
@media (prefers-reduced-motion: reduce) { .exp-maintenance .exp-state.is-on .exp-dot { animation: none; } }
.exp-maintenance .exp-state .exp-facts { grid-column: 1 / -1; margin: 0; padding: 12px 0 0; border-top: 1px solid rgba(138, 75, 0, 0.18); }
.exp-maintenance .exp-state-action { display: flex; flex-direction: column; align-items: flex-end; gap: 6px; }
.exp-maintenance .exp-state-action .exp-meta { color: var(--mt-ink); text-align: right; max-width: 30ch; }

.exp-maintenance .exp-facts { display: grid; grid-template-columns: repeat(auto-fit, minmax(min(100%, 180px), 1fr)); gap: 10px 18px; margin: 12px 0 0; }
.exp-maintenance .exp-facts > div { min-width: 0; }
.exp-maintenance .exp-facts dt { margin: 0; font-size: 12px; font-weight: 650; text-transform: uppercase; letter-spacing: .04em; color: var(--mt-muted); }
.exp-maintenance .exp-state .exp-facts dt { color: #5c4214; }
.exp-maintenance .exp-facts dd { margin: 2px 0 0; overflow-wrap: anywhere; }
.exp-maintenance .exp-facts dd .exp-badge { white-space: normal; }
.exp-maintenance .exp-check .exp-hint { font-weight: 400; }

/* Badges */
.exp-maintenance .exp-badge { display: inline-flex; align-items: center; gap: 5px; margin: 0; padding: 2px 9px; border-radius: 999px; font-size: 12px; font-weight: 650; line-height: 1.5; white-space: nowrap; background: var(--mt-soft); color: var(--mt-muted); }
.exp-maintenance .exp-badge.is-ok { background: var(--mt-ok-bg); color: var(--mt-ok); }
.exp-maintenance .exp-badge.is-warn { background: var(--mt-warn-bg); color: var(--mt-warn); }
.exp-maintenance .exp-badge.is-bad { background: var(--mt-bad-bg); color: var(--mt-bad); }
.exp-maintenance .exp-badge.is-info { background: var(--mt-info-bg); color: var(--mt-info); }
.exp-maintenance .exp-state .exp-badge { background: #fff; }

/* Buttons */
.exp-maintenance .exp-btn {
    display: inline-flex; align-items: center; justify-content: center; gap: 8px; min-height: 36px; margin: 0;
    padding: 6px 14px; border: 1px solid #c9ced6; border-radius: 9px; background: #fff; color: var(--mt-ink);
    font: 600 13.5px/1.2 inherit; font-family: inherit; cursor: pointer; white-space: nowrap; text-decoration: none;
}
.exp-maintenance .exp-btn:hover:not([disabled]) { border-color: var(--mt-accent); color: var(--mt-accent-hover); }
.exp-maintenance .exp-btn-large { min-height: 44px; padding: 10px 20px; font-size: 15px; }
.exp-maintenance .exp-btn-go { border-color: var(--mt-ok); background: var(--mt-ok); color: #fff; }
.exp-maintenance .exp-btn-go:hover:not([disabled]) { border-color: #0f4a26; background: #0f4a26; color: #fff; }
.exp-maintenance .exp-btn-danger { border-color: var(--mt-bad); background: var(--mt-bad); color: #fff; }
.exp-maintenance .exp-btn-danger:hover:not([disabled]) { border-color: #8f1414; background: #8f1414; color: #fff; }
.exp-maintenance .exp-btn-small { min-height: 30px; padding: 4px 10px; font-size: 12.5px; }
.exp-maintenance .exp-btn svg { flex: 0 0 auto; }

/* The form */
.exp-maintenance .exp-plan { display: grid; grid-template-columns: minmax(0, 1fr) minmax(0, 0.9fr); gap: 20px; align-items: start; margin: 0 0 22px; }
.exp-maintenance .exp-card { min-width: 0; margin: 0; padding: 16px 18px; border: 1px solid var(--mt-line); border-radius: var(--mt-radius); background: var(--mt-card); box-shadow: 0 1px 2px rgba(16, 24, 40, 0.04); }
.exp-maintenance .exp-card + .exp-card { margin-top: 14px; }
.exp-maintenance .exp-card-head { display: flex; flex-wrap: wrap; align-items: baseline; gap: 4px 12px; margin: 0 0 12px; }
.exp-maintenance .exp-step { display: inline-flex; align-items: center; justify-content: center; flex: 0 0 auto; width: 24px; height: 24px; border-radius: 50%; background: var(--mt-soft); border: 1px solid var(--mt-line); font-size: 12.5px; font-weight: 700; color: var(--mt-ink); }
.exp-maintenance .exp-steps { display: flex; flex-direction: column; gap: 18px; margin: 0; padding: 0; list-style: none; }
.exp-maintenance .exp-steps > li { display: grid; grid-template-columns: 24px minmax(0, 1fr); gap: 4px 12px; margin: 0; padding: 0 0 18px; border-bottom: 1px solid var(--mt-line); }
.exp-maintenance .exp-steps > li:last-child { padding-bottom: 0; border-bottom: 0; }
.exp-maintenance .exp-step-body { min-width: 0; display: flex; flex-direction: column; gap: 6px; }
.exp-maintenance .exp-step-body > label, .exp-maintenance .exp-step-title { margin: 0; padding: 0; font-size: 14px; font-weight: 650; color: var(--mt-ink); }
.exp-maintenance .exp-hint { font-size: 12.5px; color: var(--mt-muted); }
.exp-maintenance .exp-step-body textarea,
.exp-maintenance .exp-step-body input[type="number"] {
    width: 100%; min-height: 38px; margin: 0; padding: 7px 10px; border: 1px solid #b9bfc9; border-radius: 9px;
    background: #fff; color: var(--mt-ink); font: inherit; font-size: 14px; line-height: 1.45;
}
.exp-maintenance .exp-step-body textarea { resize: vertical; }
.exp-maintenance .exp-step-body textarea.is-code { font-family: var(--mt-mono); font-size: 13px; }
.exp-maintenance .exp-step-body input[type="number"] { width: 9em; }
.exp-maintenance .exp-step-body textarea:focus, .exp-maintenance .exp-step-body input[type="number"]:focus { border-color: var(--mt-accent); outline: 3px solid var(--mt-ring); outline-offset: 0; }
.exp-maintenance .exp-step-body textarea[aria-invalid="true"] { border-color: var(--mt-bad); }
.exp-maintenance .exp-row { display: flex; flex-wrap: wrap; align-items: center; gap: 8px 10px; }
.exp-maintenance .exp-presets { display: flex; flex-wrap: wrap; gap: 6px; }
.exp-maintenance .exp-preset { min-height: 32px; padding: 4px 12px; border: 1px solid #c9ced6; border-radius: 999px; background: #fff; color: var(--mt-ink); font: inherit; font-size: 13px; cursor: pointer; }
.exp-maintenance .exp-preset:hover { border-color: var(--mt-accent); color: var(--mt-accent-hover); }
.exp-maintenance .exp-preset[aria-pressed="true"] { border-color: var(--mt-accent); background: var(--mt-accent); color: #fff; font-weight: 650; }
.exp-maintenance .exp-check { display: flex; align-items: flex-start; gap: 10px; margin: 0; padding: 0; font-weight: 400; color: var(--mt-ink); cursor: pointer; }
.exp-maintenance .exp-check input { flex: 0 0 auto; width: 18px; height: 18px; margin: 1px 0 0; accent-color: var(--mt-accent); }
.exp-maintenance .exp-access { margin: 0; padding: 0; list-style: none; display: flex; flex-direction: column; gap: 6px; }
.exp-maintenance .exp-access li { display: flex; flex-wrap: wrap; align-items: baseline; gap: 4px 10px; margin: 0; padding: 8px 10px; border: 1px solid var(--mt-line); border-radius: 9px; background: var(--mt-soft); }
.exp-maintenance .exp-access li strong { font-weight: 650; }
.exp-maintenance .exp-access code { color: var(--mt-ink); }
.exp-maintenance .exp-ipcheck { margin: 0; font-size: 13px; font-weight: 650; color: var(--mt-bad); }
.exp-maintenance .exp-counter { align-self: flex-end; font-size: 12px; color: var(--mt-muted); font-variant-numeric: tabular-nums; }

.exp-maintenance .exp-confirm { margin: 18px 0 0; padding: 14px 16px; border: 1px solid var(--mt-bad-line); border-radius: var(--mt-radius); background: var(--mt-bad-bg); }
.exp-maintenance .exp-confirm .exp-check { color: #7f1515; font-weight: 600; }
.exp-maintenance .exp-confirm .exp-check input { accent-color: var(--mt-bad); }
.exp-maintenance .exp-confirm-row { display: flex; flex-wrap: wrap; align-items: center; gap: 10px 16px; margin: 12px 0 0; }
.exp-maintenance .exp-confirm-row .exp-meta { color: #7f1515; }

/* The preview */
.exp-maintenance .exp-preview { position: sticky; top: 12px; }
.exp-maintenance .exp-frame-wrap { overflow: hidden; border: 1px solid var(--mt-line); border-radius: 10px; background: var(--mt-soft); }
.exp-maintenance .exp-frame-bar { display: flex; align-items: center; gap: 6px; padding: 7px 10px; border-bottom: 1px solid var(--mt-line); font-size: 12px; color: var(--mt-muted); }
.exp-maintenance .exp-frame-bar span[aria-hidden] { width: 9px; height: 9px; border-radius: 50%; background: #c9ced6; }
.exp-maintenance .exp-frame-bar code { margin-left: 6px; min-width: 0; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; color: var(--mt-muted); }
.exp-maintenance .exp-frame { display: block; width: 100%; height: 440px; margin: 0; border: 0; background: #fff; }
.exp-maintenance .exp-preview .exp-meta { margin: 8px 0 0; }

/* Folding sections */
.exp-maintenance .exp-fold { min-width: 0; margin: 0 0 14px; border: 1px solid var(--mt-line); border-radius: var(--mt-radius); background: var(--mt-card); }
.exp-maintenance .exp-fold > summary { display: flex; flex-wrap: wrap; align-items: baseline; gap: 4px 12px; padding: 12px 16px; cursor: pointer; list-style: none; border-radius: var(--mt-radius); }
.exp-maintenance .exp-fold > summary::-webkit-details-marker { display: none; }
.exp-maintenance .exp-fold > summary::before { content: "\25B8"; flex: 0 0 auto; width: 1em; color: var(--mt-muted); }
.exp-maintenance .exp-fold[open] > summary::before { content: "\25BE"; }
.exp-maintenance .exp-fold > summary:hover h2 { color: var(--mt-accent-hover); }
.exp-maintenance .exp-fold-body { min-width: 0; max-width: 100%; padding: 0 16px 16px; }
.exp-maintenance .exp-fold-body p { margin: 0 0 8px; }
.exp-maintenance .exp-fold-body ul.exp-plain { margin: 0 0 8px; padding: 0 0 0 18px; }
.exp-maintenance .exp-fold-body ul.exp-plain li { margin: 0 0 4px; }

.exp-maintenance .exp-cmds { margin: 0; padding: 0; list-style: none; display: flex; flex-direction: column; gap: 8px; }
.exp-maintenance .exp-cmd-row { display: flex; align-items: center; gap: 8px; margin: 0; }
.exp-maintenance .exp-cmd-row .exp-cmd-label { flex: 0 0 7.5em; font-size: 12px; font-weight: 650; color: var(--mt-muted); }
.exp-maintenance .exp-cmd { flex: 1 1 auto; min-width: 0; padding: 6px 10px; border: 1px solid var(--mt-line); border-radius: 8px; background: var(--mt-soft); color: var(--mt-ink); overflow-wrap: anywhere; user-select: all; -webkit-user-select: all; }

.exp-maintenance .exp-table-wrap { overflow-x: auto; border: 1px solid var(--mt-line); border-radius: var(--mt-radius); background: var(--mt-card); }
.exp-maintenance .exp-table { width: 100%; margin: 0; border-collapse: collapse; }
.exp-maintenance .exp-table th, .exp-maintenance .exp-table td { padding: 9px 12px; border: 0; border-bottom: 1px solid var(--mt-line); text-align: left; vertical-align: top; background: transparent; color: var(--mt-ink); }
.exp-maintenance .exp-table th { font-size: 12px; font-weight: 650; text-transform: uppercase; letter-spacing: .04em; color: var(--mt-muted); background: var(--mt-soft); white-space: nowrap; }
.exp-maintenance .exp-table tr:last-child td { border-bottom: 0; }
.exp-maintenance .exp-table .exp-nowrap { white-space: nowrap; }
.exp-maintenance .exp-table a, .exp-maintenance .exp-fold-body a, .exp-maintenance .exp-link { color: var(--mt-accent-hover); }
.exp-maintenance .exp-empty { margin: 0; padding: 16px; border: 1px dashed #c9ced6; border-radius: var(--mt-radius); color: var(--mt-muted); text-align: center; }

@media (max-width: 1100px) {
    .exp-maintenance .exp-plan { grid-template-columns: minmax(0, 1fr); }
    .exp-maintenance .exp-preview { position: static; }
    .exp-maintenance .exp-frame { height: 400px; }
}
/* By the width the page really has: the side menus narrow it far more than the window says */
@container exp-mt (max-width: 760px) {
    .exp-maintenance .exp-plan { grid-template-columns: minmax(0, 1fr); }
    .exp-maintenance .exp-preview { position: static; }
}
@container exp-mt (max-width: 640px) {
    .exp-maintenance .exp-table thead { position: absolute; width: 1px; height: 1px; overflow: hidden; clip: rect(0 0 0 0); }
    .exp-maintenance .exp-table tr { display: block; padding: 8px 0; border-bottom: 1px solid var(--mt-line); }
    .exp-maintenance .exp-table tr:last-child { border-bottom: 0; }
    .exp-maintenance .exp-table td { display: flex; gap: 10px; padding: 3px 12px; border: 0; }
    .exp-maintenance .exp-table td::before { content: attr(data-label); flex: 0 0 8.5em; font-size: 12px; font-weight: 650; color: var(--mt-muted); }
}
@container exp-mt (max-width: 560px) {
    .exp-maintenance .exp-state { grid-template-columns: minmax(0, 1fr); padding: 14px; }
    .exp-maintenance .exp-state-action { align-items: stretch; }
    .exp-maintenance .exp-state-action .exp-meta { text-align: left; max-width: none; }
    .exp-maintenance .exp-state-action .exp-btn, .exp-maintenance .exp-confirm-row .exp-btn { width: 100%; white-space: normal; }
    .exp-maintenance .exp-card { padding: 14px 12px; }
    .exp-maintenance .exp-steps > li { grid-template-columns: minmax(0, 1fr); }
    .exp-maintenance .exp-cmd-row { flex-wrap: wrap; }
    .exp-maintenance .exp-cmd-row .exp-cmd-label { flex-basis: 100%; }
    .exp-maintenance .exp-frame { height: 360px; }
}
</style>
{/literal}

<div class="context-block exp-maintenance">

<div class="box-header"><div class="box-ml">
<h1 class="context-title">{'Maintenance'|i18n( 'design/admin/setup/maintenance' )}</h1>
</div></div>

<div class="box-bc"><div class="box-ml"><div class="box-content">

<p class="exp-intro">{'Takes the public site offline while you work on it: every visitor gets a maintenance page instead of the site, and the administration stays reachable so the site can be brought back from here.'|i18n( 'design/admin/setup/maintenance' )}</p>

{if $feedback}
<div class="exp-feedback {if $feedback[0]}is-ok{else}is-bad{/if}" role="{if $feedback[0]}status{else}alert{/if}">
    <p><span class="exp-meta">[{currentdate()|l10n( shortdatetime )}]</span> {$feedback[1]|wash}</p>
    {if is_set( $feedback[2] )}<p>{$feedback[2]|wash}</p>{/if}
</div>
{/if}

<form name="maintenanceform" method="post" action={'/setup/maintenance/'|ezurl}>

{* The state, and the one action it calls for *}
<section class="exp-state{if $mi.on} is-on{/if}" aria-labelledby="maintenance-state-title">
    <div>
        <div class="exp-state-head">
            <span class="exp-dot" aria-hidden="true"></span>
            <h2 class="exp-state-title" id="maintenance-state-title">{if $mi.on}{'The site is in maintenance mode'|i18n( 'design/admin/setup/maintenance' )}{else}{'The site is online'|i18n( 'design/admin/setup/maintenance' )}{/if}</h2>
            {if $mi.on}
                {if eq( $mi.reason, 'manual' )}<span class="exp-badge is-warn">{'Maintenance window'|i18n( 'design/admin/setup/maintenance' )}</span>
                {elseif eq( $mi.reason, 'setup' )}<span class="exp-badge is-info">{'Setup wizard running'|i18n( 'design/admin/setup/maintenance' )}</span>
                {elseif eq( $mi.reason, 'install' )}<span class="exp-badge is-info">{'An installation is running'|i18n( 'design/admin/setup/maintenance' )}</span>
                {else}<span class="exp-badge is-bad">{'Unreadable marker'|i18n( 'design/admin/setup/maintenance' )}</span>{/if}
                {if $mi.overdue}<span class="exp-badge is-bad">{'Past the expected end'|i18n( 'design/admin/setup/maintenance' )}</span>{/if}
            {/if}
        </div>
        <p class="exp-state-text">{if $mi.on}{'Visitors get the maintenance page (503). The administration stays reachable.'|i18n( 'design/admin/setup/maintenance' )}{else}{'Visitors get the site as usual. Prepare a maintenance window below; nothing changes until you confirm it.'|i18n( 'design/admin/setup/maintenance' )}{/if}</p>
    </div>
    {if $mi.on}
    <div class="exp-state-action">
        <button type="submit" class="exp-btn exp-btn-go exp-btn-large" name="SwitchOffButton" value="1"><svg width="16" height="16" viewBox="0 0 16 16" aria-hidden="true" focusable="false"><path fill="currentColor" d="M6.2 11.6 2.7 8.1l1.1-1.1 2.4 2.4 6-6 1.1 1.1z"/></svg>{'Bring the site back online'|i18n( 'design/admin/setup/maintenance' )}</button>
        {if or( eq( $mi.reason, 'setup' ), eq( $mi.reason, 'install' ) )}
        <span class="exp-meta">{'An installation is still running: visitors would meet a half-built site.'|i18n( 'design/admin/setup/maintenance' )}</span>
        {/if}
    </div>
    <dl class="exp-facts">
        {if $mi.by|ne( '' )}<div><dt>{'Switched on by'|i18n( 'design/admin/setup/maintenance' )}</dt><dd>{$mi.by|wash}</dd></div>{/if}
        <div><dt>{'Since'|i18n( 'design/admin/setup/maintenance' )}</dt>
            <dd>{if $mi.since|gt(0)}{$mi.since|l10n( shortdatetime )}{if $mi.running_text|ne( '' )} <span class="exp-meta">({'for %duration'|i18n( 'design/admin/setup/maintenance',, hash( '%duration', $mi.running_text ) )})</span>{/if}{else}&mdash;{/if}</dd></div>
        <div><dt>{'Expected back'|i18n( 'design/admin/setup/maintenance' )}</dt>
            <dd>{if $mi.until|gt(0)}{$mi.until|l10n( shortdatetime )}
                {if $mi.overdue}<br /><span class="exp-badge is-bad">{if $mi.overdue_text|ne( '' )}{'%duration ago, and it does not end by itself'|i18n( 'design/admin/setup/maintenance',, hash( '%duration', $mi.overdue_text ) )}{else}{'Passed, and it does not end by itself'|i18n( 'design/admin/setup/maintenance' )}{/if}</span>{/if}
                {if $mi.remaining_text|ne( '' )} <span class="exp-meta">({'in %duration'|i18n( 'design/admin/setup/maintenance',, hash( '%duration', $mi.remaining_text ) )})</span>{/if}
            {else}<span class="exp-muted">{'Not given'|i18n( 'design/admin/setup/maintenance' )}</span>{/if}</dd></div>
        {if $mi.lease|gt(0)}<div><dt>{'Ends unless the wizard is used again'|i18n( 'design/admin/setup/maintenance' )}</dt><dd>{$mi.lease|l10n( shortdatetime )}</dd></div>{/if}
        <div><dt>{'You'|i18n( 'design/admin/setup/maintenance' )}</dt>
            <dd>{if $mi.client_allowed}{'See the public site (%ip is allowed)'|i18n( 'design/admin/setup/maintenance',, hash( '%ip', $client_ip|wash ) )}{else}{'See the maintenance page on the public site (%ip)'|i18n( 'design/admin/setup/maintenance',, hash( '%ip', $client_ip|wash ) )}{/if}</dd></div>
    </dl>
    {/if}
</section>

{if $mi.on}
{* Offline: what visitors are told and who still gets in *}
<div class="exp-plan">
    <div>
        <section class="exp-card" aria-labelledby="maintenance-told-title">
            <div class="exp-card-head"><h2 class="exp-h2" id="maintenance-told-title">{'What visitors are told'|i18n( 'design/admin/setup/maintenance' )}</h2></div>
            {if $mi.custom_message}<p>{$mi.message|wash}</p>{else}<p class="exp-muted">{'No message of its own: the page says its default text.'|i18n( 'design/admin/setup/maintenance' )}</p>{/if}
        </section>
        <section class="exp-card" aria-labelledby="maintenance-who-title">
            <div class="exp-card-head"><h2 class="exp-h2" id="maintenance-who-title">{'Who still gets in'|i18n( 'design/admin/setup/maintenance' )}</h2></div>
            <ul class="exp-access">
                {foreach $mi.allow_paths as $mt_path}
                <li><strong>{if or( eq( $mt_path, '/admin' ), eq( $mt_path, 'admin' ), eq( $mt_path, '/admin/' ) )}{'The administration'|i18n( 'design/admin/setup/maintenance' )}{else}{'Pages below'|i18n( 'design/admin/setup/maintenance' )}{/if}</strong> <code>{$mt_path|wash}</code></li>
                {/foreach}
                {foreach $mi.allow_ips as $mt_ip}
                <li><strong>{'Address'|i18n( 'design/admin/setup/maintenance' )}</strong> <code>{$mt_ip|wash}</code>{if eq( $mt_ip, $client_ip )} <span class="exp-badge is-info">{'You'|i18n( 'design/admin/setup/maintenance' )}</span>{/if}</li>
                {/foreach}
                {if and( $mi.allow_paths|count|eq(0), $mi.allow_ips|count|eq(0) )}
                <li><span class="exp-muted">{'Nobody: every page request gets the maintenance page.'|i18n( 'design/admin/setup/maintenance' )}</span></li>
                {/if}
            </ul>
        </section>
    </div>
{else}
{* Online: the next window, step by step *}
<div class="exp-plan">
    <section class="exp-card" aria-labelledby="maintenance-plan-title">
        <div class="exp-card-head"><h2 class="exp-h2" id="maintenance-plan-title">{'Plan the maintenance window'|i18n( 'design/admin/setup/maintenance' )}</h2></div>
        <ol class="exp-steps">
            <li>
                <span class="exp-step" aria-hidden="true">1</span>
                <div class="exp-step-body">
                    <label for="MaintenanceMessage">{'Message for visitors (optional)'|i18n( 'design/admin/setup/maintenance' )}</label>
                    <textarea id="MaintenanceMessage" name="MaintenanceMessage" rows="3" maxlength="500" aria-describedby="maintenance-message-hint maintenance-message-count"></textarea>
                    <span class="exp-hint" id="maintenance-message-hint">{'Left empty, the page says: %text'|i18n( 'design/admin/setup/maintenance',, hash( '%text', concat( '"', 'We are working on the site and will be back shortly.', '"' ) ) )|wash}</span>
                    <span class="exp-counter exp-js-only" id="maintenance-message-count" hidden></span>
                </div>
            </li>
            <li>
                <span class="exp-step" aria-hidden="true">2</span>
                <div class="exp-step-body">
                    <label for="MaintenanceMinutes">{'Expected duration in minutes (optional)'|i18n( 'design/admin/setup/maintenance' )}</label>
                    <div class="exp-row">
                        <input type="number" min="0" max="{$maintenance_max_minutes}" step="1" inputmode="numeric" id="MaintenanceMinutes" name="MaintenanceMinutes" value="" aria-describedby="maintenance-minutes-hint maintenance-minutes-result" />
                        <div class="exp-presets exp-js-only" role="group" aria-label="{'Common durations'|i18n( 'design/admin/setup/maintenance' )|wash}" hidden>
                        {foreach $maintenance_presets as $mt_preset}
                            <button type="button" class="exp-preset" data-minutes="{$mt_preset.minutes}" aria-pressed="false">{$mt_preset.label|wash}</button>
                        {/foreach}
                        </div>
                    </div>
                    <span class="exp-hint" id="maintenance-minutes-result" aria-live="polite"></span>
                    <span class="exp-hint" id="maintenance-minutes-hint">{'Visitors are told when to expect the site back, and search engines when to try again. Maintenance does not end by itself: you switch it off here.'|i18n( 'design/admin/setup/maintenance' )}</span>
                </div>
            </li>
            <li>
                <span class="exp-step" aria-hidden="true">3</span>
                <div class="exp-step-body">
                    <span class="exp-step-title" id="maintenance-who-title">{'Who still gets in'|i18n( 'design/admin/setup/maintenance' )}</span>
                    <ul class="exp-access" aria-labelledby="maintenance-who-title">
                        <li><strong>{'The administration'|i18n( 'design/admin/setup/maintenance' )}</strong> <code>/admin</code> <span class="exp-meta">{'always, so the site can be brought back from here'|i18n( 'design/admin/setup/maintenance' )}</span></li>
                    </ul>
                    <label class="exp-check" for="MaintenanceAllowMe"><input type="checkbox" id="MaintenanceAllowMe" name="MaintenanceAllowMe" value="1" /> <span>{'Let my own address (%ip) still see the site'|i18n( 'design/admin/setup/maintenance',, hash( '%ip', $client_ip|wash ) )}<br /><span class="exp-hint">{'To check your work on the public site while everyone else gets the maintenance page.'|i18n( 'design/admin/setup/maintenance' )}</span></span></label>
                    <label for="MaintenanceAllowIPs">{'Other addresses that still see the site (optional)'|i18n( 'design/admin/setup/maintenance' )}</label>
                    <textarea class="is-code" id="MaintenanceAllowIPs" name="MaintenanceAllowIPs" rows="2" spellcheck="false" autocomplete="off" aria-describedby="maintenance-ips-hint maintenance-ip-check"></textarea>
                    <span class="exp-hint" id="maintenance-ips-hint">{'IPv4 or IPv6 addresses, separated by commas, spaces or new lines; for example the testers of your team. Ranges are not supported.'|i18n( 'design/admin/setup/maintenance' )}</span>
                    <p class="exp-ipcheck" id="maintenance-ip-check" aria-live="polite" hidden></p>
                </div>
            </li>
        </ol>

        {* The guard: a required checkbox, so the browser refuses to send the form without it, script or not *}
        <div class="exp-confirm">
            <label class="exp-check" for="maintenance-confirm"><input type="checkbox" id="maintenance-confirm" required="required" /> <span>{'I understand that the public site goes offline for every visitor not listed above, until I switch maintenance off again.'|i18n( 'design/admin/setup/maintenance' )}</span></label>
            <div class="exp-confirm-row">
                <button type="submit" class="exp-btn exp-btn-danger exp-btn-large" name="SwitchOnButton" value="1"><svg width="16" height="16" viewBox="0 0 16 16" aria-hidden="true" focusable="false"><path fill="currentColor" d="M7.25 1.5h1.5v6.5h-1.5zM4.2 3.6l1 1.1A4.5 4.5 0 1 0 10.8 4.7l1-1.1A6 6 0 1 1 4.2 3.6z"/></svg>{'Take the site offline'|i18n( 'design/admin/setup/maintenance' )}</button>
                <span class="exp-meta">{'Also empties the caches that answer ahead of the site.'|i18n( 'design/admin/setup/maintenance' )}</span>
            </div>
        </div>
    </section>
{/if}

    {* What visitors get: the real page, filled in, in a frame that runs none of its scripts *}
    {if $maintenance_preview}
    <section class="exp-card exp-preview" aria-labelledby="maintenance-preview-title">
        <div class="exp-card-head">
            <h2 class="exp-h2" id="maintenance-preview-title">{if $mi.on}{'What visitors see now'|i18n( 'design/admin/setup/maintenance' )}{else}{'Preview: what visitors will see'|i18n( 'design/admin/setup/maintenance' )}{/if}</h2>
        </div>
        <div class="exp-frame-wrap">
            <div class="exp-frame-bar"><span aria-hidden="true"></span><span aria-hidden="true"></span><span aria-hidden="true"></span><code>503 &middot; {$maintenance_preview.file|wash}</code></div>
            {* allow-same-origin only: the page loads its fonts, but none of its scripts run *}
            <iframe class="exp-frame" id="maintenance-preview-frame" sandbox="allow-same-origin" loading="lazy" referrerpolicy="no-referrer"
                    {if $mi.on|not}data-template="{$maintenance_preview.template|wash}" data-message-slot="{$maintenance_preview.message_slot|wash}" data-until-slot="{$maintenance_preview.until_slot|wash}" data-default-message="{$maintenance_preview.default_message|wash}" data-default-until="{$maintenance_preview.default_until|wash}"{/if}
                    title="{'Preview of the maintenance page'|i18n( 'design/admin/setup/maintenance' )|wash}"
                    srcdoc="{$maintenance_preview.html|wash}"></iframe>
        </div>
        <p class="exp-meta">{if $mi.on}{'Served with status 503 and never cached; images, styles and scripts of the site are still served.'|i18n( 'design/admin/setup/maintenance' )}{else}<span class="exp-js-only" hidden>{'Follows what you type.'|i18n( 'design/admin/setup/maintenance' )} </span>{'The page comes from %file.'|i18n( 'design/admin/setup/maintenance',, hash( '%file', $maintenance_preview.file|wash ) )}{/if}</p>
    </section>
    {/if}
</div>

{* The same from a shell, for a window opened before a deploy or when the administration cannot be reached *}
{if $maintenance_commands}
<details class="exp-fold" id="maintenance-shell">
    <summary>
        <h2 class="exp-h2">{'From a shell'|i18n( 'design/admin/setup/maintenance' )}</h2>
        <span class="exp-meta">{'The same switch, run in the installation directory'|i18n( 'design/admin/setup/maintenance' )}</span>
    </summary>
    <div class="exp-fold-body">
        <ul class="exp-cmds">
            {foreach $maintenance_commands as $mt_cmd}
            <li class="exp-cmd-row">
                <span class="exp-cmd-label" id="maintenance-cmd-{$mt_cmd.key|wash}">{$mt_cmd.label|wash}</span>
                <code class="exp-cmd" id="maintenance-cmd-{$mt_cmd.key|wash}-text" aria-labelledby="maintenance-cmd-{$mt_cmd.key|wash}">{$mt_cmd.command|wash}</code>
                <button type="button" class="exp-btn exp-btn-small exp-copy exp-js-only" data-copy-from="maintenance-cmd-{$mt_cmd.key|wash}-text" hidden
                        aria-label="{'Copy the command: %what'|i18n( 'design/admin/setup/maintenance',, hash( '%what', $mt_cmd.label ) )|wash}">{'Copy'|i18n( 'design/admin/setup/maintenance' )}</button>
            </li>
            {/foreach}
        </ul>
        <p class="exp-meta" style="margin-top: 10px;">{'Switch on takes --message="...", --until=30m, 2h or a date and time, and --allow-ip=1.2.3.4,5.6.7.8. Run as root, add --allow-root-user. ./console exp:maintenance on|off|status does the same.'|i18n( 'design/admin/setup/maintenance' )}</p>
    </div>
</details>
{/if}

{* The latest changes, from the audit *}
{if $maintenance_history}
<details class="exp-fold" id="maintenance-history" open="open">
    <summary>
        <h2 class="exp-h2" id="maintenance-history-title">{'Recent changes'|i18n( 'design/admin/setup/maintenance' )}</h2>
        <span class="exp-meta">{'From the audit, newest first'|i18n( 'design/admin/setup/maintenance' )}</span>
    </summary>
    <div class="exp-fold-body">
    {if and( $maintenance_history.available, $maintenance_history.rows|count|gt(0) )}
        <div class="exp-table-wrap" role="region" aria-labelledby="maintenance-history-title" tabindex="0">
        <table class="exp-table">
        <thead><tr>
            <th scope="col">{'When'|i18n( 'design/admin/setup/maintenance' )}</th>
            <th scope="col">{'Change'|i18n( 'design/admin/setup/maintenance' )}</th>
            <th scope="col">{'Reason'|i18n( 'design/admin/setup/maintenance' )}</th>
            <th scope="col">{'By'|i18n( 'design/admin/setup/maintenance' )}</th>
            <th scope="col">{'Expected back'|i18n( 'design/admin/setup/maintenance' )}</th>
        </tr></thead>
        <tbody>
        {foreach $maintenance_history.rows as $mt_row}
        <tr>
            <td class="exp-nowrap" data-label="{'When'|i18n( 'design/admin/setup/maintenance' )|wash}"><a href={concat( 'audit/event/', $mt_row.id )|ezurl}>{$mt_row.time|l10n( shortdatetime )}</a></td>
            <td data-label="{'Change'|i18n( 'design/admin/setup/maintenance' )|wash}">{if eq( $mt_row.mode, 'on' )}<span class="exp-badge is-warn">{'Switched on'|i18n( 'design/admin/setup/maintenance' )}</span>{elseif eq( $mt_row.mode, 'off' )}<span class="exp-badge is-ok">{'Switched off'|i18n( 'design/admin/setup/maintenance' )}</span>{else}<span class="exp-badge is-info">{'Changed'|i18n( 'design/admin/setup/maintenance' )}</span>{/if}</td>
            <td data-label="{'Reason'|i18n( 'design/admin/setup/maintenance' )|wash}">{if eq( $mt_row.reason, 'manual' )}{'Maintenance window'|i18n( 'design/admin/setup/maintenance' )}{elseif eq( $mt_row.reason, 'setup' )}{'Installation'|i18n( 'design/admin/setup/maintenance' )}{elseif eq( $mt_row.reason, '' )}&mdash;{else}{'Unknown'|i18n( 'design/admin/setup/maintenance' )}{/if}</td>
            <td data-label="{'By'|i18n( 'design/admin/setup/maintenance' )|wash}">{if $mt_row.login|ne( '' )}{$mt_row.login|wash}{else}&mdash;{/if}{if $mt_row.cli} <span class="exp-meta">({'shell'|i18n( 'design/admin/setup/maintenance' )})</span>{/if}</td>
            <td class="exp-nowrap" data-label="{'Expected back'|i18n( 'design/admin/setup/maintenance' )|wash}">{if $mt_row.until|gt(0)}{$mt_row.until|l10n( shortdatetime )}{else}&mdash;{/if}</td>
        </tr>
        {/foreach}
        </tbody>
        </table>
        </div>
    {elseif $maintenance_history.available}
        <p class="exp-empty">{'Maintenance has not been switched on or off since the audit began.'|i18n( 'design/admin/setup/maintenance' )}</p>
    {elseif eq( $maintenance_history.why, 'no-access' )}
        <p class="exp-empty">{'Reading the changes needs the audit/read right for the system channel.'|i18n( 'design/admin/setup/maintenance' )}</p>
    {elseif eq( $maintenance_history.why, 'no-index' )}
        <p class="exp-empty">{'The audit index is not available, so the changes cannot be listed here.'|i18n( 'design/admin/setup/maintenance' )}</p>
    {else}
        <p class="exp-empty">{'The audit is not available, so the changes cannot be listed here.'|i18n( 'design/admin/setup/maintenance' )}</p>
    {/if}
    {if $maintenance_history.console|ne( '' )}
        <p style="margin-top: 10px;"><a class="exp-link" href={$maintenance_history.console|ezurl}>{'Every change in the audit console'|i18n( 'design/admin/setup/maintenance' )}</a></p>
    {/if}
    </div>
</details>
{/if}

<details class="exp-fold" id="maintenance-how">
    <summary>
        <h2 class="exp-h2">{'How it works'|i18n( 'design/admin/setup/maintenance' )}</h2>
        <span class="exp-meta">{'What is served, what is not, and where the state is kept'|i18n( 'design/admin/setup/maintenance' )}</span>
    </summary>
    <div class="exp-fold-body">
        <ul class="exp-plain">
            <li>{'In maintenance mode every page request is answered with the maintenance page (503, never cached) before the settings or the database are used; images, styles and scripts are still served. Use it while you change the site, or it is switched on by the kickstarter while it installs.'|i18n( 'design/admin/setup/maintenance' )}</li>
            <li>{'The administration stays reachable while the site is offline, so it can be switched off again here.'|i18n( 'design/admin/setup/maintenance' )} {'An administration reached by its own host name rather than /admin is not let through; switch off from a shell then.'|i18n( 'design/admin/setup/maintenance' )}</li>
            <li>{'The state is the file %file: it exists while maintenance is on. Switching on and off is recorded in the audit.'|i18n( 'design/admin/setup/maintenance',, hash( '%file', 'var/maintenance.json' ) )}</li>
            <li>{'The page is errors/maintenance.html of the first active extension that has one, else share/maintenance.html.'|i18n( 'design/admin/setup/maintenance' )}</li>
        </ul>
    </div>
</details>

</form>

<p class="exp-sr" id="maintenance-copy-status" role="status" aria-live="polite"></p>

</div></div></div>
</div>
<script type="text/javascript">
var expMaintenanceText = {ldelim}
    back: '{'Expected back around %local (%utc UTC).'|i18n( 'design/admin/setup/maintenance' )|wash( javascript )}',
    tooLong: '{'At most %max minutes (a week).'|i18n( 'design/admin/setup/maintenance',, hash( '%max', $maintenance_max_minutes ) )|wash( javascript )}',
    chars: '{'%count of 500 characters'|i18n( 'design/admin/setup/maintenance' )|wash( javascript )}',
    notAddresses: '{'Not addresses, these would be left out: %list'|i18n( 'design/admin/setup/maintenance' )|wash( javascript )}',
    copied: '{'Copied'|i18n( 'design/admin/setup/maintenance' )|wash( javascript )}',
    copiedLong: '{'The command is on the clipboard.'|i18n( 'design/admin/setup/maintenance' )|wash( javascript )}',
    copyFailed: '{'Could not copy. Select the text and copy it by hand.'|i18n( 'design/admin/setup/maintenance' )|wash( javascript )}'
{rdelim};
{literal}
(function () {
    var root = document.querySelector( '.exp-maintenance' );
    if ( !root ) return;
    function each( selector, fn ) { var n = root.querySelectorAll( selector ), i; for ( i = 0; i < n.length; i++ ) fn( n[i] ); }
    function tr( name, values ) { var s = expMaintenanceText[name], k; for ( k in values || {} ) s = s.split( '%' + k ).join( values[k] ); return s; }
    function text( el, value ) { if ( el ) el.textContent = value; }
    function pad( n ) { return ( n < 10 ? '0' : '' ) + n; }
    function escapeHtml( s ) { return String( s ).replace( /&/g, '&amp;' ).replace( /</g, '&lt;' ).replace( />/g, '&gt;' ).replace( /"/g, '&quot;' ).replace( /'/g, '&#039;' ); }

    each( '.exp-js-only', function ( el ) { el.hidden = false; } );

    var message = document.getElementById( 'MaintenanceMessage' );
    var minutes = document.getElementById( 'MaintenanceMinutes' );
    var minutesResult = document.getElementById( 'maintenance-minutes-result' );
    var count = document.getElementById( 'maintenance-message-count' );
    var ips = document.getElementById( 'MaintenanceAllowIPs' );
    var ipCheck = document.getElementById( 'maintenance-ip-check' );
    var frame = document.getElementById( 'maintenance-preview-frame' );
    var preview = frame && frame.hasAttribute( 'data-template' ) ? {
        template: frame.getAttribute( 'data-template' ), messageSlot: frame.getAttribute( 'data-message-slot' ),
        untilSlot: frame.getAttribute( 'data-until-slot' ), defaultMessage: frame.getAttribute( 'data-default-message' ),
        defaultUntil: frame.getAttribute( 'data-default-until' ) } : null;

    function minutesValue() {
        var m = minutes ? parseInt( minutes.value, 10 ) : 0;
        return isNaN( m ) || m < 0 ? 0 : m;
    }

    // The preview: the real page with what is typed in the message and the expected end
    var timer = null;
    function renderPreview() {
        if ( !frame || !preview ) return;
        var msg = message ? message.value.trim() : '';
        var m = Math.min( minutesValue(), 10080 );
        var until = preview.defaultUntil;
        if ( m > 0 ) {
            var d = new Date( Date.now() + m * 60000 );
            until = 'Expected back at ' + pad( d.getUTCHours() ) + ':' + pad( d.getUTCMinutes() ) + ' UTC.';
        }
        msg = msg !== '' ? msg : preview.defaultMessage;
        // In place when the frame's page can be reached (it is same-origin and runs no script): loading it again
        // for every key would abort its font downloads and flash. The page shows the default texts at first.
        var doc = null;
        try { doc = frame.contentDocument; } catch ( e ) {}
        if ( doc && doc.body && doc.readyState === 'complete' && doc.URL === 'about:srcdoc' ) {
            replaceText( doc.body, shown.msg, msg );
            replaceText( doc.body, shown.until, until );
        } else {
            frame.setAttribute( 'srcdoc', preview.template.split( preview.messageSlot ).join( escapeHtml( msg ) )
                                                          .split( preview.untilSlot ).join( escapeHtml( until ) ) );
        }
        shown = { msg: msg, until: until };
    }
    var shown = { msg: preview ? preview.defaultMessage : '', until: preview ? preview.defaultUntil : '' };
    function replaceText( node, from, to ) {
        if ( from === to || from === '' ) return;
        var walker = node.ownerDocument.createTreeWalker( node, 4 /* NodeFilter.SHOW_TEXT */ );
        while ( walker.nextNode() ) {
            var t = walker.currentNode;
            if ( t.nodeValue.indexOf( from ) !== -1 ) t.nodeValue = t.nodeValue.split( from ).join( to );
        }
    }
    function schedulePreview() { window.clearTimeout( timer ); timer = window.setTimeout( renderPreview, 120 ); }

    function updateMinutes() {
        var m = minutesValue();
        each( '.exp-preset', function ( b ) { b.setAttribute( 'aria-pressed', String( parseInt( b.getAttribute( 'data-minutes' ), 10 ) === m ) ); } );
        if ( !minutesResult ) return;
        if ( m > 10080 ) { text( minutesResult, tr( 'tooLong' ) ); return; }
        if ( m === 0 ) { text( minutesResult, '' ); return; }
        var d = new Date( Date.now() + m * 60000 );
        text( minutesResult, tr( 'back', { local: d.toLocaleString( document.documentElement.lang || undefined, { weekday: 'short', hour: '2-digit', minute: '2-digit' } ),
                                           utc: pad( d.getUTCHours() ) + ':' + pad( d.getUTCMinutes() ) } ) );
    }
    if ( minutes ) minutes.addEventListener( 'input', function () { updateMinutes(); schedulePreview(); } );
    each( '.exp-preset', function ( b ) {
        b.addEventListener( 'click', function () {
            if ( !minutes ) return;
            minutes.value = b.getAttribute( 'data-minutes' );
            updateMinutes(); schedulePreview();
        } );
    } );

    function updateCount() { if ( message && count ) text( count, tr( 'chars', { count: message.value.length } ) ); }
    if ( message ) message.addEventListener( 'input', function () { updateCount(); schedulePreview(); } );

    // The same check as the server's: what is not an address is left out, so it is said before sending
    var v4 = /^(25[0-5]|2[0-4]\d|1\d\d|[1-9]?\d)(\.(25[0-5]|2[0-4]\d|1\d\d|[1-9]?\d)){3}$/;
    function isAddress( s ) {
        if ( v4.test( s ) ) return true;
        if ( s.indexOf( ':' ) === -1 || !/^[0-9a-fA-F:.]+$/.test( s ) ) return false;
        var tail = s.match( /:(\d+\.\d+\.\d+\.\d+)$/ );
        if ( tail && !v4.test( tail[1] ) ) return false;
        var parts = s.split( '::' );
        if ( parts.length > 2 ) return false;
        var groups = s.replace( /:\d+\.\d+\.\d+\.\d+$/, ':0:0' ).split( ':' ).filter( function ( g ) { return g !== ''; } );
        for ( var i = 0; i < groups.length; i++ ) if ( !/^[0-9a-fA-F]{1,4}$/.test( groups[i] ) ) return false;
        return parts.length === 2 ? groups.length < 8 : groups.length === 8;
    }
    function checkAddresses() {
        if ( !ips || !ipCheck ) return;
        var bad = ips.value.split( /[\s,;]+/ ).filter( function ( e ) { return e !== '' && !isAddress( e ); } );
        ipCheck.hidden = bad.length === 0;
        ips.setAttribute( 'aria-invalid', bad.length ? 'true' : 'false' );
        text( ipCheck, bad.length ? tr( 'notAddresses', { list: bad.join( ', ' ) } ) : '' );
    }
    if ( ips ) ips.addEventListener( 'input', checkAddresses );

    updateMinutes(); updateCount(); checkAddresses();

    // Copy buttons
    var copyStatus = document.getElementById( 'maintenance-copy-status' );
    function copyText( value, button ) {
        function done( ok ) {
            text( copyStatus, ok ? tr( 'copiedLong' ) : tr( 'copyFailed' ) );
            if ( !ok ) return;
            var label = button.textContent;
            text( button, tr( 'copied' ) );
            window.setTimeout( function () { text( button, label ); }, 1600 );
        }
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
        if ( navigator.clipboard && window.isSecureContext ) {
            navigator.clipboard.writeText( value ).then( function () { done( true ); }, function () { done( fallback() ); } );
            return;
        }
        done( fallback() );
    }
    each( '.exp-copy', function ( button ) {
        button.addEventListener( 'click', function () {
            var from = document.getElementById( button.getAttribute( 'data-copy-from' ) );
            copyText( from ? from.textContent : '', button );
        } );
    } );
})();
{/literal}
</script>
{undef $mi}
