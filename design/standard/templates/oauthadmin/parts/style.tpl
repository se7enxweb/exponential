{* The styles of the REST and API key administration (oauthadmin/*), in the visual language of the cronjobs page.
   Scoped to .exp-oauth; takes admin4's tokens where they exist (--a4-*) and has values of its own for the older
   designs. Every text has at least 4.5:1 contrast on its background. Included once at the top of each page. *}
{literal}
<style>
.exp-oauth {
    --oa-ink: var(--a4-ink, #1f2430);
    --oa-muted: var(--a4-muted, #5d6573);
    --oa-line: var(--a4-line, #e3e6eb);
    --oa-soft: var(--a4-soft, #f6f7f9);
    --oa-card: #fff;
    --oa-accent: #c2410c;          /* white text on it is 5.2:1 */
    --oa-accent-hover: #9a3412;
    --oa-ring: rgba(194, 65, 12, 0.45);
    --oa-ok: #166534;   --oa-ok-bg: #e7f5ea;
    --oa-warn: #8a4b00; --oa-warn-bg: #fff3df;
    --oa-bad: #b91c1c;  --oa-bad-bg: #fdecec;
    --oa-info: #1e4fa8; --oa-info-bg: #e8effd;
    --oa-radius: 12px;
    --oa-mono: ui-monospace, SFMono-Regular, Menlo, Consolas, monospace;
    color: var(--oa-ink);
    font-size: 14px;
    line-height: 1.5;
}
.exp-oauth *, .exp-oauth *::before, .exp-oauth *::after { box-sizing: border-box; }
.exp-oauth [hidden] { display: none !important; }
.exp-oauth .box-content { padding-bottom: 24px; }
.exp-oauth h1.context-title { margin: 0; overflow-wrap: anywhere; }
.exp-oauth h2.exp-h2 { margin: 0; padding: 0; border: 0; background: none; font-size: 16px; font-weight: 650; color: var(--oa-ink); }
.exp-oauth h3 { margin: 0; padding: 0; border: 0; background: none; font-size: 15px; font-weight: 650; color: var(--oa-ink); overflow-wrap: anywhere; }
.exp-oauth p { margin: 0; }
.exp-oauth code { font-family: var(--oa-mono); font-size: 12.5px; overflow-wrap: anywhere; color: var(--oa-ink); }
.exp-oauth a { color: var(--oa-accent-hover); }
.exp-oauth a:hover { color: var(--oa-ink); }
.exp-oauth :focus-visible { outline: 3px solid var(--oa-ring); outline-offset: 2px; }
.exp-oauth .exp-sr { position: absolute; width: 1px; height: 1px; margin: -1px; padding: 0; overflow: hidden; clip: rect(0 0 0 0); white-space: nowrap; border: 0; }
.exp-oauth .exp-muted { color: var(--oa-muted); }
.exp-oauth .exp-meta { color: var(--oa-muted); font-size: 13px; }
.exp-oauth .exp-intro { margin: 10px 0 18px; max-width: 76ch; color: var(--oa-muted); }
.exp-oauth .exp-section { margin: 0 0 26px; }
.exp-oauth .exp-section-head { display: flex; flex-wrap: wrap; align-items: baseline; justify-content: space-between; gap: 4px 12px; margin: 0 0 10px; }

/* Tabs: applications and API keys */
.exp-oauth .exp-tabs { display: flex; flex-wrap: wrap; gap: 6px; margin: 14px 0 16px; padding: 0; list-style: none; border-bottom: 1px solid var(--oa-line); }
.exp-oauth .exp-tabs li { margin: 0 0 -1px; }
.exp-oauth .exp-tabs a { display: inline-flex; align-items: center; gap: 8px; min-height: 40px; padding: 8px 14px; border: 1px solid transparent; border-radius: 10px 10px 0 0; color: var(--oa-muted); font-weight: 650; text-decoration: none; }
.exp-oauth .exp-tabs a:hover { color: var(--oa-accent-hover); background: var(--oa-soft); }
.exp-oauth .exp-tabs a[aria-current="page"] { border-color: var(--oa-line); border-bottom-color: var(--oa-card); background: var(--oa-card); color: var(--oa-ink); }
.exp-oauth .exp-tabs .exp-count { display: inline-flex; min-width: 22px; justify-content: center; padding: 0 7px; border-radius: 999px; background: var(--oa-soft); color: var(--oa-ink); font-size: 12px; font-variant-numeric: tabular-nums; }

/* Messages */
.exp-oauth .exp-feedback { margin: 0 0 14px; padding: 10px 14px; border: 1px solid; border-left-width: 4px; border-radius: 10px; }
.exp-oauth .exp-feedback.is-ok { border-color: #b7dfc1; border-left-color: var(--oa-ok); background: var(--oa-ok-bg); color: var(--oa-ok); }
.exp-oauth .exp-feedback.is-bad { border-color: #f1b4b4; border-left-color: var(--oa-bad); background: var(--oa-bad-bg); color: var(--oa-bad); }
.exp-oauth .exp-feedback.is-warn { border-color: #f0d29b; border-left-color: var(--oa-warn); background: var(--oa-warn-bg); color: var(--oa-warn); }
.exp-oauth .exp-feedback ul { margin: 4px 0 0; padding-left: 20px; }

/* Overview figures */
.exp-oauth .exp-figures { display: grid; grid-template-columns: repeat(auto-fit, minmax(128px, 1fr)); gap: 10px; margin: 0 0 22px; padding: 0; list-style: none; }
.exp-oauth .exp-figure { display: flex; flex-direction: column; gap: 2px; margin: 0; padding: 12px 14px; border: 1px solid var(--oa-line); border-radius: var(--oa-radius); background: var(--oa-card); }
.exp-oauth a.exp-figure { text-decoration: none; }
.exp-oauth a.exp-figure:hover { border-color: var(--oa-accent); }
.exp-oauth .exp-figure strong { font-size: 22px; line-height: 1.15; font-weight: 700; font-variant-numeric: tabular-nums; color: var(--oa-ink); }
.exp-oauth .exp-figure span { font-size: 12.5px; color: var(--oa-muted); }
.exp-oauth .exp-figure.is-attention strong { color: var(--oa-bad); }
.exp-oauth .exp-figure.is-good strong { color: var(--oa-ok); }

/* Buttons */
.exp-oauth .exp-btn {
    display: inline-flex; align-items: center; justify-content: center; gap: 6px; min-height: 36px; margin: 0;
    padding: 6px 14px; border: 1px solid #c9ced6; border-radius: 9px; background: #fff; color: var(--oa-ink);
    font: 600 13.5px/1.2 inherit; font-family: inherit; cursor: pointer; white-space: nowrap; text-decoration: none;
}
.exp-oauth .exp-btn:hover:not([disabled]) { border-color: var(--oa-accent); color: var(--oa-accent-hover); }
.exp-oauth .exp-btn[disabled] { opacity: .5; cursor: not-allowed; }
.exp-oauth .exp-btn-primary { border-color: var(--oa-accent); background: var(--oa-accent); color: #fff; }
.exp-oauth .exp-btn-primary:hover:not([disabled]) { border-color: var(--oa-accent-hover); background: var(--oa-accent-hover); color: #fff; }
.exp-oauth .exp-btn-danger { border-color: var(--oa-bad); background: var(--oa-bad); color: #fff; }
.exp-oauth .exp-btn-danger:hover:not([disabled]) { border-color: #8f1515; background: #8f1515; color: #fff; }
.exp-oauth .exp-btn-outline { border-color: var(--oa-accent); color: var(--oa-accent-hover); }
.exp-oauth .exp-btn-outline:hover:not([disabled]) { background: var(--oa-accent); color: #fff; }
.exp-oauth .exp-btn-small { min-height: 30px; padding: 4px 10px; font-size: 12.5px; }
.exp-oauth .exp-btn svg { flex: 0 0 auto; }
.exp-oauth .exp-actions { display: flex; flex-wrap: wrap; align-items: center; gap: 8px; }
.exp-oauth .exp-bar { display: flex; flex-wrap: wrap; align-items: center; justify-content: space-between; gap: 10px 16px; margin: 16px 0 0; padding: 12px 14px; border: 1px solid var(--oa-line); border-radius: var(--oa-radius); background: var(--oa-soft); }

/* Filter toolbar */
.exp-oauth .exp-toolbar {
    display: grid; grid-template-columns: repeat(auto-fit, minmax(min(100%, 200px), 1fr)); gap: 12px 16px; align-items: end;
    margin: 0 0 18px; padding: 14px 16px; border: 1px solid var(--oa-line); border-radius: var(--oa-radius); background: var(--oa-card);
}
.exp-oauth .exp-field { display: flex; flex-direction: column; gap: 5px; min-width: 0; margin: 0; padding: 0; border: 0; }
.exp-oauth .exp-field > label, .exp-oauth .exp-field > .exp-label { padding: 0; font-size: 12.5px; font-weight: 650; color: var(--oa-ink); }
.exp-oauth .exp-field input[type="text"], .exp-oauth .exp-field input[type="search"], .exp-oauth .exp-field input[type="url"],
.exp-oauth .exp-field select, .exp-oauth .exp-field textarea {
    width: 100%; min-height: 38px; margin: 0; padding: 7px 10px; border: 1px solid #b9bfc9; border-radius: 9px;
    background: #fff; color: var(--oa-ink); font: inherit; font-size: 14px;
}
.exp-oauth .exp-field textarea { min-height: 90px; resize: vertical; }
.exp-oauth .exp-field input:focus, .exp-oauth .exp-field select:focus, .exp-oauth .exp-field textarea:focus { border-color: var(--oa-accent); outline: 3px solid var(--oa-ring); outline-offset: 0; }
.exp-oauth .exp-field .exp-hint { font-size: 12.5px; color: var(--oa-muted); }
.exp-oauth .exp-toolbar .exp-actions { align-self: end; }

/* Cards (applications) */
.exp-oauth .exp-cards { display: grid; grid-template-columns: minmax(0, 1fr); gap: 12px; margin: 0; padding: 0; list-style: none; }
.exp-oauth .exp-card {
    min-width: 0; margin: 0; padding: 14px 16px; border: 1px solid var(--oa-line); border-radius: var(--oa-radius); background: var(--oa-card);
    box-shadow: 0 1px 2px rgba(16, 24, 40, 0.04);
}
.exp-oauth .exp-card.is-revoked, .exp-oauth .exp-card.is-expired { background: var(--oa-soft); }
.exp-oauth .exp-card-head { display: flex; flex-wrap: wrap; align-items: flex-start; justify-content: space-between; gap: 10px 16px; }
.exp-oauth .exp-card-title { display: flex; flex-wrap: wrap; align-items: center; gap: 6px 10px; min-width: 0; }
.exp-oauth .exp-card-title input[type="checkbox"] { width: 18px; height: 18px; margin: 0; accent-color: var(--oa-accent); }
.exp-oauth .exp-badges { display: inline-flex; flex-wrap: wrap; gap: 6px; margin: 0; padding: 0; list-style: none; }
.exp-oauth .exp-badge { display: inline-flex; align-items: center; gap: 5px; margin: 0; padding: 2px 9px; border-radius: 999px; font-size: 12px; font-weight: 650; line-height: 1.5; white-space: nowrap; background: var(--oa-soft); color: var(--oa-muted); }
.exp-oauth .exp-card.is-revoked .exp-badge, .exp-oauth .exp-card.is-expired .exp-badge { background: #e8eaee; color: #4b5260; }
.exp-oauth .exp-badge.is-ok { background: var(--oa-ok-bg); color: var(--oa-ok); }
.exp-oauth .exp-badge.is-warn { background: var(--oa-warn-bg); color: var(--oa-warn); }
.exp-oauth .exp-badge.is-bad { background: var(--oa-bad-bg); color: var(--oa-bad); }
.exp-oauth .exp-badge.is-info { background: var(--oa-info-bg); color: var(--oa-info); }
.exp-oauth .exp-facts { display: grid; grid-template-columns: repeat(auto-fit, minmax(min(100%, 170px), 1fr)); gap: 8px 18px; margin: 12px 0 0; }
.exp-oauth .exp-facts > div { min-width: 0; }
.exp-oauth .exp-facts dt { margin: 0; font-size: 12px; font-weight: 650; text-transform: uppercase; letter-spacing: .04em; color: var(--oa-muted); }
.exp-oauth .exp-facts dd { margin: 2px 0 0; overflow-wrap: anywhere; }
.exp-oauth .exp-secret { display: flex; flex-wrap: wrap; align-items: center; gap: 8px; margin: 2px 0 0; }
.exp-oauth .exp-secret code, .exp-oauth .exp-value {
    flex: 1 1 260px; min-width: 0; padding: 6px 10px; border: 1px solid var(--oa-line); border-radius: 8px; background: var(--oa-soft);
    color: var(--oa-ink); overflow-wrap: anywhere; user-select: all; -webkit-user-select: all;
}
.exp-oauth details.exp-reveal > summary { display: inline-flex; align-items: center; min-height: 30px; padding: 4px 10px; border: 1px solid #c9ced6; border-radius: 9px; background: #fff; color: var(--oa-ink); font-size: 12.5px; font-weight: 600; cursor: pointer; list-style: none; }
.exp-oauth details.exp-reveal > summary::-webkit-details-marker { display: none; }
.exp-oauth details.exp-reveal[open] > summary { margin-bottom: 8px; }
.exp-oauth .exp-empty { margin: 0; padding: 18px 16px; border: 1px dashed #c9ced6; border-radius: var(--oa-radius); color: var(--oa-muted); text-align: center; }

/* Tables */
.exp-oauth .exp-table-wrap { overflow-x: auto; border: 1px solid var(--oa-line); border-radius: var(--oa-radius); background: var(--oa-card); }
.exp-oauth .exp-table { width: 100%; margin: 0; border-collapse: collapse; }
.exp-oauth .exp-table th, .exp-oauth .exp-table td { padding: 9px 12px; border: 0; border-bottom: 1px solid var(--oa-line); text-align: left; vertical-align: top; background: transparent; color: var(--oa-ink); }
.exp-oauth .exp-table th { font-size: 12px; font-weight: 650; text-transform: uppercase; letter-spacing: .04em; color: var(--oa-muted); background: var(--oa-soft); white-space: nowrap; }
.exp-oauth .exp-table tr:last-child td { border-bottom: 0; }
.exp-oauth .exp-table .exp-num { text-align: right; font-variant-numeric: tabular-nums; white-space: nowrap; }

/* Key rows: a table on wide screens, cards on narrow ones */
.exp-oauth .exp-keys { margin: 0; padding: 0; list-style: none; border: 1px solid var(--oa-line); border-radius: var(--oa-radius); background: var(--oa-card); }
.exp-oauth .exp-key { display: grid; grid-template-columns: 28px minmax(0, 2.2fr) minmax(0, 1.4fr) minmax(0, 1.2fr) minmax(0, 1.3fr) minmax(0, 1.1fr) 92px; gap: 6px 14px; align-items: start; margin: 0; padding: 12px 14px; border-top: 1px solid var(--oa-line); }
.exp-oauth .exp-key:first-child { border-top: 0; }
.exp-oauth .exp-key.is-head { padding-top: 9px; padding-bottom: 9px; background: var(--oa-soft); border-radius: var(--oa-radius) var(--oa-radius) 0 0; font-size: 12px; font-weight: 650; text-transform: uppercase; letter-spacing: .04em; color: var(--oa-muted); }
.exp-oauth .exp-key.is-revoked, .exp-oauth .exp-key.is-expired { background: var(--oa-soft); }
.exp-oauth .exp-key input[type="checkbox"] { width: 18px; height: 18px; margin: 2px 0 0; accent-color: var(--oa-accent); }
.exp-oauth .exp-key-name { font-weight: 650; overflow-wrap: anywhere; }
.exp-oauth .exp-key-cell { min-width: 0; overflow-wrap: anywhere; }
.exp-oauth .exp-key-cell .exp-cell-label { display: none; }
.exp-oauth .exp-key-actions { display: flex; justify-content: flex-end; }
@media (max-width: 900px) {
    .exp-oauth .exp-key { grid-template-columns: 28px repeat(2, minmax(0, 1fr)); gap: 10px 14px; }
    .exp-oauth .exp-key.is-head { display: none; }
    .exp-oauth .exp-key > :first-child { grid-row: 1 / span 4; }
    .exp-oauth .exp-key .exp-key-main, .exp-oauth .exp-key .exp-key-actions { grid-column: 2 / -1; }
    .exp-oauth .exp-key .exp-key-cell .exp-cell-label { display: block; font-size: 11.5px; font-weight: 650; text-transform: uppercase; letter-spacing: .04em; color: var(--oa-muted); }
    .exp-oauth .exp-key .exp-key-main .exp-cell-label { display: none; }
    .exp-oauth .exp-key .exp-key-actions { justify-content: flex-start; }
}
@media (max-width: 520px) {
    .exp-oauth .exp-key { grid-template-columns: 24px minmax(0, 1fr); }
    .exp-oauth .exp-key > :first-child { grid-row: 1 / span 6; }
    .exp-oauth .exp-key > :not(:first-child) { grid-column: 2; }
}

.exp-oauth .exp-pager { margin: 14px 0 0; }
/* The shared page navigator, in this page's colours: its own grey and orange fall short of 4.5:1 on white. */
.exp-oauth .exp-pager a { color: var(--oa-accent-hover); }
.exp-oauth .exp-pager span.text, .exp-oauth .exp-pager span.text a { color: var(--oa-accent-hover); }
.exp-oauth .exp-pager span.disabled, .exp-oauth .exp-pager span.text.disabled { color: var(--oa-muted); }
.exp-oauth .exp-pager span.current { color: var(--oa-ink); }

/* Confirmation */
.exp-oauth .exp-confirm { margin: 0 0 18px; padding: 16px; border: 1px solid #f1b4b4; border-left: 4px solid var(--oa-bad); border-radius: var(--oa-radius); background: var(--oa-card); }
.exp-oauth .exp-confirm h2 { margin: 0 0 6px; }
.exp-oauth .exp-confirm ul { margin: 10px 0 14px; padding-left: 20px; }
.exp-oauth .exp-confirm li { margin: 0 0 4px; overflow-wrap: anywhere; }

/* Edit form */
.exp-oauth .exp-form { display: grid; grid-template-columns: minmax(0, 1fr); gap: 16px; max-width: 760px; margin: 0 0 18px; padding: 16px; border: 1px solid var(--oa-line); border-radius: var(--oa-radius); background: var(--oa-card); }

@media (max-width: 600px) {
    .exp-oauth .exp-card { padding: 12px; }
    .exp-oauth .exp-bar .exp-actions, .exp-oauth .exp-bar .exp-btn { width: 100%; }
    .exp-oauth .exp-tabs a { padding: 8px 10px; }
}
</style>
{/literal}
