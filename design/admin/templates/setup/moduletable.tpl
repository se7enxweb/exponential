{* The module table wizard (setup/moduletable): a new data table with its class, schema, module, templates, fetch
   functions and JSON endpoint, written as an extension and installed from this page.
   Variables: settings, fields, field_errors, source_label, sources (id, name, refs: ref, label), parts, licences,
   connections, problems, feedback (ok, message), files (path, lines, contents, exists), written, can_write, ready,
   extension_exists, extension_active, table_exists, types. *}
<div class="a4-page a4-datatable" id="exp-setup-moduletable">

<div class="a4-pagehead">
    <div class="a4-pagehead-main">
        <p class="a4-pagehead-pre"><a href={'setup/rad'|ezurl}>{'Rapid Application Development'|i18n( 'design/admin/datatable' )}</a></p>
        <h1 class="a4-pagehead-title context-title">{'Module table wizard'|i18n( 'design/admin/datatable' )}</h1>
        <p class="a4-pagehead-sub">{'A new data table with everything around it: the persistent class, the schema and install SQL of every engine, a module with list, edit, remove and a JSON endpoint, its policies, the admin pages and the template fetch functions. The fields come from a list, a content class, an existing table or an import source. Nothing is written until you ask for it, and the table is installed from this page.'|i18n( 'design/admin/datatable' )}</p>
    </div>
</div>

{foreach $feedback as $f}
<div class="a4-notice is-{if $f.ok}ok{else}bad{/if}" role="{if $f.ok}status{else}alert{/if}"><p>{$f.message|wash}</p></div>
{/foreach}
{if $problems|count}
<div class="a4-error-summary a4-notice is-bad" role="alert"><ul>{foreach $problems as $p}<li>{$p|wash}</li>{/foreach}</ul></div>
{/if}
{if $field_errors|count}
<div class="a4-notice is-warn" role="status"><ul>{foreach $field_errors as $e}<li>{$e|wash}</li>{/foreach}</ul></div>
{/if}

<form method="post" action={'setup/moduletable'|ezurl} class="a4-form">
<input type="hidden" name="Submitted" value="1" />

<section class="a4-card a4-section" aria-labelledby="mt-where">
    <div class="a4-card-head"><h2 class="a4-h3 a4-card-title" id="mt-where">{'The extension and the table'|i18n( 'design/admin/datatable' )}</h2>
        {if $extension_exists}<p>{'The extension exists: the wizard adds the files it does not have and appends its settings; nothing there is overwritten.'|i18n( 'design/admin/datatable' )}</p>{/if}</div>
    <div class="a4-card-body a4-grid">
        <div class="a4-field">
            <label class="a4-label" for="mt-name">{'Extension'|i18n( 'design/admin/datatable' )} <span class="a4-required-mark" aria-hidden="true">*</span></label>
            <input class="a4-input" id="mt-name" name="name" required="required" maxlength="64" value="{$settings.name|wash}" aria-describedby="mt-name-help" />
            <p class="a4-help" id="mt-name-help">{'A new name, or an existing extension to add the table to.'|i18n( 'design/admin/datatable' )}</p>
        </div>
        <div class="a4-field">
            <label class="a4-label" for="mt-module">{'Module'|i18n( 'design/admin/datatable' )}</label>
            <input class="a4-input" id="mt-module" name="module" maxlength="64" value="{$settings.module|wash}" />
        </div>
        <div class="a4-field">
            <label class="a4-label" for="mt-title">{'Title'|i18n( 'design/admin/datatable' )}</label>
            <input class="a4-input" id="mt-title" name="title" maxlength="120" value="{$settings.title|wash}" />
        </div>
        <div class="a4-field">
            <label class="a4-label" for="mt-table">{'Table'|i18n( 'design/admin/datatable' )}</label>
            <input class="a4-input" id="mt-table" name="table" maxlength="30" value="{$settings.table|wash}" />
        </div>
        <div class="a4-field">
            <label class="a4-label" for="mt-class">{'PHP class'|i18n( 'design/admin/datatable' )}</label>
            <input class="a4-input" id="mt-class" name="class" maxlength="64" value="{$settings.class|wash}" />
        </div>
        <div class="a4-field">
            <label class="a4-label" for="mt-connection">{'Connection'|i18n( 'design/admin/datatable' )}</label>
            <select class="a4-input" id="mt-connection" name="connection">
                {foreach $connections as $c}<option value="{$c.identifier|wash}"{if or( eq( $c.identifier, $settings.connection ), and( eq( $c.identifier, 'site' ), eq( $settings.connection, '' ) ) )} selected="selected"{/if}{if $c.read_only} disabled="disabled"{/if}>{$c.name|wash} ({$c.engine|wash})</option>{/foreach}
            </select>
        </div>
    </div>
