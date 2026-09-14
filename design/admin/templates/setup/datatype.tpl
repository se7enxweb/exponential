{* The datatype wizard.

   A datatype is the largest thing this system asks anybody to write. This page
   asks what it has to do rather than what it is called, and writes only the
   methods that answer to that. *}

{literal}
<style type="text/css">
.exp-dw {
    --dw-ink: #1c1c1e; --dw-muted: #6a6a72; --dw-line: #e2e2e6;
    --dw-accent: #2d6cdf; --dw-ok: #1f8a4c; --dw-bad: #b4232c; --dw-gap: 1.6rem;
    color: var(--dw-ink);
}
.exp-dw h2 { font-size: 1.05rem; margin: 0 0 .15rem 0; }
.exp-dw h3 { font-size: .92rem; margin: 1rem 0 .3rem 0; color: var(--dw-muted); text-transform: uppercase; letter-spacing: .04em; }
.exp-dw .dw-meta { color: var(--dw-muted); font-size: .92em; white-space: normal; }
.exp-dw code, .exp-dw pre { font-family: Menlo, Consolas, "DejaVu Sans Mono", monospace; overflow-wrap: anywhere; }
.exp-dw pre { max-width: 100%; overflow-x: auto; white-space: pre; }
.exp-dw label, .exp-dw label * { white-space: normal; }

.dw-note {
    display: flex; gap: .6rem; align-items: flex-start;
    border: 1px solid var(--dw-line); border-left-width: 4px; border-radius: 6px;
    padding: .7rem .9rem; margin: 0 0 .8rem 0; background: #fcfcfd;
}
.dw-note.is-ok { border-left-color: var(--dw-ok); }
.dw-note.is-bad { border-left-color: var(--dw-bad); }
.dw-note.is-info { border-left-color: var(--dw-accent); }

.dw-card {
    border: 1px solid var(--dw-line); border-radius: 8px; background: #fff;
    padding: 1rem 1.1rem; margin: 0 0 var(--dw-gap) 0; min-width: 0;
}
.dw-card > .dw-meta { display: block; padding-bottom: .9rem; }
.dw-grid { display: flex; flex-wrap: wrap; gap: var(--dw-gap); align-items: flex-start; }
.dw-col { flex: 1 1 26rem; min-width: 0; }

.dw-field { display: flex; flex-direction: column; gap: .3rem; padding-bottom: .9rem; }
.dw-field label { font-size: .82em; color: var(--dw-muted); text-transform: uppercase; letter-spacing: .04em; font-weight: 600; }
.dw-field input[type=text], .dw-field select {
    border: 1px solid var(--dw-line); border-radius: 6px; padding: .45rem .55rem;
    font: inherit; background: #fff; width: 100%; box-sizing: border-box;
}
.dw-field .dw-hint { font-size: .85em; color: var(--dw-muted); text-transform: none; letter-spacing: 0; font-weight: normal; }
.dw-row { display: flex; flex-wrap: wrap; gap: .9rem; }
.dw-row > .dw-field { flex: 1 1 9rem; }

