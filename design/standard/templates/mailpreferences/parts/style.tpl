{* The styles of the e-mail preference pages, for every design: tokens (--mp-*) that take the admin4 values when the
   page runs in admin4 (--a4-*) and plain defaults on a public site or the old admin. Included once by each page.
   No script is needed: every switch is a checkbox or a button in a form. *}
<style type="text/css">
{literal}
.mp { --mp-ink: var(--a4-ink, #1f2430); --mp-muted: var(--a4-muted, #5d6573); --mp-line: var(--a4-line, #d9dde3);
      --mp-soft: var(--a4-soft, #f5f6f8); --mp-card: #fff; --mp-radius: var(--a4-radius-s, 9px);
      --mp-accent: #c2410c; --mp-accent-dark: #9a3412; --mp-accent-soft: rgba(194,65,12,.14);
      --mp-ok: #1e5e22; --mp-ok-bg: #e1f1e2; --mp-ok-line: #b9e2c5; --mp-warn: #7a3410; --mp-warn-bg: #fdeadc; --mp-warn-line: #f2d59a;
      --mp-bad: #9b001c; --mp-bad-bg: #fbe3e6; --mp-bad-line: #f3bcbc; --mp-info: #1f4a7f; --mp-info-bg: #e8f0fb; --mp-info-line: #bcd2f0;
      color: var(--mp-ink); font-size: 1rem; line-height: 1.5; container: mp / inline-size; max-width: 62em; }
.mp.mp-public { margin: 1.5em auto 2.5em; padding: 0 16px; }
.mp.mp-wide { max-width: none; }
.mp *, .mp *::before, .mp *::after { box-sizing: border-box; }
.mp h1, .mp h2, .mp h3 { margin: 0; padding: 0; border: 0; background: none; box-shadow: none; color: inherit; font-family: inherit; text-transform: none; letter-spacing: normal; }
.mp p { margin: 0 0 .5em; }
.mp a { color: var(--mp-accent-dark); }
.mp label { font-weight: 400; display: inline; float: none; width: auto; margin: 0; padding: 0; white-space: normal; }
.mp legend { white-space: normal; max-width: 100%; }
.mp b, .mp strong { font-weight: 650; }
.mp .mp-sr { position: absolute; width: 1px; height: 1px; overflow: hidden; clip: rect(0 0 0 0); white-space: nowrap; }
.mp code { padding: .05em .35em; background: var(--mp-soft); border-radius: 5px; font-size: .9em; overflow-wrap: anywhere; }

.mp-head { margin: 0 0 1em; }
.mp-head h1 { font-size: 1.6em; line-height: 1.2; margin: 0 0 .25em; }
.mp-head p { color: var(--mp-muted); max-width: 46em; }
.mp-head .mp-for { color: var(--mp-ink); }
.mp-crumb { margin: 0 0 .3em; font-size: .88em; color: var(--mp-muted); }

.mp-notice { display: flex; gap: .6em; align-items: flex-start; margin: 0 0 1em; padding: .75em .95em; border-radius: var(--mp-radius); border: 1px solid; }
.mp-notice p { margin: 0; flex: 1; }
.mp-notice p + p { margin-top: .3em; }
.mp-notice-success { background: var(--mp-ok-bg); border-color: var(--mp-ok-line); color: var(--mp-ok); }
.mp-notice-warning { background: var(--mp-warn-bg); border-color: var(--mp-warn-line); color: var(--mp-warn); }
.mp-notice-error { background: var(--mp-bad-bg); border-color: var(--mp-bad-line); color: var(--mp-bad); }
.mp-notice-info { background: var(--mp-info-bg); border-color: var(--mp-info-line); color: var(--mp-info); }
.mp-notice a { color: inherit; font-weight: 600; }

.mp-card { margin: 0 0 1em; padding: 1em 1.1em; border: 1px solid var(--mp-line); border-radius: var(--mp-radius); background: var(--mp-card); }
.mp-card > h2 { font-size: 1.15em; line-height: 1.3; margin: 0 0 .3em; }
.mp-card > .mp-lead { color: var(--mp-muted); max-width: 52em; }
.mp-card > form { margin: 0; }

/* The main switch: one sentence, its state, one button. On and off are the same kind of button. */
.mp-master { display: grid; grid-template-columns: minmax(0, 1fr) auto; gap: .6em 1em; align-items: center; }
.mp-master h2 { font-size: 1.2em; margin: 0 0 .15em; }
.mp-master p { margin: 0; color: var(--mp-muted); }
.mp-master .mp-state { font-weight: 650; color: var(--mp-ink); }
.mp-master form { margin: 0; }
@container mp (max-width: 34em) { .mp-master { grid-template-columns: minmax(0, 1fr); } .mp-master .mp-btn { width: 100%; } }

/* A category: a switch (a checkbox drawn as one), its name and what it is, the frequency where it has one. */
.mp-list { margin: .4em 0 0; padding: 0; list-style: none; }
.mp-cat { display: grid; grid-template-columns: auto minmax(0, 1fr); gap: .2em .8em; padding: .85em 0; border-top: 1px solid var(--mp-line); }
.mp-cat:first-child { border-top: 0; }
.mp-cat-text { min-width: 0; overflow-wrap: anywhere; }
.mp-cat-name { display: flex; flex-wrap: wrap; align-items: center; gap: .3em .6em; font-weight: 650; }
.mp-cat-desc { margin: .1em 0 0; color: var(--mp-muted); }
.mp-paused .mp-cat-name { color: var(--mp-muted); }

.mp-switch { position: relative; display: inline-block; width: 2.9em; height: 1.65em; margin: .05em 0 0; flex: none; }
.mp-switch input { position: absolute; inset: 0; width: 100%; height: 100%; margin: 0; opacity: 0; cursor: pointer; z-index: 1; }
.mp-switch .mp-track { position: absolute; inset: 0; border-radius: 999px; background: #8b929e; transition: background .15s; }
.mp-switch .mp-track::after { content: ""; position: absolute; top: .2em; left: .2em; width: 1.25em; height: 1.25em; border-radius: 50%; background: #fff; box-shadow: 0 1px 2px rgba(0,0,0,.3); transition: transform .15s; }
.mp-switch input:checked + .mp-track { background: var(--mp-accent); }
.mp-switch input:checked + .mp-track::after { transform: translateX(1.25em); }
.mp-switch input:focus-visible + .mp-track { outline: 2px solid var(--mp-accent); outline-offset: 2px; }
.mp-switch input:disabled { cursor: not-allowed; }
.mp-switch input:disabled + .mp-track { opacity: .55; }
@media (prefers-reduced-motion: reduce) { .mp-switch .mp-track, .mp-switch .mp-track::after { transition: none; } }

.mp-badge { display: inline-block; padding: .08em .6em; border-radius: 999px; font-size: .78em; font-weight: 700; white-space: nowrap; background: var(--mp-soft); color: var(--mp-muted); border: 1px solid var(--mp-line); }
.mp-badge.on { background: var(--mp-ok-bg); color: var(--mp-ok); border-color: var(--mp-ok-line); }
.mp-badge.pending { background: var(--mp-warn-bg); color: var(--mp-warn); border-color: var(--mp-warn-line); }
.mp-badge.bad { background: var(--mp-bad-bg); color: var(--mp-bad); border-color: var(--mp-bad-line); }

.mp-freq { margin: .45em 0 0; padding: 0; border: 0; min-width: 0; background: none; box-shadow: none; }
.mp-freq legend { float: left; margin: 0 .6em 0 0; padding: 0; font-size: .9em; font-weight: 400; color: var(--mp-muted); width: auto; border: 0; background: none; }
.mp-freq label { display: inline-flex; align-items: center; gap: .3em; margin: 0 .9em .2em 0; font-size: .95em; white-space: nowrap; }
.mp-freq input { width: 1.05em; height: 1.05em; margin: 0; accent-color: var(--mp-accent); }
.mp-part { margin: .8em 0 0; }
.mp-hint { margin: .35em 0 0; font-size: .9em; color: var(--mp-muted); }

/* Essential mail: listed, not switchable, with the reason */
.mp-essential { margin: .4em 0 0; padding: 0; list-style: none; }
.mp-essential li { display: grid; grid-template-columns: 1.4em minmax(0, 1fr); gap: .1em .6em; padding: .6em 0; border-top: 1px solid var(--mp-line); overflow-wrap: anywhere; }
.mp-essential li:first-child { border-top: 0; }
.mp-essential svg { width: 1.1em; height: 1.1em; margin: .2em 0 0; fill: var(--mp-muted); }
.mp-essential b { display: block; }
.mp-essential span { color: var(--mp-muted); }

.mp-actions { display: flex; flex-wrap: wrap; gap: .5em; margin: .9em 0 0; align-items: center; }
.mp-btn { display: inline-block; max-width: 100%; padding: .5em 1.05em; border: 1px solid var(--mp-ink); border-radius: 8px; background: var(--mp-card); color: var(--mp-ink); font: inherit; font-weight: 650; line-height: 1.3; cursor: pointer; text-decoration: none; text-align: center; white-space: normal; }
.mp-btn:hover { background: var(--mp-soft); }
.mp-btn:focus-visible { outline: 2px solid var(--mp-accent); outline-offset: 2px; }
.mp a.mp-btn { color: var(--mp-ink); }
.mp-btn.primary { background: var(--mp-accent); border-color: var(--mp-accent); color: #fff; }
.mp-btn.primary:hover { background: var(--mp-accent-dark); border-color: var(--mp-accent-dark); }
.mp-btn.small { padding: .3em .75em; font-size: .9em; }
.mp-btn[disabled] { opacity: .5; cursor: not-allowed; }

.mp-field { display: grid; grid-template-columns: minmax(0, 1fr); gap: .25em; margin: 0 0 .8em; max-width: 34em; min-width: 0; }
.mp fieldset.mp-field { background: none; box-shadow: none; }
.mp fieldset.mp-field legend { background: none; padding: 0; margin: 0 0 .3em; font-size: 1em; font-weight: 650; color: inherit; border: 0; }
.mp-field > label, .mp-field > .mp-label { font-weight: 650; }
.mp-field input[type=text], .mp-field input[type=email], .mp-field input[type=date], .mp-field select, .mp-field textarea {
    width: 100%; padding: .5em .65em; border: 1px solid #8b929e; border-radius: 7px; font: inherit; background: #fff; color: #1f2430; }
.mp-field input:focus-visible, .mp-field select:focus-visible, .mp-field textarea:focus-visible { outline: 2px solid var(--mp-accent); outline-offset: 1px; }
.mp-check { display: flex; gap: .5em; align-items: flex-start; margin: 0 0 .5em; min-width: 0; }
.mp-check span { min-width: 0; overflow-wrap: anywhere; white-space: normal; }
.mp-check input { width: 1.15em; height: 1.15em; margin: .2em 0 0; flex: none; accent-color: var(--mp-accent); }
.mp-filters { display: grid; grid-template-columns: repeat(auto-fit, minmax(12em, 1fr)); gap: .2em 1em; align-items: end; }
.mp-filters .mp-field { margin: 0 0 .6em; }

.mp-table { width: 100%; border-collapse: collapse; font-size: .93em; }
.mp-table th, .mp-table td { padding: .45em .55em; text-align: left; border-top: 1px solid var(--mp-line); vertical-align: top; overflow-wrap: break-word; }
.mp-table thead th { font-size: .8em; color: var(--mp-muted); text-transform: uppercase; letter-spacing: .03em; border-top: 0; white-space: nowrap; }
.mp-table td.mp-num { white-space: nowrap; }
.mp-table form { margin: 0; }
.mp-scroll { overflow-x: auto; position: relative; }
.mp-table .mp-badge { white-space: normal; border-radius: .7em; }
.mp-wording { color: var(--mp-muted); font-size: .92em; }
/* Narrow screens: a table row becomes a small card, each cell with its column name */
@container mp (max-width: 40em) {
  .mp-table.mp-stack thead { position: absolute; width: 1px; height: 1px; overflow: hidden; clip: rect(0 0 0 0); }
  .mp-table.mp-stack, .mp-table.mp-stack tbody, .mp-table.mp-stack tr, .mp-table.mp-stack td, .mp-table.mp-stack th { display: block; width: 100%; }
  .mp-table.mp-stack tr { padding: .5em 0; border-top: 1px solid var(--mp-line); }
  .mp-table.mp-stack tr:first-child { border-top: 0; }
  .mp-table.mp-stack td, .mp-table.mp-stack th { border: 0; padding: .1em 0; }
  .mp-table.mp-stack td[data-label]::before { content: attr(data-label) ": "; color: var(--mp-muted); font-size: .85em; }
}

.mp-stats { display: grid; grid-template-columns: repeat(auto-fit, minmax(10em, 1fr)); gap: .6em; margin: 0 0 1em; padding: 0; list-style: none; }
.mp-stat { height: 100%; padding: .65em .8em; border: 1px solid var(--mp-line); border-radius: var(--mp-radius); background: var(--mp-card); }
.mp-stat strong { display: block; font-size: 1.45em; line-height: 1.15; overflow-wrap: anywhere; }
.mp-stat span { display: block; font-size: .85em; color: var(--mp-muted); overflow-wrap: anywhere; }
.mp-stat.bad strong { color: var(--mp-bad); }
.mp-problems { margin: 0 0 1em; padding: 0; list-style: none; display: grid; gap: .4em; }
.mp-problems li { padding: .55em .8em; border-radius: var(--mp-radius); border: 1px solid; }
.mp-problems .error { background: var(--mp-bad-bg); border-color: var(--mp-bad-line); color: var(--mp-bad); }
.mp-problems .warning { background: var(--mp-warn-bg); border-color: var(--mp-warn-line); color: var(--mp-warn); }
.mp-problems .info { background: var(--mp-info-bg); border-color: var(--mp-info-line); color: var(--mp-info); }
.mp-facts { display: grid; grid-template-columns: repeat(auto-fit, minmax(12em, 1fr)); gap: .5em 1.2em; margin: .4em 0 0; }
.mp-facts dt { font-size: .8em; color: var(--mp-muted); text-transform: uppercase; letter-spacing: .03em; }
.mp-facts dd { margin: 0; overflow-wrap: anywhere; }
.mp-layout { display: grid; grid-template-columns: minmax(0, 1fr); gap: 0 1em; }
@container mp (min-width: 56em) { .mp-layout.two { grid-template-columns: minmax(0, 1fr) minmax(0, 1fr); } }
.mp-pager { display: flex; flex-wrap: wrap; gap: .5em; justify-content: space-between; align-items: center; margin: .7em 0 0; font-size: .92em; color: var(--mp-muted); }
.mp-tabs { display: flex; flex-wrap: wrap; gap: .4em; margin: 0 0 1em; padding: 0; list-style: none; }
.mp-tabs a { display: inline-block; padding: .3em .85em; border: 1px solid var(--mp-line); border-radius: 999px; background: var(--mp-card); color: var(--mp-ink); text-decoration: none; font-size: .92em; }
.mp-tabs a:hover { border-color: var(--mp-accent); }
.mp-tabs a[aria-current=page] { background: var(--mp-accent); border-color: var(--mp-accent); color: #fff; }
.mp-big { max-width: 36em; }
.mp-big .mp-card { padding: 1.3em 1.4em; }

@media (max-width: 600px) {
  .mp-head h1 { font-size: 1.35em; }
  .mp-actions .mp-btn { flex: 1 1 10em; }
}
{/literal}
</style>
