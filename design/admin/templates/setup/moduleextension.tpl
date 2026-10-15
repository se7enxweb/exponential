{* The module extension wizard.

   Connect to a database, pick tables, and the whole extension is worked out and
   shown before any of it is written. Everything happens on this page: the table
   list comes from the connection the form describes, and the preview comes from
   the tables ticked in it. *}


<form method="post" action={'setup/moduleextension'|ezurl} name="ModuleExtensionWizard">
<input type="hidden" name="Submitted" value="1" />

<div class="context-block a4-page a4-setup-wz">

{* DESIGN: Header START *}<div class="box-header"><div class="box-ml">
<h1 class="context-title">{'Module extension wizard'|i18n( 'design/admin/setup/rad/moduleextension' )}</h1>
{* DESIGN: Mainline *}<div class="header-mainline"></div>
{* DESIGN: Header END *}</div></div>

{* DESIGN: Content START *}<div class="box-ml"><div class="box-mr"><div class="box-content">

<div class="context-attributes">
<p>{'A table that eZ did not make has no way into the admin interface and no way into a template. Connect to a database, pick the tables, and this writes the eZPersistentObject classes, a module with list, edit and remove, the templates they draw with, the fetch functions that reach the same rows from a template, and the settings that put it all in the Setup menu. Nothing is written until you ask for it.'|i18n( 'design/admin/setup/rad/moduleextension' )}</p>
</div>

{foreach $wizard_feedback as $mew_note}
<div class="a4-notice {if $mew_note.ok}is-ok{else}is-bad{/if}">
    <strong>{if $mew_note.ok}&#10003;{else}!{/if}</strong><span>{$mew_note.message|wash}</span>
</div>
{/foreach}

{foreach $wizard_problems as $mew_problem}
<div class="a4-notice is-bad"><strong>!</strong><span>{$mew_problem|wash}</span></div>
{/foreach}

{if $wizard_written|count|gt( 0 )}
<div class="a4-notice is-info"><strong>&#10003;</strong><span>{'Written to %target. Switch it on with the lines below, clear the caches, and regenerate the extension autoloads.'|i18n( 'design/admin/setup/rad/moduleextension',, hash( '%target', $wizard_target ) )}</span></div>
{/if}

{if $wizard_can_write|not}
<div class="a4-notice is-info"><strong>i</strong><span>{'The web server cannot write into extension/, so this page can only hand you an archive.'|i18n( 'design/admin/setup/rad/moduleextension' )}</span></div>
{/if}

<div class="a4-setup-wz-grid">

{* ------------------------------------------------------ the connection -- *}
<div class="a4-setup-wz-col">

<div class="a4-card">
<h2>{'Where the tables are'|i18n( 'design/admin/setup/rad/moduleextension' )}</h2>
<span class="a4-meta">{$wizard_connection_message|wash}</span>

<div class="a4-field">
    <label for="mewSource">{'Database'|i18n( 'design/admin/setup/rad/moduleextension' )}</label>
    <select id="mewSource" name="source">
    {foreach $wizard_sources as $mew_key => $mew_label}
        <option value="{$mew_key|wash}"{if eq( $wizard_settings.source, $mew_key )} selected="selected"{/if}>{$mew_label|wash}</option>
    {/foreach}
    </select>
</div>

<div id="mewExternal" class="a4-setup-wz-external"{if ne( $wizard_settings.source, 'external' )} hidden{/if}>
<div class="a4-field">
    <label for="mewType">{'Kind'|i18n( 'design/admin/setup/rad/moduleextension' )}</label>
    <select id="mewType" name="db_type">
    {foreach $wizard_db_types as $mew_key => $mew_label}
        <option value="{$mew_key|wash}"{if eq( $wizard_settings.db_type, $mew_key )} selected="selected"{/if}>{$mew_label|wash}</option>
    {/foreach}
    </select>
    {if $wizard_db_capabilities[$wizard_settings.db_type].notes|ne('')}
    <span class="a4-help">{$wizard_db_capabilities[$wizard_settings.db_type].notes|wash}</span>
    {/if}
    {if $wizard_db_capabilities[$wizard_settings.db_type].available|not}
    <span class="a4-help a4-setup-wz-nokey">{$wizard_db_capabilities[$wizard_settings.db_type].why|wash}</span>
    {/if}
</div>

{* A database that is a file is chosen, not typed: the ones this installation
   can see are offered, and anything else can still be written by hand. *}
{if $wizard_fields.db_file.show}
<div class="a4-field">
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
    <span class="a4-help">{$wizard_fields.db_file.hint|i18n( 'design/admin/setup/rad/moduleextension' )}</span>
</div>
{/if}

