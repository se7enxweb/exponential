{* The look of the content language pages (content/translations, its language page and the add form), included by
   each of them. In the visual language of the cronjobs page: cards, badges, figures, folding sections. Scoped to
   .exp-languages; takes admin4's tokens where they exist (--a4-*) and values of its own for the older designs.
   The same file is in design/admin and design/admin4. Every text colour has at least 4.5:1 on its background. *}
{literal}
<style>
.exp-languages {
    --lg-ink: var(--a4-ink, #1f2430);
    --lg-muted: var(--a4-muted, #5d6573);
    --lg-line: var(--a4-line, #e3e6eb);
    --lg-soft: var(--a4-soft, #f6f7f9);
    --lg-card: #fff;
    --lg-accent: #c2410c;          /* white text on it is 5.2:1 */
    --lg-accent-hover: #9a3412;
    --lg-ring: rgba(194, 65, 12, 0.45);
    --lg-ok: #166534;   --lg-ok-bg: #e7f5ea;
    --lg-warn: #8a4b00; --lg-warn-bg: #fff3df;
    --lg-bad: #b91c1c;  --lg-bad-bg: #fdecec;
    --lg-info: #1e4fa8; --lg-info-bg: #e8effd;
    --lg-radius: 12px;
    --lg-mono: ui-monospace, SFMono-Regular, Menlo, Consolas, monospace;
    color: var(--lg-ink);
    font-size: 14px;
    line-height: 1.5;
}
.exp-languages *, .exp-languages *::before, .exp-languages *::after { box-sizing: border-box; }
.exp-languages [hidden] { display: none !important; }
.exp-languages .box-content { padding-bottom: 24px; }
.exp-languages h1.context-title, .exp-languages legend, .exp-languages .exp-lang-title, .exp-languages .exp-figure { overflow-wrap: anywhere; }
.exp-languages h1.context-title { display: flex; flex-wrap: wrap; align-items: center; gap: 4px 12px; margin: 0; }
.exp-languages h2.exp-h2 { margin: 0; padding: 0; border: 0; background: none; font-size: 16px; font-weight: 650; color: var(--lg-ink); }
.exp-languages h3 { margin: 0; padding: 0; border: 0; background: none; font-size: 15px; font-weight: 650; color: var(--lg-ink); }
.exp-languages p { margin: 0; }
.exp-languages code { font-family: var(--lg-mono); font-size: 12.5px; overflow-wrap: anywhere; color: var(--lg-ink); }
.exp-languages a { color: var(--lg-accent-hover); }
.exp-languages a:hover { color: var(--lg-accent); }
.exp-languages :focus-visible { outline: 3px solid var(--lg-ring); outline-offset: 2px; }
.exp-languages .exp-sr { position: absolute; width: 1px; height: 1px; margin: -1px; padding: 0; overflow: hidden; clip: rect(0 0 0 0); white-space: nowrap; border: 0; }
.exp-languages .exp-muted, .exp-languages .exp-meta { color: var(--lg-muted); }
.exp-languages .exp-meta { font-size: 13px; }

.exp-languages .exp-intro { margin: 10px 0 18px; max-width: 76ch; color: var(--lg-muted); }
.exp-languages .exp-intro strong { color: var(--lg-ink); }
.exp-languages .exp-section { margin: 0 0 26px; }
.exp-languages .exp-section-head { display: flex; flex-wrap: wrap; align-items: center; justify-content: space-between; gap: 8px 12px; margin: 0 0 10px; }

/* Messages */
.exp-languages .exp-feedback { margin: 0 0 14px; padding: 10px 14px; border: 1px solid; border-left-width: 4px; border-radius: 10px; }
.exp-languages .exp-feedback.is-ok { border-color: #b7dfc1; border-left-color: var(--lg-ok); background: var(--lg-ok-bg); color: var(--lg-ok); }
.exp-languages .exp-feedback.is-bad { border-color: #f1b4b4; border-left-color: var(--lg-bad); background: var(--lg-bad-bg); color: var(--lg-bad); }
.exp-languages .exp-feedback.is-warn { border-color: #f0d29b; border-left-color: var(--lg-warn); background: var(--lg-warn-bg); color: var(--lg-warn); }
.exp-languages .exp-feedback.is-info { border-color: #bcd0f5; border-left-color: var(--lg-info); background: var(--lg-info-bg); color: var(--lg-ink); }
.exp-languages .exp-feedback p + p, .exp-languages .exp-feedback p + ol, .exp-languages .exp-feedback p + ul { margin-top: 6px; }
.exp-languages .exp-feedback ol, .exp-languages .exp-feedback ul { margin: 0; padding-left: 1.4em; }
.exp-languages .exp-feedback a { color: inherit; font-weight: 650; }

/* Figures */
.exp-languages .exp-figures { display: grid; grid-template-columns: repeat(auto-fit, minmax(min(100%, 130px), 1fr)); gap: 10px; margin: 0 0 22px; padding: 0; list-style: none; }
.exp-languages .exp-figure { display: flex; flex-direction: column; gap: 2px; min-width: 0; margin: 0; padding: 12px 14px; border: 1px solid var(--lg-line); border-radius: var(--lg-radius); background: var(--lg-card); }
.exp-languages .exp-figure strong { font-size: 22px; line-height: 1.15; font-weight: 700; font-variant-numeric: tabular-nums; color: var(--lg-ink); overflow-wrap: anywhere; }
.exp-languages .exp-figure span { font-size: 12.5px; color: var(--lg-muted); }
.exp-languages .exp-figure.is-attention strong { color: var(--lg-bad); }
.exp-languages .exp-figure.is-wide { grid-column: 1 / -1; }
.exp-languages .exp-figure.is-wide strong { font-size: 15px; line-height: 1.35; }

/* Buttons */
.exp-languages .exp-btn {
    display: inline-flex; align-items: center; justify-content: center; gap: 6px; min-height: 36px; margin: 0;
    padding: 6px 14px; border: 1px solid #c9ced6; border-radius: 9px; background: #fff; color: var(--lg-ink);
    font: 600 13.5px/1.2 inherit; font-family: inherit; cursor: pointer; white-space: nowrap; text-decoration: none;
}
.exp-languages .exp-btn:hover:not([disabled]) { border-color: var(--lg-accent); color: var(--lg-accent-hover); }
.exp-languages .exp-btn[disabled] { opacity: .5; cursor: not-allowed; }
.exp-languages .exp-btn-primary { border-color: var(--lg-accent); background: var(--lg-accent); color: #fff; }
.exp-languages .exp-btn-primary:hover:not([disabled]) { border-color: var(--lg-accent-hover); background: var(--lg-accent-hover); color: #fff; }
.exp-languages .exp-btn-danger { border-color: var(--lg-bad); color: var(--lg-bad); }
.exp-languages .exp-btn-danger:hover:not([disabled]) { background: var(--lg-bad); border-color: var(--lg-bad); color: #fff; }
.exp-languages .exp-btn-small { min-height: 30px; padding: 4px 10px; font-size: 12.5px; }
.exp-languages .exp-btn svg { flex: 0 0 auto; }
.exp-languages .exp-actions { display: flex; flex-wrap: wrap; align-items: center; gap: 8px 10px; }

/* Folding sections */
.exp-languages .exp-fold { min-width: 0; margin: 0 0 22px; border: 1px solid var(--lg-line); border-radius: var(--lg-radius); background: var(--lg-card); }
.exp-languages .exp-fold > summary { display: grid; grid-template-columns: 1em minmax(0, 1fr); align-items: baseline; gap: 2px 8px; padding: 12px 16px; cursor: pointer; list-style: none; border-radius: var(--lg-radius); }
.exp-languages .exp-fold > summary > * { grid-column: 2; }
.exp-languages .exp-fold > summary::-webkit-details-marker { display: none; }
.exp-languages .exp-fold > summary::before { content: "\25B8"; grid-column: 1; grid-row: 1; color: var(--lg-muted); }
.exp-languages .exp-fold[open] > summary::before { content: "\25BE"; }
.exp-languages .exp-fold > summary:hover h2 { color: var(--lg-accent-hover); }
.exp-languages .exp-fold-body { min-width: 0; max-width: 100%; padding: 0 16px 16px; }
.exp-languages .exp-fold-body > p { margin: 0 0 10px; max-width: 80ch; }

/* Language cards */
.exp-languages .exp-langs { display: grid; grid-template-columns: minmax(0, 1fr); gap: 12px; margin: 0; padding: 0; list-style: none; }
.exp-languages .exp-lang {
    min-width: 0; margin: 0; padding: 14px 16px; border: 1px solid var(--lg-line); border-radius: var(--lg-radius); background: var(--lg-card);
    box-shadow: 0 1px 2px rgba(16, 24, 40, 0.04);
}
.exp-languages .exp-lang.is-attention { box-shadow: inset 4px 0 0 var(--lg-warn), 0 1px 2px rgba(16, 24, 40, 0.04); }
.exp-languages .exp-lang-head { display: flex; flex-wrap: wrap; align-items: flex-start; justify-content: space-between; gap: 10px 16px; }
.exp-languages .exp-lang-title { display: flex; flex-wrap: wrap; align-items: center; gap: 6px 10px; min-width: 0; }
.exp-languages .exp-lang-title img { flex: 0 0 auto; border-radius: 2px; box-shadow: 0 0 0 1px rgba(16, 24, 40, 0.12); }
.exp-languages .exp-lang-title h3 a, .exp-languages h1 .exp-title-text { color: var(--lg-ink); text-decoration: none; }
.exp-languages .exp-lang-title h3 a:hover { color: var(--lg-accent-hover); text-decoration: underline; }
.exp-languages .exp-select { display: inline-flex; align-items: center; gap: 6px; margin: 0; font-size: 13px; color: var(--lg-muted); cursor: pointer; }
.exp-languages .exp-select input { width: 18px; height: 18px; margin: 0; accent-color: var(--lg-accent); }
.exp-languages .exp-select.is-disabled { cursor: not-allowed; }
.exp-languages .exp-badges { display: inline-flex; flex-wrap: wrap; gap: 6px; margin: 0; padding: 0; list-style: none; }
.exp-languages .exp-badge { display: inline-flex; align-items: center; gap: 5px; margin: 0; padding: 2px 9px; border-radius: 999px; font-size: 12px; font-weight: 650; line-height: 1.5; white-space: nowrap; background: var(--lg-soft); color: var(--lg-muted); }
.exp-languages .exp-badge.is-ok { background: var(--lg-ok-bg); color: var(--lg-ok); }
.exp-languages .exp-badge.is-warn { background: var(--lg-warn-bg); color: var(--lg-warn); }
.exp-languages .exp-badge.is-bad { background: var(--lg-bad-bg); color: var(--lg-bad); }
.exp-languages .exp-badge.is-info { background: var(--lg-info-bg); color: var(--lg-info); }
.exp-languages .exp-facts { display: grid; grid-template-columns: repeat(auto-fit, minmax(min(100%, 180px), 1fr)); gap: 8px 18px; margin: 12px 0 0; }
.exp-languages .exp-facts > div { min-width: 0; }
.exp-languages .exp-facts > .exp-fact-wide { grid-column: 1 / -1; }
.exp-languages .exp-fact-wide .exp-chips { margin: 3px 0 4px; }
.exp-languages .exp-facts dt { margin: 0; font-size: 12px; font-weight: 650; text-transform: uppercase; letter-spacing: .04em; color: var(--lg-muted); }
.exp-languages .exp-facts dd { margin: 2px 0 0; overflow-wrap: anywhere; }
.exp-languages .exp-facts dd strong { font-size: 16px; font-variant-numeric: tabular-nums; }
.exp-languages .exp-chips { display: flex; flex-wrap: wrap; gap: 4px 6px; margin: 0; padding: 0; list-style: none; }
.exp-languages .exp-chip { display: inline-flex; align-items: center; gap: 4px; margin: 0; padding: 1px 8px; border: 1px solid var(--lg-line); border-radius: 6px; background: var(--lg-soft); font-family: var(--lg-mono); font-size: 12.5px; color: var(--lg-ink); }
.exp-languages .exp-chip.is-main { border-color: #9fb7e8; background: var(--lg-info-bg); color: var(--lg-info); font-weight: 650; }
.exp-languages .exp-chip.is-unknown { border-color: #f1b4b4; background: var(--lg-bad-bg); color: var(--lg-bad); }
.exp-languages .exp-hint { margin: 12px 0 0; padding: 10px 12px; border-radius: 10px; background: var(--lg-warn-bg); color: var(--lg-ink); }
.exp-languages .exp-hint.is-quiet { background: var(--lg-soft); }
.exp-languages .exp-hint strong { color: var(--lg-warn); }
.exp-languages .exp-hint.is-quiet strong { color: var(--lg-ink); }
.exp-languages .exp-why { margin: 10px 0 0; font-size: 13px; color: var(--lg-muted); }
.exp-languages .exp-empty { margin: 0; padding: 18px 16px; border: 1px dashed #c9ced6; border-radius: var(--lg-radius); color: var(--lg-muted); text-align: center; }

.exp-languages .exp-state { margin: 0 0 14px; }
.exp-languages .exp-legend { margin-top: 6px; }

/* The bar under the list */
.exp-languages .exp-bar {
    display: flex; flex-wrap: wrap; align-items: center; justify-content: space-between; gap: 10px 16px;
    margin: 14px 0 22px; padding: 12px 16px; border: 1px solid var(--lg-line); border-radius: var(--lg-radius); background: var(--lg-soft);
}

/* Tables */
.exp-languages .exp-table-wrap { overflow-x: auto; border: 1px solid var(--lg-line); border-radius: var(--lg-radius); background: var(--lg-card); }
.exp-languages .exp-table { width: 100%; margin: 0; border-collapse: collapse; }
.exp-languages .exp-table th, .exp-languages .exp-table td { padding: 9px 12px; border: 0; border-bottom: 1px solid var(--lg-line); text-align: left; vertical-align: top; background: transparent; color: var(--lg-ink); }
.exp-languages .exp-table th { font-size: 12px; font-weight: 650; text-transform: uppercase; letter-spacing: .04em; color: var(--lg-muted); background: var(--lg-soft); white-space: nowrap; }
.exp-languages .exp-table tr:last-child td { border-bottom: 0; }
.exp-languages .exp-table td > code { overflow-wrap: normal; white-space: nowrap; }
.exp-languages .exp-table .exp-num { text-align: right; font-variant-numeric: tabular-nums; white-space: nowrap; }

/* The locale details of one language */
.exp-languages .exp-locale-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(min(100%, 240px), 1fr)); gap: 8px 22px; margin: 0; }
.exp-languages .exp-locale-grid > div { min-width: 0; padding: 6px 0; border-bottom: 1px dashed var(--lg-line); }
.exp-languages .exp-locale-grid dt { margin: 0; font-size: 12.5px; font-weight: 650; color: var(--lg-muted); }
.exp-languages .exp-locale-grid dd { margin: 2px 0 0; overflow-wrap: anywhere; }

/* The add form */
.exp-languages .exp-form { display: grid; gap: 18px; max-width: 760px; margin: 0 0 22px; }
.exp-languages .exp-panel { min-width: 0; margin: 0; padding: 16px; border: 1px solid var(--lg-line); border-radius: var(--lg-radius); background: var(--lg-card); }
.exp-languages fieldset.exp-panel { box-shadow: none; }
.exp-languages fieldset.exp-panel > legend { float: left; width: 100%; margin: 0 0 6px; padding: 0; background: none; font-size: 16px; font-weight: 650; color: var(--lg-ink); }
.exp-languages fieldset.exp-panel > legend + * { clear: both; }
.exp-languages fieldset.exp-panel[disabled] { opacity: .6; }
.exp-languages .exp-field { display: flex; flex-direction: column; gap: 5px; min-width: 0; margin: 12px 0 0; }
.exp-languages .exp-field > label { padding: 0; font-size: 13px; font-weight: 650; color: var(--lg-ink); }
.exp-languages .exp-field .exp-meta { font-size: 12.5px; }
.exp-languages .exp-field input[type="text"], .exp-languages .exp-field input[type="search"], .exp-languages .exp-field select {
    width: 100%; max-width: 100%; min-width: 0; min-height: 36px; margin: 0; padding: 6px 10px; border: 1px solid #8c939e; border-radius: 9px;
    background: #fff; color: var(--lg-ink); font: inherit; font-size: 14px;
}
.exp-languages .exp-field select[size] { min-height: 0; padding: 4px; }
.exp-languages .exp-field select[size] option { padding: 5px 8px; border-radius: 6px; }
.exp-languages .exp-field select option:disabled { color: #6b7280; }
.exp-languages .exp-field input:focus, .exp-languages .exp-field select:focus { border-color: var(--lg-accent); outline: 3px solid var(--lg-ring); outline-offset: 0; }
.exp-languages .exp-field-row { display: grid; grid-template-columns: repeat(auto-fit, minmax(min(100%, 200px), 1fr)); gap: 0 16px; }
.exp-languages .exp-steps { margin: 6px 0 0; padding-left: 1.4em; }
.exp-languages .exp-steps li { margin: 0 0 4px; }

@media (max-width: 600px) {
    .exp-languages .exp-lang { padding: 12px; }
    .exp-languages .exp-bar .exp-actions, .exp-languages .exp-bar .exp-btn { width: 100%; }
    .exp-languages .exp-bar .exp-btn { flex: 1 1 0; }
    .exp-languages .exp-panel { padding: 12px; }
}
</style>
{/literal}
