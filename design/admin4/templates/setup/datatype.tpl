{* The datatype wizard.

   A datatype is the largest thing this system asks anybody to write. This page
   asks what it has to do rather than what it is called, and writes only the
   methods that answer to that. *}


<form method="post" action={'setup/datatype'|ezurl} name="DatatypeWizard">
<input type="hidden" name="Submitted" value="1" />

<div class="context-block a4-page a4-setup-wz">

{* DESIGN: Header START *}<div class="box-header"><div class="box-ml">
<h1 class="context-title">{'Datatype wizard'|i18n( 'design/admin/setup/rad/datatype' )}</h1>
{* DESIGN: Mainline *}<div class="header-mainline"></div>
{* DESIGN: Header END *}</div></div>

{* DESIGN: Content START *}<div class="box-ml"><div class="box-mr"><div class="box-content">

<div class="context-attributes">
<p>{'A datatype is a kind of value a content class attribute can hold, with its own editing field, its own validation, its own storage and its own display. eZDataType declares over ninety methods; which of them a datatype needs depends entirely on what it is for. Say what it has to do below and only those are written.'|i18n( 'design/admin/setup/rad/datatype' )}</p>
</div>

{foreach $wizard_feedback as $dw_note}
<div class="a4-notice {if $dw_note.ok}is-ok{else}is-bad{/if}">
    <strong>{if $dw_note.ok}&#10003;{else}!{/if}</strong><span>{$dw_note.message|wash}</span>
</div>
{/foreach}

{foreach $wizard_problems as $dw_problem}
<div class="a4-notice is-bad"><span>{$dw_problem|wash}</span></div>
{/foreach}

{if $wizard_written|count|gt( 0 )}
<div class="a4-notice is-ok"><span>{'Written to %target. Switch it on with the lines below, regenerate the extension autoloads, and clear the caches.'|i18n( 'design/admin/setup/rad/datatype',, hash( '%target', $wizard_target ) )}</span></div>
{/if}

{if $wizard_can_write|not}
<div class="a4-notice is-info"><span>{'The web server cannot write into extension/, so this page can only hand you an archive.'|i18n( 'design/admin/setup/rad/datatype' )}</span></div>
{/if}

<div class="a4-setup-wz-summary">
    <span>{'%methods methods'|i18n( 'design/admin/setup/rad/datatype',, hash( '%methods', concat( '<b>', $wizard_method_count, '</b>' ) ) )}</span>
    <span><b>{$wizard_file_count}</b> {'files'|i18n( 'design/admin/setup/rad/datatype' )}</span>
    <span class="a4-meta">{'%count datatypes are already installed on this site.'|i18n( 'design/admin/setup/rad/datatype',, hash( '%count', $wizard_existing|count ) )}</span>
</div>

<div class="a4-setup-wz-grid">
<div class="a4-setup-wz-col">

<div class="a4-card">
<h2>{'The datatype'|i18n( 'design/admin/setup/rad/datatype' )}</h2>
<span class="a4-meta">{'The identifier goes in the database against every attribute of this type, in every ini that mentions it, and in the name of the file the kernel looks for. It cannot be changed once content exists.'|i18n( 'design/admin/setup/rad/datatype' )}</span>

<div class="a4-setup-wz-row">
    <div class="a4-field">
        <label for="dwName">{'Extension name'|i18n( 'design/admin/setup/rad/datatype' )}</label>
        <input type="text" id="dwName" name="name" value="{$wizard_settings.name|wash}" placeholder="my_datatype" autocomplete="off" />
    </div>
    <div class="a4-field">
        <label for="dwType">{'Datatype identifier'|i18n( 'design/admin/setup/rad/datatype' )}</label>
        <input type="text" id="dwType" name="type" value="{$wizard_settings.type|wash}" placeholder="mytype" autocomplete="off" />
        <span class="a4-help">{'Lower case letters and digits only.'|i18n( 'design/admin/setup/rad/datatype' )}</span>
    </div>
    <div class="a4-field">
        <label for="dwClass">{'Class name'|i18n( 'design/admin/setup/rad/datatype' )}</label>
        <input type="text" id="dwClass" name="class" value="{$wizard_settings.class|wash}" autocomplete="off" />
    </div>
</div>