</section>

<section class="a4-card a4-section" aria-labelledby="mt-fields">
    <div class="a4-card-head"><h2 class="a4-h3 a4-card-title" id="mt-fields">{'The fields'|i18n( 'design/admin/datatable' )}</h2>
        <p>{'Each table also gets id, a key counted up by the database.'|i18n( 'design/admin/datatable' )}</p></div>
    <div class="a4-card-body a4-stack">
        <div class="a4-grid">
            <div class="a4-field">
                <label class="a4-label" for="mt-source">{'Where the fields come from'|i18n( 'design/admin/datatable' )}</label>
                <select class="a4-input" id="mt-source" name="source">
                    {foreach $sources as $s}<option value="{$s.id|wash}"{if eq( $s.id, $settings.source )} selected="selected"{/if}>{$s.name|wash}</option>{/foreach}
                </select>
            </div>
            <div class="a4-field">
                <label class="a4-label" for="mt-ref">{'Which one'|i18n( 'design/admin/datatable' )}</label>
                <input class="a4-input" id="mt-ref" name="ref" maxlength="255" list="mt-refs" value="{$settings.ref|wash}" aria-describedby="mt-ref-help" />
                <datalist id="mt-refs">{foreach $sources as $s}{foreach $s.refs as $r}<option value="{$r.ref|wash}">{$s.name|wash}: {$r.label|wash}</option>{/foreach}{/foreach}</datalist>
                <p class="a4-help" id="mt-ref-help">{'A content class identifier, connection:table, or what the source asks for. Not needed for a list.'|i18n( 'design/admin/datatable' )}</p>
            </div>
            <div class="a4-field">
                <span class="a4-label">&nbsp;</span>
                <button type="submit" class="a4-btn" name="UseFieldsButton" value="1">{'Copy its fields into the list'|i18n( 'design/admin/datatable' )}</button>
            </div>
        </div>
        <div class="a4-field a4-field-wide">
            <label class="a4-label" for="mt-fields-text">{'Fields, one per line: name type[(length)] [required] [unique]'|i18n( 'design/admin/datatable' )}</label>
            <textarea class="a4-input" id="mt-fields-text" name="fields_text" rows="8" aria-describedby="mt-fields-help">{$settings.fields_text|wash}</textarea>
            <p class="a4-help" id="mt-fields-help">{'Types: %types. object, node and user are relations to content: the content view shows the rows that point at an item.'|i18n( 'design/admin/datatable',, hash( '%types', $types|implode( ', ' ) ) )|wash}</p>
        </div>
        <fieldset class="a4-radios">
            <legend class="a4-label">{'Tie each row to content'|i18n( 'design/admin/datatable' )}</legend>
            {foreach hash( 'none', 'No', 'object', 'A content object (contentobject_id)', 'node', 'A node (node_id)', 'both', 'Both' ) as $key => $label}
            <label class="a4-check"><input type="radio" name="content" value="{$key}"{if eq( $key, $settings.content )} checked="checked"{/if} /> {$label|i18n( 'design/admin/datatable' )}</label>
            {/foreach}
        </fieldset>
        <label class="a4-check"><input type="checkbox" name="stamps" value="1"{if $settings.stamps} checked="checked"{/if} /> {'Add created and modified'|i18n( 'design/admin/datatable' )}</label>
        {if $fields|count}
        <div class="a4-table-wrap" role="region" aria-labelledby="mt-fields" tabindex="0">
        <table class="a4-table is-compact">
            <thead><tr><th scope="col">{'Field'|i18n( 'design/admin/datatable' )}</th><th scope="col">{'Type'|i18n( 'design/admin/datatable' )}</th><th scope="col">{'Required'|i18n( 'design/admin/datatable' )}</th><th scope="col">{'Unique'|i18n( 'design/admin/datatable' )}</th><th scope="col">{'Label'|i18n( 'design/admin/datatable' )}</th></tr></thead>
            <tbody>
            <tr><td><code class="a4-code">id</code></td><td>integer</td><td>{'yes'|i18n( 'design/admin/datatable' )}</td><td>{'yes'|i18n( 'design/admin/datatable' )}</td><td>ID</td></tr>
            {foreach $fields as $f}<tr><td><code class="a4-code">{$f.name|wash}</code></td><td>{$f.type|wash}{if eq( $f.type, 'string' )} ({$f.length}){/if}</td><td>{if $f.required}{'yes'|i18n( 'design/admin/datatable' )}{/if}</td><td>{if $f.unique}{'yes'|i18n( 'design/admin/datatable' )}{/if}</td><td>{$f.label|wash}</td></tr>{/foreach}
            </tbody>
        </table>
        </div>
        {/if}
    </div>
