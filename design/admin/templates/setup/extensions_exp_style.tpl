{* The look of Setup > Extensions, in the visual language of the sections, sessions and cronjobs pages. Included
   once by setup/extensions.tpl.

   The same file is in design/admin and design/admin4, so every administration design resolves it. Everything is
   scoped to .exp-extpage and takes admin4's tokens where they exist (--a4-*), with values of its own for the older
   designs. The list narrows by its own width (a container query), not the window's, because the admin's side
   columns take a different share at each size. Guide: doc/guides/extensions-page.md *}
{literal}
<style>
.exp-extpage {
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
.exp-extpage *, .exp-extpage *::before, .exp-extpage *::after { box-sizing: border-box; }
.exp-extpage [hidden] { display: none !important; }
.exp-extpage .box-content { padding-bottom: 20px; }
.exp-extpage h1.context-title { margin: 0; overflow-wrap: anywhere; }
.exp-extpage h2.exp-h2 { margin: 0; padding: 0; border: 0; background: none; font-size: 16px; font-weight: 650; color: var(--sc-ink); }
.exp-extpage h3 { overflow-wrap: anywhere; margin: 0; padding: 0; border: 0; background: none; font-size: 15px; font-weight: 650; color: var(--sc-ink); }
.exp-extpage p { margin: 0; }
.exp-extpage code { font-family: var(--sc-mono); font-size: 12.5px; overflow-wrap: anywhere; }
.exp-extpage a { color: var(--sc-accent-hover); }
.exp-extpage a:hover { color: var(--sc-ink); }
.exp-extpage :focus-visible { outline: 3px solid var(--sc-ring); outline-offset: 2px; }
.exp-extpage .exp-sr { position: absolute; width: 1px; height: 1px; margin: -1px; padding: 0; overflow: hidden; clip: rect(0 0 0 0); white-space: nowrap; border: 0; }
.exp-extpage .exp-muted { color: var(--sc-muted); }
.exp-extpage .exp-intro { margin: 8px 0 18px; max-width: 78ch; color: var(--sc-muted); }
.exp-extpage .exp-section { margin: 0 0 22px; }
.exp-extpage .exp-section-head { display: flex; flex-wrap: wrap; align-items: baseline; gap: 4px 12px; margin: 0 0 10px; }
.exp-extpage .exp-section-head p { flex: 1 1 100%; color: var(--sc-muted); max-width: 78ch; }
.exp-extpage .exp-meta { color: var(--sc-muted); font-size: 13px; }

/* Messages */
.exp-extpage .exp-feedback { margin: 0 0 14px; padding: 10px 14px; border: 1px solid; border-left-width: 4px; border-radius: 10px; overflow-wrap: anywhere; }
.exp-extpage .exp-feedback.is-ok { border-color: #b7dfc1; border-left-color: var(--sc-ok); background: var(--sc-ok-bg); color: var(--sc-ok); }
.exp-extpage .exp-feedback.is-bad { border-color: #f1b4b4; border-left-color: var(--sc-bad); background: var(--sc-bad-bg); color: var(--sc-bad); }
.exp-extpage .exp-feedback.is-warn { border-color: #f3d19c; border-left-color: var(--sc-warn); background: var(--sc-warn-bg); color: var(--sc-warn); }
.exp-extpage .exp-feedback.is-info { border-color: #bcd0f5; border-left-color: var(--sc-info); background: var(--sc-info-bg); color: var(--sc-info); }
.exp-extpage .exp-feedback strong, .exp-extpage .exp-feedback code { color: inherit; }
.exp-extpage .exp-feedback p + p, .exp-extpage .exp-feedback p + ul, .exp-extpage .exp-feedback ul + p { margin-top: 6px; }
.exp-extpage .exp-feedback ul { margin: 6px 0 0; padding-left: 20px; }
.exp-extpage details.exp-warnings > summary { cursor: pointer; }

/* Overview figures */
.exp-extpage .exp-figures { display: grid; grid-template-columns: repeat(auto-fit, minmax(min(100%, 110px), 1fr)); gap: 10px; margin: 0 0 16px; padding: 0; list-style: none; }
.exp-extpage .exp-figure { display: flex; flex-direction: column; gap: 2px; margin: 0; padding: 10px 14px; border: 1px solid var(--sc-line); border-radius: var(--sc-radius); background: var(--sc-card); }
.exp-extpage .exp-figure strong { font-size: 22px; line-height: 1.15; font-weight: 700; font-variant-numeric: tabular-nums; color: var(--sc-ink); }
.exp-extpage .exp-figure span { font-size: 12.5px; color: var(--sc-muted); }
.exp-extpage .exp-figure.is-attention strong { color: var(--sc-bad); }

/* Buttons */
.exp-extpage .exp-btn {
    display: inline-flex; align-items: center; justify-content: center; gap: 6px; min-height: 36px; max-width: 100%; margin: 0;
    padding: 6px 14px; border: 1px solid #c9ced6; border-radius: 9px; background: #fff; color: var(--sc-ink);
    font: 600 13.5px/1.2 inherit; font-family: inherit; cursor: pointer; white-space: nowrap; text-decoration: none;
}
.exp-extpage .exp-btn:hover:not([disabled]) { border-color: var(--sc-accent); color: var(--sc-accent-hover); }
.exp-extpage .exp-btn[disabled] { opacity: .55; cursor: not-allowed; }
.exp-extpage .exp-btn-primary { border-color: var(--sc-accent); background: var(--sc-accent); color: #fff; }
.exp-extpage .exp-btn-primary:hover:not([disabled]) { border-color: var(--sc-accent-hover); background: var(--sc-accent-hover); color: #fff; }
.exp-extpage .exp-btn-danger { border-color: var(--sc-bad); background: var(--sc-bad); color: #fff; }
.exp-extpage .exp-btn-danger:hover:not([disabled]) { border-color: #7f1d1d; background: #7f1d1d; color: #fff; }
.exp-extpage .exp-btn-small { min-height: 32px; padding: 4px 10px; font-size: 12.5px; }
.exp-extpage .exp-btn-icon { min-width: 34px; min-height: 32px; padding: 4px 8px; font-size: 15px; line-height: 1; }
.exp-extpage .exp-btn svg { flex: 0 0 auto; }
.exp-extpage .exp-actions { display: flex; flex-wrap: wrap; align-items: center; gap: 8px; }
.exp-extpage .exp-bar { display: flex; flex-wrap: wrap; align-items: center; justify-content: space-between; gap: 10px 16px;
    margin: 0 0 16px; padding: 12px 14px; border: 1px solid var(--sc-line); border-radius: var(--sc-radius); background: var(--sc-soft); }
.exp-extpage .exp-bar .exp-meta { flex: 1 1 240px; }
.exp-extpage .exp-bar.is-dirty { border-color: var(--sc-accent); box-shadow: inset 4px 0 0 var(--sc-accent); background: #fff7f2; }
.exp-extpage .exp-bar.is-dirty .exp-meta { color: var(--sc-ink); }
.exp-extpage .exp-pending { position: sticky; top: 8px; z-index: 5; }

/* Controls */
.exp-extpage .exp-toolbar {
    display: grid; grid-template-columns: repeat(auto-fit, minmax(min(100%, 220px), 1fr)); gap: 12px 16px; align-items: end;
    margin: 0 0 16px; padding: 14px 16px; border: 1px solid var(--sc-line); border-radius: var(--sc-radius); background: var(--sc-card);
}
.exp-extpage .exp-field { display: flex; flex-direction: column; gap: 5px; min-width: 0; margin: 0; padding: 0; border: 0; }
.exp-extpage fieldset.exp-field { background: none; box-shadow: none; border-radius: 0; }
.exp-extpage fieldset.exp-field > legend { float: left; width: 100%; margin: 0 0 5px; padding: 0; background: none; }
.exp-extpage fieldset.exp-field > legend + * { clear: both; }
.exp-extpage .exp-field > label, .exp-extpage .exp-field > legend { padding: 0; font-size: 12.5px; font-weight: 650; color: var(--sc-ink); }
.exp-extpage .exp-field input[type="search"] {
    width: 100%; min-height: 38px; margin: 0; padding: 6px 10px; border: 1px solid #8f96a3; border-radius: 9px;
    background: #fff; color: var(--sc-ink); font: inherit; font-size: 14px; box-shadow: none;
}
.exp-extpage .exp-field input:focus { border-color: var(--sc-accent); outline: 3px solid var(--sc-ring); outline-offset: 0; }
.exp-extpage .exp-field-wide { grid-column: 1 / -1; }
.exp-extpage .exp-chips { display: flex; flex-wrap: wrap; gap: 6px; }
.exp-extpage .exp-chip { position: relative; display: inline-flex; margin: 0; }
.exp-extpage .exp-chip input { position: absolute; opacity: 0; width: 1px; height: 1px; }
.exp-extpage .exp-chip span { display: inline-flex; align-items: center; gap: 6px; min-height: 32px; padding: 4px 12px; border: 1px solid #c9ced6; border-radius: 999px; background: #fff; color: var(--sc-ink); font-size: 13px; font-weight: 400; cursor: pointer; }
.exp-extpage .exp-chip input:checked + span { border-color: var(--sc-accent); background: var(--sc-accent); color: #fff; font-weight: 650; }
.exp-extpage .exp-chip input:focus-visible + span { outline: 3px solid var(--sc-ring); outline-offset: 2px; }
.exp-extpage .exp-chip small { font-size: 12px; font-variant-numeric: tabular-nums; opacity: .85; }
.exp-extpage .exp-filter-count { grid-column: 1 / -1; margin: 0; font-size: 13px; color: var(--sc-muted); }

/* Badges */
.exp-extpage .exp-badges { display: inline-flex; flex-wrap: wrap; gap: 6px; margin: 0; padding: 0; list-style: none; }
.exp-extpage .exp-badge { display: inline-flex; align-items: center; gap: 5px; margin: 0; padding: 2px 9px; border-radius: 999px; font-size: 12px; font-weight: 650; line-height: 1.5; white-space: nowrap; background: var(--sc-soft); color: var(--sc-muted); }
.exp-extpage .exp-badge.is-ok { background: var(--sc-ok-bg); color: var(--sc-ok); }
.exp-extpage .exp-badge.is-warn { background: var(--sc-warn-bg); color: var(--sc-warn); }
.exp-extpage .exp-badge.is-bad { background: var(--sc-bad-bg); color: var(--sc-bad); }
.exp-extpage .exp-badge.is-info { background: var(--sc-info-bg); color: var(--sc-info); }
.exp-extpage .exp-badge.is-change { background: #fff1e8; color: var(--sc-accent-hover); box-shadow: inset 0 0 0 1px #f5c3a6; }

/* The list */
.exp-extpage .exp-ext-list { container-type: inline-size; display: grid; grid-template-columns: minmax(0, 1fr); gap: 8px; margin: 0; padding: 0; list-style: none; }
.exp-extpage .exp-ext {
    display: grid; grid-template-columns: 48px minmax(0, 1fr) auto; gap: 6px 14px; align-items: start;
    min-width: 0; margin: 0; padding: 12px 14px; border: 1px solid var(--sc-line); border-radius: var(--sc-radius); background: var(--sc-card);
    box-shadow: 0 1px 2px rgba(16, 24, 40, 0.04);
}
.exp-extpage .exp-ext.is-inactive { background: var(--sc-soft); }
.exp-extpage .exp-ext.is-warn { box-shadow: inset 4px 0 0 var(--sc-warn), 0 1px 2px rgba(16, 24, 40, 0.04); }
.exp-extpage .exp-ext.is-bad { box-shadow: inset 4px 0 0 var(--sc-bad), 0 1px 2px rgba(16, 24, 40, 0.04); }
.exp-extpage .exp-ext.is-changed { border-color: var(--sc-accent); }
.exp-extpage .exp-ext.is-dragging { opacity: .5; }
.exp-extpage .exp-ext.is-drop-target { border-color: var(--sc-accent); }
.exp-extpage .exp-ext:target { outline: 3px solid var(--sc-ring); outline-offset: 2px; }
.exp-extpage .exp-ext-pos { display: flex; flex-direction: column; align-items: center; gap: 4px; }
.exp-extpage .exp-ext-num { display: inline-flex; align-items: center; justify-content: center; min-width: 40px; height: 32px; padding: 0 6px; border-radius: 9px;
    background: var(--sc-ok-bg); color: var(--sc-ok); font-weight: 700; font-variant-numeric: tabular-nums; }
.exp-extpage .exp-ext.is-inactive .exp-ext-num { background: #e6e9ee; color: var(--sc-muted); font-weight: 600; }
.exp-extpage .exp-grip { display: inline-flex; align-items: center; justify-content: center; width: 40px; height: 28px; margin: 0; padding: 0;
    border: 1px dashed #b8bec8; border-radius: 8px; background: transparent; color: var(--sc-muted); cursor: grab; font: inherit; }
.exp-extpage .exp-grip:hover, .exp-extpage .exp-grip[aria-pressed="true"] { border-style: solid; border-color: var(--sc-accent); color: var(--sc-accent-hover); }
.exp-extpage .exp-ext-main { min-width: 0; }
.exp-extpage .exp-ext-title { display: flex; flex-wrap: wrap; align-items: baseline; gap: 4px 10px; }
.exp-extpage .exp-ext-title h3 code { font-size: 14.5px; font-weight: 700; color: var(--sc-ink); }
.exp-extpage .exp-ext-title .exp-ext-label { color: var(--sc-muted); overflow-wrap: anywhere; }
.exp-extpage .exp-ext-desc { margin: 4px 0 0; max-width: 90ch; color: var(--sc-muted); overflow-wrap: anywhere; }
.exp-extpage .exp-ext-badges { margin-top: 6px; }
.exp-extpage .exp-facts { display: flex; flex-wrap: wrap; gap: 4px 18px; margin: 8px 0 0; font-size: 13px; }
.exp-extpage .exp-facts > div { display: flex; flex-wrap: wrap; gap: 0 6px; min-width: 0; }
.exp-extpage .exp-facts dt { margin: 0; color: var(--sc-muted); font-weight: 650; }
.exp-extpage .exp-facts dd { margin: 0; min-width: 0; overflow-wrap: anywhere; }
.exp-extpage .exp-problems { margin: 8px 0 0; padding: 0; list-style: none; font-size: 13px; }
.exp-extpage .exp-problems li { margin: 2px 0 0; padding-left: 14px; position: relative; overflow-wrap: anywhere; }
.exp-extpage .exp-problems li::before { content: ""; position: absolute; left: 0; top: .55em; width: 7px; height: 7px; border-radius: 50%; background: var(--sc-muted); }
.exp-extpage .exp-problems li.is-bad { color: var(--sc-bad); }
.exp-extpage .exp-problems li.is-bad::before { background: var(--sc-bad); }
.exp-extpage .exp-problems li.is-warn { color: var(--sc-warn); }
.exp-extpage .exp-problems li.is-warn::before { background: var(--sc-warn); }
.exp-extpage .exp-problems li.is-info { color: var(--sc-muted); }
.exp-extpage details.exp-more { margin: 8px 0 0; font-size: 13px; }
.exp-extpage details.exp-more > summary { cursor: pointer; color: var(--sc-accent-hover); font-weight: 600; width: max-content; max-width: 100%; }
.exp-extpage details.exp-more[open] > summary { margin-bottom: 6px; }
.exp-extpage .exp-ext-actions { display: flex; flex-wrap: wrap; justify-content: flex-end; align-items: center; gap: 6px; }
.exp-extpage .exp-ext-moves { display: inline-flex; gap: 4px; }
@container (max-width: 620px) {
    .exp-extpage .exp-ext { grid-template-columns: 44px minmax(0, 1fr); padding: 10px 12px; }
    .exp-extpage .exp-ext-actions { grid-column: 1 / -1; justify-content: flex-start; }
}

/* The review */
.exp-extpage .exp-review { margin: 0 0 20px; padding: 16px; border: 2px solid var(--sc-accent); border-radius: var(--sc-radius); background: var(--sc-card); }
.exp-extpage .exp-review h2.exp-h2 { margin-bottom: 8px; }
.exp-extpage .exp-review-cols { display: grid; grid-template-columns: repeat(auto-fit, minmax(min(100%, 180px), 1fr)); gap: 12px; margin: 12px 0; }
.exp-extpage .exp-review-cols > div { min-width: 0; padding: 10px 12px; border: 1px solid var(--sc-line); border-radius: 10px; background: var(--sc-soft); }
.exp-extpage .exp-review-cols h3 { font-size: 13px; text-transform: uppercase; letter-spacing: .04em; color: var(--sc-muted); }
.exp-extpage .exp-review-cols ul { margin: 6px 0 0; padding: 0; list-style: none; }
.exp-extpage .exp-review-cols li { margin: 2px 0 0; overflow-wrap: anywhere; }
.exp-extpage .exp-lines { max-height: 320px; margin: 8px 0 0; padding: 10px 12px; overflow: auto; border: 1px solid var(--sc-line); border-radius: 10px; background: var(--sc-soft);
    font: 12.5px/1.55 var(--sc-mono); color: var(--sc-ink); }
.exp-extpage .exp-line { display: block; white-space: pre; width: max-content; min-width: 100%; }
.exp-extpage .exp-line em { font-style: normal; margin-left: 1ch; }
.exp-extpage .exp-lines .is-added { background: var(--sc-ok-bg); color: var(--sc-ok); }
.exp-extpage .exp-lines .is-moved { background: #fff1e8; color: var(--sc-accent-hover); }
.exp-extpage .exp-ack { display: flex; gap: 8px; align-items: flex-start; margin: 12px 0 0; font-weight: 600; color: var(--sc-bad); white-space: normal; }
.exp-extpage .exp-ack span { min-width: 0; white-space: normal; overflow-wrap: anywhere; }
.exp-extpage .exp-ack input { width: 18px; height: 18px; margin: 1px 0 0; flex: 0 0 auto; accent-color: var(--sc-bad); }
.exp-extpage .exp-review .exp-actions { margin-top: 14px; }

.exp-extpage .exp-empty { margin: 0; padding: 18px 16px; border: 1px dashed #c9ced6; border-radius: var(--sc-radius); color: var(--sc-muted); text-align: center; }
.exp-extpage .exp-bottombar { display: flex; flex-wrap: wrap; align-items: center; gap: 10px 12px; margin: 18px 0 0; padding: 12px 14px; border: 1px solid var(--sc-line); border-radius: var(--sc-radius); background: var(--sc-soft); }
.exp-extpage .exp-bottombar .exp-meta { flex: 1 1 260px; }

@media (max-width: 600px) {
    .exp-extpage .exp-bar .exp-actions, .exp-extpage .exp-bottombar .exp-actions { width: 100%; }
    .exp-extpage .exp-bar .exp-btn, .exp-extpage .exp-bottombar .exp-btn { flex: 1 1 auto; white-space: normal; }
}
@media (prefers-reduced-motion: no-preference) {
    .exp-extpage .exp-ext { transition: border-color .15s ease, box-shadow .15s ease; }
}
</style>
{/literal}
