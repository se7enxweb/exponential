{* The look of the PDF export pages (pdf/list, its removal confirmation and pdf/edit), in the visual language of the
   section, cronjobs and RSS pages. Included once by each page that uses it.

   The same file is in design/admin and design/admin4, so every administration design resolves it. Everything is
   scoped to .exp-lists and takes admin4's tokens where they exist (--a4-*), with values of its own for the older
   designs. The rules are those of the RSS list, kept here as the pdf module's own copy so that it depends on no
   stylesheet another module may change, with the parts only these pages use at the end. *}
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
.exp-lists .exp-select { display: inline-flex; align-items: center; justify-content: center; width: 32px; height: 32px; margin: -4px 0; border-radius: 8px; cursor: pointer; }
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

/* ---- Status bars, order links, addresses, warnings ---- */
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

/* The confirmation of a removal */
.exp-lists .exp-confirm { margin: 0 0 18px; padding: 16px 18px; border: 1px solid #f1b4b4; border-left: 4px solid var(--sc-bad); border-radius: var(--sc-radius); background: var(--sc-card); }
.exp-lists .exp-confirm > h2.exp-h2 { margin-bottom: 6px; }
.exp-lists .exp-confirm .exp-secs { margin-top: 14px; }
.exp-lists .exp-consequences { margin: 10px 0 0; padding-left: 20px; }
.exp-lists .exp-consequences li + li { margin-top: 4px; }

@media (max-width: 600px) {
    .exp-lists .exp-table select { width: 100%; }
}

/* ---- Edit forms ---- */
.exp-lists .exp-panel > .exp-section-head { margin-bottom: 14px; }
.exp-lists .exp-form-fields.exp-form-wide { max-width: none; grid-template-columns: repeat(auto-fit, minmax(min(100%, 340px), 1fr)); gap: 18px 24px; }
.exp-lists .exp-field textarea {
    width: 100%; min-height: 72px; margin: 0; padding: 6px 10px; border: 1px solid #8f96a3; border-radius: 9px;
    background: #fff; color: var(--sc-ink); font: inherit; font-size: 14px; box-shadow: none; resize: vertical;
}
.exp-lists .exp-field textarea:focus { border-color: var(--sc-accent); outline: 3px solid var(--sc-ring); outline-offset: 0; }
.exp-lists .exp-field input[readonly] { background: var(--sc-soft); }
.exp-lists .exp-field input[type="text"]::placeholder { color: var(--sc-muted); opacity: 1; }
.exp-lists .exp-inline { display: flex; flex-wrap: wrap; align-items: center; gap: 8px; min-width: 0; }
.exp-lists .exp-inline > input, .exp-lists .exp-inline > select { flex: 1 1 200px; width: auto; min-width: 0; }
.exp-lists .exp-prefix { padding: 7px 8px; border-radius: 8px; background: var(--sc-soft); color: var(--sc-muted); white-space: nowrap; }
.exp-lists .exp-check { display: inline-flex; align-items: center; gap: 8px; font-size: 14px !important; font-weight: 650; cursor: pointer; }
.exp-lists .exp-check + .exp-check { margin-top: 4px; }
.exp-lists .exp-check input { width: 18px; height: 18px; margin: 0; accent-color: var(--sc-accent); }
.exp-lists .exp-field > .exp-label { font-size: 12.5px; font-weight: 650; color: var(--sc-ink); }
.exp-lists .exp-field em { font-style: normal; font-weight: 400; color: var(--sc-muted); }
.exp-lists .exp-mapping { display: grid; grid-template-columns: repeat(auto-fit, minmax(min(100%, 230px), 1fr)); gap: 14px 18px; margin: 14px 0 0; padding: 14px 0 0; border-top: 1px dashed var(--sc-line); }
.exp-lists .exp-url a { font-size: 13px; }


/* ---- The PDF export pages ---- */
.exp-lists .exp-search { display: flex; flex-wrap: wrap; align-items: flex-end; gap: 10px 12px; }
.exp-lists .exp-search .exp-field { flex: 1 1 260px; }
.exp-lists .exp-search .exp-actions { flex: 0 0 auto; }
.exp-lists .exp-filters { display: flex; flex-wrap: wrap; align-items: center; gap: 6px; margin: 0; font-size: 13px; color: var(--sc-muted); }
.exp-lists .exp-filters a { display: inline-flex; align-items: center; gap: 6px; min-height: 30px; padding: 2px 12px;
    border: 1px solid #c9ced6; border-radius: 999px; text-decoration: none; color: var(--sc-ink); background: #fff; }
.exp-lists .exp-filters a:not(.current):hover { border-color: var(--sc-accent); color: var(--sc-accent-hover); }
.exp-lists .exp-filters a.current { border-color: var(--sc-accent); background: var(--sc-accent); color: #fff; font-weight: 650; }
.exp-lists .exp-filters a .exp-count { font-variant-numeric: tabular-nums; opacity: .85; }
.exp-lists .exp-controls { display: flex; flex-direction: column; gap: 12px; margin: 0 0 18px; padding: 14px 16px;
    border: 1px solid var(--sc-line); border-radius: var(--sc-radius); background: var(--sc-card); }
.exp-lists .exp-controls .exp-row { display: flex; flex-wrap: wrap; align-items: center; justify-content: space-between; gap: 8px 18px; }
.exp-lists .exp-figure a { color: inherit; text-decoration: none; }
.exp-lists .exp-figure a:hover strong { color: var(--sc-accent-hover); }
.exp-lists .exp-figure.is-warn strong { color: var(--sc-warn); }
.exp-lists .exp-file { display: flex; flex-wrap: wrap; align-items: center; gap: 4px 10px; }
.exp-lists .exp-file code { padding: 2px 8px; border-radius: 6px; background: var(--sc-soft); color: var(--sc-ink); }
.exp-lists .exp-reason { display: block; font-size: 12.5px; color: var(--sc-muted); }
.exp-lists .exp-pager .pagenavigator { display: flex; flex-wrap: wrap; align-items: center; gap: 4px; }
.exp-lists .exp-pager .pagenavigator a, .exp-lists .exp-pager .pagenavigator span.current { display: inline-flex; align-items: center; justify-content: center;
    min-width: 32px; min-height: 30px; padding: 2px 8px; border: 1px solid #c9ced6; border-radius: 8px; text-decoration: none; background: #fff; }
.exp-lists .exp-pager .pagenavigator span.current { border-color: var(--sc-accent); background: var(--sc-accent); color: #fff; font-weight: 650; }
.exp-lists .exp-pager .pagenavigator p { display: flex; flex-wrap: wrap; align-items: center; gap: 4px; margin: 0; }

/* The edit form */
.exp-lists .exp-group { margin: 0 0 18px; padding: 16px 18px; border: 1px solid var(--sc-line); border-radius: var(--sc-radius); background: var(--sc-card); }
.exp-lists .exp-group > h2.exp-h2 { margin: 0 0 4px; }
.exp-lists .exp-group > p.exp-help { margin: 0 0 14px; }
.exp-lists .exp-choices { display: grid; grid-template-columns: repeat(auto-fit, minmax(min(100%, 260px), 1fr)); gap: 10px; margin: 0; padding: 0; border: 0; }
.exp-lists .exp-choice { position: relative; display: flex; gap: 10px; align-items: flex-start; margin: 0; padding: 12px 14px;
    border: 1px solid #c9ced6; border-radius: 10px; background: #fff; cursor: pointer; }
.exp-lists .exp-choice input { flex: 0 0 auto; width: 18px; height: 18px; margin: 2px 0 0; accent-color: var(--sc-accent); }
.exp-lists .exp-choice, .exp-lists .exp-choice * { white-space: normal; }
.exp-lists .exp-choice > span { flex: 1 1 auto; min-width: 0; font-weight: 400; }
.exp-lists .exp-choice strong { display: block; font-size: 14px; font-weight: 650; color: var(--sc-ink); }
.exp-lists .exp-source .exp-meta { display: block; }
.exp-lists .exp-choice span.exp-help { display: block; margin-top: 2px; }
.exp-lists .exp-choice:has(input:checked) { border-color: var(--sc-accent); box-shadow: inset 0 0 0 1px var(--sc-accent); }
.exp-lists .exp-choice:has(input:focus-visible) { outline: 3px solid var(--sc-ring); outline-offset: 2px; }
.exp-lists .exp-classes { display: grid; grid-template-columns: repeat(auto-fill, minmax(min(100%, 210px), 1fr)); gap: 2px 14px;
    max-height: 280px; overflow: auto; margin: 0; padding: 10px 12px; border: 1px solid #8f96a3; border-radius: 9px; background: #fff; }
.exp-lists .exp-classes label { display: flex; align-items: center; gap: 8px; min-height: 30px; font-size: 13.5px; font-weight: 400; color: var(--sc-ink); cursor: pointer; overflow-wrap: anywhere; }
.exp-lists .exp-classes input { flex: 0 0 auto; width: 16px; height: 16px; margin: 0; accent-color: var(--sc-accent); }
.exp-lists .exp-classes[aria-invalid="true"] { border-color: var(--sc-bad); box-shadow: inset 0 0 0 1px var(--sc-bad); }
.exp-lists .exp-source { display: flex; flex-wrap: wrap; align-items: center; justify-content: space-between; gap: 10px 16px; padding: 12px 14px;
    border: 1px solid var(--sc-line); border-radius: 10px; background: var(--sc-soft); }
.exp-lists .exp-source.is-bad { border-color: #f1b4b4; background: var(--sc-bad-bg); color: var(--sc-bad); }
.exp-lists .exp-source .exp-source-text { flex: 1 1 260px; min-width: 0; }
.exp-lists .exp-source strong { overflow-wrap: anywhere; }
.exp-lists .exp-check-row { display: flex; flex-wrap: wrap; align-items: center; gap: 8px 14px; }
.exp-lists .exp-feedback a { color: inherit; }
/* Nothing wider than its column: long words and file names wrap, flex and grid children may shrink, fields fit */
.exp-lists label, .exp-lists legend, .exp-lists .exp-help, .exp-lists .exp-meta, .exp-lists .exp-intro, .exp-lists dd, .exp-lists h1, .exp-lists h2, .exp-lists h3,
.exp-lists .exp-feedback, .exp-lists .exp-warnings, .exp-lists .exp-source, .exp-lists code, .exp-lists .exp-badge { overflow-wrap: anywhere; word-break: normal; }
.exp-lists .exp-badge { white-space: normal; }
.exp-lists .exp-facts dd .exp-meta { display: block; margin-top: 2px; }
.exp-lists .exp-field, .exp-lists .exp-choice, .exp-lists .exp-sec-title, .exp-lists .exp-sec-head > *, .exp-lists .exp-facts > div, .exp-lists .exp-actions > *, .exp-lists .exp-source > * { min-width: 0; }
.exp-lists input[type="text"], .exp-lists input[type="search"], .exp-lists textarea, .exp-lists select { max-width: 100%; box-sizing: border-box; }
.exp-lists .exp-actions, .exp-lists .exp-bottombar, .exp-lists .exp-title-row { flex-wrap: wrap; }
@media (max-width: 600px) {
    .exp-lists .exp-group { padding: 14px 12px; }
    .exp-lists .exp-search .exp-actions, .exp-lists .exp-search .exp-actions .exp-btn { width: 100%; }
}
</style>
{/literal}
