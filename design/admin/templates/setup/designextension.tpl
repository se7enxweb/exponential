{* The design extension wizard.

   Everything it will write is worked out before anything is written, so the
   page can show the shape on disk and the contents of every file first. The
   form posts to itself; the two buttons at the foot are the only things that
   do anything. *}


<form method="post" action={'setup/designextension'|ezurl} name="DesignExtensionWizard">
<input type="hidden" name="Submitted" value="1" />

<div class="context-block a4-page a4-setup-wz">

{* DESIGN: Header START *}<div class="box-header"><div class="box-ml">
<h1 class="context-title">{'Design extension wizard'|i18n( 'design/admin/setup/rad/designextension' )}</h1>
{* DESIGN: Mainline *}<div class="header-mainline"></div>
{* DESIGN: Header END *}</div></div>

{* DESIGN: Content START *}<div class="box-ml"><div class="box-mr"><div class="box-content">

<div class="context-attributes">
<p>{'A design extension holds the templates, stylesheets, images and settings a site is drawn with, kept apart from the kernel so an upgrade cannot walk over them. Describe the one you want below; nothing is written until you ask for it.'|i18n( 'design/admin/setup/rad/designextension' )}</p>
</div>

{foreach $wizard_feedback as $dew_note}
<div class="a4-notice {if $dew_note.ok}is-ok{else}is-bad{/if}">
    <strong>{if $dew_note.ok}&#10003;{else}!{/if}</strong>
    <span>{$dew_note.message|wash}</span>
</div>
{/foreach}

{foreach $wizard_problems as $dew_problem}
<div class="a4-notice is-bad"><strong>!</strong><span>{$dew_problem|wash}</span></div>
{/foreach}

{if $wizard_written|count|gt( 0 )}
<div class="a4-notice is-info">
    <strong>&#10003;</strong>
    <span>{'Written to %target. Switch it on with the lines below, then clear the caches.'|i18n( 'design/admin/setup/rad/designextension',, hash( '%target', $wizard_target ) )}</span>
</div>
{/if}

{if $wizard_can_write|not}
<div class="a4-notice is-info">
    <strong>i</strong>
    <span>{'The web server cannot write into extension/, so this page can only hand you an archive. That is the usual arrangement on a server worth having.'|i18n( 'design/admin/setup/rad/designextension' )}</span>
</div>
{/if}

<div class="a4-setup-wz-grid">

{* ------------------------------------------------------------ the form -- *}
<div class="a4-setup-wz-col">

<div class="a4-card">
<h2>{'What it is'|i18n( 'design/admin/setup/rad/designextension' )}</h2>
<span class="a4-meta">{'The name is the directory, the design and the value in design.ini, so it is lower case letters, digits and underscores.'|i18n( 'design/admin/setup/rad/designextension' )}</span>

<div class="a4-field">
    <label for="dewName">{'Extension name'|i18n( 'design/admin/setup/rad/designextension' )}</label>
    <input type="text" id="dewName" name="name" value="{$wizard_settings.name|wash}" placeholder="my_site_design" autocomplete="off" />
    <span class="a4-help" data-named="{'Becomes extension/%name and design/%name.'|i18n( 'design/admin/setup/rad/designextension' )|wash}">{'Becomes extension/<name> and design/<name>.'|i18n( 'design/admin/setup/rad/designextension' )|wash}</span>
</div>

<div class="a4-field">
    <label for="dewTitle">{'Title'|i18n( 'design/admin/setup/rad/designextension' )}</label>
    <input type="text" id="dewTitle" name="title" value="{$wizard_settings.title|wash}" placeholder="{'My Site Design'|i18n( 'design/admin/setup/rad/designextension' )}" />
    <span class="a4-help">{'What the admin interface calls it. Left empty, it is made from the name.'|i18n( 'design/admin/setup/rad/designextension' )}</span>
</div>

<div class="a4-field">
    <label for="dewSummary">{'Summary'|i18n( 'design/admin/setup/rad/designextension' )}</label>
    <input type="text" id="dewSummary" name="summary" value="{$wizard_settings.summary|wash}" />
</div>

<div class="a4-setup-wz-row">
<div class="a4-field">
    <label for="dewAuthor">{'Author'|i18n( 'design/admin/setup/rad/designextension' )}</label>
    <input type="text" id="dewAuthor" name="author" value="{$wizard_settings.author|wash}" />
</div>
<div class="a4-field">
    <label for="dewVendor">{'Composer vendor'|i18n( 'design/admin/setup/rad/designextension' )}</label>
    <input type="text" id="dewVendor" name="vendor" value="{$wizard_settings.vendor|wash}" />
</div>
<div class="a4-field">
    <label for="dewVersion">{'Version'|i18n( 'design/admin/setup/rad/designextension' )}</label>
    <input type="text" id="dewVersion" name="version" value="{$wizard_settings.version|wash}" />
</div>
</div>

