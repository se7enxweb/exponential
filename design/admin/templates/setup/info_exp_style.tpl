{* The look of Setup > System information (setup/info.tpl), in the visual language of the redesigned admin pages.

   The same file is in design/admin and design/admin4, so every administration design resolves it. Everything is
   scoped to .exp-sysinfo and takes admin4's tokens where they exist (--a4-*), with values of its own for the older
   designs. admin4's dark mode keeps the content card white, so the colours here hold in both modes. Long values
   (paths, versions, lists) wrap; only the wide tables scroll inside their own frame. *}
{literal}
<style>
.exp-sysinfo {
    --si-ink: var(--a4-ink, #1f2430);
    --si-muted: var(--a4-muted, #5d6573);
    --si-line: var(--a4-line, #e3e6eb);
    --si-soft: var(--a4-soft, #f6f7f9);
    --si-card: #fff;
    --si-accent: #c2410c;
    --si-accent-hover: #9a3412;
    --si-ring: rgba(194, 65, 12, 0.45);
    --si-ok: #166534;   --si-ok-bg: #e7f5ea;
    --si-warn: #8a4b00; --si-warn-bg: #fff3df;
    --si-bad: #b91c1c;  --si-bad-bg: #fdecec;
    --si-info: #1e4fa8; --si-info-bg: #e8effd;
    --si-radius: 12px;
    --si-mono: ui-monospace, SFMono-Regular, Menlo, Consolas, monospace;
    color: var(--si-ink);
    font-size: 14px;
    line-height: 1.5;
}
.exp-sysinfo *, .exp-sysinfo *::before, .exp-sysinfo *::after { box-sizing: border-box; }
.exp-sysinfo [hidden] { display: none !important; }
.exp-sysinfo .box-content { padding-bottom: 20px; min-width: 0; }
.exp-sysinfo h1.context-title { margin: 0; overflow-wrap: anywhere; }
.exp-sysinfo h2.exp-h2 { margin: 0; padding: 0; border: 0; background: none; font-size: 17px; font-weight: 650; color: var(--si-ink); }
.exp-sysinfo h3 { margin: 0; padding: 0; border: 0; background: none; font-size: 15px; font-weight: 650; color: var(--si-ink); overflow-wrap: anywhere; }
.exp-sysinfo p { margin: 0; }
.exp-sysinfo code { font-family: var(--si-mono); font-size: 12.5px; overflow-wrap: anywhere; word-break: break-word; color: var(--si-ink); background: none; }
.exp-sysinfo a { color: var(--si-accent-hover); overflow-wrap: anywhere; }
.exp-sysinfo a:hover { color: var(--si-ink); }
.exp-sysinfo :focus-visible { outline: 3px solid var(--si-ring); outline-offset: 2px; }
.exp-sysinfo .exp-sr { position: absolute; width: 1px; height: 1px; margin: -1px; padding: 0; overflow: hidden; clip: rect(0 0 0 0); white-space: nowrap; border: 0; }
.exp-sysinfo .exp-muted { color: var(--si-muted); }
.exp-sysinfo .exp-intro { margin: 8px 0 16px; max-width: 80ch; color: var(--si-muted); }
.exp-sysinfo .exp-section { margin: 0 0 24px; min-width: 0; }
.exp-sysinfo .exp-section-head { display: flex; flex-wrap: wrap; align-items: baseline; gap: 4px 12px; margin: 0 0 10px; }
.exp-sysinfo .exp-section-head p { flex: 1 1 100%; color: var(--si-muted); max-width: 80ch; }

/* Who answered */
.exp-sysinfo .exp-served { display: flex; flex-wrap: wrap; align-items: center; gap: 10px 16px; margin: 0 0 14px; padding: 14px 16px;
    border: 1px solid var(--si-line); border-left: 5px solid var(--si-info); border-radius: var(--si-radius); background: var(--si-info-bg); }
.exp-sysinfo .exp-served.is-velocity { border-left-color: var(--si-accent); background: #fff4ec; }
.exp-sysinfo .exp-served-main { flex: 1 1 320px; min-width: 0; }
.exp-sysinfo .exp-served-main strong { font-size: 16px; }
.exp-sysinfo .exp-served-main p { color: var(--si-ink); overflow-wrap: anywhere; }
.exp-sysinfo .exp-served-main p + p { margin-top: 4px; font-size: 13px; color: var(--si-muted); }

/* Buttons */
.exp-sysinfo .exp-actionbar { display: flex; flex-wrap: wrap; align-items: center; gap: 8px; margin: 0 0 20px; padding: 10px 12px;
    border: 1px solid var(--si-line); border-radius: var(--si-radius); background: var(--si-soft); }
.exp-sysinfo .exp-actionbar .exp-meta { flex: 1 1 100%; font-size: 13px; color: var(--si-muted); }
.exp-sysinfo .exp-btn {
    display: inline-flex; align-items: center; justify-content: center; gap: 6px; min-height: 36px; max-width: 100%; margin: 0;
    padding: 6px 14px; border: 1px solid #c9ced6; border-radius: 9px; background: #fff; color: var(--si-ink);
    font: 600 13.5px/1.2 inherit; font-family: inherit; cursor: pointer; text-decoration: none; white-space: normal; text-align: center;
}
.exp-sysinfo a.exp-btn { color: var(--si-ink); }
.exp-sysinfo .exp-btn:hover { border-color: var(--si-accent); color: var(--si-accent-hover); }
.exp-sysinfo .exp-btn-primary, .exp-sysinfo a.exp-btn-primary { border-color: var(--si-accent); background: var(--si-accent); color: #fff; }
.exp-sysinfo .exp-btn-primary:hover, .exp-sysinfo a.exp-btn-primary:hover { border-color: var(--si-accent-hover); background: var(--si-accent-hover); color: #fff; }
.exp-sysinfo .exp-btn-small { min-height: 30px; padding: 4px 10px; font-size: 12.5px; }
.exp-sysinfo .exp-actions { display: flex; flex-wrap: wrap; align-items: center; gap: 8px; margin-top: 10px; }
.exp-sysinfo .exp-status-text { font-size: 13px; color: var(--si-ok); }

/* Messages */
.exp-sysinfo .exp-feedback { margin: 0 0 12px; padding: 10px 14px; border: 1px solid; border-left-width: 4px; border-radius: 10px; }
.exp-sysinfo .exp-feedback.is-ok { border-color: #b7dfc1; border-left-color: var(--si-ok); background: var(--si-ok-bg); color: var(--si-ok); }
.exp-sysinfo .exp-feedback.is-warn { border-color: #f3d19c; border-left-color: var(--si-warn); background: var(--si-warn-bg); color: var(--si-warn); }
.exp-sysinfo .exp-feedback.is-info { border-color: #bcd0f5; border-left-color: var(--si-info); background: var(--si-info-bg); color: var(--si-info); }

/* Figures */
.exp-sysinfo .exp-figures { display: grid; grid-template-columns: repeat(auto-fit, minmax(min(100%, 130px), 1fr)); gap: 10px; margin: 0 0 14px; padding: 0; list-style: none; }
.exp-sysinfo .exp-figure { display: flex; flex-direction: column; gap: 2px; margin: 0; padding: 12px 14px; border: 1px solid var(--si-line); border-radius: var(--si-radius); background: var(--si-card); }
.exp-sysinfo .exp-figure strong { font-size: 24px; line-height: 1.15; font-weight: 700; font-variant-numeric: tabular-nums; }
.exp-sysinfo .exp-figure span { font-size: 12.5px; color: var(--si-muted); }
.exp-sysinfo .exp-figure.is-bad { box-shadow: inset 4px 0 0 var(--si-bad); } .exp-sysinfo .exp-figure.is-bad strong { color: var(--si-bad); }
.exp-sysinfo .exp-figure.is-warn { box-shadow: inset 4px 0 0 var(--si-warn); } .exp-sysinfo .exp-figure.is-warn strong { color: var(--si-warn); }
.exp-sysinfo .exp-figure.is-info strong { color: var(--si-info); }
.exp-sysinfo .exp-figure.is-ok strong { color: var(--si-ok); }

/* Badges */
.exp-sysinfo .exp-badge { display: inline-flex; align-items: center; gap: 5px; margin: 0; padding: 2px 9px; border-radius: 999px; font-size: 12px; font-weight: 650; line-height: 1.5; white-space: nowrap; background: var(--si-soft); color: var(--si-muted); }
.exp-sysinfo .exp-badge.is-ok { background: var(--si-ok-bg); color: var(--si-ok); }
.exp-sysinfo .exp-badge.is-warn { background: var(--si-warn-bg); color: var(--si-warn); }
.exp-sysinfo .exp-badge.is-bad { background: var(--si-bad-bg); color: var(--si-bad); }
.exp-sysinfo .exp-badge.is-info { background: var(--si-info-bg); color: var(--si-info); }

/* Health checks */
.exp-sysinfo .exp-checks { display: grid; grid-template-columns: minmax(0, 1fr); gap: 8px; margin: 0; padding: 0; list-style: none; }
.exp-sysinfo .exp-check { display: grid; grid-template-columns: 92px minmax(0, 1fr); gap: 4px 12px; align-items: start; margin: 0; padding: 10px 14px;
    border: 1px solid var(--si-line); border-radius: 10px; background: var(--si-card); }
.exp-sysinfo .exp-check.is-bad { box-shadow: inset 4px 0 0 var(--si-bad); }
.exp-sysinfo .exp-check.is-warn { box-shadow: inset 4px 0 0 var(--si-warn); }
.exp-sysinfo .exp-check .exp-badge { justify-self: start; }
.exp-sysinfo .exp-check-text { min-width: 0; overflow-wrap: anywhere; }
.exp-sysinfo .exp-check-text strong { font-weight: 650; }
.exp-sysinfo .exp-check-text p { margin-top: 2px; font-size: 13px; color: var(--si-muted); }
.exp-sysinfo .exp-check-text p.exp-fix { color: var(--si-ink); }
.exp-sysinfo .exp-check-text p.exp-fix::before { content: "\2192\00a0"; color: var(--si-accent); font-weight: 700; }
.exp-sysinfo details.exp-ok-checks { margin-top: 8px; }
.exp-sysinfo details.exp-ok-checks > summary { cursor: pointer; font-size: 13px; font-weight: 650; color: var(--si-muted); padding: 4px 0; }

/* Overview cards */
.exp-sysinfo .exp-cards { display: grid; grid-template-columns: repeat(auto-fill, minmax(min(100%, 300px), 1fr)); gap: 12px; margin: 0; padding: 0; list-style: none; }
.exp-sysinfo .exp-card { min-width: 0; margin: 0; padding: 14px 16px; border: 1px solid var(--si-line); border-radius: var(--si-radius); background: var(--si-card);
    box-shadow: 0 1px 2px rgba(16, 24, 40, 0.04); }
.exp-sysinfo .exp-card.is-warn { box-shadow: inset 4px 0 0 var(--si-warn), 0 1px 2px rgba(16, 24, 40, 0.04); }
.exp-sysinfo .exp-card.is-bad { box-shadow: inset 4px 0 0 var(--si-bad), 0 1px 2px rgba(16, 24, 40, 0.04); }
.exp-sysinfo .exp-card-head { display: flex; flex-wrap: wrap; align-items: center; justify-content: space-between; gap: 6px 10px; margin: 0 0 8px; }
.exp-sysinfo .exp-rows { display: grid; grid-template-columns: fit-content(14em) minmax(0, 1fr); gap: 5px 12px; margin: 0; }
.exp-sysinfo .exp-rows dt { margin: 0; min-width: 0; font-size: 12.5px; font-weight: 650; color: var(--si-muted); overflow-wrap: break-word; }
.exp-sysinfo .exp-rows dd { margin: 0; min-width: 0; overflow-wrap: anywhere; word-break: break-word; }

/* Panels (the detailed parts, folded) */
.exp-sysinfo details.exp-panel { margin: 0 0 12px; border: 1px solid var(--si-line); border-radius: var(--si-radius); background: var(--si-card); min-width: 0; }
.exp-sysinfo details.exp-panel > summary { display: flex; flex-wrap: wrap; align-items: center; gap: 6px 10px; padding: 12px 16px; cursor: pointer; list-style: none; border-radius: var(--si-radius); }
.exp-sysinfo details.exp-panel > summary::-webkit-details-marker { display: none; }
.exp-sysinfo details.exp-panel > summary::before { content: ""; width: 8px; height: 8px; border-right: 2px solid var(--si-muted); border-bottom: 2px solid var(--si-muted); transform: rotate(-45deg); transition: transform .15s; flex: 0 0 auto; }
.exp-sysinfo details.exp-panel[open] > summary::before { transform: rotate(45deg); }
.exp-sysinfo details.exp-panel > summary h2.exp-h2 { font-size: 15px; }
.exp-sysinfo details.exp-panel > summary .exp-muted { font-size: 13px; }
.exp-sysinfo .exp-panel-body { padding: 0 16px 16px; min-width: 0; }
.exp-sysinfo .exp-panel-body > * + * { margin-top: 12px; }
.exp-sysinfo .exp-subhead { display: flex; flex-wrap: wrap; align-items: center; gap: 6px 10px; }
.exp-sysinfo .exp-subhead small { color: var(--si-muted); }
.exp-sysinfo .exp-note { font-size: 13px; color: var(--si-muted); max-width: 90ch; }
.exp-sysinfo .exp-inline { margin: 0; padding: 0; list-style: none; font-size: 13px; }
.exp-sysinfo .exp-inline li { display: inline; }
.exp-sysinfo .exp-inline li + li::before { content: " \00b7 "; color: var(--si-muted); }
.exp-sysinfo .exp-inline code { color: var(--si-muted); }
.exp-sysinfo .exp-two { display: grid; grid-template-columns: repeat(auto-fit, minmax(min(100%, 320px), 1fr)); gap: 16px 24px; }
.exp-sysinfo .exp-box { min-width: 0; padding: 12px 14px; border: 1px solid var(--si-line); border-radius: 10px; }
.exp-sysinfo .exp-box > * + * { margin-top: 8px; }
.exp-sysinfo .exp-table td:first-child, .exp-sysinfo .exp-table-views td:nth-child(2) { overflow-wrap: normal; white-space: nowrap; }

/* Bars */
.exp-sysinfo .exp-bars { display: grid; grid-template-columns: minmax(0, 8em) minmax(60px, 1fr) minmax(0, auto); gap: 6px 10px; align-items: center; margin: 0; font-size: 13px; }
.exp-sysinfo .exp-bars dt { margin: 0; color: var(--si-muted); font-weight: 650; font-size: 12.5px; }
.exp-sysinfo .exp-bars dd { margin: 0; min-width: 0; overflow-wrap: anywhere; }
.exp-sysinfo .exp-bar { height: 9px; border-radius: 5px; background: #e6e8ec; overflow: hidden; }
.exp-sysinfo .exp-bar > span { display: block; height: 100%; border-radius: 5px; background: var(--si-ok); }
.exp-sysinfo .exp-bar.is-warn > span { background: #c27a00; }
.exp-sysinfo .exp-bar.is-bad > span { background: var(--si-bad); }
.exp-sysinfo .exp-bar.is-neutral > span { background: #64748b; }
@media (max-width: 560px) {
    .exp-sysinfo .exp-bars { grid-template-columns: minmax(0, 1fr) minmax(60px, 1fr); }
    .exp-sysinfo .exp-bars dd.exp-bar-text { grid-column: 1 / -1; margin-bottom: 4px; }
}

/* Chips and tables */
.exp-sysinfo .exp-chips { display: flex; flex-wrap: wrap; gap: 6px; margin: 0; padding: 0; list-style: none; }
.exp-sysinfo .exp-chip { display: inline-block; max-width: 100%; padding: 1px 9px; border-radius: 6px; background: var(--si-soft); color: var(--si-ink); font: 12.5px/1.7 var(--si-mono); overflow-wrap: anywhere; }
.exp-sysinfo .exp-table-wrap { overflow-x: auto; border: 1px solid var(--si-line); border-radius: 10px; background: var(--si-card); max-width: 100%; }
.exp-sysinfo .exp-table { width: 100%; margin: 0; border-collapse: collapse; }
.exp-sysinfo .exp-table th, .exp-sysinfo .exp-table td { padding: 8px 12px; border: 0; border-bottom: 1px solid var(--si-line); text-align: left; vertical-align: top; background: transparent; color: var(--si-ink); font-size: 13px; }
.exp-sysinfo .exp-table th { font-size: 12px; font-weight: 650; text-transform: uppercase; letter-spacing: .04em; color: var(--si-muted); background: var(--si-soft); white-space: nowrap; }
.exp-sysinfo .exp-table tr:last-child td { border-bottom: 0; }
.exp-sysinfo .exp-table td { overflow-wrap: anywhere; }
.exp-sysinfo .exp-table .exp-num { text-align: right; font-variant-numeric: tabular-nums; white-space: nowrap; }
.exp-sysinfo .exp-ext-list { display: grid; grid-template-columns: repeat(auto-fill, minmax(min(100%, 260px), 1fr)); gap: 4px 18px; margin: 0; padding: 0; list-style: none; }
.exp-sysinfo .exp-ext-list li { display: flex; justify-content: space-between; gap: 10px; padding: 4px 0; border-bottom: 1px solid var(--si-line); min-width: 0; font-size: 13px; }
.exp-sysinfo .exp-ext-list li span:first-child { min-width: 0; overflow-wrap: anywhere; font-family: var(--si-mono); font-size: 12.5px; }
.exp-sysinfo .exp-ext-list li span:last-child { color: var(--si-muted); font-variant-numeric: tabular-nums; white-space: nowrap; }

/* The report */
.exp-sysinfo textarea.exp-report { display: block; width: 100%; min-height: 320px; margin: 0; padding: 10px 12px; border: 1px solid #8f96a3; border-radius: 9px;
    background: var(--si-soft); color: var(--si-ink); font: 12px/1.5 var(--si-mono); white-space: pre; overflow: auto; resize: vertical; }

.exp-sysinfo .exp-sysinfo-form { display: flex; flex-wrap: wrap; gap: 8px; margin: 0; }
.exp-sysinfo .exp-sysinfo-form .button { margin: 0; }

@media (max-width: 600px) {
    .exp-sysinfo .exp-check { grid-template-columns: minmax(0, 1fr); }
    .exp-sysinfo .exp-rows { grid-template-columns: minmax(0, 1fr); gap: 0 12px; }
    .exp-sysinfo .exp-rows dd { margin-bottom: 6px; }
    .exp-sysinfo .exp-actionbar .exp-btn { flex: 1 1 auto; }
}
</style>
{/literal}