<div class="a4-setup-wz-row">
    {if $wizard_fields.db_server.show}
    <div class="a4-field">
        <label for="mewServer">{$wizard_fields.db_server.label|i18n( 'design/admin/setup/rad/moduleextension' )}</label>
        <input type="text" id="mewServer" name="db_server" value="{$wizard_settings.db_server|wash}" placeholder="localhost" />
    </div>
    {/if}
    {if $wizard_fields.db_port.show}
    <div class="a4-field">
        <label for="mewPort">{$wizard_fields.db_port.label|i18n( 'design/admin/setup/rad/moduleextension' )}</label>
        <input type="text" id="mewPort" name="db_port" value="{if $wizard_settings.db_port|gt(0)}{$wizard_settings.db_port}{/if}" placeholder="{$wizard_db_capabilities[$wizard_settings.db_type].port}" />
    </div>
    {/if}
    {if $wizard_fields.db_name.show}
    <div class="a4-field">
        <label for="mewDbName">{$wizard_fields.db_name.label|i18n( 'design/admin/setup/rad/moduleextension' )}</label>
        <input type="text" id="mewDbName" name="db_name" value="{$wizard_settings.db_name|wash}" />
        {if $wizard_fields.db_name.hint|ne('')}<span class="a4-help">{$wizard_fields.db_name.hint|i18n( 'design/admin/setup/rad/moduleextension' )}</span>{/if}
    </div>
    {/if}
</div>

<div class="a4-setup-wz-row">
    {if $wizard_fields.db_user.show}
    <div class="a4-field">
        <label for="mewUser">{$wizard_fields.db_user.label|i18n( 'design/admin/setup/rad/moduleextension' )}</label>
        <input type="text" id="mewUser" name="db_user" value="{$wizard_settings.db_user|wash}" autocomplete="off" />
        {if $wizard_fields.db_user.hint|ne('')}<span class="a4-help">{$wizard_fields.db_user.hint|i18n( 'design/admin/setup/rad/moduleextension' )}</span>{/if}
    </div>
    {/if}
    {if $wizard_fields.db_password.show}
    <div class="a4-field">
        <label for="mewPassword">{$wizard_fields.db_password.label|i18n( 'design/admin/setup/rad/moduleextension' )}</label>
        {* Never echoed back. A password in the value of an input is a password in
           the page source, in the browser's cache and in any proxy between. It
           is typed again when it is needed again. *}
        <input type="password" id="mewPassword" name="db_password" value="" autocomplete="new-password" />
        <span class="a4-help">{'Typed again each time it is needed; it is never written back into this page.'|i18n( 'design/admin/setup/rad/moduleextension' )}</span>
    </div>
    {/if}
    {if $wizard_fields.db_sample.show}
    <div class="a4-field">
        <label for="mewSample">{$wizard_fields.db_sample.label|i18n( 'design/admin/setup/rad/moduleextension' )}</label>
        <input type="text" id="mewSample" name="db_sample" value="{$wizard_settings.db_sample|wash}" />
        <span class="a4-help">{$wizard_fields.db_sample.hint|i18n( 'design/admin/setup/rad/moduleextension' )}</span>
    </div>
    {/if}
</div>
<p class="a4-meta">{'A user that may read is enough; nothing here writes to the database it reads.'|i18n( 'design/admin/setup/rad/moduleextension' )}</p>
</div>

<div class="a4-actions">
    <button type="submit" class="a4-btn a4-btn-small" name="ConnectButton" value="1">{'Connect and list tables'|i18n( 'design/admin/setup/rad/moduleextension' )}</button>
    <span class="a4-meta">{'%count tables visible'|i18n( 'design/admin/setup/rad/moduleextension',, hash( '%count', $wizard_table_count ) )}</span>
</div>
</div>

<div class="a4-card">
<h2>{if eq( $wizard_kind, 'document' )}{'Collections'|i18n( 'design/admin/setup/rad/moduleextension' )}{else}{'Tables'|i18n( 'design/admin/setup/rad/moduleextension' )}{/if}</h2>
<span class="a4-meta">{if eq( $wizard_kind, 'document' )}{'Tick the collections this extension should cover. A collection has no declared shape, so the fields below were worked out by reading a sample of its documents: a field that only some documents carry may be missing, and one that holds different kinds of value in different documents is treated as text.'|i18n( 'design/admin/setup/rad/moduleextension' )}{else}{'Tick the ones this extension should cover. A table with no primary key can still be read, but a single row cannot be addressed, so the first column is used instead.'|i18n( 'design/admin/setup/rad/moduleextension' )}{/if}</span>

