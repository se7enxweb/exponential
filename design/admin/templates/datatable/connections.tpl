{* The datatable/connections view: the named database connections and the engines of this server.
   Variables: connections (descriptors + secret_set, test), engines (key, label, available, via, handler, why,
   untested), installed, sqlite_directories. *}
<div class="a4-page a4-datatable" id="exp-datatable-connections">

<div class="a4-pagehead">
    <div class="a4-pagehead-main">
        <p class="a4-pagehead-pre"><a href={'datatable/dashboard'|ezurl}>{'Data tables'|i18n( 'design/admin/datatable' )}</a></p>
        <h1 class="a4-pagehead-title context-title">{'Connections'|i18n( 'design/admin/datatable' )}</h1>
        <p class="a4-pagehead-sub">{'Databases the data tables and the import and export tools can use besides this installation\'s own: a SQLite file, or a MySQL, MariaDB, PostgreSQL, Oracle, SQL Server or MongoDB server, here or remote. A password is the name of a secret, never its value.'|i18n( 'design/admin/datatable' )}</p>
    </div>
    {if $installed}
    <div class="a4-pagehead-actions a4-actions"><a class="a4-btn a4-btn-primary" href={'datatable/connection/new'|ezurl}><span class="a4-icon a4-bi-plus-lg" aria-hidden="true"></span> {'New connection'|i18n( 'design/admin/datatable' )}</a></div>
    {/if}
</div>

{if $installed|not}
<div class="a4-notice is-warn" role="status"><p>{'The table exp_data_connection is missing: create it on the <a href="%url">Data tables</a> page. Until then only the connections declared in datatable.ini are listed.'|i18n( 'design/admin/datatable',, hash( '%url', 'datatable/dashboard'|ezurl( 'no' ) ) )}</p></div>
{/if}

<form method="post" action={'datatable/connections'|ezurl}>
<div class="a4-card is-flush a4-section">
<div class="a4-table-wrap" role="region" aria-label="{'Connections'|i18n( 'design/admin/datatable' )|wash}" tabindex="0">
<table class="a4-table">
    <thead><tr>
        <th scope="col">{'Connection'|i18n( 'design/admin/datatable' )}</th>
        <th scope="col">{'Engine'|i18n( 'design/admin/datatable' )}</th>
        <th scope="col">{'Server and database'|i18n( 'design/admin/datatable' )}</th>
        <th scope="col">{'Password'|i18n( 'design/admin/datatable' )}</th>
        <th scope="col">{'Made in'|i18n( 'design/admin/datatable' )}</th>
        <th scope="col"><span class="a4-sr-only">{'Actions'|i18n( 'design/admin/datatable' )}</span></th>
    </tr></thead>
    <tbody>
    {foreach $connections as $c}
    <tr>
        <td><a href={concat( 'datatable/connection/', $c.identifier )|ezurl}>{$c.name|wash}</a><br /><code class="a4-code">{$c.identifier|wash}</code>{if $c.read_only} <span class="a4-badge">{'read only'|i18n( 'design/admin/datatable' )}</span>{/if}</td>
        <td>{$c.engine|wash}</td>
        <td>{if $c.server}{$c.server|wash}{if $c.port}:{$c.port}{/if} / {/if}<code class="a4-code">{$c.database|wash}</code></td>
        <td>{if $c.secret}<code class="a4-code">{$c.secret|wash}</code> {if $c.secret_set}<span class="a4-badge is-ok">{'set'|i18n( 'design/admin/datatable' )}</span>{else}<span class="a4-badge is-bad">{'not set'|i18n( 'design/admin/datatable' )}</span>{/if}{else}<span class="a4-muted">-</span>{/if}</td>
        <td>{switch match=$c.origin}{case match='site'}{'site.ini'|i18n( 'design/admin/datatable' )}{/case}{case match='settings'}{'datatable.ini'|i18n( 'design/admin/datatable' )}{/case}{case}{'the administration'|i18n( 'design/admin/datatable' )}{/case}{/switch}</td>
        <td class="a4-nowrap"><button type="submit" class="a4-btn a4-btn-small" name="TestButton[{$c.identifier|wash}]" value="1">{'Test'|i18n( 'design/admin/datatable' )}</button></td>
    </tr>
    {if $c.test}
    <tr>
        <td colspan="6">
        {if $c.test.ok}
        <p class="a4-notice is-ok" role="status">{'Connected through %via in %ms ms: %version, %tables tables.'|i18n( 'design/admin/datatable',, hash( '%via', $c.test.via, '%ms', $c.test.ms, '%version', $c.test.version, '%tables', cond( is_null( $c.test.tables ), '?', $c.test.tables ) ) )|wash}</p>
        {else}
        <p class="a4-notice is-bad" role="alert">{'The connection failed after %ms ms: %error'|i18n( 'design/admin/datatable',, hash( '%ms', $c.test.ms, '%error', $c.test.error ) )|wash}</p>
        {/if}
        </td>
    </tr>
    {/if}
    {/foreach}
    </tbody>
</table>
</div>
</div>
</form>

<section class="a4-card is-flush a4-section" aria-labelledby="datatable-engines">
    <div class="a4-card-head"><h2 class="a4-h3 a4-card-title" id="datatable-engines">{'Engines on this server'|i18n( 'design/admin/datatable' )}</h2>
        <p class="a4-meta">{'SQLite files may be in: %dirs'|i18n( 'design/admin/datatable',, hash( '%dirs', $sqlite_directories|implode( ', ' ) ) )|wash}</p></div>
    <div class="a4-table-wrap" role="region" aria-labelledby="datatable-engines" tabindex="0">
    <table class="a4-table is-compact">
        <thead><tr>
            <th scope="col">{'Engine'|i18n( 'design/admin/datatable' )}</th>
            <th scope="col">{'Available'|i18n( 'design/admin/datatable' )}</th>
            <th scope="col">{'Driver'|i18n( 'design/admin/datatable' )}</th>
        </tr></thead>
        <tbody>
        {foreach $engines as $e}
        <tr>
            <td>{$e.label|wash}{if $e.untested} <span class="a4-badge is-warn">{'untested here'|i18n( 'design/admin/datatable' )}</span>{/if}</td>
            <td>{if $e.available}<span class="a4-badge is-ok">{'yes'|i18n( 'design/admin/datatable' )}</span>{else}<span class="a4-badge is-bad">{'no'|i18n( 'design/admin/datatable' )}</span> <span class="a4-meta">{$e.why|wash}</span>{/if}</td>
            <td>{if $e.handler}<code class="a4-code">{$e.handler|wash}</code>{if eq( $e.via, 'pdo' )} (pdo_{$e.pdo_driver|wash}){/if}{else}<span class="a4-muted">-</span>{/if}</td>
        </tr>
        {/foreach}
        </tbody>
    </table>
    </div>
</section>

</div>
