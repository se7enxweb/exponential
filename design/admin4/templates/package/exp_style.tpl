{* The look of the package pages (the list with its overview, the removal confirmation, a package's page, the
   upload, the install and uninstall steps and the choice of creation wizard), in the visual language of the
   redesigned section and cronjob pages. Included once by each of them.

   The same file is in design/admin and design/admin4, so every administration design resolves it. Everything is
   scoped to .exp-packages and takes admin4's tokens where they exist (--a4-*), with values of its own for the
   older designs. An edit view (install, uninstall, create) is drawn by admin4 without the main card;
   .exp-standalone gives it a card of its own there and none inside one. Guide: doc/guides/packages.md *}
{literal}
<style>
.exp-packages {
    --pk-ink: var(--a4-ink, #1f2430);
    --pk-muted: var(--a4-muted, #5d6573);
    --pk-line: var(--a4-line, #e3e6eb);
    --pk-soft: var(--a4-soft, #f6f7f9);
    --pk-card: #fff;
    --pk-accent: #c2410c;          /* white text on it is 5.2:1 */
    --pk-accent-hover: #9a3412;
    --pk-ring: rgba(194, 65, 12, 0.45);
    --pk-ok: #166534;   --pk-ok-bg: #e7f5ea;
    --pk-warn: #8a4b00; --pk-warn-bg: #fff3df;
    --pk-bad: #b91c1c;  --pk-bad-bg: #fdecec;
    --pk-info: #1e4fa8; --pk-info-bg: #e8effd;
    --pk-radius: 12px;
    --pk-mono: ui-monospace, SFMono-Regular, Menlo, Consolas, monospace;
    color: var(--pk-ink);
    font-size: 14px;
    line-height: 1.5;
}
.exp-packages *, .exp-packages *::before, .exp-packages *::after { box-sizing: border-box; }
.exp-packages [hidden] { display: none !important; }
.exp-packages .box-content { padding-bottom: 20px; }
.exp-packages h1.context-title { margin: 0; overflow-wrap: anywhere; }
.exp-packages h2.exp-h2 { margin: 0; padding: 0; border: 0; background: none; font-size: 16px; font-weight: 650; color: var(--pk-ink); }
.exp-packages h3 { overflow-wrap: anywhere; margin: 0; padding: 0; border: 0; background: none; font-size: 15px; font-weight: 650; color: var(--pk-ink); }
.exp-packages p { margin: 0; }
.exp-packages code { font-family: var(--pk-mono); font-size: 12.5px; overflow-wrap: anywhere; }
.exp-packages a { color: var(--pk-accent-hover); }
.exp-packages a:hover { color: var(--pk-ink); }
.exp-packages :focus-visible { outline: 3px solid var(--pk-ring); outline-offset: 2px; }
.exp-packages .exp-sr { position: absolute; width: 1px; height: 1px; margin: -1px; padding: 0; overflow: hidden; clip: rect(0 0 0 0); white-space: nowrap; border: 0; }
.exp-packages .exp-muted { color: var(--pk-muted); }
.exp-packages ul.exp-plain { margin: 0; padding: 0; list-style: none; }

/* A page drawn without the admin's main card (the edit view) gets one of its own */
.exp-packages.exp-standalone { max-width: 980px; margin: 16px auto 24px; padding: 18px 20px; border-radius: 18px; background: var(--pk-card);
    box-shadow: 0 0 0 1px rgba(16, 24, 40, 0.06), 0 12px 32px -6px rgba(16, 24, 40, 0.12); }
#maincontent .exp-packages.exp-standalone { max-width: none; margin: 0; padding: 0; border-radius: 0; background: transparent; box-shadow: none; }

.exp-packages .exp-title-row { display: flex; flex-wrap: wrap; align-items: center; gap: 6px 12px; }
.exp-packages .exp-title-key { color: var(--pk-muted); }
.exp-packages .exp-intro { margin: 8px 0 18px; max-width: 78ch; color: var(--pk-muted); }
.exp-packages .exp-section { margin: 0 0 24px; }
.exp-packages .exp-section-head { display: flex; flex-wrap: wrap; align-items: baseline; gap: 4px 12px; margin: 0 0 10px; }
.exp-packages .exp-section-head p { flex: 1 1 100%; color: var(--pk-muted); max-width: 78ch; }

/* Messages */
.exp-packages .exp-feedback { margin: 0 0 14px; padding: 10px 14px; border: 1px solid; border-left-width: 4px; border-radius: 10px; }
.exp-packages .exp-feedback.is-ok { border-color: #b7dfc1; border-left-color: var(--pk-ok); background: var(--pk-ok-bg); color: var(--pk-ok); }
.exp-packages .exp-feedback.is-bad { border-color: #f1b4b4; border-left-color: var(--pk-bad); background: var(--pk-bad-bg); color: var(--pk-bad); }
.exp-packages .exp-feedback.is-warn { border-color: #f3d19c; border-left-color: var(--pk-warn); background: var(--pk-warn-bg); color: var(--pk-warn); }
.exp-packages .exp-feedback.is-info { border-color: #bcd0f5; border-left-color: var(--pk-info); background: var(--pk-info-bg); color: var(--pk-info); }
.exp-packages .exp-feedback strong { color: inherit; }
.exp-packages .exp-feedback p + p, .exp-packages .exp-feedback p + ul, .exp-packages .exp-feedback ul + p { margin-top: 6px; }
.exp-packages .exp-feedback ul { margin: 6px 0 0; padding-left: 20px; }
.exp-packages .exp-feedback h2.exp-h2 { color: inherit; }
.exp-packages .exp-reasons { margin-top: 8px; }
.exp-packages .exp-reasons li + li { margin-top: 4px; }

/* Overview figures */
.exp-packages .exp-figures { display: grid; grid-template-columns: repeat(auto-fit, minmax(min(100%, 120px), 1fr)); gap: 10px; margin: 0 0 20px; padding: 0; list-style: none; }
.exp-packages .exp-figure { display: flex; flex-direction: column; gap: 2px; margin: 0; padding: 12px 14px; border: 1px solid var(--pk-line); border-radius: var(--pk-radius); background: var(--pk-card); }
.exp-packages .exp-figure strong { font-size: 22px; line-height: 1.15; font-weight: 700; font-variant-numeric: tabular-nums; color: var(--pk-ink); }
.exp-packages .exp-figure span { font-size: 12.5px; color: var(--pk-muted); }
.exp-packages .exp-figure.is-attention strong { color: var(--pk-bad); }

/* Buttons */
.exp-packages .exp-btn {
    display: inline-flex; align-items: center; justify-content: center; gap: 6px; min-height: 36px; margin: 0;
    padding: 6px 14px; border: 1px solid #c9ced6; border-radius: 9px; background: #fff; color: var(--pk-ink);
    font: 600 13.5px/1.2 inherit; font-family: inherit; cursor: pointer; white-space: nowrap; text-decoration: none;
}
.exp-packages a.exp-btn { color: var(--pk-ink); }
.exp-packages .exp-btn:hover:not([disabled]):not(.is-disabled) { border-color: var(--pk-accent); color: var(--pk-accent-hover); }
.exp-packages .exp-btn[disabled], .exp-packages .exp-btn.is-disabled { opacity: .55; cursor: not-allowed; }
.exp-packages .exp-btn-primary, .exp-packages a.exp-btn-primary { border-color: var(--pk-accent); background: var(--pk-accent); color: #fff; }
.exp-packages .exp-btn-primary:hover:not([disabled]) { border-color: var(--pk-accent-hover); background: var(--pk-accent-hover); color: #fff; }
.exp-packages .exp-btn-danger { border-color: var(--pk-bad); background: var(--pk-bad); color: #fff; }
.exp-packages .exp-btn-danger:hover:not([disabled]) { border-color: #7f1d1d; background: #7f1d1d; color: #fff; }
.exp-packages .exp-btn-outline-danger { border-color: var(--pk-bad); color: var(--pk-bad); }
.exp-packages .exp-btn-outline-danger:hover:not([disabled]) { background: var(--pk-bad); border-color: var(--pk-bad); color: #fff; }
.exp-packages .exp-btn-small { min-height: 30px; padding: 4px 10px; font-size: 12.5px; }
.exp-packages .exp-btn svg { flex: 0 0 auto; }
.exp-packages .exp-btn { max-width: 100%; }
@media (max-width: 600px) { .exp-packages .exp-btn { white-space: normal; text-align: center; } }
.exp-packages .exp-actions { display: flex; flex-wrap: wrap; align-items: center; gap: 8px; }
.exp-packages .exp-actions form { display: contents; }
.exp-packages .exp-actionbar { display: flex; flex-wrap: wrap; align-items: center; justify-content: space-between; gap: 10px 16px;
    margin: 0 0 18px; padding: 12px 14px; border: 1px solid var(--pk-line); border-radius: var(--pk-radius); background: var(--pk-soft); }
.exp-packages .exp-actionbar .exp-meta { flex: 1 1 260px; }
.exp-packages .exp-meta { color: var(--pk-muted); font-size: 13px; }

/* Controls */
.exp-packages .exp-toolbar {
    display: grid; grid-template-columns: repeat(auto-fit, minmax(min(100%, 230px), 1fr)); gap: 14px 16px; align-items: end;
    margin: 0 0 20px; padding: 16px; border: 1px solid var(--pk-line); border-radius: var(--pk-radius); background: var(--pk-card);
}
.exp-packages .exp-field { display: flex; flex-direction: column; gap: 5px; min-width: 0; margin: 0; padding: 0; border: 0; }
.exp-packages fieldset.exp-field { background: none; box-shadow: none; border-radius: 0; }
.exp-packages fieldset.exp-field > legend { float: left; width: 100%; margin: 0 0 5px; padding: 0; background: none; }
.exp-packages fieldset.exp-field > legend + * { clear: both; }
.exp-packages .exp-field > label, .exp-packages .exp-field > legend { padding: 0; font-size: 12.5px; font-weight: 650; color: var(--pk-ink); }
.exp-packages .exp-field select,
.exp-packages .exp-field input[type="search"],
.exp-packages .exp-field input[type="text"] {
    width: 100%; min-height: 38px; margin: 0; padding: 6px 10px; border: 1px solid #8f96a3; border-radius: 9px;
    background: #fff; color: var(--pk-ink); font: inherit; font-size: 14px; box-shadow: none;
}
.exp-packages .exp-field select:focus, .exp-packages .exp-field input:focus { border-color: var(--pk-accent); outline: 3px solid var(--pk-ring); outline-offset: 0; }
.exp-packages .exp-field input[aria-invalid="true"] { border-color: var(--pk-bad); box-shadow: inset 0 0 0 1px var(--pk-bad); }
.exp-packages .exp-field-wide { grid-column: 1 / -1; }
.exp-packages .exp-form-fields { display: grid; grid-template-columns: minmax(0, 1fr); gap: 18px; max-width: 640px; }
.exp-packages .exp-help { font-size: 13px; color: var(--pk-muted); max-width: 72ch; }
.exp-packages .exp-field-error { font-size: 13px; font-weight: 650; color: var(--pk-bad); }
.exp-packages .exp-chips { display: flex; flex-wrap: wrap; gap: 6px; }
.exp-packages .exp-chip { position: relative; display: inline-flex; margin: 0; }
.exp-packages .exp-chip input { position: absolute; opacity: 0; width: 1px; height: 1px; }
.exp-packages .exp-chip span { display: inline-flex; align-items: center; min-height: 32px; padding: 4px 12px; border: 1px solid #c9ced6; border-radius: 999px; background: #fff; color: var(--pk-ink); font-size: 13px; font-weight: 400; cursor: pointer; }
.exp-packages .exp-chip input:checked + span { border-color: var(--pk-accent); background: var(--pk-accent); color: #fff; font-weight: 650; }
.exp-packages .exp-chip input:focus-visible + span { outline: 3px solid var(--pk-ring); outline-offset: 2px; }
.exp-packages .exp-filter-count { grid-column: 1 / -1; margin: 0; font-size: 13px; color: var(--pk-muted); }

/* The section cards */
.exp-packages .exp-pkgs { display: grid; grid-template-columns: minmax(0, 1fr); gap: 12px; margin: 0; padding: 0; list-style: none; }
.exp-packages .exp-pkg {
    min-width: 0; margin: 0; padding: 14px 16px; border: 1px solid var(--pk-line); border-radius: var(--pk-radius); background: var(--pk-card);
    box-shadow: 0 1px 2px rgba(16, 24, 40, 0.04);
}
.exp-packages .exp-pkg.is-attention { box-shadow: inset 4px 0 0 var(--pk-warn), 0 1px 2px rgba(16, 24, 40, 0.04); }
.exp-packages .exp-pkg.is-selected { border-color: var(--pk-accent); }
.exp-packages .exp-pkg-head { display: flex; flex-wrap: wrap; align-items: flex-start; justify-content: space-between; gap: 10px 16px; }
.exp-packages .exp-pkg-title { display: flex; flex-wrap: wrap; align-items: center; gap: 6px 10px; min-width: 0; flex: 1 1 320px; }
.exp-packages .exp-pkg-title h3 a { color: var(--pk-ink); text-decoration: none; }
.exp-packages .exp-pkg-title h3 a:hover { color: var(--pk-accent-hover); text-decoration: underline; }
.exp-packages .exp-select { display: inline-flex; align-items: center; justify-content: center; width: 32px; height: 32px; margin: -4px 0 -4px -6px; border-radius: 8px; cursor: pointer; }
.exp-packages .exp-select:hover { background: var(--pk-soft); }
.exp-packages .exp-select input { width: 18px; height: 18px; margin: 0; accent-color: var(--pk-accent); cursor: pointer; }
.exp-packages .exp-badges { display: inline-flex; flex-wrap: wrap; gap: 6px; margin: 0; padding: 0; list-style: none; }
.exp-packages .exp-badge { display: inline-flex; align-items: center; gap: 5px; margin: 0; padding: 2px 9px; border-radius: 999px; font-size: 12px; font-weight: 650; line-height: 1.5; white-space: nowrap; background: var(--pk-soft); color: var(--pk-muted); }
.exp-packages .exp-badge.is-ok { background: var(--pk-ok-bg); color: var(--pk-ok); }
.exp-packages .exp-badge.is-warn { background: var(--pk-warn-bg); color: var(--pk-warn); }
.exp-packages .exp-badge.is-bad { background: var(--pk-bad-bg); color: var(--pk-bad); }
.exp-packages .exp-badge.is-info { background: var(--pk-info-bg); color: var(--pk-info); }
.exp-packages .exp-facts { display: grid; grid-template-columns: repeat(auto-fit, minmax(min(100%, 165px), 1fr)); gap: 8px 18px; margin: 12px 0 0; }
.exp-packages .exp-facts > div { min-width: 0; }
.exp-packages .exp-facts dt { margin: 0; font-size: 12px; font-weight: 650; text-transform: uppercase; letter-spacing: .04em; color: var(--pk-muted); }
.exp-packages .exp-facts dd { margin: 2px 0 0; overflow-wrap: anywhere; }
.exp-packages .exp-facts dd code { color: var(--pk-muted); }
.exp-packages .exp-panel { margin: 0 0 18px; padding: 14px 16px; border: 1px solid var(--pk-line); border-radius: var(--pk-radius); background: var(--pk-card); }
.exp-packages .exp-panel > .exp-facts { margin-top: 0; }
.exp-packages .exp-fn { display: inline-block; margin: 0 4px 4px 0; padding: 1px 8px; border-radius: 6px; background: var(--pk-soft); color: var(--pk-ink); font: 12.5px/1.6 var(--pk-mono); white-space: nowrap; }
.exp-packages .exp-empty { margin: 0; padding: 18px 16px; border: 1px dashed #c9ced6; border-radius: var(--pk-radius); color: var(--pk-muted); text-align: center; }

/* Tables */
.exp-packages .exp-table-wrap { overflow-x: auto; border: 1px solid var(--pk-line); border-radius: var(--pk-radius); background: var(--pk-card); }
.exp-packages .exp-table { width: 100%; margin: 0; border-collapse: collapse; }
.exp-packages .exp-table th, .exp-packages .exp-table td { padding: 9px 12px; border: 0; border-bottom: 1px solid var(--pk-line); text-align: left; vertical-align: top; background: transparent; color: var(--pk-ink); }
.exp-packages .exp-table th { font-size: 12px; font-weight: 650; text-transform: uppercase; letter-spacing: .04em; color: var(--pk-muted); background: var(--pk-soft); white-space: nowrap; }
.exp-packages .exp-table tr:last-child td { border-bottom: 0; }
.exp-packages .exp-table .exp-num { text-align: right; font-variant-numeric: tabular-nums; white-space: nowrap; }
.exp-packages .exp-table img { vertical-align: -3px; }

/* Page size and pages */
.exp-packages .exp-listfoot { display: flex; flex-wrap: wrap; align-items: center; justify-content: space-between; gap: 10px 18px; margin: 14px 0 0; }
.exp-packages .exp-sizes { display: flex; flex-wrap: wrap; align-items: center; gap: 6px; margin: 0; font-size: 13px; color: var(--pk-muted); }
.exp-packages .exp-sizes a, .exp-packages .exp-sizes span.current { display: inline-flex; align-items: center; justify-content: center; min-width: 34px; min-height: 30px; padding: 2px 8px; border: 1px solid #c9ced6; border-radius: 8px; text-decoration: none; }
.exp-packages .exp-sizes span.current { border-color: var(--pk-accent); background: var(--pk-accent); color: #fff; font-weight: 650; }
.exp-packages .exp-pager { min-width: 0; }
.exp-packages .exp-pager .pagenavigator { margin: 0; }
.exp-packages .exp-pager a { color: var(--pk-accent-hover); }
.exp-packages .exp-pager span.text, .exp-packages .exp-pager span.text a { color: var(--pk-accent-hover); }
.exp-packages .exp-pager span.disabled, .exp-packages .exp-pager span.text.disabled { color: var(--pk-muted); }
.exp-packages .exp-pager span.current { color: var(--pk-ink); }

/* The buttons under the list */
.exp-packages .exp-bottombar { display: flex; flex-wrap: wrap; align-items: center; gap: 10px 12px; margin: 18px 0 0; padding: 12px 14px; border: 1px solid var(--pk-line); border-radius: var(--pk-radius); background: var(--pk-soft); }
.exp-packages .exp-bottombar .exp-meta { flex: 1 1 280px; }

@media (max-width: 600px) {
    .exp-packages.exp-standalone { margin: 8px; padding: 14px 12px; }
    .exp-packages .exp-pkg { padding: 12px; }
    .exp-packages .exp-pkg-head .exp-actions { width: 100%; }
    .exp-packages .exp-pkg-head .exp-actions .exp-btn { flex: 1 1 0; }
    .exp-packages .exp-actionbar .exp-actions, .exp-packages .exp-bottombar .exp-actions { width: 100%; }
    .exp-packages .exp-actionbar .exp-btn, .exp-packages .exp-bottombar .exp-btn { flex: 1 1 auto; }
    .exp-packages .exp-table th, .exp-packages .exp-table td { padding: 8px 9px; }
}

/* ---- Package pages only ---- */

/* The repositories: one tile each, a link to its list */
.exp-packages .exp-repos { display: grid; grid-template-columns: repeat(auto-fill, minmax(min(100%, 230px), 1fr)); gap: 10px; margin: 0 0 20px; padding: 0; list-style: none; }
.exp-packages .exp-repo { margin: 0; }
.exp-packages .exp-repo > a { display: flex; flex-direction: column; gap: 6px; height: 100%; padding: 12px 14px; border: 1px solid var(--pk-line); border-radius: var(--pk-radius);
    background: var(--pk-card); color: var(--pk-ink); text-decoration: none; }
.exp-packages .exp-repo > a:hover { border-color: var(--pk-accent); }
.exp-packages .exp-repo.is-current > a { border-color: var(--pk-accent); box-shadow: inset 0 0 0 1px var(--pk-accent); }
.exp-packages .exp-repo-name { display: flex; flex-wrap: wrap; align-items: center; gap: 6px 8px; font-weight: 650; font-size: 15px; }
.exp-packages .exp-repo-name code { font-size: 14px; color: var(--pk-ink); }
.exp-packages .exp-repo-count { font-size: 22px; font-weight: 700; line-height: 1.1; font-variant-numeric: tabular-nums; }
.exp-packages .exp-repo-count span { font-size: 12.5px; font-weight: 400; color: var(--pk-muted); }
.exp-packages .exp-repo-lines { margin: 0; padding: 0; list-style: none; font-size: 12.5px; color: var(--pk-muted); }
.exp-packages .exp-repo-lines li.is-warn { color: var(--pk-warn); font-weight: 650; }

/* The cards */
.exp-packages .exp-pkg.is-source { box-shadow: inset 4px 0 0 var(--pk-info), 0 1px 2px rgba(16, 24, 40, 0.04); }
.exp-packages .exp-pkg-summary { margin: 6px 0 0; color: var(--pk-ink); }
.exp-packages .exp-pkg-title h3 { font-size: 16px; }
.exp-packages .exp-version { font: 600 12.5px/1.6 var(--pk-mono); padding: 1px 8px; border-radius: 6px; background: var(--pk-soft); color: var(--pk-ink); white-space: nowrap; }
.exp-packages .exp-deps { margin: 0; padding: 0; list-style: none; display: flex; flex-wrap: wrap; gap: 4px 6px; }
.exp-packages .exp-deps li { margin: 0; }
.exp-packages .exp-deps .is-missing { color: var(--pk-bad); font-weight: 650; }

/* The search and filters (a GET form) */
.exp-packages .exp-toolbar .exp-actions { align-self: end; }
.exp-packages .exp-sortbar { display: flex; flex-wrap: wrap; align-items: center; gap: 6px; margin: 0; font-size: 13px; color: var(--pk-muted); }
.exp-packages .exp-sortbar a, .exp-packages .exp-sortbar span.current { display: inline-flex; align-items: center; min-height: 30px; padding: 2px 10px; border: 1px solid #c9ced6; border-radius: 999px; text-decoration: none; }
.exp-packages .exp-sortbar span.current { border-color: var(--pk-accent); background: var(--pk-accent); color: #fff; font-weight: 650; }

/* The pager */
.exp-packages .exp-pages { display: flex; flex-wrap: wrap; align-items: center; gap: 6px; margin: 0; padding: 0; list-style: none; font-size: 13px; }
.exp-packages .exp-pages a, .exp-packages .exp-pages span { display: inline-flex; align-items: center; justify-content: center; min-width: 34px; min-height: 30px; padding: 2px 8px;
    border: 1px solid #c9ced6; border-radius: 8px; text-decoration: none; color: var(--pk-accent-hover); }
.exp-packages .exp-pages span[aria-current] { border-color: var(--pk-accent); background: var(--pk-accent); color: #fff; font-weight: 650; }
.exp-packages .exp-pages span.is-disabled { color: var(--pk-muted); border-style: dashed; }

/* The confirmation */
.exp-packages .exp-goes { display: grid; grid-template-columns: minmax(0, 1fr); gap: 10px; margin: 0 0 16px; padding: 0; list-style: none; }
.exp-packages .exp-confirm-tick { display: flex; flex: 1 1 100%; min-width: 0; align-items: flex-start; gap: 10px; margin: 0; font-weight: 650; overflow-wrap: anywhere; }
.exp-packages .exp-confirm-tick input { width: 18px; height: 18px; margin: 2px 0 0; accent-color: var(--pk-bad); flex: 0 0 auto; }

/* A package's page */
.exp-packages .exp-carries { display: grid; grid-template-columns: repeat(auto-fit, minmax(min(100%, 260px), 1fr)); gap: 12px; margin: 0 0 18px; }
.exp-packages .exp-carries .exp-panel { margin: 0; }
.exp-packages .exp-carries h3 { margin-bottom: 8px; font-size: 14px; }
.exp-packages .exp-description { margin: 12px 0 0; max-width: 78ch; overflow-wrap: anywhere; }
.exp-packages .exp-thumb { max-width: 160px; max-height: 120px; border: 1px solid var(--pk-line); border-radius: 8px; }
.exp-packages .exp-head-row { display: flex; flex-wrap: wrap; align-items: flex-start; justify-content: space-between; gap: 12px 18px; }
.exp-packages .exp-changelog { margin: 0; padding: 0; list-style: none; }
.exp-packages .exp-changelog li + li { margin-top: 8px; }
.exp-packages .exp-changelog ul { margin: 4px 0 0; padding-left: 20px; }
/* The package contents browser keeps its own look (content.css); inside these pages its form follows ours */
.exp-packages .pvf-browser { margin-top: 0; }

/* Upload */
.exp-packages .exp-file { display: block; width: 100%; max-width: 560px; padding: 14px; border: 2px dashed #8f96a3; border-radius: var(--pk-radius); background: var(--pk-soft); color: var(--pk-ink); font: inherit; }
.exp-packages .exp-file:focus { outline: 3px solid var(--pk-ring); outline-offset: 2px; }
.exp-packages ol.exp-steps { margin: 0; padding-left: 20px; max-width: 78ch; }
.exp-packages ol.exp-steps li + li { margin-top: 4px; }

/* The choice of creation wizard */
.exp-packages .exp-choices { display: grid; grid-template-columns: repeat(auto-fit, minmax(min(100%, 240px), 1fr)); gap: 10px; margin: 0; padding: 0; list-style: none; }
.exp-packages .exp-choice label { display: flex; align-items: flex-start; gap: 10px; height: 100%; padding: 12px 14px; border: 1px solid var(--pk-line); border-radius: var(--pk-radius); background: var(--pk-card); cursor: pointer; font-weight: 650; }
.exp-packages .exp-choice input { width: 18px; height: 18px; margin: 1px 0 0; accent-color: var(--pk-accent); }
.exp-packages .exp-choice input:checked + span { color: var(--pk-accent-hover); }
.exp-packages .exp-choice label:has(input:checked) { border-color: var(--pk-accent); box-shadow: inset 0 0 0 1px var(--pk-accent); }
</style>
{/literal}