<div class="a4-setup-wz-row">
    <div class="a4-field">
        <label for="dwTitle">{'Name in the class editor'|i18n( 'design/admin/setup/rad/datatype' )}</label>
        <input type="text" id="dwTitle" name="title" value="{$wizard_settings.title|wash}" />
    </div>
    <div class="a4-field">
        <label for="dwGroup">{'Group it is listed under'|i18n( 'design/admin/setup/rad/datatype' )}</label>
        <input type="text" id="dwGroup" name="group" value="{$wizard_settings.group|wash}" />
    </div>
</div>

<div class="a4-field">
    <label for="dwSummary">{'Summary'|i18n( 'design/admin/setup/rad/datatype' )}</label>
    <input type="text" id="dwSummary" name="summary" value="{$wizard_settings.summary|wash}" />
</div>

<div class="a4-setup-wz-row">
    <div class="a4-field">
        <label for="dwAuthor">{'Author'|i18n( 'design/admin/setup/rad/datatype' )}</label>
        <input type="text" id="dwAuthor" name="author" value="{$wizard_settings.author|wash}" />
    </div>
    <div class="a4-field">
        <label for="dwVendor">{'Composer vendor'|i18n( 'design/admin/setup/rad/datatype' )}</label>
        <input type="text" id="dwVendor" name="vendor" value="{$wizard_settings.vendor|wash}" />
    </div>
    <div class="a4-field">
        <label for="dwVersion">{'Version'|i18n( 'design/admin/setup/rad/datatype' )}</label>
        <input type="text" id="dwVersion" name="version" value="{$wizard_settings.version|wash}" />
    </div>
</div>

<div class="a4-field">
    <label for="dwLicence">{'Licence'|i18n( 'design/admin/setup/rad/datatype' )}</label>
    {include uri='design:setup/rad_licence_select.tpl' select_id='dwLicence' selected=$wizard_settings.licence groups=$wizard_licences}
</div>
</div>

<div class="a4-card">
<h2>{'What it has to do'|i18n( 'design/admin/setup/rad/datatype' )}</h2>
<span class="a4-meta">{'Each of these is a group of methods that only make sense together. Turning one on writes all of them, each with a note saying what the kernel calls it for. Leaving one off is the honest state for most of them in most datatypes.'|i18n( 'design/admin/setup/rad/datatype' )}</span>

{foreach $wizard_capabilities as $dw_cap}
    <label class="a4-setup-wz-pick{if $dw_cap.locked} is-locked{/if}">
        <input type="checkbox" name="Capabilities[]" value="{$dw_cap.key|wash}"{if $dw_cap.chosen} checked="checked"{/if}{if $dw_cap.locked} disabled="disabled"{/if} />
        {if $dw_cap.locked}<input type="hidden" name="Capabilities[]" value="{$dw_cap.key|wash}" />{/if}
        <span>
            <span class="a4-setup-wz-pick-label">{$dw_cap.label|i18n( 'design/admin/setup/rad/datatype' )|wash} <span class="a4-setup-wz-count">{'+%count methods'|i18n( 'design/admin/setup/rad/datatype',, hash( '%count', $dw_cap.count ) )}</span></span>
            <span class="a4-meta">{$dw_cap.summary|i18n( 'design/admin/setup/rad/datatype' )|wash} {$dw_cap.what|i18n( 'design/admin/setup/rad/datatype' )|wash}</span>
        </span>
    </label>
{/foreach}
</div>

<div class="a4-card">
<h2>{'Where the value lives'|i18n( 'design/admin/setup/rad/datatype' )}</h2>
<span class="a4-meta">{'An attribute is one row in ezcontentobject_attribute. That row has five columns a datatype may use and no others. A datatype needing more keeps a table of its own and puts the key in one of these.'|i18n( 'design/admin/setup/rad/datatype' )}</span>

{foreach $wizard_storage as $dw_column}
    <label class="a4-setup-wz-pick">
        <input type="checkbox" name="Storage[]" value="{$dw_column.key|wash}"{if $dw_column.chosen} checked="checked"{/if} />
        <span>
            <span class="a4-setup-wz-pick-label"><code>{$dw_column.label|wash}</code> <span class="a4-meta">{$dw_column.sql|wash}</span></span>
            <span class="a4-meta">{$dw_column.what|i18n( 'design/admin/setup/rad/datatype' )|wash}</span>
        </span>
    </label>
{/foreach}
</div>

<div class="a4-card">
<h2>{'Class settings'|i18n( 'design/admin/setup/rad/datatype' )}</h2>
<span class="a4-meta">{'Settings an editor chooses once, when the attribute is added to a content class: a maximum length, a default, a folder to browse from. Name the ones this datatype keeps and they become constants in the class and fields in the settings form. Leave a box empty to leave that column alone.'|i18n( 'design/admin/setup/rad/datatype' )}</span>

