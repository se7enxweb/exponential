{* The design extension wizard.

   Everything it will write is worked out before anything is written, so the
   page can show the shape on disk and the contents of every file first. The
   form posts to itself; the two buttons at the foot are the only things that
   do anything. *}

{literal}
<style type="text/css">
.exp-dew {
    --dew-ink: #1c1c1e;
    --dew-muted: #6a6a72;
    --dew-line: #e2e2e6;
    --dew-accent: #2d6cdf;
    --dew-ok: #1f8a4c;
    --dew-bad: #b4232c;
    --dew-gap: 1.6rem;
    color: var(--dew-ink);
}
.exp-dew h2 { font-size: 1.05rem; margin: 0; }
.exp-dew .dew-meta { color: var(--dew-muted); font-size: .92em; }
.exp-dew code, .exp-dew pre {
    font-family: Menlo, Consolas, "DejaVu Sans Mono", monospace;
}

.dew-head { display: flex; flex-wrap: wrap; align-items: baseline; gap: .8rem; padding-bottom: .4rem; }

.dew-note {
    display: flex; gap: .6rem; align-items: flex-start;
    border: 1px solid var(--dew-line); border-left-width: 4px; border-radius: 6px;
    padding: .7rem .9rem; margin: 0 0 .8rem 0; background: #fcfcfd;
}
.dew-note.is-ok  { border-left-color: var(--dew-ok); }
.dew-note.is-bad { border-left-color: var(--dew-bad); }
.dew-note.is-info { border-left-color: var(--dew-accent); }

.dew-grid { display: flex; flex-wrap: wrap; gap: var(--dew-gap); align-items: flex-start; }
.dew-col { flex: 1 1 22rem; min-width: 0; }

.dew-card {
    border: 1px solid var(--dew-line); border-radius: 8px; background: #fff;
    padding: 1rem 1.1rem; margin: 0 0 var(--dew-gap) 0;
}
.dew-card > h2 { margin-bottom: .2rem; }
.dew-card > .dew-meta { display: block; padding-bottom: .9rem; }

.dew-field { display: flex; flex-direction: column; gap: .3rem; padding-bottom: .9rem; }
.dew-field label { font-size: .82em; color: var(--dew-muted); text-transform: uppercase; letter-spacing: .04em; }
.dew-field input[type=text], .dew-field select, .dew-field textarea {
    border: 1px solid var(--dew-line); border-radius: 6px; padding: .45rem .55rem;
    font: inherit; background: #fff; width: 100%; box-sizing: border-box;
}
.dew-field .dew-hint { font-size: .85em; color: var(--dew-muted); }
.dew-row { display: flex; flex-wrap: wrap; gap: .9rem; }
.dew-row > .dew-field { flex: 1 1 10rem; }

