{* The look of Setup > Preload (setup/preload.tpl), in the visual language of the cronjobs, sessions and sections
   pages. The same file is in design/admin and design/admin4, so every administration design resolves it.
   Everything is scoped to .exp-preload and takes admin4's tokens where they exist (--a4-*), with values of its own
   for the older designs. Guide: doc/guides/preloading-caches.md *}
{literal}
<style>
.exp-preload {
    --pl-ink: var(--a4-ink, #1f2430);
    --pl-muted: var(--a4-muted, #5d6573);
    --pl-line: var(--a4-line, #e3e6eb);
    --pl-soft: var(--a4-soft, #f6f7f9);
    --pl-card: #fff;
    --pl-accent: #c2410c;          /* white text on it is 5.2:1 */
    --pl-accent-hover: #9a3412;
    --pl-ring: rgba(194, 65, 12, 0.45);
    --pl-ok: #166534;   --pl-ok-bg: #e7f5ea;
    --pl-warn: #8a4b00; --pl-warn-bg: #fff3df;
    --pl-bad: #b91c1c;  --pl-bad-bg: #fdecec;
    --pl-info: #1e4fa8; --pl-info-bg: #e8effd;
    --pl-radius: 12px;
    --pl-mono: ui-monospace, SFMono-Regular, Menlo, Consolas, monospace;
    color: var(--pl-ink);
    font-size: 14px;
    line-height: 1.5;
}
.exp-preload *, .exp-preload *::before, .exp-preload *::after { box-sizing: border-box; }
.exp-preload [hidden] { display: none !important; }
.exp-preload .box-content { padding-bottom: 24px; }
.exp-preload h1.context-title { margin: 0; }
.exp-preload h2.exp-h2 { margin: 0; padding: 0; border: 0; background: none; font-size: 16px; font-weight: 650; color: var(--pl-ink); }
.exp-preload h3 { margin: 0; padding: 0; border: 0; background: none; font-size: 15px; font-weight: 650; color: var(--pl-ink); }
.exp-preload p { margin: 0; }
.exp-preload code { font-family: var(--pl-mono); font-size: 12.5px; overflow-wrap: anywhere; }
.exp-preload a { color: var(--pl-accent-hover); }
.exp-preload a:hover { color: var(--pl-ink); }
.exp-preload :focus-visible { outline: 3px solid var(--pl-ring); outline-offset: 2px; }
.exp-preload .exp-sr { position: absolute; width: 1px; height: 1px; margin: -1px; padding: 0; overflow: hidden; clip: rect(0 0 0 0); white-space: nowrap; border: 0; }
.exp-preload .exp-muted { color: var(--pl-muted); }
.exp-preload .exp-nowrap { white-space: nowrap; }
.exp-preload .exp-intro { margin: 10px 0 18px; max-width: 78ch; color: var(--pl-muted); }
.exp-preload .exp-section { margin: 0 0 24px; }
.exp-preload .exp-section-head { display: flex; flex-wrap: wrap; align-items: baseline; gap: 4px 12px; margin: 0 0 10px; }
.exp-preload .exp-section-head p { flex: 1 1 100%; color: var(--pl-muted); max-width: 78ch; }
.exp-preload ul.exp-plain { margin: 0; padding: 0; list-style: none; }

/* Messages */
.exp-preload .exp-feedback { margin: 0 0 14px; padding: 10px 14px; border: 1px solid; border-left-width: 4px; border-radius: 10px; }
.exp-preload .exp-feedback.is-ok { border-color: #b7dfc1; border-left-color: var(--pl-ok); background: var(--pl-ok-bg); color: var(--pl-ok); }
.exp-preload .exp-feedback.is-bad { border-color: #f1b4b4; border-left-color: var(--pl-bad); background: var(--pl-bad-bg); color: var(--pl-bad); }
.exp-preload .exp-feedback.is-warn { border-color: #f3d19c; border-left-color: var(--pl-warn); background: var(--pl-warn-bg); color: var(--pl-warn); }
.exp-preload .exp-feedback.is-info { border-color: #bcd0f5; border-left-color: var(--pl-info); background: var(--pl-info-bg); color: var(--pl-info); }

/* What is running now */
.exp-preload .exp-statusbar {
    display: flex; flex-wrap: wrap; align-items: center; justify-content: space-between; gap: 12px 20px;
    margin: 0 0 18px; padding: 14px 16px; border: 1px solid var(--pl-line); border-radius: var(--pl-radius); background: var(--pl-soft);
}
.exp-preload .exp-status { display: flex; flex-direction: column; gap: 4px; min-width: 0; flex: 1 1 320px; }
.exp-preload .exp-status-line { display: flex; flex-wrap: wrap; align-items: center; gap: 6px 16px; min-width: 0; }
.exp-preload .exp-pill { display: inline-flex; align-items: center; gap: 8px; font-weight: 700; }
.exp-preload .exp-dot { flex: 0 0 auto; width: 10px; height: 10px; border-radius: 50%; background: #8a919c; }
.exp-preload .exp-pill.is-running { color: var(--pl-ok); }
.exp-preload .exp-pill.is-running .exp-dot { background: var(--pl-ok); box-shadow: 0 0 0 4px rgba(22, 101, 52, 0.18); animation: exp-pl-pulse 1.4s ease-in-out infinite; }
@keyframes exp-pl-pulse { 0%, 100% { opacity: 1; } 50% { opacity: .35; } }
@media (prefers-reduced-motion: reduce) { .exp-preload .exp-pill.is-running .exp-dot { animation: none; } }
.exp-preload .exp-meta { color: var(--pl-muted); font-size: 13px; }
.exp-preload .exp-current { font-family: var(--pl-mono); font-size: 12.5px; color: var(--pl-muted); overflow-wrap: anywhere; }
.exp-preload .exp-progress { position: relative; height: 8px; margin: 4px 0 2px; border-radius: 999px; background: var(--pl-line); overflow: hidden; max-width: 520px; }
.exp-preload .exp-progress span { display: block; height: 100%; border-radius: 999px; background: var(--pl-ok); transition: width .4s ease; }
.exp-preload .exp-actions { display: flex; flex-wrap: wrap; align-items: center; gap: 8px; }

/* Buttons */
.exp-preload .exp-btn {
    display: inline-flex; align-items: center; justify-content: center; gap: 6px; min-height: 36px; margin: 0; max-width: 100%;
    padding: 6px 14px; border: 1px solid #c9ced6; border-radius: 9px; background: #fff; color: var(--pl-ink);
    font: 600 13.5px/1.2 inherit; font-family: inherit; cursor: pointer; white-space: nowrap; text-decoration: none;
}
.exp-preload a.exp-btn { color: var(--pl-ink); }
.exp-preload .exp-btn:hover:not([disabled]) { border-color: var(--pl-accent); color: var(--pl-accent-hover); }
.exp-preload .exp-btn[disabled] { opacity: .55; cursor: not-allowed; }
.exp-preload .exp-btn-primary { border-color: var(--pl-accent); background: var(--pl-accent); color: #fff; }
.exp-preload .exp-btn-primary:hover:not([disabled]) { border-color: var(--pl-accent-hover); background: var(--pl-accent-hover); color: #fff; }
.exp-preload .exp-btn-outline-danger { border-color: var(--pl-bad); color: var(--pl-bad); }
.exp-preload .exp-btn-outline-danger:hover:not([disabled]) { background: var(--pl-bad); border-color: var(--pl-bad); color: #fff; }
.exp-preload .exp-btn-small { min-height: 30px; padding: 4px 10px; font-size: 12.5px; }

/* The form */
.exp-preload .exp-card { margin: 0 0 18px; padding: 16px; border: 1px solid var(--pl-line); border-radius: var(--pl-radius); background: var(--pl-card); }
.exp-preload .exp-card > .exp-section-head { margin-bottom: 12px; }
.exp-preload .exp-fields { display: grid; grid-template-columns: minmax(0, 2fr) repeat(2, minmax(0, 1fr)); gap: 14px 16px; align-items: start; }
.exp-preload .exp-field { display: flex; flex-direction: column; gap: 5px; min-width: 0; margin: 0; padding: 0; border: 0; }
.exp-preload .exp-field > label, .exp-preload .exp-field > .exp-label { padding: 0; font-size: 12.5px; font-weight: 650; color: var(--pl-ink); }
.exp-preload .exp-field select, .exp-preload .exp-field input[type="number"] {
    width: 100%; min-height: 38px; margin: 0; padding: 6px 10px; border: 1px solid #8f96a3; border-radius: 9px;
    background: #fff; color: var(--pl-ink); font: inherit; font-size: 14px; box-shadow: none;
}
.exp-preload .exp-field select:focus, .exp-preload .exp-field input:focus { border-color: var(--pl-accent); outline: 3px solid var(--pl-ring); outline-offset: 0; }
.exp-preload .exp-field-wide { grid-column: 1 / -1; }
.exp-preload .exp-help { font-size: 12.5px; color: var(--pl-muted); }
.exp-preload .exp-check { display: flex; align-items: flex-start; gap: 10px; margin: 0; padding: 10px 12px; border: 1px solid var(--pl-line); border-radius: 10px; background: var(--pl-soft); cursor: pointer; font-weight: 400; }
.exp-preload .exp-check input { flex: 0 0 auto; width: 18px; height: 18px; margin: 2px 0 0; accent-color: var(--pl-accent); }
.exp-preload .exp-check > span { flex: 1 1 auto; min-width: 0; font-weight: 400; }
.exp-preload .exp-check, .exp-preload .exp-check * { white-space: normal; overflow-wrap: anywhere; }
.exp-preload label.exp-check { padding: 10px 12px; }
.exp-preload .exp-check strong { display: block; font-weight: 650; }
.exp-preload .exp-check .exp-help { display: block; margin-top: 2px; }
.exp-preload .exp-formbar { display: flex; flex-wrap: wrap; align-items: center; gap: 10px 12px; margin: 16px 0 0; padding: 12px 0 0; border-top: 1px solid var(--pl-line); }
.exp-preload .exp-formbar .exp-meta { flex: 1 1 260px; }

/* Facts, address lists, command lines */
.exp-preload .exp-facts { display: grid; grid-template-columns: repeat(auto-fit, minmax(min(100%, 170px), 1fr)); gap: 8px 18px; margin: 0; }
.exp-preload .exp-facts > div { min-width: 0; }
.exp-preload .exp-facts dt { margin: 0; font-size: 12px; font-weight: 650; text-transform: uppercase; letter-spacing: .04em; color: var(--pl-muted); }
.exp-preload .exp-facts dd { margin: 2px 0 0; overflow-wrap: anywhere; }
.exp-preload .exp-urls { margin: 8px 0 0; padding: 0; list-style: none; }
.exp-preload .exp-urls li { padding: 4px 0; border-top: 1px solid var(--pl-line); font-family: var(--pl-mono); font-size: 12.5px; overflow-wrap: anywhere; }
.exp-preload .exp-urls li:first-child { border-top: 0; }
.exp-preload pre.exp-code { margin: 8px 0 0; padding: 10px 12px; overflow-x: auto; border: 1px solid var(--pl-line); border-radius: 9px; background: var(--pl-soft); color: var(--pl-ink); font: 12.5px/1.6 var(--pl-mono); white-space: pre; }
.exp-preload .exp-columns { display: grid; grid-template-columns: repeat(auto-fit, minmax(min(100%, 340px), 1fr)); gap: 0 18px; }
.exp-preload .exp-columns > .exp-card { min-width: 0; }
.exp-preload .exp-card ul.exp-points { margin: 6px 0 0; padding-left: 20px; }
.exp-preload .exp-card ul.exp-points li + li { margin-top: 4px; }
.exp-preload .exp-card p + p { margin-top: 8px; }

/* Badges */
.exp-preload .exp-badge { display: inline-flex; align-items: center; gap: 5px; margin: 0; padding: 2px 9px; border-radius: 999px; font-size: 12px; font-weight: 650; line-height: 1.5; white-space: nowrap; background: var(--pl-soft); color: var(--pl-muted); }
.exp-preload .exp-badge.is-ok { background: var(--pl-ok-bg); color: var(--pl-ok); }
.exp-preload .exp-badge.is-warn { background: var(--pl-warn-bg); color: var(--pl-warn); }
.exp-preload .exp-badge.is-bad { background: var(--pl-bad-bg); color: var(--pl-bad); }
.exp-preload .exp-badge.is-info { background: var(--pl-info-bg); color: var(--pl-info); }

/* Overview figures of a run */
.exp-preload .exp-figures { display: grid; grid-template-columns: repeat(auto-fit, minmax(min(100%, 120px), 1fr)); gap: 10px; margin: 0 0 14px; padding: 0; list-style: none; }
.exp-preload .exp-figure { display: flex; flex-direction: column; gap: 2px; margin: 0; padding: 10px 12px; border: 1px solid var(--pl-line); border-radius: var(--pl-radius); background: var(--pl-card); }
.exp-preload .exp-figure strong { font-size: 20px; line-height: 1.15; font-weight: 700; font-variant-numeric: tabular-nums; color: var(--pl-ink); }
.exp-preload .exp-figure span { font-size: 12.5px; color: var(--pl-muted); }
.exp-preload .exp-figure.is-attention strong { color: var(--pl-bad); }

/* The live output */
.exp-preload .exp-console {
    height: 22em; margin: 0; padding: 10px 12px; overflow: auto; border: 1px solid #2b3240; border-radius: 10px;
    background: #141922; color: #d8dde6; font: 12px/1.55 var(--pl-mono); white-space: pre-wrap; word-break: break-word;
}
.exp-preload .exp-console .is-phase, .exp-preload .exp-console .is-done { color: #8fd3ff; font-weight: 700; }
.exp-preload .exp-console .is-ok, .exp-preload .exp-console .is-phase-item { color: #a7e08a; }
.exp-preload .exp-console .is-image { color: #b7c3d4; }
.exp-preload .exp-console .is-warn { color: #f0cf72; }
.exp-preload .exp-console .is-error { color: #ff9c92; }
.exp-preload .exp-console .is-info { color: #aab2bf; }
.exp-preload .exp-console .is-report { color: #ffdcdc; }

/* Tables */
.exp-preload .exp-table-wrap { overflow-x: auto; border: 1px solid var(--pl-line); border-radius: var(--pl-radius); background: var(--pl-card); }
.exp-preload .exp-table { width: 100%; margin: 0; border-collapse: collapse; }
.exp-preload .exp-table th, .exp-preload .exp-table td { padding: 9px 12px; border: 0; border-bottom: 1px solid var(--pl-line); text-align: left; vertical-align: top; background: transparent; color: var(--pl-ink); }
.exp-preload .exp-table th { font-size: 12px; font-weight: 650; text-transform: uppercase; letter-spacing: .04em; color: var(--pl-muted); background: var(--pl-soft); }
.exp-preload .exp-table .exp-url-text { overflow-wrap: anywhere; }
.exp-preload .exp-table td.exp-run { min-width: 16em; }
.exp-preload .exp-table tr:last-child > td { border-bottom: 0; }
.exp-preload .exp-table td.exp-num { text-align: right; font-variant-numeric: tabular-nums; white-space: nowrap; }
.exp-preload .exp-table th.exp-num { text-align: right; }
.exp-preload .exp-table td.exp-url { font-family: var(--pl-mono); font-size: 12.5px; overflow-wrap: anywhere; word-break: break-word; min-width: 14em; }
.exp-preload .exp-table tr.exp-detail-row > td { background: var(--pl-soft); }
.exp-preload details.exp-failures > summary { cursor: pointer; font-weight: 650; color: var(--pl-bad); }
.exp-preload details.exp-failures .exp-table-wrap { margin-top: 8px; }
.exp-preload .exp-empty { margin: 0; padding: 18px 16px; border: 1px dashed #c9ced6; border-radius: var(--pl-radius); color: var(--pl-muted); text-align: center; }

@media (max-width: 900px) {
    .exp-preload .exp-fields { grid-template-columns: minmax(0, 1fr) minmax(0, 1fr); }
    .exp-preload .exp-fields .exp-field-site { grid-column: 1 / -1; }
}
@media (max-width: 600px) {
    .exp-preload .exp-fields { grid-template-columns: minmax(0, 1fr); }
    .exp-preload .exp-btn { white-space: normal; text-align: center; }
    .exp-preload .exp-formbar .exp-btn, .exp-preload .exp-statusbar .exp-btn { flex: 1 1 auto; }
    .exp-preload .exp-table th, .exp-preload .exp-table td { padding: 8px 9px; }
}
</style>
{/literal}
