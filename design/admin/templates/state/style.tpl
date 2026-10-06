{* The look of the object state pages (state/groups, state/group, state/view, state/edit, state/group_edit,
   state/assign), included by each of them. It is the cronjobs page's visual language: cards, badges, figures and
   buttons, scoped to .exp-states, taking admin4's tokens where they exist (--a4-*) and values of its own for the
   older designs. The same file is in design/admin and design/admin4, so every administration design resolves it.
   Text colours are chosen for at least 4.5:1 on the white cards admin4 keeps in both of its modes. *}
{literal}
<style>
.exp-states {
    --st-ink: var(--a4-ink, #1f2430);
    --st-muted: var(--a4-muted, #5d6573);
    --st-line: var(--a4-line, #e3e6eb);
    --st-soft: var(--a4-soft, #f6f7f9);
    --st-card: #fff;
    --st-accent: #c2410c;          /* white text on it is 5.2:1 */
    --st-accent-hover: #9a3412;
    --st-link: #a63a0f;            /* 6.1:1 on white */
    --st-ring: rgba(194, 65, 12, 0.45);
    --st-ok: #166534;   --st-ok-bg: #e7f5ea;
    --st-warn: #8a4b00; --st-warn-bg: #fff3df;
    --st-bad: #b91c1c;  --st-bad-bg: #fdecec;
    --st-info: #1e4fa8; --st-info-bg: #e8effd;
    --st-sys: #4b4f8a;  --st-sys-bg: #eeeffa;
    --st-radius: 12px;
    --st-mono: ui-monospace, SFMono-Regular, Menlo, Consolas, monospace;
    color: var(--st-ink);
    font-size: 14px;
    line-height: 1.5;
}
.exp-states *, .exp-states *::before, .exp-states *::after { box-sizing: border-box; }
/* The edit forms run in the edit context, which admin4 draws without the white card of other pages: in its dark
   mode the form would sit on the dark page. They bring a card of their own. */
.exp-states.exp-own-card { padding: 16px 18px 20px; border-radius: 16px; background: var(--st-card); }
.exp-states [hidden] { display: none !important; }
.exp-states .box-content { padding-bottom: 24px; }
.exp-states h1.context-title { margin: 0; overflow-wrap: anywhere; }
.exp-states h2.exp-h2 { margin: 0; padding: 0; border: 0; background: none; font-size: 16px; font-weight: 650; color: var(--st-ink); }
.exp-states h3 { margin: 0; padding: 0; border: 0; background: none; font-size: 15px; font-weight: 650; color: var(--st-ink); overflow-wrap: anywhere; }
.exp-states p { margin: 0; }
.exp-states ul.exp-plain, .exp-states ol.exp-plain { margin: 0; padding: 0; list-style: none; }
.exp-states code { font-family: var(--st-mono); font-size: 12.5px; overflow-wrap: anywhere; color: var(--st-ink); background: none; }
.exp-states a { color: var(--st-link); }
.exp-states a:hover { color: var(--st-accent-hover); }
.exp-states :focus-visible { outline: 3px solid var(--st-ring); outline-offset: 2px; }
.exp-states .exp-sr { position: absolute; width: 1px; height: 1px; margin: -1px; padding: 0; overflow: hidden; clip: rect(0 0 0 0); white-space: nowrap; border: 0; }
.exp-states .exp-muted { color: var(--st-muted); }
.exp-states .exp-meta { color: var(--st-muted); font-size: 13px; }

.exp-states .exp-title-row { display: flex; flex-wrap: wrap; align-items: center; gap: 6px 12px; }
.exp-states .exp-intro { margin: 10px 0 18px; max-width: 75ch; color: var(--st-muted); }
.exp-states .exp-intro strong { color: var(--st-ink); }
.exp-states .exp-section { margin: 0 0 26px; }
.exp-states .exp-section-head { display: flex; flex-wrap: wrap; align-items: baseline; justify-content: space-between; gap: 6px 12px; margin: 0 0 10px; }
.exp-states .exp-section-head .exp-meta { flex: 1 1 260px; }

/* Messages */
.exp-states .exp-feedback { margin: 0 0 14px; padding: 10px 14px; border: 1px solid; border-left-width: 4px; border-radius: 10px; }
.exp-states .exp-feedback.is-ok { border-color: #b7dfc1; border-left-color: var(--st-ok); background: var(--st-ok-bg); color: var(--st-ok); }
.exp-states .exp-feedback.is-bad { border-color: #f1b4b4; border-left-color: var(--st-bad); background: var(--st-bad-bg); color: var(--st-bad); }
.exp-states .exp-feedback.is-warn { border-color: #f0d29b; border-left-color: var(--st-warn); background: var(--st-warn-bg); color: var(--st-warn); }
.exp-states .exp-feedback.is-info { border-color: #bfd0f3; border-left-color: var(--st-info); background: var(--st-info-bg); color: var(--st-info); }
.exp-states .exp-feedback h2 { margin: 0 0 4px; padding: 0; border: 0; background: none; font-size: 15px; font-weight: 700; color: inherit; }
.exp-states .exp-feedback ul { margin: 4px 0 0; padding-left: 20px; }
.exp-states .exp-feedback p + p { margin-top: 6px; }

/* Overview figures */
.exp-states .exp-figures { display: grid; grid-template-columns: repeat(auto-fit, minmax(128px, 1fr)); gap: 10px; margin: 0 0 22px; padding: 0; list-style: none; }
.exp-states .exp-figure { display: flex; flex-direction: column; gap: 2px; margin: 0; padding: 12px 14px; border: 1px solid var(--st-line); border-radius: var(--st-radius); background: var(--st-card); }
.exp-states .exp-figure strong { font-size: 22px; line-height: 1.15; font-weight: 700; font-variant-numeric: tabular-nums; color: var(--st-ink); overflow-wrap: anywhere; }
.exp-states .exp-figure span { font-size: 12.5px; color: var(--st-muted); }
.exp-states .exp-figure.is-text strong { font-size: 15px; line-height: 1.35; }

/* Buttons */
.exp-states .exp-btn {
    display: inline-flex; align-items: center; justify-content: center; gap: 6px; min-height: 36px; margin: 0;
    padding: 6px 14px; border: 1px solid #c9ced6; border-radius: 9px; background: #fff; color: var(--st-ink);
    font: 600 13.5px/1.2 inherit; font-family: inherit; cursor: pointer; white-space: nowrap; text-decoration: none;
}
.exp-states a.exp-btn { color: var(--st-ink); }
.exp-states .exp-btn:hover:not([disabled]) { border-color: var(--st-accent); color: var(--st-accent-hover); }
.exp-states .exp-btn[disabled] { opacity: .5; cursor: not-allowed; }
.exp-states .exp-btn-primary, .exp-states a.exp-btn-primary { border-color: var(--st-accent); background: var(--st-accent); color: #fff; }
.exp-states .exp-btn-primary:hover:not([disabled]) { border-color: var(--st-accent-hover); background: var(--st-accent-hover); color: #fff; }
.exp-states .exp-btn-danger { border-color: var(--st-bad); color: var(--st-bad); }
.exp-states .exp-btn-danger:hover:not([disabled]) { border-color: var(--st-bad); background: var(--st-bad); color: #fff; }
.exp-states .exp-btn-danger.is-solid { background: var(--st-bad); color: #fff; }
.exp-states .exp-btn-danger.is-solid:hover:not([disabled]) { background: #8f1515; border-color: #8f1515; }
.exp-states .exp-btn-small { min-height: 30px; padding: 4px 10px; font-size: 12.5px; }
.exp-states .exp-btn-icon { min-width: 32px; min-height: 32px; padding: 4px 6px; }
.exp-states .exp-btn svg { flex: 0 0 auto; }
.exp-states .exp-actions { display: flex; flex-wrap: wrap; align-items: center; gap: 8px; }
.exp-states .exp-actions-bar {
    display: flex; flex-wrap: wrap; align-items: center; justify-content: space-between; gap: 10px 16px;
    margin: 16px 0 0; padding: 12px 14px; border: 1px solid var(--st-line); border-radius: var(--st-radius); background: var(--st-soft);
}
.exp-states .exp-actions-bar .exp-meta { flex: 1 1 240px; }

/* Badges */
.exp-states .exp-badges { display: inline-flex; flex-wrap: wrap; gap: 6px; margin: 0; padding: 0; list-style: none; }
.exp-states .exp-badge { display: inline-flex; align-items: center; gap: 5px; margin: 0; padding: 2px 9px; border-radius: 999px; font-size: 12px; font-weight: 650; line-height: 1.5; white-space: nowrap; background: var(--st-soft); color: var(--st-muted); }
.exp-states .exp-badge.is-ok { background: var(--st-ok-bg); color: var(--st-ok); }
.exp-states .exp-badge.is-warn { background: var(--st-warn-bg); color: var(--st-warn); }
.exp-states .exp-badge.is-bad { background: var(--st-bad-bg); color: var(--st-bad); }
.exp-states .exp-badge.is-info { background: var(--st-info-bg); color: var(--st-info); }
.exp-states .exp-badge.is-system { background: var(--st-sys-bg); color: var(--st-sys); }
.exp-states .exp-badge svg { flex: 0 0 auto; }

/* Cards */
.exp-states .exp-cards { display: grid; grid-template-columns: minmax(0, 1fr); gap: 12px; margin: 0; padding: 0; list-style: none; }
.exp-states .exp-card {
    min-width: 0; margin: 0; padding: 14px 16px; border: 1px solid var(--st-line); border-radius: var(--st-radius); background: var(--st-card);
    box-shadow: 0 1px 2px rgba(16, 24, 40, 0.04);
}
.exp-states .exp-card.is-system { box-shadow: inset 4px 0 0 var(--st-sys), 0 1px 2px rgba(16, 24, 40, 0.04); }
.exp-states .exp-card-head { display: flex; flex-wrap: wrap; align-items: flex-start; justify-content: space-between; gap: 10px 16px; }
.exp-states .exp-card-title { display: flex; flex-wrap: wrap; align-items: center; gap: 6px 10px; min-width: 0; }
.exp-states .exp-card-title h3 a { color: var(--st-ink); text-decoration: none; }
.exp-states .exp-card-title h3 a:hover { color: var(--st-accent-hover); text-decoration: underline; }
.exp-states .exp-key { color: var(--st-muted); }
.exp-states .exp-select { display: inline-flex; align-items: center; gap: 8px; min-height: 32px; font-size: 13px; color: var(--st-ink); cursor: pointer; }
.exp-states .exp-select input { width: 18px; height: 18px; margin: 0; accent-color: var(--st-accent); }
.exp-states .exp-desc { margin: 8px 0 0; max-width: 80ch; color: var(--st-ink); overflow-wrap: anywhere; }
.exp-states .exp-facts { display: grid; grid-template-columns: repeat(auto-fit, minmax(min(100%, 150px), 1fr)); gap: 8px 18px; margin: 12px 0 0; }
.exp-states .exp-facts > div { min-width: 0; }
.exp-states .exp-facts dt { margin: 0; font-size: 12px; font-weight: 650; text-transform: uppercase; letter-spacing: .04em; color: var(--st-muted); }
.exp-states .exp-facts dd { margin: 2px 0 0; overflow-wrap: anywhere; }

/* The states of a group, in order */
.exp-states .exp-flow { display: flex; flex-wrap: wrap; align-items: center; gap: 6px; margin: 12px 0 0; padding: 0; list-style: none; }
.exp-states .exp-flow li { display: inline-flex; align-items: center; gap: 6px; min-width: 0; margin: 0; }
.exp-states .exp-flow li + li::before { content: "\2192"; color: var(--st-muted); }
.exp-states .exp-chip { display: inline-flex; align-items: center; gap: 6px; max-width: 100%; padding: 3px 10px; border: 1px solid var(--st-line); border-radius: 999px; background: var(--st-soft); font-size: 13px; overflow-wrap: anywhere; }
.exp-states .exp-chip.is-default { border-color: #9fd0ad; background: var(--st-ok-bg); }
.exp-states .exp-chip .exp-count { font-variant-numeric: tabular-nums; color: var(--st-muted); font-size: 12px; }
.exp-states .exp-chip a { text-decoration: none; }
.exp-states .exp-chip a:hover { text-decoration: underline; }
.exp-states .exp-roles { margin: 10px 0 0; font-size: 13px; color: var(--st-muted); }
.exp-states .exp-roles a { font-weight: 600; }

/* The ordered list of states on a group's page */
.exp-states .exp-state-list { container-type: inline-size; display: grid; grid-template-columns: minmax(0, 1fr); gap: 0; margin: 0; padding: 0; list-style: none; border: 1px solid var(--st-line); border-radius: var(--st-radius); background: var(--st-card); }
.exp-states .exp-state { display: grid; grid-template-columns: auto minmax(0, 1fr) auto; align-items: start; gap: 6px 14px; margin: 0; padding: 12px 14px; border-top: 1px solid var(--st-line); }
.exp-states .exp-state:first-child { border-top: 0; }
.exp-states .exp-state.is-default { box-shadow: inset 4px 0 0 var(--st-ok); }
.exp-states .exp-pos { display: inline-flex; align-items: center; justify-content: center; width: 30px; height: 30px; border-radius: 50%; background: var(--st-soft); border: 1px solid var(--st-line); font-weight: 700; font-variant-numeric: tabular-nums; color: var(--st-ink); }
.exp-states .exp-state.is-default .exp-pos { background: var(--st-ok-bg); border-color: #9fd0ad; color: var(--st-ok); }
.exp-states .exp-state-main { min-width: 0; }
.exp-states .exp-state-name { display: flex; flex-wrap: wrap; align-items: center; gap: 4px 10px; }
.exp-states .exp-state-name h3 { font-size: 15px; }
.exp-states .exp-state-meta { display: flex; flex-wrap: wrap; gap: 2px 16px; margin: 4px 0 0; font-size: 13px; color: var(--st-muted); }
.exp-states .exp-state .exp-desc { margin-top: 4px; font-size: 13.5px; }
.exp-states .exp-state-side { display: flex; flex-wrap: wrap; align-items: center; justify-content: flex-end; gap: 8px; }
.exp-states .exp-order { display: inline-flex; align-items: center; gap: 4px; }
.exp-states .exp-order label { font-size: 12px; font-weight: 650; color: var(--st-muted); }
.exp-states .exp-order input.exp-order-input { width: 4.2em; min-height: 32px; margin: 0; padding: 4px 6px; border: 1px solid #b9bfc9; border-radius: 8px; background: #fff; color: var(--st-ink); font: inherit; font-variant-numeric: tabular-nums; text-align: center; }
.exp-states .exp-order input.exp-order-input:focus { border-color: var(--st-accent); outline: 3px solid var(--st-ring); outline-offset: 0; }

/* Forms */
.exp-states .exp-form { display: grid; grid-template-columns: minmax(0, 1fr); gap: 16px; max-width: 760px; }
.exp-states .exp-field { display: flex; flex-direction: column; gap: 5px; min-width: 0; margin: 0; padding: 0; border: 0; }
.exp-states .exp-field > label, .exp-states .exp-field > .exp-label { white-space: normal; overflow-wrap: anywhere; padding: 0; font-size: 13px; font-weight: 650; color: var(--st-ink); }
.exp-states .exp-field .exp-hint { font-size: 12.5px; color: var(--st-muted); }
.exp-states .exp-field input[type="text"], .exp-states .exp-field select, .exp-states .exp-field textarea {
    width: 100%; max-width: 100%; min-height: 38px; margin: 0; padding: 7px 10px; border: 1px solid #b9bfc9; border-radius: 9px;
    background: #fff; color: var(--st-ink); font: inherit; font-size: 14px;
}
.exp-states .exp-field textarea { min-height: 96px; resize: vertical; }
.exp-states .exp-field input[type="text"]:focus, .exp-states .exp-field select:focus, .exp-states .exp-field textarea:focus { border-color: var(--st-accent); outline: 3px solid var(--st-ring); outline-offset: 0; }
.exp-states .exp-field input.exp-mono { font-family: var(--st-mono); font-size: 13.5px; }
.exp-states fieldset.exp-lang { min-width: 0; margin: 0; padding: 12px 14px 14px; border: 1px solid var(--st-line); border-radius: var(--st-radius); background: var(--st-card); box-shadow: none; }
.exp-states fieldset.exp-lang > legend { display: inline-flex; align-items: center; gap: 8px; margin: 0; padding: 0 6px; font-size: 14px; font-weight: 650; color: var(--st-ink); background: none; }
.exp-states fieldset.exp-lang .exp-field + .exp-field { margin-top: 10px; }
.exp-states .exp-flag { width: 18px; height: 12px; }

/* Tables */
.exp-states .exp-table-wrap { overflow-x: auto; border: 1px solid var(--st-line); border-radius: var(--st-radius); background: var(--st-card); }
.exp-states .exp-table { width: 100%; margin: 0; border-collapse: collapse; }
.exp-states .exp-table th, .exp-states .exp-table td { padding: 9px 12px; border: 0; border-bottom: 1px solid var(--st-line); text-align: left; vertical-align: top; background: transparent; color: var(--st-ink); }
.exp-states .exp-table th { font-size: 12px; font-weight: 650; text-transform: uppercase; letter-spacing: .04em; color: var(--st-muted); background: var(--st-soft); white-space: nowrap; }
.exp-states .exp-table tr:last-child td { border-bottom: 0; }
.exp-states .exp-table .exp-num { text-align: right; font-variant-numeric: tabular-nums; white-space: nowrap; }

.exp-states .exp-empty { margin: 0; padding: 18px 16px; border: 1px dashed #c9ced6; border-radius: var(--st-radius); color: var(--st-muted); text-align: center; }
.exp-states .exp-empty .exp-btn { margin-top: 10px; }
.exp-states .exp-note { margin: 12px 0 0; padding: 10px 12px; border-radius: 10px; background: var(--st-soft); color: var(--st-ink); font-size: 13px; }

/* Page sizes and pages */
.exp-states .exp-pager { display: flex; flex-wrap: wrap; align-items: center; justify-content: space-between; gap: 8px 16px; margin: 14px 0 0; }
.exp-states .exp-sizes { display: inline-flex; flex-wrap: wrap; align-items: center; gap: 4px 8px; font-size: 13px; color: var(--st-muted); }
.exp-states .exp-sizes a, .exp-states .exp-sizes span.current { display: inline-flex; align-items: center; justify-content: center; min-width: 32px; min-height: 30px; padding: 2px 8px; border-radius: 8px; }
.exp-states .exp-sizes span.current { background: var(--st-soft); border: 1px solid var(--st-line); color: var(--st-ink); font-weight: 700; }
/* The shared page navigator, in this page's colours: its own grey and orange fall short of 4.5:1 on white. */
.exp-states .exp-pager a { color: var(--st-link); }
.exp-states .exp-pager span.text, .exp-states .exp-pager span.text a { color: var(--st-link); }
.exp-states .exp-pager span.disabled, .exp-states .exp-pager span.text.disabled { color: var(--st-muted); }
.exp-states .exp-pager span.current { color: var(--st-ink); }

/* A state row puts its controls under the text when the list is narrow: the side menus leave the column
   narrow long before the window is. */
@container (max-width: 720px) {
    .exp-states .exp-state { grid-template-columns: auto minmax(0, 1fr); }
    .exp-states .exp-state-side { grid-column: 2 / -1; justify-content: flex-start; }
}
@media (max-width: 600px) {
    .exp-states .exp-card { padding: 12px; }
    .exp-states .exp-state { padding: 12px; }

    .exp-states .exp-actions-bar { padding: 12px; }
    .exp-states .exp-actions-bar .exp-actions, .exp-states .exp-actions-bar .exp-actions .exp-btn { flex: 1 1 auto; }
}
</style>
{/literal}
