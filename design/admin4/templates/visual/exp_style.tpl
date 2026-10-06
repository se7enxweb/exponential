{* The look of the classic menu settings page (visual/menuconfig), in the visual language of the redesigned admin
   pages (sections, maintenance, cronjobs). Included once by visual/menuconfig.tpl.

   The same file is in design/admin and design/admin4, so every administration design resolves it. Everything is
   scoped to .exp-menuconfig and takes admin4's tokens where they exist (--a4-*), with values of its own for the
   older designs. The cards stay light in admin4's dark mode, as admin4 draws its content cards white there. *}
{literal}
<style>
.exp-menuconfig {
    --mc-ink: var(--a4-ink, #1f2430);
    --mc-muted: var(--a4-muted, #5d6573);
    --mc-line: var(--a4-line, #e3e6eb);
    --mc-soft: var(--a4-soft, #f6f7f9);
    --mc-card: #fff;
    --mc-accent: #c2410c;          /* white text on it is 5.2:1 */
    --mc-accent-hover: #9a3412;
    --mc-accent-soft: #fff1e8;
    --mc-ring: rgba(194, 65, 12, 0.45);
    --mc-ok: #166534;   --mc-ok-bg: #e7f5ea;
    --mc-warn: #8a4b00; --mc-warn-bg: #fff3df;
    --mc-bad: #b91c1c;  --mc-bad-bg: #fdecec;
    --mc-info: #1e4fa8; --mc-info-bg: #e8effd;
    --mc-wire: #475569; --mc-wire-soft: #cbd5e1;
    --mc-radius: 12px;
    --mc-mono: ui-monospace, SFMono-Regular, Menlo, Consolas, monospace;
    color: var(--mc-ink);
    font-size: 14px;
    line-height: 1.5;
}
.exp-menuconfig *, .exp-menuconfig *::before, .exp-menuconfig *::after { box-sizing: border-box; }
.exp-menuconfig [hidden] { display: none !important; }
.exp-menuconfig .box-content { padding-bottom: 20px; }
.exp-menuconfig h1.context-title { margin: 0; overflow-wrap: anywhere; }
.exp-menuconfig h2.exp-h2 { margin: 0; padding: 0; border: 0; background: none; font-size: 16px; font-weight: 650; color: var(--mc-ink); }
.exp-menuconfig h3 { overflow-wrap: anywhere; margin: 0; padding: 0; border: 0; background: none; font-size: 15px; font-weight: 650; color: var(--mc-ink); }
.exp-menuconfig p { margin: 0; }
.exp-menuconfig p + p { margin-top: 8px; }
.exp-menuconfig code { font-family: var(--mc-mono); font-size: 12.5px; overflow-wrap: anywhere; }
.exp-menuconfig a { color: var(--mc-accent-hover); }
.exp-menuconfig a:hover { color: var(--mc-ink); }
.exp-menuconfig :focus-visible { outline: 3px solid var(--mc-ring); outline-offset: 2px; }
.exp-menuconfig .exp-sr { position: absolute; width: 1px; height: 1px; margin: -1px; padding: 0; overflow: hidden; clip: rect(0 0 0 0); white-space: nowrap; border: 0; }
.exp-menuconfig .exp-muted { color: var(--mc-muted); }
.exp-menuconfig ul.exp-plain { margin: 0; padding: 0; list-style: none; }

.exp-menuconfig .exp-title-row { display: flex; flex-wrap: wrap; align-items: center; gap: 6px 12px; }
.exp-menuconfig .exp-subtitle { margin: 4px 0 12px; color: var(--mc-muted); font-size: 13px; }
.exp-menuconfig .exp-intro { margin: 10px 0 18px; max-width: 80ch; color: var(--mc-muted); }
.exp-menuconfig .exp-intro strong { color: var(--mc-ink); }
.exp-menuconfig .exp-section { margin: 0 0 24px; }
.exp-menuconfig .exp-section-head { display: flex; flex-wrap: wrap; align-items: baseline; gap: 4px 12px; margin: 0 0 10px; }
.exp-menuconfig .exp-section-head p { flex: 1 1 100%; color: var(--mc-muted); max-width: 80ch; }

/* Messages */
.exp-menuconfig .exp-feedback { margin: 0 0 16px; padding: 10px 14px; border: 1px solid; border-left-width: 4px; border-radius: 10px; }
.exp-menuconfig .exp-feedback.is-ok { border-color: #b7dfc1; border-left-color: var(--mc-ok); background: var(--mc-ok-bg); color: var(--mc-ok); }
.exp-menuconfig .exp-feedback.is-bad { border-color: #f1b4b4; border-left-color: var(--mc-bad); background: var(--mc-bad-bg); color: var(--mc-bad); }
.exp-menuconfig .exp-feedback.is-warn { border-color: #f3d19c; border-left-color: var(--mc-warn); background: var(--mc-warn-bg); color: var(--mc-warn); }
.exp-menuconfig .exp-feedback.is-info { border-color: #bcd0f5; border-left-color: var(--mc-info); background: var(--mc-info-bg); color: var(--mc-info); }
.exp-menuconfig .exp-feedback strong, .exp-menuconfig .exp-feedback code, .exp-menuconfig .exp-feedback a { color: inherit; }
.exp-menuconfig .exp-feedback ul { margin: 6px 0 0; padding-left: 20px; }
.exp-menuconfig .exp-feedback .exp-actions { margin-top: 10px; }

/* The intro: what the page is for, and the way to Layouts */
.exp-menuconfig .exp-about { display: grid; grid-template-columns: minmax(0, 1fr) auto; gap: 12px 20px; align-items: start; margin: 12px 0 20px;
    padding: 16px 18px; border: 1px solid var(--mc-line); border-radius: var(--mc-radius); background: var(--mc-soft); }
.exp-menuconfig .exp-about p { max-width: 78ch; }
.exp-menuconfig .exp-about .exp-actions { justify-content: flex-end; }
@media (max-width: 760px) { .exp-menuconfig .exp-about { grid-template-columns: minmax(0, 1fr); } .exp-menuconfig .exp-about .exp-actions { justify-content: flex-start; } }

/* Buttons */
.exp-menuconfig .exp-btn {
    display: inline-flex; align-items: center; justify-content: center; gap: 6px; min-height: 36px; max-width: 100%; margin: 0;
    padding: 6px 14px; border: 1px solid #c9ced6; border-radius: 9px; background: #fff; color: var(--mc-ink);
    font: 600 13.5px/1.2 inherit; font-family: inherit; cursor: pointer; white-space: nowrap; text-decoration: none;
}
.exp-menuconfig a.exp-btn { color: var(--mc-ink); }
.exp-menuconfig .exp-btn:hover:not([disabled]) { border-color: var(--mc-accent); color: var(--mc-accent-hover); }
.exp-menuconfig .exp-btn[disabled] { opacity: .55; cursor: not-allowed; }
.exp-menuconfig .exp-btn-primary, .exp-menuconfig a.exp-btn-primary { border-color: var(--mc-accent); background: var(--mc-accent); color: #fff; }
.exp-menuconfig .exp-btn-primary:hover:not([disabled]), .exp-menuconfig a.exp-btn-primary:hover { border-color: var(--mc-accent-hover); background: var(--mc-accent-hover); color: #fff; }
.exp-menuconfig .exp-btn-small { min-height: 30px; padding: 4px 10px; font-size: 12.5px; }
.exp-menuconfig .exp-btn svg { flex: 0 0 auto; }
@media (max-width: 600px) { .exp-menuconfig .exp-btn { white-space: normal; text-align: center; } }
.exp-menuconfig .exp-actions { display: flex; flex-wrap: wrap; align-items: center; gap: 8px; }

/* Badges */
.exp-menuconfig .exp-badge { display: inline-flex; align-items: center; gap: 5px; margin: 0; padding: 2px 9px; border-radius: 999px; font-size: 12px; font-weight: 650; line-height: 1.5; white-space: nowrap; background: var(--mc-soft); color: var(--mc-muted); }
.exp-menuconfig .exp-badge.is-ok { background: var(--mc-ok-bg); color: var(--mc-ok); }
.exp-menuconfig .exp-badge.is-warn { background: var(--mc-warn-bg); color: var(--mc-warn); }
.exp-menuconfig .exp-badge.is-info { background: var(--mc-info-bg); color: var(--mc-info); }
.exp-menuconfig .exp-badge.is-classic { background: #efe9f8; color: #5b21b6; }
@media (max-width: 600px) { .exp-menuconfig .exp-badge { white-space: normal; } }

/* The siteaccesses */
.exp-menuconfig .exp-sa-list { display: grid; grid-template-columns: repeat(auto-fill, minmax(min(100%, 260px), 1fr)); gap: 10px; margin: 0; padding: 0; list-style: none; }
.exp-menuconfig .exp-sa { display: flex; flex-direction: column; gap: 6px; min-width: 0; margin: 0; padding: 12px 14px; border: 1px solid var(--mc-line);
    border-radius: var(--mc-radius); background: var(--mc-card); }
.exp-menuconfig .exp-sa.is-selected { border-color: var(--mc-accent); box-shadow: inset 0 0 0 1px var(--mc-accent); }
.exp-menuconfig .exp-sa-head { display: flex; flex-wrap: wrap; align-items: center; justify-content: space-between; gap: 6px 10px; }
.exp-menuconfig .exp-sa-head h3 { font-family: var(--mc-mono); font-size: 14px; }
.exp-menuconfig .exp-sa p { font-size: 13px; color: var(--mc-muted); }
.exp-menuconfig .exp-sa p code { color: var(--mc-ink); }
.exp-menuconfig .exp-sa .exp-actions { margin-top: auto; padding-top: 4px; }
.exp-menuconfig .exp-empty { margin: 0 0 12px; padding: 14px 16px; border: 1px dashed #c9ced6; border-radius: var(--mc-radius); color: var(--mc-muted); }

/* Folding sections */
.exp-menuconfig details.exp-fold { margin: 12px 0 0; border: 1px solid var(--mc-line); border-radius: var(--mc-radius); background: var(--mc-card); }
.exp-menuconfig details.exp-fold > summary { display: flex; flex-wrap: wrap; align-items: center; gap: 4px 10px; padding: 11px 14px; cursor: pointer; font-weight: 650; list-style: none; }
.exp-menuconfig details.exp-fold > summary::-webkit-details-marker { display: none; }
.exp-menuconfig details.exp-fold > summary::before { content: ""; flex: 0 0 auto; width: 8px; height: 8px; margin-right: 2px; border-right: 2px solid var(--mc-muted); border-bottom: 2px solid var(--mc-muted); transform: rotate(-45deg); transition: transform .15s; }
.exp-menuconfig details.exp-fold[open] > summary::before { transform: rotate(45deg); }
.exp-menuconfig details.exp-fold > summary .exp-muted { font-weight: 400; font-size: 13px; }
.exp-menuconfig details.exp-fold > .exp-fold-body { padding: 0 14px 14px; }
.exp-menuconfig details.exp-fold > .exp-fold-body > p { max-width: 80ch; }

/* The editing panel of one siteaccess */
.exp-menuconfig .exp-panel { margin: 0 0 18px; padding: 16px 18px; border: 1px solid var(--mc-line); border-radius: var(--mc-radius); background: var(--mc-card); }
.exp-menuconfig .exp-switch { display: flex; flex-wrap: wrap; align-items: end; gap: 8px 12px; margin: 0 0 14px; }
.exp-menuconfig .exp-field { display: flex; flex-direction: column; gap: 5px; min-width: 0; margin: 0; padding: 0; border: 0; }
.exp-menuconfig .exp-field > label { padding: 0; font-size: 12.5px; font-weight: 650; color: var(--mc-ink); }
.exp-menuconfig .exp-field select { min-width: 200px; max-width: 100%; min-height: 38px; margin: 0; padding: 6px 10px; border: 1px solid #8f96a3; border-radius: 9px; background: #fff; color: var(--mc-ink); font: inherit; font-size: 14px; }
.exp-menuconfig .exp-field select:focus { border-color: var(--mc-accent); outline: 3px solid var(--mc-ring); outline-offset: 0; }

.exp-menuconfig .exp-facts { display: grid; grid-template-columns: repeat(auto-fit, minmax(min(100%, 200px), 1fr)); gap: 10px 18px; margin: 12px 0 0; }
.exp-menuconfig .exp-facts > div { min-width: 0; }
.exp-menuconfig .exp-facts dt { margin: 0; font-size: 12px; font-weight: 650; text-transform: uppercase; letter-spacing: .04em; color: var(--mc-muted); }
.exp-menuconfig .exp-facts dd { min-width: 0; margin: 2px 0 0; overflow-wrap: anywhere; }
.exp-menuconfig .exp-facts dd code { word-break: break-all; }
.exp-menuconfig .exp-facts dd .exp-muted { font-size: 13px; }

.exp-menuconfig .exp-table-wrap { overflow-x: auto; margin: 12px 0 0; border: 1px solid var(--mc-line); border-radius: var(--mc-radius); background: var(--mc-card); }
.exp-menuconfig .exp-table { width: 100%; margin: 0; border-collapse: collapse; }
.exp-menuconfig .exp-table th, .exp-menuconfig .exp-table td { padding: 8px 12px; border: 0; border-bottom: 1px solid var(--mc-line); text-align: left; vertical-align: top; background: transparent; color: var(--mc-ink); }
.exp-menuconfig .exp-table th { font-size: 12px; font-weight: 650; text-transform: uppercase; letter-spacing: .04em; color: var(--mc-muted); background: var(--mc-soft); white-space: nowrap; }
.exp-menuconfig .exp-table tr:last-child td { border-bottom: 0; }

/* The menu choices, each with a small page built from this installation's real pages */
.exp-menuconfig fieldset.exp-choices { margin: 16px 0 0; padding: 0; border: 0; background: none; box-shadow: none; min-width: 0; }
.exp-menuconfig fieldset.exp-choices > legend { margin: 0 0 4px; padding: 0; font-size: 15px; font-weight: 650; color: var(--mc-ink); background: none; }
.exp-menuconfig .exp-choice-list { display: grid; grid-template-columns: repeat(auto-fill, minmax(min(100%, 300px), 1fr)); gap: 12px; margin: 10px 0 0; padding: 0; list-style: none; }
.exp-menuconfig .exp-choice { position: relative; display: flex; flex-direction: column; min-width: 0; margin: 0; border: 1px solid #c9ced6; border-radius: var(--mc-radius); background: var(--mc-card); }
.exp-menuconfig .exp-choice:hover { border-color: var(--mc-accent); }
.exp-menuconfig .exp-choice:has(input:checked) { border-color: var(--mc-accent); box-shadow: inset 0 0 0 1px var(--mc-accent); }
.exp-menuconfig .exp-choice:has(input:checked) .exp-choice-pick { background: var(--mc-accent-soft); }
.exp-menuconfig .exp-choice:has(input:focus-visible) { outline: 3px solid var(--mc-ring); outline-offset: 2px; }
.exp-menuconfig .exp-choice-pick { position: relative; display: flex; flex-direction: column; gap: 8px; min-width: 0; margin: 0; padding: 12px; border-radius: var(--mc-radius) var(--mc-radius) 0 0; cursor: pointer; font-weight: 400; white-space: normal; }
.exp-menuconfig .exp-choice-pick input { position: absolute; top: 14px; right: 14px; width: 18px; height: 18px; margin: 0; accent-color: var(--mc-accent); cursor: pointer; }
.exp-menuconfig .exp-choice-title { display: flex; flex-wrap: wrap; align-items: center; gap: 4px 8px; padding-right: 28px; font-size: 14.5px; font-weight: 650; color: var(--mc-ink); }
.exp-menuconfig .exp-choice-desc { display: block; min-width: 0; }
.exp-menuconfig .exp-choice dl { margin: 0; font-size: 13px; overflow-wrap: anywhere; }
.exp-menuconfig .exp-choice dl > div + div { margin-top: 4px; }
.exp-menuconfig .exp-choice dt { display: inline; font-weight: 650; color: var(--mc-ink); }
.exp-menuconfig .exp-choice dd { display: inline; margin: 0; color: var(--mc-muted); overflow-wrap: anywhere; }
.exp-menuconfig details.exp-choice-more { margin: auto 0 0; border-top: 1px dashed var(--mc-line); font-size: 13px; }
.exp-menuconfig details.exp-choice-more > summary { padding: 8px 12px; cursor: pointer; font-weight: 650; color: var(--mc-accent-hover); }
.exp-menuconfig details.exp-choice-more > ul, .exp-menuconfig details.exp-choice-more > pre { margin: 0 12px 12px; }
.exp-menuconfig .exp-choice-facts li { margin: 0 0 6px; overflow-wrap: anywhere; }

/* The small page: header, top rows, left column and content, with real page names */
.exp-menuconfig .exp-mini { display: flex; flex-direction: column; gap: 4px; min-height: 150px; padding: 6px; border: 1px solid var(--mc-wire-soft); border-radius: 8px; background: #f8fafc; font-size: 11px; line-height: 1.35; overflow: hidden; }
.exp-menuconfig .exp-mini b { font-weight: 600; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; }
.exp-menuconfig .exp-mini-head { padding: 2px 6px; border-radius: 4px; background: #e2e8f0; color: #1e293b; font-weight: 700; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; }
.exp-menuconfig .exp-mini-top, .exp-menuconfig .exp-mini-second { display: flex; flex-wrap: wrap; gap: 2px 10px; padding: 3px 6px; border-radius: 4px; background: var(--mc-wire); color: #fff; }
.exp-menuconfig .exp-mini-top b { max-width: 12em; }
.exp-menuconfig .exp-mini-top b.is-in { text-decoration: underline; text-underline-offset: 2px; }
.exp-menuconfig .exp-mini-second { margin: 0 10px; background: #e2e8f0; color: #1e293b; }
.exp-menuconfig .exp-mini-body { display: grid; grid-template-columns: minmax(0, 1fr); gap: 6px; flex: 1 1 auto; min-height: 0; }
.exp-menuconfig .exp-mini-body.has-left { grid-template-columns: minmax(0, 38%) minmax(0, 1fr); }
.exp-menuconfig .exp-mini-left { display: flex; flex-direction: column; gap: 2px; min-width: 0; padding: 4px 6px; border-radius: 4px; background: var(--mc-wire); color: #fff; }
.exp-menuconfig .exp-mini-left b.is-sub { padding-left: 8px; font-weight: 400; }
.exp-menuconfig .exp-mini-main { display: flex; flex-direction: column; gap: 5px; min-width: 0; padding: 4px 2px; }
.exp-menuconfig .exp-mini-main i { display: block; height: 5px; border-radius: 2px; background: var(--mc-wire-soft); }
.exp-menuconfig .exp-mini-main i:first-child { height: auto; background: none; color: #334155; font-style: normal; font-weight: 600; overflow-wrap: anywhere; }
.exp-menuconfig .exp-mini-main i:nth-child(3) { width: 80%; }

/* Examples and template snippets */
.exp-menuconfig .exp-recipes { display: grid; grid-template-columns: repeat(auto-fill, minmax(min(100%, 380px), 1fr)); gap: 12px; }
.exp-menuconfig .exp-recipe { min-width: 0; margin: 0; padding: 14px 16px; border: 1px solid var(--mc-line); border-radius: var(--mc-radius); background: var(--mc-card); }
.exp-menuconfig .exp-recipe > p { margin-top: 8px; }
.exp-menuconfig .exp-recipe-file { font-size: 12.5px; color: var(--mc-muted); }
.exp-menuconfig .exp-recipe-sees { padding-top: 8px; border-top: 1px dashed var(--mc-line); }
.exp-menuconfig .exp-designs { margin: 0 0 14px; padding: 0; list-style: none; display: grid; gap: 6px; }
.exp-menuconfig .exp-designs li { display: flex; flex-wrap: wrap; align-items: baseline; gap: 4px 10px; padding: 8px 12px; border: 1px solid var(--mc-line); border-radius: 9px; background: var(--mc-card); overflow-wrap: anywhere; }
.exp-menuconfig .exp-designs li code { font-weight: 650; }
.exp-menuconfig .exp-snippet { margin: 0 0 14px; min-width: 0; }
.exp-menuconfig .exp-snippet-head { display: flex; flex-wrap: wrap; align-items: center; justify-content: space-between; gap: 6px 12px; margin: 0 0 4px; }
.exp-menuconfig .exp-snippet-head h3 { font-size: 14.5px; }
.exp-menuconfig .exp-snippet > p { margin: 0 0 6px; max-width: 80ch; color: var(--mc-muted); }
.exp-menuconfig pre.exp-code { max-width: 100%; margin: 0; padding: 12px 14px; border-radius: 9px; background: #1e293b; color: #e2e8f0; font: 12.5px/1.55 var(--mc-mono); white-space: pre; overflow-x: auto; tab-size: 4; }
.exp-menuconfig pre.exp-code:focus-visible { outline: 3px solid var(--mc-ring); outline-offset: 2px; }

/* Save */
.exp-menuconfig .exp-savebar { display: flex; flex-wrap: wrap; align-items: center; gap: 10px 16px; margin: 18px 0 0; padding: 12px 14px; border: 1px solid var(--mc-line); border-radius: var(--mc-radius); background: var(--mc-soft); }
.exp-menuconfig .exp-savebar .exp-meta { flex: 1 1 300px; color: var(--mc-muted); font-size: 13px; }
.exp-menuconfig .exp-confirm { display: flex; align-items: flex-start; gap: 8px; flex: 1 1 100%; min-width: 0; white-space: normal; font-size: 13.5px; font-weight: 400; color: var(--mc-ink); }
.exp-menuconfig .exp-confirm span { min-width: 0; overflow-wrap: anywhere; }
.exp-menuconfig .exp-confirm input { flex: 0 0 auto; width: 18px; height: 18px; margin: 1px 0 0; accent-color: var(--mc-accent); }
.exp-menuconfig .exp-steps { margin: 6px 0 0; padding-left: 20px; }
.exp-menuconfig .exp-steps li + li { margin-top: 4px; }
.exp-menuconfig pre.exp-lines { margin: 8px 0 0; padding: 10px 12px; border: 1px solid var(--mc-line); border-radius: 9px; background: var(--mc-soft); color: var(--mc-ink); font: 12.5px/1.6 var(--mc-mono); white-space: pre-wrap; overflow-wrap: anywhere; }

@media (max-width: 600px) {
    .exp-menuconfig .exp-panel { padding: 12px; }
    .exp-menuconfig .exp-savebar .exp-actions, .exp-menuconfig .exp-savebar .exp-btn { width: 100%; }
    .exp-menuconfig .exp-field select { min-width: 0; width: 100%; }
    .exp-menuconfig .exp-switch .exp-field { flex: 1 1 100%; }
}
</style>
{/literal}
