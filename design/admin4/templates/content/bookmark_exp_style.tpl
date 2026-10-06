{* The look of the bookmark page (content/bookmark), in the visual language of the section, link list, RSS and
   session pages: overview figures, a search and order bar, one card per bookmark grouped by folder, a folder panel,
   in-place confirmations and a bar for the selected bookmarks. Included once by content/bookmark.tpl.

   The same file is in design/admin and design/admin4, so every administration design resolves it. Everything is
   scoped to .exp-bm and takes admin4's tokens where they exist (--a4-*), with values of its own for the older
   designs. No shared stylesheet is changed; the tree of the Bookmarks box (exp_bookmarks.css, .exp-bm-*) is not
   used here. Guide: doc/guides/bookmarks.md *}
{literal}
<style>
.exp-bm {
    --bm-ink: var(--a4-ink, #1f2430);
    --bm-muted: var(--a4-muted, #5d6573);
    --bm-line: var(--a4-line, #e3e6eb);
    --bm-soft: var(--a4-soft, #f6f7f9);
    --bm-card: #fff;
    --bm-edge: #c9ced6;
    --bm-field: #8f96a3;
    --bm-accent: #c2410c;          /* white text on it is 5.2:1 */
    --bm-accent-hover: #9a3412;    /* links: 7.3:1 on white */
    --bm-accent-soft: #fff1e8;
    --bm-ring: rgba(194, 65, 12, 0.45);
    --bm-ok: #166534;   --bm-ok-bg: #e7f5ea;
    --bm-warn: #8a4b00; --bm-warn-bg: #fff3df;
    --bm-bad: #b91c1c;  --bm-bad-bg: #fdecec;
    --bm-info: #1e4fa8; --bm-info-bg: #e8effd;
    --bm-radius: 12px;
    color: var(--bm-ink);
    font-size: 14px;
    line-height: 1.5;
}
.exp-bm *, .exp-bm *::before, .exp-bm *::after { box-sizing: border-box; }
.exp-bm [hidden] { display: none !important; }
.exp-bm .box-content { padding-bottom: 20px; }
.exp-bm h1.context-title { margin: 0; overflow-wrap: anywhere; }
.exp-bm h2.exp-h2 { margin: 0; padding: 0; border: 0; background: none; font-size: 16px; font-weight: 650; color: var(--bm-ink); }
.exp-bm h3.exp-h3 { margin: 0; padding: 0; border: 0; background: none; font-size: 15px; font-weight: 650; color: var(--bm-ink); overflow-wrap: anywhere; }
.exp-bm p { margin: 0; }
.exp-bm a { color: var(--bm-accent-hover); }
.exp-bm a:hover { color: var(--bm-ink); }
.exp-bm :focus-visible { outline: 3px solid var(--bm-ring); outline-offset: 2px; }
.exp-bm .exp-sr { position: absolute; width: 1px; height: 1px; margin: -1px; padding: 0; overflow: hidden; clip: rect(0 0 0 0); white-space: nowrap; border: 0; }
.exp-bm .exp-muted { color: var(--bm-muted); }
.exp-bm ul.exp-plain { margin: 0; padding: 0; list-style: none; }
.exp-bm .exp-intro { margin: 8px 0 18px; max-width: 78ch; color: var(--bm-muted); }
.exp-bm .exp-meta { color: var(--bm-muted); font-size: 13px; }
.exp-bm .exp-help { font-size: 13px; color: var(--bm-muted); max-width: 72ch; }
.exp-bm .exp-mt { margin-top: 10px; }

/* Messages */
.exp-bm .exp-feedback { margin: 0 0 14px; padding: 10px 14px; border: 1px solid; border-left-width: 4px; border-radius: 10px; }
.exp-bm .exp-feedback.is-ok { border-color: #b7dfc1; border-left-color: var(--bm-ok); background: var(--bm-ok-bg); color: var(--bm-ok); }
.exp-bm .exp-feedback.is-bad { border-color: #f1b4b4; border-left-color: var(--bm-bad); background: var(--bm-bad-bg); color: var(--bm-bad); }

/* Overview figures */
.exp-bm .exp-figures { display: grid; grid-template-columns: repeat(auto-fit, minmax(min(100%, 120px), 1fr)); gap: 10px; margin: 0 0 20px; padding: 0; list-style: none; }
.exp-bm .exp-figure { display: flex; flex-direction: column; gap: 2px; margin: 0; padding: 12px 14px; border: 1px solid var(--bm-line); border-radius: var(--bm-radius); background: var(--bm-card); }
.exp-bm .exp-figure a { display: flex; flex-direction: column; gap: 2px; color: inherit; text-decoration: none; }
.exp-bm .exp-figure a:hover span { text-decoration: underline; }
.exp-bm .exp-figure strong { font-size: 22px; line-height: 1.15; font-weight: 700; font-variant-numeric: tabular-nums; color: var(--bm-ink); }
.exp-bm .exp-figure span { font-size: 12.5px; color: var(--bm-muted); }
.exp-bm .exp-figure.is-attention strong { color: var(--bm-bad); }
.exp-bm .exp-figure.is-current { border-color: var(--bm-accent); box-shadow: inset 0 0 0 1px var(--bm-accent); }

/* Buttons */
.exp-bm .exp-btn {
    display: inline-flex; align-items: center; justify-content: center; gap: 6px; min-height: 36px; max-width: 100%; margin: 0;
    padding: 6px 14px; border: 1px solid var(--bm-edge); border-radius: 9px; background: #fff; color: var(--bm-ink);
    font: 600 13.5px/1.2 inherit; font-family: inherit; cursor: pointer; white-space: nowrap; text-decoration: none; box-shadow: none;
}
.exp-bm a.exp-btn { color: var(--bm-ink); }
.exp-bm .exp-btn:hover:not([disabled]) { border-color: var(--bm-accent); color: var(--bm-accent-hover); background: #fff; }
.exp-bm .exp-btn[disabled] { opacity: .55; cursor: not-allowed; }
.exp-bm .exp-btn-primary, .exp-bm a.exp-btn-primary { border-color: var(--bm-accent); background: var(--bm-accent); color: #fff; }
.exp-bm .exp-btn-primary:hover:not([disabled]) { border-color: var(--bm-accent-hover); background: var(--bm-accent-hover); color: #fff; }
.exp-bm .exp-btn-danger { border-color: var(--bm-bad); background: var(--bm-bad); color: #fff; }
.exp-bm .exp-btn-danger:hover:not([disabled]) { border-color: #7f1d1d; background: #7f1d1d; color: #fff; }
.exp-bm .exp-btn-small { min-height: 30px; padding: 4px 10px; font-size: 12.5px; }
.exp-bm .exp-btn-icon { min-width: 30px; padding-inline: 6px; }
.exp-bm .exp-btn svg { flex: 0 0 auto; }
.exp-bm .exp-actions { display: flex; flex-wrap: wrap; align-items: center; gap: 8px; }
.exp-bm .exp-actions-tight { gap: 6px; }

/* Fields */
.exp-bm .exp-field { display: flex; flex-direction: column; gap: 5px; min-width: 0; margin: 0; padding: 0; border: 0; }
.exp-bm .exp-field > label, .exp-bm .exp-field-label { padding: 0; font-size: 12.5px; font-weight: 650; color: var(--bm-ink); }
.exp-bm select, .exp-bm input[type="search"], .exp-bm input[type="text"] {
    width: 100%; min-height: 38px; margin: 0; padding: 6px 10px; border: 1px solid var(--bm-field); border-radius: 9px;
    background: #fff; color: var(--bm-ink); font: inherit; font-size: 14px; box-shadow: none;
}
.exp-bm select:focus, .exp-bm input[type="search"]:focus, .exp-bm input[type="text"]:focus { border-color: var(--bm-accent); outline: 3px solid var(--bm-ring); outline-offset: 0; }
.exp-bm .exp-searchrow { display: flex; flex-wrap: wrap; gap: 8px; align-items: center; }
.exp-bm .exp-searchrow input { flex: 1 1 220px; min-width: 0; width: auto; }
.exp-bm .exp-inline-form { display: grid; gap: 10px; margin: 0; }
.exp-bm .exp-inline-form .exp-actions { justify-content: flex-start; }

/* Search and order */
.exp-bm .exp-toolbar { display: grid; grid-template-columns: minmax(0, 1fr); gap: 14px; align-items: start;
    margin: 0 0 18px; padding: 16px; border: 1px solid var(--bm-line); border-radius: var(--bm-radius); background: var(--bm-card); }
.exp-bm .exp-tabs { display: flex; flex-wrap: wrap; gap: 6px; margin: 0; padding: 0; list-style: none; }
.exp-bm .exp-tabs a, .exp-bm .exp-tabs span.current { display: inline-flex; align-items: center; gap: 6px; min-height: 32px; padding: 4px 12px; border: 1px solid var(--bm-edge); border-radius: 999px; background: #fff; color: var(--bm-ink); font-size: 13px; text-decoration: none; }
.exp-bm .exp-tabs a:hover { border-color: var(--bm-accent); color: var(--bm-accent-hover); }
.exp-bm .exp-tabs span.current { border-color: var(--bm-accent); background: var(--bm-accent); color: #fff; font-weight: 650; }

/* Folders beside the list; with less than about 690 px of room the panel goes above it */
.exp-bm .exp-bm-layout { display: flex; flex-wrap: wrap; align-items: flex-start; gap: 18px; }
.exp-bm .exp-bm-side { flex: 1 1 250px; min-width: 0; display: grid; gap: 14px; }
.exp-bm .exp-bm-main { flex: 999 1 440px; min-width: 0; }
.exp-bm .exp-panel { min-width: 0; margin: 0; padding: 14px 16px; border: 1px solid var(--bm-line); border-radius: var(--bm-radius); background: var(--bm-card); }
.exp-bm .exp-panel-head { display: flex; flex-wrap: wrap; align-items: baseline; justify-content: space-between; gap: 4px 10px; margin: 0 0 10px; }
.exp-bm .exp-bm-nav { display: grid; gap: 2px; margin: 0; padding: 0; list-style: none; }
.exp-bm .exp-bm-nav a, .exp-bm .exp-bm-nav span.current {
    display: flex; align-items: center; gap: 8px; min-height: 34px; padding: 5px 10px; border-radius: 8px;
    color: var(--bm-ink); text-decoration: none; overflow-wrap: anywhere;
}
.exp-bm .exp-bm-nav a:hover { background: var(--bm-soft); color: var(--bm-accent-hover); }
.exp-bm .exp-bm-nav span.current { background: var(--bm-accent-soft); box-shadow: inset 3px 0 0 var(--bm-accent); font-weight: 650; }
.exp-bm .exp-bm-nav .exp-bm-navname { flex: 1 1 auto; min-width: 0; }
.exp-bm .exp-bm-nav .exp-count { flex: 0 0 auto; min-width: 26px; padding: 0 7px; border-radius: 999px; background: var(--bm-soft); color: var(--bm-muted); font-size: 12px; font-weight: 650; text-align: center; font-variant-numeric: tabular-nums; }
.exp-bm .exp-bm-nav span.current .exp-count { background: #fff; color: var(--bm-ink); }
.exp-bm .exp-bm-nav svg { flex: 0 0 auto; color: var(--bm-muted); }
.exp-bm .exp-bm-nav .is-sep { height: 1px; margin: 6px 4px; background: var(--bm-line); }
.exp-bm .exp-bm-nav li { position: relative; display: flex; align-items: center; gap: 4px; min-width: 0; }
.exp-bm .exp-bm-nav li > a, .exp-bm .exp-bm-nav li > span.current { flex: 1 1 auto; min-width: 0; }
.exp-bm .exp-bm-nav li.d1 { padding-left: 14px; }
.exp-bm .exp-bm-nav li.d2 { padding-left: 28px; }
.exp-bm .exp-bm-nav li.d3 { padding-left: 42px; }
.exp-bm .exp-bm-nav li.d4 { padding-left: 56px; }
/* dropping a bookmark on a folder moves it into the folder: the whole entry is outlined and says so */
.exp-bm .exp-bm-nav li.is-drop > a, .exp-bm .exp-bm-nav li.is-drop > span.current { outline: 2px dashed var(--bm-accent); outline-offset: -2px; background: var(--bm-accent-soft); }
.exp-bm .exp-bm-nav li.is-drop::after { content: attr(data-drop-label); position: absolute; right: 6px; top: -10px; z-index: 2; padding: 1px 8px; border-radius: 999px; background: var(--bm-accent); color: #fff; font-size: 11.5px; font-weight: 650; pointer-events: none; }
/* arranging folders: a line where the folder will go */
.exp-bm .exp-bm-nav li.is-before { box-shadow: inset 0 3px 0 var(--bm-accent); }
.exp-bm .exp-bm-nav li.is-after { box-shadow: inset 0 -3px 0 var(--bm-accent); }
.exp-bm .exp-bm-nav li.is-dragged { opacity: .5; }
.exp-bm .exp-bm-nav li.is-moved > a, .exp-bm .exp-bm-nav li.is-moved > span.current { box-shadow: 0 0 0 2px var(--bm-ring); }

/* Arranging bookmarks: the grip (drag it, or the arrow keys), the place a card moves to, the position field */
.exp-bm .exp-grip { display: inline-flex; align-items: center; justify-content: center; flex: 0 0 auto; width: 26px; min-height: 30px; margin: 0; padding: 0; border: 1px dashed #aab1bd; border-radius: 8px; background: var(--bm-soft); color: var(--bm-muted); font-size: 15px; line-height: 1; cursor: grab; touch-action: none; }
.exp-bm .exp-grip:hover, .exp-bm .exp-grip:focus-visible { border-style: solid; border-color: var(--bm-accent); color: var(--bm-accent-hover); }
.exp-bm .exp-grip:active { cursor: grabbing; }
.exp-bm .exp-bm-nav .exp-grip { width: 22px; min-height: 26px; font-size: 13px; }
.exp-bm .exp-cards.is-arranging { outline: 2px dashed var(--bm-edge); outline-offset: 4px; border-radius: var(--bm-radius); }
.exp-bm .exp-card.is-moved { border-color: var(--bm-accent); box-shadow: 0 0 0 2px var(--bm-ring); }
.exp-bm .exp-bm-arrange { margin: 0 0 12px; }
.exp-bm .exp-bm-cardfoot { display: flex; flex-wrap: wrap; align-items: flex-end; justify-content: space-between; gap: 8px 18px; margin: 8px 0 0; padding-left: 30px; }
.exp-bm .exp-bm-cardfoot > .exp-facts, .exp-bm .exp-bm-cardfoot > .exp-bm-note { flex: 1 1 320px; margin: 0; padding-left: 0; }
.exp-bm .exp-bm-position { display: flex; flex-wrap: wrap; align-items: center; gap: 6px 8px; margin: 0; font-size: 13px; }
.exp-bm .exp-bm-position label { font-weight: 650; color: var(--bm-ink); }
.exp-bm .exp-bm-position input[type="number"] { width: 4.6em; min-height: 30px; padding: 2px 6px; border: 1px solid var(--bm-field); border-radius: 8px; background: #fff; color: var(--bm-ink); font: inherit; }
.exp-bm .exp-bm-position input[type="number"]:focus { border-color: var(--bm-accent); outline: 3px solid var(--bm-ring); outline-offset: 0; }

/* Disclosures: forms that open in place, without javascript */
.exp-bm details.exp-disclosure { margin: 0; padding: 0; border: 1px solid var(--bm-line); border-radius: var(--bm-radius); background: var(--bm-card); }
.exp-bm details.exp-disclosure > summary { display: flex; align-items: center; gap: 8px; min-height: 40px; padding: 8px 14px; font-weight: 650; cursor: pointer; list-style: none; color: var(--bm-ink); }
.exp-bm details.exp-disclosure > summary::-webkit-details-marker { display: none; }
.exp-bm details.exp-disclosure > summary::before { content: "\25B8"; color: var(--bm-muted); }
.exp-bm details.exp-disclosure[open] > summary::before { content: "\25BE"; }
.exp-bm details.exp-disclosure > div { padding: 0 14px 14px; }
.exp-bm details.exp-disclosure.is-danger > summary { color: var(--bm-bad); }

/* The folder that is shown */
.exp-bm .exp-bm-folderhead { margin: 0 0 18px; }
.exp-bm .exp-bm-crumbs { display: flex; flex-wrap: wrap; gap: 2px 6px; margin: 0 0 4px; padding: 0; list-style: none; font-size: 13px; color: var(--bm-muted); }
.exp-bm .exp-bm-crumbs li + li::before { content: "/"; margin-right: 6px; color: var(--bm-muted); }
.exp-bm .exp-bm-folderacts { display: grid; grid-template-columns: repeat(auto-fit, minmax(min(100%, 200px), 1fr)); gap: 8px; margin-top: 12px; align-items: start; }
.exp-bm .exp-bm-folderacts > form { margin: 0; }
.exp-bm .exp-bm-folderacts > details[open] { grid-column: 1 / -1; }
.exp-bm .exp-choice { display: grid; gap: 8px; min-width: 0; margin: 0; padding: 0; border: 0; }
.exp-bm .exp-choice legend { margin: 0 0 4px; padding: 0; font-weight: 650; }
.exp-bm .exp-check { display: flex; align-items: flex-start; gap: 8px; margin: 0; font-weight: 400; }
.exp-bm .exp-check input { flex: 0 0 auto; width: 18px; height: 18px; margin: 2px 0 0; accent-color: var(--bm-accent); }
.exp-bm .exp-check > span { flex: 1 1 auto; min-width: 0; }
.exp-bm .exp-check, .exp-bm .exp-check * { white-space: normal; }

/* The list */
.exp-bm .exp-section-head { display: flex; flex-wrap: wrap; align-items: center; gap: 4px 12px; margin: 0 0 10px; }
.exp-bm .exp-section-head .exp-bm-selectall { display: inline-flex; align-items: center; gap: 6px; margin-left: auto; }
.exp-bm .exp-section-head .exp-bm-selectall input { width: 18px; height: 18px; margin: 0; accent-color: var(--bm-accent); }
.exp-bm .exp-bm-group { margin: 0 0 16px; }
.exp-bm .exp-bm-grouphead { display: flex; flex-wrap: wrap; align-items: center; gap: 4px 10px; margin: 0 0 8px; padding: 0 2px; }
.exp-bm .exp-bm-grouphead svg { color: var(--bm-muted); flex: 0 0 auto; }
.exp-bm .exp-bm-grouppath { flex: 1 1 100%; margin: 0; padding-left: 24px; font-size: 13px; color: var(--bm-muted); overflow-wrap: anywhere; }
.exp-bm .exp-cards { display: grid; grid-template-columns: minmax(0, 1fr); gap: 8px; margin: 0; padding: 0; list-style: none; }
.exp-bm .exp-card { min-width: 0; margin: 0; padding: 12px 14px; border: 1px solid var(--bm-line); border-radius: var(--bm-radius); background: var(--bm-card); box-shadow: 0 1px 2px rgba(16, 24, 40, 0.04); }
.exp-bm .exp-card.is-attention { box-shadow: inset 4px 0 0 var(--bm-warn), 0 1px 2px rgba(16, 24, 40, 0.04); }
.exp-bm .exp-card.is-bad { box-shadow: inset 4px 0 0 var(--bm-bad), 0 1px 2px rgba(16, 24, 40, 0.04); }
.exp-bm .exp-card.is-selected { border-color: var(--bm-accent); background: #fffaf6; }
.exp-bm .exp-card.is-dragged { opacity: .5; }
.exp-bm .exp-card-head { display: flex; flex-wrap: wrap; align-items: center; justify-content: space-between; gap: 8px 14px; }
.exp-bm .exp-card-title { display: flex; flex-wrap: wrap; align-items: center; gap: 4px 10px; min-width: 0; flex: 1 1 300px; }
.exp-bm .exp-card-title h4 { margin: 0; padding: 0; border: 0; background: none; font-size: 15px; font-weight: 650; line-height: 1.35; color: var(--bm-ink); overflow-wrap: anywhere; }
.exp-bm .exp-card-title h4 a { color: var(--bm-ink); text-decoration: none; }
.exp-bm .exp-card-title h4 a:hover { color: var(--bm-accent-hover); text-decoration: underline; }
.exp-bm .exp-card-title img { flex: 0 0 auto; width: 16px; height: 16px; vertical-align: middle; }
.exp-bm .exp-select { display: inline-flex; align-items: center; justify-content: center; width: 32px; height: 32px; margin: -4px -2px -4px -6px; border-radius: 8px; cursor: pointer; }
.exp-bm .exp-select:hover { background: var(--bm-soft); }
.exp-bm .exp-select input { width: 18px; height: 18px; margin: 0; accent-color: var(--bm-accent); cursor: pointer; }
.exp-bm .exp-badges { display: inline-flex; flex-wrap: wrap; gap: 6px; margin: 0; padding: 0; list-style: none; }
.exp-bm .exp-badge { display: inline-flex; align-items: center; gap: 5px; margin: 0; padding: 2px 9px; border-radius: 999px; font-size: 12px; font-weight: 650; line-height: 1.5; white-space: nowrap; background: var(--bm-soft); color: var(--bm-muted); }
.exp-bm .exp-badge.is-warn { background: var(--bm-warn-bg); color: var(--bm-warn); }
.exp-bm .exp-badge.is-bad { background: var(--bm-bad-bg); color: var(--bm-bad); }
.exp-bm .exp-badge.is-info { background: var(--bm-info-bg); color: var(--bm-info); }
.exp-bm .exp-facts { display: grid; grid-template-columns: minmax(0, 2fr) minmax(0, 1fr); gap: 4px 18px; margin: 8px 0 0; padding-left: 30px; }
.exp-bm .exp-facts > div { min-width: 0; }
.exp-bm .exp-facts dt { margin: 0; font-size: 11.5px; font-weight: 650; text-transform: uppercase; letter-spacing: .04em; color: var(--bm-muted); }
.exp-bm .exp-facts dd { margin: 1px 0 0; overflow-wrap: anywhere; }
.exp-bm .exp-bm-folderfacts { grid-template-columns: repeat(auto-fit, minmax(min(100%, 130px), 1fr)); padding-left: 0; }
.exp-bm .exp-bm-note { margin: 8px 0 0; padding-left: 30px; font-size: 13px; color: var(--bm-muted); }
.exp-bm .exp-empty { margin: 0; padding: 22px 16px; border: 1px dashed var(--bm-edge); border-radius: var(--bm-radius); color: var(--bm-muted); text-align: center; }
.exp-bm .exp-empty strong { display: block; margin-bottom: 4px; color: var(--bm-ink); font-size: 15px; }
.exp-bm .exp-empty .exp-actions { justify-content: center; margin-top: 12px; }

/* Page size and pages */
.exp-bm .exp-listfoot { display: flex; flex-wrap: wrap; align-items: center; justify-content: space-between; gap: 10px 18px; margin: 14px 0 0; }
.exp-bm .exp-sizes { display: flex; flex-wrap: wrap; align-items: center; gap: 6px; margin: 0; font-size: 13px; color: var(--bm-muted); }
.exp-bm .exp-sizes a, .exp-bm .exp-sizes span.current { display: inline-flex; align-items: center; justify-content: center; min-width: 34px; min-height: 30px; padding: 2px 8px; border: 1px solid var(--bm-edge); border-radius: 8px; text-decoration: none; }
.exp-bm .exp-sizes span.current { border-color: var(--bm-accent); background: var(--bm-accent); color: #fff; font-weight: 650; }
.exp-bm .exp-pager { min-width: 0; }
.exp-bm .exp-pager .pagenavigator { margin: 0; }
.exp-bm .exp-pager a, .exp-bm .exp-pager span.text, .exp-bm .exp-pager span.text a { color: var(--bm-accent-hover); }
.exp-bm .exp-pager span.disabled, .exp-bm .exp-pager span.text.disabled { color: var(--bm-muted); }
.exp-bm .exp-pager span.current { color: var(--bm-ink); }

/* The bar of the selected bookmarks */
.exp-bm .exp-bottombar { display: flex; flex-wrap: wrap; align-items: flex-end; gap: 12px 14px; margin: 18px 0 0; padding: 12px 14px; border: 1px solid var(--bm-line); border-radius: var(--bm-radius); background: var(--bm-soft); }
.exp-bm .exp-bm-movegroup { display: flex; flex-wrap: wrap; align-items: flex-end; gap: 8px; flex: 1 1 340px; min-width: 0; }
.exp-bm .exp-bm-movegroup .exp-field { flex: 1 1 200px; }
.exp-bm .exp-section-head .exp-bm-addtop { margin-left: 4px; }
.exp-bm .exp-bottombar .exp-meta { flex: 1 1 100%; }
.exp-bm details.exp-confirm { margin: 0; padding: 0; border: 1px solid var(--bm-line); border-radius: 9px; background: var(--bm-card); }
.exp-bm details.exp-confirm > summary { display: inline-flex; align-items: center; gap: 6px; min-height: 36px; padding: 6px 14px; color: var(--bm-bad); font-weight: 650; cursor: pointer; list-style: none; }
.exp-bm details.exp-confirm > summary::-webkit-details-marker { display: none; }
.exp-bm details.exp-confirm > summary::before { content: "\25B8"; }
.exp-bm details.exp-confirm[open] { flex: 1 1 100%; }
.exp-bm details.exp-confirm[open] > summary::before { content: "\25BE"; }
.exp-bm details.exp-confirm > div { display: flex; flex-wrap: wrap; align-items: center; gap: 10px 14px; padding: 0 14px 14px; }
.exp-bm details.exp-confirm > div p { flex: 1 1 280px; color: var(--bm-ink); }

@media (max-width: 600px) {
    .exp-bm .exp-card { padding: 12px; }
    .exp-bm .exp-facts { grid-template-columns: minmax(0, 1fr); padding-left: 0; }
    .exp-bm .exp-bm-note, .exp-bm .exp-bm-cardfoot { padding-left: 0; }
    .exp-bm .exp-card-head .exp-actions { width: 100%; }
    .exp-bm .exp-btn { white-space: normal; text-align: center; }
    .exp-bm .exp-bottombar > * { flex: 1 1 100%; }
}
</style>
{/literal}
