{* The module extension wizard.

   Connect to a database, pick tables, and the whole extension is worked out and
   shown before any of it is written. Everything happens on this page: the table
   list comes from the connection the form describes, and the preview comes from
   the tables ticked in it. *}

{literal}
<style type="text/css">
.exp-mew {
    --mew-ink: #1c1c1e;
    --mew-muted: #6a6a72;
    --mew-line: #e2e2e6;
    --mew-accent: #2d6cdf;
    --mew-ok: #1f8a4c;
    --mew-bad: #b4232c;
    --mew-gap: 1.6rem;
    color: var(--mew-ink);
}
.exp-mew h2 { font-size: 1.05rem; margin: 0; }
.exp-mew .mew-meta { color: var(--mew-muted); font-size: .92em; }
.exp-mew code, .exp-mew pre { font-family: Menlo, Consolas, "DejaVu Sans Mono", monospace; }

.mew-note {
    display: flex; gap: .6rem; align-items: flex-start;
    border: 1px solid var(--mew-line); border-left-width: 4px; border-radius: 6px;
    padding: .7rem .9rem; margin: 0 0 .8rem 0; background: #fcfcfd;
}
.mew-note.is-ok { border-left-color: var(--mew-ok); }
.mew-note.is-bad { border-left-color: var(--mew-bad); }
.mew-note.is-info { border-left-color: var(--mew-accent); }

.mew-grid { display: flex; flex-wrap: wrap; gap: var(--mew-gap); align-items: flex-start; }
.mew-col { flex: 1 1 24rem; min-width: 0; }

.mew-card {
    border: 1px solid var(--mew-line); border-radius: 8px; background: #fff;
    padding: 1rem 1.1rem; margin: 0 0 var(--mew-gap) 0;
}
.mew-card > h2 { margin-bottom: .2rem; }
.mew-card > .mew-meta { display: block; padding-bottom: .9rem; }

.mew-field { display: flex; flex-direction: column; gap: .3rem; padding-bottom: .9rem; }
.mew-field label { font-size: .82em; color: var(--mew-muted); text-transform: uppercase; letter-spacing: .04em; }
.mew-field input[type=text], .mew-field input[type=password], .mew-field select {
    border: 1px solid var(--mew-line); border-radius: 6px; padding: .45rem .55rem;
    font: inherit; background: #fff; width: 100%; box-sizing: border-box;
}
.mew-field .mew-hint { font-size: .85em; color: var(--mew-muted); }
.mew-row { display: flex; flex-wrap: wrap; gap: .9rem; }
.mew-row > .mew-field { flex: 1 1 9rem; }

