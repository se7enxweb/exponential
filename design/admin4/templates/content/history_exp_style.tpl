{* The look of the versions page of an object (content/history), in the visual language of the redesigned
   administration pages (the link list, the cache and settings pages). Included once by content/history.tpl.

   The same file is in design/admin and design/admin4, so every administration design resolves it. Everything is
   scoped to .exp-history and takes admin4's tokens where they exist (--a4-*), with values of its own for the older
   designs; admin4's dark mode keeps the content card white, so the page needs no dark rules of its own. No shared
   stylesheet is changed. Guide: doc/guides/content-history.md *}
{literal}
<style>
.exp-history {
    --hi-ink: var(--a4-ink, #1f2430);
    --hi-muted: var(--a4-muted, #5d6573);
    --hi-line: var(--a4-line, #e3e6eb);
    --hi-soft: var(--a4-soft, #f6f7f9);
    --hi-card: #fff;
    --hi-accent: #c2410c;          /* white text on it is 5.2:1 */
    --hi-accent-hover: #9a3412;
    --hi-ring: rgba(194, 65, 12, 0.45);
    --hi-ok: #166534;   --hi-ok-bg: #e7f5ea;
    --hi-warn: #8a4b00; --hi-warn-bg: #fff3df;
    --hi-bad: #b91c1c;  --hi-bad-bg: #fdecec;
    --hi-info: #1e4fa8; --hi-info-bg: #e8effd;
    --hi-radius: 12px;
    --hi-mono: ui-monospace, SFMono-Regular, Menlo, Consolas, monospace;
    color: var(--hi-ink);
    font-size: 14px;
    line-height: 1.5;
}
.exp-history *, .exp-history *::before, .exp-history *::after { box-sizing: border-box; }
.exp-history [hidden] { display: none !important; }
.exp-history .box-content { padding-bottom: 20px; }
.exp-history h1.context-title { margin: 0; overflow-wrap: anywhere; }
.exp-history h2.exp-h2 { margin: 0; padding: 0; border: 0; background: none; font-size: 16px; font-weight: 650; color: var(--hi-ink); }
.exp-history h3 { overflow-wrap: anywhere; margin: 0; padding: 0; border: 0; background: none; font-size: 15px; font-weight: 650; color: var(--hi-ink); }
.exp-history p { margin: 0; }
.exp-history code { font-family: var(--hi-mono); font-size: 12.5px; overflow-wrap: anywhere; }
.exp-history a { color: var(--hi-accent-hover); }
.exp-history a:hover { color: var(--hi-ink); }
.exp-history :focus-visible { outline: 3px solid var(--hi-ring); outline-offset: 2px; }
.exp-history .exp-sr { position: absolute; width: 1px; height: 1px; margin: -1px; padding: 0; overflow: hidden; clip: rect(0 0 0 0); white-space: nowrap; border: 0; }
.exp-history .exp-muted { color: var(--hi-muted); }
.exp-history ul.exp-plain { margin: 0; padding: 0; list-style: none; }

/* A page drawn without the admin's main card (the edit view) gets one of its own */
.exp-history.exp-standalone { max-width: 980px; margin: 16px auto 24px; padding: 18px 20px; border-radius: 18px; background: var(--hi-card);
    box-shadow: 0 0 0 1px rgba(16, 24, 40, 0.06), 0 12px 32px -6px rgba(16, 24, 40, 0.12); }
#maincontent .exp-history.exp-standalone { max-width: none; margin: 0; padding: 0; border-radius: 0; background: transparent; box-shadow: none; }

.exp-history .exp-title-row { display: flex; flex-wrap: wrap; align-items: center; gap: 6px 12px; }
.exp-history .exp-title-key { color: var(--hi-muted); }
.exp-history .exp-intro { margin: 8px 0 18px; max-width: 78ch; color: var(--hi-muted); }
.exp-history .exp-section { margin: 0 0 24px; }
.exp-history .exp-section-head { display: flex; flex-wrap: wrap; align-items: baseline; gap: 4px 12px; margin: 0 0 10px; }
.exp-history .exp-section-head p { flex: 1 1 100%; color: var(--hi-muted); max-width: 78ch; }

/* Messages */
.exp-history .exp-feedback { margin: 0 0 14px; padding: 10px 14px; border: 1px solid; border-left-width: 4px; border-radius: 10px; }
.exp-history .exp-feedback.is-ok { border-color: #b7dfc1; border-left-color: var(--hi-ok); background: var(--hi-ok-bg); color: var(--hi-ok); }
.exp-history .exp-feedback.is-bad { border-color: #f1b4b4; border-left-color: var(--hi-bad); background: var(--hi-bad-bg); color: var(--hi-bad); }
.exp-history .exp-feedback.is-warn { border-color: #f3d19c; border-left-color: var(--hi-warn); background: var(--hi-warn-bg); color: var(--hi-warn); }
.exp-history .exp-feedback.is-info { border-color: #bcd0f5; border-left-color: var(--hi-info); background: var(--hi-info-bg); color: var(--hi-info); }
.exp-history .exp-feedback strong { color: inherit; }
.exp-history .exp-feedback p + p, .exp-history .exp-feedback p + ul, .exp-history .exp-feedback ul + p { margin-top: 6px; }
.exp-history .exp-feedback ul { margin: 6px 0 0; padding-left: 20px; }
.exp-history .exp-feedback h2.exp-h2 { color: inherit; }
.exp-history .exp-reasons { margin-top: 8px; }
.exp-history .exp-reasons li + li { margin-top: 4px; }

/* Overview figures */
.exp-history .exp-figures { display: grid; grid-template-columns: repeat(auto-fit, minmax(min(100%, 120px), 1fr)); gap: 10px; margin: 0 0 20px; padding: 0; list-style: none; }
.exp-history .exp-figure { display: flex; flex-direction: column; gap: 2px; margin: 0; padding: 12px 14px; border: 1px solid var(--hi-line); border-radius: var(--hi-radius); background: var(--hi-card); }
.exp-history .exp-figure strong { font-size: 22px; line-height: 1.15; font-weight: 700; font-variant-numeric: tabular-nums; color: var(--hi-ink); }
.exp-history .exp-figure span { font-size: 12.5px; color: var(--hi-muted); }
.exp-history .exp-figure.is-attention strong { color: var(--hi-bad); }

/* Buttons */
.exp-history .exp-btn {
    display: inline-flex; align-items: center; justify-content: center; gap: 6px; min-height: 36px; margin: 0;
    padding: 6px 14px; border: 1px solid #c9ced6; border-radius: 9px; background: #fff; color: var(--hi-ink);
    font: 600 13.5px/1.2 inherit; font-family: inherit; cursor: pointer; white-space: nowrap; text-decoration: none;
}
.exp-history a.exp-btn { color: var(--hi-ink); }
.exp-history .exp-btn:hover:not([disabled]):not(.is-disabled) { border-color: var(--hi-accent); color: var(--hi-accent-hover); }
.exp-history .exp-btn[disabled], .exp-history .exp-btn.is-disabled { opacity: .55; cursor: not-allowed; }
.exp-history .exp-btn-primary, .exp-history a.exp-btn-primary { border-color: var(--hi-accent); background: var(--hi-accent); color: #fff; }
.exp-history .exp-btn-primary:hover:not([disabled]) { border-color: var(--hi-accent-hover); background: var(--hi-accent-hover); color: #fff; }
.exp-history .exp-btn-danger { border-color: var(--hi-bad); background: var(--hi-bad); color: #fff; }
.exp-history .exp-btn-danger:hover:not([disabled]) { border-color: #7f1d1d; background: #7f1d1d; color: #fff; }
.exp-history .exp-btn-outline-danger { border-color: var(--hi-bad); color: var(--hi-bad); }
.exp-history .exp-btn-outline-danger:hover:not([disabled]) { background: var(--hi-bad); border-color: var(--hi-bad); color: #fff; }
.exp-history .exp-btn-small { min-height: 30px; padding: 4px 10px; font-size: 12.5px; }
.exp-history .exp-btn svg { flex: 0 0 auto; }
.exp-history .exp-btn { max-width: 100%; }
@media (max-width: 600px) { .exp-history .exp-btn { white-space: normal; text-align: center; } }
.exp-history .exp-actions { display: flex; flex-wrap: wrap; align-items: center; gap: 8px; }
.exp-history .exp-actions form { display: contents; }
.exp-history .exp-actionbar { display: flex; flex-wrap: wrap; align-items: center; justify-content: space-between; gap: 10px 16px;
    margin: 0 0 18px; padding: 12px 14px; border: 1px solid var(--hi-line); border-radius: var(--hi-radius); background: var(--hi-soft); }
.exp-history .exp-actionbar .exp-meta { flex: 1 1 260px; }
.exp-history .exp-meta { color: var(--hi-muted); font-size: 13px; }

/* Controls */
.exp-history .exp-toolbar {
    display: grid; grid-template-columns: repeat(auto-fit, minmax(min(100%, 230px), 1fr)); gap: 14px 16px; align-items: end;
    margin: 0 0 20px; padding: 16px; border: 1px solid var(--hi-line); border-radius: var(--hi-radius); background: var(--hi-card);
}
.exp-history .exp-field { display: flex; flex-direction: column; gap: 5px; min-width: 0; margin: 0; padding: 0; border: 0; }
.exp-history fieldset.exp-field { background: none; box-shadow: none; border-radius: 0; }
.exp-history fieldset.exp-field > legend { float: left; width: 100%; margin: 0 0 5px; padding: 0; background: none; }
.exp-history fieldset.exp-field > legend + * { clear: both; }
.exp-history .exp-field > label, .exp-history .exp-field > legend { padding: 0; font-size: 12.5px; font-weight: 650; color: var(--hi-ink); }
.exp-history .exp-field select,
.exp-history .exp-field input[type="search"],
.exp-history .exp-field input[type="text"] {
    width: 100%; min-height: 38px; margin: 0; padding: 6px 10px; border: 1px solid #8f96a3; border-radius: 9px;
    background: #fff; color: var(--hi-ink); font: inherit; font-size: 14px; box-shadow: none;
}
.exp-history .exp-field select:focus, .exp-history .exp-field input:focus { border-color: var(--hi-accent); outline: 3px solid var(--hi-ring); outline-offset: 0; }
.exp-history .exp-field input[aria-invalid="true"] { border-color: var(--hi-bad); box-shadow: inset 0 0 0 1px var(--hi-bad); }
.exp-history .exp-field-wide { grid-column: 1 / -1; }
.exp-history .exp-form-fields { display: grid; grid-template-columns: minmax(0, 1fr); gap: 18px; max-width: 640px; }
.exp-history .exp-help { font-size: 13px; color: var(--hi-muted); max-width: 72ch; }
.exp-history .exp-field-error { font-size: 13px; font-weight: 650; color: var(--hi-bad); }
.exp-history .exp-chips { display: flex; flex-wrap: wrap; gap: 6px; }
.exp-history .exp-chip { position: relative; display: inline-flex; margin: 0; }
.exp-history .exp-chip input { position: absolute; opacity: 0; width: 1px; height: 1px; }
.exp-history .exp-chip span { display: inline-flex; align-items: center; min-height: 32px; padding: 4px 12px; border: 1px solid #c9ced6; border-radius: 999px; background: #fff; color: var(--hi-ink); font-size: 13px; font-weight: 400; cursor: pointer; }
.exp-history .exp-chip input:checked + span { border-color: var(--hi-accent); background: var(--hi-accent); color: #fff; font-weight: 650; }
.exp-history .exp-chip input:focus-visible + span { outline: 3px solid var(--hi-ring); outline-offset: 2px; }
.exp-history .exp-filter-count { grid-column: 1 / -1; margin: 0; font-size: 13px; color: var(--hi-muted); }

/* The section cards */
.exp-history .exp-cards { display: grid; grid-template-columns: minmax(0, 1fr); gap: 12px; margin: 0; padding: 0; list-style: none; }
.exp-history .exp-card {
    min-width: 0; margin: 0; padding: 14px 16px; border: 1px solid var(--hi-line); border-radius: var(--hi-radius); background: var(--hi-card);
    box-shadow: 0 1px 2px rgba(16, 24, 40, 0.04);
}
.exp-history .exp-card.is-attention { box-shadow: inset 4px 0 0 var(--hi-warn), 0 1px 2px rgba(16, 24, 40, 0.04); }
.exp-history .exp-card.is-selected { border-color: var(--hi-accent); }
.exp-history .exp-card-head { display: flex; flex-wrap: wrap; align-items: flex-start; justify-content: space-between; gap: 10px 16px; }
.exp-history .exp-card-title { display: flex; flex-wrap: wrap; align-items: center; gap: 6px 10px; min-width: 0; flex: 1 1 320px; }
.exp-history .exp-card-title h3 a { color: var(--hi-ink); text-decoration: none; }
.exp-history .exp-card-title h3 a:hover { color: var(--hi-accent-hover); text-decoration: underline; }
.exp-history .exp-select { display: inline-flex; align-items: center; justify-content: center; width: 32px; height: 32px; margin: -4px 0 -4px -6px; border-radius: 8px; cursor: pointer; }
.exp-history .exp-select:hover { background: var(--hi-soft); }
.exp-history .exp-select input { width: 18px; height: 18px; margin: 0; accent-color: var(--hi-accent); cursor: pointer; }
.exp-history .exp-badges { display: inline-flex; flex-wrap: wrap; gap: 6px; margin: 0; padding: 0; list-style: none; }
.exp-history .exp-badge { display: inline-flex; align-items: center; gap: 5px; margin: 0; padding: 2px 9px; border-radius: 999px; font-size: 12px; font-weight: 650; line-height: 1.5; white-space: nowrap; background: var(--hi-soft); color: var(--hi-muted); }
.exp-history .exp-badge.is-ok { background: var(--hi-ok-bg); color: var(--hi-ok); }
.exp-history .exp-badge.is-warn { background: var(--hi-warn-bg); color: var(--hi-warn); }
.exp-history .exp-badge.is-bad { background: var(--hi-bad-bg); color: var(--hi-bad); }
.exp-history .exp-badge.is-info { background: var(--hi-info-bg); color: var(--hi-info); }
.exp-history .exp-facts { display: grid; grid-template-columns: repeat(auto-fit, minmax(min(100%, 165px), 1fr)); gap: 8px 18px; margin: 12px 0 0; }
.exp-history .exp-facts > div { min-width: 0; }
.exp-history .exp-facts dt { margin: 0; font-size: 12px; font-weight: 650; text-transform: uppercase; letter-spacing: .04em; color: var(--hi-muted); }
.exp-history .exp-facts dd { margin: 2px 0 0; overflow-wrap: anywhere; }
.exp-history .exp-facts dd code { color: var(--hi-muted); }
.exp-history .exp-panel { margin: 0 0 18px; padding: 14px 16px; border: 1px solid var(--hi-line); border-radius: var(--hi-radius); background: var(--hi-card); }
.exp-history .exp-panel > .exp-facts { margin-top: 0; }
.exp-history .exp-fn { display: inline-block; margin: 0 4px 4px 0; padding: 1px 8px; border-radius: 6px; background: var(--hi-soft); color: var(--hi-ink); font: 12.5px/1.6 var(--hi-mono); white-space: nowrap; }
.exp-history .exp-empty { margin: 0; padding: 18px 16px; border: 1px dashed #c9ced6; border-radius: var(--hi-radius); color: var(--hi-muted); text-align: center; }

/* Tables */
.exp-history .exp-table-wrap { overflow-x: auto; border: 1px solid var(--hi-line); border-radius: var(--hi-radius); background: var(--hi-card); }
.exp-history .exp-table { width: 100%; margin: 0; border-collapse: collapse; }
.exp-history .exp-table th, .exp-history .exp-table td { padding: 9px 12px; border: 0; border-bottom: 1px solid var(--hi-line); text-align: left; vertical-align: top; background: transparent; color: var(--hi-ink); }
.exp-history .exp-table th { font-size: 12px; font-weight: 650; text-transform: uppercase; letter-spacing: .04em; color: var(--hi-muted); background: var(--hi-soft); white-space: nowrap; }
.exp-history .exp-table tr:last-child td { border-bottom: 0; }
.exp-history .exp-table .exp-num { text-align: right; font-variant-numeric: tabular-nums; white-space: nowrap; }
.exp-history .exp-table img { vertical-align: -3px; }

/* Page size and pages */
.exp-history .exp-listfoot { display: flex; flex-wrap: wrap; align-items: center; justify-content: space-between; gap: 10px 18px; margin: 14px 0 0; }
.exp-history .exp-sizes { display: flex; flex-wrap: wrap; align-items: center; gap: 6px; margin: 0; font-size: 13px; color: var(--hi-muted); }
.exp-history .exp-sizes a, .exp-history .exp-sizes span.current { display: inline-flex; align-items: center; justify-content: center; min-width: 34px; min-height: 30px; padding: 2px 8px; border: 1px solid #c9ced6; border-radius: 8px; text-decoration: none; }
.exp-history .exp-sizes span.current { border-color: var(--hi-accent); background: var(--hi-accent); color: #fff; font-weight: 650; }
.exp-history .exp-pager { min-width: 0; }
.exp-history .exp-pager .pagenavigator { margin: 0; }
.exp-history .exp-pager a { color: var(--hi-accent-hover); }
.exp-history .exp-pager span.text, .exp-history .exp-pager span.text a { color: var(--hi-accent-hover); }
.exp-history .exp-pager span.disabled, .exp-history .exp-pager span.text.disabled { color: var(--hi-muted); }
.exp-history .exp-pager span.current { color: var(--hi-ink); }

/* The buttons under the list */
.exp-history .exp-bottombar { display: flex; flex-wrap: wrap; align-items: center; gap: 10px 12px; margin: 18px 0 0; padding: 12px 14px; border: 1px solid var(--hi-line); border-radius: var(--hi-radius); background: var(--hi-soft); }
.exp-history .exp-bottombar .exp-meta { flex: 1 1 280px; }

/* Tabs of lists (all, valid, invalid ...) and orders: links, the current one marked */
.exp-history .exp-tabs { display: flex; flex-wrap: wrap; gap: 6px; margin: 0; padding: 0; list-style: none; }
.exp-history .exp-tabs a, .exp-history .exp-tabs span.current { display: inline-flex; align-items: center; gap: 6px; min-height: 32px; padding: 4px 12px; border: 1px solid #c9ced6; border-radius: 999px; background: #fff; color: var(--hi-ink); font-size: 13px; text-decoration: none; }
.exp-history .exp-tabs a:hover { border-color: var(--hi-accent); color: var(--hi-accent-hover); }
.exp-history .exp-tabs span.current { border-color: var(--hi-accent); background: var(--hi-accent); color: #fff; font-weight: 650; }
.exp-history .exp-tabs .exp-count { font-variant-numeric: tabular-nums; opacity: .9; }
.exp-history .exp-figure a { color: inherit; text-decoration: none; }
.exp-history .exp-figure a:hover span { text-decoration: underline; }
.exp-history .exp-figure.is-current { border-color: var(--hi-accent); box-shadow: inset 0 0 0 1px var(--hi-accent); }
.exp-history .exp-searchrow { display: flex; flex-wrap: wrap; gap: 8px; align-items: center; }
.exp-history .exp-searchrow input[type="search"], .exp-history .exp-searchrow input[type="text"] { flex: 1 1 220px; min-width: 0; }
.exp-history .exp-field input[type="checkbox"] { width: 18px; height: 18px; margin: 0; accent-color: var(--hi-accent); }
.exp-history .exp-check { display: flex; align-items: flex-start; gap: 8px; font-weight: 400; }
.exp-history .exp-check input { flex: 0 0 auto; margin-top: 2px !important; }
.exp-history .exp-check strong { font-weight: 650; }
.exp-history .exp-check > span { flex: 1 1 auto; min-width: 0; }
.exp-history .exp-check .exp-help { display: block; font-weight: 400; }
.exp-history .exp-check, .exp-history .exp-check * { white-space: normal; }

/* One URL, alias or wildcard */
.exp-history .exp-card-addr { min-width: 0; font: 600 14px/1.45 var(--hi-mono); overflow-wrap: anywhere; word-break: break-word; }
.exp-history .exp-card-addr a { color: var(--hi-ink); text-decoration: none; }
.exp-history .exp-card-addr a:hover { color: var(--hi-accent-hover); text-decoration: underline; }
.exp-history .exp-arrow { color: var(--hi-muted); font-family: var(--hi-mono); }
.exp-history .exp-card.is-bad { box-shadow: inset 4px 0 0 var(--hi-bad), 0 1px 2px rgba(16, 24, 40, 0.04); }
.exp-history .exp-usage { margin: 0; padding: 0; list-style: none; }
.exp-history .exp-usage li { display: inline; }
.exp-history .exp-usage li + li::before { content: ", "; color: var(--hi-muted); }

/* A confirmation that opens in place: works without javascript */
.exp-history details.exp-confirm { flex: 1 1 100%; margin: 0; padding: 0; border: 1px solid var(--hi-line); border-radius: var(--hi-radius); background: var(--hi-card); }
.exp-history details.exp-confirm > summary { display: inline-flex; align-items: center; gap: 6px; min-height: 36px; padding: 6px 14px; color: var(--hi-bad); font-weight: 650; cursor: pointer; list-style: none; }
.exp-history details.exp-confirm > summary::-webkit-details-marker { display: none; }
.exp-history details.exp-confirm > summary::before { content: "\25B8"; }
.exp-history details.exp-confirm[open] > summary::before { content: "\25BE"; }
.exp-history details.exp-confirm > div { display: flex; flex-wrap: wrap; align-items: center; gap: 10px 14px; padding: 0 14px 14px; }
.exp-history details.exp-confirm > div p { flex: 1 1 320px; color: var(--hi-ink); }
.exp-history .exp-bottombar details.exp-confirm { flex: 0 1 auto; }
.exp-history .exp-bottombar details.exp-confirm[open] { flex: 1 1 100%; }

/* The wildcard tester's answer */
.exp-history .exp-result { margin: 12px 0 0; }
.exp-history .exp-result dl { display: grid; grid-template-columns: max-content minmax(0, 1fr); gap: 6px 14px; margin: 8px 0 0; }
.exp-history .exp-result dt { font-weight: 650; }
.exp-history .exp-result dd { margin: 0; overflow-wrap: anywhere; }
.exp-history .exp-columns { display: grid; grid-template-columns: repeat(auto-fit, minmax(min(100%, 360px), 1fr)); gap: 18px; align-items: start; }
.exp-history .exp-columns > .exp-panel { margin: 0; }

@media (max-width: 600px) {
    .exp-history .exp-result dl { grid-template-columns: minmax(0, 1fr); gap: 2px; }
    .exp-history .exp-result dd { margin-bottom: 6px; }
    .exp-history.exp-standalone { margin: 8px; padding: 14px 12px; }
    .exp-history .exp-card { padding: 12px; }
    .exp-history .exp-card-head .exp-actions { width: 100%; }
    .exp-history .exp-card-head .exp-actions .exp-btn { flex: 1 1 0; }
    .exp-history .exp-actionbar .exp-actions, .exp-history .exp-bottombar .exp-actions { width: 100%; }
    .exp-history .exp-actionbar .exp-btn, .exp-history .exp-bottombar .exp-btn { flex: 1 1 auto; }
    .exp-history .exp-table th, .exp-history .exp-table td { padding: 8px 9px; }
}

/* The versions page */
.exp-history .exp-field-label { font-size: 12.5px; color: var(--hi-ink); }
.exp-history .exp-card.is-current { box-shadow: inset 4px 0 0 var(--hi-ok), 0 1px 2px rgba(16, 24, 40, 0.04); }
.exp-history .exp-card-title h3 { font-size: 15px; }
.exp-history .exp-facts img { vertical-align: -1px; }
.exp-history .exp-copy { display: flex; flex-wrap: wrap; align-items: center; gap: 8px 10px; margin: 12px 0 0; padding: 10px 12px; border-radius: 10px; background: var(--hi-soft); }
.exp-history .exp-copy label { font-size: 13px; font-weight: 650; color: var(--hi-ink); }
.exp-history .exp-copy select { min-height: 32px; max-width: 100%; padding: 4px 8px; border: 1px solid #8f96a3; border-radius: 8px; background: #fff; color: var(--hi-ink); font: inherit; font-size: 13.5px; }
.exp-history .exp-copy-language { font-size: 13.5px; }
.exp-history .exp-why { margin: 10px 0 0; padding: 0; list-style: none; font-size: 13px; color: var(--hi-muted); }
.exp-history .exp-why li { position: relative; padding-left: 16px; }
.exp-history .exp-why li::before { content: "\2013"; position: absolute; left: 2px; }
.exp-history .exp-why-page { flex: 1 1 100%; margin: 2px 0 0; }
.exp-history .exp-compare { display: grid; grid-template-columns: repeat(auto-fit, minmax(min(100%, 180px), 1fr)); gap: 12px 14px; align-items: end; margin-top: 12px; }
.exp-history .exp-panel > .exp-h2 + .exp-help { margin-top: 4px; }
.exp-history button.exp-tab { display: inline-flex; align-items: center; min-height: 32px; padding: 4px 12px; border: 1px solid #c9ced6; border-radius: 999px; background: #fff; color: var(--hi-ink); font: inherit; font-size: 13px; cursor: pointer; }
.exp-history button.exp-tab:hover { border-color: var(--hi-accent); color: var(--hi-accent-hover); }
.exp-history button.exp-tab.is-current { border-color: var(--hi-accent); background: var(--hi-accent); color: #fff; font-weight: 650; }
.exp-history .exp-diff .exp-tabs { margin: 0 0 12px; }
.exp-history .exp-diff .exp-card h3 { margin-bottom: 6px; }
.exp-history .attribute-view-diff { overflow-wrap: anywhere; }
.exp-history .attribute-view-diff .block { margin: 0; }
/* the changes, in colours that read on white (the admin's own are lighter) */
.exp-history #diffview ins { color: var(--hi-ok); border-bottom: 1px solid var(--hi-ok); text-decoration: none; }
.exp-history #diffview del { color: var(--hi-bad); text-decoration: line-through; }
.exp-history #diffview.blockchanges ins, .exp-history #diffview.blockchanges del { display: block; padding-left: .5em; color: var(--hi-ink); text-decoration: none; border-bottom: 0; }
.exp-history #diffview.blockchanges ins { border-left: .5em solid var(--hi-ok); background: var(--hi-ok-bg); }
.exp-history #diffview.blockchanges del { border-left: .5em solid var(--hi-bad); background: var(--hi-bad-bg); }
.exp-history #diffview.previous ins { display: none; }
.exp-history #diffview.previous del { color: var(--hi-ink); background: #fff3a8; text-decoration: none; }
.exp-history #diffview.latest del { display: none; }
.exp-history #diffview.latest ins { color: var(--hi-ink); background: #fff3a8; border-bottom: 0; }
.exp-history .exp-bottombar .exp-check { flex: 1 1 100%; }
@media (max-width: 600px) {
    .exp-history .exp-copy { flex-direction: column; align-items: stretch; }
    .exp-history .exp-copy .exp-btn { width: 100%; }
}
</style>
{/literal}
