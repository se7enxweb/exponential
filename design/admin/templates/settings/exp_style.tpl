{* The look of the settings pages (settings/view and settings/edit), in the visual language of the redesigned
   administration pages. Included once by each of them.

   The same file is in design/admin and design/admin4, so every administration design resolves it. Everything is
   scoped to .exp-settings and takes admin4's tokens where they exist (--a4-*), with values of its own for the older
   designs. admin4's dark mode keeps its cards white, so the page needs no dark rules of its own. settings/edit is
   an edit view, which admin4 draws without the main card; .exp-standalone gives it a card of its own there and
   none inside one. *}
{literal}
<style>
.exp-settings {
    --st-ink: var(--a4-ink, #1f2430);
    --st-muted: var(--a4-muted, #5d6573);
    --st-line: var(--a4-line, #e3e6eb);
    --st-soft: var(--a4-soft, #f6f7f9);
    --st-card: #fff;
    --st-accent: #c2410c;          /* white text on it is 5.2:1 */
    --st-accent-hover: #9a3412;
    --st-ring: rgba(194, 65, 12, 0.45);
    --st-ok: #166534;   --st-ok-bg: #e7f5ea;
    --st-warn: #8a4b00; --st-warn-bg: #fff3df;
    --st-bad: #b91c1c;  --st-bad-bg: #fdecec;
    --st-info: #1e4fa8; --st-info-bg: #e8effd;
    --st-violet: #5b21b6; --st-violet-bg: #f1ebfd;
    --st-teal: #0f5f5a; --st-teal-bg: #e3f4f2;
    --st-radius: 12px;
    --st-mono: ui-monospace, SFMono-Regular, Menlo, Consolas, monospace;
    color: var(--st-ink);
    font-size: 14px;
    line-height: 1.5;
}
.exp-settings *, .exp-settings *::before, .exp-settings *::after { box-sizing: border-box; }
.exp-settings [hidden] { display: none !important; }
.exp-settings .box-content { padding-bottom: 20px; }
.exp-settings h1.context-title { margin: 0; overflow-wrap: anywhere; }
.exp-settings h2.exp-h2 { margin: 0; padding: 0; border: 0; background: none; font-size: 16px; font-weight: 650; color: var(--st-ink); }
.exp-settings h3 { overflow-wrap: anywhere; margin: 0; padding: 0; border: 0; background: none; font-size: 15px; font-weight: 650; color: var(--st-ink); }
.exp-settings p { margin: 0; }
.exp-settings code, .exp-settings .exp-mono { font-family: var(--st-mono); font-size: 12.5px; overflow-wrap: anywhere; word-break: break-word; }
.exp-settings a { color: var(--st-accent-hover); }
.exp-settings a:hover { color: var(--st-ink); }
.exp-settings :focus-visible { outline: 3px solid var(--st-ring); outline-offset: 2px; }
.exp-settings .exp-sr { position: absolute; width: 1px; height: 1px; margin: -1px; padding: 0; overflow: hidden; clip: rect(0 0 0 0); white-space: nowrap; border: 0; }
.exp-settings .exp-muted { color: var(--st-muted); }
.exp-settings ul.exp-plain, .exp-settings ol.exp-plain { margin: 0; padding: 0; list-style: none; }

.exp-settings.exp-standalone { max-width: 1040px; margin: 16px auto 24px; padding: 18px 20px; border-radius: 18px; background: var(--st-card);
    box-shadow: 0 0 0 1px rgba(16, 24, 40, 0.06), 0 12px 32px -6px rgba(16, 24, 40, 0.12); }
#maincontent .exp-settings.exp-standalone { max-width: none; margin: 0; padding: 0; border-radius: 0; background: transparent; box-shadow: none; }

.exp-settings .exp-title-row { display: flex; flex-wrap: wrap; align-items: baseline; gap: 6px 12px; }
.exp-settings .exp-intro { margin: 8px 0 18px; max-width: 80ch; color: var(--st-muted); }
.exp-settings .exp-section { margin: 0 0 22px; }
.exp-settings .exp-section-head { display: flex; flex-wrap: wrap; align-items: baseline; gap: 4px 12px; margin: 0 0 10px; }
.exp-settings .exp-section-head p { flex: 1 1 100%; color: var(--st-muted); max-width: 80ch; }

/* Messages */
.exp-settings .exp-feedback { margin: 0 0 14px; padding: 10px 14px; border: 1px solid; border-left-width: 4px; border-radius: 10px; }
.exp-settings .exp-feedback.is-ok { border-color: #b7dfc1; border-left-color: var(--st-ok); background: var(--st-ok-bg); color: var(--st-ok); }
.exp-settings .exp-feedback.is-bad { border-color: #f1b4b4; border-left-color: var(--st-bad); background: var(--st-bad-bg); color: var(--st-bad); }
.exp-settings .exp-feedback.is-warn { border-color: #f3d19c; border-left-color: var(--st-warn); background: var(--st-warn-bg); color: var(--st-warn); }
.exp-settings .exp-feedback.is-info { border-color: #bcd0f5; border-left-color: var(--st-info); background: var(--st-info-bg); color: var(--st-info); }
.exp-settings .exp-feedback strong, .exp-settings .exp-feedback code { color: inherit; }
.exp-settings .exp-feedback p + p, .exp-settings .exp-feedback p + ul, .exp-settings .exp-feedback ul + p { margin-top: 6px; }
.exp-settings .exp-feedback ul { margin: 6px 0 0; padding-left: 20px; }

/* Figures */
.exp-settings .exp-figures { display: grid; grid-template-columns: repeat(auto-fit, minmax(min(100%, 118px), 1fr)); gap: 10px; margin: 0 0 16px; padding: 0; list-style: none; }
.exp-settings .exp-figure { display: flex; flex-direction: column; gap: 2px; margin: 0; padding: 11px 13px; border: 1px solid var(--st-line); border-radius: var(--st-radius); background: var(--st-card); }
.exp-settings .exp-figure strong { font-size: 21px; line-height: 1.15; font-weight: 700; font-variant-numeric: tabular-nums; color: var(--st-ink); }
.exp-settings .exp-figure span { font-size: 12.5px; color: var(--st-muted); }
.exp-settings .exp-figure a { display: flex; flex-direction: column; gap: 2px; color: inherit; text-decoration: none; }
.exp-settings .exp-figure a:hover span { text-decoration: underline; }
.exp-settings .exp-figure.is-attention strong { color: var(--st-warn); }

/* Buttons */
.exp-settings .exp-btn {
    display: inline-flex; align-items: center; justify-content: center; gap: 6px; min-height: 36px; margin: 0; max-width: 100%;
    padding: 6px 14px; border: 1px solid #c9ced6; border-radius: 9px; background: #fff; color: var(--st-ink);
    font: 600 13.5px/1.2 inherit; font-family: inherit; cursor: pointer; white-space: nowrap; text-decoration: none;
}
.exp-settings a.exp-btn { color: var(--st-ink); }
.exp-settings .exp-btn:hover:not([disabled]) { border-color: var(--st-accent); color: var(--st-accent-hover); }
.exp-settings .exp-btn[disabled] { opacity: .55; cursor: not-allowed; }
.exp-settings .exp-btn-primary, .exp-settings a.exp-btn-primary { border-color: var(--st-accent); background: var(--st-accent); color: #fff; }
.exp-settings .exp-btn-primary:hover:not([disabled]) { border-color: var(--st-accent-hover); background: var(--st-accent-hover); color: #fff; }
.exp-settings .exp-btn-outline-danger { border-color: var(--st-bad); color: var(--st-bad); }
.exp-settings .exp-btn-outline-danger:hover:not([disabled]) { background: var(--st-bad); border-color: var(--st-bad); color: #fff; }
.exp-settings .exp-btn-small { min-height: 30px; padding: 4px 10px; font-size: 12.5px; }
@media (max-width: 600px) { .exp-settings .exp-btn { white-space: normal; text-align: center; } }
.exp-settings .exp-actions { display: flex; flex-wrap: wrap; align-items: center; gap: 8px; }

/* Controls */
.exp-settings .exp-toolbar {
    display: grid; grid-template-columns: repeat(auto-fit, minmax(min(100%, 210px), 1fr)); gap: 14px 16px; align-items: end;
    margin: 0 0 16px; padding: 14px 16px; border: 1px solid var(--st-line); border-radius: var(--st-radius); background: var(--st-card);
}
.exp-settings .exp-toolbar.is-soft { background: var(--st-soft); }
.exp-settings .exp-toolbar.is-bare { margin: 0 0 16px; padding: 0; border: 0; background: transparent; }
.exp-settings .exp-field.exp-form-row { margin: 0 0 16px; }
.exp-settings fieldset.exp-field .exp-check { align-items: flex-start; min-height: 0; padding: 4px 0; }
.exp-settings fieldset.exp-field .exp-check input { margin-top: 2px; flex: 0 0 auto; }
.exp-settings .exp-check code { display: block; font-size: 12px; }
.exp-settings .exp-field { display: flex; flex-direction: column; gap: 5px; min-width: 0; margin: 0; padding: 0; border: 0; }
.exp-settings fieldset.exp-field { background: none; box-shadow: none; border-radius: 0; }
.exp-settings fieldset.exp-field > legend { float: left; width: 100%; margin: 0 0 5px; padding: 0; background: none; }
.exp-settings fieldset.exp-field > legend + * { clear: both; }
.exp-settings .exp-field > label, .exp-settings .exp-field > legend, .exp-settings .exp-label { padding: 0; font-size: 12.5px; font-weight: 650; color: var(--st-ink); }
.exp-settings .exp-field select,
.exp-settings .exp-field input[type="search"],
.exp-settings .exp-field input[type="text"],
.exp-settings .exp-field input[type="password"],
.exp-settings .exp-field textarea {
    width: 100%; min-height: 38px; margin: 0; padding: 6px 10px; border: 1px solid #8f96a3; border-radius: 9px;
    background: #fff; color: var(--st-ink); font: inherit; font-size: 14px; box-shadow: none;
}
.exp-settings .exp-field textarea { min-height: 160px; font-family: var(--st-mono); font-size: 13px; line-height: 1.5; resize: vertical; }
.exp-settings .exp-field select:focus, .exp-settings .exp-field input:focus, .exp-settings .exp-field textarea:focus { border-color: var(--st-accent); outline: 3px solid var(--st-ring); outline-offset: 0; }
.exp-settings .exp-field [aria-invalid="true"] { border-color: var(--st-bad); box-shadow: inset 0 0 0 1px var(--st-bad); }
.exp-settings .exp-field-wide { grid-column: 1 / -1; }
.exp-settings .exp-help { font-size: 13px; color: var(--st-muted); max-width: 76ch; }
.exp-settings .exp-field-error { font-size: 13px; font-weight: 650; color: var(--st-bad); }
.exp-settings .exp-check { display: inline-flex; align-items: center; gap: 8px; min-height: 36px; font-size: 13.5px; cursor: pointer; }
.exp-settings .exp-check input { width: 18px; height: 18px; margin: 0; accent-color: var(--st-accent); }
.exp-settings .exp-chips { display: flex; flex-wrap: wrap; gap: 6px; }
.exp-settings .exp-chip { position: relative; display: inline-flex; margin: 0; }
.exp-settings .exp-chip input { position: absolute; opacity: 0; width: 1px; height: 1px; }
.exp-settings .exp-chip span { display: inline-flex; align-items: center; min-height: 32px; padding: 4px 12px; border: 1px solid #c9ced6; border-radius: 999px; background: #fff; color: var(--st-ink); font-size: 13px; cursor: pointer; }
.exp-settings .exp-chip input:checked + span { border-color: var(--st-accent); background: var(--st-accent); color: #fff; font-weight: 650; }
.exp-settings .exp-chip input:focus-visible + span { outline: 3px solid var(--st-ring); outline-offset: 2px; }

/* Panels and facts */
.exp-settings .exp-panel { margin: 0 0 16px; padding: 14px 16px; border: 1px solid var(--st-line); border-radius: var(--st-radius); background: var(--st-card); }
.exp-settings .exp-facts { display: grid; grid-template-columns: repeat(auto-fit, minmax(min(100%, 170px), 1fr)); gap: 8px 18px; margin: 0; }
.exp-settings .exp-facts > div { min-width: 0; }
.exp-settings .exp-facts dt { margin: 0; font-size: 12px; font-weight: 650; text-transform: uppercase; letter-spacing: .04em; color: var(--st-muted); }
.exp-settings .exp-facts dd { margin: 2px 0 0; overflow-wrap: anywhere; }
.exp-settings details.exp-fold { margin: 0 0 16px; border: 1px solid var(--st-line); border-radius: var(--st-radius); background: var(--st-card); }
.exp-settings details.exp-fold > summary { padding: 10px 14px; font-weight: 650; cursor: pointer; list-style-position: inside; }
.exp-settings details.exp-fold[open] > summary { border-bottom: 1px solid var(--st-line); }
.exp-settings details.exp-fold > .exp-fold-body { padding: 12px 14px; }
.exp-settings .exp-empty { margin: 0; padding: 18px 16px; border: 1px dashed #c9ced6; border-radius: var(--st-radius); color: var(--st-muted); text-align: center; }

/* Badges and origins */
.exp-settings .exp-badges { display: inline-flex; flex-wrap: wrap; gap: 5px; margin: 0; padding: 0; list-style: none; }
.exp-settings .exp-badge { display: inline-flex; align-items: center; gap: 4px; margin: 0; padding: 1px 8px; border-radius: 999px; font-size: 11.5px; font-weight: 650; line-height: 1.6; white-space: nowrap; background: var(--st-soft); color: var(--st-muted); }
.exp-settings .exp-badge.is-ok { background: var(--st-ok-bg); color: var(--st-ok); }
.exp-settings .exp-badge.is-warn { background: var(--st-warn-bg); color: var(--st-warn); }
.exp-settings .exp-badge.is-bad { background: var(--st-bad-bg); color: var(--st-bad); }
.exp-settings .exp-badge.is-info { background: var(--st-info-bg); color: var(--st-info); }
.exp-settings .exp-origin { display: inline-flex; align-items: center; max-width: 100%; padding: 1px 8px; border-radius: 6px; font-size: 12px; font-weight: 600; line-height: 1.6; background: var(--st-soft); color: var(--st-muted); overflow-wrap: anywhere; }
.exp-settings .exp-origin.is-override { background: var(--st-warn-bg); color: var(--st-warn); }
.exp-settings .exp-origin.is-siteaccess { background: var(--st-info-bg); color: var(--st-info); }
.exp-settings .exp-origin.is-extension, .exp-settings .exp-origin.is-extension-dir { background: var(--st-violet-bg); color: var(--st-violet); }
.exp-settings .exp-origin.is-extension-siteaccess { background: var(--st-teal-bg); color: var(--st-teal); }

/* The file's settings */
.exp-settings .exp-blocknav { display: flex; flex-wrap: wrap; gap: 4px 6px; margin: 0; padding: 0; list-style: none; }
.exp-settings .exp-blocknav a { display: inline-flex; gap: 4px; align-items: baseline; padding: 2px 9px; border: 1px solid var(--st-line); border-radius: 999px; font-size: 12.5px; text-decoration: none; color: var(--st-ink); background: #fff; }
.exp-settings .exp-blocknav a:hover { border-color: var(--st-accent); color: var(--st-accent-hover); }
.exp-settings .exp-blocknav span { color: var(--st-muted); font-size: 11.5px; }
.exp-settings .exp-block { margin: 0 0 14px; border: 1px solid var(--st-line); border-radius: var(--st-radius); background: var(--st-card); scroll-margin-top: 90px; }
.exp-settings .exp-block-head { display: flex; flex-wrap: wrap; align-items: center; justify-content: space-between; gap: 6px 12px; padding: 10px 14px; border-bottom: 1px solid var(--st-line); background: var(--st-soft); border-radius: var(--st-radius) var(--st-radius) 0 0; }
.exp-settings .exp-block-head h3 { font-family: var(--st-mono); font-size: 14px; }
.exp-settings .exp-set { display: grid; grid-template-columns: 28px minmax(0, 1fr) auto; gap: 4px 12px; margin: 0; padding: 10px 14px; border-bottom: 1px solid var(--st-line); scroll-margin-top: 90px; }
.exp-settings .exp-set:last-child { border-bottom: 0; }
.exp-settings .exp-set:target { box-shadow: inset 4px 0 0 var(--st-accent); background: #fffaf5; }
.exp-settings .exp-set.is-pending { box-shadow: inset 4px 0 0 var(--st-warn); }
.exp-settings .exp-set-select { padding-top: 2px; }
.exp-settings .exp-set-select input { width: 18px; height: 18px; margin: 0; accent-color: var(--st-accent); }
.exp-settings .exp-set-main { min-width: 0; }
.exp-settings .exp-set-name { display: flex; flex-wrap: wrap; align-items: center; gap: 4px 8px; }
.exp-settings .exp-set-name code { font-size: 13.5px; font-weight: 650; color: var(--st-ink); }
.exp-settings .exp-set-value { margin: 4px 0 0; }
.exp-settings .exp-val { display: inline; padding: 1px 6px; border-radius: 5px; background: var(--st-soft); font-family: var(--st-mono); font-size: 12.5px; overflow-wrap: anywhere; word-break: break-word; white-space: pre-wrap; }
.exp-settings .exp-val.is-empty { font-family: inherit; font-style: italic; color: var(--st-muted); background: transparent; padding: 0; }
.exp-settings .exp-val.is-secret { letter-spacing: .08em; }
.exp-settings .exp-val.is-true { background: var(--st-ok-bg); color: var(--st-ok); }
.exp-settings .exp-val.is-false { background: var(--st-bad-bg); color: var(--st-bad); }
.exp-settings ol.exp-vals { margin: 4px 0 0; padding: 0; list-style: none; display: grid; gap: 3px; }
.exp-settings ol.exp-vals li { display: flex; flex-wrap: wrap; align-items: baseline; gap: 2px 8px; min-width: 0; }
.exp-settings .exp-key { flex: 0 0 auto; min-width: 2.2em; font-family: var(--st-mono); font-size: 12px; color: var(--st-muted); }
.exp-settings .exp-vals .exp-origin { font-size: 11px; }
.exp-settings .exp-set-side { display: flex; flex-direction: column; align-items: flex-end; gap: 6px; text-align: right; max-width: 260px; }
.exp-settings .exp-set-from { font-size: 11.5px; }
.exp-settings .exp-set-note { margin: 4px 0 0; font-size: 12.5px; color: var(--st-muted); }
.exp-settings .exp-set-note.is-warn { color: var(--st-warn); }
.exp-settings details.exp-chain { margin: 6px 0 0; }
.exp-settings details.exp-chain > summary { display: inline-flex; align-items: center; gap: 4px; font-size: 12.5px; color: var(--st-accent-hover); cursor: pointer; }
.exp-settings ol.exp-steps { margin: 8px 0 0; padding: 0; list-style: none; counter-reset: step; border-left: 2px solid var(--st-line); }
.exp-settings ol.exp-steps > li { position: relative; margin: 0 0 8px; padding: 0 0 0 14px; }
.exp-settings ol.exp-steps > li::before { content: ""; position: absolute; left: -6px; top: 6px; width: 10px; height: 10px; border-radius: 50%; background: #c9ced6; }
.exp-settings ol.exp-steps > li.is-wins::before, .exp-settings ol.exp-steps > li.is-adds::before { background: var(--st-ok); }
.exp-settings ol.exp-steps > li.is-overridden { color: var(--st-muted); }
.exp-settings ol.exp-steps > li.is-overridden code { text-decoration: line-through; text-decoration-color: rgba(93, 101, 115, .6); }
.exp-settings .exp-step-head { display: flex; flex-wrap: wrap; align-items: center; gap: 4px 8px; }
.exp-settings .exp-step-path { font-family: var(--st-mono); font-size: 11.5px; color: var(--st-muted); overflow-wrap: anywhere; }
.exp-settings ul.exp-ops { margin: 3px 0 0; padding: 0; list-style: none; }
.exp-settings ul.exp-ops li { font-size: 12.5px; overflow-wrap: anywhere; }
.exp-settings ul.exp-ops .exp-op-what { color: var(--st-muted); font-size: 12px; }
.exp-settings .exp-bottombar { display: flex; flex-wrap: wrap; align-items: center; gap: 10px 12px; margin: 16px 0 0; padding: 10px 14px; border: 1px solid var(--st-line); border-radius: var(--st-radius); background: var(--st-soft); }
.exp-settings .exp-bottombar .exp-meta { flex: 1 1 260px; font-size: 13px; color: var(--st-muted); }

/* Tables (comparison, search hits, files) */
.exp-settings .exp-table-wrap { overflow-x: auto; border: 1px solid var(--st-line); border-radius: var(--st-radius); background: var(--st-card); }
.exp-settings .exp-table { width: 100%; margin: 0; border-collapse: collapse; }
.exp-settings .exp-table th, .exp-settings .exp-table td { padding: 8px 12px; border: 0; border-bottom: 1px solid var(--st-line); text-align: left; vertical-align: top; background: transparent; color: var(--st-ink); }
.exp-settings .exp-table th { font-size: 12px; font-weight: 650; text-transform: uppercase; letter-spacing: .04em; color: var(--st-muted); background: var(--st-soft); white-space: nowrap; }
.exp-settings .exp-table tr:last-child td { border-bottom: 0; }
.exp-settings .exp-table .exp-num { text-align: right; font-variant-numeric: tabular-nums; white-space: nowrap; }
.exp-settings .exp-table td.exp-v { min-width: 12ch; }
.exp-settings .exp-table td.exp-name { min-width: 18ch; }
.exp-settings .exp-table td.exp-name code { word-break: normal; }

.exp-settings .exp-setlist { container-type: inline-size; container-name: setlist; }
@container setlist (max-width: 620px) {
    .exp-settings .exp-set { grid-template-columns: 28px minmax(0, 1fr); }
    .exp-settings .exp-set-side { grid-column: 2; flex-direction: row; flex-wrap: wrap; align-items: center; justify-content: flex-start; text-align: left; max-width: none; }
}
@media (max-width: 600px) {
    .exp-settings.exp-standalone { margin: 8px; padding: 14px 12px; }
    .exp-settings .exp-set { padding: 10px; gap: 4px 8px; }
    .exp-settings .exp-table th, .exp-settings .exp-table td { padding: 7px 8px; }
}
</style>
{/literal}
