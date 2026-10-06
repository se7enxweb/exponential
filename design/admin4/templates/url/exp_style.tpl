{* The look of the URL pages: the link list (url/list), the global URL aliases (content/urltranslator) and the
   URL wildcards (content/urlwildcards), in the visual language of the section and cronjobs pages. Included once by
   each of them.

   The same file is in design/admin and design/admin4, so every administration design resolves it. Everything is
   scoped to .exp-urls and takes admin4's tokens where they exist (--a4-*), with values of its own for the
   older designs. No shared stylesheet is changed. Guide: doc/guides/urls-and-aliases.md *}
{literal}
<style>
.exp-urls {
    --ur-ink: var(--a4-ink, #1f2430);
    --ur-muted: var(--a4-muted, #5d6573);
    --ur-line: var(--a4-line, #e3e6eb);
    --ur-soft: var(--a4-soft, #f6f7f9);
    --ur-card: #fff;
    --ur-accent: #c2410c;          /* white text on it is 5.2:1 */
    --ur-accent-hover: #9a3412;
    --ur-ring: rgba(194, 65, 12, 0.45);
    --ur-ok: #166534;   --ur-ok-bg: #e7f5ea;
    --ur-warn: #8a4b00; --ur-warn-bg: #fff3df;
    --ur-bad: #b91c1c;  --ur-bad-bg: #fdecec;
    --ur-info: #1e4fa8; --ur-info-bg: #e8effd;
    --ur-radius: 12px;
    --ur-mono: ui-monospace, SFMono-Regular, Menlo, Consolas, monospace;
    color: var(--ur-ink);
    font-size: 14px;
    line-height: 1.5;
}
.exp-urls *, .exp-urls *::before, .exp-urls *::after { box-sizing: border-box; }
.exp-urls [hidden] { display: none !important; }
.exp-urls .box-content { padding-bottom: 20px; }
.exp-urls h1.context-title { margin: 0; overflow-wrap: anywhere; }
.exp-urls h2.exp-h2 { margin: 0; padding: 0; border: 0; background: none; font-size: 16px; font-weight: 650; color: var(--ur-ink); }
.exp-urls h3 { overflow-wrap: anywhere; margin: 0; padding: 0; border: 0; background: none; font-size: 15px; font-weight: 650; color: var(--ur-ink); }
.exp-urls p { margin: 0; }
.exp-urls code { font-family: var(--ur-mono); font-size: 12.5px; overflow-wrap: anywhere; }
.exp-urls a { color: var(--ur-accent-hover); }
.exp-urls a:hover { color: var(--ur-ink); }
.exp-urls :focus-visible { outline: 3px solid var(--ur-ring); outline-offset: 2px; }
.exp-urls .exp-sr { position: absolute; width: 1px; height: 1px; margin: -1px; padding: 0; overflow: hidden; clip: rect(0 0 0 0); white-space: nowrap; border: 0; }
.exp-urls .exp-muted { color: var(--ur-muted); }
.exp-urls ul.exp-plain { margin: 0; padding: 0; list-style: none; }

/* A page drawn without the admin's main card (the edit view) gets one of its own */
.exp-urls.exp-standalone { max-width: 980px; margin: 16px auto 24px; padding: 18px 20px; border-radius: 18px; background: var(--ur-card);
    box-shadow: 0 0 0 1px rgba(16, 24, 40, 0.06), 0 12px 32px -6px rgba(16, 24, 40, 0.12); }
#maincontent .exp-urls.exp-standalone { max-width: none; margin: 0; padding: 0; border-radius: 0; background: transparent; box-shadow: none; }

.exp-urls .exp-title-row { display: flex; flex-wrap: wrap; align-items: center; gap: 6px 12px; }
.exp-urls .exp-title-key { color: var(--ur-muted); }
.exp-urls .exp-intro { margin: 8px 0 18px; max-width: 78ch; color: var(--ur-muted); }
.exp-urls .exp-section { margin: 0 0 24px; }
.exp-urls .exp-section-head { display: flex; flex-wrap: wrap; align-items: baseline; gap: 4px 12px; margin: 0 0 10px; }
.exp-urls .exp-section-head p { flex: 1 1 100%; color: var(--ur-muted); max-width: 78ch; }

/* Messages */
.exp-urls .exp-feedback { margin: 0 0 14px; padding: 10px 14px; border: 1px solid; border-left-width: 4px; border-radius: 10px; }
.exp-urls .exp-feedback.is-ok { border-color: #b7dfc1; border-left-color: var(--ur-ok); background: var(--ur-ok-bg); color: var(--ur-ok); }
.exp-urls .exp-feedback.is-bad { border-color: #f1b4b4; border-left-color: var(--ur-bad); background: var(--ur-bad-bg); color: var(--ur-bad); }
.exp-urls .exp-feedback.is-warn { border-color: #f3d19c; border-left-color: var(--ur-warn); background: var(--ur-warn-bg); color: var(--ur-warn); }
.exp-urls .exp-feedback.is-info { border-color: #bcd0f5; border-left-color: var(--ur-info); background: var(--ur-info-bg); color: var(--ur-info); }
.exp-urls .exp-feedback strong { color: inherit; }
.exp-urls .exp-feedback p + p, .exp-urls .exp-feedback p + ul, .exp-urls .exp-feedback ul + p { margin-top: 6px; }
.exp-urls .exp-feedback ul { margin: 6px 0 0; padding-left: 20px; }
.exp-urls .exp-feedback h2.exp-h2 { color: inherit; }
.exp-urls .exp-reasons { margin-top: 8px; }
.exp-urls .exp-reasons li + li { margin-top: 4px; }

/* Overview figures */
.exp-urls .exp-figures { display: grid; grid-template-columns: repeat(auto-fit, minmax(min(100%, 120px), 1fr)); gap: 10px; margin: 0 0 20px; padding: 0; list-style: none; }
.exp-urls .exp-figure { display: flex; flex-direction: column; gap: 2px; margin: 0; padding: 12px 14px; border: 1px solid var(--ur-line); border-radius: var(--ur-radius); background: var(--ur-card); }
.exp-urls .exp-figure strong { font-size: 22px; line-height: 1.15; font-weight: 700; font-variant-numeric: tabular-nums; color: var(--ur-ink); }
.exp-urls .exp-figure span { font-size: 12.5px; color: var(--ur-muted); }
.exp-urls .exp-figure.is-attention strong { color: var(--ur-bad); }

/* Buttons */
.exp-urls .exp-btn {
    display: inline-flex; align-items: center; justify-content: center; gap: 6px; min-height: 36px; margin: 0;
    padding: 6px 14px; border: 1px solid #c9ced6; border-radius: 9px; background: #fff; color: var(--ur-ink);
    font: 600 13.5px/1.2 inherit; font-family: inherit; cursor: pointer; white-space: nowrap; text-decoration: none;
}
.exp-urls a.exp-btn { color: var(--ur-ink); }
.exp-urls .exp-btn:hover:not([disabled]):not(.is-disabled) { border-color: var(--ur-accent); color: var(--ur-accent-hover); }
.exp-urls .exp-btn[disabled], .exp-urls .exp-btn.is-disabled { opacity: .55; cursor: not-allowed; }
.exp-urls .exp-btn-primary, .exp-urls a.exp-btn-primary { border-color: var(--ur-accent); background: var(--ur-accent); color: #fff; }
.exp-urls .exp-btn-primary:hover:not([disabled]) { border-color: var(--ur-accent-hover); background: var(--ur-accent-hover); color: #fff; }
.exp-urls .exp-btn-danger { border-color: var(--ur-bad); background: var(--ur-bad); color: #fff; }
.exp-urls .exp-btn-danger:hover:not([disabled]) { border-color: #7f1d1d; background: #7f1d1d; color: #fff; }
.exp-urls .exp-btn-outline-danger { border-color: var(--ur-bad); color: var(--ur-bad); }
.exp-urls .exp-btn-outline-danger:hover:not([disabled]) { background: var(--ur-bad); border-color: var(--ur-bad); color: #fff; }
.exp-urls .exp-btn-small { min-height: 30px; padding: 4px 10px; font-size: 12.5px; }
.exp-urls .exp-btn svg { flex: 0 0 auto; }
.exp-urls .exp-btn { max-width: 100%; }
@media (max-width: 600px) { .exp-urls .exp-btn { white-space: normal; text-align: center; } }
.exp-urls .exp-actions { display: flex; flex-wrap: wrap; align-items: center; gap: 8px; }
.exp-urls .exp-actions form { display: contents; }
.exp-urls .exp-actionbar { display: flex; flex-wrap: wrap; align-items: center; justify-content: space-between; gap: 10px 16px;
    margin: 0 0 18px; padding: 12px 14px; border: 1px solid var(--ur-line); border-radius: var(--ur-radius); background: var(--ur-soft); }
.exp-urls .exp-actionbar .exp-meta { flex: 1 1 260px; }
.exp-urls .exp-meta { color: var(--ur-muted); font-size: 13px; }

/* Controls */
.exp-urls .exp-toolbar {
    display: grid; grid-template-columns: repeat(auto-fit, minmax(min(100%, 230px), 1fr)); gap: 14px 16px; align-items: end;
    margin: 0 0 20px; padding: 16px; border: 1px solid var(--ur-line); border-radius: var(--ur-radius); background: var(--ur-card);
}
.exp-urls .exp-field { display: flex; flex-direction: column; gap: 5px; min-width: 0; margin: 0; padding: 0; border: 0; }
.exp-urls fieldset.exp-field { background: none; box-shadow: none; border-radius: 0; }
.exp-urls fieldset.exp-field > legend { float: left; width: 100%; margin: 0 0 5px; padding: 0; background: none; }
.exp-urls fieldset.exp-field > legend + * { clear: both; }
.exp-urls .exp-field > label, .exp-urls .exp-field > legend { padding: 0; font-size: 12.5px; font-weight: 650; color: var(--ur-ink); }
.exp-urls .exp-field select,
.exp-urls .exp-field input[type="search"],
.exp-urls .exp-field input[type="text"] {
    width: 100%; min-height: 38px; margin: 0; padding: 6px 10px; border: 1px solid #8f96a3; border-radius: 9px;
    background: #fff; color: var(--ur-ink); font: inherit; font-size: 14px; box-shadow: none;
}
.exp-urls .exp-field select:focus, .exp-urls .exp-field input:focus { border-color: var(--ur-accent); outline: 3px solid var(--ur-ring); outline-offset: 0; }
.exp-urls .exp-field input[aria-invalid="true"] { border-color: var(--ur-bad); box-shadow: inset 0 0 0 1px var(--ur-bad); }
.exp-urls .exp-field-wide { grid-column: 1 / -1; }
.exp-urls .exp-form-fields { display: grid; grid-template-columns: minmax(0, 1fr); gap: 18px; max-width: 640px; }
.exp-urls .exp-help { font-size: 13px; color: var(--ur-muted); max-width: 72ch; }
.exp-urls .exp-field-error { font-size: 13px; font-weight: 650; color: var(--ur-bad); }
.exp-urls .exp-chips { display: flex; flex-wrap: wrap; gap: 6px; }
.exp-urls .exp-chip { position: relative; display: inline-flex; margin: 0; }
.exp-urls .exp-chip input { position: absolute; opacity: 0; width: 1px; height: 1px; }
.exp-urls .exp-chip span { display: inline-flex; align-items: center; min-height: 32px; padding: 4px 12px; border: 1px solid #c9ced6; border-radius: 999px; background: #fff; color: var(--ur-ink); font-size: 13px; font-weight: 400; cursor: pointer; }
.exp-urls .exp-chip input:checked + span { border-color: var(--ur-accent); background: var(--ur-accent); color: #fff; font-weight: 650; }
.exp-urls .exp-chip input:focus-visible + span { outline: 3px solid var(--ur-ring); outline-offset: 2px; }
.exp-urls .exp-filter-count { grid-column: 1 / -1; margin: 0; font-size: 13px; color: var(--ur-muted); }

/* The section cards */
.exp-urls .exp-cards { display: grid; grid-template-columns: minmax(0, 1fr); gap: 12px; margin: 0; padding: 0; list-style: none; }
.exp-urls .exp-card {
    min-width: 0; margin: 0; padding: 14px 16px; border: 1px solid var(--ur-line); border-radius: var(--ur-radius); background: var(--ur-card);
    box-shadow: 0 1px 2px rgba(16, 24, 40, 0.04);
}
.exp-urls .exp-card.is-attention { box-shadow: inset 4px 0 0 var(--ur-warn), 0 1px 2px rgba(16, 24, 40, 0.04); }
.exp-urls .exp-card.is-selected { border-color: var(--ur-accent); }
.exp-urls .exp-card-head { display: flex; flex-wrap: wrap; align-items: flex-start; justify-content: space-between; gap: 10px 16px; }
.exp-urls .exp-card-title { display: flex; flex-wrap: wrap; align-items: center; gap: 6px 10px; min-width: 0; flex: 1 1 320px; }
.exp-urls .exp-card-title h3 a { color: var(--ur-ink); text-decoration: none; }
.exp-urls .exp-card-title h3 a:hover { color: var(--ur-accent-hover); text-decoration: underline; }
.exp-urls .exp-select { display: inline-flex; align-items: center; justify-content: center; width: 32px; height: 32px; margin: -4px 0 -4px -6px; border-radius: 8px; cursor: pointer; }
.exp-urls .exp-select:hover { background: var(--ur-soft); }
.exp-urls .exp-select input { width: 18px; height: 18px; margin: 0; accent-color: var(--ur-accent); cursor: pointer; }
.exp-urls .exp-badges { display: inline-flex; flex-wrap: wrap; gap: 6px; margin: 0; padding: 0; list-style: none; }
.exp-urls .exp-badge { display: inline-flex; align-items: center; gap: 5px; margin: 0; padding: 2px 9px; border-radius: 999px; font-size: 12px; font-weight: 650; line-height: 1.5; white-space: nowrap; background: var(--ur-soft); color: var(--ur-muted); }
.exp-urls .exp-badge.is-ok { background: var(--ur-ok-bg); color: var(--ur-ok); }
.exp-urls .exp-badge.is-warn { background: var(--ur-warn-bg); color: var(--ur-warn); }
.exp-urls .exp-badge.is-bad { background: var(--ur-bad-bg); color: var(--ur-bad); }
.exp-urls .exp-badge.is-info { background: var(--ur-info-bg); color: var(--ur-info); }
.exp-urls .exp-facts { display: grid; grid-template-columns: repeat(auto-fit, minmax(min(100%, 165px), 1fr)); gap: 8px 18px; margin: 12px 0 0; }
.exp-urls .exp-facts > div { min-width: 0; }
.exp-urls .exp-facts dt { margin: 0; font-size: 12px; font-weight: 650; text-transform: uppercase; letter-spacing: .04em; color: var(--ur-muted); }
.exp-urls .exp-facts dd { margin: 2px 0 0; overflow-wrap: anywhere; }
.exp-urls .exp-facts dd code { color: var(--ur-muted); }
.exp-urls .exp-panel { margin: 0 0 18px; padding: 14px 16px; border: 1px solid var(--ur-line); border-radius: var(--ur-radius); background: var(--ur-card); }
.exp-urls .exp-panel > .exp-facts { margin-top: 0; }
.exp-urls .exp-fn { display: inline-block; margin: 0 4px 4px 0; padding: 1px 8px; border-radius: 6px; background: var(--ur-soft); color: var(--ur-ink); font: 12.5px/1.6 var(--ur-mono); white-space: nowrap; }
.exp-urls .exp-empty { margin: 0; padding: 18px 16px; border: 1px dashed #c9ced6; border-radius: var(--ur-radius); color: var(--ur-muted); text-align: center; }

/* Tables */
.exp-urls .exp-table-wrap { overflow-x: auto; border: 1px solid var(--ur-line); border-radius: var(--ur-radius); background: var(--ur-card); }
.exp-urls .exp-table { width: 100%; margin: 0; border-collapse: collapse; }
.exp-urls .exp-table th, .exp-urls .exp-table td { padding: 9px 12px; border: 0; border-bottom: 1px solid var(--ur-line); text-align: left; vertical-align: top; background: transparent; color: var(--ur-ink); }
.exp-urls .exp-table th { font-size: 12px; font-weight: 650; text-transform: uppercase; letter-spacing: .04em; color: var(--ur-muted); background: var(--ur-soft); white-space: nowrap; }
.exp-urls .exp-table tr:last-child td { border-bottom: 0; }
.exp-urls .exp-table .exp-num { text-align: right; font-variant-numeric: tabular-nums; white-space: nowrap; }
.exp-urls .exp-table img { vertical-align: -3px; }

/* Page size and pages */
.exp-urls .exp-listfoot { display: flex; flex-wrap: wrap; align-items: center; justify-content: space-between; gap: 10px 18px; margin: 14px 0 0; }
.exp-urls .exp-sizes { display: flex; flex-wrap: wrap; align-items: center; gap: 6px; margin: 0; font-size: 13px; color: var(--ur-muted); }
.exp-urls .exp-sizes a, .exp-urls .exp-sizes span.current { display: inline-flex; align-items: center; justify-content: center; min-width: 34px; min-height: 30px; padding: 2px 8px; border: 1px solid #c9ced6; border-radius: 8px; text-decoration: none; }
.exp-urls .exp-sizes span.current { border-color: var(--ur-accent); background: var(--ur-accent); color: #fff; font-weight: 650; }
.exp-urls .exp-pager { min-width: 0; }
.exp-urls .exp-pager .pagenavigator { margin: 0; }
.exp-urls .exp-pager a { color: var(--ur-accent-hover); }
.exp-urls .exp-pager span.text, .exp-urls .exp-pager span.text a { color: var(--ur-accent-hover); }
.exp-urls .exp-pager span.disabled, .exp-urls .exp-pager span.text.disabled { color: var(--ur-muted); }
.exp-urls .exp-pager span.current { color: var(--ur-ink); }

/* The buttons under the list */
.exp-urls .exp-bottombar { display: flex; flex-wrap: wrap; align-items: center; gap: 10px 12px; margin: 18px 0 0; padding: 12px 14px; border: 1px solid var(--ur-line); border-radius: var(--ur-radius); background: var(--ur-soft); }
.exp-urls .exp-bottombar .exp-meta { flex: 1 1 280px; }

/* Tabs of lists (all, valid, invalid ...) and orders: links, the current one marked */
.exp-urls .exp-tabs { display: flex; flex-wrap: wrap; gap: 6px; margin: 0; padding: 0; list-style: none; }
.exp-urls .exp-tabs a, .exp-urls .exp-tabs span.current { display: inline-flex; align-items: center; gap: 6px; min-height: 32px; padding: 4px 12px; border: 1px solid #c9ced6; border-radius: 999px; background: #fff; color: var(--ur-ink); font-size: 13px; text-decoration: none; }
.exp-urls .exp-tabs a:hover { border-color: var(--ur-accent); color: var(--ur-accent-hover); }
.exp-urls .exp-tabs span.current { border-color: var(--ur-accent); background: var(--ur-accent); color: #fff; font-weight: 650; }
.exp-urls .exp-tabs .exp-count { font-variant-numeric: tabular-nums; opacity: .9; }
.exp-urls .exp-figure a { color: inherit; text-decoration: none; }
.exp-urls .exp-figure a:hover span { text-decoration: underline; }
.exp-urls .exp-figure.is-current { border-color: var(--ur-accent); box-shadow: inset 0 0 0 1px var(--ur-accent); }
.exp-urls .exp-searchrow { display: flex; flex-wrap: wrap; gap: 8px; align-items: center; }
.exp-urls .exp-searchrow input[type="search"], .exp-urls .exp-searchrow input[type="text"] { flex: 1 1 220px; min-width: 0; }
.exp-urls .exp-field input[type="checkbox"] { width: 18px; height: 18px; margin: 0; accent-color: var(--ur-accent); }
.exp-urls .exp-check { display: flex; align-items: flex-start; gap: 8px; font-weight: 400; }
.exp-urls .exp-check input { flex: 0 0 auto; margin-top: 2px !important; }
.exp-urls .exp-check strong { font-weight: 650; }
.exp-urls .exp-check > span { flex: 1 1 auto; min-width: 0; }
.exp-urls .exp-check .exp-help { display: block; font-weight: 400; }
.exp-urls .exp-check, .exp-urls .exp-check * { white-space: normal; }

/* One URL, alias or wildcard */
.exp-urls .exp-card-addr { min-width: 0; font: 600 14px/1.45 var(--ur-mono); overflow-wrap: anywhere; word-break: break-word; }
.exp-urls .exp-card-addr a { color: var(--ur-ink); text-decoration: none; }
.exp-urls .exp-card-addr a:hover { color: var(--ur-accent-hover); text-decoration: underline; }
.exp-urls .exp-arrow { color: var(--ur-muted); font-family: var(--ur-mono); }
.exp-urls .exp-card.is-bad { box-shadow: inset 4px 0 0 var(--ur-bad), 0 1px 2px rgba(16, 24, 40, 0.04); }
.exp-urls .exp-usage { margin: 0; padding: 0; list-style: none; }
.exp-urls .exp-usage li { display: inline; }
.exp-urls .exp-usage li + li::before { content: ", "; color: var(--ur-muted); }

/* A confirmation that opens in place: works without javascript */
.exp-urls details.exp-confirm { flex: 1 1 100%; margin: 0; padding: 0; border: 1px solid var(--ur-line); border-radius: var(--ur-radius); background: var(--ur-card); }
.exp-urls details.exp-confirm > summary { display: inline-flex; align-items: center; gap: 6px; min-height: 36px; padding: 6px 14px; color: var(--ur-bad); font-weight: 650; cursor: pointer; list-style: none; }
.exp-urls details.exp-confirm > summary::-webkit-details-marker { display: none; }
.exp-urls details.exp-confirm > summary::before { content: "\25B8"; }
.exp-urls details.exp-confirm[open] > summary::before { content: "\25BE"; }
.exp-urls details.exp-confirm > div { display: flex; flex-wrap: wrap; align-items: center; gap: 10px 14px; padding: 0 14px 14px; }
.exp-urls details.exp-confirm > div p { flex: 1 1 320px; color: var(--ur-ink); }
.exp-urls .exp-bottombar details.exp-confirm { flex: 0 1 auto; }
.exp-urls .exp-bottombar details.exp-confirm[open] { flex: 1 1 100%; }

/* The wildcard tester's answer */
.exp-urls .exp-result { margin: 12px 0 0; }
.exp-urls .exp-result dl { display: grid; grid-template-columns: max-content minmax(0, 1fr); gap: 6px 14px; margin: 8px 0 0; }
.exp-urls .exp-result dt { font-weight: 650; }
.exp-urls .exp-result dd { margin: 0; overflow-wrap: anywhere; }
.exp-urls .exp-columns { display: grid; grid-template-columns: repeat(auto-fit, minmax(min(100%, 360px), 1fr)); gap: 18px; align-items: start; }
.exp-urls .exp-columns > .exp-panel { margin: 0; }

@media (max-width: 600px) {
    .exp-urls .exp-result dl { grid-template-columns: minmax(0, 1fr); gap: 2px; }
    .exp-urls .exp-result dd { margin-bottom: 6px; }
    .exp-urls.exp-standalone { margin: 8px; padding: 14px 12px; }
    .exp-urls .exp-card { padding: 12px; }
    .exp-urls .exp-card-head .exp-actions { width: 100%; }
    .exp-urls .exp-card-head .exp-actions .exp-btn { flex: 1 1 0; }
    .exp-urls .exp-actionbar .exp-actions, .exp-urls .exp-bottombar .exp-actions { width: 100%; }
    .exp-urls .exp-actionbar .exp-btn, .exp-urls .exp-bottombar .exp-btn { flex: 1 1 auto; }
    .exp-urls .exp-table th, .exp-urls .exp-table td { padding: 8px 9px; }
}
</style>
{/literal}