<div class="a4-setup-wz-row">
<div class="a4-field">
    <label for="dewLicence">{'Licence'|i18n( 'design/admin/setup/rad/designextension' )}</label>
    {include uri='design:setup/rad_licence_select.tpl' select_id='dewLicence' selected=$wizard_settings.licence groups=$wizard_licences}
</div>
<div class="a4-field">
    <label for="dewBase">{'Falls back on'|i18n( 'design/admin/setup/rad/designextension' )}</label>
    <select id="dewBase" name="base_design">
    {foreach $wizard_base_designs as $dew_key => $dew_label}
        <option value="{$dew_key|wash}"{if eq( $wizard_settings.base_design, $dew_key )} selected="selected"{/if}>{$dew_label|wash}</option>
    {/foreach}
    </select>
</div>
</div>

<div class="a4-field">
    <label for="dewSiteaccess">{'Siteaccess'|i18n( 'design/admin/setup/rad/designextension' )}</label>
    <select id="dewSiteaccess" name="siteaccess">
        <option value="">{'None - just add the design to the chain'|i18n( 'design/admin/setup/rad/designextension' )}</option>
    {foreach $wizard_siteaccess_list as $dew_sa}
        <option value="{$dew_sa|wash}"{if eq( $wizard_settings.siteaccess, $dew_sa )} selected="selected"{/if}>{$dew_sa|wash}</option>
    {/foreach}
    </select>
    <span class="a4-help">{'Used only when siteaccess settings are ticked below.'|i18n( 'design/admin/setup/rad/designextension' )}</span>
</div>
</div>

<div class="a4-card">
<h2>{'What goes in it'|i18n( 'design/admin/setup/rad/designextension' )}</h2>
<span class="a4-meta">{'Everything here is a starting point meant to be edited, not a black box.'|i18n( 'design/admin/setup/rad/designextension' )}</span>

<div class="a4-actions">
    <button type="button" class="a4-btn a4-btn-small" id="dewAll">{'Tick all'|i18n( 'design/admin/setup/rad/designextension' )}</button>
    <button type="button" class="a4-btn a4-btn-small" id="dewNone">{'Tick none'|i18n( 'design/admin/setup/rad/designextension' )}</button>
    <button type="submit" class="a4-btn a4-btn-small" name="PreviewButton" value="1">{'Refresh preview'|i18n( 'design/admin/setup/rad/designextension' )}</button>
</div>

<div class="a4-setup-wz-parts">
{foreach $wizard_parts as $dew_key => $dew_part}
    <label class="a4-setup-wz-pick">
        <input type="checkbox" name="Parts[]" value="{$dew_key|wash}"{if $wizard_settings.parts[$dew_key]} checked="checked"{/if} />
        <span>
            <span class="a4-setup-wz-pick-label">{$dew_part.label|i18n( 'design/admin/setup/rad/designextension' )}</span>
            <span class="a4-meta">{$dew_part.description|i18n( 'design/admin/setup/rad/designextension' )}</span>
        </span>
    </label>
{/foreach}
</div>
</div>

</div>

{* --------------------------------------------------------- the preview -- *}
<div class="a4-setup-wz-col">

<div class="a4-card">
<h2>{'What it will write'|i18n( 'design/admin/setup/rad/designextension' )}</h2>
<span class="a4-meta">{$wizard_target|wash}</span>

<div class="a4-setup-wz-summary">
    <span><b>{$wizard_file_count}</b><span class="a4-meta">{'files'|i18n( 'design/admin/setup/rad/designextension' )}</span></span>
    <span><b>{$wizard_directories|count}</b><span class="a4-meta">{'directories'|i18n( 'design/admin/setup/rad/designextension' )}</span></span>
</div>

{if $wizard_file_count|gt( 0 )}
<ul class="a4-setup-wz-tree">
{foreach $wizard_files as $dew_index => $dew_file}
    <li class="a4-setup-wz-depth-{min( 10, $dew_file.depth )}">
        <a href="#dew-file-{$dew_index}">{$dew_file.basename|wash}</a>
        <span class="a4-setup-wz-size">{'%lines lines'|i18n( 'design/admin/setup/rad/designextension',, hash( '%lines', $dew_file.lines ) )}</span>
        {if $dew_file.depth|gt( 0 )}<span class="a4-setup-wz-dir">&mdash; {$dew_file.path|wash}</span>{/if}
    </li>
{/foreach}
</ul>
{else}
<p class="a4-meta">{'Name it, and the file list appears here.'|i18n( 'design/admin/setup/rad/designextension' )}</p>
{/if}
</div>

<div class="a4-card">
<h2>{'Switching it on'|i18n( 'design/admin/setup/rad/designextension' )}</h2>
<span class="a4-meta">{'Add this to settings/override/site.ini.append.php, then clear the caches.'|i18n( 'design/admin/setup/rad/designextension' )}</span>
<pre class="a4-pre">{$wizard_activation|wash}</pre>
</div>

</div>
</div>

