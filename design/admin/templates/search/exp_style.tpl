{* The look of the search statistics page (search/stats), in the visual language of the section, cronjobs and URL
   pages: the same building blocks as url/exp_style.tpl, scoped to this page.

   The same file is in design/admin and design/admin4, so every administration design resolves it. Everything is
   scoped to .exp-searchstats and takes admin4's tokens where they exist (--a4-*), with values of its own for the
   older designs. No shared stylesheet is changed. Guide: doc/guides/urls-and-aliases.md *}
{literal}
<style>
.exp-searchstats {
    --ss-ink: var(--a4-ink, #1f2430);
    --ss-muted: var(--a4-muted, #5d6573);
    --ss-line: var(--a4-line, #e3e6eb);
    --ss-soft: var(--a4-soft, #f6f7f9);
    --ss-card: #fff;
    --ss-accent: #c2410c;          /* white text on it is 5.2:1 */
    --ss-accent-hover: #9a3412;
    --ss-ring: rgba(194, 65, 12, 0.45);
    --ss-ok: #166534;   --ss-ok-bg: #e7f5ea;
    --ss-warn: #8a4b00; --ss-warn-bg: #fff3df;
    --ss-bad: #b91c1c;  --ss-bad-bg: #fdecec;
    --ss-info: #1e4fa8; --ss-info-bg: #e8effd;
    --ss-radius: 12px;
    --ss-mono: ui-monospace, SFMono-Regular, Menlo, Consolas, monospace;
    color: var(--ss-ink);
    font-size: 14px;
    line-height: 1.5;
}
.exp-searchstats *, .exp-searchstats *::before, .exp-searchstats *::after { box-sizing: border-box; }
.exp-searchstats [hidden] { display: none !important; }
.exp-searchstats .box-content { padding-bottom: 20px; }
.exp-searchstats h1.context-title { margin: 0; overflow-wrap: anywhere; }
.exp-searchstats h2.exp-h2 { margin: 0; padding: 0; border: 0; background: none; font-size: 16px; font-weight: 650; color: var(--ss-ink); }
.exp-searchstats h3 { overflow-wrap: anywhere; margin: 0; padding: 0; border: 0; background: none; font-size: 15px; font-weight: 650; color: var(--ss-ink); }
.exp-searchstats p { margin: 0; }
.exp-searchstats code { font-family: var(--ss-mono); font-size: 12.5px; overflow-wrap: anywhere; }
.exp-searchstats a { color: var(--ss-accent-hover); }
.exp-searchstats a:hover { color: var(--ss-ink); }
.exp-searchstats :focus-visible { outline: 3px solid var(--ss-ring); outline-offset: 2px; }
.exp-searchstats .exp-sr { position: absolute; width: 1px; height: 1px; margin: -1px; padding: 0; overflow: hidden; clip: rect(0 0 0 0); white-space: nowrap; border: 0; }
.exp-searchstats .exp-muted { color: var(--ss-muted); }
.exp-searchstats ul.exp-plain { margin: 0; padding: 0; list-style: none; }

/* A page drawn without the admin's main card (the edit view) gets one of its own */
.exp-searchstats.exp-standalone { max-width: 980px; margin: 16px auto 24px; padding: 18px 20px; border-radius: 18px; background: var(--ss-card);
    box-shadow: 0 0 0 1px rgba(16, 24, 40, 0.06), 0 12px 32px -6px rgba(16, 24, 40, 0.12); }
#maincontent .exp-searchstats.exp-standalone { max-width: none; margin: 0; padding: 0; border-radius: 0; background: transparent; box-shadow: none; }

.exp-searchstats .exp-title-row { display: flex; flex-wrap: wrap; align-items: center; gap: 6px 12px; }
.exp-searchstats .exp-title-key { color: var(--ss-muted); }
.exp-searchstats .exp-intro { margin: 8px 0 18px; max-width: 78ch; color: var(--ss-muted); }
.exp-searchstats .exp-section { margin: 0 0 24px; }
.exp-searchstats .exp-section-head { display: flex; flex-wrap: wrap; align-items: baseline; gap: 4px 12px; margin: 0 0 10px; }
.exp-searchstats .exp-section-head p { flex: 1 1 100%; color: var(--ss-muted); max-width: 78ch; }

/* Messages */
.exp-searchstats .exp-feedback { margin: 0 0 14px; padding: 10px 14px; border: 1px solid; border-left-width: 4px; border-radius: 10px; }
.exp-searchstats .exp-feedback.is-ok { border-color: #b7dfc1; border-left-color: var(--ss-ok); background: var(--ss-ok-bg); color: var(--ss-ok); }
.exp-searchstats .exp-feedback.is-bad { border-color: #f1b4b4; border-left-color: var(--ss-bad); background: var(--ss-bad-bg); color: var(--ss-bad); }
.exp-searchstats .exp-feedback.is-warn { border-color: #f3d19c; border-left-color: var(--ss-warn); background: var(--ss-warn-bg); color: var(--ss-warn); }
.exp-searchstats .exp-feedback.is-info { border-color: #bcd0f5; border-left-color: var(--ss-info); background: var(--ss-info-bg); color: var(--ss-info); }
.exp-searchstats .exp-feedback strong { color: inherit; }
.exp-searchstats .exp-feedback p + p, .exp-searchstats .exp-feedback p + ul, .exp-searchstats .exp-feedback ul + p { margin-top: 6px; }
.exp-searchstats .exp-feedback ul { margin: 6px 0 0; padding-left: 20px; }
.exp-searchstats .exp-feedback h2.exp-h2 { color: inherit; }
.exp-searchstats .exp-reasons { margin-top: 8px; }
.exp-searchstats .exp-reasons li + li { margin-top: 4px; }

/* Overview figures */
.exp-searchstats .exp-figures { display: grid; grid-template-columns: repeat(auto-fit, minmax(min(100%, 120px), 1fr)); gap: 10px; margin: 0 0 20px; padding: 0; list-style: none; }
.exp-searchstats .exp-figure { display: flex; flex-direction: column; gap: 2px; margin: 0; padding: 12px 14px; border: 1px solid var(--ss-line); border-radius: var(--ss-radius); background: var(--ss-card); }
.exp-searchstats .exp-figure strong { font-size: 22px; line-height: 1.15; font-weight: 700; font-variant-numeric: tabular-nums; color: var(--ss-ink); }
.exp-searchstats .exp-figure span { font-size: 12.5px; color: var(--ss-muted); }
.exp-searchstats .exp-figure.is-attention strong { color: var(--ss-bad); }

/* Buttons */
.exp-searchstats .exp-btn {
    display: inline-flex; align-items: center; justify-content: center; gap: 6px; min-height: 36px; margin: 0;
    padding: 6px 14px; border: 1px solid #c9ced6; border-radius: 9px; background: #fff; color: var(--ss-ink);
    font: 600 13.5px/1.2 inherit; font-family: inherit; cursor: pointer; white-space: nowrap; text-decoration: none;
}
.exp-searchstats a.exp-btn { color: var(--ss-ink); }
.exp-searchstats .exp-btn:hover:not([disabled]):not(.is-disabled) { border-color: var(--ss-accent); color: var(--ss-accent-hover); }
.exp-searchstats .exp-btn[disabled], .exp-searchstats .exp-btn.is-disabled { opacity: .55; cursor: not-allowed; }
.exp-searchstats .exp-btn-primary, .exp-searchstats a.exp-btn-primary { border-color: var(--ss-accent); background: var(--ss-accent); color: #fff; }
.exp-searchstats .exp-btn-primary:hover:not([disabled]) { border-color: var(--ss-accent-hover); background: var(--ss-accent-hover); color: #fff; }
.exp-searchstats .exp-btn-danger { border-color: var(--ss-bad); background: var(--ss-bad); color: #fff; }
.exp-searchstats .exp-btn-danger:hover:not([disabled]) { border-color: #7f1d1d; background: #7f1d1d; color: #fff; }
.exp-searchstats .exp-btn-outline-danger { border-color: var(--ss-bad); color: var(--ss-bad); }
.exp-searchstats .exp-btn-outline-danger:hover:not([disabled]) { background: var(--ss-bad); border-color: var(--ss-bad); color: #fff; }
.exp-searchstats .exp-btn-small { min-height: 30px; padding: 4px 10px; font-size: 12.5px; }
.exp-searchstats .exp-btn svg { flex: 0 0 auto; }
.exp-searchstats .exp-btn { max-width: 100%; }
@media (max-width: 600px) { .exp-searchstats .exp-btn { white-space: normal; text-align: center; } }
.exp-searchstats .exp-actions { display: flex; flex-wrap: wrap; align-items: center; gap: 8px; }
.exp-searchstats .exp-actions form { display: contents; }
.exp-searchstats .exp-actionbar { display: flex; flex-wrap: wrap; align-items: center; justify-content: space-between; gap: 10px 16px;
    margin: 0 0 18px; padding: 12px 14px; border: 1px solid var(--ss-line); border-radius: var(--ss-radius); background: var(--ss-soft); }
.exp-searchstats .exp-actionbar .exp-meta { flex: 1 1 260px; }
.exp-searchstats .exp-meta { color: var(--ss-muted); font-size: 13px; }

/* Controls */
.exp-searchstats .exp-toolbar {
    display: grid; grid-template-columns: repeat(auto-fit, minmax(min(100%, 230px), 1fr)); gap: 14px 16px; align-items: end;
    margin: 0 0 20px; padding: 16px; border: 1px solid var(--ss-line); border-radius: var(--ss-radius); background: var(--ss-card);
}
.exp-searchstats .exp-field { display: flex; flex-direction: column; gap: 5px; min-width: 0; margin: 0; padding: 0; border: 0; }
.exp-searchstats fieldset.exp-field { background: none; box-shadow: none; border-radius: 0; }
.exp-searchstats fieldset.exp-field > legend { float: left; width: 100%; margin: 0 0 5px; padding: 0; background: none; }
.exp-searchstats fieldset.exp-field > legend + * { clear: both; }
.exp-searchstats .exp-field > label, .exp-searchstats .exp-field > legend { padding: 0; font-size: 12.5px; font-weight: 650; color: var(--ss-ink); }
.exp-searchstats .exp-field select,
.exp-searchstats .exp-field input[type="search"],
.exp-searchstats .exp-field input[type="text"] {
    width: 100%; min-height: 38px; margin: 0; padding: 6px 10px; border: 1px solid #8f96a3; border-radius: 9px;
    background: #fff; color: var(--ss-ink); font: inherit; font-size: 14px; box-shadow: none;
}
.exp-searchstats .exp-field select:focus, .exp-searchstats .exp-field input:focus { border-color: var(--ss-accent); outline: 3px solid var(--ss-ring); outline-offset: 0; }
.exp-searchstats .exp-field input[aria-invalid="true"] { border-color: var(--ss-bad); box-shadow: inset 0 0 0 1px var(--ss-bad); }
.exp-searchstats .exp-field-wide { grid-column: 1 / -1; }
.exp-searchstats .exp-form-fields { display: grid; grid-template-columns: minmax(0, 1fr); gap: 18px; max-width: 640px; }
.exp-searchstats .exp-help { font-size: 13px; color: var(--ss-muted); max-width: 72ch; }
.exp-searchstats .exp-field-error { font-size: 13px; font-weight: 650; color: var(--ss-bad); }
.exp-searchstats .exp-chips { display: flex; flex-wrap: wrap; gap: 6px; }
.exp-searchstats .exp-chip { position: relative; display: inline-flex; margin: 0; }
.exp-searchstats .exp-chip input { position: absolute; opacity: 0; width: 1px; height: 1px; }
.exp-searchstats .exp-chip span { display: inline-flex; align-items: center; min-height: 32px; padding: 4px 12px; border: 1px solid #c9ced6; border-radius: 999px; background: #fff; color: var(--ss-ink); font-size: 13px; font-weight: 400; cursor: pointer; }
.exp-searchstats .exp-chip input:checked + span { border-color: var(--ss-accent); background: var(--ss-accent); color: #fff; font-weight: 650; }
.exp-searchstats .exp-chip input:focus-visible + span { outline: 3px solid var(--ss-ring); outline-offset: 2px; }
.exp-searchstats .exp-filter-count { grid-column: 1 / -1; margin: 0; font-size: 13px; color: var(--ss-muted); }

/* The section cards */
.exp-searchstats .exp-cards { display: grid; grid-template-columns: minmax(0, 1fr); gap: 12px; margin: 0; padding: 0; list-style: none; }
.exp-searchstats .exp-card {
    min-width: 0; margin: 0; padding: 14px 16px; border: 1px solid var(--ss-line); border-radius: var(--ss-radius); background: var(--ss-card);
    box-shadow: 0 1px 2px rgba(16, 24, 40, 0.04);
}
.exp-searchstats .exp-card.is-attention { box-shadow: inset 4px 0 0 var(--ss-warn), 0 1px 2px rgba(16, 24, 40, 0.04); }
.exp-searchstats .exp-card.is-selected { border-color: var(--ss-accent); }
.exp-searchstats .exp-card-head { display: flex; flex-wrap: wrap; align-items: flex-start; justify-content: space-between; gap: 10px 16px; }
.exp-searchstats .exp-card-title { display: flex; flex-wrap: wrap; align-items: center; gap: 6px 10px; min-width: 0; flex: 1 1 320px; }
.exp-searchstats .exp-card-title h3 a { color: var(--ss-ink); text-decoration: none; }
.exp-searchstats .exp-card-title h3 a:hover { color: var(--ss-accent-hover); text-decoration: underline; }
.exp-searchstats .exp-select { display: inline-flex; align-items: center; justify-content: center; width: 32px; height: 32px; margin: -4px 0 -4px -6px; border-radius: 8px; cursor: pointer; }
.exp-searchstats .exp-select:hover { background: var(--ss-soft); }
.exp-searchstats .exp-select input { width: 18px; height: 18px; margin: 0; accent-color: var(--ss-accent); cursor: pointer; }
.exp-searchstats .exp-badges { display: inline-flex; flex-wrap: wrap; gap: 6px; margin: 0; padding: 0; list-style: none; }
.exp-searchstats .exp-badge { display: inline-flex; align-items: center; gap: 5px; margin: 0; padding: 2px 9px; border-radius: 999px; font-size: 12px; font-weight: 650; line-height: 1.5; white-space: nowrap; background: var(--ss-soft); color: var(--ss-muted); }
.exp-searchstats .exp-badge.is-ok { background: var(--ss-ok-bg); color: var(--ss-ok); }
.exp-searchstats .exp-badge.is-warn { background: var(--ss-warn-bg); color: var(--ss-warn); }
.exp-searchstats .exp-badge.is-bad { background: var(--ss-bad-bg); color: var(--ss-bad); }
.exp-searchstats .exp-badge.is-info { background: var(--ss-info-bg); color: var(--ss-info); }
.exp-searchstats .exp-facts { display: grid; grid-template-columns: repeat(auto-fit, minmax(min(100%, 165px), 1fr)); gap: 8px 18px; margin: 12px 0 0; }
.exp-searchstats .exp-facts > div { min-width: 0; }
.exp-searchstats .exp-facts dt { margin: 0; font-size: 12px; font-weight: 650; text-transform: uppercase; letter-spacing: .04em; color: var(--ss-muted); }
.exp-searchstats .exp-facts dd { margin: 2px 0 0; overflow-wrap: anywhere; }
.exp-searchstats .exp-facts dd code { color: var(--ss-muted); }
.exp-searchstats .exp-panel { margin: 0 0 18px; padding: 14px 16px; border: 1px solid var(--ss-line); border-radius: var(--ss-radius); background: var(--ss-card); }
.exp-searchstats .exp-panel > .exp-facts { margin-top: 0; }
.exp-searchstats .exp-fn { display: inline-block; margin: 0 4px 4px 0; padding: 1px 8px; border-radius: 6px; background: var(--ss-soft); color: var(--ss-ink); font: 12.5px/1.6 var(--ss-mono); white-space: nowrap; }
.exp-searchstats .exp-empty { margin: 0; padding: 18px 16px; border: 1px dashed #c9ced6; border-radius: var(--ss-radius); color: var(--ss-muted); text-align: center; }

/* Tables */
.exp-searchstats .exp-table-wrap { overflow-x: auto; border: 1px solid var(--ss-line); border-radius: var(--ss-radius); background: var(--ss-card); }
.exp-searchstats .exp-table { width: 100%; margin: 0; border-collapse: collapse; }
.exp-searchstats .exp-table th, .exp-searchstats .exp-table td { padding: 9px 12px; border: 0; border-bottom: 1px solid var(--ss-line); text-align: left; vertical-align: top; background: transparent; color: var(--ss-ink); }
.exp-searchstats .exp-table th { font-size: 12px; font-weight: 650; text-transform: uppercase; letter-spacing: .04em; color: var(--ss-muted); background: var(--ss-soft); white-space: nowrap; }
.exp-searchstats .exp-table tr:last-child td { border-bottom: 0; }
.exp-searchstats .exp-table .exp-num { text-align: right; font-variant-numeric: tabular-nums; white-space: nowrap; }
.exp-searchstats .exp-table img { vertical-align: -3px; }

/* Page size and pages */
.exp-searchstats .exp-listfoot { display: flex; flex-wrap: wrap; align-items: center; justify-content: space-between; gap: 10px 18px; margin: 14px 0 0; }
.exp-searchstats .exp-sizes { display: flex; flex-wrap: wrap; align-items: center; gap: 6px; margin: 0; font-size: 13px; color: var(--ss-muted); }
.exp-searchstats .exp-sizes a, .exp-searchstats .exp-sizes span.current { display: inline-flex; align-items: center; justify-content: center; min-width: 34px; min-height: 30px; padding: 2px 8px; border: 1px solid #c9ced6; border-radius: 8px; text-decoration: none; }
.exp-searchstats .exp-sizes span.current { border-color: var(--ss-accent); background: var(--ss-accent); color: #fff; font-weight: 650; }
.exp-searchstats .exp-pager { min-width: 0; }
.exp-searchstats .exp-pager .pagenavigator { margin: 0; }
.exp-searchstats .exp-pager a { color: var(--ss-accent-hover); }
.exp-searchstats .exp-pager span.text, .exp-searchstats .exp-pager span.text a { color: var(--ss-accent-hover); }
.exp-searchstats .exp-pager span.disabled, .exp-searchstats .exp-pager span.text.disabled { color: var(--ss-muted); }
.exp-searchstats .exp-pager span.current { color: var(--ss-ink); }

/* The buttons under the list */
.exp-searchstats .exp-bottombar { display: flex; flex-wrap: wrap; align-items: center; gap: 10px 12px; margin: 18px 0 0; padding: 12px 14px; border: 1px solid var(--ss-line); border-radius: var(--ss-radius); background: var(--ss-soft); }
.exp-searchstats .exp-bottombar .exp-meta { flex: 1 1 280px; }

/* Tabs of lists (all, valid, invalid ...) and orders: links, the current one marked */
.exp-searchstats .exp-tabs { display: flex; flex-wrap: wrap; gap: 6px; margin: 0; padding: 0; list-style: none; }
.exp-searchstats .exp-tabs a, .exp-searchstats .exp-tabs span.current { display: inline-flex; align-items: center; gap: 6px; min-height: 32px; padding: 4px 12px; border: 1px solid #c9ced6; border-radius: 999px; background: #fff; color: var(--ss-ink); font-size: 13px; text-decoration: none; }
.exp-searchstats .exp-tabs a:hover { border-color: var(--ss-accent); color: var(--ss-accent-hover); }
.exp-searchstats .exp-tabs span.current { border-color: var(--ss-accent); background: var(--ss-accent); color: #fff; font-weight: 650; }
.exp-searchstats .exp-tabs .exp-count { font-variant-numeric: tabular-nums; opacity: .9; }
.exp-searchstats .exp-figure a { color: inherit; text-decoration: none; }
.exp-searchstats .exp-figure a:hover span { text-decoration: underline; }
.exp-searchstats .exp-figure.is-current { border-color: var(--ss-accent); box-shadow: inset 0 0 0 1px var(--ss-accent); }
.exp-searchstats .exp-searchrow { display: flex; flex-wrap: wrap; gap: 8px; align-items: center; }
.exp-searchstats .exp-searchrow input[type="search"], .exp-searchstats .exp-searchrow input[type="text"] { flex: 1 1 220px; min-width: 0; }
.exp-searchstats .exp-field input[type="checkbox"] { width: 18px; height: 18px; margin: 0; accent-color: var(--ss-accent); }
.exp-searchstats .exp-check { display: flex; align-items: flex-start; gap: 8px; font-weight: 400; }
.exp-searchstats .exp-check input { flex: 0 0 auto; margin-top: 2px !important; }
.exp-searchstats .exp-check strong { font-weight: 650; }
.exp-searchstats .exp-check > span { flex: 1 1 auto; min-width: 0; }
.exp-searchstats .exp-check .exp-help { display: block; font-weight: 400; }
.exp-searchstats .exp-check, .exp-searchstats .exp-check * { white-space: normal; }

/* One URL, alias or wildcard */
.exp-searchstats .exp-card-addr { min-width: 0; font: 600 14px/1.45 var(--ss-mono); overflow-wrap: anywhere; word-break: break-word; }
.exp-searchstats .exp-card-addr a { color: var(--ss-ink); text-decoration: none; }
.exp-searchstats .exp-card-addr a:hover { color: var(--ss-accent-hover); text-decoration: underline; }
.exp-searchstats .exp-arrow { color: var(--ss-muted); font-family: var(--ss-mono); }
.exp-searchstats .exp-card.is-bad { box-shadow: inset 4px 0 0 var(--ss-bad), 0 1px 2px rgba(16, 24, 40, 0.04); }
.exp-searchstats .exp-usage { margin: 0; padding: 0; list-style: none; }
.exp-searchstats .exp-usage li { display: inline; }
.exp-searchstats .exp-usage li + li::before { content: ", "; color: var(--ss-muted); }

/* A confirmation that opens in place: works without javascript */
.exp-searchstats details.exp-confirm { flex: 1 1 100%; margin: 0; padding: 0; border: 1px solid var(--ss-line); border-radius: var(--ss-radius); background: var(--ss-card); }
.exp-searchstats details.exp-confirm > summary { display: inline-flex; align-items: center; gap: 6px; min-height: 36px; padding: 6px 14px; color: var(--ss-bad); font-weight: 650; cursor: pointer; list-style: none; }
.exp-searchstats details.exp-confirm > summary::-webkit-details-marker { display: none; }
.exp-searchstats details.exp-confirm > summary::before { content: "\25B8"; }
.exp-searchstats details.exp-confirm[open] > summary::before { content: "\25BE"; }
.exp-searchstats details.exp-confirm > div { display: flex; flex-wrap: wrap; align-items: center; gap: 10px 14px; padding: 0 14px 14px; }
.exp-searchstats details.exp-confirm > div p { flex: 1 1 320px; color: var(--ss-ink); }

/* The wildcard tester's answer */
.exp-searchstats .exp-result { margin: 12px 0 0; }
.exp-searchstats .exp-result dl { display: grid; grid-template-columns: max-content minmax(0, 1fr); gap: 6px 14px; margin: 8px 0 0; }
.exp-searchstats .exp-result dt { font-weight: 650; }
.exp-searchstats .exp-result dd { margin: 0; overflow-wrap: anywhere; }
.exp-searchstats .exp-columns { display: grid; grid-template-columns: repeat(auto-fit, minmax(min(100%, 360px), 1fr)); gap: 18px; align-items: start; }
.exp-searchstats .exp-columns > .exp-panel { margin: 0; }

/* The phrase table: how often, as a bar against the most searched phrase of the page */
.exp-searchstats .exp-phrase { overflow-wrap: anywhere; font-weight: 600; }
.exp-searchstats .exp-table td { vertical-align: middle; }
.exp-searchstats .exp-table th.exp-bar-col, .exp-searchstats .exp-table td.exp-bar-col { width: 22%; min-width: 80px; }
.exp-searchstats .exp-bar { display: block; height: 8px; border-radius: 999px; background: var(--ss-soft); box-shadow: inset 0 0 0 1px var(--ss-line); overflow: hidden; }
.exp-searchstats .exp-bar > span { display: block; height: 100%; min-width: 4px; border-radius: 999px; background: var(--ss-accent); }
.exp-searchstats .exp-table tr.is-none .exp-bar > span { background: var(--ss-warn); }
.exp-searchstats .exp-bottombar details.exp-confirm { flex: 0 1 auto; }
.exp-searchstats .exp-bottombar details.exp-confirm[open] { flex: 1 1 100%; }
@media (max-width: 600px) {
    .exp-searchstats .exp-table th.exp-bar-col, .exp-searchstats .exp-table td.exp-bar-col { display: none; }
}

@media (max-width: 600px) {
    .exp-searchstats .exp-result dl { grid-template-columns: minmax(0, 1fr); gap: 2px; }
    .exp-searchstats .exp-result dd { margin-bottom: 6px; }
    .exp-searchstats.exp-standalone { margin: 8px; padding: 14px 12px; }
    .exp-searchstats .exp-card { padding: 12px; }
    .exp-searchstats .exp-card-head .exp-actions { width: 100%; }
    .exp-searchstats .exp-card-head .exp-actions .exp-btn { flex: 1 1 0; }
    .exp-searchstats .exp-actionbar .exp-actions, .exp-searchstats .exp-bottombar .exp-actions { width: 100%; }
    .exp-searchstats .exp-actionbar .exp-btn, .exp-searchstats .exp-bottombar .exp-btn { flex: 1 1 auto; }
    .exp-searchstats .exp-table th, .exp-searchstats .exp-table td { padding: 8px 9px; }
}
</style>
{/literal}
