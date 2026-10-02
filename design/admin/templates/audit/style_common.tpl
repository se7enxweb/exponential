{* The rules of the audit views, on the tokens audit/style.tpl of each design sets. Charts: categorical slots
   --au-s1 ... --au-s8 in a fixed order (content, access, system, commerce, read), status colours for refusals
   and failures, 2px surface gaps between segments, 4px rounded bar ends. *}
<style type="text/css">
{literal}
.au-view { color: var(--au-ink); }
.au-view .au-note { color: var(--au-muted); font-size: .9em; }
.au-view .au-chains { display: flex; flex-wrap: wrap; gap: .4em .6em; margin: 0 0 .8em; padding: 0; list-style: none; }
.au-view .au-chain { display: inline-block; padding: .2em .6em; border-radius: var(--au-radius); background: var(--au-line); color: var(--au-ink); font-size: .9em; }
.au-view .au-chain.intact { background: var(--au-ok-bg); color: var(--au-ok); }
.au-view .au-chain.repaired, .au-view .au-chain.unchecked { background: var(--au-warn-bg); color: var(--au-warn); }
.au-view .au-chain.broken { background: var(--au-bad-bg); color: var(--au-bad); font-weight: bold; }
.au-view .au-result { display: inline-block; padding: .1em .5em; border-radius: var(--au-radius); font-size: .8em; font-weight: bold; text-transform: uppercase; white-space: nowrap; background: var(--au-line); color: var(--au-ink); }
.au-view .au-result.success, .au-view .au-result.intact { background: var(--au-ok-bg); color: var(--au-ok); }
.au-view .au-result.refused, .au-view .au-result.repaired, .au-view .au-result.unchecked { background: var(--au-warn-bg); color: var(--au-warn); }
.au-view .au-result.failed, .au-view .au-result.broken, .au-view .au-result.no { background: var(--au-bad-bg); color: var(--au-bad); }
.au-view .au-filter { display: grid; grid-template-columns: repeat( auto-fill, minmax( 11em, 1fr ) ); gap: .5em .8em; margin: 0 0 .8em; padding: .8em; border: 1px solid var(--au-line); border-radius: var(--au-radius); background: var(--au-soft); }
.au-view .au-filter label { display: block; font-size: .85em; font-weight: bold; margin: 0 0 .15em; color: var(--au-muted); }
.au-view .au-filter input[type=text], .au-view .au-filter input[type=date], .au-view .au-filter select { width: 100%; box-sizing: border-box; }
.au-view .au-filter .au-wide { grid-column: span 2; }
.au-view .au-filter .au-actions { display: flex; gap: .5em; align-items: end; }
.au-view .au-bar { display: flex; flex-wrap: wrap; align-items: center; justify-content: space-between; gap: .5em; margin: .4em 0; }
.au-view .au-active { display: flex; flex-wrap: wrap; gap: .3em; margin: 0 0 .6em; padding: 0; list-style: none; }
.au-view .au-active li { padding: .1em .55em; border-radius: var(--au-radius); background: var(--au-soft); border: 1px solid var(--au-line); font-size: .85em; }
.au-view .au-table { width: 100%; overflow-x: auto; }
.au-view table.list td { vertical-align: top; overflow-wrap: anywhere; }
.au-view td.au-time { white-space: nowrap; }
.au-view td.au-name code, .au-view code { font-size: .85em; }
.au-view tr.au-child td.au-name { padding-left: 1.4em; }
.au-view .au-sub { color: var(--au-muted); font-size: .85em; }
.au-view .au-nowrap { white-space: nowrap; }
.au-view .au-empty { padding: 1.2em; text-align: center; color: var(--au-muted); }
.au-view .au-pager { display: flex; justify-content: space-between; align-items: center; gap: .6em; margin: .6em 0; }
.au-view dl.au-fields { display: grid; grid-template-columns: minmax( 7em, max-content ) 1fr; gap: .3em 1em; margin: 0 0 1em; }
.au-view dl.au-fields dt { font-weight: bold; color: var(--au-muted); }
.au-view dl.au-fields dd { margin: 0; overflow-wrap: anywhere; }
.au-view .au-changes td.au-changed { background: var(--au-warn-bg); }
.au-view pre.au-raw { max-height: 28em; overflow: auto; padding: .8em; border-radius: var(--au-radius); background: var(--au-soft); border: 1px solid var(--au-line); font-size: .8em; white-space: pre-wrap; overflow-wrap: anywhere; }
.au-view h2.au-h { font-size: 1.1em; margin: 1.2em 0 .5em; }
.au-view .au-cards { display: grid; grid-template-columns: repeat( auto-fit, minmax( 20em, 1fr ) ); gap: 1em; }
.au-view .au-card { border: 1px solid var(--au-line); border-radius: var(--au-radius); padding: .8em 1em; }
/* charts */
.au-view .au-chart { margin: 0 0 1em; }
.au-view .au-legend { display: flex; flex-wrap: wrap; gap: .3em 1em; margin: 0 0 .5em; padding: 0; list-style: none; font-size: .85em; color: var(--au-muted); }
.au-view .au-key { display: inline-block; width: .8em; height: .8em; border-radius: 2px; margin-right: .3em; vertical-align: -1px; }
.au-view .au-rows { display: grid; grid-template-columns: max-content 1fr max-content; gap: 3px .6em; align-items: center; }
.au-view .au-rows .au-lab { font-size: .85em; color: var(--au-muted); white-space: nowrap; }
.au-view .au-rows .au-val { font-size: .85em; color: var(--au-ink); text-align: right; font-variant-numeric: tabular-nums; }
.au-view .au-track { display: flex; gap: 2px; height: 14px; min-width: 0; }
.au-view .au-seg { height: 100%; min-width: 0; }
.au-view .au-seg:last-child { border-radius: 0 4px 4px 0; }
.au-view .au-seg:hover, .au-view .au-seg:focus { outline: 2px solid var(--au-ink); outline-offset: 1px; }
.au-view .s1 { background: var(--au-s1); } .au-view .s2 { background: var(--au-s2); } .au-view .s3 { background: var(--au-s3); }
.au-view .s4 { background: var(--au-s4); } .au-view .s5 { background: var(--au-s5); } .au-view .s6 { background: var(--au-s6); }
.au-view .s7 { background: var(--au-s7); } .au-view .s8 { background: var(--au-s8); }
.au-view .serious { background: var(--au-serious); } .au-view .critical { background: var(--au-critical); }
.au-view details.au-data { margin: .4em 0 1em; }
.au-view details.au-data summary { cursor: pointer; color: var(--au-muted); font-size: .9em; }
@media ( max-width: 640px ) {
  .au-view .au-filter .au-wide { grid-column: auto; }
  .au-view dl.au-fields { grid-template-columns: 1fr; }
}
{/literal}
</style>
