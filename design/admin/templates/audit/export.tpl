{* audit/export without a format: the choice of format for the current filter. Variables: filters, total, max,
   console_url, urls (csv, jsonl, json => module/view path). *}
{include uri='design:audit/style.tpl'}
<div class="context-block au-view" id="exp-audit-export">

{* DESIGN: Header START *}<div class="box-header"><div class="box-ml">
<h1 class="context-title">{'Export audit events'|i18n( 'design/admin/audit' )}</h1>
{* DESIGN: Mainline *}<div class="header-mainline"></div>
{* DESIGN: Header END *}</div></div>

{* DESIGN: Content START *}<div class="box-ml"><div class="box-mr"><div class="box-content">

<div class="block">
{if $filters|count}
<p>{'The filter:'|i18n( 'design/admin/audit' )}</p>
<ul class="au-active">{foreach $filters as $k => $v}<li>{$k|wash}: <strong>{$v|wash}</strong></li>{/foreach}</ul>
{else}
<p>{'No filter: every audit event you may read.'|i18n( 'design/admin/audit' )}</p>
{/if}
<p>{'%total events, newest first.'|i18n( 'design/admin/audit',, hash( '%total', $total ) )}
{if gt( $total, $max )}<strong>{'Only the newest %max are exported here; exp:audit export has no limit.'|i18n( 'design/admin/audit',, hash( '%max', $max ) )}</strong>{/if}</p>

<ul>
    <li><a href={$urls.csv|ezurl}>{'CSV'|i18n( 'design/admin/audit' )}</a> &ndash; {'one row per event, for a spreadsheet'|i18n( 'design/admin/audit' )}</li>
    <li><a href={$urls.jsonl|ezurl}>{'JSON lines'|i18n( 'design/admin/audit' )}</a> &ndash; {'the records as written, one per line; their hashes can be checked'|i18n( 'design/admin/audit' )}</li>
    <li><a href={$urls.json|ezurl}>{'JSON'|i18n( 'design/admin/audit' )}</a> &ndash; {'the records as one array'|i18n( 'design/admin/audit' )}</li>
</ul>
<p class="au-note">{'An export is recorded (system.audit.export) with the filter, the format, the count and the sha256 of the file.'|i18n( 'design/admin/audit' )}</p>
<p><a href={$console_url|ezurl}>&larr; {'Back to the console'|i18n( 'design/admin/audit' )}</a></p>
</div>

{* DESIGN: Content END *}</div></div></div>
</div>
