{* audit/export without a format: the choice of format for the current filter. Variables: filters, total, max,
   console_url, urls (csv, jsonl, json => module/view path), audit_sources (audit/sources.tpl), source_columns (label,
   columns: the CSV columns the source of the filtered channel adds), max_background, export_url, background (the
   background export is available), background_jobs (the user's background exports), background_error. *}
<div class="context-block a4-audit-view a4-page" id="exp-audit-export">

{* DESIGN: Header START *}<div class="box-header"><div class="box-ml">
<h1 class="context-title">{'Export audit events'|i18n( 'design/admin/audit' )}</h1>
{* DESIGN: Mainline *}<div class="header-mainline"></div>
{* DESIGN: Header END *}</div></div>

{* DESIGN: Content START *}<div class="box-ml"><div class="box-mr"><div class="box-content">

<div class="block">
{if $filters|count}
<p>{'The filter:'|i18n( 'design/admin/audit' )}</p>
<ul class="a4-audit-active">{foreach $filters as $k => $v}<li>{$k|wash}: <strong>{$v|wash}</strong></li>{/foreach}</ul>
{else}
<p>{'No filter: every audit event you may read.'|i18n( 'design/admin/audit' )}</p>
{/if}
<p>{'%total events, newest first.'|i18n( 'design/admin/audit',, hash( '%total', $total ) )}
{if gt( $total, $max )}<strong>{'A download here has the newest %max of them; an export in the background has up to %background.'|i18n( 'design/admin/audit',, hash( '%max', $max, '%background', $max_background ) )}</strong>{/if}</p>

<h2>{'Download now'|i18n( 'design/admin/audit' )}</h2>
<p class="a4-audit-note">{'The file starts at once and is sent while it is read; %count events.'|i18n( 'design/admin/audit',, hash( '%count', min( $total, $max ) ) )}</p>
<ul>
    <li><a href={$urls.csv|ezurl}>{'CSV'|i18n( 'design/admin/audit' )}</a> &ndash; {'one row per event, for a spreadsheet'|i18n( 'design/admin/audit' )}{if and( is_set( $source_columns ), $source_columns.columns|count )}; {'with the columns of %source: %columns'|i18n( 'design/admin/audit',, hash( '%source', $source_columns.label, '%columns', $source_columns.columns|implode( ', ' ) ) )|wash}{/if}</li>
    <li><a href={$urls.jsonl|ezurl}>{'JSON lines'|i18n( 'design/admin/audit' )}</a> &ndash; {'the records as written, one per line; their hashes can be checked'|i18n( 'design/admin/audit' )}</li>
    <li><a href={$urls.json|ezurl}>{'JSON'|i18n( 'design/admin/audit' )}</a> &ndash; {'the records as one array'|i18n( 'design/admin/audit' )}</li>
</ul>

{if $background}
<h2>{'Export in the background'|i18n( 'design/admin/audit' )}</h2>
<p class="a4-audit-note">{'For a large filter: a background job writes the file (%count events), shows its progress and offers it here when it is done. Only you can download it; it is removed with the job after the days the content jobs are kept.'|i18n( 'design/admin/audit',, hash( '%count', min( $total, $max_background ) ) )}</p>
{if $background_error}<p class="a4-audit-hintbox"><strong>{$background_error|wash}</strong></p>{/if}
<form method="post" action={$export_url|ezurl}>
<p>
    <label for="au-background-format">{'Format'|i18n( 'design/admin/audit' )}</label>
    <select id="au-background-format" name="BackgroundFormat">
        <option value="csv">{'CSV'|i18n( 'design/admin/audit' )}</option>
        <option value="jsonl">{'JSON lines'|i18n( 'design/admin/audit' )}</option>
        <option value="json">{'JSON'|i18n( 'design/admin/audit' )}</option>
    </select>
    <input type="submit" class="button" name="BackgroundExportButton" value="{'Start the export'|i18n( 'design/admin/audit' )|wash}" />
</p>
</form>
{if $background_jobs|count}
<div class="a4-audit-table">
<table class="list" cellspacing="0">
<tr>
    <th scope="col">{'Started'|i18n( 'design/admin/audit' )}</th>
    <th scope="col">{'Export'|i18n( 'design/admin/audit' )}</th>
    <th scope="col">{'State'|i18n( 'design/admin/audit' )}</th>
    <th scope="col">{'File'|i18n( 'design/admin/audit' )}</th>
</tr>
{foreach $background_jobs as $j sequence array( 'bglight', 'bgdark' ) as $style}
<tr class="{$style}">
    <td class="a4-audit-nowrap">{$j.created|l10n( 'shortdatetime' )}</td>
    <td>{$j.description|wash}</td>
    <td><a href={$j.job_url|ezurl}><span class="a4-audit-result {if eq( $j.state, 'done' )}success{elseif eq( $j.state, 'failed' )}failed{/if}">{$j.state|wash}</span></a>{if or( eq( $j.state, 'running' ), eq( $j.state, 'queued' ) )} {'%done of %total'|i18n( 'design/admin/audit',, hash( '%done', $j.done, '%total', $j.total ) )}{/if}</td>
    <td>{if $j.download_url}<a href={$j.download_url|ezurl}>{'Download'|i18n( 'design/admin/audit' )}</a> ({'%count events'|i18n( 'design/admin/audit',, hash( '%count', $j.count ) )}, {$j.bytes|si( byte )}){else}&ndash;{/if}</td>
</tr>
{/foreach}
</table>
</div>
{/if}
{/if}
{include uri='design:audit/sources.tpl'}
<p class="a4-audit-note">{'An export is recorded (system.audit.export) with the filter, the format, the count and the sha256 of the file.'|i18n( 'design/admin/audit' )}</p>
<p><a href={$console_url|ezurl}>&larr; {'Back to the console'|i18n( 'design/admin/audit' )}</a></p>
</div>

{* DESIGN: Content END *}</div></div></div>
</div>
