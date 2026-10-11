{* The content finder's styles (contentfinder/search, contentfinder/action). The admin4 design variables are used
   where it defines them, with the admin colours as fallback, so one file serves every administration design. *}
<style type="text/css">
{literal}
.exp-cf .cf-form { display: flex; flex-wrap: wrap; gap: .5em; align-items: flex-end; margin: 0 0 .6em; }
.exp-cf .cf-form label { display: block; font-weight: bold; margin: 0 0 .2em; }
.exp-cf .cf-form .cf-text { flex: 1 1 22em; min-width: 0; }
.exp-cf .cf-form .cf-text input { width: 100%; box-sizing: border-box; }
.exp-cf .cf-form .cf-type select { max-width: 100%; }
.exp-cf .cf-hint { color: var(--a4-muted, #555); font-size: .9em; margin: 0 0 1em; }
.exp-cf .cf-hint code, .exp-cf td code, .exp-cf dd code { font-size: .9em; overflow-wrap: anywhere; }
.exp-cf .cf-result { border: 1px solid var(--a4-line, #ccc); border-radius: var(--a4-radius-s, 4px); padding: .6em .8em; margin: 0 0 1.2em; }
.exp-cf .cf-result h2 { margin: 0 0 .3em; font-size: 1.2em; }
.exp-cf .cf-result h3 { margin: 1em 0 .3em; font-size: 1em; }
.exp-cf .cf-meta { display: grid; grid-template-columns: minmax(8em, max-content) 1fr; gap: .15em 1em; margin: .4em 0; }
.exp-cf .cf-meta dt { font-weight: bold; }
.exp-cf .cf-meta dd { margin: 0; overflow-wrap: anywhere; }
.exp-cf .cf-match { background: var(--a4-orange-soft, #fdf1e3); border-radius: var(--a4-radius-s, 4px); padding: .3em .6em; margin: .2em 0 .5em; }
.exp-cf .cf-badge { display: inline-block; padding: .05em .45em; border-radius: var(--a4-radius-s, 4px); font-size: .8em; font-weight: bold; white-space: nowrap; background: var(--a4-line, #e8e8e8); color: var(--a4-muted, #444); }
.exp-cf .cf-badge.published, .exp-cf .cf-badge.main { background: #e1f1e2; color: #2e7d32; }
.exp-cf .cf-badge.archived, .exp-cf .cf-badge.trash { background: #fbe3e6; color: #b00020; }
.exp-cf .cf-badge.draft, .exp-cf .cf-badge.internal_draft, .exp-cf .cf-badge.pending { background: #fde7d9; color: #b84a0e; }
.exp-cf .cf-table { width: 100%; overflow-x: auto; }
.exp-cf .cf-table table { width: 100%; }
.exp-cf .cf-table td { vertical-align: top; overflow-wrap: anywhere; }
.exp-cf .cf-actions { display: flex; flex-wrap: wrap; gap: .5em; margin: .8em 0 0; }
.exp-cf .cf-actions form { margin: 0; display: inline; }
.exp-cf .cf-inline { display: inline; margin: 0; }
.exp-cf .cf-danger { border-color: #b00020; color: #b00020; }
.exp-cf .cf-none { padding: 1em; }
.exp-cf .cf-plan li { margin: .2em 0; }
.exp-cf .cf-permanent { margin: .8em 0; padding: .5em .7em; border: 1px solid #b00020; border-radius: var(--a4-radius-s, 4px); }
@media (max-width: 640px) {
  .exp-cf .cf-meta { grid-template-columns: 1fr; }
  .exp-cf .cf-meta dt { margin-top: .3em; }
  .exp-cf .cf-table table, .exp-cf .cf-table tbody, .exp-cf .cf-table tr, .exp-cf .cf-table td { display: block; width: auto; }
  .exp-cf .cf-table tr:first-child { display: none; }
  .exp-cf .cf-table tr { border-bottom: 1px solid var(--a4-line, #ccc); padding: .3em 0; }
  .exp-cf .cf-table td[data-label]::before { content: attr(data-label) ": "; font-weight: bold; }
}
{/literal}
</style>