{* ------------------------------------------------------- the files ------ *}
{if $wizard_file_count|gt( 0 )}
<div class="a4-card">
<h2>{'Every file, before it is written'|i18n( 'design/admin/setup/rad/designextension' )}</h2>
<span class="a4-meta">{'Click a heading to read one. Nothing here has been written yet.'|i18n( 'design/admin/setup/rad/designextension' )}</span>

<div class="a4-actions">
    <button type="button" class="a4-btn a4-btn-small" id="dewOpenAll">{'Open all'|i18n( 'design/admin/setup/rad/designextension' )}</button>
    <button type="button" class="a4-btn a4-btn-small" id="dewCloseAll">{'Close all'|i18n( 'design/admin/setup/rad/designextension' )}</button>
</div>

{foreach $wizard_files as $dew_index => $dew_file}
<div class="a4-setup-wz-file is-collapsed" id="dew-file-{$dew_index}">
    <div class="a4-setup-wz-file-head">
        <span class="a4-setup-wz-file-toggle">+</span>
        <span class="a4-setup-wz-file-path">{$dew_file.path|wash}</span>
        <span class="a4-meta">{'%lines lines, %bytes bytes'|i18n( 'design/admin/setup/rad/designextension',, hash( '%lines', $dew_file.lines, '%bytes', $dew_file.bytes ) )}</span>
    </div>
    <pre>{$dew_file.contents|wash}</pre>
</div>
{/foreach}
</div>
{/if}

{* DESIGN: Content END *}</div></div></div>

<div class="controlbar">
{* DESIGN: Control bar START *}<div class="box-bc"><div class="box-ml">
<div class="block a4-actions">
    {if $wizard_ready}
    <input class="defaultbutton" type="submit" name="CreateButton" value="{'Create in extension/'|i18n( 'design/admin/setup/rad/designextension' )}" />
    {else}
    <input class="button-disabled" type="submit" name="_Disabled" value="{'Create in extension/'|i18n( 'design/admin/setup/rad/designextension' )}" disabled="disabled" />
    {/if}

    {if $wizard_file_count|gt( 0 )}
        {if $wizard_can_archive}
    <input class="button" type="submit" name="DownloadButton" value="{'Download as zip'|i18n( 'design/admin/setup/rad/designextension' )}" />
        {/if}
    {/if}

    <input class="button" type="submit" name="PreviewButton" value="{'Refresh preview'|i18n( 'design/admin/setup/rad/designextension' )}" />
    <a class="a4-meta" href={'setup/rad'|ezurl}>{'Back to the RAD tools'|i18n( 'design/admin/setup/rad/designextension' )}</a>
</div>
{* DESIGN: Control bar END *}</div></div>
</div>

</div>
</form>

{literal}
<script type="text/javascript">
( function () {
    // Folding a file open and shut. The markup is already complete; this only
    // saves scrolling past a thousand lines to reach the next heading.
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

    var panels = document.getElementsByTagName( 'div' ), files = [], i;
    for ( i = 0; i < panels.length; i++ )
        if ( panels[i].className.indexOf( 'a4-setup-wz-file' ) === 0 )
            files.push( panels[i] );

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

    function every( open )
    {
        for ( var j = 0; j < files.length; j++ )
            fold( files[j], open );
    }

    var openAll = document.getElementById( 'dewOpenAll' ),
        closeAll = document.getElementById( 'dewCloseAll' );
    if ( openAll )  openAll.onclick  = function () { every( true ); return false; };
    if ( closeAll ) closeAll.onclick = function () { every( false ); return false; };

    // Ticking all or none of the parts.
    function boxes()
    {
        var inputs = document.getElementsByTagName( 'input' ), found = [], k;
        for ( k = 0; k < inputs.length; k++ )
            if ( inputs[k].type === 'checkbox' && inputs[k].name === 'Parts[]' )
                found.push( inputs[k] );
        return found;
    }

    function tick( on )
    {
        var list = boxes(), k;
        for ( k = 0; k < list.length; k++ )
            list[k].checked = on;
    }

    var all = document.getElementById( 'dewAll' ), none = document.getElementById( 'dewNone' );
    if ( all )  all.onclick  = function () { tick( true ); return false; };
    if ( none ) none.onclick = function () { tick( false ); return false; };

    // The name drives the directory, so show what it will become as it is typed.
    var name = document.getElementById( 'dewName' );
    if ( name )
    {
        name.onkeyup = function () {
            var clean = name.value.toLowerCase().replace( /[^a-z0-9_]+/g, '_' ).replace( /^_+|_+$/g, '' ),
                hint = name.parentNode.getElementsByTagName( 'span' )[0];
            if ( hint ) {
                if ( !hint.getAttribute( 'data-empty' ) )
                    hint.setAttribute( 'data-empty', hint.textContent );
                hint.textContent = clean === ''
                    ? hint.getAttribute( 'data-empty' )
                    : hint.getAttribute( 'data-named' ).split( '%name' ).join( clean );
            }
        };
    }
} )();
</script>
{/literal}