.dew-parts { display: flex; flex-direction: column; gap: .1rem; }
.dew-part {
    display: flex; gap: .6rem; align-items: flex-start;
    padding: .5rem .55rem; border-radius: 6px;
}
.dew-part:hover { background: #f6f7f9; }
.dew-part input { margin-top: .25rem; }
.dew-part .dew-part-label { font-weight: 600; }
.dew-part .dew-meta { display: block; }

.dew-toolbar { display: flex; flex-wrap: wrap; gap: .5rem; padding: .2rem 0 .9rem 0; }
.dew-btn {
    border: 1px solid var(--dew-line); background: #f4f4f5; border-radius: 6px;
    padding: .3rem .7rem; font: inherit; cursor: pointer; color: inherit;
}
.dew-btn:hover { border-color: #9a9aa0; }

.dew-tree { margin: 0; padding: 0; list-style: none; font-size: .95em; }
.dew-tree li { padding: .12rem 0; }
.dew-tree .dew-dir { color: var(--dew-muted); }
.dew-tree a { color: var(--dew-accent); text-decoration: none; }
.dew-tree a:hover { text-decoration: underline; }
.dew-tree .dew-size { color: var(--dew-muted); font-size: .88em; }

.dew-file { border: 1px solid var(--dew-line); border-radius: 8px; margin: 0 0 .7rem 0; background: #fff; }
.dew-file-head {
    display: flex; flex-wrap: wrap; align-items: center; gap: .6rem;
    padding: .55rem .8rem; cursor: pointer;
}
.dew-file-head:hover { background: #f6f7f9; }
.dew-file-toggle {
    display: inline-flex; align-items: center; justify-content: center;
    width: 1.25rem; height: 1.25rem; border: 1px solid var(--dew-line);
    border-radius: 4px; font-weight: 700; background: #f4f4f5; line-height: 1;
}
.dew-file-path { font-weight: 600; }
.dew-file pre {
    margin: 0; padding: .8rem 1rem; border-top: 1px solid var(--dew-line);
    background: #fbfbfc; overflow-x: auto; font-size: .88em; line-height: 1.5;
    white-space: pre; border-radius: 0 0 8px 8px;
}
.dew-file.is-collapsed pre { display: none; }

.dew-actions {
    display: flex; flex-wrap: wrap; gap: .6rem; align-items: center;
    padding-top: .4rem;
}
.dew-summary { display: flex; flex-wrap: wrap; gap: 1.4rem; padding-bottom: .9rem; }
.dew-summary b { display: block; font-size: 1.3rem; line-height: 1.2; }
</style>
{/literal}

<form method="post" action={'setup/designextension'|ezurl} name="DesignExtensionWizard">
<input type="hidden" name="Submitted" value="1" />

<div class="context-block exp-dew">

{* DESIGN: Header START *}<div class="box-header"><div class="box-ml">
<h1 class="context-title">{'Design extension wizard'|i18n( 'design/admin/setup/rad/designextension' )}</h1>
{* DESIGN: Mainline *}<div class="header-mainline"></div>
{* DESIGN: Header END *}</div></div>

{* DESIGN: Content START *}<div class="box-ml"><div class="box-mr"><div class="box-content">

<div class="context-attributes">
<p>{'A design extension holds the templates, stylesheets, images and settings a site is drawn with, kept apart from the kernel so an upgrade cannot walk over them. Describe the one you want below; nothing is written until you ask for it.'|i18n( 'design/admin/setup/rad/designextension' )}</p>
</div>

{foreach $wizard_feedback as $dew_note}
<div class="dew-note {if $dew_note.ok}is-ok{else}is-bad{/if}">
    <strong>{if $dew_note.ok}&#10003;{else}!{/if}</strong>
    <span>{$dew_note.message|wash}</span>
</div>
{/foreach}

{foreach $wizard_problems as $dew_problem}
<div class="dew-note is-bad"><strong>!</strong><span>{$dew_problem|wash}</span></div>
{/foreach}

{if $wizard_written|count|gt( 0 )}
<div class="dew-note is-info">
    <strong>&#10003;</strong>
    <span>{'Written to %target. Switch it on with the lines below, then clear the caches.'|i18n( 'design/admin/setup/rad/designextension',, hash( '%target', $wizard_target ) )}</span>
</div>
{/if}

{if $wizard_can_write|not}
<div class="dew-note is-info">
    <strong>i</strong>
    <span>{'The web server cannot write into extension/, so this page can only hand you an archive. That is the usual arrangement on a server worth having.'|i18n( 'design/admin/setup/rad/designextension' )}</span>
</div>
{/if}

<div class="dew-grid">

{* ------------------------------------------------------------ the form -- *}
<div class="dew-col">

<div class="dew-card">
<h2>{'What it is'|i18n( 'design/admin/setup/rad/designextension' )}</h2>
<span class="dew-meta">{'The name is the directory, the design and the value in design.ini, so it is lower case letters, digits and underscores.'|i18n( 'design/admin/setup/rad/designextension' )}</span>

<div class="dew-field">
    <label for="dewName">{'Extension name'|i18n( 'design/admin/setup/rad/designextension' )}</label>
    <input type="text" id="dewName" name="name" value="{$wizard_settings.name|wash}" placeholder="my_site_design" autocomplete="off" />
    <span class="dew-hint">{'Becomes extension/<name> and design/<name>.'|i18n( 'design/admin/setup/rad/designextension' )|wash}</span>
</div>

<div class="dew-field">
    <label for="dewTitle">{'Title'|i18n( 'design/admin/setup/rad/designextension' )}</label>
    <input type="text" id="dewTitle" name="title" value="{$wizard_settings.title|wash}" placeholder="My Site Design" />
    <span class="dew-hint">{'What the admin interface calls it. Left empty, it is made from the name.'|i18n( 'design/admin/setup/rad/designextension' )}</span>
</div>

<div class="dew-field">
    <label for="dewSummary">{'Summary'|i18n( 'design/admin/setup/rad/designextension' )}</label>
    <input type="text" id="dewSummary" name="summary" value="{$wizard_settings.summary|wash}" />
</div>

<div class="dew-row">
<div class="dew-field">
    <label for="dewAuthor">{'Author'|i18n( 'design/admin/setup/rad/designextension' )}</label>
    <input type="text" id="dewAuthor" name="author" value="{$wizard_settings.author|wash}" />
</div>
<div class="dew-field">
    <label for="dewVendor">{'Composer vendor'|i18n( 'design/admin/setup/rad/designextension' )}</label>
    <input type="text" id="dewVendor" name="vendor" value="{$wizard_settings.vendor|wash}" />
</div>
<div class="dew-field">
    <label for="dewVersion">{'Version'|i18n( 'design/admin/setup/rad/designextension' )}</label>
    <input type="text" id="dewVersion" name="version" value="{$wizard_settings.version|wash}" />
</div>
</div>

<div class="dew-row">
<div class="dew-field">
    <label for="dewLicence">{'Licence'|i18n( 'design/admin/setup/rad/designextension' )}</label>
    <select id="dewLicence" name="licence">
    {foreach $wizard_licences as $dew_key => $dew_label}
        <option value="{$dew_key|wash}"{if eq( $wizard_settings.licence, $dew_key )} selected="selected"{/if}>{$dew_label|wash}</option>
    {/foreach}
    </select>
</div>
<div class="dew-field">
    <label for="dewBase">{'Falls back on'|i18n( 'design/admin/setup/rad/designextension' )}</label>
    <select id="dewBase" name="base_design">
    {foreach $wizard_base_designs as $dew_key => $dew_label}
        <option value="{$dew_key|wash}"{if eq( $wizard_settings.base_design, $dew_key )} selected="selected"{/if}>{$dew_label|wash}</option>
    {/foreach}
    </select>
</div>
</div>

<div class="dew-field">
    <label for="dewSiteaccess">{'Siteaccess'|i18n( 'design/admin/setup/rad/designextension' )}</label>
    <select id="dewSiteaccess" name="siteaccess">
        <option value="">{'None - just add the design to the chain'|i18n( 'design/admin/setup/rad/designextension' )}</option>
    {foreach $wizard_siteaccess_list as $dew_sa}
        <option value="{$dew_sa|wash}"{if eq( $wizard_settings.siteaccess, $dew_sa )} selected="selected"{/if}>{$dew_sa|wash}</option>
    {/foreach}
    </select>
    <span class="dew-hint">{'Used only when siteaccess settings are ticked below.'|i18n( 'design/admin/setup/rad/designextension' )}</span>
</div>
</div>

<div class="dew-card">
<h2>{'What goes in it'|i18n( 'design/admin/setup/rad/designextension' )}</h2>
<span class="dew-meta">{'Everything here is a starting point meant to be edited, not a black box.'|i18n( 'design/admin/setup/rad/designextension' )}</span>

<div class="dew-toolbar">
    <button type="button" class="dew-btn" id="dewAll">{'Tick all'|i18n( 'design/admin/setup/rad/designextension' )}</button>
    <button type="button" class="dew-btn" id="dewNone">{'Tick none'|i18n( 'design/admin/setup/rad/designextension' )}</button>
    <button type="submit" class="dew-btn" name="PreviewButton" value="1">{'Refresh preview'|i18n( 'design/admin/setup/rad/designextension' )}</button>
</div>

<div class="dew-parts">
{foreach $wizard_parts as $dew_key => $dew_part}
    <label class="dew-part">
        <input type="checkbox" name="Parts[]" value="{$dew_key|wash}"{if $wizard_settings.parts[$dew_key]} checked="checked"{/if} />
        <span>
            <span class="dew-part-label">{$dew_part.label|i18n( 'design/admin/setup/rad/designextension' )}</span>
            <span class="dew-meta">{$dew_part.description|i18n( 'design/admin/setup/rad/designextension' )}</span>
        </span>
    </label>
{/foreach}
</div>
</div>

</div>

{* --------------------------------------------------------- the preview -- *}
<div class="dew-col">

<div class="dew-card">
<h2>{'What it will write'|i18n( 'design/admin/setup/rad/designextension' )}</h2>
<span class="dew-meta">{$wizard_target|wash}</span>

<div class="dew-summary">
    <span><b>{$wizard_file_count}</b><span class="dew-meta">{'files'|i18n( 'design/admin/setup/rad/designextension' )}</span></span>
    <span><b>{$wizard_directories|count}</b><span class="dew-meta">{'directories'|i18n( 'design/admin/setup/rad/designextension' )}</span></span>
</div>

{if $wizard_file_count|gt( 0 )}
<ul class="dew-tree">
{foreach $wizard_files as $dew_file}
    <li style="padding-left: {mul( $dew_file.depth, 1.1 )}rem;">
        <a href="#dew-{$dew_file.path|wash|simplify_tag_name}">{$dew_file.basename|wash}</a>
        <span class="dew-size">{$dew_file.lines} {'lines'|i18n( 'design/admin/setup/rad/designextension' )}</span>
        {if $dew_file.depth|gt( 0 )}<span class="dew-dir">&mdash; {$dew_file.path|wash}</span>{/if}
    </li>
{/foreach}
</ul>
{else}
<p class="dew-meta">{'Name it, and the file list appears here.'|i18n( 'design/admin/setup/rad/designextension' )}</p>
{/if}
</div>

<div class="dew-card">
<h2>{'Switching it on'|i18n( 'design/admin/setup/rad/designextension' )}</h2>
<span class="dew-meta">{'Add this to settings/override/site.ini.append.php, then clear the caches.'|i18n( 'design/admin/setup/rad/designextension' )}</span>
<pre class="dew-activation">{$wizard_activation|wash}</pre>
</div>

</div>
</div>

{* ------------------------------------------------------- the files ------ *}
{if $wizard_file_count|gt( 0 )}
<div class="dew-card">
<h2>{'Every file, before it is written'|i18n( 'design/admin/setup/rad/designextension' )}</h2>
<span class="dew-meta">{'Click a heading to read one. Nothing here has been written yet.'|i18n( 'design/admin/setup/rad/designextension' )}</span>

<div class="dew-toolbar">
    <button type="button" class="dew-btn" id="dewOpenAll">{'Open all'|i18n( 'design/admin/setup/rad/designextension' )}</button>
    <button type="button" class="dew-btn" id="dewCloseAll">{'Close all'|i18n( 'design/admin/setup/rad/designextension' )}</button>
</div>

{foreach $wizard_files as $dew_file}
<div class="dew-file is-collapsed" id="dew-{$dew_file.path|wash|simplify_tag_name}">
    <div class="dew-file-head">
        <span class="dew-file-toggle">+</span>
        <span class="dew-file-path">{$dew_file.path|wash}</span>
        <span class="dew-meta">{$dew_file.lines} {'lines'|i18n( 'design/admin/setup/rad/designextension' )}, {$dew_file.bytes} {'bytes'|i18n( 'design/admin/setup/rad/designextension' )}</span>
    </div>
    <pre>{$dew_file.contents|wash}</pre>
</div>
{/foreach}
</div>
{/if}

{* DESIGN: Content END *}</div></div></div>

<div class="controlbar">
{* DESIGN: Control bar START *}<div class="box-bc"><div class="box-ml">
<div class="block dew-actions">
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
    <a class="dew-meta" href={'setup/rad'|ezurl}>{'Back to the RAD tools'|i18n( 'design/admin/setup/rad/designextension' )}</a>
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
        if ( panels[i].className.indexOf( 'dew-file' ) === 0 )
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
            if ( hint )
                hint.innerHTML = clean === ''
                    ? 'Becomes extension/&lt;name&gt; and design/&lt;name&gt;.'
                    : 'Becomes extension/' + clean + ' and design/' + clean + '.';
        };
    }
} )();
</script>
{/literal}
