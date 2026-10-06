{* The styles of the API access page (apikey/list), for every design: tokens (--ak-*) that take admin4's values when
   the page runs there (--a4-*) and plain defaults on a public site. Scoped to .ak; included once by
   parts/page_start.tpl. Every text has at least 4.5:1 contrast on its background. *}
{literal}
<style>
.ak { --ak-ink: var(--a4-ink, #1f2430); --ak-muted: var(--a4-muted, #5a6270); --ak-line: var(--a4-line, #d9dde3);
      --ak-soft: var(--a4-soft, #f5f6f8); --ak-card: #fff; --ak-radius: 10px;
      --ak-accent: #c2410c; --ak-accent-dark: #9a3412; --ak-ring: rgba(194, 65, 12, .45);
      --ak-ok: #1e5e22; --ak-ok-bg: #e1f1e2; --ak-ok-line: #b9e2c5;
      --ak-warn: #7a3410; --ak-warn-bg: #fdeadc; --ak-warn-line: #f2d59a;
      --ak-bad: #9b001c; --ak-bad-bg: #fbe3e6; --ak-bad-line: #f3bcbc;
      --ak-info: #1f4a7f; --ak-info-bg: #e8f0fb; --ak-info-line: #bcd2f0;
      --ak-mono: ui-monospace, SFMono-Regular, Menlo, Consolas, monospace;
      color: var(--ak-ink); font-size: 1rem; line-height: 1.55; max-width: 62em; container: ak / inline-size; }
.ak.ak-public { margin: 1.5em auto 2.5em; padding: 0 16px; }
.ak *, .ak *::before, .ak *::after { box-sizing: border-box; }
.ak [hidden] { display: none !important; }
.ak h1, .ak h2, .ak h3 { margin: 0; padding: 0; border: 0; background: none; box-shadow: none; color: inherit; font-family: inherit; text-transform: none; letter-spacing: normal; }
.ak p { margin: 0 0 .5em; }
.ak a { color: var(--ak-accent-dark); }
.ak a:hover { color: var(--ak-ink); }
.ak label { display: inline; float: none; width: auto; margin: 0; padding: 0; font-weight: 400; white-space: normal; }
.ak b, .ak strong { font-weight: 650; }
.ak :focus-visible { outline: 3px solid var(--ak-ring); outline-offset: 2px; }
.ak .ak-sr { position: absolute; width: 1px; height: 1px; margin: -1px; overflow: hidden; clip: rect(0 0 0 0); white-space: nowrap; border: 0; }
.ak code { padding: .05em .35em; border-radius: 5px; background: var(--ak-soft); color: var(--ak-ink); font-family: var(--ak-mono); font-size: .88em; overflow-wrap: anywhere; }
.ak .ak-muted { color: var(--ak-muted); }

.ak-head { margin: 0 0 1.2em; }
.ak-head h1 { margin: 0 0 .25em; font-size: 1.6em; line-height: 1.2; }
.ak-head p { max-width: 46em; color: var(--ak-muted); }
.ak-crumb { margin: 0 0 .3em; font-size: .88em; }

.ak-notice { margin: 0 0 1em; padding: .75em .95em; border: 1px solid; border-radius: var(--ak-radius); }
.ak-notice p:last-child, .ak-notice ul:last-child { margin-bottom: 0; }
.ak-notice ul { margin: .3em 0 0; padding-left: 1.2em; }
.ak-notice-ok { border-color: var(--ak-ok-line); background: var(--ak-ok-bg); color: var(--ak-ok); }
.ak-notice-warn { border-color: var(--ak-warn-line); background: var(--ak-warn-bg); color: var(--ak-warn); }
.ak-notice-bad { border-color: var(--ak-bad-line); background: var(--ak-bad-bg); color: var(--ak-bad); }
.ak-notice-info { border-color: var(--ak-info-line); background: var(--ak-info-bg); color: var(--ak-info); }
.ak-notice a { color: inherit; font-weight: 600; }

.ak-card { margin: 0 0 1.1em; padding: 1em 1.1em; border: 1px solid var(--ak-line); border-radius: var(--ak-radius); background: var(--ak-card); }
.ak-card > h2 { margin: 0 0 .35em; font-size: 1.15em; line-height: 1.3; }
.ak-card .ak-lead { max-width: 52em; color: var(--ak-muted); }
.ak-card form { margin: 0; }

.ak-btn { display: inline-flex; align-items: center; justify-content: center; gap: .4em; min-height: 2.5em; margin: 0; padding: .45em 1em;
          border: 1px solid var(--ak-accent); border-radius: 8px; background: var(--ak-card); color: var(--ak-accent-dark);
          font: inherit; font-weight: 650; line-height: 1.2; text-decoration: none; cursor: pointer; white-space: nowrap; }
.ak-btn:hover:not([disabled]) { background: var(--ak-accent); border-color: var(--ak-accent); color: #fff; }
.ak-btn[disabled] { opacity: .5; cursor: not-allowed; }
.ak-btn-primary { background: var(--ak-accent); color: #fff; }
.ak-btn-primary:hover:not([disabled]) { background: var(--ak-accent-dark); border-color: var(--ak-accent-dark); }
.ak-btn-danger { border-color: var(--ak-bad); background: var(--ak-bad); color: #fff; }
.ak-btn-danger:hover:not([disabled]) { border-color: #7a0016; background: #7a0016; }
.ak-btn-quiet { border-color: var(--ak-line); color: var(--ak-ink); }
.ak-btn-small { min-height: 2.1em; padding: .3em .75em; font-size: .9em; }
.ak-actions { display: flex; flex-wrap: wrap; align-items: center; gap: .5em .7em; margin: .8em 0 0; }

/* The new key: shown once */
.ak-new { border: 2px solid var(--ak-ok); background: var(--ak-ok-bg); }
.ak-new h2 { color: var(--ak-ok); }
.ak-new p { color: var(--ak-ink); }
.ak-token { display: flex; flex-wrap: wrap; align-items: stretch; gap: .5em; margin: .6em 0; }
.ak-token output, .ak-token code.ak-token-value { flex: 1 1 18em; min-width: 0; display: block; padding: .6em .75em; border: 1px solid var(--ak-ok-line); border-radius: 8px;
             background: #fff; color: var(--ak-ink); font-family: var(--ak-mono); font-size: .92em; overflow-wrap: anywhere; user-select: all; -webkit-user-select: all; }
.ak pre { margin: .4em 0 0; padding: .7em .85em; overflow-x: auto; border-radius: 8px; background: #16161a; color: #e4e4e7; font: .82em/1.6 var(--ak-mono); white-space: pre-wrap; overflow-wrap: anywhere; }
.ak pre code { padding: 0; background: none; color: inherit; font-size: 1em; }

/* The keys */
.ak-keys { margin: 0; padding: 0; list-style: none; }
.ak-key { margin: 0; padding: .9em 0; border-top: 1px solid var(--ak-line); }
.ak-key:first-child { border-top: 0; padding-top: .2em; }
.ak-key-head { display: flex; flex-wrap: wrap; align-items: center; justify-content: space-between; gap: .4em 1em; }
.ak-key-title { display: flex; flex-wrap: wrap; align-items: center; gap: .3em .6em; min-width: 0; }
.ak-key-title h3 { font-size: 1.05em; overflow-wrap: anywhere; }
.ak-badges { display: inline-flex; flex-wrap: wrap; gap: .35em; margin: 0; padding: 0; list-style: none; }
.ak-badge { display: inline-flex; align-items: center; padding: .05em .6em; border-radius: 999px; background: var(--ak-soft); color: var(--ak-muted); font-size: .8em; font-weight: 650; line-height: 1.6; white-space: nowrap; }
.ak-badge-ok { background: var(--ak-ok-bg); color: var(--ak-ok); }
.ak-badge-warn { background: var(--ak-warn-bg); color: var(--ak-warn); }
.ak-badge-bad { background: var(--ak-bad-bg); color: var(--ak-bad); }
.ak-badge-info { background: var(--ak-info-bg); color: var(--ak-info); }
.ak-facts { display: grid; grid-template-columns: repeat(auto-fit, minmax(min(100%, 11em), 1fr)); gap: .5em 1.2em; margin: .6em 0 0; }
.ak-facts > div { min-width: 0; }
.ak-facts dt { margin: 0; font-size: .78em; font-weight: 650; letter-spacing: .04em; text-transform: uppercase; color: var(--ak-muted); }
.ak-facts dd { margin: .1em 0 0; overflow-wrap: anywhere; }
.ak-key.is-revoked h3, .ak-key.is-expired h3 { color: var(--ak-muted); }

/* The form */
.ak-form { display: grid; grid-template-columns: minmax(0, 1fr); gap: 1em; max-width: 40em; }
.ak-field { display: flex; flex-direction: column; gap: .3em; min-width: 0; margin: 0; padding: 0; border: 0; }
.ak-field > label, .ak-field > legend { padding: 0; font-weight: 650; }
.ak-field input[type="text"], .ak-field input[type="password"], .ak-field select {
    width: 100%; min-height: 2.6em; margin: 0; padding: .45em .65em; border: 1px solid #a9b0bb; border-radius: 8px; background: #fff; color: var(--ak-ink); font: inherit; }
.ak-field input:focus, .ak-field select:focus { border-color: var(--ak-accent); outline: 3px solid var(--ak-ring); outline-offset: 0; }
.ak-hint { font-size: .88em; color: var(--ak-muted); }
.ak-scopes { display: grid; gap: .45em; margin: .2em 0 0; padding: 0; list-style: none; }
.ak-scope { display: flex; align-items: flex-start; gap: .55em; }
.ak-scope input { flex: 0 0 auto; width: 1.15em; height: 1.15em; margin: .2em 0 0; accent-color: var(--ak-accent); }
.ak-scope label { display: block; }
.ak-scope .ak-hint { display: block; }
.ak fieldset.ak-field > legend { float: left; width: 100%; margin: 0 0 .3em; padding: 0; border: 0; font-size: 1rem; line-height: 1.55; font-weight: 650; color: var(--ak-ink); text-transform: none; letter-spacing: normal; }
.ak fieldset.ak-field > legend + * { clear: both; }

.ak-two { display: grid; grid-template-columns: repeat(auto-fit, minmax(min(100%, 16em), 1fr)); gap: 1.1em; }
@container ak (max-width: 30em) { .ak-actions .ak-btn { flex: 1 1 auto; } }
</style>
{/literal}