.dw-pick { display: flex; gap: .6rem; align-items: flex-start; padding: .5rem .55rem; border-radius: 6px; }
.dw-pick:hover { background: #f6f7f9; }
.dw-pick input { margin-top: .25rem; flex: 0 0 auto; }
.dw-pick > span { flex: 1 1 auto; min-width: 0; }
.dw-pick .dw-pick-label { display: block; font-weight: 600; }
.dw-pick .dw-meta { display: block; }
.dw-pick .dw-count { color: var(--dw-accent); font-weight: 600; font-size: .85em; }
.dw-pick.is-locked { opacity: .75; }

.dw-cols { border-top: 1px solid #f0f0f3; padding: .5rem 0; display: flex; flex-wrap: wrap; gap: .5rem; align-items: baseline; }
.dw-cols code { flex: 0 0 9rem; font-weight: 600; }
.dw-cols .dw-meta { flex: 1 1 14rem; min-width: 0; }

.dw-setting { display: flex; flex-wrap: wrap; gap: .5rem; align-items: center; padding: .25rem 0; }
.dw-setting code { flex: 0 0 7rem; }
.dw-setting input { flex: 1 1 10rem; min-width: 0; border: 1px solid var(--dw-line); border-radius: 6px; padding: .3rem .45rem; font: inherit; box-sizing: border-box; }

.dw-method { border-top: 1px solid #f0f0f3; padding: .55rem 0; }
.dw-method code { font-weight: 600; display: block; padding-bottom: .15rem; }
.dw-group { display: block; font-size: .78em; letter-spacing: .05em; text-transform: uppercase; color: var(--dw-accent); font-weight: 700; padding-top: .8rem; }

.dw-file { border: 1px solid var(--dw-line); border-radius: 8px; margin: 0 0 .6rem 0; background: #fff; }
.dw-file-head { display: flex; flex-wrap: wrap; align-items: center; gap: .6rem; padding: .55rem .8rem; cursor: pointer; }
.dw-file-head:hover { background: #f6f7f9; }
.dw-file-toggle {
    display: inline-flex; align-items: center; justify-content: center;
    width: 1.25rem; height: 1.25rem; border: 1px solid var(--dw-line);
    border-radius: 4px; font-weight: 700; background: #f4f4f5; line-height: 1;
}
.dw-file-path { font-weight: 600; overflow-wrap: anywhere; min-width: 0; }
.dw-file pre { margin: 0; padding: .8rem 1rem; border-top: 1px solid var(--dw-line); background: #fbfbfc; font-size: .88em; line-height: 1.5; }
.dw-file.is-collapsed pre { display: none; }
.dw-toolbar { display: flex; flex-wrap: wrap; gap: .5rem; padding: .2rem 0 .9rem 0; }
.dw-btn { border: 1px solid var(--dw-line); background: #f4f4f5; border-radius: 6px; padding: .3rem .7rem; font: inherit; cursor: pointer; color: inherit; }
.dw-btn:hover { border-color: #9a9aa0; }
.dw-summary { display: flex; flex-wrap: wrap; gap: 1.6rem; align-items: baseline; padding: 0 0 1rem 0; }
.dw-summary b { font-size: 1.5rem; }
</style>
{/literal}

<form method="post" action={'setup/datatype'|ezurl} name="DatatypeWizard">
<input type="hidden" name="Submitted" value="1" />

<div class="context-block exp-dw">

{* DESIGN: Header START *}<div class="box-header"><div class="box-ml">
<h1 class="context-title">{'Datatype wizard'|i18n( 'design/admin/setup/rad/datatype' )}</h1>
{* DESIGN: Mainline *}<div class="header-mainline"></div>
{* DESIGN: Header END *}</div></div>

{* DESIGN: Content START *}<div class="box-ml"><div class="box-mr"><div class="box-content">

<div class="context-attributes">
<p>{'A datatype is a kind of value a content class attribute can hold, with its own editing field, its own validation, its own storage and its own display. eZDataType declares over ninety methods; which of them a datatype needs depends entirely on what it is for. Say what it has to do below and only those are written.'|i18n( 'design/admin/setup/rad/datatype' )}</p>
</div>

{foreach $wizard_feedback as $dw_note}
<div class="dw-note {if $dw_note.ok}is-ok{else}is-bad{/if}">
    <strong>{if $dw_note.ok}&#10003;{else}!{/if}</strong><span>{$dw_note.message|wash}</span>
</div>
{/foreach}

{foreach $wizard_problems as $dw_problem}
<div class="dw-note is-bad"><strong>!</strong><span>{$dw_problem|wash}</span></div>
{/foreach}

{if $wizard_written|count|gt( 0 )}
<div class="dw-note is-info"><strong>&#10003;</strong><span>{'Written to %target. Switch it on with the lines below, regenerate the extension autoloads, and clear the caches.'|i18n( 'design/admin/setup/rad/datatype',, hash( '%target', $wizard_target ) )}</span></div>
{/if}

{if $wizard_can_write|not}
<div class="dw-note is-info"><strong>i</strong><span>{'The web server cannot write into extension/, so this page can only hand you an archive.'|i18n( 'design/admin/setup/rad/datatype' )}</span></div>
{/if}

<div class="dw-summary">
    <span><b>{$wizard_method_count}</b> {'methods'|i18n( 'design/admin/setup/rad/datatype' )}</span>
    <span><b>{$wizard_file_count}</b> {'files'|i18n( 'design/admin/setup/rad/datatype' )}</span>
    <span class="dw-meta">{'%count datatypes are already installed on this site.'|i18n( 'design/admin/setup/rad/datatype',, hash( '%count', $wizard_existing|count ) )}</span>
</div>

<div class="dw-grid">
<div class="dw-col">

<div class="dw-card">
<h2>{'The datatype'|i18n( 'design/admin/setup/rad/datatype' )}</h2>
<span class="dw-meta">{'The identifier goes in the database against every attribute of this type, in every ini that mentions it, and in the name of the file the kernel looks for. It cannot be changed once content exists.'|i18n( 'design/admin/setup/rad/datatype' )}</span>

<div class="dw-row">
    <div class="dw-field">
        <label for="dwName">{'Extension name'|i18n( 'design/admin/setup/rad/datatype' )}</label>
        <input type="text" id="dwName" name="name" value="{$wizard_settings.name|wash}" placeholder="my_datatype" autocomplete="off" />
    </div>
    <div class="dw-field">
        <label for="dwType">{'Datatype identifier'|i18n( 'design/admin/setup/rad/datatype' )}</label>
        <input type="text" id="dwType" name="type" value="{$wizard_settings.type|wash}" placeholder="mytype" autocomplete="off" />
        <span class="dw-hint">{'Lower case letters and digits only.'|i18n( 'design/admin/setup/rad/datatype' )}</span>
    </div>
    <div class="dw-field">
        <label for="dwClass">{'Class name'|i18n( 'design/admin/setup/rad/datatype' )}</label>
        <input type="text" id="dwClass" name="class" value="{$wizard_settings.class|wash}" autocomplete="off" />
    </div>
</div>

<div class="dw-row">
    <div class="dw-field">
        <label for="dwTitle">{'Name in the class editor'|i18n( 'design/admin/setup/rad/datatype' )}</label>
        <input type="text" id="dwTitle" name="title" value="{$wizard_settings.title|wash}" />
    </div>
    <div class="dw-field">
        <label for="dwGroup">{'Group it is listed under'|i18n( 'design/admin/setup/rad/datatype' )}</label>
        <input type="text" id="dwGroup" name="group" value="{$wizard_settings.group|wash}" />
    </div>
</div>

<div class="dw-field">
    <label for="dwSummary">{'Summary'|i18n( 'design/admin/setup/rad/datatype' )}</label>
    <input type="text" id="dwSummary" name="summary" value="{$wizard_settings.summary|wash}" />
</div>

<div class="dw-row">
    <div class="dw-field">
        <label for="dwAuthor">{'Author'|i18n( 'design/admin/setup/rad/datatype' )}</label>
        <input type="text" id="dwAuthor" name="author" value="{$wizard_settings.author|wash}" />
    </div>
    <div class="dw-field">
        <label for="dwVendor">{'Composer vendor'|i18n( 'design/admin/setup/rad/datatype' )}</label>
        <input type="text" id="dwVendor" name="vendor" value="{$wizard_settings.vendor|wash}" />
    </div>
    <div class="dw-field">
        <label for="dwVersion">{'Version'|i18n( 'design/admin/setup/rad/datatype' )}</label>
        <input type="text" id="dwVersion" name="version" value="{$wizard_settings.version|wash}" />
    </div>
</div>

<div class="dw-field">
    <label for="dwLicence">{'Licence'|i18n( 'design/admin/setup/rad/datatype' )}</label>
    <select id="dwLicence" name="licence">
    {foreach $wizard_licences as $dw_key => $dw_label}
        <option value="{$dw_key|wash}"{if eq( $wizard_settings.licence, $dw_key )} selected="selected"{/if}>{$dw_label|wash}</option>
    {/foreach}
    </select>
</div>
</div>

<div class="dw-card">
<h2>{'What it has to do'|i18n( 'design/admin/setup/rad/datatype' )}</h2>
<span class="dw-meta">{'Each of these is a group of methods that only make sense together. Turning one on writes all of them, each with a note saying what the kernel calls it for. Leaving one off is the honest state for most of them in most datatypes.'|i18n( 'design/admin/setup/rad/datatype' )}</span>

{foreach $wizard_capabilities as $dw_cap}
    <label class="dw-pick{if $dw_cap.locked} is-locked{/if}">
        <input type="checkbox" name="Capabilities[]" value="{$dw_cap.key|wash}"{if $dw_cap.chosen} checked="checked"{/if}{if $dw_cap.locked} disabled="disabled"{/if} />
        {if $dw_cap.locked}<input type="hidden" name="Capabilities[]" value="{$dw_cap.key|wash}" />{/if}
        <span>
            <span class="dw-pick-label">{$dw_cap.label|i18n( 'design/admin/setup/rad/datatype' )|wash} <span class="dw-count">+{$dw_cap.count} {'methods'|i18n( 'design/admin/setup/rad/datatype' )}</span></span>
            <span class="dw-meta">{$dw_cap.summary|i18n( 'design/admin/setup/rad/datatype' )|wash} {$dw_cap.what|i18n( 'design/admin/setup/rad/datatype' )|wash}</span>
        </span>
    </label>
{/foreach}
</div>

<div class="dw-card">
<h2>{'Where the value lives'|i18n( 'design/admin/setup/rad/datatype' )}</h2>
<span class="dw-meta">{'An attribute is one row in ezcontentobject_attribute. That row has five columns a datatype may use and no others. A datatype needing more keeps a table of its own and puts the key in one of these.'|i18n( 'design/admin/setup/rad/datatype' )}</span>

{foreach $wizard_storage as $dw_column}
    <label class="dw-pick">
        <input type="checkbox" name="Storage[]" value="{$dw_column.key|wash}"{if $dw_column.chosen} checked="checked"{/if} />
        <span>
            <span class="dw-pick-label"><code>{$dw_column.label|wash}</code> <span class="dw-meta">{$dw_column.sql|wash}</span></span>
            <span class="dw-meta">{$dw_column.what|i18n( 'design/admin/setup/rad/datatype' )|wash}</span>
        </span>
    </label>
{/foreach}
</div>

<div class="dw-card">
<h2>{'Class settings'|i18n( 'design/admin/setup/rad/datatype' )}</h2>
<span class="dw-meta">{'Settings an editor chooses once, when the attribute is added to a content class: a maximum length, a default, a folder to browse from. Name the ones this datatype keeps and they become constants in the class and fields in the settings form. Leave a box empty to leave that column alone.'|i18n( 'design/admin/setup/rad/datatype' )}</span>

{foreach $wizard_class_settings as $dw_setting}
    <div class="dw-setting">
        <code>{$dw_setting.key|wash}</code>
        <input type="text" name="ClassSettingNames[{$dw_setting.key|wash}]" value="{$dw_setting.value|wash}" placeholder="{$dw_setting.kind|wash}" autocomplete="off" />
    </div>
{/foreach}
</div>

<div class="dw-card">
<h2>{'What goes in it'|i18n( 'design/admin/setup/rad/datatype' )}</h2>
{foreach $wizard_parts as $dw_key => $dw_part}
    <label class="dw-pick">
        <input type="checkbox" name="Parts[]" value="{$dw_key|wash}"{if $wizard_settings.parts[$dw_key]} checked="checked"{/if} />
        <span>
            <span class="dw-pick-label">{$dw_part.label|i18n( 'design/admin/setup/rad/datatype' )|wash}</span>
            <span class="dw-meta">{$dw_part.description|i18n( 'design/admin/setup/rad/datatype' )|wash}</span>
        </span>
    </label>
{/foreach}
</div>

</div>

<div class="dw-col">

<div class="dw-card">
<h2>{'What will be written'|i18n( 'design/admin/setup/rad/datatype' )} ({$wizard_method_count})</h2>
<span class="dw-meta">{'Every one of these is a method the kernel calls. Each is generated with this note beside it and returns something that leaves the system working; none of them does anything useful until it is written.'|i18n( 'design/admin/setup/rad/datatype' )}</span>

{def $dw_last=''}
{foreach $wizard_methods as $dw_method}
    {if ne( $dw_method.capability, $dw_last )}
        <span class="dw-group">{$dw_method.capability_label|i18n( 'design/admin/setup/rad/datatype' )|wash}</span>
        {set $dw_last=$dw_method.capability}
    {/if}
    <div class="dw-method">
        <code>{$dw_method.signature|wash}</code>
        <span class="dw-meta">{$dw_method.what|i18n( 'design/admin/setup/rad/datatype' )|wash}</span>
    </div>
{/foreach}
{undef $dw_last}
</div>

<div class="dw-card">
<h2>{'Switching it on'|i18n( 'design/admin/setup/rad/datatype' )}</h2>
<span class="dw-meta">{'Add this to settings/override/site.ini.append.php. The extension brings its own content.ini naming the datatype and its own design.ini naming the templates - without that second one the datatype works and draws nothing.'|i18n( 'design/admin/setup/rad/datatype' )}</span>
<pre>{$wizard_activation|wash}</pre>

<h3>{'Then'|i18n( 'design/admin/setup/rad/datatype' )}</h3>
<pre>php bin/php/ezpgenerateautoloads.php --extension={$wizard_settings.name|wash}
php bin/php/ezcache.php --clear-all</pre>
</div>

</div>
</div>

{if $wizard_file_count|gt( 0 )}
<div class="dw-card">
<h2>{'Every file, before it is written'|i18n( 'design/admin/setup/rad/datatype' )}</h2>
<span class="dw-meta">{$wizard_target|wash}</span>

<div class="dw-toolbar">
    <button type="button" class="dw-btn" id="dwOpenAll">{'Open all'|i18n( 'design/admin/setup/rad/datatype' )}</button>
    <button type="button" class="dw-btn" id="dwCloseAll">{'Close all'|i18n( 'design/admin/setup/rad/datatype' )}</button>
</div>

{foreach $wizard_files as $dw_file}
<div class="dw-file is-collapsed">
    <div class="dw-file-head">
        <span class="dw-file-toggle">+</span>
        <span class="dw-file-path">{$dw_file.path|wash}</span>
        <span class="dw-meta">{$dw_file.lines|wash} {'lines'|i18n( 'design/admin/setup/rad/datatype' )}, {$dw_file.bytes|wash} {'bytes'|i18n( 'design/admin/setup/rad/datatype' )}</span>
    </div>
    <pre>{$dw_file.contents|wash}</pre>
</div>
{/foreach}
</div>
{/if}

{* DESIGN: Content END *}</div></div></div>

<div class="controlbar">
{* DESIGN: Control bar START *}<div class="box-bc"><div class="box-ml">
<div class="block">
    {if $wizard_ready}
    <input class="defaultbutton" type="submit" name="CreateButton" value="{'Create in extension/'|i18n( 'design/admin/setup/rad/datatype' )}" />
    {else}
    <input class="button-disabled" type="submit" name="_Disabled" value="{'Create in extension/'|i18n( 'design/admin/setup/rad/datatype' )}" disabled="disabled" />
    {/if}

    {if $wizard_file_count|gt( 0 )}
        {if $wizard_can_archive}
    <input class="button" type="submit" name="DownloadButton" value="{'Download as zip'|i18n( 'design/admin/setup/rad/datatype' )}" />
        {/if}
    {/if}

    <input class="button" type="submit" name="PreviewButton" value="{'Refresh preview'|i18n( 'design/admin/setup/rad/datatype' )}" />
    <a class="dw-meta" href={'setup/rad'|ezurl}>{'Back to the RAD tools'|i18n( 'design/admin/setup/rad/datatype' )}</a>
</div>
{* DESIGN: Control bar END *}</div></div>
</div>

</div>
</form>

{literal}
<script type="text/javascript">
( function () {
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
        if ( divs[i].className.indexOf( 'dw-file' ) === 0 )
            files.push( divs[i] );

    for ( i = 0; i < files.length; i++ )
        ( function ( panel ) {
            var head = panel.getElementsByTagName( 'div' )[0];
            if ( !head ) return;
            head.onclick = function () {
                fold( panel, panel.className.indexOf( 'is-collapsed' ) !== -1 );
                return false;
            };
        } )( files[i] );

    function every( open ) { for ( var j = 0; j < files.length; j++ ) fold( files[j], open ); }
    var openAll = document.getElementById( 'dwOpenAll' ), closeAll = document.getElementById( 'dwCloseAll' );
    if ( openAll )  openAll.onclick  = function () { every( true ); return false; };
    if ( closeAll ) closeAll.onclick = function () { every( false ); return false; };
} )();
</script>
{/literal}
