{* The look of the upgrade check (setup/systemupgrade), in the visual language of the cronjobs, sections and
   sessions pages. Included once by setup/systemupgrade.tpl.

   The same file is in design/admin and design/admin4, so every administration design resolves it. Everything is
   scoped to .exp-upgrade and takes admin4's tokens where they exist (--a4-*), with values of its own for the older
   designs. admin4's dark mode keeps the content card white, so the colours here hold in both modes.
   Guide: doc/guides/upgrade-check.md *}
{literal}
<style>
.exp-upgrade {
    --up-ink: var(--a4-ink, #1f2430);
    --up-muted: var(--a4-muted, #5d6573);
    --up-line: var(--a4-line, #e3e6eb);
    --up-soft: var(--a4-soft, #f6f7f9);
    --up-card: #fff;
    --up-accent: #c2410c;          /* white text on it is 5.2:1 */
    --up-accent-hover: #9a3412;
    --up-ring: rgba(194, 65, 12, 0.45);
    --up-ok: #166534;   --up-ok-bg: #e7f5ea;
    --up-warn: #8a4b00; --up-warn-bg: #fff3df;
    --up-bad: #b91c1c;  --up-bad-bg: #fdecec;
    --up-info: #1e4fa8; --up-info-bg: #e8effd;
    --up-radius: 12px;
    --up-mono: ui-monospace, SFMono-Regular, Menlo, Consolas, monospace;
    color: var(--up-ink);
    font-size: 14px;
    line-height: 1.5;
}
.exp-upgrade *, .exp-upgrade *::before, .exp-upgrade *::after { box-sizing: border-box; }
.exp-upgrade [hidden] { display: none !important; }
.exp-upgrade .box-content { padding-bottom: 20px; }
.exp-upgrade h1.context-title { margin: 0; overflow-wrap: anywhere; }
.exp-upgrade h2.exp-h2 { margin: 0; padding: 0; border: 0; background: none; font-size: 16px; font-weight: 650; color: var(--up-ink); }
.exp-upgrade h3 { overflow-wrap: anywhere; margin: 0; padding: 0; border: 0; background: none; font-size: 15px; font-weight: 650; color: var(--up-ink); }
.exp-upgrade p { margin: 0; }
.exp-upgrade code { font-family: var(--up-mono); font-size: 12.5px; overflow-wrap: anywhere; }
.exp-upgrade a { color: var(--up-accent-hover); }
.exp-upgrade a:hover { color: var(--up-ink); }
.exp-upgrade :focus-visible { outline: 3px solid var(--up-ring); outline-offset: 2px; }
.exp-upgrade .exp-sr { position: absolute; width: 1px; height: 1px; margin: -1px; padding: 0; overflow: hidden; clip: rect(0 0 0 0); white-space: nowrap; border: 0; }
.exp-upgrade .exp-muted, .exp-upgrade .exp-meta { color: var(--up-muted); }
.exp-upgrade .exp-meta { font-size: 13px; }
.exp-upgrade ul.exp-plain { margin: 0; padding: 0; list-style: none; }

.exp-upgrade .exp-intro { margin: 8px 0 18px; max-width: 80ch; color: var(--up-muted); }
.exp-upgrade .exp-intro p + p { margin-top: 6px; }
.exp-upgrade .exp-section { margin: 0 0 24px; }
.exp-upgrade .exp-section-head { display: flex; flex-wrap: wrap; align-items: baseline; gap: 4px 12px; margin: 0 0 10px; }
.exp-upgrade .exp-section-head p { flex: 1 1 100%; color: var(--up-muted); max-width: 80ch; }

/* Messages */
.exp-upgrade .exp-feedback { margin: 0 0 14px; padding: 12px 14px; border: 1px solid; border-left-width: 4px; border-radius: 10px; }
.exp-upgrade .exp-feedback.is-ok { border-color: #b7dfc1; border-left-color: var(--up-ok); background: var(--up-ok-bg); color: var(--up-ok); }
.exp-upgrade .exp-feedback.is-bad { border-color: #f1b4b4; border-left-color: var(--up-bad); background: var(--up-bad-bg); color: var(--up-bad); }
.exp-upgrade .exp-feedback.is-warn { border-color: #f3d19c; border-left-color: var(--up-warn); background: var(--up-warn-bg); color: var(--up-warn); }
.exp-upgrade .exp-feedback.is-info { border-color: #bcd0f5; border-left-color: var(--up-info); background: var(--up-info-bg); color: var(--up-info); }
.exp-upgrade .exp-feedback strong, .exp-upgrade .exp-feedback h2.exp-h2 { color: inherit; }
.exp-upgrade .exp-feedback p { margin-top: 4px; }
.exp-upgrade .exp-feedback-head { display: flex; flex-wrap: wrap; align-items: baseline; justify-content: space-between; gap: 4px 14px; }
.exp-upgrade .exp-feedback .exp-time { font-size: 13px; font-weight: 600; font-variant-numeric: tabular-nums; }

/* Overview figures */
.exp-upgrade .exp-figures { display: grid; grid-template-columns: repeat(auto-fit, minmax(min(100%, 150px), 1fr)); gap: 10px; margin: 0 0 20px; padding: 0; list-style: none; }
.exp-upgrade .exp-figure { display: flex; flex-direction: column; gap: 2px; min-width: 0; margin: 0; padding: 12px 14px; border: 1px solid var(--up-line); border-radius: var(--up-radius); background: var(--up-card); }
.exp-upgrade .exp-figure strong { font-size: 22px; line-height: 1.15; font-weight: 700; font-variant-numeric: tabular-nums; color: var(--up-ink); overflow-wrap: anywhere; }
.exp-upgrade .exp-figure.is-text strong { font-size: 16px; line-height: 1.35; }
.exp-upgrade .exp-figure span { font-size: 12.5px; color: var(--up-muted); }
.exp-upgrade .exp-figure.is-attention strong { color: var(--up-bad); }
.exp-upgrade .exp-figure.is-note strong { color: var(--up-warn); }
.exp-upgrade .exp-figure.is-good strong { color: var(--up-ok); }

/* The two checks */
.exp-upgrade .exp-checks { display: grid; grid-template-columns: repeat(auto-fit, minmax(min(100%, 340px), 1fr)); gap: 12px; margin: 0 0 22px; }
.exp-upgrade .exp-check { display: flex; flex-direction: column; gap: 10px; min-width: 0; margin: 0; padding: 16px; border: 1px solid var(--up-line); border-radius: var(--up-radius); background: var(--up-card); box-shadow: 0 1px 2px rgba(16, 24, 40, 0.04); }
.exp-upgrade .exp-check.is-current { border-color: var(--up-accent); }
.exp-upgrade .exp-check-head { display: flex; flex-wrap: wrap; align-items: center; gap: 6px 10px; }
.exp-upgrade .exp-check p { color: var(--up-muted); }
.exp-upgrade .exp-check .exp-actions { margin-top: auto; padding-top: 4px; }

/* Buttons */
.exp-upgrade .exp-btn {
    display: inline-flex; align-items: center; justify-content: center; gap: 6px; min-height: 36px; max-width: 100%; margin: 0;
    padding: 6px 14px; border: 1px solid #c9ced6; border-radius: 9px; background: #fff; color: var(--up-ink);
    font: 600 13.5px/1.2 inherit; font-family: inherit; cursor: pointer; white-space: nowrap; text-decoration: none;
}
.exp-upgrade .exp-btn:hover:not([disabled]) { border-color: var(--up-accent); color: var(--up-accent-hover); }
.exp-upgrade .exp-btn[disabled] { opacity: .55; cursor: not-allowed; }
.exp-upgrade .exp-btn-primary { border-color: var(--up-accent); background: var(--up-accent); color: #fff; }
.exp-upgrade .exp-btn-primary:hover:not([disabled]) { border-color: var(--up-accent-hover); background: var(--up-accent-hover); color: #fff; }
.exp-upgrade .exp-btn-small { min-height: 30px; padding: 4px 10px; font-size: 12.5px; }
.exp-upgrade .exp-btn svg { flex: 0 0 auto; }
.exp-upgrade .exp-actions { display: flex; flex-wrap: wrap; align-items: center; gap: 8px; }
.exp-upgrade .exp-btn.is-busy { cursor: progress; }
@media (max-width: 600px) { .exp-upgrade .exp-btn { white-space: normal; text-align: center; } }

/* Badges */
.exp-upgrade .exp-badges { display: inline-flex; flex-wrap: wrap; gap: 6px; margin: 0; padding: 0; list-style: none; }
.exp-upgrade .exp-badge { display: inline-flex; align-items: center; gap: 5px; margin: 0; padding: 2px 9px; border-radius: 999px; font-size: 12px; font-weight: 650; line-height: 1.5; white-space: nowrap; background: var(--up-soft); color: var(--up-muted); }
.exp-upgrade .exp-badge.is-ok { background: var(--up-ok-bg); color: var(--up-ok); }
.exp-upgrade .exp-badge.is-warn { background: var(--up-warn-bg); color: var(--up-warn); }
.exp-upgrade .exp-badge.is-bad { background: var(--up-bad-bg); color: var(--up-bad); }
.exp-upgrade .exp-badge.is-info { background: var(--up-info-bg); color: var(--up-info); }

/* Facts */
.exp-upgrade .exp-facts { display: grid; grid-template-columns: repeat(auto-fit, minmax(min(100%, 170px), 1fr)); gap: 8px 18px; margin: 10px 0 0; }
.exp-upgrade .exp-facts > div { min-width: 0; }
.exp-upgrade .exp-facts dt { margin: 0; font-size: 12px; font-weight: 650; text-transform: uppercase; letter-spacing: .04em; color: var(--up-muted); }
.exp-upgrade .exp-facts dd { margin: 2px 0 0; overflow-wrap: anywhere; }

/* Panels and folds */
.exp-upgrade .exp-panel { min-width: 0; margin: 0 0 14px; padding: 14px 16px; border: 1px solid var(--up-line); border-radius: var(--up-radius); background: var(--up-card); }
.exp-upgrade .exp-panel-head { display: flex; flex-wrap: wrap; align-items: center; justify-content: space-between; gap: 6px 12px; }
.exp-upgrade .exp-fold { min-width: 0; margin: 0 0 12px; border: 1px solid var(--up-line); border-radius: var(--up-radius); background: var(--up-card); }
.exp-upgrade .exp-fold > summary {
    display: flex; flex-wrap: wrap; align-items: center; gap: 4px 12px; padding: 12px 16px; cursor: pointer; list-style: none; border-radius: var(--up-radius);
}
.exp-upgrade .exp-fold > summary::-webkit-details-marker { display: none; }
.exp-upgrade .exp-fold > summary::before { content: "\25B8"; flex: 0 0 auto; width: 1em; color: var(--up-muted); }
.exp-upgrade .exp-fold[open] > summary::before { content: "\25BE"; }
.exp-upgrade .exp-fold > summary:hover h3 { color: var(--up-accent-hover); }
.exp-upgrade .exp-fold.is-problem { box-shadow: inset 4px 0 0 var(--up-bad); }
.exp-upgrade .exp-fold.is-note { box-shadow: inset 4px 0 0 var(--up-warn); }
.exp-upgrade .exp-fold-body { min-width: 0; padding: 0 16px 16px; }
.exp-upgrade .exp-fold-body > * + * { margin-top: 10px; }

/* How to fix */
.exp-upgrade .exp-fix { padding: 10px 14px; border-radius: 10px; background: var(--up-soft); color: var(--up-ink); }
.exp-upgrade .exp-fix strong { display: block; margin-bottom: 2px; font-size: 13px; }
.exp-upgrade .exp-fix p + p { margin-top: 4px; }
.exp-upgrade .exp-fix code { color: var(--up-ink); }

/* Controls */
.exp-upgrade .exp-toolbar {
    display: grid; grid-template-columns: repeat(auto-fit, minmax(min(100%, 230px), 1fr)); gap: 12px 16px; align-items: end;
    margin: 0 0 14px; padding: 14px 16px; border: 1px solid var(--up-line); border-radius: var(--up-radius); background: var(--up-card);
}
.exp-upgrade .exp-field { display: flex; flex-direction: column; gap: 5px; min-width: 0; margin: 0; padding: 0; border: 0; }
.exp-upgrade fieldset.exp-field { background: none; box-shadow: none; border-radius: 0; }
.exp-upgrade fieldset.exp-field > legend { float: left; width: 100%; margin: 0 0 5px; padding: 0; background: none; }
.exp-upgrade fieldset.exp-field > legend + * { clear: both; }
.exp-upgrade .exp-field > label, .exp-upgrade .exp-field > legend { padding: 0; font-size: 12.5px; font-weight: 650; color: var(--up-ink); }
.exp-upgrade .exp-field select, .exp-upgrade .exp-field input[type="search"] {
    width: 100%; min-height: 38px; margin: 0; padding: 6px 10px; border: 1px solid #8f96a3; border-radius: 9px;
    background: #fff; color: var(--up-ink); font: inherit; font-size: 14px; box-shadow: none;
}
.exp-upgrade .exp-field select:focus, .exp-upgrade .exp-field input:focus { border-color: var(--up-accent); outline: 3px solid var(--up-ring); outline-offset: 0; }
.exp-upgrade .exp-field-wide { grid-column: 1 / -1; }
.exp-upgrade .exp-chips { display: flex; flex-wrap: wrap; gap: 6px; }
.exp-upgrade .exp-chip { position: relative; display: inline-flex; margin: 0; }
.exp-upgrade .exp-chip input { position: absolute; opacity: 0; width: 1px; height: 1px; }
.exp-upgrade .exp-chip span { display: inline-flex; align-items: center; gap: 6px; min-height: 32px; padding: 4px 12px; border: 1px solid #c9ced6; border-radius: 999px; background: #fff; color: var(--up-ink); font-size: 13px; font-weight: 400; cursor: pointer; }
.exp-upgrade .exp-chip input:checked + span { border-color: var(--up-accent); background: var(--up-accent); color: #fff; font-weight: 650; }
.exp-upgrade .exp-chip input:focus-visible + span { outline: 3px solid var(--up-ring); outline-offset: 2px; }
.exp-upgrade .exp-filter-count { grid-column: 1 / -1; margin: 0; font-size: 13px; color: var(--up-muted); }

/* Tables */
.exp-upgrade .exp-table-wrap { overflow-x: auto; border: 1px solid var(--up-line); border-radius: 10px; background: var(--up-card); }
.exp-upgrade .exp-table { width: 100%; margin: 0; border-collapse: collapse; }
.exp-upgrade .exp-table th, .exp-upgrade .exp-table td { padding: 8px 12px; border: 0; border-bottom: 1px solid var(--up-line); text-align: left; vertical-align: top; background: transparent; color: var(--up-ink); }
.exp-upgrade .exp-table th { font-size: 12px; font-weight: 650; text-transform: uppercase; letter-spacing: .04em; color: var(--up-muted); background: var(--up-soft); white-space: nowrap; }
.exp-upgrade .exp-table tr:last-child td { border-bottom: 0; }
.exp-upgrade .exp-table td.exp-path { min-width: 220px; overflow-wrap: anywhere; font-family: var(--up-mono); font-size: 12.5px; }
.exp-upgrade .exp-table td.exp-sums { font-family: var(--up-mono); font-size: 12px; color: var(--up-muted); overflow-wrap: anywhere; }
.exp-upgrade .exp-table td.exp-sums span[title] { white-space: nowrap; color: var(--up-ink); }
.exp-upgrade .exp-table td.exp-sums .exp-sum { display: inline-block; margin-right: 10px; white-space: nowrap; }
.exp-upgrade .exp-table .exp-num { text-align: right; font-variant-numeric: tabular-nums; white-space: nowrap; }
.exp-upgrade .exp-table td .exp-sum-label { font-family: inherit; font-size: 11.5px; font-weight: 650; text-transform: uppercase; letter-spacing: .03em; }

/* SQL */
.exp-upgrade .exp-sql {
    margin: 0; max-height: 26em; overflow: auto; padding: 12px 14px; border-radius: 10px; background: #16161a; color: #e4e4e7;
    white-space: pre-wrap; word-break: break-word; overflow-wrap: anywhere; font: 12px/1.55 var(--up-mono);
}
.exp-upgrade .exp-sql-head { display: flex; flex-wrap: wrap; align-items: center; justify-content: space-between; gap: 6px 12px; }
.exp-upgrade .exp-empty { margin: 0; padding: 18px 16px; border: 1px dashed #c9ced6; border-radius: var(--up-radius); color: var(--up-muted); text-align: center; }
.exp-upgrade .exp-ext-list { display: flex; flex-wrap: wrap; gap: 6px; margin: 8px 0 0; padding: 0; list-style: none; }
.exp-upgrade .exp-ext-list li { margin: 0; padding: 1px 8px; border-radius: 6px; background: var(--up-soft); font: 12.5px/1.6 var(--up-mono); }
.exp-upgrade .exp-docs { margin: 24px 0 0; padding-top: 14px; border-top: 1px solid var(--up-line); color: var(--up-muted); font-size: 13px; }

@media (max-width: 600px) {
    .exp-upgrade .exp-check, .exp-upgrade .exp-panel { padding: 12px; }
    .exp-upgrade .exp-check .exp-actions .exp-btn { flex: 1 1 auto; }
    .exp-upgrade .exp-table th, .exp-upgrade .exp-table td { padding: 7px 9px; }
}
</style>
{/literal}
