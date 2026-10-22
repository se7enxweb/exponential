{* The datatable/schema view: create or upgrade data tables on a connection.
   Variables: class, extension, source, connection_id, connection, connections, classes (class, origin), extensions,
   problems, tables (table, action, statements, held, notes, sql), engine, outcomes (table, action, ok, done, errors),
   applied, applied_ok, options (drop, alter), install_sql (engine, sql). *}
<div class="a4-page a4-datatable" id="exp-datatable-schema">

<div class="a4-pagehead">
    <div class="a4-pagehead-main">
        <p class="a4-pagehead-pre"><a href={'datatable/dashboard'|ezurl}>{'Data tables'|i18n( 'design/admin/datatable' )}</a></p>
        <h1 class="a4-pagehead-title context-title">{'Schema'|i18n( 'design/admin/datatable' )}</h1>
        <p class="a4-pagehead-sub">{'Create the tables of a class or an extension on a connection, or bring them up to date. Nothing runs before "Apply"; columns and indexes are dropped or changed only when you tick it.'|i18n( 'design/admin/datatable' )}</p>
    </div>
</div>

{if $problems|count}
<div class="a4-notice is-bad" role="alert"><ul>{foreach $problems as $p}<li>{$p|wash}</li>{/foreach}</ul></div>
{/if}

{if $applied}
<div class="a4-notice is-{if $applied_ok}ok{else}bad{/if}" role="{if $applied_ok}status{else}alert{/if}">
    <p class="a4-notice-title">{if $applied_ok}{'Done'|i18n( 'design/admin/datatable' )}{else}{'Not everything could be done'|i18n( 'design/admin/datatable' )}{/if}</p>
    <ul>{foreach $outcomes as $o}<li><code class="a4-code">{$o.table|wash}</code>: {$o.action|wash}, {'%done statements'|i18n( 'design/admin/datatable',, hash( '%done', $o.done ) )|wash}{if $o.errors|count} &middot; {$o.errors|implode( '; ' )|wash}{/if}</li>{/foreach}</ul>
</div>
{/if}

<form method="post" action={'datatable/schema'|ezurl} class="a4-form">
<div class="a4-card a4-section">
    <div class="a4-card-body a4-grid">
        <div class="a4-field">
            <label class="a4-label" for="schema-class">{'From the definition of a class'|i18n( 'design/admin/datatable' )}</label>
            <select class="a4-input" id="schema-class" name="ClassName">
                <option value="">-</option>
                {foreach $classes as $c}<option value="{$c.class|wash}"{if eq( $c.class, $class )} selected="selected"{/if}>{$c.class|wash} ({$c.origin|wash})</option>{/foreach}
                {if and( $class, $source|eq( $class ), $classes|count|eq( 0 ) )}<option value="{$class|wash}" selected="selected">{$class|wash}</option>{/if}
            </select>
        </div>
        <div class="a4-field">
            <label class="a4-label" for="schema-extension">{'Or from the schema file of an extension'|i18n( 'design/admin/datatable' )}</label>
            <select class="a4-input" id="schema-extension" name="Extension">
                <option value="">-</option>
                {foreach $extensions as $e}<option value="{$e|wash}"{if eq( $e, $extension )} selected="selected"{/if}>{$e|wash}</option>{/foreach}
            </select>
        </div>
        <div class="a4-field">
            <label class="a4-label" for="schema-connection">{'On the connection'|i18n( 'design/admin/datatable' )}</label>
            <select class="a4-input" id="schema-connection" name="Connection">
                {foreach $connections as $c}<option value="{$c.identifier|wash}"{if eq( $c.identifier, $connection_id )} selected="selected"{/if}{if $c.read_only} disabled="disabled"{/if}>{$c.name|wash} ({$c.engine|wash})</option>{/foreach}
            </select>
        </div>
        <div class="a4-field a4-field-wide">
            <label class="a4-check"><input type="checkbox" name="AllowDrop" value="1"{if $options.drop} checked="checked"{/if} /> {'Drop columns and indexes the schema does not have'|i18n( 'design/admin/datatable' )}</label>
            <label class="a4-check"><input type="checkbox" name="AllowAlter" value="1"{if $options.alter} checked="checked"{/if} /> {'Change columns and indexes that differ'|i18n( 'design/admin/datatable' )}</label>
        </div>
    </div>
    <div class="a4-card-foot a4-actions">
        <button type="submit" class="a4-btn" name="PreviewButton" value="1">{'Show what would be done'|i18n( 'design/admin/datatable' )}</button>
        {if $tables|count}<button type="submit" class="a4-btn a4-btn-primary" name="ApplyButton" value="1">{'Apply'|i18n( 'design/admin/datatable' )}</button>{/if}
    </div>
</div>
</form>

{if $tables|count}
<section class="a4-card is-flush a4-section" aria-labelledby="schema-plan">
    <div class="a4-card-head"><h2 class="a4-h3 a4-card-title" id="schema-plan">{'What would be done on %connection (%engine)'|i18n( 'design/admin/datatable',, hash( '%connection', $connection_id, '%engine', $engine ) )|wash}</h2></div>
    <div class="a4-card-body a4-stack">
    {foreach $tables as $t}
        <div>
            <h3 class="a4-h3"><code class="a4-code">{$t.table|wash}</code> {switch match=$t.action}{case match='create'}<span class="a4-badge is-info">{'create'|i18n( 'design/admin/datatable' )}</span>{/case}{case match='upgrade'}<span class="a4-badge is-warn">{'upgrade'|i18n( 'design/admin/datatable' )}</span>{/case}{case match='unknown'}<span class="a4-badge is-bad">{'unknown'|i18n( 'design/admin/datatable' )}</span>{/case}{case}<span class="a4-badge is-ok">{'up to date'|i18n( 'design/admin/datatable' )}</span>{/case}{/switch}</h3>
            {if $t.sql}<pre class="a4-pre">{$t.sql|wash}</pre>{/if}
            {if $t.held|count}<p class="a4-warn-text">{'Not done unless ticked above: %list'|i18n( 'design/admin/datatable',, hash( '%list', $t.held|implode( '; ' ) ) )|wash}</p>{/if}
            {foreach $t.notes as $n}<p class="a4-meta">{$n|wash}</p>{/foreach}
        </div>
    {/foreach}
    </div>
</section>
{/if}

{if $install_sql|count}
<details class="a4-fold a4-section">
    <summary>{'The install SQL of every engine'|i18n( 'design/admin/datatable' )}</summary>
    <div class="a4-fold-body a4-stack">
    {foreach $install_sql as $s}
        <h3 class="a4-h3">{$s.engine|wash}</h3>
        <pre class="a4-pre">{$s.sql|wash}</pre>
    {/foreach}
    </div>
</details>
{/if}

</div>