.mew-toolbar { display: flex; flex-wrap: wrap; gap: .5rem; align-items: center; padding: .2rem 0 .9rem 0; }
.mew-btn {
    border: 1px solid var(--mew-line); background: #f4f4f5; border-radius: 6px;
    padding: .3rem .7rem; font: inherit; cursor: pointer; color: inherit;
}
.mew-btn:hover { border-color: #9a9aa0; }

.mew-tables { max-height: 28rem; overflow-y: auto; border: 1px solid var(--mew-line); border-radius: 6px; }
.mew-tables table { width: 100%; border-collapse: collapse; font-size: .93em; }
.mew-tables th, .mew-tables td { padding: .35rem .5rem; text-align: left; border-bottom: 1px solid #f0f0f3; }
.mew-tables th { position: sticky; top: 0; background: #f6f7f9; z-index: 1; }
.mew-tables tr.is-selected { background: #f2f7ff; }
.mew-tables td.mew-num { text-align: right; color: var(--mew-muted); white-space: nowrap; }
.mew-tables .mew-nokey { color: var(--mew-bad); }

.mew-parts { display: flex; flex-direction: column; gap: .1rem; }
.mew-part { display: flex; gap: .6rem; align-items: flex-start; padding: .5rem .55rem; border-radius: 6px; }
.mew-part:hover { background: #f6f7f9; }
.mew-part input { margin-top: .25rem; }
.mew-part .mew-part-label { font-weight: 600; }
.mew-part .mew-meta { display: block; overflow-wrap: anywhere; }
/* The text beside a tick box is a sentence, not a word: it has to be allowed
   to wrap, which inside a flex row means saying so. */
.mew-part > span { flex: 1 1 auto; min-width: 0; }
.mew-part .mew-part-label { display: block; }
.mew-part input { flex: 0 0 auto; }
/* The admin stylesheet gives every label white-space: nowrap, which is right
   for a one word label beside a box and wrong for a sentence: the text ran off
   the side of the card and was clipped by .box-content, so it could not be read
   at all. These are sentences, and they wrap. */
.mew-part, .mew-part * { white-space: normal; }
.mew-field label, .mew-field .mew-hint { white-space: normal; }

.mew-map table { width: 100%; border-collapse: collapse; font-size: .93em; }
.mew-map th, .mew-map td { padding: .35rem .5rem; text-align: left; border-bottom: 1px solid #f0f0f3; }
.mew-map th { color: var(--mew-muted); font-weight: 600; }
.mew-cols { display: flex; flex-wrap: wrap; gap: .3rem; padding-top: .3rem; }
.mew-col-chip {
    border: 1px solid var(--mew-line); border-radius: 4px; padding: .05rem .35rem;
    font-size: .85em; background: #fafafb;
}
.mew-col-chip b { font-weight: 600; }
.mew-col-chip.is-key { border-color: var(--mew-accent); color: var(--mew-accent); }

.mew-file { border: 1px solid var(--mew-line); border-radius: 8px; margin: 0 0 .7rem 0; background: #fff; }
.mew-file-head { display: flex; flex-wrap: wrap; align-items: center; gap: .6rem; padding: .55rem .8rem; cursor: pointer; }
.mew-file-head:hover { background: #f6f7f9; }
.mew-file-toggle {
    display: inline-flex; align-items: center; justify-content: center;
    width: 1.25rem; height: 1.25rem; border: 1px solid var(--mew-line);
    border-radius: 4px; font-weight: 700; background: #f4f4f5; line-height: 1;
}
.mew-file-path { font-weight: 600; }
.mew-file pre {
    margin: 0; padding: .8rem 1rem; border-top: 1px solid var(--mew-line);
    background: #fbfbfc; overflow-x: auto; font-size: .88em; line-height: 1.5; white-space: pre;
}
.mew-file.is-collapsed pre { display: none; }

.mew-summary { display: flex; flex-wrap: wrap; gap: 1.4rem; padding-bottom: .9rem; }
.mew-summary b { display: block; font-size: 1.3rem; line-height: 1.2; }
.mew-external { border-left: 3px solid var(--mew-line); padding-left: .9rem; }

/* Keeping everything inside the box it was given. A preview of generated code
   is as wide as its widest line, and a column list is as wide as the table;
   both would otherwise push the page out past its right edge. */
.mew-card, .mew-col { min-width: 0; }
.exp-mew pre {
    max-width: 100%; overflow-x: auto; white-space: pre;
    -webkit-overflow-scrolling: touch;
}
.exp-mew code { overflow-wrap: anywhere; }
.mew-file-path { overflow-wrap: anywhere; min-width: 0; }

/* The mapping table and the table picker both scroll sideways rather than
   widening the page. */
.mew-map { overflow-x: auto; }
.mew-map table { min-width: 34rem; }
.mew-tables { overflow: auto; }
.mew-tables table { min-width: 30rem; }
.mew-cols { max-width: 100%; }
.mew-col-chip { overflow-wrap: anywhere; }
</style>
{/literal}

<form method="post" action={'setup/moduleextension'|ezurl} name="ModuleExtensionWizard">
<input type="hidden" name="Submitted" value="1" />

<div class="context-block exp-mew">

{* DESIGN: Header START *}<div class="box-header"><div class="box-ml">
<h1 class="context-title">{'Module extension wizard'|i18n( 'design/admin/setup/rad/moduleextension' )}</h1>
{* DESIGN: Mainline *}<div class="header-mainline"></div>
{* DESIGN: Header END *}</div></div>

{* DESIGN: Content START *}<div class="box-ml"><div class="box-mr"><div class="box-content">

<div class="context-attributes">
<p>{'A table that eZ did not make has no way into the admin interface and no way into a template. Connect to a database, pick the tables, and this writes the eZPersistentObject classes, a module with list, edit and remove, the templates they draw with, the fetch functions that reach the same rows from a template, and the settings that put it all in the Setup menu. Nothing is written until you ask for it.'|i18n( 'design/admin/setup/rad/moduleextension' )}</p>
</div>

{foreach $wizard_feedback as $mew_note}
<div class="mew-note {if $mew_note.ok}is-ok{else}is-bad{/if}">
    <strong>{if $mew_note.ok}&#10003;{else}!{/if}</strong><span>{$mew_note.message|wash}</span>
</div>
{/foreach}

{foreach $wizard_problems as $mew_problem}
<div class="mew-note is-bad"><strong>!</strong><span>{$mew_problem|wash}</span></div>
{/foreach}

{if $wizard_written|count|gt( 0 )}
<div class="mew-note is-info"><strong>&#10003;</strong><span>{'Written to %target. Switch it on with the lines below, clear the caches, and regenerate the extension autoloads.'|i18n( 'design/admin/setup/rad/moduleextension',, hash( '%target', $wizard_target ) )}</span></div>
{/if}

{if $wizard_can_write|not}
<div class="mew-note is-info"><strong>i</strong><span>{'The web server cannot write into extension/, so this page can only hand you an archive.'|i18n( 'design/admin/setup/rad/moduleextension' )}</span></div>
{/if}

<div class="mew-grid">

{* ------------------------------------------------------ the connection -- *}
<div class="mew-col">

<div class="mew-card">
<h2>{'Where the tables are'|i18n( 'design/admin/setup/rad/moduleextension' )}</h2>
<span class="mew-meta">{$wizard_connection_message|wash}</span>

<div class="mew-field">
    <label for="mewSource">{'Database'|i18n( 'design/admin/setup/rad/moduleextension' )}</label>
    <select id="mewSource" name="source">
    {foreach $wizard_sources as $mew_key => $mew_label}
        <option value="{$mew_key|wash}"{if eq( $wizard_settings.source, $mew_key )} selected="selected"{/if}>{$mew_label|wash}</option>
    {/foreach}
    </select>
</div>

<div id="mewExternal" class="mew-external"{if ne( $wizard_settings.source, 'external' )} style="display:none;"{/if}>
<div class="mew-field">
    <label for="mewType">{'Kind'|i18n( 'design/admin/setup/rad/moduleextension' )}</label>
    <select id="mewType" name="db_type">
    {foreach $wizard_db_types as $mew_key => $mew_label}
        <option value="{$mew_key|wash}"{if eq( $wizard_settings.db_type, $mew_key )} selected="selected"{/if}>{$mew_label|wash}</option>
    {/foreach}
    </select>
    {if $wizard_db_capabilities[$wizard_settings.db_type].notes|ne('')}
    <span class="mew-hint">{$wizard_db_capabilities[$wizard_settings.db_type].notes|wash}</span>
    {/if}
    {if $wizard_db_capabilities[$wizard_settings.db_type].available|not}
    <span class="mew-hint mew-nokey">{$wizard_db_capabilities[$wizard_settings.db_type].why|wash}</span>
    {/if}
</div>

{* A database that is a file is chosen, not typed: the ones this installation
   can see are offered, and anything else can still be written by hand. *}
{if $wizard_fields.db_file.show}
<div class="mew-field">
    <label for="mewFile">{$wizard_fields.db_file.label|i18n( 'design/admin/setup/rad/moduleextension' )}</label>
    {if $wizard_sqlite_files|count|gt( 0 )}
    <select id="mewFilePick">
        <option value="">{'Choose a file this installation can see...'|i18n( 'design/admin/setup/rad/moduleextension' )}</option>
    {foreach $wizard_sqlite_files as $mew_path => $mew_label}
        <option value="{$mew_path|wash}"{if eq( $wizard_settings.db_file, $mew_path )} selected="selected"{/if}>{$mew_label|wash}</option>
    {/foreach}
    </select>
    {/if}
    <input type="text" id="mewFile" name="db_file" value="{$wizard_settings.db_file|wash}" placeholder="var/storage/data.sqlite" />
    <span class="mew-hint">{$wizard_fields.db_file.hint|i18n( 'design/admin/setup/rad/moduleextension' )}</span>
</div>
{/if}

<div class="mew-row">
    {if $wizard_fields.db_server.show}
    <div class="mew-field">
        <label for="mewServer">{$wizard_fields.db_server.label|i18n( 'design/admin/setup/rad/moduleextension' )}</label>
        <input type="text" id="mewServer" name="db_server" value="{$wizard_settings.db_server|wash}" placeholder="localhost" />
    </div>
    {/if}
    {if $wizard_fields.db_port.show}
    <div class="mew-field">
        <label for="mewPort">{$wizard_fields.db_port.label|i18n( 'design/admin/setup/rad/moduleextension' )}</label>
        <input type="text" id="mewPort" name="db_port" value="{if $wizard_settings.db_port|gt(0)}{$wizard_settings.db_port}{/if}" placeholder="{$wizard_db_capabilities[$wizard_settings.db_type].port}" />
    </div>
    {/if}
    {if $wizard_fields.db_name.show}
    <div class="mew-field">
        <label for="mewDbName">{$wizard_fields.db_name.label|i18n( 'design/admin/setup/rad/moduleextension' )}</label>
        <input type="text" id="mewDbName" name="db_name" value="{$wizard_settings.db_name|wash}" />
        {if $wizard_fields.db_name.hint|ne('')}<span class="mew-hint">{$wizard_fields.db_name.hint|i18n( 'design/admin/setup/rad/moduleextension' )}</span>{/if}
    </div>
    {/if}
</div>

<div class="mew-row">
    {if $wizard_fields.db_user.show}
    <div class="mew-field">
        <label for="mewUser">{$wizard_fields.db_user.label|i18n( 'design/admin/setup/rad/moduleextension' )}</label>
        <input type="text" id="mewUser" name="db_user" value="{$wizard_settings.db_user|wash}" autocomplete="off" />
        {if $wizard_fields.db_user.hint|ne('')}<span class="mew-hint">{$wizard_fields.db_user.hint|i18n( 'design/admin/setup/rad/moduleextension' )}</span>{/if}
    </div>
    {/if}
    {if $wizard_fields.db_password.show}
    <div class="mew-field">
        <label for="mewPassword">{$wizard_fields.db_password.label|i18n( 'design/admin/setup/rad/moduleextension' )}</label>
        {* Never echoed back. A password in the value of an input is a password in
           the page source, in the browser's cache and in any proxy between. It
           is typed again when it is needed again. *}
        <input type="password" id="mewPassword" name="db_password" value="" autocomplete="new-password" />
        <span class="mew-hint">{'Typed again each time it is needed; it is never written back into this page.'|i18n( 'design/admin/setup/rad/moduleextension' )}</span>
    </div>
    {/if}
    {if $wizard_fields.db_sample.show}
    <div class="mew-field">
        <label for="mewSample">{$wizard_fields.db_sample.label|i18n( 'design/admin/setup/rad/moduleextension' )}</label>
        <input type="text" id="mewSample" name="db_sample" value="{$wizard_settings.db_sample}" />
        <span class="mew-hint">{$wizard_fields.db_sample.hint|i18n( 'design/admin/setup/rad/moduleextension' )}</span>
    </div>
    {/if}
</div>
<p class="mew-meta">{'A user that may read is enough; nothing here writes to the database it reads.'|i18n( 'design/admin/setup/rad/moduleextension' )}</p>
</div>

<div class="mew-toolbar">
    <button type="submit" class="mew-btn" name="ConnectButton" value="1">{'Connect and list tables'|i18n( 'design/admin/setup/rad/moduleextension' )}</button>
    <span class="mew-meta">{'%count tables visible'|i18n( 'design/admin/setup/rad/moduleextension',, hash( '%count', $wizard_table_count ) )}</span>
</div>
</div>

<div class="mew-card">
<h2>{if eq( $wizard_kind, 'document' )}{'Collections'|i18n( 'design/admin/setup/rad/moduleextension' )}{else}{'Tables'|i18n( 'design/admin/setup/rad/moduleextension' )}{/if}</h2>
<span class="mew-meta">{if eq( $wizard_kind, 'document' )}{'Tick the collections this extension should cover. A collection has no declared shape, so the fields below were worked out by reading a sample of its documents: a field that only some documents carry may be missing, and one that holds different kinds of value in different documents is treated as text.'|i18n( 'design/admin/setup/rad/moduleextension' )}{else}{'Tick the ones this extension should cover. A table with no primary key can still be read, but a single row cannot be addressed, so the first column is used instead.'|i18n( 'design/admin/setup/rad/moduleextension' )}{/if}</span>

<div class="mew-toolbar">
    <input type="text" id="mewFilter" class="mew-btn" placeholder="{'Filter'|i18n( 'design/admin/setup/rad/moduleextension' )}" style="cursor:text;" />
    <button type="button" class="mew-btn" id="mewNone">{'Tick none'|i18n( 'design/admin/setup/rad/moduleextension' )}</button>
    <button type="submit" class="mew-btn" name="PreviewButton" value="1">{'Refresh preview'|i18n( 'design/admin/setup/rad/moduleextension' )}</button>
</div>

{if $wizard_tables|count|gt( 0 )}
<div class="mew-tables">
<table id="mewTableList">
<tr>
    <th>&nbsp;</th>
    <th>{'Table'|i18n( 'design/admin/setup/rad/moduleextension' )}</th>
    <th class="mew-num">{'Columns'|i18n( 'design/admin/setup/rad/moduleextension' )}</th>
    <th>{'Key'|i18n( 'design/admin/setup/rad/moduleextension' )}</th>
    <th class="mew-num">{'Rows'|i18n( 'design/admin/setup/rad/moduleextension' )}</th>
</tr>
{foreach $wizard_tables as $mew_table}
<tr{if $mew_table.selected} class="is-selected"{/if}>
    <td><input type="checkbox" name="Tables[]" value="{$mew_table.name|wash}"{if $mew_table.selected} checked="checked"{/if} /></td>
    <td><code>{$mew_table.name|wash}</code></td>
    <td class="mew-num">{$mew_table.columns}</td>
    <td>{if $mew_table.has_key}<code>{$mew_table.keys|wash}</code>{else}<span class="mew-nokey">{'none'|i18n( 'design/admin/setup/rad/moduleextension' )}</span>{/if}</td>
    <td class="mew-num">{if ne( $mew_table.rows, '' )}{$mew_table.rows}{else}&mdash;{/if}</td>
</tr>
{/foreach}
</table>
</div>
{else}
<p class="mew-meta">{'No tables to show. Connect first.'|i18n( 'design/admin/setup/rad/moduleextension' )}</p>
{/if}
</div>

</div>

{* -------------------------------------------------------- the extension - *}
<div class="mew-col">

<div class="mew-card">
<h2>{'The extension'|i18n( 'design/admin/setup/rad/moduleextension' )}</h2>
<span class="mew-meta">{'The name is the directory; the module name is what appears in an address such as /<module>/list.'|i18n( 'design/admin/setup/rad/moduleextension' )|wash}</span>

<div class="mew-field">
    <label for="mewName">{'Extension name'|i18n( 'design/admin/setup/rad/moduleextension' )}</label>
    <input type="text" id="mewName" name="name" value="{$wizard_settings.name|wash}" placeholder="my_tables" autocomplete="off" />
</div>

<div class="mew-row">
    <div class="mew-field">
        <label for="mewModule">{'Module name'|i18n( 'design/admin/setup/rad/moduleextension' )}</label>
        <input type="text" id="mewModule" name="module" value="{$wizard_settings.module|wash}" />
    </div>
    <div class="mew-field">
        <label for="mewPrefix">{'Class prefix'|i18n( 'design/admin/setup/rad/moduleextension' )}</label>
        <input type="text" id="mewPrefix" name="prefix" value="{$wizard_settings.prefix|wash}" />
        <span class="mew-hint">{'myTables + a table name makes the class name.'|i18n( 'design/admin/setup/rad/moduleextension' )}</span>
    </div>
</div>

<div class="mew-field">
    <label for="mewTitle">{'Title'|i18n( 'design/admin/setup/rad/moduleextension' )}</label>
    <input type="text" id="mewTitle" name="title" value="{$wizard_settings.title|wash}" />
    <span class="mew-hint">{'What the Setup menu entry reads as.'|i18n( 'design/admin/setup/rad/moduleextension' )}</span>
</div>

<div class="mew-field">
    <label for="mewSummary">{'Summary'|i18n( 'design/admin/setup/rad/moduleextension' )}</label>
    <input type="text" id="mewSummary" name="summary" value="{$wizard_settings.summary|wash}" />
</div>

<div class="mew-row">
    <div class="mew-field">
        <label for="mewAuthor">{'Author'|i18n( 'design/admin/setup/rad/moduleextension' )}</label>
        <input type="text" id="mewAuthor" name="author" value="{$wizard_settings.author|wash}" />
    </div>
    <div class="mew-field">
        <label for="mewVendor">{'Composer vendor'|i18n( 'design/admin/setup/rad/moduleextension' )}</label>
        <input type="text" id="mewVendor" name="vendor" value="{$wizard_settings.vendor|wash}" />
    </div>
    <div class="mew-field">
        <label for="mewVersion">{'Version'|i18n( 'design/admin/setup/rad/moduleextension' )}</label>
        <input type="text" id="mewVersion" name="version" value="{$wizard_settings.version|wash}" />
    </div>
</div>

<div class="mew-field">
    <label for="mewLicence">{'Licence'|i18n( 'design/admin/setup/rad/moduleextension' )}</label>
    <select id="mewLicence" name="licence">
    {foreach $wizard_licences as $mew_key => $mew_label}
        <option value="{$mew_key|wash}"{if eq( $wizard_settings.licence, $mew_key )} selected="selected"{/if}>{$mew_label|wash}</option>
    {/foreach}
    </select>
</div>
</div>

<div class="mew-card">
<h2>{'What goes in it'|i18n( 'design/admin/setup/rad/moduleextension' )}</h2>
<div class="mew-parts">
{foreach $wizard_parts as $mew_key => $mew_part}
    <label class="mew-part">
        <input type="checkbox" name="Parts[]" value="{$mew_key|wash}"{if $wizard_settings.parts[$mew_key]} checked="checked"{/if} />
        <span>
            <span class="mew-part-label">{$mew_part.label|i18n( 'design/admin/setup/rad/moduleextension' )}</span>
            <span class="mew-meta">{$mew_part.description|i18n( 'design/admin/setup/rad/moduleextension' )}</span>
        </span>
    </label>
{/foreach}
</div>
</div>

<div class="mew-card">
<h2>{'Switching it on'|i18n( 'design/admin/setup/rad/moduleextension' )}</h2>
<span class="mew-meta">{'Add this to settings/override/site.ini.append.php, then clear the caches and regenerate the extension autoloads.'|i18n( 'design/admin/setup/rad/moduleextension' )}</span>
<pre>{$wizard_activation|wash}</pre>
</div>

</div>
</div>

{* ------------------------------------------------------- the mapping ---- *}
{if $wizard_mapping|count|gt( 0 )}
<div class="mew-card mew-map">
<h2>{'How the columns were read'|i18n( 'design/admin/setup/rad/moduleextension' )}</h2>
<span class="mew-meta">{'Taken from the database, not guessed. A key column is marked.'|i18n( 'design/admin/setup/rad/moduleextension' )}</span>

<table>
<tr>
    <th>{'Table'|i18n( 'design/admin/setup/rad/moduleextension' )}</th>
    <th>{'Class'|i18n( 'design/admin/setup/rad/moduleextension' )}</th>
    <th>{'Key'|i18n( 'design/admin/setup/rad/moduleextension' )}</th>
    <th>{'Columns'|i18n( 'design/admin/setup/rad/moduleextension' )}</th>
</tr>
{foreach $wizard_mapping as $mew_map}
<tr>
    <td><code>{$mew_map.table|wash}</code></td>
    <td><code>{$mew_map.class|wash}</code></td>
    <td><code>{$mew_map.keys|wash}</code>{if $mew_map.increment}<span class="mew-meta"> ({'numbered by the database'|i18n( 'design/admin/setup/rad/moduleextension' )})</span>{/if}</td>
    <td>
        <div class="mew-cols">
        {foreach $mew_map.columns as $mew_column}
            <span class="mew-col-chip{if $mew_column.primary} is-key{/if}"><b>{$mew_column.name|wash}</b> {$mew_column.datatype|wash}</span>
        {/foreach}
        </div>
    </td>
</tr>
{/foreach}
</table>
</div>
{/if}

{* --------------------------------------------------------- the files ---- *}
{if $wizard_file_count|gt( 0 )}
<div class="mew-card">
<h2>{'Every file, before it is written'|i18n( 'design/admin/setup/rad/moduleextension' )}</h2>
<span class="mew-meta">{$wizard_target|wash}</span>

<div class="mew-summary">
    <span><b>{$wizard_file_count}</b><span class="mew-meta">{'files'|i18n( 'design/admin/setup/rad/moduleextension' )}</span></span>
    <span><b>{$wizard_mapping|count}</b><span class="mew-meta">{'tables'|i18n( 'design/admin/setup/rad/moduleextension' )}</span></span>
</div>

<div class="mew-toolbar">
    <button type="button" class="mew-btn" id="mewOpenAll">{'Open all'|i18n( 'design/admin/setup/rad/moduleextension' )}</button>
    <button type="button" class="mew-btn" id="mewCloseAll">{'Close all'|i18n( 'design/admin/setup/rad/moduleextension' )}</button>
</div>

{foreach $wizard_files as $mew_file}
<div class="mew-file is-collapsed">
    <div class="mew-file-head">
        <span class="mew-file-toggle">+</span>
        <span class="mew-file-path">{$mew_file.path|wash}</span>
        <span class="mew-meta">{$mew_file.lines} {'lines'|i18n( 'design/admin/setup/rad/moduleextension' )}, {$mew_file.bytes} {'bytes'|i18n( 'design/admin/setup/rad/moduleextension' )}</span>
    </div>
    <pre>{$mew_file.contents|wash}</pre>
</div>
{/foreach}
</div>
{/if}

{* DESIGN: Content END *}</div></div></div>

<div class="controlbar">
{* DESIGN: Control bar START *}<div class="box-bc"><div class="box-ml">
<div class="block">
    {if $wizard_ready}
    <input class="defaultbutton" type="submit" name="CreateButton" value="{'Create in extension/'|i18n( 'design/admin/setup/rad/moduleextension' )}" />
    {else}
    <input class="button-disabled" type="submit" name="_Disabled" value="{'Create in extension/'|i18n( 'design/admin/setup/rad/moduleextension' )}" disabled="disabled" />
    {/if}

    {if $wizard_file_count|gt( 0 )}
        {if $wizard_can_archive}
    <input class="button" type="submit" name="DownloadButton" value="{'Download as zip'|i18n( 'design/admin/setup/rad/moduleextension' )}" />
        {/if}
    {/if}

    <input class="button" type="submit" name="PreviewButton" value="{'Refresh preview'|i18n( 'design/admin/setup/rad/moduleextension' )}" />
    <a class="mew-meta" href={'setup/rad'|ezurl}>{'Back to the RAD tools'|i18n( 'design/admin/setup/rad/moduleextension' )}</a>
</div>
{* DESIGN: Control bar END *}</div></div>
</div>

</div>
</form>

{literal}
<script type="text/javascript">
( function () {
    // The external details only apply to an external database.
    var source = document.getElementById( 'mewSource' ),
        external = document.getElementById( 'mewExternal' );

    // Choosing a file from the list fills the box; the box is still there for a
    // path the list does not know about.
    var pick = document.getElementById( 'mewFilePick' ),
        file = document.getElementById( 'mewFile' );
    if ( pick && file )
    {
        pick.onchange = function () {
            if ( pick.value !== '' ) file.value = pick.value;
        };
    }

    // Which boxes apply depends on the kind, and the server decides the same
    // thing after every submit; this only saves waiting for one.
    var type = document.getElementById( 'mewType' );
    if ( type )
    {
        type.onchange = function () {
            var connect = document.getElementsByName( 'ConnectButton' );
            if ( connect.length ) connect[0].click();
        };
    }

    if ( source && external )
    {
        source.onchange = function () {
            external.style.display = source.value === 'external' ? '' : 'none';
        };
    }

    // Folding a file open and shut.
    function fold( panel, open )
    {
        var collapsed = panel.className.indexOf( 'is-collapsed' ) !== -1;
        if ( open === collapsed )
        {
            panel.className = open
                ? panel.className.replace( / ?is-collapsed/, '' )
                : panel.className + ' is-collapsed';
        }
        var toggle = panel.getElementsByTagName( 'span' )[0];
        if ( toggle ) toggle.innerHTML = open ? '&minus;' : '+';
    }

    var divs = document.getElementsByTagName( 'div' ), files = [], i;
    for ( i = 0; i < divs.length; i++ )
        if ( divs[i].className.indexOf( 'mew-file' ) === 0 )
            files.push( divs[i] );

    for ( i = 0; i < files.length; i++ )
    {
        ( function ( panel ) {
            var head = panel.getElementsByTagName( 'div' )[0];
            if ( !head ) return;
            head.onclick = function () {
                fold( panel, panel.className.indexOf( 'is-collapsed' ) !== -1 );
                return false;
            };
        } )( files[i] );
    }

    function every( open ) { for ( var j = 0; j < files.length; j++ ) fold( files[j], open ); }

    var openAll = document.getElementById( 'mewOpenAll' ),
        closeAll = document.getElementById( 'mewCloseAll' );
    if ( openAll )  openAll.onclick  = function () { every( true ); return false; };
    if ( closeAll ) closeAll.onclick = function () { every( false ); return false; };

    // Narrowing a long table list without going back to the server.
    var filter = document.getElementById( 'mewFilter' ),
        list = document.getElementById( 'mewTableList' );

    if ( filter && list )
    {
        filter.onkeyup = function () {
            var want = filter.value.toLowerCase(),
                rows = list.getElementsByTagName( 'tr' ), r;

            for ( r = 1; r < rows.length; r++ )
            {
                var text = ( rows[r].textContent || rows[r].innerText || '' ).toLowerCase();
                rows[r].style.display = want === '' || text.indexOf( want ) !== -1 ? '' : 'none';
            }
        };
    }

    var none = document.getElementById( 'mewNone' );
    if ( none )
    {
        none.onclick = function () {
            var boxes = document.getElementsByTagName( 'input' ), b;
            for ( b = 0; b < boxes.length; b++ )
                if ( boxes[b].type === 'checkbox' && boxes[b].name === 'Tables[]' )
                    boxes[b].checked = false;
            return false;
        };
    }
} )();
</script>
{/literal}
