{* The datatable/table view: one persistent class and its table.
   Variables: entry (class, table, file, origin, fields, keys, increment_key, sort, function_attributes, connection,
   relations, error), description (exists, columns, rows, drift, engine, connection, error), relations (+ target),
   sample (rows of values in field order), sample_limit, hidden_columns, connection (descriptor or null). *}
<div class="a4-page a4-datatable" id="exp-datatable-table">

<div class="a4-pagehead">
    <div class="a4-pagehead-main">
        <p class="a4-pagehead-pre"><a href={'datatable/dashboard'|ezurl}>{'Data tables'|i18n( 'design/admin/datatable' )}</a></p>
        <h1 class="a4-pagehead-title context-title">{$entry.class|wash}</h1>
        <p class="a4-pagehead-sub"><code class="a4-code">{$entry.table|wash}</code> &middot; {if eq( $entry.origin, 'kernel' )}{'Kernel'|i18n( 'design/admin/datatable' )}{else}{'Extension %name'|i18n( 'design/admin/datatable',, hash( '%name', $entry.origin ) )|wash}{/if} &middot; <code class="a4-code">{$entry.file|wash}</code></p>
    </div>
    <div class="a4-pagehead-actions a4-actions">
        {if $description.exists}<a class="a4-btn" href={concat( 'datatable/rows/', $entry.class )|ezurl}>{'Rows'|i18n( 'design/admin/datatable' )}</a>{/if}
        {if and( $can_manage, $entry.error|not )}<a class="a4-btn" href={concat( 'datatable/schema/(class)/', $entry.class )|ezurl}>{if $description.exists}{'Upgrade the table'|i18n( 'design/admin/datatable' )}{else}{'Create the table'|i18n( 'design/admin/datatable' )}{/if}</a>{/if}
    </div>
</div>

{if $entry.error}
<div class="a4-notice is-bad" role="alert"><p class="a4-notice-title">{'The definition of this class cannot be read'|i18n( 'design/admin/datatable' )}</p><p>{$entry.error|wash}</p></div>
{/if}
{if $description.error}
<div class="a4-notice is-bad" role="alert"><p>{$description.error|wash}</p></div>
{/if}

<div class="a4-figures">
    <div class="a4-figure"><span class="a4-meta">{'Connection'|i18n( 'design/admin/datatable' )}</span> <strong>{if $connection}{$connection.name|wash}{else}{$description.connection|wash}{/if}</strong> <span class="a4-meta">{$description.engine|wash}</span></div>
    <div class="a4-figure{if eq( $description.exists, false() )} is-bad{/if}"><span class="a4-meta">{'Table'|i18n( 'design/admin/datatable' )}</span> <strong>{if $description.exists}{'exists'|i18n( 'design/admin/datatable' )}{elseif eq( $description.exists, false() )}{'missing'|i18n( 'design/admin/datatable' )}{else}{'unknown'|i18n( 'design/admin/datatable' )}{/if}</strong></div>
    <div class="a4-figure"><span class="a4-meta">{'Rows'|i18n( 'design/admin/datatable' )}</span> <strong>{if is_null( $description.rows )}-{else}{$description.rows|l10n( 'number' )}{/if}</strong></div>
    <div class="a4-figure{if $description.drift|count} is-warn{else} is-ok{/if}"><span class="a4-meta">{'Differences'|i18n( 'design/admin/datatable' )}</span> <strong>{$description.drift|count}</strong></div>
</div>

{if $description.drift|count}
<section class="a4-card is-warn a4-section" aria-labelledby="datatable-drift">
    <div class="a4-card-head"><h2 class="a4-h3 a4-card-title" id="datatable-drift">{'Where the table differs from the definition'|i18n( 'design/admin/datatable' )}</h2></div>
    <div class="a4-card-body">
    <ul>
    {foreach $description.drift as $d}
        <li>{switch match=$d.kind}
            {case match='missing_table'}{'The table %table is not in the database.'|i18n( 'design/admin/datatable',, hash( '%table', $d.detail ) )|wash}{/case}
            {case match='missing_column'}{'The field %column (%type) has no column.'|i18n( 'design/admin/datatable',, hash( '%column', $d.column, '%type', $d.detail ) )|wash}{/case}
            {case match='extra_column'}{'The column %column (%type) is not in the definition.'|i18n( 'design/admin/datatable',, hash( '%column', $d.column, '%type', $d.detail ) )|wash}{/case}
            {case}{'The field %column: %detail.'|i18n( 'design/admin/datatable',, hash( '%column', $d.column, '%detail', $d.detail ) )|wash}{/case}
        {/switch}</li>
    {/foreach}
    </ul>
    </div>
</section>
{/if}