</section>

<details class="a4-fold a4-section">
    <summary>{'Parts, license and author'|i18n( 'design/admin/datatable' )}</summary>
    <div class="a4-fold-body a4-stack">
        {foreach $parts as $key => $part}
        <label class="a4-check"><input type="checkbox" name="Parts[]" value="{$key|wash}"{if $settings.parts[$key]} checked="checked"{/if} /> {$part.label|i18n( 'design/admin/datatable' )} <span class="a4-meta">{$part.description|i18n( 'design/admin/datatable' )}</span></label>
        {/foreach}
        <div class="a4-grid">
            <div class="a4-field"><label class="a4-label" for="mt-author">{'Author'|i18n( 'design/admin/datatable' )}</label><input class="a4-input" id="mt-author" name="author" maxlength="120" value="{$settings.author|wash}" /></div>
            <div class="a4-field"><label class="a4-label" for="mt-version">{'Version'|i18n( 'design/admin/datatable' )}</label><input class="a4-input is-auto" id="mt-version" name="version" maxlength="20" value="{$settings.version|wash}" /></div>
            <div class="a4-field"><label class="a4-label" for="mt-licence">{'License'|i18n( 'design/admin/datatable' )}</label>
                {include uri='design:setup/rad_licence_select.tpl' select_id='mt-licence' selected=$settings.licence groups=$licences}</div>
        </div>
    </div>
</details>

<div class="a4-toolbar a4-section">
    <div class="a4-actions">
        <button type="submit" class="a4-btn" name="PreviewButton" value="1">{'Show the files'|i18n( 'design/admin/datatable' )}</button>
        {if $ready}
            {if $can_write}<button type="submit" class="a4-btn a4-btn-primary" name="WriteButton" value="1">{if $extension_exists}{'Add to the extension'|i18n( 'design/admin/datatable' )}{else}{'Write the extension'|i18n( 'design/admin/datatable' )}{/if}</button>{/if}
            <button type="submit" class="a4-btn" name="DownloadButton" value="1">{'Download as zip'|i18n( 'design/admin/datatable' )}</button>
            <button type="submit" class="a4-btn" name="CreateTableButton" value="1">{if $table_exists}{'Bring the table up to date'|i18n( 'design/admin/datatable' )}{else}{'Create the table'|i18n( 'design/admin/datatable' )}{/if}</button>
        {/if}
        {if $extension_exists}
            <button type="submit" class="a4-btn" name="ActivateButton" value="1">{if $extension_active}{'Refresh: autoloads and caches'|i18n( 'design/admin/datatable' )}{else}{'Activate the extension'|i18n( 'design/admin/datatable' )}{/if}</button>
        {/if}
    </div>
    <p class="a4-meta">
        {if $extension_exists}{if $extension_active}{'The extension is active.'|i18n( 'design/admin/datatable' )}{else}{'The extension is written but not active.'|i18n( 'design/admin/datatable' )}{/if}{/if}
        {if is_null( $table_exists )|not}{if $table_exists}{'The table exists.'|i18n( 'design/admin/datatable' )}{else}{'The table does not exist yet.'|i18n( 'design/admin/datatable' )}{/if}{/if}
        {if $extension_active}<a href={concat( $settings.module, '/list' )|ezurl}>{'Open the module'|i18n( 'design/admin/datatable' )}</a>{/if}
    </p>
</div>
</form>

{if $files|count}
<section class="a4-card a4-section" aria-labelledby="mt-files">
    <div class="a4-card-head"><h2 class="a4-h3 a4-card-title" id="mt-files">{'The files (%count)'|i18n( 'design/admin/datatable',, hash( '%count', $files|count ) )|wash}</h2></div>
    <div class="a4-card-body a4-stack">
    {foreach $files as $f}
        <details class="a4-fold">
            <summary><code class="a4-code">{$f.path|wash}</code> <span class="a4-meta">{'%lines lines'|i18n( 'design/admin/datatable',, hash( '%lines', $f.lines ) )|wash}</span>{if $f.exists} <span class="a4-badge is-warn">{'exists, left as it is'|i18n( 'design/admin/datatable' )}</span>{/if}{if $written|contains( $f.path )} <span class="a4-badge is-ok">{'written'|i18n( 'design/admin/datatable' )}</span>{/if}</summary>
            <div class="a4-fold-body"><pre class="a4-pre is-tall">{$f.contents|wash}</pre></div>
        </details>
    {/foreach}
    </div>
</section>
{/if}

</div>
