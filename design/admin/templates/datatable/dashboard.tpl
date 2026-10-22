{* The datatable/dashboard view: every persistent class with its table, grouped by the kernel and each extension.
   Variables: groups (origin, rows: class, table, connection, fields, keys, relations, content_relations, error), total,
   all_count, origins, origin, query, messages, can_manage, can_connect, connections_installed, secrets_installed,
   connection_count. *}
<div class="a4-page a4-datatable" id="exp-datatable-dashboard">

<div class="a4-pagehead">
    <div class="a4-pagehead-main">
        <h1 class="a4-pagehead-title context-title">{'Data tables'|i18n( 'design/admin/datatable' )}</h1>
        <p class="a4-pagehead-sub">{'The tables of the persistent classes of this installation, as their definitions describe them, and where each one is stored.'|i18n( 'design/admin/datatable' )}</p>
    </div>
    <div class="a4-pagehead-actions a4-actions">
        {if $can_connect}<a class="a4-btn" href={'datatable/connections'|ezurl}><span class="a4-icon a4-bi-plug" aria-hidden="true"></span> {'Connections (%count)'|i18n( 'design/admin/datatable',, hash( '%count', $connection_count ) )}</a>{/if}
        <a class="a4-btn" href={'secrets/list'|ezurl}><span class="a4-icon a4-bi-key" aria-hidden="true"></span> {'Secrets'|i18n( 'design/admin/datatable' )}</a>
    </div>
</div>

{foreach $messages as $m}
<div class="a4-notice is-{if eq( $m.kind, 'ok' )}ok{elseif eq( $m.kind, 'warn' )}warn{else}bad{/if}" role="{if eq( $m.kind, 'bad' )}alert{else}status{/if}"><p>{$m.text|wash}</p></div>
{/foreach}

{if or( $connections_installed|not, $secrets_installed|not )}
<div class="a4-notice is-warn" role="status">
    <p class="a4-notice-title">{'The tables of the data tables are not installed yet'|i18n( 'design/admin/datatable' )}</p>
    <p>{'Named connections need the table exp_data_connection, the secret store the table exp_secret.'|i18n( 'design/admin/datatable' )}
    {if $can_manage}{'They are created on this database with the button below; nothing else changes.'|i18n( 'design/admin/datatable' )}{else}{'Someone with the policy datatable/manage can create them here.'|i18n( 'design/admin/datatable' )}{/if}</p>
    {if $can_manage}
    <form method="post" action={'datatable/dashboard'|ezurl}>
        <button type="submit" class="a4-btn a4-btn-primary" name="CreateTablesButton" value="1">{'Create the tables'|i18n( 'design/admin/datatable' )}</button>
    </form>
    {/if}
</div>
{/if}

<form method="get" action={'datatable/dashboard'|ezurl} class="a4-filterbar" role="search">
    <div class="a4-field">
        <label class="a4-label" for="datatable-q">{'Class, table or field'|i18n( 'design/admin/datatable' )}</label>
        <input class="a4-input" type="search" id="datatable-q" name="q" value="{$query|wash}" />
    </div>
    <div class="a4-field">
        <span class="a4-label">&nbsp;</span>
        <button type="submit" class="a4-btn">{'Filter'|i18n( 'design/admin/datatable' )}</button>
    </div>
</form>

<nav class="a4-tabs" aria-label="{'Where the classes come from'|i18n( 'design/admin/datatable' )|wash}">
    {if $origin|eq( '' )}<span class="current" aria-current="page">{'All (%count)'|i18n( 'design/admin/datatable',, hash( '%count', $all_count ) )}</span>{else}<a href={'datatable/dashboard'|ezurl}>{'All (%count)'|i18n( 'design/admin/datatable',, hash( '%count', $all_count ) )}</a>{/if}
    {foreach $origins as $o}
    {if eq( $o, $origin )}<span class="current" aria-current="page">{$o|wash}</span>{else}<a href={concat( 'datatable/dashboard/(origin)/', $o )|ezurl}>{$o|wash}</a>{/if}
    {/foreach}
</nav>

{if $groups|count}
{foreach $groups as $group}
<section class="a4-card is-flush a4-section" aria-labelledby="datatable-group-{$group.origin|wash}">
    <div class="a4-card-head">
        <h2 class="a4-h3 a4-card-title" id="datatable-group-{$group.origin|wash}">{if eq( $group.origin, 'kernel' )}{'Kernel'|i18n( 'design/admin/datatable' )}{else}{'Extension %name'|i18n( 'design/admin/datatable',, hash( '%name', $group.origin ) )|wash}{/if}</h2>
        <p class="a4-meta">{'%count classes'|i18n( 'design/admin/datatable',, hash( '%count', $group.rows|count ) )}</p>
    </div>
    <div class="a4-table-wrap" role="region" aria-labelledby="datatable-group-{$group.origin|wash}" tabindex="0">
    <table class="a4-table is-compact">
        <thead><tr>
            <th scope="col">{'Class'|i18n( 'design/admin/datatable' )}</th>
            <th scope="col">{'Table'|i18n( 'design/admin/datatable' )}</th>
            <th scope="col">{'Connection'|i18n( 'design/admin/datatable' )}</th>
            <th scope="col" class="a4-num">{'Fields'|i18n( 'design/admin/datatable' )}</th>
            <th scope="col">{'Key'|i18n( 'design/admin/datatable' )}</th>
            <th scope="col">{'Relations'|i18n( 'design/admin/datatable' )}</th>
        </tr></thead>
        <tbody>
        {foreach $group.rows as $row}
        <tr>
            <td><a href={concat( 'datatable/table/', $row.class )|ezurl}>{$row.class|wash}</a>{if $row.error}<br /><span class="a4-badge is-bad">{'definition() fails'|i18n( 'design/admin/datatable' )}</span>{/if}</td>
            <td><code class="a4-code">{$row.table|wash}</code></td>
            <td>{if eq( $row.connection, 'site' )}<span class="a4-muted">{'site'|i18n( 'design/admin/datatable' )}</span>{else}<span class="a4-badge is-info">{$row.connection|wash}</span>{/if}</td>
            <td class="a4-num">{$row.fields}</td>
            <td><code class="a4-code">{$row.keys|wash}</code></td>
            <td>{if $row.relations}{$row.relations}{if $row.content_relations} <span class="a4-badge is-accent">{'%count to content'|i18n( 'design/admin/datatable',, hash( '%count', $row.content_relations ) )}</span>{/if}{else}<span class="a4-muted">-</span>{/if}</td>
        </tr>
        {/foreach}
        </tbody>
    </table>
    </div>
</section>
{/foreach}
<p class="a4-meta">{'%shown of %all classes shown.'|i18n( 'design/admin/datatable',, hash( '%shown', $total, '%all', $all_count ) )}</p>
{else}
<p class="a4-empty">{'No persistent class matches.'|i18n( 'design/admin/datatable' )}</p>
{/if}

{if $can_manage}
<form method="post" action={'datatable/dashboard'|ezurl} class="a4-toolbar is-bottom">
    <p class="a4-meta">{'The catalogue is made from the autoload arrays and kept until they change.'|i18n( 'design/admin/datatable' )}</p>
    <div class="a4-actions"><button type="submit" class="a4-btn" name="RebuildCatalogueButton" value="1">{'Make the catalogue again'|i18n( 'design/admin/datatable' )}</button></div>
</form>
{/if}

</div>
