{* The styles of the notification views in admin4: tokens (--nf-*) built on the admin's own (--a4-*), the overview strip,
   the cards, the subscription list, the confirmation, the status page. Included once by each view. *}
<style type="text/css">
{literal}
.nf { --nf-ink: var(--a4-ink, #1f2430); --nf-muted: var(--a4-muted, #5d6573); --nf-line: var(--a4-line, #e3e6eb);
      --nf-soft: var(--a4-soft, #f6f7f9); --nf-radius: var(--a4-radius-s, 9px); --nf-accent: var(--a4-orange, #f26a21);
      --nf-accent-dark: var(--a4-orange-dark, #d9561a); --nf-accent-soft: var(--a4-orange-soft, rgba(242,106,33,.12));
      --nf-ok: #1e5e22; --nf-ok-bg: #e1f1e2; --nf-warn: #8a3a0c; --nf-warn-bg: #fde7d9; --nf-bad: #9b001c; --nf-bad-bg: #fbe3e6;
      --nf-info: #1f4a7f; --nf-info-bg: #e8f0fb; color: var(--nf-ink); container: nf / inline-size; }
.nf *, .nf *::before, .nf *::after { box-sizing: border-box; }
.nf h1, .nf h2, .nf h3 { margin: 0; padding: 0; border: 0; background: none; box-shadow: none; }
.nf label { font-weight: 400; }
.nf label b, .nf b { font-weight: 650; }
.nf-head { display: flex; flex-wrap: wrap; justify-content: space-between; align-items: flex-start; gap: .5em 1.2em; margin: 0 0 .9em; }
.nf-head h1 { font-size: 1.5em; line-height: 1.2; }
.nf-head p { margin: .2em 0 0; color: var(--nf-muted); max-width: 46em; }
.nf-crumb { margin: 0 0 .3em; font-size: .85em; color: var(--nf-muted); }
.nf-crumb a { color: var(--nf-accent-dark); }

.nf-notice { display: flex; gap: .6em; align-items: flex-start; margin: 0 0 1em; padding: .7em .9em; border-radius: var(--nf-radius); border: 1px solid; }
.nf-notice p { margin: 0; flex: 1; }
.nf-notice button { border: 0; background: none; color: inherit; font-size: 1.2em; line-height: 1; cursor: pointer; opacity: .6; }
.nf-notice-success { background: var(--nf-ok-bg); border-color: #b9e2c5; color: var(--nf-ok); }
.nf-notice-warning { background: var(--nf-warn-bg); border-color: #f2d59a; color: var(--nf-warn); }
.nf-notice-error { background: var(--nf-bad-bg); border-color: #f3bcbc; color: var(--nf-bad); }
.nf-notice-info { background: var(--nf-info-bg); border-color: #bcd2f0; color: var(--nf-info); }

.nf-stats { display: grid; grid-template-columns: repeat(auto-fit, minmax(11em, 1fr)); gap: .6em; margin: 0 0 1em; padding: 0; list-style: none; }
.nf-stat { display: block; height: 100%; padding: .65em .8em; border: 1px solid var(--nf-line); border-radius: var(--nf-radius); background: #fff; color: inherit; text-decoration: none; }
a.nf-stat:hover, a.nf-stat:focus-visible { border-color: var(--nf-accent); box-shadow: 0 0 0 3px var(--nf-accent-soft); outline: none; }
.nf-stat strong { display: block; font-size: 1.5em; line-height: 1.15; overflow-wrap: anywhere; }
.nf-stat span { display: block; font-size: .82em; color: var(--nf-muted); overflow-wrap: anywhere; }
.nf-stat.hot strong { color: var(--nf-accent-dark); }
.nf-stat.bad strong { color: var(--nf-bad); }

.nf-layout { display: grid; grid-template-columns: minmax(0, 1fr); gap: 1em; }
@container nf (min-width: 56em) { .nf-layout.two { grid-template-columns: minmax(0, 1fr) minmax(0, 1fr); } }
.nf-card { padding: .9em 1em; border: 1px solid var(--nf-line); border-radius: var(--nf-radius); background: #fff; margin: 0 0 1em; }
.nf-layout .nf-card { margin: 0; }
.nf-card > h2 { font-size: 1.1em; margin: 0 0 .25em; }
.nf-card > .nf-lead { margin: 0 0 .7em; color: var(--nf-muted); max-width: 52em; }
.nf-tag { display: inline-block; padding: .05em .55em; border-radius: 999px; background: var(--nf-soft); color: var(--nf-muted); font-size: .72em; font-weight: 600; vertical-align: middle; }

.nf-filters { display: flex; flex-wrap: wrap; gap: .4em .8em; align-items: center; margin: 0 0 .7em; }
.nf-filters form { display: flex; flex-wrap: wrap; gap: .4em; align-items: center; margin: 0; }
.nf-filters input[type=text], .nf-filters select, .nf-field input[type=text], .nf-field select { padding: .4em .6em; border: 1px solid var(--nf-line); border-radius: 7px; font: inherit; max-width: 100%; background: #fff; color: var(--nf-ink); }
.nf-chip { display: inline-block; padding: .2em .7em; border: 1px solid var(--nf-line); border-radius: 999px; background: #fff; color: var(--nf-ink); font-size: .88em; text-decoration: none; }
.nf-chip:hover { border-color: var(--nf-accent); }
.nf-chip.current { background: var(--nf-accent); border-color: var(--nf-accent); color: #fff; }

.nf-list { margin: 0 0 .7em; padding: 0; list-style: none; border: 1px solid var(--nf-line); border-radius: var(--nf-radius); background: #fff; overflow: hidden; }
.nf-row { display: grid; grid-template-columns: 2em minmax(0, 1fr); gap: .5em; padding: .6em .8em; border-top: 1px solid var(--nf-line); align-items: start; }
.nf-row:first-child { border-top: 0; }
.nf-row.gone { background: var(--nf-warn-bg); }
.nf-row input[type=checkbox] { width: 1.15em; height: 1.15em; margin: .2em 0 0; accent-color: var(--nf-accent); }
.nf-title { display: flex; flex-wrap: wrap; align-items: center; gap: .4em; font-weight: 600; overflow-wrap: anywhere; }
.nf-title a { color: inherit; }
.nf-meta { display: flex; flex-wrap: wrap; gap: .1em .9em; margin: .2em 0 0; font-size: .85em; color: var(--nf-muted); overflow-wrap: anywhere; }
.nf-badge { display: inline-block; padding: .08em .55em; border-radius: 999px; font-size: .76em; font-weight: 700; letter-spacing: .02em; white-space: nowrap; background: var(--nf-soft); color: var(--nf-muted); }
.nf-badge.warn { background: var(--nf-warn-bg); color: var(--nf-warn); }
.nf-badge.ok { background: var(--nf-ok-bg); color: var(--nf-ok); }
.nf-badge.bad { background: var(--nf-bad-bg); color: var(--nf-bad); }
.nf-selectall { display: flex; align-items: center; gap: .5em; margin: 0 0 .4em; font-size: .88em; color: var(--nf-muted); }
.nf-selectall input { accent-color: var(--nf-accent); }

.nf-actions { display: flex; flex-wrap: wrap; gap: .5em; margin: .7em 0 0; }
.nf-btn { display: inline-block; padding: .45em 1em; border: 1px solid var(--nf-line); border-radius: 8px; background: #fff; color: var(--nf-ink); font: inherit; font-weight: 600; cursor: pointer; text-decoration: none; }
.nf-btn:hover { border-color: var(--nf-accent); }
.nf-btn:focus-visible, .nf-chip:focus-visible { outline: 2px solid var(--nf-accent); outline-offset: 2px; }
.nf-btn.primary { background: var(--nf-accent); border-color: var(--nf-accent); color: #fff; }
.nf-btn.primary:hover { background: var(--nf-accent-dark); }
.nf-btn.danger { background: #fff; border-color: #c62828; color: #c62828; }
.nf-btn.danger:hover { background: #c62828; color: #fff; }
.nf-btn[disabled] { opacity: .5; cursor: not-allowed; }
.nf-hint { margin: .4em 0 0; font-size: .88em; color: var(--nf-muted); }

.nf-confirm { margin: 0 0 1em; padding: .8em 1em; border: 1px solid #f3bcbc; border-radius: var(--nf-radius); background: var(--nf-bad-bg); }
.nf-confirm h2 { font-size: 1.05em; color: var(--nf-bad); }
.nf-confirm ul { margin: .4em 0 .2em 1.2em; padding: 0; }

.nf-empty { padding: 1.2em 1em; border: 1px dashed var(--nf-line); border-radius: var(--nf-radius); background: #fff; margin: 0 0 .7em; }
.nf-empty h3 { font-size: 1.05em; margin: 0 0 .3em; }
.nf-empty p { margin: .3em 0; color: var(--nf-muted); max-width: 44em; }
.nf-empty code { padding: .05em .35em; background: var(--nf-soft); border-radius: 5px; font-size: .9em; }

.nf-choice { display: grid; grid-template-columns: 1.6em minmax(0, 1fr); gap: .2em .5em; align-items: center; margin: 0 0 .5em; }
.nf-choice input[type=radio], .nf-choice input[type=checkbox] { width: 1.1em; height: 1.1em; accent-color: var(--nf-accent); margin: 0; }
.nf-choice .nf-field { min-width: 0; display: flex; flex-wrap: wrap; gap: .4em; align-items: center; }
.nf-toggle { white-space: normal; max-width: 100%; display: flex; gap: .6em; align-items: flex-start; margin: 0 0 .8em; padding: .6em .8em; border: 1px solid var(--nf-line); border-radius: var(--nf-radius); background: var(--nf-soft); }
.nf-toggle > div { min-width: 0; overflow-wrap: anywhere; }
.nf-toggle input { flex: none; width: 1.2em; height: 1.2em; margin: .15em 0 0; accent-color: var(--nf-accent); }
.nf-toggle b { display: block; white-space: normal; }
.nf-toggle span { color: var(--nf-muted); font-size: .9em; }
.nf-types { display: grid; gap: .4em; margin: 0 0 .5em; }
.nf-types label { display: flex; gap: .5em; align-items: center; }
.nf-types input { width: 1.1em; height: 1.1em; accent-color: var(--nf-accent); }

.nf-table { width: 100%; border-collapse: collapse; font-size: .92em; }
.nf-table th, .nf-table td { padding: .35em .5em; text-align: left; border-top: 1px solid var(--nf-line); vertical-align: top; }
.nf-table th { font-size: .8em; color: var(--nf-muted); text-transform: uppercase; letter-spacing: .03em; border-top: 0; }
.nf-scroll { overflow-x: auto; }
.nf-console { max-height: 16em; overflow: auto; margin: .6em 0 0; padding: .6em .8em; border-radius: var(--nf-radius); background: #1f2430; color: #e8eaf0; font: .85em/1.45 ui-monospace, SFMono-Regular, Menlo, monospace; white-space: pre-wrap; overflow-wrap: anywhere; }
.nf-console[hidden] { display: none; }
.nf-problems { margin: 0 0 1em; padding: 0; list-style: none; display: grid; gap: .4em; }
.nf-problems li { padding: .55em .8em; border-radius: var(--nf-radius); border: 1px solid; }
.nf-problems .error { background: var(--nf-bad-bg); border-color: #f3bcbc; color: var(--nf-bad); }
.nf-problems .warning { background: var(--nf-warn-bg); border-color: #f2d59a; color: var(--nf-warn); }
.nf-problems .info { background: var(--nf-info-bg); border-color: #bcd2f0; color: var(--nf-info); }
.nf-facts { display: grid; grid-template-columns: repeat(auto-fit, minmax(11em, 1fr)); gap: .5em 1.2em; margin: .4em 0 0; }
.nf-facts dt { font-size: .78em; color: var(--nf-muted); text-transform: uppercase; letter-spacing: .03em; }
.nf-facts dd { margin: 0; overflow-wrap: anywhere; }
.nf code { padding: .05em .35em; background: var(--nf-soft); border-radius: 5px; font-size: .9em; overflow-wrap: anywhere; }

@media (max-width: 600px) {
  .nf-head h1 { font-size: 1.3em; }
  .nf-actions .nf-btn { flex: 1 1 8em; text-align: center; }
  .nf-row { padding: .6em .6em; }
}
{/literal}
</style>
