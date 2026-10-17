{* The plan of a log management action (setup/logsmanage; doc/features/6.0/log-viewer.md, "Managing logs"): every step
   it will take, nothing changed yet (the dry run). Confirm posts the same action with ConfirmButton; the plan is then
   made again from the disk and applied, each step recorded in the audit. Variables: log_op (rotate | truncate |
   compress | prune | rotate_all | prune_all), log_key, log_days, log_plan (op, steps => key, op, from, to, bytes,
   unknown), log_freed (bytes removed or emptied), log_token_field, log_token.

   The same file is in design/admin and design/admin4. *}
<div class="context-block a4-page a4-setup-logs a4-setup-logmanage">

<div class="box-header"><div class="box-ml">
<h1 class="context-title">{switch match=$log_op}
    {case match='rotate'}{'Rotate %key now?'|i18n( 'design/admin/setup/logs',, hash( '%key', $log_key ) )|wash}{/case}
    {case match='truncate'}{'Empty %key after archiving it?'|i18n( 'design/admin/setup/logs',, hash( '%key', $log_key ) )|wash}{/case}
    {case match='compress'}{'Compress the archives of %key?'|i18n( 'design/admin/setup/logs',, hash( '%key', $log_key ) )|wash}{/case}
    {case match='prune'}{'Delete the old archives of %key?'|i18n( 'design/admin/setup/logs',, hash( '%key', $log_key ) )|wash}{/case}
    {case match='rotate_all'}{'Rotate every log over its limit?'|i18n( 'design/admin/setup/logs' )}{/case}
    {case}{'Prune the archives of every log?'|i18n( 'design/admin/setup/logs' )}{/case}
{/switch}</h1>
<div class="header-mainline"></div>
</div></div>

<div class="box-bc"><div class="box-ml"><div class="box-content">
{include uri='design:setup/logs_nav.tpl' log_nav_current='manage'}

{if $log_plan.steps|count}
<div class="a4-notice {if or( $log_op|eq( 'truncate' ), $log_op|eq( 'prune' ), $log_op|eq( 'prune_all' ) )}is-bad{else}is-warn{/if}" role="alert">
    {'This is a preview: nothing has changed yet. Confirm to carry out these %count steps. The files are checked again before each step, and each step is recorded in the audit.'|i18n( 'design/admin/setup/logs',, hash( '%count', $log_plan.steps|count ) )|wash}
    {if $log_freed|gt( 0 )}{'%size are removed for good.'|i18n( 'design/admin/setup/logs',, hash( '%size', $log_freed|si( byte ) ) )|wash}{/if}
</div>

<div class="a4-table-wrap">
<table class="a4-table a4-setup-logmanage-plan">
<caption class="a4-sr-only">{'The steps of this action'|i18n( 'design/admin/setup/logs' )}</caption>
<thead>
<tr>
    <th scope="col" class="a4-num">#</th>
    <th scope="col">{'Log'|i18n( 'design/admin/setup/logs' )}</th>
    <th scope="col">{'Step'|i18n( 'design/admin/setup/logs' )}</th>
    <th scope="col">{'File'|i18n( 'design/admin/setup/logs' )}</th>
    <th scope="col">{'Becomes'|i18n( 'design/admin/setup/logs' )}</th>
    <th scope="col" class="a4-num">{'Size'|i18n( 'design/admin/setup/logs' )}</th>
</tr>
</thead>
<tbody>
{foreach $log_plan.steps as $log_i => $log_s}
<tr>
    <td class="a4-num">{inc( $log_i )}</td>
    <td>{$log_s.key|wash}</td>
    <td>{switch match=$log_s.op}
        {case match='delete'}<span class="a4-tag is-bad">{'delete'|i18n( 'design/admin/setup/logs' )}</span>{/case}
        {case match='rename'}<span class="a4-tag">{'rename'|i18n( 'design/admin/setup/logs' )}</span>{/case}
        {case match='copy'}<span class="a4-tag">{'copy'|i18n( 'design/admin/setup/logs' )}</span>{/case}
        {case match='gzip'}<span class="a4-tag is-info">{'compress'|i18n( 'design/admin/setup/logs' )}</span>{/case}
        {case match='truncate'}<span class="a4-tag is-bad">{'empty'|i18n( 'design/admin/setup/logs' )}</span>{/case}
        {case}{$log_s.op|wash}{/case}
    {/switch}</td>
    <td><code>{$log_s.from|wash}</code></td>
    <td>{if $log_s.to}<code>{$log_s.to|wash}</code>{else}<span class="a4-meta">–</span>{/if}</td>
    <td class="a4-num">{$log_s.bytes|si( byte )}</td>
</tr>
{/foreach}
</tbody>
</table>
</div>

<form method="post" action={concat( 'setup/logsmanage', cond( $log_key|ne( '' ), concat( '/', $log_key ), '' ) )|ezurl} class="a4-row a4-setup-log-confirm">
    <input type="hidden" name="{$log_token_field|wash}" value="{$log_token|wash}" />
    <input type="hidden" name="Op" value="{$log_op|wash}" />
    <input type="hidden" name="Key" value="{$log_key|wash}" />
    <input type="hidden" name="Days" value="{$log_days|wash}" />
    <button type="submit" class="a4-btn {if or( $log_op|eq( 'truncate' ), $log_op|eq( 'prune' ), $log_op|eq( 'prune_all' ) )}a4-btn-danger{else}a4-btn-primary{/if}" name="ConfirmButton" value="1">{'Carry out %count steps'|i18n( 'design/admin/setup/logs',, hash( '%count', $log_plan.steps|count ) )|wash}</button>
    <button type="submit" class="a4-btn" name="CancelButton" value="1">{'Cancel'|i18n( 'design/admin/setup/logs' )}</button>
</form>
{else}
<div class="a4-notice is-info" role="status">{'Nothing to do: no file needs this action now.'|i18n( 'design/admin/setup/logs' )}</div>
<p><a class="a4-btn" href={'setup/logsmanage'|ezurl}>{'Back to the logs'|i18n( 'design/admin/setup/logs' )}</a></p>
{/if}
{if $log_plan.unknown|count}
<p class="a4-meta">{'Not managed here: %keys'|i18n( 'design/admin/setup/logs',, hash( '%keys', $log_plan.unknown|implode( ', ' ) ) )|wash}</p>
{/if}

</div></div></div>
</div>
