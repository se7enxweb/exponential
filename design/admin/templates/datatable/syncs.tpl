{* The datatable/syncs view: the synchronisations between content and data tables.
   Variables: syncs (expDataSyncDefinition), installed, messages, open_conflicts. *}
<div class="a4-page a4-datatable" id="exp-datatable-syncs">

<div class="a4-pagehead">
    <div class="a4-pagehead-main">
        <p class="a4-pagehead-pre"><a href={'datatable/dashboard'|ezurl}>{'Data tables'|i18n( 'design/admin/datatable' )}</a></p>
        <h1 class="a4-pagehead-title context-title">{'Synchronisations'|i18n( 'design/admin/datatable' )}</h1>
        <p class="a4-pagehead-sub">{'Content copied to a data table, a table copied to content, or both kept in step: once, on a schedule, or live on every publish, move and removal.'|i18n( 'design/admin/datatable' )}</p>
    </div>
    {if $installed}
    <div class="a4-pagehead-actions a4-actions">
        {if $open_conflicts}<a class="a4-btn" href={'datatable/conflicts'|ezurl}>{'Conflicts (%count)'|i18n( 'design/admin/datatable',, hash( '%count', $open_conflicts ) )|wash}</a>{/if}
        <a class="a4-btn a4-btn-primary" href={'datatable/sync/new'|ezurl}>{'New synchronisation'|i18n( 'design/admin/datatable' )}</a>
    </div>
    {/if}
</div>

{foreach $messages as $m}
<div class="a4-notice is-{if eq( $m.kind, 'ok' )}ok{elseif eq( $m.kind, 'warn' )}warn{else}bad{/if}" role="{if eq( $m.kind, 'bad' )}alert{else}status{/if}"><p>{$m.text|wash}</p></div>
{/foreach}

{if $installed|not}
<div class="a4-notice is-warn" role="status">
    <p class="a4-notice-title">{'The tables of the synchronisation are not installed yet'|i18n( 'design/admin/datatable' )}</p>
    <p>{'exp_data_sync, exp_data_sync_link and exp_data_sync_conflict are created on this database with the button; nothing else changes.'|i18n( 'design/admin/datatable' )}</p>
    <form method="post" action={'datatable/syncs'|ezurl}><button type="submit" class="a4-btn a4-btn-primary" name="CreateTablesButton" value="1">{'Create the tables'|i18n( 'design/admin/datatable' )}</button></form>
</div>
{elseif $syncs|count}
<form method="post" action={'datatable/syncs'|ezurl}>
<div class="a4-card is-flush a4-section">
<div class="a4-table-wrap" role="region" aria-label="{'Synchronisations'|i18n( 'design/admin/datatable' )|wash}" tabindex="0">
<table class="a4-table">
    <thead><tr>
        <th scope="col">{'Name'|i18n( 'design/admin/datatable' )}</th>
        <th scope="col">{'Content and table'|i18n( 'design/admin/datatable' )}</th>
        <th scope="col">{'Direction'|i18n( 'design/admin/datatable' )}</th>
        <th scope="col">{'Runs'|i18n( 'design/admin/datatable' )}</th>
        <th scope="col">{'Last run'|i18n( 'design/admin/datatable' )}</th>
        <th scope="col"><span class="a4-sr-only">{'Actions'|i18n( 'design/admin/datatable' )}</span></th>
    </tr></thead>
    <tbody>
    {foreach $syncs as $s}
    <tr>
        <td><a href={concat( 'datatable/sync/', $s.id )|ezurl}>{$s.name|wash}</a>{if eq( $s.state, 'disabled' )} <span class="a4-badge">{'off'|i18n( 'design/admin/datatable' )}</span>{/if}{if $s.open_conflicts} <a class="a4-badge is-warn" href={concat( 'datatable/conflicts/(sync)/', $s.id )|ezurl}>{'%count conflicts'|i18n( 'design/admin/datatable',, hash( '%count', $s.open_conflicts ) )|wash}</a>{/if}</td>
        <td><code class="a4-code">{$s.content_class|wash}</code> &middot; <a href={concat( 'datatable/table/', $s.class_name )|ezurl}>{$s.class_name|wash}</a></td>
        <td>{switch match=$s.direction}{case match='content_to_table'}{'content to table'|i18n( 'design/admin/datatable' )}{/case}{case match='table_to_content'}{'table to content'|i18n( 'design/admin/datatable' )}{/case}{case}{'both ways'|i18n( 'design/admin/datatable' )} ({$s.conflict_rule|wash}){/case}{/switch}</td>
        <td>{switch match=$s.run_mode}{case match='once'}{'when started'|i18n( 'design/admin/datatable' )}{/case}{case match='live'}{'live'|i18n( 'design/admin/datatable' )}{/case}{case}{'every %minutes minutes'|i18n( 'design/admin/datatable',, hash( '%minutes', div( $s.interval_seconds, 60 )|floor ) )|wash}{/case}{/switch}</td>
        <td>{if $s.last_run}{$s.last_run|l10n( 'shortdatetime' )}{else}<span class="a4-muted">-</span>{/if}</td>
        <td class="a4-nowrap"><button type="submit" class="a4-btn a4-btn-small" name="RunButton[{$s.id}]" value="1">{'Run now'|i18n( 'design/admin/datatable' )}</button></td>
    </tr>
    {/foreach}
    </tbody>
</table>
</div>
</div>
</form>
{else}
<p class="a4-empty">{'No synchronisation yet.'|i18n( 'design/admin/datatable' )}</p>
{/if}

</div>
