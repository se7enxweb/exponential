{* The styles of the collaboration views in admin4: tokens (--cb-*) built on the admin's own (--a4-*), the inbox, the
   item view, the message thread and the group tools. Included once by each view. *}
<style type="text/css">
{literal}
.cb { --cb-ink: var(--a4-ink, #1f2430); --cb-muted: var(--a4-muted, #5d6573); --cb-line: var(--a4-line, #e3e6eb);
      --cb-soft: var(--a4-soft, #f6f7f9); --cb-radius: var(--a4-radius-s, 9px); --cb-accent: var(--a4-orange, #f26a21);
      --cb-accent-dark: var(--a4-orange-dark, #d9561a); --cb-accent-soft: var(--a4-orange-soft, rgba(242,106,33,.12));
      --cb-ok: #1e5e22; --cb-ok-bg: #e1f1e2; --cb-warn: #8a3a0c; --cb-warn-bg: #fde7d9; --cb-bad: #9b001c; --cb-bad-bg: #fbe3e6;
      --cb-info: #1f4a7f; --cb-info-bg: #e8f0fb; color: var(--cb-ink); container: cb / inline-size; }
.cb *, .cb *::before, .cb *::after { box-sizing: border-box; }
.cb h1, .cb h2, .cb h3 { margin: 0; }
.cb-head { display: flex; flex-wrap: wrap; justify-content: space-between; align-items: flex-start; gap: .5em 1.2em; margin: 0 0 .9em; }
.cb-head h1 { font-size: 1.5em; line-height: 1.2; }
.cb-head p { margin: .2em 0 0; color: var(--cb-muted); max-width: 46em; }
.cb-crumb { margin: 0 0 .3em; font-size: .85em; color: var(--cb-muted); }
.cb-crumb a { color: var(--cb-accent-dark); }

.cb-notice { display: flex; gap: .6em; align-items: flex-start; margin: 0 0 1em; padding: .7em .9em; border-radius: var(--cb-radius); border: 1px solid; }
.cb-notice p { margin: 0; flex: 1; }
.cb-notice button { border: 0; background: none; color: inherit; font-size: 1.2em; line-height: 1; cursor: pointer; opacity: .6; }
.cb-notice-success { background: var(--cb-ok-bg); border-color: #b9e2c5; color: var(--cb-ok); }
.cb-notice-warning { background: var(--cb-warn-bg); border-color: #f2d59a; color: var(--cb-warn); }
.cb-notice-error { background: var(--cb-bad-bg); border-color: #f3bcbc; color: var(--cb-bad); }

.cb-stats { display: grid; grid-template-columns: repeat(auto-fit, minmax(8.5em, 1fr)); gap: .6em; margin: 0 0 1em; padding: 0; list-style: none; }
.cb-stat { display: block; height: 100%; padding: .65em .8em; border: 1px solid var(--cb-line); border-radius: var(--cb-radius); background: #fff; color: inherit; text-decoration: none; }
.cb-stat:hover, .cb-stat:focus-visible { border-color: var(--cb-accent); box-shadow: 0 0 0 3px var(--cb-accent-soft); outline: none; }
.cb-stat.current { border-color: var(--cb-accent); background: var(--cb-accent-soft); }
.cb-stat strong { display: block; font-size: 1.7em; line-height: 1.1; }
.cb-stat span { display: block; font-size: .82em; color: var(--cb-muted); }
.cb-stat.hot strong { color: var(--cb-accent-dark); }

.cb-filters { display: flex; flex-wrap: wrap; gap: .5em 1.4em; margin: 0 0 .8em; }
.cb-filter { display: flex; flex-wrap: wrap; align-items: center; gap: .3em; }
.cb-filter > b { font-size: .8em; font-weight: 600; color: var(--cb-muted); margin-right: .2em; }
.cb-chip { display: inline-block; padding: .2em .7em; border: 1px solid var(--cb-line); border-radius: 999px; background: #fff; color: var(--cb-ink); font-size: .88em; text-decoration: none; }
.cb-chip:hover { border-color: var(--cb-accent); }
.cb-chip.current { background: var(--cb-accent); border-color: var(--cb-accent); color: #fff; }
.cb-chip small { opacity: .75; }

.cb-layout { display: grid; grid-template-columns: minmax(0, 1fr); gap: 1em; }
@container cb (min-width: 46em) { .cb-layout.has-side { grid-template-columns: minmax(0, 1fr) 16em; } }
.cb-card { padding: .9em 1em; border: 1px solid var(--cb-line); border-radius: var(--cb-radius); background: #fff; margin: 0 0 1em; }
.cb-layout .cb-card:last-child, .cb-side .cb-card:last-child { margin-bottom: 0; }
.cb-card > h2, .cb-card > summary { font-size: 1.05em; margin: 0 0 .5em; }
.cb-card > summary { cursor: pointer; font-weight: 650; }
.cb-card > summary h2 { display: inline; font-size: 1em; }

.cb-list { margin: 0; padding: 0; list-style: none; border: 1px solid var(--cb-line); border-radius: var(--cb-radius); background: #fff; overflow: hidden; }
.cb-row { border-top: 1px solid var(--cb-line); }
.cb-row:first-child { border-top: 0; }
.cb-row-main, .cb-row-main:hover, .cb-row-main:visited, .cb-row-main:focus { color: inherit; text-decoration: none; }
.cb-row-main { display: block; padding: .7em .9em; color: inherit; text-decoration: none; border-left: 4px solid transparent; }
.cb-row-main:hover, .cb-row-main:focus-visible { background: var(--cb-soft); outline: none; }
.cb-state-waiting > .cb-row-main { border-left-color: var(--cb-accent); }
.cb-state-approved > .cb-row-main { border-left-color: #2e7d32; }
.cb-state-denied > .cb-row-main { border-left-color: #c62828; }
.cb-title { display: flex; flex-wrap: wrap; align-items: center; gap: .4em; font-weight: 600; overflow-wrap: anywhere; }
.cb-unread .cb-title { font-weight: 750; }
.cb-meta { display: flex; flex-wrap: wrap; gap: .1em .8em; margin: .25em 0 0; font-size: .85em; color: var(--cb-muted); }
.cb-excerpt { display: block; margin: .3em 0 0; font-size: .88em; color: var(--cb-muted); overflow-wrap: anywhere; }
.cb-excerpt b { color: var(--cb-ink); font-weight: 600; }
.cb-badge { display: inline-block; padding: .08em .55em; border-radius: 999px; font-size: .76em; font-weight: 700; text-transform: uppercase; letter-spacing: .02em; white-space: nowrap; background: var(--cb-soft); color: var(--cb-muted); }
.cb-badge.waiting { background: var(--cb-warn-bg); color: var(--cb-warn); }
.cb-badge.approved { background: var(--cb-ok-bg); color: var(--cb-ok); }
.cb-badge.denied { background: var(--cb-bad-bg); color: var(--cb-bad); }
.cb-pill { display: inline-block; min-width: 1.6em; padding: .05em .5em; border-radius: 999px; background: var(--cb-accent); color: #fff; font-size: .78em; font-weight: 700; text-align: center; }

.cb-empty { padding: 1.4em 1.2em; border: 1px dashed var(--cb-line); border-radius: var(--cb-radius); background: #fff; }
.cb-empty h2 { font-size: 1.15em; margin: 0 0 .4em; }
.cb-empty p { margin: .3em 0; color: var(--cb-muted); max-width: 44em; }
.cb-empty code { padding: .05em .35em; background: var(--cb-soft); border-radius: 5px; font-size: .9em; }
.cb-empty ol { margin: .4em 0 .4em 1.2em; padding: 0; color: var(--cb-muted); }

.cb-tree { margin: 0; padding: 0; list-style: none; }
.cb-tree li { margin: 0; }
.cb-tree a { display: flex; justify-content: space-between; gap: .5em; padding: .3em .5em; border-radius: 7px; color: inherit; text-decoration: none; overflow-wrap: anywhere; }
.cb-tree a:hover { background: var(--cb-soft); }
.cb-tree a.current { background: var(--cb-accent-soft); font-weight: 650; }
.cb-tree .count { color: var(--cb-muted); font-size: .85em; }

.cb-form { display: flex; flex-wrap: wrap; gap: .4em; align-items: center; margin: .5em 0 0; }
.cb-form input[type=text], .cb-form select, .cb-compose textarea { padding: .4em .6em; border: 1px solid var(--cb-line); border-radius: 7px; font: inherit; max-width: 100%; background: #fff; color: var(--cb-ink); }
.cb-form input[type=text] { flex: 1 1 9em; min-width: 0; }
.cb-btn { display: inline-block; padding: .45em 1em; border: 1px solid var(--cb-line); border-radius: 8px; background: #fff; color: var(--cb-ink); font: inherit; font-weight: 600; cursor: pointer; text-decoration: none; }
.cb-btn:hover { border-color: var(--cb-accent); }
.cb-btn:focus-visible, .cb-chip:focus-visible, .cb-tree a:focus-visible { outline: 2px solid var(--cb-accent); outline-offset: 2px; }
.cb-btn.primary { background: var(--cb-accent); border-color: var(--cb-accent); color: #fff; }
.cb-btn.primary:hover { background: var(--cb-accent-dark); }
.cb-btn.approve { background: #2e7d32; border-color: #2e7d32; color: #fff; }
.cb-btn.deny { background: #fff; border-color: #c62828; color: #c62828; }
.cb-btn.deny:hover { background: #c62828; color: #fff; }
.cb-btn[disabled] { opacity: .5; cursor: not-allowed; }
.cb-hint { margin: .4em 0 0; font-size: .88em; color: var(--cb-muted); }

.cb-facts { display: grid; grid-template-columns: repeat(auto-fit, minmax(10em, 1fr)); gap: .5em 1.2em; margin: .6em 0 0; }
.cb-facts div { min-width: 0; }
.cb-facts dt { font-size: .78em; color: var(--cb-muted); text-transform: uppercase; letter-spacing: .03em; }
.cb-facts dd { margin: 0; overflow-wrap: anywhere; }
.cb-compose textarea { width: 100%; min-height: 6em; resize: vertical; }
.cb-actions { display: flex; flex-wrap: wrap; gap: .5em; margin: .6em 0 0; }
.cb-people { display: flex; flex-wrap: wrap; gap: .4em; margin: 0; padding: 0; list-style: none; }
.cb-people li { padding: .15em .7em; border: 1px solid var(--cb-line); border-radius: 999px; background: var(--cb-soft); font-size: .9em; }
.cb-people li small { color: var(--cb-muted); }

.cb-thread { margin: 0; padding: 0; list-style: none; display: grid; gap: .7em; }
.cb-msg { display: grid; grid-template-columns: 2.2em minmax(0, 1fr); gap: .6em; }
.cb-avatar { display: grid; place-items: center; width: 2.2em; height: 2.2em; border-radius: 50%; background: var(--cb-accent-soft); color: var(--cb-accent-dark); font-weight: 700; }
.cb-bubble { padding: .5em .8em; border: 1px solid var(--cb-line); border-radius: 4px 12px 12px 12px; background: var(--cb-soft); overflow-wrap: anywhere; }
.cb-msg.new .cb-bubble { border-color: var(--cb-accent); background: var(--cb-accent-soft); }
.cb-msg-head { display: flex; flex-wrap: wrap; gap: .2em .7em; align-items: baseline; font-size: .85em; color: var(--cb-muted); }
.cb-msg-head b { color: var(--cb-ink); }
.cb-msg p { margin: .25em 0 0; }
.cb-preview { max-height: 30em; overflow: auto; padding: .4em .2em; }
.cb-preview .mainobject-window { overflow-wrap: anywhere; }

@media (max-width: 600px) {
  .cb-head h1 { font-size: 1.3em; }
  .cb-row-main { padding: .65em .7em; }
  .cb-actions .cb-btn { flex: 1 1 8em; text-align: center; }
}
{/literal}
</style>
