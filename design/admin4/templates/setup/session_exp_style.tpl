{* The look of the session pages (setup/session, its confirmation and the page for handlers without a session
   table), in the visual language of the sections and cronjobs pages. Included once by each of them.

   The same file is in design/admin and design/admin4, so every administration design resolves it. Everything is
   scoped to .exp-sess and takes admin4's tokens where they exist (--a4-*), with values of its own for the older
   designs. Guide: doc/guides/sessions.md *}
{literal}
<style>
.exp-sess {
    --sc-ink: var(--a4-ink, #1f2430);
    --sc-muted: var(--a4-muted, #5d6573);
    --sc-line: var(--a4-line, #e3e6eb);
    --sc-soft: var(--a4-soft, #f6f7f9);
    --sc-card: #fff;
    --sc-accent: #c2410c;          /* white text on it is 5.2:1 */
    --sc-accent-hover: #9a3412;
    --sc-ring: rgba(194, 65, 12, 0.45);
    --sc-ok: #166534;   --sc-ok-bg: #e7f5ea;
    --sc-warn: #8a4b00; --sc-warn-bg: #fff3df;
    --sc-bad: #b91c1c;  --sc-bad-bg: #fdecec;
    --sc-info: #1e4fa8; --sc-info-bg: #e8effd;
    --sc-radius: 12px;
    --sc-mono: ui-monospace, SFMono-Regular, Menlo, Consolas, monospace;
    color: var(--sc-ink);
    font-size: 14px;
    line-height: 1.5;
}
.exp-sess *, .exp-sess *::before, .exp-sess *::after { box-sizing: border-box; }
.exp-sess [hidden] { display: none !important; }
.exp-sess .box-content { padding-bottom: 20px; }
.exp-sess h1.context-title { margin: 0; overflow-wrap: anywhere; }
.exp-sess h2.exp-h2 { margin: 0; padding: 0; border: 0; background: none; font-size: 16px; font-weight: 650; color: var(--sc-ink); }
.exp-sess h3 { overflow-wrap: anywhere; margin: 0; padding: 0; border: 0; background: none; font-size: 15px; font-weight: 650; color: var(--sc-ink); }
.exp-sess p { margin: 0; }
.exp-sess code { font-family: var(--sc-mono); font-size: 12.5px; overflow-wrap: anywhere; }
.exp-sess a { color: var(--sc-accent-hover); }
.exp-sess a:hover { color: var(--sc-ink); }
.exp-sess :focus-visible { outline: 3px solid var(--sc-ring); outline-offset: 2px; }
.exp-sess .exp-sr { position: absolute; width: 1px; height: 1px; margin: -1px; padding: 0; overflow: hidden; clip: rect(0 0 0 0); white-space: nowrap; border: 0; }
.exp-sess .exp-muted { color: var(--sc-muted); }
.exp-sess ul.exp-plain { margin: 0; padding: 0; list-style: none; }

/* A page drawn without the admin's main card (the edit view) gets one of its own */
.exp-sess.exp-standalone { max-width: 980px; margin: 16px auto 24px; padding: 18px 20px; border-radius: 18px; background: var(--sc-card);
    box-shadow: 0 0 0 1px rgba(16, 24, 40, 0.06), 0 12px 32px -6px rgba(16, 24, 40, 0.12); }
#maincontent .exp-sess.exp-standalone { max-width: none; margin: 0; padding: 0; border-radius: 0; background: transparent; box-shadow: none; }

.exp-sess .exp-title-row { display: flex; flex-wrap: wrap; align-items: center; gap: 6px 12px; }
.exp-sess .exp-title-key { color: var(--sc-muted); }
.exp-sess .exp-intro { margin: 8px 0 18px; max-width: 78ch; color: var(--sc-muted); }
.exp-sess .exp-section { margin: 0 0 24px; }
.exp-sess .exp-section-head { display: flex; flex-wrap: wrap; align-items: baseline; gap: 4px 12px; margin: 0 0 10px; }
.exp-sess .exp-section-head p { flex: 1 1 100%; color: var(--sc-muted); max-width: 78ch; }

/* Messages */
.exp-sess .exp-feedback { margin: 0 0 14px; padding: 10px 14px; border: 1px solid; border-left-width: 4px; border-radius: 10px; }
.exp-sess .exp-feedback.is-ok { border-color: #b7dfc1; border-left-color: var(--sc-ok); background: var(--sc-ok-bg); color: var(--sc-ok); }
.exp-sess .exp-feedback.is-bad { border-color: #f1b4b4; border-left-color: var(--sc-bad); background: var(--sc-bad-bg); color: var(--sc-bad); }
.exp-sess .exp-feedback.is-warn { border-color: #f3d19c; border-left-color: var(--sc-warn); background: var(--sc-warn-bg); color: var(--sc-warn); }
.exp-sess .exp-feedback.is-info { border-color: #bcd0f5; border-left-color: var(--sc-info); background: var(--sc-info-bg); color: var(--sc-info); }
.exp-sess .exp-feedback strong { color: inherit; }
.exp-sess .exp-feedback p + p, .exp-sess .exp-feedback p + ul, .exp-sess .exp-feedback ul + p { margin-top: 6px; }
.exp-sess .exp-feedback ul { margin: 6px 0 0; padding-left: 20px; }
.exp-sess .exp-feedback h2.exp-h2 { color: inherit; }
.exp-sess .exp-reasons { margin-top: 8px; }
.exp-sess .exp-reasons li + li { margin-top: 4px; }

/* Overview figures */
.exp-sess .exp-figures { display: grid; grid-template-columns: repeat(auto-fit, minmax(min(100%, 120px), 1fr)); gap: 10px; margin: 0 0 20px; padding: 0; list-style: none; }
.exp-sess .exp-figure { display: flex; flex-direction: column; gap: 2px; margin: 0; padding: 12px 14px; border: 1px solid var(--sc-line); border-radius: var(--sc-radius); background: var(--sc-card); }
.exp-sess .exp-figure strong { font-size: 22px; line-height: 1.15; font-weight: 700; font-variant-numeric: tabular-nums; color: var(--sc-ink); }
.exp-sess .exp-figure span { font-size: 12.5px; color: var(--sc-muted); }
.exp-sess .exp-figure.is-attention strong { color: var(--sc-bad); }

/* Buttons */
.exp-sess .exp-btn {
    display: inline-flex; align-items: center; justify-content: center; gap: 6px; min-height: 36px; margin: 0;
    padding: 6px 14px; border: 1px solid #c9ced6; border-radius: 9px; background: #fff; color: var(--sc-ink);
    font: 600 13.5px/1.2 inherit; font-family: inherit; cursor: pointer; white-space: nowrap; text-decoration: none;
}
.exp-sess a.exp-btn { color: var(--sc-ink); }
.exp-sess .exp-btn:hover:not([disabled]):not(.is-disabled) { border-color: var(--sc-accent); color: var(--sc-accent-hover); }
.exp-sess .exp-btn[disabled], .exp-sess .exp-btn.is-disabled { opacity: .55; cursor: not-allowed; }
.exp-sess .exp-btn-primary, .exp-sess a.exp-btn-primary { border-color: var(--sc-accent); background: var(--sc-accent); color: #fff; }
.exp-sess .exp-btn-primary:hover:not([disabled]):not(.is-disabled) { border-color: var(--sc-accent-hover); background: var(--sc-accent-hover); color: #fff; }
.exp-sess .exp-btn-danger { border-color: var(--sc-bad); background: var(--sc-bad); color: #fff; }
.exp-sess .exp-btn-danger:hover:not([disabled]):not(.is-disabled) { border-color: #7f1d1d; background: #7f1d1d; color: #fff; }
.exp-sess .exp-btn-outline-danger { border-color: var(--sc-bad); color: var(--sc-bad); }
.exp-sess .exp-btn-outline-danger:hover:not([disabled]):not(.is-disabled) { background: var(--sc-bad); border-color: var(--sc-bad); color: #fff; }
.exp-sess .exp-btn-small { min-height: 30px; padding: 4px 10px; font-size: 12.5px; }
.exp-sess .exp-btn svg { flex: 0 0 auto; }
.exp-sess .exp-btn { max-width: 100%; }
/* Sorting, the settings snippet, the confirmation */
.exp-sess .exp-sortbar { display: flex; flex-wrap: wrap; align-items: center; gap: 6px; margin: 0 0 12px; font-size: 13px; color: var(--sc-muted); }
.exp-sess .exp-sortbar a, .exp-sess .exp-sortbar span.current { display: inline-flex; align-items: center; gap: 4px; min-height: 30px; padding: 2px 10px; border: 1px solid #c9ced6; border-radius: 999px; text-decoration: none; color: var(--sc-ink); background: var(--sc-card); }
.exp-sess .exp-sortbar span.current { border-color: var(--sc-accent); background: var(--sc-accent); color: #fff; font-weight: 650; }
.exp-sess pre.exp-code { margin: 8px 0 0; padding: 10px 12px; overflow-x: auto; border: 1px solid var(--sc-line); border-radius: 9px; background: var(--sc-soft); color: var(--sc-ink); font: 12.5px/1.6 var(--sc-mono); white-space: pre; }
.exp-sess ol.exp-steps { margin: 6px 0 0; padding-left: 22px; }
.exp-sess ol.exp-steps li + li { margin-top: 6px; }
.exp-sess .exp-confirm-list { margin: 10px 0 0; padding-left: 20px; }
.exp-sess .exp-confirm-list li + li { margin-top: 3px; }
.exp-sess .exp-row.is-self { box-shadow: inset 4px 0 0 var(--sc-info), 0 1px 2px rgba(16, 24, 40, 0.04); }
.exp-sess .exp-row.is-expired { background: var(--sc-soft); }
.exp-sess .exp-select input[disabled] { cursor: not-allowed; opacity: .5; }
.exp-sess details.exp-panel > summary { cursor: pointer; }
.exp-sess details.exp-panel > summary h2 { display: inline; }

@media (max-width: 600px) { .exp-sess .exp-btn { white-space: normal; text-align: center; } }
.exp-sess .exp-actions { display: flex; flex-wrap: wrap; align-items: center; gap: 8px; }
.exp-sess .exp-actions form { display: contents; }
.exp-sess .exp-actionbar { display: flex; flex-wrap: wrap; align-items: center; justify-content: space-between; gap: 10px 16px;
    margin: 0 0 18px; padding: 12px 14px; border: 1px solid var(--sc-line); border-radius: var(--sc-radius); background: var(--sc-soft); }
.exp-sess .exp-actionbar .exp-meta { flex: 1 1 260px; }
.exp-sess .exp-meta { color: var(--sc-muted); font-size: 13px; }

/* Controls */
.exp-sess .exp-toolbar {
    display: grid; grid-template-columns: repeat(auto-fit, minmax(min(100%, 230px), 1fr)); gap: 14px 16px; align-items: end;
    margin: 0 0 20px; padding: 16px; border: 1px solid var(--sc-line); border-radius: var(--sc-radius); background: var(--sc-card);
}
.exp-sess .exp-field { display: flex; flex-direction: column; gap: 5px; min-width: 0; margin: 0; padding: 0; border: 0; }
.exp-sess fieldset.exp-field { background: none; box-shadow: none; border-radius: 0; }
.exp-sess fieldset.exp-field > legend { float: left; width: 100%; margin: 0 0 5px; padding: 0; background: none; }
.exp-sess fieldset.exp-field > legend + * { clear: both; }
.exp-sess .exp-field > label, .exp-sess .exp-field > legend { padding: 0; font-size: 12.5px; font-weight: 650; color: var(--sc-ink); }
.exp-sess .exp-field select,
.exp-sess .exp-field input[type="search"],
.exp-sess .exp-field input[type="text"] {
    width: 100%; min-height: 38px; margin: 0; padding: 6px 10px; border: 1px solid #8f96a3; border-radius: 9px;
    background: #fff; color: var(--sc-ink); font: inherit; font-size: 14px; box-shadow: none;
}
.exp-sess .exp-field select:focus, .exp-sess .exp-field input:focus { border-color: var(--sc-accent); outline: 3px solid var(--sc-ring); outline-offset: 0; }
.exp-sess .exp-field input[aria-invalid="true"] { border-color: var(--sc-bad); box-shadow: inset 0 0 0 1px var(--sc-bad); }
.exp-sess .exp-field-wide { grid-column: 1 / -1; }
.exp-sess .exp-form-fields { display: grid; grid-template-columns: minmax(0, 1fr); gap: 18px; max-width: 640px; }
.exp-sess .exp-help { font-size: 13px; color: var(--sc-muted); max-width: 72ch; }
.exp-sess .exp-field-error { font-size: 13px; font-weight: 650; color: var(--sc-bad); }
.exp-sess .exp-chips { display: flex; flex-wrap: wrap; gap: 6px; }
.exp-sess .exp-chip { position: relative; display: inline-flex; margin: 0; }
.exp-sess .exp-chip input { position: absolute; opacity: 0; width: 1px; height: 1px; }
.exp-sess .exp-chip span { display: inline-flex; align-items: center; min-height: 32px; padding: 4px 12px; border: 1px solid #c9ced6; border-radius: 999px; background: #fff; color: var(--sc-ink); font-size: 13px; font-weight: 400; cursor: pointer; }
.exp-sess .exp-chip input:checked + span { border-color: var(--sc-accent); background: var(--sc-accent); color: #fff; font-weight: 650; }
.exp-sess .exp-chip input:focus-visible + span { outline: 3px solid var(--sc-ring); outline-offset: 2px; }
.exp-sess .exp-filter-count { grid-column: 1 / -1; margin: 0; font-size: 13px; color: var(--sc-muted); }

/* The section cards */
.exp-sess .exp-rows { display: grid; grid-template-columns: minmax(0, 1fr); gap: 12px; margin: 0; padding: 0; list-style: none; }
.exp-sess .exp-row {
    min-width: 0; margin: 0; padding: 14px 16px; border: 1px solid var(--sc-line); border-radius: var(--sc-radius); background: var(--sc-card);
    box-shadow: 0 1px 2px rgba(16, 24, 40, 0.04);
}
.exp-sess .exp-row.is-attention { box-shadow: inset 4px 0 0 var(--sc-warn), 0 1px 2px rgba(16, 24, 40, 0.04); }
.exp-sess .exp-row.is-selected { border-color: var(--sc-accent); }
.exp-sess .exp-row-head { display: flex; flex-wrap: wrap; align-items: flex-start; justify-content: space-between; gap: 10px 16px; }
.exp-sess .exp-row-title { display: flex; flex-wrap: wrap; align-items: center; gap: 6px 10px; min-width: 0; flex: 1 1 320px; }
.exp-sess .exp-row-title h3 a { color: var(--sc-ink); text-decoration: none; }
.exp-sess .exp-row-title h3 a:hover { color: var(--sc-accent-hover); text-decoration: underline; }
.exp-sess .exp-select { display: inline-flex; align-items: center; justify-content: center; width: 32px; height: 32px; margin: -4px 0 -4px -6px; border-radius: 8px; cursor: pointer; }
.exp-sess .exp-select:hover { background: var(--sc-soft); }
.exp-sess .exp-select input { width: 18px; height: 18px; margin: 0; accent-color: var(--sc-accent); cursor: pointer; }
.exp-sess .exp-badges { display: inline-flex; flex-wrap: wrap; gap: 6px; margin: 0; padding: 0; list-style: none; }
.exp-sess .exp-badge { display: inline-flex; align-items: center; gap: 5px; margin: 0; padding: 2px 9px; border-radius: 999px; font-size: 12px; font-weight: 650; line-height: 1.5; white-space: nowrap; background: var(--sc-soft); color: var(--sc-muted); }
.exp-sess .exp-badge.is-ok { background: var(--sc-ok-bg); color: var(--sc-ok); }
.exp-sess .exp-badge.is-warn { background: var(--sc-warn-bg); color: var(--sc-warn); }
.exp-sess .exp-badge.is-bad { background: var(--sc-bad-bg); color: var(--sc-bad); }
.exp-sess .exp-badge.is-info { background: var(--sc-info-bg); color: var(--sc-info); }
.exp-sess .exp-facts { display: grid; grid-template-columns: repeat(auto-fit, minmax(min(100%, 165px), 1fr)); gap: 8px 18px; margin: 12px 0 0; }
.exp-sess .exp-facts > div { min-width: 0; }
.exp-sess .exp-facts dt { margin: 0; font-size: 12px; font-weight: 650; text-transform: uppercase; letter-spacing: .04em; color: var(--sc-muted); }
.exp-sess .exp-facts dd { margin: 2px 0 0; overflow-wrap: anywhere; }
.exp-sess .exp-facts dd code { color: var(--sc-muted); }
.exp-sess .exp-panel { margin: 0 0 18px; padding: 14px 16px; border: 1px solid var(--sc-line); border-radius: var(--sc-radius); background: var(--sc-card); }
.exp-sess .exp-panel > .exp-facts { margin-top: 0; }
.exp-sess .exp-fn { display: inline-block; margin: 0 4px 4px 0; padding: 1px 8px; border-radius: 6px; background: var(--sc-soft); color: var(--sc-ink); font: 12.5px/1.6 var(--sc-mono); white-space: nowrap; }
.exp-sess .exp-empty { margin: 0; padding: 18px 16px; border: 1px dashed #c9ced6; border-radius: var(--sc-radius); color: var(--sc-muted); text-align: center; }

/* Tables */
.exp-sess .exp-table-wrap { overflow-x: auto; border: 1px solid var(--sc-line); border-radius: var(--sc-radius); background: var(--sc-card); }
.exp-sess .exp-table { width: 100%; margin: 0; border-collapse: collapse; }
.exp-sess .exp-table th, .exp-sess .exp-table td { padding: 9px 12px; border: 0; border-bottom: 1px solid var(--sc-line); text-align: left; vertical-align: top; background: transparent; color: var(--sc-ink); }
.exp-sess .exp-table th { font-size: 12px; font-weight: 650; text-transform: uppercase; letter-spacing: .04em; color: var(--sc-muted); background: var(--sc-soft); white-space: nowrap; }
.exp-sess .exp-table tr:last-child td { border-bottom: 0; }
.exp-sess .exp-table .exp-num { text-align: right; font-variant-numeric: tabular-nums; white-space: nowrap; }
.exp-sess .exp-table img { vertical-align: -3px; }

/* Page size and pages */
.exp-sess .exp-listfoot { display: flex; flex-wrap: wrap; align-items: center; justify-content: space-between; gap: 10px 18px; margin: 14px 0 0; }
.exp-sess .exp-sizes { display: flex; flex-wrap: wrap; align-items: center; gap: 6px; margin: 0; font-size: 13px; color: var(--sc-muted); }
.exp-sess .exp-sizes a, .exp-sess .exp-sizes span.current { display: inline-flex; align-items: center; justify-content: center; min-width: 34px; min-height: 30px; padding: 2px 8px; border: 1px solid #c9ced6; border-radius: 8px; text-decoration: none; }
.exp-sess .exp-sizes span.current { border-color: var(--sc-accent); background: var(--sc-accent); color: #fff; font-weight: 650; }
.exp-sess .exp-pager { min-width: 0; }
.exp-sess .exp-pager .pagenavigator { margin: 0; }
.exp-sess .exp-pager a { color: var(--sc-accent-hover); }
.exp-sess .exp-pager span.text, .exp-sess .exp-pager span.text a { color: var(--sc-accent-hover); }
.exp-sess .exp-pager span.disabled, .exp-sess .exp-pager span.text.disabled { color: var(--sc-muted); }
.exp-sess .exp-pager span.current { color: var(--sc-ink); }

/* The buttons under the list */
.exp-sess .exp-bottombar { display: flex; flex-wrap: wrap; align-items: center; gap: 10px 12px; margin: 18px 0 0; padding: 12px 14px; border: 1px solid var(--sc-line); border-radius: var(--sc-radius); background: var(--sc-soft); }
.exp-sess .exp-bottombar .exp-meta { flex: 1 1 280px; }

@media (max-width: 600px) {
    .exp-sess.exp-standalone { margin: 8px; padding: 14px 12px; }
    .exp-sess .exp-row { padding: 12px; }
    .exp-sess .exp-row-head .exp-actions { width: 100%; }
    .exp-sess .exp-row-head .exp-actions .exp-btn { flex: 1 1 0; }
    .exp-sess .exp-actionbar .exp-actions, .exp-sess .exp-bottombar .exp-actions { width: 100%; }
    .exp-sess .exp-actionbar .exp-btn, .exp-sess .exp-bottombar .exp-btn { flex: 1 1 auto; }
    .exp-sess .exp-table th, .exp-sess .exp-table td { padding: 8px 9px; }
}
</style>
{/literal}