<div class="a4-actions">
    <input type="text" id="mewFilter" class="a4-input is-auto" placeholder="{'Filter'|i18n( 'design/admin/setup/rad/moduleextension' )}" />
    <button type="button" class="a4-btn a4-btn-small" id="mewNone">{'Tick none'|i18n( 'design/admin/setup/rad/moduleextension' )}</button>
    <button type="submit" class="a4-btn a4-btn-small" name="PreviewButton" value="1">{'Refresh preview'|i18n( 'design/admin/setup/rad/moduleextension' )}</button>
</div>

{if $wizard_tables|count|gt( 0 )}
<div class="a4-setup-wz-tables">
<table id="mewTableList">
<tr>
    <th>&nbsp;</th>
    <th>{'Table'|i18n( 'design/admin/setup/rad/moduleextension' )}</th>
    <th class="a4-num a4-muted">{'Columns'|i18n( 'design/admin/setup/rad/moduleextension' )}</th>
    <th>{'Key'|i18n( 'design/admin/setup/rad/moduleextension' )}</th>
    <th class="a4-num a4-muted">{'Rows'|i18n( 'design/admin/setup/rad/moduleextension' )}</th>
</tr>
{foreach $wizard_tables as $mew_table}
<tr{if $mew_table.selected} class="is-selected"{/if}>
    <td><input type="checkbox" name="Tables[]" value="{$mew_table.name|wash}"{if $mew_table.selected} checked="checked"{/if} /></td>
    <td><code>{$mew_table.name|wash}</code></td>
    <td class="a4-num a4-muted">{$mew_table.columns}</td>
    <td>{if $mew_table.has_key}<code>{$mew_table.keys|wash}</code>{else}<span class="a4-setup-wz-nokey">{'none'|i18n( 'design/admin/setup/rad/moduleextension' )}</span>{/if}</td>
    <td class="a4-num a4-muted">{if ne( $mew_table.rows, '' )}{$mew_table.rows}{else}&mdash;{/if}</td>
</tr>
{/foreach}
</table>
</div>
{else}
<p class="a4-meta">{'No tables to show. Connect first.'|i18n( 'design/admin/setup/rad/moduleextension' )}</p>
{/if}
</div>

</div>

{* -------------------------------------------------------- the extension - *}
<div class="a4-setup-wz-col">

<div class="a4-card">
<h2>{'The extension'|i18n( 'design/admin/setup/rad/moduleextension' )}</h2>
<span class="a4-meta">{'The name is the directory; the module name is what appears in an address such as /<module>/list.'|i18n( 'design/admin/setup/rad/moduleextension' )|wash}</span>

<div class="a4-field">
    <label for="mewName">{'Extension name'|i18n( 'design/admin/setup/rad/moduleextension' )}</label>
    <input type="text" id="mewName" name="name" value="{$wizard_settings.name|wash}" placeholder="my_tables" autocomplete="off" />
</div>

<div class="a4-setup-wz-row">
    <div class="a4-field">
        <label for="mewModule">{'Module name'|i18n( 'design/admin/setup/rad/moduleextension' )}</label>
        <input type="text" id="mewModule" name="module" value="{$wizard_settings.module|wash}" />
    </div>
    <div class="a4-field">
        <label for="mewPrefix">{'Class prefix'|i18n( 'design/admin/setup/rad/moduleextension' )}</label>
        <input type="text" id="mewPrefix" name="prefix" value="{$wizard_settings.prefix|wash}" />
        <span class="a4-help">{'myTables + a table name makes the class name.'|i18n( 'design/admin/setup/rad/moduleextension' )}</span>
    </div>
</div>

<div class="a4-field">
    <label for="mewTitle">{'Title'|i18n( 'design/admin/setup/rad/moduleextension' )}</label>
    <input type="text" id="mewTitle" name="title" value="{$wizard_settings.title|wash}" />
    <span class="a4-help">{'What the Setup menu entry reads as.'|i18n( 'design/admin/setup/rad/moduleextension' )}</span>
</div>

<div class="a4-field">
    <label for="mewSummary">{'Summary'|i18n( 'design/admin/setup/rad/moduleextension' )}</label>
    <input type="text" id="mewSummary" name="summary" value="{$wizard_settings.summary|wash}" />
</div>

<div class="a4-setup-wz-row">
    <div class="a4-field">
        <label for="mewAuthor">{'Author'|i18n( 'design/admin/setup/rad/moduleextension' )}</label>
        <input type="text" id="mewAuthor" name="author" value="{$wizard_settings.author|wash}" />
    </div>
    <div class="a4-field">
        <label for="mewVendor">{'Composer vendor'|i18n( 'design/admin/setup/rad/moduleextension' )}</label>
        <input type="text" id="mewVendor" name="vendor" value="{$wizard_settings.vendor|wash}" />
    </div>
    <div class="a4-field">
        <label for="mewVersion">{'Version'|i18n( 'design/admin/setup/rad/moduleextension' )}</label>
        <input type="text" id="mewVersion" name="version" value="{$wizard_settings.version|wash}" />
    </div>
