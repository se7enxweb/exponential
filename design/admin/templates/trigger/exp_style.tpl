{* The look of the trigger list (trigger/list), in the visual language of the section and cronjobs pages. Included once by
   each page that uses it.

   The same file is in design/admin and design/admin4, so every administration design resolves it. Everything is
   scoped to .exp-lists and takes admin4's tokens where they exist (--a4-*), with values of its own for the older
   designs. The RSS list, the trigger list and the workflow and class group lists share these rules; each module
   keeps its own copy so that none of them depends on a stylesheet the others may change. *}
{literal}
<style>
.exp-lists {
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
.exp-lists *, .exp-lists *::before, .exp-lists *::after { box-sizing: border-box; }
.exp-lists [hidden] { display: none !important; }
.exp-lists .box-content { padding-bottom: 20px; }
.exp-lists h1.context-title { margin: 0; overflow-wrap: anywhere; }
.exp-lists h2.exp-h2 { margin: 0; padding: 0; border: 0; background: none; font-size: 16px; font-weight: 650; color: var(--sc-ink); }
.exp-lists h3 { overflow-wrap: anywhere; margin: 0; padding: 0; border: 0; background: none; font-size: 15px; font-weight: 650; color: var(--sc-ink); }
.exp-lists p { margin: 0; }
.exp-lists code { font-family: var(--sc-mono); font-size: 12.5px; overflow-wrap: anywhere; }
.exp-lists a { color: var(--sc-accent-hover); }
.exp-lists a:hover { color: var(--sc-ink); }
.exp-lists :focus-visible { outline: 3px solid var(--sc-ring); outline-offset: 2px; }
.exp-lists .exp-sr { position: absolute; width: 1px; height: 1px; margin: -1px; padding: 0; overflow: hidden; clip: rect(0 0 0 0); white-space: nowrap; border: 0; }
.exp-lists .exp-muted { color: var(--sc-muted); }
.exp-lists ul.exp-plain { margin: 0; padding: 0; list-style: none; }

/* A page drawn without the admin's main card (the edit view) gets one of its own */
.exp-lists.exp-standalone { max-width: 980px; margin: 16px auto 24px; padding: 18px 20px; border-radius: 18px; background: var(--sc-card);
    box-shadow: 0 0 0 1px rgba(16, 24, 40, 0.06), 0 12px 32px -6px rgba(16, 24, 40, 0.12); }
#maincontent .exp-lists.exp-standalone { max-width: none; margin: 0; padding: 0; border-radius: 0; background: transparent; box-shadow: none; }

.exp-lists .exp-title-row { display: flex; flex-wrap: wrap; align-items: center; gap: 6px 12px; }
.exp-lists .exp-title-key { color: var(--sc-muted); }
.exp-lists .exp-intro { margin: 8px 0 18px; max-width: 78ch; color: var(--sc-muted); }
.exp-lists .exp-section { margin: 0 0 24px; }
.exp-lists .exp-section-head { display: flex; flex-wrap: wrap; align-items: baseline; gap: 4px 12px; margin: 0 0 10px; }
.exp-lists .exp-section-head p { flex: 1 1 100%; color: var(--sc-muted); max-width: 78ch; }

/* Messages */
.exp-lists .exp-feedback { margin: 0 0 14px; padding: 10px 14px; border: 1px solid; border-left-width: 4px; border-radius: 10px; }
.exp-lists .exp-feedback.is-ok { border-color: #b7dfc1; border-left-color: var(--sc-ok); background: var(--sc-ok-bg); color: var(--sc-ok); }
.exp-lists .exp-feedback.is-bad { border-color: #f1b4b4; border-left-color: var(--sc-bad); background: var(--sc-bad-bg); color: var(--sc-bad); }
.exp-lists .exp-feedback.is-warn { border-color: #f3d19c; border-left-color: var(--sc-warn); background: var(--sc-warn-bg); color: var(--sc-warn); }
.exp-lists .exp-feedback.is-info { border-color: #bcd0f5; border-left-color: var(--sc-info); background: var(--sc-info-bg); color: var(--sc-info); }
.exp-lists .exp-feedback strong { color: inherit; }
.exp-lists .exp-feedback p + p, .exp-lists .exp-feedback p + ul, .exp-lists .exp-feedback ul + p { margin-top: 6px; }
.exp-lists .exp-feedback ul { margin: 6px 0 0; padding-left: 20px; }
.exp-lists .exp-feedback h2.exp-h2 { color: inherit; }
.exp-lists .exp-reasons { margin-top: 8px; }
.exp-lists .exp-reasons li + li { margin-top: 4px; }

/* Overview figures */
.exp-lists .exp-figures { display: grid; grid-template-columns: repeat(auto-fit, minmax(min(100%, 120px), 1fr)); gap: 10px; margin: 0 0 20px; padding: 0; list-style: none; }
.exp-lists .exp-figure { display: flex; flex-direction: column; gap: 2px; margin: 0; padding: 12px 14px; border: 1px solid var(--sc-line); border-radius: var(--sc-radius); background: var(--sc-card); }
.exp-lists .exp-figure strong { font-size: 22px; line-height: 1.15; font-weight: 700; font-variant-numeric: tabular-nums; color: var(--sc-ink); }
.exp-lists .exp-figure span { font-size: 12.5px; color: var(--sc-muted); }
.exp-lists .exp-figure.is-attention strong { color: var(--sc-bad); }

/* Buttons */
.exp-lists .exp-btn {
    display: inline-flex; align-items: center; justify-content: center; gap: 6px; min-height: 36px; margin: 0;
    padding: 6px 14px; border: 1px solid #c9ced6; border-radius: 9px; background: #fff; color: var(--sc-ink);
    font: 600 13.5px/1.2 inherit; font-family: inherit; cursor: pointer; white-space: nowrap; text-decoration: none;
}
.exp-lists a.exp-btn { color: var(--sc-ink); }
.exp-lists .exp-btn:hover:not([disabled]):not(.is-disabled) { border-color: var(--sc-accent); color: var(--sc-accent-hover); }
.exp-lists .exp-btn[disabled], .exp-lists .exp-btn.is-disabled { opacity: .55; cursor: not-allowed; }
.exp-lists .exp-btn-primary, .exp-lists a.exp-btn-primary { border-color: var(--sc-accent); background: var(--sc-accent); color: #fff; }
.exp-lists .exp-btn-primary:hover:not([disabled]) { border-color: var(--sc-accent-hover); background: var(--sc-accent-hover); color: #fff; }
.exp-lists .exp-btn-danger { border-color: var(--sc-bad); background: var(--sc-bad); color: #fff; }
.exp-lists .exp-btn-danger:hover:not([disabled]) { border-color: #7f1d1d; background: #7f1d1d; color: #fff; }
.exp-lists .exp-btn-outline-danger { border-color: var(--sc-bad); color: var(--sc-bad); }
.exp-lists .exp-btn-outline-danger:hover:not([disabled]) { background: var(--sc-bad); border-color: var(--sc-bad); color: #fff; }
.exp-lists .exp-btn-small { min-height: 30px; padding: 4px 10px; font-size: 12.5px; }
.exp-lists .exp-btn svg { flex: 0 0 auto; }
.exp-lists .exp-btn { max-width: 100%; }
@media (max-width: 600px) { .exp-lists .exp-btn { white-space: normal; text-align: center; } }
.exp-lists .exp-actions { display: flex; flex-wrap: wrap; align-items: center; gap: 8px; }
.exp-lists .exp-actions form { display: contents; }
.exp-lists .exp-actionbar { display: flex; flex-wrap: wrap; align-items: center; justify-content: space-between; gap: 10px 16px;
    margin: 0 0 18px; padding: 12px 14px; border: 1px solid var(--sc-line); border-radius: var(--sc-radius); background: var(--sc-soft); }
.exp-lists .exp-actionbar .exp-meta { flex: 1 1 260px; }
.exp-lists .exp-meta { color: var(--sc-muted); font-size: 13px; }

/* Controls */
.exp-lists .exp-toolbar {
    display: grid; grid-template-columns: repeat(auto-fit, minmax(min(100%, 230px), 1fr)); gap: 14px 16px; align-items: end;
    margin: 0 0 20px; padding: 16px; border: 1px solid var(--sc-line); border-radius: var(--sc-radius); background: var(--sc-card);
}
.exp-lists .exp-field { display: flex; flex-direction: column; gap: 5px; min-width: 0; margin: 0; padding: 0; border: 0; }
.exp-lists fieldset.exp-field { background: none; box-shadow: none; border-radius: 0; }
.exp-lists fieldset.exp-field > legend { float: left; width: 100%; margin: 0 0 5px; padding: 0; background: none; }
.exp-lists fieldset.exp-field > legend + * { clear: both; }
.exp-lists .exp-field > label, .exp-lists .exp-field > legend { padding: 0; font-size: 12.5px; font-weight: 650; color: var(--sc-ink); }
.exp-lists .exp-field select,
.exp-lists .exp-field input[type="search"],
.exp-lists .exp-field input[type="text"] {
    width: 100%; min-height: 38px; margin: 0; padding: 6px 10px; border: 1px solid #8f96a3; border-radius: 9px;
    background: #fff; color: var(--sc-ink); font: inherit; font-size: 14px; box-shadow: none;
}
.exp-lists .exp-field select:focus, .exp-lists .exp-field input:focus { border-color: var(--sc-accent); outline: 3px solid var(--sc-ring); outline-offset: 0; }
.exp-lists .exp-field input[aria-invalid="true"] { border-color: var(--sc-bad); box-shadow: inset 0 0 0 1px var(--sc-bad); }
.exp-lists .exp-field-wide { grid-column: 1 / -1; }
.exp-lists .exp-form-fields { display: grid; grid-template-columns: minmax(0, 1fr); gap: 18px; max-width: 640px; }
.exp-lists .exp-help { font-size: 13px; color: var(--sc-muted); max-width: 72ch; }
.exp-lists .exp-field-error { font-size: 13px; font-weight: 650; color: var(--sc-bad); }
.exp-lists .exp-chips { display: flex; flex-wrap: wrap; gap: 6px; }
.exp-lists .exp-chip { position: relative; display: inline-flex; margin: 0; }
.exp-lists .exp-chip input { position: absolute; opacity: 0; width: 1px; height: 1px; }
.exp-lists .exp-chip span { display: inline-flex; align-items: center; min-height: 32px; padding: 4px 12px; border: 1px solid #c9ced6; border-radius: 999px; background: #fff; color: var(--sc-ink); font-size: 13px; font-weight: 400; cursor: pointer; }
.exp-lists .exp-chip input:checked + span { border-color: var(--sc-accent); background: var(--sc-accent); color: #fff; font-weight: 650; }
.exp-lists .exp-chip input:focus-visible + span { outline: 3px solid var(--sc-ring); outline-offset: 2px; }
.exp-lists .exp-filter-count { grid-column: 1 / -1; margin: 0; font-size: 13px; color: var(--sc-muted); }

/* The section cards */
.exp-lists .exp-secs { display: grid; grid-template-columns: minmax(0, 1fr); gap: 12px; margin: 0; padding: 0; list-style: none; }
.exp-lists .exp-sec {
    min-width: 0; margin: 0; padding: 14px 16px; border: 1px solid var(--sc-line); border-radius: var(--sc-radius); background: var(--sc-card);
    box-shadow: 0 1px 2px rgba(16, 24, 40, 0.04);
}
.exp-lists .exp-sec.is-attention { box-shadow: inset 4px 0 0 var(--sc-warn), 0 1px 2px rgba(16, 24, 40, 0.04); }
.exp-lists .exp-sec.is-selected { border-color: var(--sc-accent); }
.exp-lists .exp-sec-head { display: flex; flex-wrap: wrap; align-items: flex-start; justify-content: space-between; gap: 10px 16px; }
.exp-lists .exp-sec-title { display: flex; flex-wrap: wrap; align-items: center; gap: 6px 10px; min-width: 0; flex: 1 1 320px; }
.exp-lists .exp-sec-title h3 a { color: var(--sc-ink); text-decoration: none; }
.exp-lists .exp-sec-title h3 a:hover { color: var(--sc-accent-hover); text-decoration: underline; }
.exp-lists .exp-select { display: inline-flex; align-items: center; justify-content: center; width: 32px; height: 32px; margin: -4px 0 -4px -6px; border-radius: 8px; cursor: pointer; }
.exp-lists .exp-select:hover { background: var(--sc-soft); }
.exp-lists .exp-select input { width: 18px; height: 18px; margin: 0; accent-color: var(--sc-accent); cursor: pointer; }
.exp-lists .exp-badges { display: inline-flex; flex-wrap: wrap; gap: 6px; margin: 0; padding: 0; list-style: none; }
.exp-lists .exp-badge { display: inline-flex; align-items: center; gap: 5px; margin: 0; padding: 2px 9px; border-radius: 999px; font-size: 12px; font-weight: 650; line-height: 1.5; white-space: nowrap; background: var(--sc-soft); color: var(--sc-muted); }
.exp-lists .exp-badge.is-ok { background: var(--sc-ok-bg); color: var(--sc-ok); }
.exp-lists .exp-badge.is-warn { background: var(--sc-warn-bg); color: var(--sc-warn); }
.exp-lists .exp-badge.is-bad { background: var(--sc-bad-bg); color: var(--sc-bad); }
.exp-lists .exp-badge.is-info { background: var(--sc-info-bg); color: var(--sc-info); }
.exp-lists .exp-facts { display: grid; grid-template-columns: repeat(auto-fit, minmax(min(100%, 165px), 1fr)); gap: 8px 18px; margin: 12px 0 0; }
.exp-lists .exp-facts > div { min-width: 0; }
.exp-lists .exp-facts dt { margin: 0; font-size: 12px; font-weight: 650; text-transform: uppercase; letter-spacing: .04em; color: var(--sc-muted); }
.exp-lists .exp-facts dd { margin: 2px 0 0; overflow-wrap: anywhere; }
.exp-lists .exp-facts dd code { color: var(--sc-muted); }
.exp-lists .exp-panel { margin: 0 0 18px; padding: 14px 16px; border: 1px solid var(--sc-line); border-radius: var(--sc-radius); background: var(--sc-card); }
.exp-lists .exp-panel > .exp-facts { margin-top: 0; }
.exp-lists .exp-fn { display: inline-block; margin: 0 4px 4px 0; padding: 1px 8px; border-radius: 6px; background: var(--sc-soft); color: var(--sc-ink); font: 12.5px/1.6 var(--sc-mono); white-space: nowrap; }
.exp-lists .exp-empty { margin: 0; padding: 18px 16px; border: 1px dashed #c9ced6; border-radius: var(--sc-radius); color: var(--sc-muted); text-align: center; }

/* Tables */
.exp-lists .exp-table-wrap { overflow-x: auto; border: 1px solid var(--sc-line); border-radius: var(--sc-radius); background: var(--sc-card); }
.exp-lists .exp-table { width: 100%; margin: 0; border-collapse: collapse; }
.exp-lists .exp-table th, .exp-lists .exp-table td { padding: 9px 12px; border: 0; border-bottom: 1px solid var(--sc-line); text-align: left; vertical-align: top; background: transparent; color: var(--sc-ink); }
.exp-lists .exp-table th { font-size: 12px; font-weight: 650; text-transform: uppercase; letter-spacing: .04em; color: var(--sc-muted); background: var(--sc-soft); white-space: nowrap; }
.exp-lists .exp-table tr:last-child td { border-bottom: 0; }
.exp-lists .exp-table .exp-num { text-align: right; font-variant-numeric: tabular-nums; white-space: nowrap; }
.exp-lists .exp-table img { vertical-align: -3px; }

/* Page size and pages */
.exp-lists .exp-listfoot { display: flex; flex-wrap: wrap; align-items: center; justify-content: space-between; gap: 10px 18px; margin: 14px 0 0; }
.exp-lists .exp-sizes { display: flex; flex-wrap: wrap; align-items: center; gap: 6px; margin: 0; font-size: 13px; color: var(--sc-muted); }
.exp-lists .exp-sizes a, .exp-lists .exp-sizes span.current { display: inline-flex; align-items: center; justify-content: center; min-width: 34px; min-height: 30px; padding: 2px 8px; border: 1px solid #c9ced6; border-radius: 8px; text-decoration: none; }
.exp-lists .exp-sizes span.current { border-color: var(--sc-accent); background: var(--sc-accent); color: #fff; font-weight: 650; }
.exp-lists .exp-pager { min-width: 0; }
.exp-lists .exp-pager .pagenavigator { margin: 0; }
.exp-lists .exp-pager a { color: var(--sc-accent-hover); }
.exp-lists .exp-pager span.text, .exp-lists .exp-pager span.text a { color: var(--sc-accent-hover); }
.exp-lists .exp-pager span.disabled, .exp-lists .exp-pager span.text.disabled { color: var(--sc-muted); }
.exp-lists .exp-pager span.current { color: var(--sc-ink); }

/* The buttons under the list */
.exp-lists .exp-bottombar { display: flex; flex-wrap: wrap; align-items: center; gap: 10px 12px; margin: 18px 0 0; padding: 12px 14px; border: 1px solid var(--sc-line); border-radius: var(--sc-radius); background: var(--sc-soft); }
.exp-lists .exp-bottombar .exp-meta { flex: 1 1 280px; }

@media (max-width: 600px) {
    .exp-lists.exp-standalone { margin: 8px; padding: 14px 12px; }
    .exp-lists .exp-sec { padding: 12px; }
    .exp-lists .exp-sec-head .exp-actions { width: 100%; }
    .exp-lists .exp-sec-head .exp-actions .exp-btn { flex: 1 1 0; }
    .exp-lists .exp-actionbar .exp-actions, .exp-lists .exp-bottombar .exp-actions { width: 100%; }
    .exp-lists .exp-actionbar .exp-btn, .exp-lists .exp-bottombar .exp-btn { flex: 1 1 auto; }
    .exp-lists .exp-table th, .exp-lists .exp-table td { padding: 8px 9px; }
}

/* ---- Beyond the section pages: the lists of RSS feeds, triggers, workflow groups and class groups ---- */
.exp-lists .exp-statusbar { display: flex; flex-wrap: wrap; align-items: center; justify-content: space-between; gap: 10px 16px;
    margin: 0 0 18px; padding: 12px 14px; border: 1px solid var(--sc-line); border-left-width: 4px; border-radius: var(--sc-radius); background: var(--sc-card); }
.exp-lists .exp-statusbar.is-ok { border-left-color: var(--sc-ok); }
.exp-lists .exp-statusbar.is-warn { border-left-color: var(--sc-warn); }
.exp-lists .exp-statusbar.is-bad { border-left-color: var(--sc-bad); }
.exp-lists .exp-statusbar.is-info { border-left-color: var(--sc-info); }
.exp-lists .exp-statusbar p { flex: 1 1 320px; }
.exp-lists .exp-sortby { display: flex; flex-wrap: wrap; align-items: center; gap: 6px; margin: 0; font-size: 13px; color: var(--sc-muted); }
.exp-lists .exp-sortby a { display: inline-flex; align-items: center; gap: 4px; min-height: 30px; padding: 2px 10px;
    border: 1px solid #c9ced6; border-radius: 999px; text-decoration: none; color: var(--sc-ink); background: #fff; }
.exp-lists .exp-sortby a:not(.current):hover { border-color: var(--sc-accent); color: var(--sc-accent-hover); }
.exp-lists .exp-sortby a.current { border-color: var(--sc-accent); background: var(--sc-accent); color: #fff; font-weight: 650; }
.exp-lists .exp-url { display: flex; flex-wrap: wrap; align-items: center; gap: 6px 10px; min-width: 0; }
.exp-lists .exp-url code { padding: 2px 8px; border-radius: 6px; background: var(--sc-soft); color: var(--sc-ink); }
.exp-lists .exp-warnings { margin: 10px 0 0; padding: 8px 12px; border-radius: 10px; background: var(--sc-warn-bg); color: var(--sc-warn); font-size: 13px; }
.exp-lists .exp-warnings ul { margin: 0; padding-left: 18px; }
.exp-lists .exp-sources { margin: 0; padding: 0; list-style: none; }
.exp-lists .exp-sources li + li { margin-top: 2px; }
.exp-lists .exp-subhead { display: flex; flex-wrap: wrap; align-items: baseline; justify-content: space-between; gap: 6px 14px; margin: 0 0 10px; }
.exp-lists .exp-part { margin: 0 0 28px; padding: 0 0 4px; }
.exp-lists .exp-part + .exp-part { padding-top: 22px; border-top: 1px solid var(--sc-line); }
.exp-lists .exp-badge.is-muted { background: var(--sc-soft); color: var(--sc-muted); }

/* The pager the RSS list shares with other pages (design:rss/pagination.tpl) */
.exp-lists .rss-pagination { display: flex; flex-wrap: wrap; align-items: center; justify-content: space-between; gap: 10px; margin: 0; padding: 0; border: 0; background: none; }
.exp-lists .rss-pagination-count { color: var(--sc-muted); font-size: 13px; }
.exp-lists .rss-pagination-pages { margin: 0; }
.exp-lists .rss-pagination-pages p { margin: 0; display: flex; flex-wrap: wrap; gap: 4px; align-items: center; }
.exp-lists .rss-pagination-pages a, .exp-lists .rss-pagination-pages .current, .exp-lists .rss-pagination-pages .disabled {
    display: inline-flex; align-items: center; justify-content: center; min-width: 32px; min-height: 30px; padding: 2px 8px;
    border: 1px solid #c9ced6; border-radius: 8px; text-decoration: none; color: var(--sc-accent-hover); background: #fff; }
.exp-lists .rss-pagination-pages .previous, .exp-lists .rss-pagination-pages .next { display: inline-flex; }
.exp-lists .rss-pagination-pages span.previous a, .exp-lists .rss-pagination-pages span.next a { border: 0; min-width: 0; padding: 0; }
.exp-lists .rss-pagination-pages .current { border-color: var(--sc-accent); background: var(--sc-accent); color: #fff; font-weight: 650; }
.exp-lists .rss-pagination-pages .disabled { color: var(--sc-muted); border-color: var(--sc-line); }
.exp-lists .rss-pagination-pages span.previous.disabled, .exp-lists .rss-pagination-pages span.next.disabled { border: 1px solid var(--sc-line); }
.exp-lists .rss-pagination-of { margin: 6px 0 0 !important; color: var(--sc-muted); font-size: 12.5px; flex-basis: 100%; }

/* Tables of choices (the trigger list) */
.exp-lists .exp-table select { min-height: 34px; max-width: 100%; padding: 4px 8px; border: 1px solid #8f96a3; border-radius: 8px; background: #fff; color: var(--sc-ink); font: inherit; font-size: 13.5px; }
.exp-lists .exp-table select:focus { border-color: var(--sc-accent); outline: 3px solid var(--sc-ring); outline-offset: 0; }
.exp-lists .exp-table tr.is-set td:first-child { box-shadow: inset 4px 0 0 var(--sc-ok); }
.exp-lists .exp-table tr.is-attention td:first-child { box-shadow: inset 4px 0 0 var(--sc-warn); }
.exp-lists .exp-table tr.is-changed td { background: var(--sc-info-bg); }
.exp-lists .exp-table td .exp-meta, .exp-lists .exp-facts dd .exp-meta { display: block; margin-top: 2px; }
.exp-lists .exp-table tbody th { font-size: 13px; text-transform: none; letter-spacing: 0; color: var(--sc-ink); background: var(--sc-soft); white-space: normal; }

/* The confirmation of a removal */
.exp-lists .exp-confirm { margin: 0 0 18px; padding: 16px 18px; border: 1px solid #f1b4b4; border-left: 4px solid var(--sc-bad); border-radius: var(--sc-radius); background: var(--sc-card); }
.exp-lists .exp-confirm > h2.exp-h2 { margin-bottom: 6px; }
.exp-lists .exp-confirm .exp-secs { margin-top: 14px; }
.exp-lists .exp-consequences { margin: 10px 0 0; padding-left: 20px; }
.exp-lists .exp-consequences li + li { margin-top: 4px; }

@media (max-width: 600px) {
    .exp-lists .exp-table select { width: 100%; }
}
</style>
{/literal}
