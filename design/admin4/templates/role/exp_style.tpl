{* The look of the role pages (role/list, role/view, role/edit, the policy wizard, role/policyedit, role/assign,
   role/copy) and of the unactivated users (user/unactivated), in the visual language of the section, link and
   class pages. Included once by each of them.

   The same file is in design/admin and design/admin4, so every administration design resolves it. Everything is
   scoped to .exp-roles and takes admin4's tokens where they exist (--a4-*), with values of its own for the older
   designs; in admin4's dark mode the cards stay white, as on the other redesigned pages. No shared stylesheet is
   changed. Guide: doc/guides/roles-and-policies.md *}
{literal}
<style>
.exp-roles {
    --rl-ink: var(--a4-ink, #1f2430);
    --rl-muted: var(--a4-muted, #5d6573);
    --rl-line: var(--a4-line, #e3e6eb);
    --rl-soft: var(--a4-soft, #f6f7f9);
    --rl-card: #fff;
    --rl-accent: #c2410c;          /* white text on it is 5.2:1 */
    --rl-accent-hover: #9a3412;
    --rl-ring: rgba(194, 65, 12, 0.45);
    --rl-ok: #166534;   --rl-ok-bg: #e7f5ea;
    --rl-warn: #8a4b00; --rl-warn-bg: #fff3df;
    --rl-bad: #b91c1c;  --rl-bad-bg: #fdecec;
    --rl-info: #1e4fa8; --rl-info-bg: #e8effd;
    --rl-radius: 12px;
    --rl-mono: ui-monospace, SFMono-Regular, Menlo, Consolas, monospace;
    color: var(--rl-ink);
    font-size: 14px;
    line-height: 1.5;
}
.exp-roles *, .exp-roles *::before, .exp-roles *::after { box-sizing: border-box; }
.exp-roles [hidden] { display: none !important; }
.exp-roles .box-content { padding-bottom: 20px; }
.exp-roles h1.context-title { margin: 0; overflow-wrap: anywhere; }
.exp-roles h2.exp-h2 { margin: 0; padding: 0; border: 0; background: none; font-size: 16px; font-weight: 650; color: var(--rl-ink); }
.exp-roles h3 { overflow-wrap: anywhere; margin: 0; padding: 0; border: 0; background: none; font-size: 15px; font-weight: 650; color: var(--rl-ink); }
.exp-roles p { margin: 0; }
.exp-roles code { font-family: var(--rl-mono); font-size: 12.5px; overflow-wrap: anywhere; }
.exp-roles a { color: var(--rl-accent-hover); }
.exp-roles a:hover { color: var(--rl-ink); }
.exp-roles :focus-visible { outline: 3px solid var(--rl-ring); outline-offset: 2px; }
.exp-roles .exp-sr { position: absolute; width: 1px; height: 1px; margin: -1px; padding: 0; overflow: hidden; clip: rect(0 0 0 0); white-space: nowrap; border: 0; }
.exp-roles .exp-muted { color: var(--rl-muted); }
.exp-roles ul.exp-plain { margin: 0; padding: 0; list-style: none; }

/* A page drawn without the admin's main card (the edit view) gets one of its own */
.exp-roles.exp-standalone { max-width: 980px; margin: 16px auto 24px; padding: 18px 20px; border-radius: 18px; background: var(--rl-card);
    box-shadow: 0 0 0 1px rgba(16, 24, 40, 0.06), 0 12px 32px -6px rgba(16, 24, 40, 0.12); }
#maincontent .exp-roles.exp-standalone { max-width: none; margin: 0; padding: 0; border-radius: 0; background: transparent; box-shadow: none; }

.exp-roles .exp-title-row { display: flex; flex-wrap: wrap; align-items: center; gap: 6px 12px; }
.exp-roles .exp-title-key { color: var(--rl-muted); }
.exp-roles .exp-intro { margin: 8px 0 18px; max-width: 78ch; color: var(--rl-muted); }
.exp-roles .exp-section { margin: 0 0 24px; }
.exp-roles .exp-section-head { display: flex; flex-wrap: wrap; align-items: baseline; gap: 4px 12px; margin: 0 0 10px; }
.exp-roles .exp-section-head p { flex: 1 1 100%; color: var(--rl-muted); max-width: 78ch; }

/* Messages */
.exp-roles .exp-feedback { margin: 0 0 14px; padding: 10px 14px; border: 1px solid; border-left-width: 4px; border-radius: 10px; }
.exp-roles .exp-feedback.is-ok { border-color: #b7dfc1; border-left-color: var(--rl-ok); background: var(--rl-ok-bg); color: var(--rl-ok); }
.exp-roles .exp-feedback.is-bad { border-color: #f1b4b4; border-left-color: var(--rl-bad); background: var(--rl-bad-bg); color: var(--rl-bad); }
.exp-roles .exp-feedback.is-warn { border-color: #f3d19c; border-left-color: var(--rl-warn); background: var(--rl-warn-bg); color: var(--rl-warn); }
.exp-roles .exp-feedback.is-info { border-color: #bcd0f5; border-left-color: var(--rl-info); background: var(--rl-info-bg); color: var(--rl-info); }
.exp-roles .exp-feedback strong { color: inherit; }
.exp-roles .exp-feedback p + p, .exp-roles .exp-feedback p + ul, .exp-roles .exp-feedback ul + p { margin-top: 6px; }
.exp-roles .exp-feedback ul { margin: 6px 0 0; padding-left: 20px; }
.exp-roles .exp-feedback h2.exp-h2 { color: inherit; }
.exp-roles .exp-reasons { margin-top: 8px; }
.exp-roles .exp-reasons li + li { margin-top: 4px; }

/* Overview figures */
.exp-roles .exp-figures { display: grid; grid-template-columns: repeat(auto-fit, minmax(min(100%, 120px), 1fr)); gap: 10px; margin: 0 0 20px; padding: 0; list-style: none; }
.exp-roles .exp-figure { display: flex; flex-direction: column; gap: 2px; margin: 0; padding: 12px 14px; border: 1px solid var(--rl-line); border-radius: var(--rl-radius); background: var(--rl-card); }
.exp-roles .exp-figure strong { font-size: 22px; line-height: 1.15; font-weight: 700; font-variant-numeric: tabular-nums; color: var(--rl-ink); }
.exp-roles .exp-figure span { font-size: 12.5px; color: var(--rl-muted); }
.exp-roles .exp-figure.is-attention strong { color: var(--rl-bad); }

/* Buttons */
.exp-roles .exp-btn {
    display: inline-flex; align-items: center; justify-content: center; gap: 6px; min-height: 36px; margin: 0;
    padding: 6px 14px; border: 1px solid #c9ced6; border-radius: 9px; background: #fff; color: var(--rl-ink);
    font: 600 13.5px/1.2 inherit; font-family: inherit; cursor: pointer; white-space: nowrap; text-decoration: none;
}
.exp-roles a.exp-btn { color: var(--rl-ink); }
.exp-roles .exp-btn:hover:not([disabled]):not(.is-disabled) { border-color: var(--rl-accent); color: var(--rl-accent-hover); }
.exp-roles .exp-btn[disabled], .exp-roles .exp-btn.is-disabled { opacity: .55; cursor: not-allowed; }
.exp-roles .exp-btn-primary, .exp-roles a.exp-btn-primary { border-color: var(--rl-accent); background: var(--rl-accent); color: #fff; }
.exp-roles .exp-btn-primary:hover:not([disabled]) { border-color: var(--rl-accent-hover); background: var(--rl-accent-hover); color: #fff; }
.exp-roles .exp-btn-danger { border-color: var(--rl-bad); background: var(--rl-bad); color: #fff; }
.exp-roles .exp-btn-danger:hover:not([disabled]) { border-color: #7f1d1d; background: #7f1d1d; color: #fff; }
.exp-roles .exp-btn-outline-danger { border-color: var(--rl-bad); color: var(--rl-bad); }
.exp-roles .exp-btn-outline-danger:hover:not([disabled]) { background: var(--rl-bad); border-color: var(--rl-bad); color: #fff; }
.exp-roles .exp-btn-small { min-height: 30px; padding: 4px 10px; font-size: 12.5px; }
.exp-roles .exp-btn svg { flex: 0 0 auto; }
.exp-roles .exp-btn { max-width: 100%; }
@media (max-width: 600px) { .exp-roles .exp-btn { white-space: normal; text-align: center; } }
.exp-roles .exp-actions { display: flex; flex-wrap: wrap; align-items: center; gap: 8px; }
.exp-roles .exp-actions form { display: contents; }
.exp-roles .exp-actionbar { display: flex; flex-wrap: wrap; align-items: center; justify-content: space-between; gap: 10px 16px;
    margin: 0 0 18px; padding: 12px 14px; border: 1px solid var(--rl-line); border-radius: var(--rl-radius); background: var(--rl-soft); }
.exp-roles .exp-actionbar .exp-meta { flex: 1 1 260px; }
.exp-roles .exp-meta { color: var(--rl-muted); font-size: 13px; }

/* Controls */
.exp-roles .exp-toolbar {
    display: grid; grid-template-columns: repeat(auto-fit, minmax(min(100%, 230px), 1fr)); gap: 14px 16px; align-items: end;
    margin: 0 0 20px; padding: 16px; border: 1px solid var(--rl-line); border-radius: var(--rl-radius); background: var(--rl-card);
}
.exp-roles .exp-field { display: flex; flex-direction: column; gap: 5px; min-width: 0; margin: 0; padding: 0; border: 0; }
.exp-roles fieldset.exp-field { background: none; box-shadow: none; border-radius: 0; }
.exp-roles fieldset.exp-field > legend { float: left; width: 100%; margin: 0 0 5px; padding: 0; background: none; }
.exp-roles fieldset.exp-field > legend + * { clear: both; }
.exp-roles .exp-field > label, .exp-roles .exp-field > legend { padding: 0; font-size: 12.5px; font-weight: 650; color: var(--rl-ink); }
.exp-roles .exp-field select,
.exp-roles .exp-field input[type="search"],
.exp-roles .exp-field input[type="text"] {
    width: 100%; min-height: 38px; margin: 0; padding: 6px 10px; border: 1px solid #8f96a3; border-radius: 9px;
    background: #fff; color: var(--rl-ink); font: inherit; font-size: 14px; box-shadow: none;
}
.exp-roles .exp-field select:focus, .exp-roles .exp-field input:focus { border-color: var(--rl-accent); outline: 3px solid var(--rl-ring); outline-offset: 0; }
.exp-roles .exp-field input[aria-invalid="true"] { border-color: var(--rl-bad); box-shadow: inset 0 0 0 1px var(--rl-bad); }
.exp-roles .exp-field-wide { grid-column: 1 / -1; }
.exp-roles .exp-form-fields { display: grid; grid-template-columns: minmax(0, 1fr); gap: 18px; max-width: 640px; }
.exp-roles .exp-help { font-size: 13px; color: var(--rl-muted); max-width: 72ch; }
.exp-roles .exp-field-error { font-size: 13px; font-weight: 650; color: var(--rl-bad); }
.exp-roles .exp-chips { display: flex; flex-wrap: wrap; gap: 6px; }
.exp-roles .exp-chip { position: relative; display: inline-flex; margin: 0; }
.exp-roles .exp-chip input { position: absolute; opacity: 0; width: 1px; height: 1px; }
.exp-roles .exp-chip span { display: inline-flex; align-items: center; min-height: 32px; padding: 4px 12px; border: 1px solid #c9ced6; border-radius: 999px; background: #fff; color: var(--rl-ink); font-size: 13px; font-weight: 400; cursor: pointer; }
.exp-roles .exp-chip input:checked + span { border-color: var(--rl-accent); background: var(--rl-accent); color: #fff; font-weight: 650; }
.exp-roles .exp-chip input:focus-visible + span { outline: 3px solid var(--rl-ring); outline-offset: 2px; }
.exp-roles .exp-filter-count { grid-column: 1 / -1; margin: 0; font-size: 13px; color: var(--rl-muted); }

/* The section cards */
.exp-roles .exp-cards { display: grid; grid-template-columns: minmax(0, 1fr); gap: 12px; margin: 0; padding: 0; list-style: none; }
.exp-roles .exp-card {
    min-width: 0; margin: 0; padding: 14px 16px; border: 1px solid var(--rl-line); border-radius: var(--rl-radius); background: var(--rl-card);
    box-shadow: 0 1px 2px rgba(16, 24, 40, 0.04);
}
.exp-roles .exp-card.is-attention { box-shadow: inset 4px 0 0 var(--rl-warn), 0 1px 2px rgba(16, 24, 40, 0.04); }
.exp-roles .exp-card.is-selected { border-color: var(--rl-accent); }
.exp-roles .exp-card-head { display: flex; flex-wrap: wrap; align-items: flex-start; justify-content: space-between; gap: 10px 16px; }
.exp-roles .exp-card-title { display: flex; flex-wrap: wrap; align-items: center; gap: 6px 10px; min-width: 0; flex: 1 1 320px; }
.exp-roles .exp-card-title h3 a { color: var(--rl-ink); text-decoration: none; }
.exp-roles .exp-card-title h3 a:hover { color: var(--rl-accent-hover); text-decoration: underline; }
.exp-roles .exp-select { display: inline-flex; align-items: center; justify-content: center; width: 32px; height: 32px; margin: -4px 0 -4px -6px; border-radius: 8px; cursor: pointer; }
.exp-roles .exp-select:hover { background: var(--rl-soft); }
.exp-roles .exp-select input { width: 18px; height: 18px; margin: 0; accent-color: var(--rl-accent); cursor: pointer; }
.exp-roles .exp-badges { display: inline-flex; flex-wrap: wrap; gap: 6px; margin: 0; padding: 0; list-style: none; }
.exp-roles .exp-badge { display: inline-flex; align-items: center; gap: 5px; margin: 0; padding: 2px 9px; border-radius: 999px; font-size: 12px; font-weight: 650; line-height: 1.5; white-space: nowrap; background: var(--rl-soft); color: var(--rl-muted); }
.exp-roles .exp-badge.is-ok { background: var(--rl-ok-bg); color: var(--rl-ok); }
.exp-roles .exp-badge.is-warn { background: var(--rl-warn-bg); color: var(--rl-warn); }
.exp-roles .exp-badge.is-bad { background: var(--rl-bad-bg); color: var(--rl-bad); }
.exp-roles .exp-badge.is-info { background: var(--rl-info-bg); color: var(--rl-info); }
.exp-roles .exp-facts { display: grid; grid-template-columns: repeat(auto-fit, minmax(min(100%, 165px), 1fr)); gap: 8px 18px; margin: 12px 0 0; }
.exp-roles .exp-facts > div { min-width: 0; }
.exp-roles .exp-facts dt { margin: 0; font-size: 12px; font-weight: 650; text-transform: uppercase; letter-spacing: .04em; color: var(--rl-muted); }
.exp-roles .exp-facts dd { margin: 2px 0 0; overflow-wrap: anywhere; }
.exp-roles .exp-facts dd code { color: var(--rl-muted); }
.exp-roles .exp-panel { margin: 0 0 18px; padding: 14px 16px; border: 1px solid var(--rl-line); border-radius: var(--rl-radius); background: var(--rl-card); }
.exp-roles .exp-panel > .exp-facts { margin-top: 0; }
.exp-roles .exp-fn { display: inline-block; margin: 0 4px 4px 0; padding: 1px 8px; border-radius: 6px; background: var(--rl-soft); color: var(--rl-ink); font: 12.5px/1.6 var(--rl-mono); white-space: nowrap; }
.exp-roles .exp-empty { margin: 0; padding: 18px 16px; border: 1px dashed #c9ced6; border-radius: var(--rl-radius); color: var(--rl-muted); text-align: center; }

/* Tables */
.exp-roles .exp-table-wrap { overflow-x: auto; border: 1px solid var(--rl-line); border-radius: var(--rl-radius); background: var(--rl-card); }
.exp-roles .exp-table { width: 100%; margin: 0; border-collapse: collapse; }
.exp-roles .exp-table th, .exp-roles .exp-table td { padding: 9px 12px; border: 0; border-bottom: 1px solid var(--rl-line); text-align: left; vertical-align: top; background: transparent; color: var(--rl-ink); }
.exp-roles .exp-table th { font-size: 12px; font-weight: 650; text-transform: uppercase; letter-spacing: .04em; color: var(--rl-muted); background: var(--rl-soft); white-space: nowrap; }
.exp-roles .exp-table tr:last-child td { border-bottom: 0; }
.exp-roles .exp-table .exp-num { text-align: right; font-variant-numeric: tabular-nums; white-space: nowrap; }
.exp-roles .exp-table img { vertical-align: -3px; }

/* Page size and pages */
.exp-roles .exp-listfoot { display: flex; flex-wrap: wrap; align-items: center; justify-content: space-between; gap: 10px 18px; margin: 14px 0 0; }
.exp-roles .exp-sizes { display: flex; flex-wrap: wrap; align-items: center; gap: 6px; margin: 0; font-size: 13px; color: var(--rl-muted); }
.exp-roles .exp-sizes a, .exp-roles .exp-sizes span.current { display: inline-flex; align-items: center; justify-content: center; min-width: 34px; min-height: 30px; padding: 2px 8px; border: 1px solid #c9ced6; border-radius: 8px; text-decoration: none; }
.exp-roles .exp-sizes span.current { border-color: var(--rl-accent); background: var(--rl-accent); color: #fff; font-weight: 650; }
.exp-roles .exp-pager { min-width: 0; }
.exp-roles .exp-pager .pagenavigator { margin: 0; }
.exp-roles .exp-pager a { color: var(--rl-accent-hover); }
.exp-roles .exp-pager span.text, .exp-roles .exp-pager span.text a { color: var(--rl-accent-hover); }
.exp-roles .exp-pager span.disabled, .exp-roles .exp-pager span.text.disabled { color: var(--rl-muted); }
.exp-roles .exp-pager span.current { color: var(--rl-ink); }

/* The buttons under the list */
.exp-roles .exp-bottombar { display: flex; flex-wrap: wrap; align-items: center; gap: 10px 12px; margin: 18px 0 0; padding: 12px 14px; border: 1px solid var(--rl-line); border-radius: var(--rl-radius); background: var(--rl-soft); }
.exp-roles .exp-bottombar .exp-meta { flex: 1 1 280px; }

/* Tabs of lists (all, valid, invalid ...) and orders: links, the current one marked */
.exp-roles .exp-tabs { display: flex; flex-wrap: wrap; gap: 6px; margin: 0; padding: 0; list-style: none; }
.exp-roles .exp-tabs a, .exp-roles .exp-tabs span.current { display: inline-flex; align-items: center; gap: 6px; min-height: 32px; padding: 4px 12px; border: 1px solid #c9ced6; border-radius: 999px; background: #fff; color: var(--rl-ink); font-size: 13px; text-decoration: none; }
.exp-roles .exp-tabs a:hover { border-color: var(--rl-accent); color: var(--rl-accent-hover); }
.exp-roles .exp-tabs span.current { border-color: var(--rl-accent); background: var(--rl-accent); color: #fff; font-weight: 650; }
.exp-roles .exp-tabs .exp-count { font-variant-numeric: tabular-nums; opacity: .9; }
.exp-roles .exp-figure a { color: inherit; text-decoration: none; }
.exp-roles .exp-figure a:hover span { text-decoration: underline; }
.exp-roles .exp-figure.is-current { border-color: var(--rl-accent); box-shadow: inset 0 0 0 1px var(--rl-accent); }
.exp-roles .exp-searchrow { display: flex; flex-wrap: wrap; gap: 8px; align-items: center; }
.exp-roles .exp-searchrow input[type="search"], .exp-roles .exp-searchrow input[type="text"] { flex: 1 1 220px; min-width: 0; }
.exp-roles .exp-field input[type="checkbox"] { width: 18px; height: 18px; margin: 0; accent-color: var(--rl-accent); }
.exp-roles .exp-check { display: flex; align-items: flex-start; gap: 8px; font-weight: 400; }
.exp-roles .exp-check input { flex: 0 0 auto; margin-top: 2px !important; }
.exp-roles .exp-check strong { font-weight: 650; }
.exp-roles .exp-check > span { flex: 1 1 auto; min-width: 0; }
.exp-roles .exp-check .exp-help { display: block; font-weight: 400; }
.exp-roles .exp-check, .exp-roles .exp-check * { white-space: normal; }

/* One URL, alias or wildcard */
.exp-roles .exp-card-addr { min-width: 0; font: 600 14px/1.45 var(--rl-mono); overflow-wrap: anywhere; word-break: break-word; }
.exp-roles .exp-card-addr a { color: var(--rl-ink); text-decoration: none; }
.exp-roles .exp-card-addr a:hover { color: var(--rl-accent-hover); text-decoration: underline; }
.exp-roles .exp-arrow { color: var(--rl-muted); font-family: var(--rl-mono); }
.exp-roles .exp-card.is-bad { box-shadow: inset 4px 0 0 var(--rl-bad), 0 1px 2px rgba(16, 24, 40, 0.04); }
.exp-roles .exp-usage { margin: 0; padding: 0; list-style: none; }
.exp-roles .exp-usage li { display: inline; }
.exp-roles .exp-usage li + li::before { content: ", "; color: var(--rl-muted); }

/* A confirmation that opens in place: works without javascript */
.exp-roles details.exp-confirm { flex: 1 1 100%; margin: 0; padding: 0; border: 1px solid var(--rl-line); border-radius: var(--rl-radius); background: var(--rl-card); }
.exp-roles details.exp-confirm > summary { display: inline-flex; align-items: center; gap: 6px; min-height: 36px; padding: 6px 14px; color: var(--rl-bad); font-weight: 650; cursor: pointer; list-style: none; }
.exp-roles details.exp-confirm > summary::-webkit-details-marker { display: none; }
.exp-roles details.exp-confirm > summary::before { content: "\25B8"; }
.exp-roles details.exp-confirm[open] > summary::before { content: "\25BE"; }
.exp-roles details.exp-confirm > div { display: flex; flex-wrap: wrap; align-items: center; gap: 10px 14px; padding: 0 14px 14px; }
.exp-roles details.exp-confirm > div p { flex: 1 1 320px; color: var(--rl-ink); }
.exp-roles .exp-bottombar details.exp-confirm { flex: 0 1 auto; }
.exp-roles .exp-bottombar details.exp-confirm[open] { flex: 1 1 100%; }


@media (max-width: 600px) {
    .exp-roles .exp-result dl { grid-template-columns: minmax(0, 1fr); gap: 2px; }
    .exp-roles .exp-result dd { margin-bottom: 6px; }
    .exp-roles.exp-standalone { margin: 8px; padding: 14px 12px; }
    .exp-roles .exp-card { padding: 12px; }
    .exp-roles .exp-card-head .exp-actions { width: 100%; }
    .exp-roles .exp-card-head .exp-actions .exp-btn { flex: 1 1 0; }
    .exp-roles .exp-actionbar .exp-actions, .exp-roles .exp-bottombar .exp-actions { width: 100%; }
    .exp-roles .exp-actionbar .exp-btn, .exp-roles .exp-bottombar .exp-btn { flex: 1 1 auto; }
    .exp-roles .exp-table th, .exp-roles .exp-table td { padding: 8px 9px; }
}

/* ---- The role pages' own parts ---- */

/* A policy: its sentence first, the module/function and limitations as small print under it */
.exp-roles .exp-policies { display: grid; grid-template-columns: minmax(0, 1fr); gap: 8px; margin: 0; padding: 0; list-style: none; }
.exp-roles .exp-policy { display: flex; flex-wrap: wrap; align-items: flex-start; gap: 6px 12px; margin: 0; padding: 10px 14px; border: 1px solid var(--rl-line); border-radius: 10px; background: var(--rl-card); }
.exp-roles .exp-policy.is-full { border-color: #f1b4b4; box-shadow: inset 4px 0 0 var(--rl-bad); }
.exp-roles .exp-policy.is-roles { box-shadow: inset 4px 0 0 var(--rl-warn); }
.exp-roles .exp-policy.is-selected { border-color: var(--rl-accent); }
.exp-roles .exp-policy-main { flex: 1 1 320px; min-width: 0; }
.exp-roles .exp-sentence { margin: 0; font-weight: 600; overflow-wrap: anywhere; }
.exp-roles .exp-policy-detail { margin: 3px 0 0; font-size: 12.5px; color: var(--rl-muted); overflow-wrap: anywhere; }
.exp-roles .exp-policy-detail code { color: var(--rl-muted); }
.exp-roles .exp-policy-tools { display: flex; flex-wrap: wrap; align-items: center; gap: 6px; }
.exp-roles .exp-policy .exp-select { margin: -4px 0 -4px -6px; }
.exp-roles .exp-order { display: inline-flex; gap: 4px; }
.exp-roles .exp-order .exp-btn { min-width: 34px; padding: 4px 8px; }
.exp-roles .exp-group-head { display: flex; flex-wrap: wrap; align-items: baseline; gap: 4px 10px; margin: 16px 0 8px; }
.exp-roles .exp-group-head:first-child { margin-top: 0; }
.exp-roles .exp-group-head h3 { font-size: 14px; }
.exp-roles .exp-denies { color: var(--rl-bad); font-style: normal; font-weight: 650; }

/* The warning of a role with full access */
.exp-roles .exp-card.is-full { box-shadow: inset 4px 0 0 var(--rl-bad), 0 1px 2px rgba(16, 24, 40, 0.04); }
.exp-roles .exp-card-title h3 { flex: 0 1 auto; }

/* The steps of the policy wizard */
.exp-roles ol.exp-steps { display: flex; flex-wrap: wrap; gap: 8px; margin: 0 0 18px; padding: 0; list-style: none; counter-reset: rlstep; }
.exp-roles ol.exp-steps li { display: inline-flex; align-items: center; gap: 8px; min-height: 34px; padding: 4px 14px 4px 6px; border: 1px solid var(--rl-line); border-radius: 999px; background: var(--rl-card); color: var(--rl-muted); font-size: 13px; counter-increment: rlstep; }
.exp-roles ol.exp-steps li::before { content: counter(rlstep); display: inline-flex; align-items: center; justify-content: center; width: 24px; height: 24px; border-radius: 50%; background: var(--rl-soft); color: var(--rl-ink); font-weight: 700; }
.exp-roles ol.exp-steps li.is-done::before { content: "\2713"; background: var(--rl-ok-bg); color: var(--rl-ok); }
.exp-roles ol.exp-steps li.is-current { border-color: var(--rl-accent); color: var(--rl-ink); font-weight: 650; }
.exp-roles ol.exp-steps li.is-current::before { background: var(--rl-accent); color: #fff; }
.exp-roles .exp-choice { display: grid; grid-template-columns: repeat(auto-fit, minmax(min(100%, 280px), 1fr)); gap: 12px; margin: 14px 0 0; }
.exp-roles .exp-choice > div { display: flex; flex-direction: column; align-items: flex-start; gap: 8px; padding: 14px; border: 1px solid var(--rl-line); border-radius: var(--rl-radius); background: var(--rl-soft); }
.exp-roles .exp-choice p { color: var(--rl-muted); font-size: 13px; }
.exp-roles .exp-field select[multiple] { min-height: 150px; padding: 4px; }
.exp-roles .exp-limits { display: grid; grid-template-columns: repeat(auto-fit, minmax(min(100%, 240px), 1fr)); gap: 16px; }
.exp-roles .exp-pickers { display: grid; grid-template-columns: repeat(auto-fit, minmax(min(100%, 320px), 1fr)); gap: 16px; margin: 16px 0 0; }
.exp-roles .exp-pickers > .exp-panel { margin: 0; }
.exp-roles ul.exp-picked { margin: 8px 0 10px; padding: 0; list-style: none; }
.exp-roles ul.exp-picked li { display: flex; align-items: center; gap: 8px; padding: 5px 0; border-bottom: 1px solid var(--rl-line); }
.exp-roles ul.exp-picked li:last-child { border-bottom: 0; }
.exp-roles ul.exp-picked input { width: 18px; height: 18px; accent-color: var(--rl-accent); }

/* Reordering: the grip (drag, or the arrow keys), the position field */
.exp-roles .exp-grip { display: inline-flex; align-items: center; justify-content: center; width: 30px; min-height: 30px; margin: 0; padding: 0; border: 1px dashed #aab1bd; border-radius: 8px; background: var(--rl-soft); color: var(--rl-muted); font-size: 16px; line-height: 1; cursor: grab; touch-action: none; }
.exp-roles .exp-grip:hover, .exp-roles .exp-grip:focus-visible { border-style: solid; border-color: var(--rl-accent); color: var(--rl-accent-hover); }
.exp-roles .exp-grip[hidden] { display: none; }
.exp-roles .exp-policy.is-dragging { opacity: .55; border-style: dashed; border-color: var(--rl-accent); }
.exp-roles .exp-policy.is-moved { border-color: var(--rl-accent); box-shadow: 0 0 0 2px var(--rl-ring); }
.exp-roles .exp-order { align-items: center; flex-wrap: wrap; }
.exp-roles .exp-moveto { display: inline-flex; align-items: center; gap: 4px; }
.exp-roles .exp-moveto input { width: 4.2em; min-height: 30px; padding: 2px 6px; border: 1px solid #8f96a3; border-radius: 8px; background: #fff; color: var(--rl-ink); font: inherit; font-size: 13px; }
.exp-roles .exp-moveto input:focus { border-color: var(--rl-accent); outline: 3px solid var(--rl-ring); outline-offset: 0; }

/* A choice of one (the section of an assignment) */
.exp-roles ul.exp-radios { display: grid; grid-template-columns: repeat(auto-fit, minmax(min(100%, 240px), 1fr)); gap: 8px; margin: 0; padding: 0; list-style: none; }
.exp-roles ul.exp-radios label { display: flex; align-items: center; gap: 10px; min-height: 40px; padding: 8px 12px; border: 1px solid var(--rl-line); border-radius: 10px; background: var(--rl-card); cursor: pointer; }
.exp-roles ul.exp-radios input { width: 18px; height: 18px; margin: 0; accent-color: var(--rl-accent); }
.exp-roles ul.exp-radios input:checked + span { font-weight: 650; }

/* The editor's save bar: stays in view at the bottom of the window */
.exp-roles .exp-savebar { position: sticky; bottom: 0; z-index: 5; display: flex; flex-wrap: wrap; align-items: center; gap: 10px 12px; margin: 18px 0 0; padding: 12px 14px; border: 1px solid var(--rl-line); border-radius: var(--rl-radius); background: var(--rl-card); box-shadow: 0 -6px 18px -8px rgba(16, 24, 40, 0.18); }
.exp-roles .exp-savebar .exp-meta { flex: 1 1 260px; }
.exp-roles .exp-dirty { display: inline-flex; align-items: center; gap: 6px; color: var(--rl-warn); font-weight: 650; }
.exp-roles .exp-name-field { max-width: 520px; margin: 0 0 18px; }
.exp-roles .exp-hidden-default { position: absolute; width: 1px; height: 1px; margin: -1px; padding: 0; overflow: hidden; clip: rect(0 0 0 0); white-space: nowrap; border: 0; opacity: 0; }
.exp-roles .exp-who { font-size: 13px; color: var(--rl-muted); }

@media (max-width: 600px) {
    .exp-roles .exp-policy { padding: 10px 12px; }
    .exp-roles .exp-savebar .exp-actions { width: 100%; }
    .exp-roles .exp-savebar .exp-btn { flex: 1 1 auto; }
    .exp-roles ol.exp-steps li { flex: 1 1 100%; }
}
</style>
{/literal}