</div>

<div class="a4-field">
    <label for="mewLicence">{'Licence'|i18n( 'design/admin/setup/rad/moduleextension' )}</label>
    {include uri='design:setup/rad_licence_select.tpl' select_id='mewLicence' selected=$wizard_settings.licence groups=$wizard_licences}
</div>
</div>

<div class="a4-card">
<h2>{'What goes in it'|i18n( 'design/admin/setup/rad/moduleextension' )}</h2>
<div class="a4-setup-wz-parts">
{foreach $wizard_parts as $mew_key => $mew_part}
    <label class="a4-setup-wz-pick">
        <input type="checkbox" name="Parts[]" value="{$mew_key|wash}"{if $wizard_settings.parts[$mew_key]} checked="checked"{/if} />
        <span>
            <span class="a4-setup-wz-pick-label">{$mew_part.label|i18n( 'design/admin/setup/rad/moduleextension' )}</span>
            <span class="a4-meta">{$mew_part.description|i18n( 'design/admin/setup/rad/moduleextension' )}</span>
        </span>
    </label>
{/foreach}
</div>
</div>

<div class="a4-card">
<h2>{'Switching it on'|i18n( 'design/admin/setup/rad/moduleextension' )}</h2>
<span class="a4-meta">{'Add this to settings/override/site.ini.append.php, then clear the caches and regenerate the extension autoloads.'|i18n( 'design/admin/setup/rad/moduleextension' )}</span>
<pre>{$wizard_activation|wash}</pre>
</div>

</div>
</div>

{* ------------------------------------------------------- the mapping ---- *}
{if $wizard_mapping|count|gt( 0 )}
<div class="a4-card a4-setup-wz-map">
<h2>{'How the columns were read'|i18n( 'design/admin/setup/rad/moduleextension' )}</h2>
<span class="a4-meta">{'Taken from the database, not guessed. A key column is marked.'|i18n( 'design/admin/setup/rad/moduleextension' )}</span>

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
    <td><code>{$mew_map.keys|wash}</code>{if $mew_map.increment}<span class="a4-meta"> ({'numbered by the database'|i18n( 'design/admin/setup/rad/moduleextension' )})</span>{/if}</td>
    <td>
        <div class="a4-setup-wz-chips">
        {foreach $mew_map.columns as $mew_column}
            <span class="a4-setup-wz-chip{if $mew_column.primary} is-key{/if}"><b>{$mew_column.name|wash}</b> {$mew_column.datatype|wash}</span>
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
<div class="a4-card">
<h2>{'Every file, before it is written'|i18n( 'design/admin/setup/rad/moduleextension' )}</h2>
<span class="a4-meta">{$wizard_target|wash}</span>

<div class="a4-setup-wz-summary">
    <span><b>{$wizard_file_count}</b><span class="a4-meta">{'files'|i18n( 'design/admin/setup/rad/moduleextension' )}</span></span>
    <span><b>{$wizard_mapping|count}</b><span class="a4-meta">{'tables'|i18n( 'design/admin/setup/rad/moduleextension' )}</span></span>
</div>

<div class="a4-actions">
    <button type="button" class="a4-btn a4-btn-small" id="mewOpenAll">{'Open all'|i18n( 'design/admin/setup/rad/moduleextension' )}</button>
    <button type="button" class="a4-btn a4-btn-small" id="mewCloseAll">{'Close all'|i18n( 'design/admin/setup/rad/moduleextension' )}</button>
</div>

{foreach $wizard_files as $mew_file}
<div class="a4-setup-wz-file is-collapsed">
    <div class="a4-setup-wz-file-head">
        <span class="a4-setup-wz-file-toggle">+</span>
        <span class="a4-setup-wz-file-path">{$mew_file.path|wash}</span>
        <span class="a4-meta">{'%lines lines, %bytes bytes'|i18n( 'design/admin/setup/rad/moduleextension',, hash( '%lines', $mew_file.lines, '%bytes', $mew_file.bytes ) )}</span>
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
    <a class="a4-meta" href={'setup/rad'|ezurl}>{'Back to the RAD tools'|i18n( 'design/admin/setup/rad/moduleextension' )}</a>
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
            external.hidden = source.value !== 'external';
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
        if ( divs[i].className.indexOf( 'a4-setup-wz-file' ) === 0 )
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
                rows[r].hidden = !( want === '' || text.indexOf( want ) !== -1 );
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