<section class="a4-card is-flush a4-section" aria-labelledby="datatable-fields">
    <div class="a4-card-head"><h2 class="a4-h3 a4-card-title" id="datatable-fields">{'Fields'|i18n( 'design/admin/datatable' )}</h2>
        <p class="a4-meta">{'Key: %keys'|i18n( 'design/admin/datatable',, hash( '%keys', $entry.keys|implode( ', ' ) ) )|wash}{if $entry.increment_key} &middot; {'counted up: %key'|i18n( 'design/admin/datatable',, hash( '%key', $entry.increment_key ) )|wash}{/if}</p></div>
    <div class="a4-table-wrap" role="region" aria-labelledby="datatable-fields" tabindex="0">
    <table class="a4-table is-compact">
        <thead><tr>
            <th scope="col">{'Field'|i18n( 'design/admin/datatable' )}</th>
            <th scope="col">{'Type'|i18n( 'design/admin/datatable' )}</th>
            <th scope="col">{'Default'|i18n( 'design/admin/datatable' )}</th>
            <th scope="col">{'Required'|i18n( 'design/admin/datatable' )}</th>
            <th scope="col">{'Column in the database'|i18n( 'design/admin/datatable' )}</th>
        </tr></thead>
        <tbody>
        {foreach $entry.fields as $name => $field}
        <tr>
            <td><code class="a4-code">{$name|wash}</code>{if $entry.keys|contains( $name )} <span class="a4-badge is-accent">{'key'|i18n( 'design/admin/datatable' )}</span>{/if}</td>
            <td>{$field.datatype|wash}</td>
            <td>{if is_null( $field.default )}<span class="a4-muted">-</span>{else}<code class="a4-code">{$field.default|wash}</code>{/if}</td>
            <td>{if $field.required}{'yes'|i18n( 'design/admin/datatable' )}{else}<span class="a4-muted">{'no'|i18n( 'design/admin/datatable' )}</span>{/if}</td>
            <td>{if is_set( $description.columns[$name] )}{$description.columns[$name].type|wash}{elseif and( $field.short_name, is_set( $description.columns[$field.short_name] ) )}{$description.columns[$field.short_name].type|wash} ({$field.short_name|wash}){else}<span class="a4-muted">-</span>{/if}</td>
        </tr>
        {/foreach}
        </tbody>
    </table>
    </div>
    {if $entry.function_attributes|count}
    <div class="a4-card-foot"><p class="a4-meta">{'Function attributes: %list'|i18n( 'design/admin/datatable',, hash( '%list', $entry.function_attributes|implode( ', ' ) ) )|wash}</p></div>
    {/if}
</section>

<section class="a4-card is-flush a4-section" aria-labelledby="datatable-relations">
    <div class="a4-card-head"><h2 class="a4-h3 a4-card-title" id="datatable-relations">{'Relations'|i18n( 'design/admin/datatable' )}</h2></div>
    {if $relations|count}
    <div class="a4-table-wrap" role="region" aria-labelledby="datatable-relations" tabindex="0">
    <table class="a4-table is-compact">
        <thead><tr>
            <th scope="col">{'Column'|i18n( 'design/admin/datatable' )}</th>
            <th scope="col">{'Points at'|i18n( 'design/admin/datatable' )}</th>
            <th scope="col">{'Field of the target'|i18n( 'design/admin/datatable' )}</th>
            <th scope="col">{'Known from'|i18n( 'design/admin/datatable' )}</th>
        </tr></thead>
        <tbody>
        {foreach $relations as $r}
        <tr>
            <td><code class="a4-code">{$r.column|wash}</code></td>
            <td>{$r.target|wash}{if and( eq( $r.kind, 'table' ), $r.class )} (<a href={concat( 'datatable/table/', $r.class )|ezurl}>{$r.class|wash}</a>){/if}</td>
            <td><code class="a4-code">{$r.field|wash}</code></td>
            <td>{switch match=$r.source}
                {case match='declared'}<span class="a4-badge is-ok">{'the definition'|i18n( 'design/admin/datatable' )}</span>{/case}
                {case match='foreign'}<span class="a4-badge is-ok">{'a foreign key of a field'|i18n( 'design/admin/datatable' )}</span>{/case}
                {case match='settings'}<span class="a4-badge is-info">{'datatable.ini'|i18n( 'design/admin/datatable' )}</span>{/case}
                {case}<span class="a4-badge">{'inferred from the name'|i18n( 'design/admin/datatable' )}</span>{/case}
            {/switch}</td>
        </tr>
        {/foreach}
        </tbody>
    </table>
    </div>
    {else}
    <div class="a4-card-body"><p class="a4-empty">{'No relation is known.'|i18n( 'design/admin/datatable' )}</p></div>
    {/if}
</section>

<section class="a4-card is-flush a4-section" aria-labelledby="datatable-sample">
    <div class="a4-card-head"><h2 class="a4-h3 a4-card-title" id="datatable-sample">{'The first %count rows'|i18n( 'design/admin/datatable',, hash( '%count', $sample_limit ) )}</h2>
        {if $hidden_columns|count}<p class="a4-meta">{'Not shown: %list'|i18n( 'design/admin/datatable',, hash( '%list', $hidden_columns|implode( ', ' ) ) )|wash}</p>{/if}</div>
    {if $sample|count}
    <div class="a4-table-wrap" role="region" aria-labelledby="datatable-sample" tabindex="0">
    <table class="a4-table is-compact">
        <thead><tr>{foreach $entry.fields as $name => $field}<th scope="col">{$name|wash}</th>{/foreach}</tr></thead>
        <tbody>
        {foreach $sample as $line}<tr>{foreach $line as $value}<td>{$value|wash}</td>{/foreach}</tr>{/foreach}
        </tbody>
    </table>
    </div>
    {else}
    <div class="a4-card-body"><p class="a4-empty">{'No rows.'|i18n( 'design/admin/datatable' )}</p></div>
    {/if}
</section>

</div>
