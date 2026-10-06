{* The look of Setup > Caches (setup/cache.tpl), in the visual language of the redesigned admin pages and of
   Setup > System information (setup/info_exp_style.tpl, whose rules it starts from).

   The same file is in design/admin and design/admin4. Everything is scoped to .exp-cachepage and takes admin4's
   tokens where they exist (--a4-*); admin4's dark mode keeps the content card white, so the colours hold in both
   modes. Long values wrap; only the cache tables scroll inside their own frame, and below 760 pixels each row is
   a card. *}
{literal}
<style>
.exp-cachepage {
    --cp-ink: var(--a4-ink, #1f2430);
    --cp-muted: var(--a4-muted, #5d6573);
    --cp-line: var(--a4-line, #e3e6eb);
    --cp-soft: var(--a4-soft, #f6f7f9);
    --cp-card: #fff;
    --cp-accent: #c2410c;
    --cp-accent-hover: #9a3412;
    --cp-ring: rgba(194, 65, 12, 0.45);
    --cp-ok: #166534;   --cp-ok-bg: #e7f5ea;
    --cp-warn: #8a4b00; --cp-warn-bg: #fff3df;
    --cp-bad: #b91c1c;  --cp-bad-bg: #fdecec;
    --cp-info: #1e4fa8; --cp-info-bg: #e8effd;
    --cp-radius: 12px;
    --cp-mono: ui-monospace, SFMono-Regular, Menlo, Consolas, monospace;
    color: var(--cp-ink);
    font-size: 14px;
    line-height: 1.5;
}
.exp-cachepage *, .exp-cachepage *::before, .exp-cachepage *::after { box-sizing: border-box; }
.exp-cachepage [hidden] { display: none !important; }
.exp-cachepage .box-content { padding-bottom: 20px; min-width: 0; }
.exp-cachepage h1.context-title { margin: 0; overflow-wrap: anywhere; }
.exp-cachepage h2.exp-h2 { margin: 0; padding: 0; border: 0; background: none; font-size: 17px; font-weight: 650; color: var(--cp-ink); }
.exp-cachepage h3 { margin: 0; padding: 0; border: 0; background: none; font-size: 15px; font-weight: 650; color: var(--cp-ink); overflow-wrap: anywhere; }
.exp-cachepage p { margin: 0; }
.exp-cachepage code { font-family: var(--cp-mono); font-size: 12.5px; overflow-wrap: anywhere; word-break: break-word; color: var(--cp-ink); background: none; }
.exp-cachepage a { color: var(--cp-accent-hover); overflow-wrap: anywhere; }
.exp-cachepage a:hover { color: var(--cp-ink); }
.exp-cachepage :focus-visible { outline: 3px solid var(--cp-ring); outline-offset: 2px; }
.exp-cachepage .exp-sr { position: absolute; width: 1px; height: 1px; margin: -1px; padding: 0; overflow: hidden; clip: rect(0 0 0 0); white-space: nowrap; border: 0; }
.exp-cachepage .exp-muted { color: var(--cp-muted); }
.exp-cachepage .exp-intro { margin: 8px 0 16px; max-width: 80ch; color: var(--cp-muted); }
.exp-cachepage .exp-section { margin: 0 0 24px; min-width: 0; }
.exp-cachepage .exp-section-head { display: flex; flex-wrap: wrap; align-items: baseline; gap: 4px 12px; margin: 0 0 10px; }
.exp-cachepage .exp-section-head p { flex: 1 1 100%; color: var(--cp-muted); max-width: 80ch; }

/* Who answered */
.exp-cachepage .exp-served { display: flex; flex-wrap: wrap; align-items: center; gap: 10px 16px; margin: 0 0 14px; padding: 14px 16px;
    border: 1px solid var(--cp-line); border-left: 5px solid var(--cp-info); border-radius: var(--cp-radius); background: var(--cp-info-bg); }
.exp-cachepage .exp-served.is-velocity { border-left-color: var(--cp-accent); background: #fff4ec; }
.exp-cachepage .exp-served-main { flex: 1 1 320px; min-width: 0; }
.exp-cachepage .exp-served-main strong { font-size: 16px; }
.exp-cachepage .exp-served-main p { color: var(--cp-ink); overflow-wrap: anywhere; }
.exp-cachepage .exp-served-main p + p { margin-top: 4px; font-size: 13px; color: var(--cp-muted); }

/* Buttons */
.exp-cachepage .exp-actionbar { display: flex; flex-wrap: wrap; align-items: center; gap: 8px; margin: 0 0 20px; padding: 10px 12px;
    border: 1px solid var(--cp-line); border-radius: var(--cp-radius); background: var(--cp-soft); }
.exp-cachepage .exp-actionbar .exp-meta { flex: 1 1 100%; font-size: 13px; color: var(--cp-muted); }
.exp-cachepage .exp-btn {
    display: inline-flex; align-items: center; justify-content: center; gap: 6px; min-height: 36px; max-width: 100%; margin: 0;
    padding: 6px 14px; border: 1px solid #c9ced6; border-radius: 9px; background: #fff; color: var(--cp-ink);
    font: 600 13.5px/1.2 inherit; font-family: inherit; cursor: pointer; text-decoration: none; white-space: normal; text-align: center;
}
.exp-cachepage a.exp-btn { color: var(--cp-ink); }
.exp-cachepage .exp-btn:hover { border-color: var(--cp-accent); color: var(--cp-accent-hover); }
.exp-cachepage .exp-btn-primary, .exp-cachepage a.exp-btn-primary { border-color: var(--cp-accent); background: var(--cp-accent); color: #fff; }
.exp-cachepage .exp-btn-primary:hover, .exp-cachepage a.exp-btn-primary:hover { border-color: var(--cp-accent-hover); background: var(--cp-accent-hover); color: #fff; }
.exp-cachepage .exp-btn-small { min-height: 30px; padding: 4px 10px; font-size: 12.5px; }
.exp-cachepage .exp-actions { display: flex; flex-wrap: wrap; align-items: center; gap: 8px; margin-top: 10px; }
.exp-cachepage .exp-status-text { font-size: 13px; color: var(--cp-ok); }

/* Messages */
.exp-cachepage .exp-feedback { margin: 0 0 12px; padding: 10px 14px; border: 1px solid; border-left-width: 4px; border-radius: 10px; }
.exp-cachepage .exp-feedback.is-ok { border-color: #b7dfc1; border-left-color: var(--cp-ok); background: var(--cp-ok-bg); color: var(--cp-ok); }
.exp-cachepage .exp-feedback.is-warn { border-color: #f3d19c; border-left-color: var(--cp-warn); background: var(--cp-warn-bg); color: var(--cp-warn); }
.exp-cachepage .exp-feedback.is-info { border-color: #bcd0f5; border-left-color: var(--cp-info); background: var(--cp-info-bg); color: var(--cp-info); }

/* Figures */
.exp-cachepage .exp-figures { display: grid; grid-template-columns: repeat(auto-fit, minmax(min(100%, 130px), 1fr)); gap: 10px; margin: 0 0 14px; padding: 0; list-style: none; }
.exp-cachepage .exp-figure { display: flex; flex-direction: column; gap: 2px; margin: 0; padding: 12px 14px; border: 1px solid var(--cp-line); border-radius: var(--cp-radius); background: var(--cp-card); }
.exp-cachepage .exp-figure strong { font-size: 24px; line-height: 1.15; font-weight: 700; font-variant-numeric: tabular-nums; }
.exp-cachepage .exp-figure span { font-size: 12.5px; color: var(--cp-muted); }
.exp-cachepage .exp-figure.is-bad { box-shadow: inset 4px 0 0 var(--cp-bad); } .exp-cachepage .exp-figure.is-bad strong { color: var(--cp-bad); }
.exp-cachepage .exp-figure.is-warn { box-shadow: inset 4px 0 0 var(--cp-warn); } .exp-cachepage .exp-figure.is-warn strong { color: var(--cp-warn); }
.exp-cachepage .exp-figure.is-info strong { color: var(--cp-info); }
.exp-cachepage .exp-figure.is-ok strong { color: var(--cp-ok); }

/* Badges */
.exp-cachepage .exp-badge { display: inline-flex; align-items: center; gap: 5px; margin: 0; padding: 2px 9px; border-radius: 999px; font-size: 12px; font-weight: 650; line-height: 1.5; white-space: nowrap; background: var(--cp-soft); color: var(--cp-muted); }
.exp-cachepage .exp-badge.is-ok { background: var(--cp-ok-bg); color: var(--cp-ok); }
.exp-cachepage .exp-badge.is-warn { background: var(--cp-warn-bg); color: var(--cp-warn); }
.exp-cachepage .exp-badge.is-bad { background: var(--cp-bad-bg); color: var(--cp-bad); }
.exp-cachepage .exp-badge.is-info { background: var(--cp-info-bg); color: var(--cp-info); }

/* Health checks */
.exp-cachepage .exp-checks { display: grid; grid-template-columns: minmax(0, 1fr); gap: 8px; margin: 0; padding: 0; list-style: none; }
.exp-cachepage .exp-check { display: grid; grid-template-columns: 92px minmax(0, 1fr); gap: 4px 12px; align-items: start; margin: 0; padding: 10px 14px;
    border: 1px solid var(--cp-line); border-radius: 10px; background: var(--cp-card); }
.exp-cachepage .exp-check.is-bad { box-shadow: inset 4px 0 0 var(--cp-bad); }
.exp-cachepage .exp-check.is-warn { box-shadow: inset 4px 0 0 var(--cp-warn); }
.exp-cachepage .exp-check .exp-badge { justify-self: start; }
.exp-cachepage .exp-check-text { min-width: 0; overflow-wrap: anywhere; }
.exp-cachepage .exp-check-text strong { font-weight: 650; }
.exp-cachepage .exp-check-text p { margin-top: 2px; font-size: 13px; color: var(--cp-muted); }
.exp-cachepage .exp-check-text p.exp-fix { color: var(--cp-ink); }
.exp-cachepage .exp-check-text p.exp-fix::before { content: "\2192\00a0"; color: var(--cp-accent); font-weight: 700; }
.exp-cachepage details.exp-ok-checks { margin-top: 8px; }
.exp-cachepage details.exp-ok-checks > summary { cursor: pointer; font-size: 13px; font-weight: 650; color: var(--cp-muted); padding: 4px 0; }

/* Overview cards */
.exp-cachepage .exp-cards { display: grid; grid-template-columns: repeat(auto-fill, minmax(min(100%, 300px), 1fr)); gap: 12px; margin: 0; padding: 0; list-style: none; }
.exp-cachepage .exp-card { min-width: 0; margin: 0; padding: 14px 16px; border: 1px solid var(--cp-line); border-radius: var(--cp-radius); background: var(--cp-card);
    box-shadow: 0 1px 2px rgba(16, 24, 40, 0.04); }
.exp-cachepage .exp-card.is-warn { box-shadow: inset 4px 0 0 var(--cp-warn), 0 1px 2px rgba(16, 24, 40, 0.04); }
.exp-cachepage .exp-card.is-bad { box-shadow: inset 4px 0 0 var(--cp-bad), 0 1px 2px rgba(16, 24, 40, 0.04); }
.exp-cachepage .exp-card-head { display: flex; flex-wrap: wrap; align-items: center; justify-content: space-between; gap: 6px 10px; margin: 0 0 8px; }
.exp-cachepage .exp-rows { display: grid; grid-template-columns: fit-content(14em) minmax(0, 1fr); gap: 5px 12px; margin: 0; }
.exp-cachepage .exp-rows dt { margin: 0; min-width: 0; font-size: 12.5px; font-weight: 650; color: var(--cp-muted); overflow-wrap: break-word; }
.exp-cachepage .exp-rows dd { margin: 0; min-width: 0; overflow-wrap: anywhere; word-break: break-word; }

/* Panels (the detailed parts, folded) */
.exp-cachepage details.exp-panel { margin: 0 0 12px; border: 1px solid var(--cp-line); border-radius: var(--cp-radius); background: var(--cp-card); min-width: 0; }
.exp-cachepage details.exp-panel > summary { display: flex; flex-wrap: wrap; align-items: center; gap: 6px 10px; padding: 12px 16px; cursor: pointer; list-style: none; border-radius: var(--cp-radius); }
.exp-cachepage details.exp-panel > summary::-webkit-details-marker { display: none; }
.exp-cachepage details.exp-panel > summary::before { content: ""; width: 8px; height: 8px; border-right: 2px solid var(--cp-muted); border-bottom: 2px solid var(--cp-muted); transform: rotate(-45deg); transition: transform .15s; flex: 0 0 auto; }
.exp-cachepage details.exp-panel[open] > summary::before { transform: rotate(45deg); }
.exp-cachepage details.exp-panel > summary h2.exp-h2 { font-size: 15px; }
.exp-cachepage details.exp-panel > summary .exp-muted { font-size: 13px; }
.exp-cachepage .exp-panel-body { padding: 0 16px 16px; min-width: 0; }
.exp-cachepage .exp-panel-body > * + * { margin-top: 12px; }
.exp-cachepage .exp-subhead { display: flex; flex-wrap: wrap; align-items: center; gap: 6px 10px; }
.exp-cachepage .exp-subhead small { color: var(--cp-muted); }
.exp-cachepage .exp-note { font-size: 13px; color: var(--cp-muted); max-width: 90ch; }
.exp-cachepage .exp-inline { margin: 0; padding: 0; list-style: none; font-size: 13px; }
.exp-cachepage .exp-inline li { display: inline; }
.exp-cachepage .exp-inline li + li::before { content: " \00b7 "; color: var(--cp-muted); }
.exp-cachepage .exp-inline code { color: var(--cp-muted); }
.exp-cachepage .exp-two { display: grid; grid-template-columns: repeat(auto-fit, minmax(min(100%, 320px), 1fr)); gap: 16px 24px; }
.exp-cachepage .exp-box { min-width: 0; padding: 12px 14px; border: 1px solid var(--cp-line); border-radius: 10px; }
.exp-cachepage .exp-box > * + * { margin-top: 8px; }
.exp-cachepage .exp-table td:first-child, .exp-cachepage .exp-table-views td:nth-child(2) { overflow-wrap: normal; white-space: nowrap; }

/* Bars */
.exp-cachepage .exp-bars { display: grid; grid-template-columns: minmax(0, 8em) minmax(60px, 1fr) minmax(0, auto); gap: 6px 10px; align-items: center; margin: 0; font-size: 13px; }
.exp-cachepage .exp-bars dt { margin: 0; color: var(--cp-muted); font-weight: 650; font-size: 12.5px; }
.exp-cachepage .exp-bars dd { margin: 0; min-width: 0; overflow-wrap: anywhere; }
.exp-cachepage .exp-bar { height: 9px; border-radius: 5px; background: #e6e8ec; overflow: hidden; }
.exp-cachepage .exp-bar > span { display: block; height: 100%; border-radius: 5px; background: var(--cp-ok); }
.exp-cachepage .exp-bar.is-warn > span { background: #c27a00; }
.exp-cachepage .exp-bar.is-bad > span { background: var(--cp-bad); }
.exp-cachepage .exp-bar.is-neutral > span { background: #64748b; }
@media (max-width: 560px) {
    .exp-cachepage .exp-bars { grid-template-columns: minmax(0, 1fr) minmax(60px, 1fr); }
    .exp-cachepage .exp-bars dd.exp-bar-text { grid-column: 1 / -1; margin-bottom: 4px; }
}

/* Chips and tables */
.exp-cachepage .exp-chips { display: flex; flex-wrap: wrap; gap: 6px; margin: 0; padding: 0; list-style: none; }
.exp-cachepage .exp-chip { display: inline-block; max-width: 100%; padding: 1px 9px; border-radius: 6px; background: var(--cp-soft); color: var(--cp-ink); font: 12.5px/1.7 var(--cp-mono); overflow-wrap: anywhere; }
.exp-cachepage .exp-table-wrap { overflow-x: auto; border: 1px solid var(--cp-line); border-radius: 10px; background: var(--cp-card); max-width: 100%; }
.exp-cachepage .exp-table { width: 100%; margin: 0; border-collapse: collapse; }
.exp-cachepage .exp-table th, .exp-cachepage .exp-table td { padding: 8px 12px; border: 0; border-bottom: 1px solid var(--cp-line); text-align: left; vertical-align: top; background: transparent; color: var(--cp-ink); font-size: 13px; }
.exp-cachepage .exp-table th { font-size: 12px; font-weight: 650; text-transform: uppercase; letter-spacing: .04em; color: var(--cp-muted); background: var(--cp-soft); white-space: nowrap; }
.exp-cachepage .exp-table tr:last-child td { border-bottom: 0; }
.exp-cachepage .exp-table td { overflow-wrap: anywhere; }
.exp-cachepage .exp-table .exp-num { text-align: right; font-variant-numeric: tabular-nums; white-space: nowrap; }
.exp-cachepage .exp-ext-list { display: grid; grid-template-columns: repeat(auto-fill, minmax(min(100%, 260px), 1fr)); gap: 4px 18px; margin: 0; padding: 0; list-style: none; }
.exp-cachepage .exp-ext-list li { display: flex; justify-content: space-between; gap: 10px; padding: 4px 0; border-bottom: 1px solid var(--cp-line); min-width: 0; font-size: 13px; }
.exp-cachepage .exp-ext-list li span:first-child { min-width: 0; overflow-wrap: anywhere; font-family: var(--cp-mono); font-size: 12.5px; }
.exp-cachepage .exp-ext-list li span:last-child { color: var(--cp-muted); font-variant-numeric: tabular-nums; white-space: nowrap; }

/* The report */
.exp-cachepage textarea.exp-report { display: block; width: 100%; min-height: 320px; margin: 0; padding: 10px 12px; border: 1px solid #8f96a3; border-radius: 9px;
    background: var(--cp-soft); color: var(--cp-ink); font: 12px/1.5 var(--cp-mono); white-space: pre; overflow: auto; resize: vertical; }

.exp-cachepage .exp-cachepage-form { display: flex; flex-wrap: wrap; gap: 8px; margin: 0; }
.exp-cachepage .exp-cachepage-form .button { margin: 0; }

@media (max-width: 600px) {
    .exp-cachepage .exp-check { grid-template-columns: minmax(0, 1fr); }
    .exp-cachepage .exp-rows { grid-template-columns: minmax(0, 1fr); gap: 0 12px; }
    .exp-cachepage .exp-rows dd { margin-bottom: 6px; }
    .exp-cachepage .exp-actionbar .exp-btn { flex: 1 1 auto; }
}

/* Setup > Caches */
.exp-cachepage form { margin: 0; }
.exp-cachepage .exp-toolbar { display: grid; grid-template-columns: minmax(0, 2fr) minmax(0, 3fr); gap: 12px 16px; align-items: end; margin: 0 0 16px; padding: 14px 16px;
    border: 1px solid var(--cp-line); border-radius: var(--cp-radius); background: var(--cp-card); }
.exp-cachepage .exp-field { display: flex; flex-direction: column; gap: 5px; min-width: 0; margin: 0; padding: 0; border: 0; }
.exp-cachepage .exp-field > label, .exp-cachepage .exp-field > legend { padding: 0; font-size: 12.5px; font-weight: 650; color: var(--cp-ink); }
.exp-cachepage fieldset.exp-field > legend { float: left; width: 100%; margin: 0 0 5px; }
.exp-cachepage fieldset.exp-field > legend + * { clear: both; }
.exp-cachepage .exp-field input[type="search"] { width: 100%; min-height: 38px; margin: 0; padding: 6px 10px; border: 1px solid #8f96a3; border-radius: 9px;
    background: #fff; color: var(--cp-ink); font: inherit; font-size: 14px; }
.exp-cachepage .exp-field input:focus { border-color: var(--cp-accent); outline: 3px solid var(--cp-ring); outline-offset: 0; }
.exp-cachepage .exp-filter-chips { display: flex; flex-wrap: wrap; gap: 6px; }
.exp-cachepage .exp-filter-chip { position: relative; display: inline-flex; margin: 0; }
.exp-cachepage .exp-filter-chip input { position: absolute; opacity: 0; width: 1px; height: 1px; }
.exp-cachepage .exp-filter-chip span { display: inline-flex; align-items: center; min-height: 32px; padding: 4px 12px; border: 1px solid #c9ced6; border-radius: 999px;
    background: #fff; color: var(--cp-ink); font-size: 13px; cursor: pointer; }
.exp-cachepage .exp-filter-chip input:checked + span { border-color: var(--cp-accent); background: var(--cp-accent); color: #fff; font-weight: 650; }
.exp-cachepage .exp-filter-chip input:focus-visible + span { outline: 3px solid var(--cp-ring); outline-offset: 2px; }
.exp-cachepage .exp-filter-count { grid-column: 1 / -1; margin: 0; font-size: 13px; color: var(--cp-muted); }
.exp-cachepage .exp-group { margin: 0 0 18px; padding: 14px 16px; border: 1px solid var(--cp-line); border-radius: var(--cp-radius); background: var(--cp-card); min-width: 0; }
.exp-cachepage .exp-group-head { display: flex; flex-wrap: wrap; align-items: baseline; gap: 4px 12px; margin: 0 0 10px; }
.exp-cachepage .exp-group-head h2.exp-h2 { font-size: 16px; }
.exp-cachepage .exp-group-head p { flex: 1 1 100%; color: var(--cp-muted); font-size: 13px; max-width: 90ch; }
.exp-cachepage .exp-group-foot { display: flex; flex-wrap: wrap; align-items: flex-start; gap: 10px 16px; margin-top: 12px; }
.exp-cachepage .exp-group-foot code { color: var(--cp-muted); }
.exp-cachepage .exp-caches { width: 100%; border-collapse: collapse; }
.exp-cachepage .exp-caches th, .exp-cachepage .exp-caches td { padding: 8px 10px; border: 0; border-bottom: 1px solid var(--cp-line); text-align: left; vertical-align: top;
    background: transparent; color: var(--cp-ink); font-size: 13px; overflow-wrap: anywhere; }
.exp-cachepage .exp-caches th { font-size: 12px; font-weight: 650; text-transform: uppercase; letter-spacing: .04em; color: var(--cp-muted); background: var(--cp-soft); white-space: nowrap; }
.exp-cachepage .exp-caches tr:last-child td { border-bottom: 0; }
.exp-cachepage .exp-caches td.exp-check-cell { width: 34px; }
.exp-cachepage .exp-caches td.exp-check-cell input { width: 18px; height: 18px; margin: 2px 0 0; accent-color: var(--cp-accent); cursor: pointer; }
.exp-cachepage .exp-caches td.exp-num { text-align: right; white-space: nowrap; font-variant-numeric: tabular-nums; }
.exp-cachepage .exp-caches .exp-cache-name { font-weight: 650; }
.exp-cachepage .exp-caches .exp-cache-holds { display: block; margin-top: 2px; color: var(--cp-muted); font-size: 12.5px; }
.exp-cachepage .exp-caches .exp-badge { margin: 3px 4px 0 0; }
.exp-cachepage .exp-caches tr.is-disabled td { color: var(--cp-muted); }
.exp-cachepage .exp-caches code { color: var(--cp-muted); }
@media (max-width: 760px) {
    .exp-cachepage .exp-toolbar { grid-template-columns: minmax(0, 1fr); }
    .exp-cachepage .exp-caches thead { position: absolute; width: 1px; height: 1px; overflow: hidden; clip: rect(0 0 0 0); }
    .exp-cachepage .exp-caches, .exp-cachepage .exp-caches tbody, .exp-cachepage .exp-caches tr, .exp-cachepage .exp-caches td { display: block; width: 100%; }
    .exp-cachepage .exp-caches tr { position: relative; padding: 10px 0 10px 40px; border-bottom: 1px solid var(--cp-line); }
    .exp-cachepage .exp-caches tr:last-child { border-bottom: 0; }
    .exp-cachepage .exp-caches td { padding: 2px 0; border: 0; }
    .exp-cachepage .exp-caches td.exp-check-cell { position: absolute; left: 4px; top: 10px; width: auto; }
    .exp-cachepage .exp-caches td.exp-num { text-align: left; }
    .exp-cachepage .exp-caches td[data-label]::before { content: attr(data-label) ": "; color: var(--cp-muted); font-weight: 650; font-size: 12px; }
}

/* In-place confirmation: a details element whose summary is the button and whose body names what goes */
.exp-cachepage details.exp-confirm { max-width: 100%; }
.exp-cachepage details.exp-confirm > summary { list-style: none; }
.exp-cachepage details.exp-confirm > summary::-webkit-details-marker { display: none; }
.exp-cachepage details.exp-confirm[open] > summary { border-color: var(--cp-accent); }
.exp-cachepage .exp-confirm-body { margin-top: 8px; padding: 12px 14px; border: 1px solid #f3d19c; border-left: 4px solid var(--cp-warn); border-radius: 10px;
    background: var(--cp-warn-bg); color: var(--cp-ink); max-width: 72ch; }
.exp-cachepage .exp-confirm-body p { margin: 0 0 6px; }
.exp-cachepage .exp-confirm-body ul { margin: 0 0 8px; padding-left: 20px; font-size: 13px; }
.exp-cachepage .exp-btn-danger { border-color: var(--cp-bad); background: var(--cp-bad); color: #fff; }
.exp-cachepage .exp-btn-danger:hover { border-color: #7f1d1d; background: #7f1d1d; color: #fff; }
.exp-cachepage .exp-btn[disabled] { opacity: .55; cursor: not-allowed; }
.exp-cachepage .exp-feedback ul { margin: 6px 0 0; padding-left: 20px; }
.exp-cachepage .exp-feedback p + p { margin-top: 6px; }
.exp-cachepage .exp-feedback code { color: inherit; }
.exp-cachepage .exp-bottombar { position: sticky; bottom: 0; z-index: 2; display: flex; flex-wrap: wrap; align-items: center; gap: 10px 12px; margin: 0 0 20px; padding: 10px 14px;
    border: 1px solid var(--cp-line); border-radius: var(--cp-radius); background: var(--cp-soft); box-shadow: 0 -4px 12px -6px rgba(16, 24, 40, 0.15); }
.exp-cachepage .exp-bottombar .exp-meta { flex: 1 1 220px; font-size: 13px; color: var(--cp-muted); }
.exp-cachepage .exp-server-rows { margin-top: 14px !important; display: grid; grid-template-columns: minmax(0, 1fr); gap: 0; margin: 0; padding: 0; list-style: none; }
.exp-cachepage .exp-server-rows > li { display: flex; flex-wrap: wrap; align-items: center; justify-content: space-between; gap: 6px 14px; padding: 10px 0; border-bottom: 1px solid var(--cp-line); }
.exp-cachepage .exp-server-rows > li:last-child { border-bottom: 0; }
.exp-cachepage .exp-server-rows .exp-server-text { flex: 1 1 320px; min-width: 0; overflow-wrap: anywhere; }
.exp-cachepage .exp-server-rows .exp-server-text small { display: block; color: var(--cp-muted); font-size: 12.5px; }
.exp-cachepage .exp-server-rows .exp-actions { margin: 0; }
.exp-cachepage .exp-static-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(min(100%, 200px), 1fr)); gap: 12px 16px; align-items: end; }
.exp-cachepage .exp-static-grid select, .exp-cachepage .exp-static-grid input[type="text"] { width: 100%; min-height: 38px; padding: 6px 10px; border: 1px solid #8f96a3;
    border-radius: 9px; background: #fff; color: var(--cp-ink); font: inherit; font-size: 14px; }
.exp-cachepage #staticcache-console { max-width: 100%; }
.exp-cachepage .exp-commands { margin: 0; padding: 0; list-style: none; }
.exp-cachepage .exp-commands li { padding: 6px 0; border-bottom: 1px solid var(--cp-line); font-size: 13px; }
.exp-cachepage .exp-commands li:last-child { border-bottom: 0; }
.exp-cachepage .exp-commands code { display: block; margin-top: 2px; color: var(--cp-ink); }
.exp-cachepage .exp-figure.is-date strong { font-size: 16px; line-height: 1.3; }
.exp-cachepage .exp-confirm-body ul { columns: 2 16em; column-gap: 24px; }
.exp-cachepage .exp-feedback ul { columns: 2 16em; column-gap: 24px; }
.exp-cachepage .exp-caches .exp-cache-meta { display: block; margin-top: 3px; color: var(--cp-muted); font-size: 12.5px; overflow-wrap: anywhere; }
.exp-cachepage .exp-caches .exp-cache-meta code { font-size: 12px; }
.exp-cachepage .exp-caches th:nth-child(3), .exp-cachepage .exp-caches td:nth-child(3) { width: 7.5em; }
.exp-cachepage .exp-caches th:nth-child(4), .exp-cachepage .exp-caches td:nth-child(4) { width: 9em; }
.exp-cachepage fieldset.exp-field { background: none; box-shadow: none; border-radius: 0; }
.exp-cachepage fieldset.exp-field > legend { background: none; }
@media (max-width: 760px) { .exp-cachepage .exp-caches th:nth-child(n), .exp-cachepage .exp-caches td:nth-child(n) { width: 100%; } .exp-cachepage .exp-caches td.exp-check-cell { width: auto; } }
.exp-cachepage .exp-caches .exp-cleared-when, .exp-cachepage .exp-caches .exp-cleared-by { display: block; } .exp-cachepage .exp-caches .exp-cleared-by { color: var(--cp-muted); font-size: 12.5px; } .exp-cachepage .exp-caches th:nth-child(4), .exp-cachepage .exp-caches td:nth-child(4) { width: 11em; }
@media (max-width: 760px) { .exp-cachepage .exp-caches td:nth-child(4) { width: 100%; } }
</style>
{/literal}