{foreach $wizard_class_settings as $dw_setting}
    <div class="a4-setup-wz-setting">
        <code>{$dw_setting.key|wash}</code>
        <input type="text" name="ClassSettingNames[{$dw_setting.key|wash}]" value="{$dw_setting.value|wash}" placeholder="{$dw_setting.kind|wash}" autocomplete="off" />
    </div>
{/foreach}
</div>

<div class="a4-card">
<h2>{'What goes in it'|i18n( 'design/admin/setup/rad/datatype' )}</h2>
{foreach $wizard_parts as $dw_key => $dw_part}
    <label class="a4-setup-wz-pick">
        <input type="checkbox" name="Parts[]" value="{$dw_key|wash}"{if $wizard_settings.parts[$dw_key]} checked="checked"{/if} />
        <span>
            <span class="a4-setup-wz-pick-label">{$dw_part.label|i18n( 'design/admin/setup/rad/datatype' )|wash}</span>
            <span class="a4-meta">{$dw_part.description|i18n( 'design/admin/setup/rad/datatype' )|wash}</span>
        </span>
    </label>
{/foreach}
</div>

</div>

<div class="a4-setup-wz-col">

<div class="a4-card">
<h2>{'What will be written'|i18n( 'design/admin/setup/rad/datatype' )} ({$wizard_method_count})</h2>
<span class="a4-meta">{'Every one of these is a method the kernel calls. Each is generated with this note beside it and returns something that leaves the system working; none of them does anything useful until it is written.'|i18n( 'design/admin/setup/rad/datatype' )}</span>

{def $dw_last=''}
{foreach $wizard_methods as $dw_method}
    {if ne( $dw_method.capability, $dw_last )}
        <span class="a4-setup-wz-group">{$dw_method.capability_label|i18n( 'design/admin/setup/rad/datatype' )|wash}</span>
        {set $dw_last=$dw_method.capability}
    {/if}
    <div class="a4-setup-wz-method">
        <code>{$dw_method.signature|wash}</code>
        <span class="a4-meta">{$dw_method.what|i18n( 'design/admin/setup/rad/datatype' )|wash}</span>
    </div>
{/foreach}
{undef $dw_last}
</div>

<div class="a4-card">
<h2>{'Switching it on'|i18n( 'design/admin/setup/rad/datatype' )}</h2>
<span class="a4-meta">{'Add this to settings/override/site.ini.append.php. The extension brings its own content.ini naming the datatype and its own design.ini naming the templates - without that second one the datatype works and draws nothing.'|i18n( 'design/admin/setup/rad/datatype' )}</span>
<pre>{$wizard_activation|wash}</pre>

<h3>{'Then'|i18n( 'design/admin/setup/rad/datatype' )}</h3>
<pre>php bin/php/ezpgenerateautoloads.php --extension={$wizard_settings.name|wash}
php bin/php/ezcache.php --clear-all</pre>
</div>

</div>
</div>

{if $wizard_file_count|gt( 0 )}
<div class="a4-card">
<h2>{'Every file, before it is written'|i18n( 'design/admin/setup/rad/datatype' )}</h2>
<span class="a4-meta">{$wizard_target|wash}</span>

<div class="a4-actions">
    <button type="button" class="a4-btn a4-btn-small" id="dwOpenAll">{'Open all'|i18n( 'design/admin/setup/rad/datatype' )}</button>
    <button type="button" class="a4-btn a4-btn-small" id="dwCloseAll">{'Close all'|i18n( 'design/admin/setup/rad/datatype' )}</button>
</div>

{foreach $wizard_files as $dw_file}
<div class="a4-setup-wz-file is-collapsed">
    <div class="a4-setup-wz-file-head">
        <span class="a4-setup-wz-file-toggle">+</span>
        <span class="a4-setup-wz-file-path">{$dw_file.path|wash}</span>
        <span class="a4-meta">{'%lines lines, %bytes bytes'|i18n( 'design/admin/setup/rad/datatype',, hash( '%lines', $dw_file.lines|wash, '%bytes', $dw_file.bytes|wash ) )}</span>
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
    <a class="a4-meta" href={'setup/rad'|ezurl}>{'Back to the RAD tools'|i18n( 'design/admin/setup/rad/datatype' )}</a>
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
        if ( divs[i].className.indexOf( 'a4-setup-wz-file' ) === 0 )
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
