{* The module wizard.

   A module is three things that have to agree with each other, plus two ini
   lines that make the kernel look for any of it. *}

{literal}
<style type="text/css">
.exp-mw {
    --mw-ink: #1c1c1e; --mw-muted: #6a6a72; --mw-line: #e2e2e6;
    --mw-accent: #2d6cdf; --mw-ok: #1f8a4c; --mw-bad: #b4232c; --mw-gap: 1.6rem;
    color: var(--mw-ink);
}
.exp-mw h2 { font-size: 1.05rem; margin: 0 0 .15rem 0; }
.exp-mw h3 { font-size: .9rem; margin: 1rem 0 .3rem 0; color: var(--mw-muted); text-transform: uppercase; letter-spacing: .04em; }
.exp-mw .mw-meta { color: var(--mw-muted); font-size: .92em; white-space: normal; }
.exp-mw code, .exp-mw pre { font-family: Menlo, Consolas, "DejaVu Sans Mono", monospace; overflow-wrap: anywhere; }
.exp-mw pre { max-width: 100%; overflow-x: auto; white-space: pre; }
.exp-mw label, .exp-mw label * { white-space: normal; }

.mw-note {
    display: flex; gap: .6rem; align-items: flex-start;
    border: 1px solid var(--mw-line); border-left-width: 4px; border-radius: 6px;
    padding: .7rem .9rem; margin: 0 0 .8rem 0; background: #fcfcfd;
}
.mw-note.is-ok { border-left-color: var(--mw-ok); }
.mw-note.is-bad { border-left-color: var(--mw-bad); }
.mw-note.is-info { border-left-color: var(--mw-accent); }

.mw-card { border: 1px solid var(--mw-line); border-radius: 8px; background: #fff; padding: 1rem 1.1rem; margin: 0 0 var(--mw-gap) 0; min-width: 0; }
.mw-card > .mw-meta { display: block; padding-bottom: .9rem; }
.mw-grid { display: flex; flex-wrap: wrap; gap: var(--mw-gap); align-items: flex-start; }
.mw-col { flex: 1 1 26rem; min-width: 0; }

.mw-field { display: flex; flex-direction: column; gap: .3rem; padding-bottom: .9rem; }
.mw-field label { font-size: .82em; color: var(--mw-muted); text-transform: uppercase; letter-spacing: .04em; font-weight: 600; }
.mw-field input[type=text], .mw-field select, .mw-field textarea {
    border: 1px solid var(--mw-line); border-radius: 6px; padding: .45rem .55rem;
    font: inherit; background: #fff; width: 100%; box-sizing: border-box;
}
.mw-field textarea { font-family: Menlo, Consolas, "DejaVu Sans Mono", monospace; font-size: .9em; min-height: 5.5rem; resize: vertical; }
.mw-field .mw-hint { font-size: .85em; color: var(--mw-muted); text-transform: none; letter-spacing: 0; font-weight: normal; }
.mw-row { display: flex; flex-wrap: wrap; gap: .9rem; }
.mw-row > .mw-field { flex: 1 1 9rem; }

.mw-pick { display: flex; gap: .6rem; align-items: flex-start; padding: .45rem .55rem; border-radius: 6px; }
.mw-pick:hover { background: #f6f7f9; }
.mw-pick input { margin-top: .25rem; flex: 0 0 auto; }
.mw-pick > span { flex: 1 1 auto; min-width: 0; }
.mw-pick .mw-pick-label { display: block; font-weight: 600; }
.mw-pick .mw-meta { display: block; }

.mw-view { border-top: 1px solid #f0f0f3; padding: .55rem 0; }
.mw-view code.mw-address { display: block; font-weight: 600; padding-bottom: .15rem; }
.mw-tag { font-size: .72em; letter-spacing: .05em; text-transform: uppercase; font-weight: 700; color: #fff; background: var(--mw-accent); border-radius: 3px; padding: .05rem .3rem; vertical-align: .1em; }
.mw-tag.is-open { background: var(--mw-muted); }
.mw-tag.is-bad { background: var(--mw-bad); }

.mw-ref { border-top: 1px solid #f0f0f3; padding: .35rem 0; display: flex; flex-wrap: wrap; gap: .5rem; align-items: baseline; }
.mw-ref code { flex: 0 0 8rem; font-weight: 600; }
.mw-ref .mw-meta { flex: 1 1 12rem; min-width: 0; }

.mw-file { border: 1px solid var(--mw-line); border-radius: 8px; margin: 0 0 .6rem 0; background: #fff; }
.mw-file-head { display: flex; flex-wrap: wrap; align-items: center; gap: .6rem; padding: .55rem .8rem; cursor: pointer; }
.mw-file-head:hover { background: #f6f7f9; }
.mw-file-toggle { display: inline-flex; align-items: center; justify-content: center; width: 1.25rem; height: 1.25rem; border: 1px solid var(--mw-line); border-radius: 4px; font-weight: 700; background: #f4f4f5; line-height: 1; }
.mw-file-path { font-weight: 600; overflow-wrap: anywhere; min-width: 0; }
.mw-file pre { margin: 0; padding: .8rem 1rem; border-top: 1px solid var(--mw-line); background: #fbfbfc; font-size: .88em; line-height: 1.5; }
.mw-file.is-collapsed pre { display: none; }
.mw-toolbar { display: flex; flex-wrap: wrap; gap: .5rem; padding: .2rem 0 .9rem 0; }
.mw-btn { border: 1px solid var(--mw-line); background: #f4f4f5; border-radius: 6px; padding: .3rem .7rem; font: inherit; cursor: pointer; color: inherit; }
.mw-btn:hover { border-color: #9a9aa0; }
</style>
{/literal}

<form method="post" action={'setup/modulewizard'|ezurl} name="ModuleWizard">
<input type="hidden" name="Submitted" value="1" />

<div class="context-block exp-mw">

{* DESIGN: Header START *}<div class="box-header"><div class="box-ml">
<h1 class="context-title">{'Module wizard'|i18n( 'design/admin/setup/rad/module' )}</h1>
{* DESIGN: Mainline *}<div class="header-mainline"></div>
{* DESIGN: Header END *}</div></div>

{* DESIGN: Content START *}<div class="box-ml"><div class="box-mr"><div class="box-content">

<div class="context-attributes">
<p>{'A module is how this system serves a page that is not content: a declaration, a script per view, a template per view, and the two ini lines that make the kernel look for any of it. When they disagree the failure is quiet - a view with no script is a blank page, a view naming a policy no module declares can be reached by nobody, and a module the ini does not list is not there at all.'|i18n( 'design/admin/setup/rad/module' )}</p>
</div>

{foreach $wizard_feedback as $mw_note}
<div class="mw-note {if $mw_note.ok}is-ok{else}is-bad{/if}">
    <strong>{if $mw_note.ok}&#10003;{else}!{/if}</strong><span>{$mw_note.message|wash}</span>
</div>
{/foreach}

{foreach $wizard_problems as $mw_problem}
<div class="mw-note is-bad"><strong>!</strong><span>{$mw_problem|wash}</span></div>
{/foreach}

{if $wizard_written|count|gt( 0 )}
<div class="mw-note is-info"><strong>&#10003;</strong><span>{'Written to %target. Switch it on with the lines below and clear the caches - the module list is itself cached.'|i18n( 'design/admin/setup/rad/module',, hash( '%target', $wizard_target ) )}</span></div>
{/if}

{if $wizard_can_write|not}
<div class="mw-note is-info"><strong>i</strong><span>{'The web server cannot write into extension/, so this page can only hand you an archive.'|i18n( 'design/admin/setup/rad/module' )}</span></div>
{/if}

<div class="mw-grid">
<div class="mw-col">

<div class="mw-card">
<h2>{'The views'|i18n( 'design/admin/setup/rad/module' )}</h2>
<span class="mw-meta">{'One per line: a name, a colon, then what it needs. A word in lower case is a policy; a word starting with a capital is a parameter in the address; a capital word ending in ? is a named parameter that may be left out.'|i18n( 'design/admin/setup/rad/module' )}</span>

<div class="mw-field">
    <textarea name="views" rows="5" placeholder="list: read Offset? Limit?&#10;view: read Id&#10;edit: edit Id">{$wizard_raw.views|wash}</textarea>
</div>

<h3>{'What that produces'|i18n( 'design/admin/setup/rad/module' )}</h3>
{if $wizard_views|count|eq( 0 )}
<p class="mw-meta">{'Name a view and its address appears here.'|i18n( 'design/admin/setup/rad/module' )}</p>
{/if}
{foreach $wizard_views as $mw_view}
<div class="mw-view">
    <code class="mw-address">{$mw_view.address|wash}</code>
    {if $mw_view.policies|count|eq( 0 )}
        <span class="mw-tag is-open">{'no policy check'|i18n( 'design/admin/setup/rad/module' )}</span>
    {else}
        {foreach $mw_view.policies as $mw_policy}<span class="mw-tag{if $mw_policy|contains( $mw_view.undeclared )} is-bad{/if}">{$mw_policy|wash}</span> {/foreach}
    {/if}
    <span class="mw-meta">{$mw_view.script|wash}</span>
</div>
{/foreach}
</div>

<div class="mw-card">
<h2>{'The policies'|i18n( 'design/admin/setup/rad/module' )}</h2>
<span class="mw-meta">{'One per line: a name, a colon, then any limitations. Every policy a view asks for has to be here, or nobody can be granted it and the view is reachable by nobody.'|i18n( 'design/admin/setup/rad/module' )}</span>

<div class="mw-field">
    <textarea name="policies" rows="3" placeholder="read: Section, Class&#10;edit: Section">{$wizard_raw.policies|wash}</textarea>
</div>

<h3>{'Limitations'|i18n( 'design/admin/setup/rad/module' )}</h3>
{foreach $wizard_limitations as $mw_key => $mw_limitation}
<div class="mw-ref">
    <code>{$mw_key|wash}</code>
    <span class="mw-meta">{$mw_limitation.what|i18n( 'design/admin/setup/rad/module' )|wash}</span>
</div>
{/foreach}
</div>

</div>

<div class="mw-col">

<div class="mw-card">
<h2>{'The module'|i18n( 'design/admin/setup/rad/module' )}</h2>

<div class="mw-row">
    <div class="mw-field">
        <label for="mwName">{'Extension name'|i18n( 'design/admin/setup/rad/module' )}</label>
        <input type="text" id="mwName" name="name" value="{$wizard_settings.name|wash}" placeholder="my_module" autocomplete="off" />
    </div>
    <div class="mw-field">
        <label for="mwModule">{'Module name'|i18n( 'design/admin/setup/rad/module' )}</label>
        <input type="text" id="mwModule" name="module" value="{$wizard_settings.module|wash}" autocomplete="off" />
        <span class="mw-hint">{'The first part of every address it answers.'|i18n( 'design/admin/setup/rad/module' )}</span>
    </div>
</div>

<div class="mw-field">
    <label for="mwTitle">{'Title'|i18n( 'design/admin/setup/rad/module' )}</label>
    <input type="text" id="mwTitle" name="title" value="{$wizard_settings.title|wash}" />
</div>

<div class="mw-field">
    <label for="mwSummary">{'Summary'|i18n( 'design/admin/setup/rad/module' )}</label>
    <input type="text" id="mwSummary" name="summary" value="{$wizard_settings.summary|wash}" />
</div>

<div class="mw-field">
    <label for="mwContext">{'Where it sits'|i18n( 'design/admin/setup/rad/module' )}</label>
    <select id="mwContext" name="context">
    {foreach $wizard_contexts as $mw_key => $mw_what}
        <option value="{$mw_key|wash}"{if eq( $wizard_settings.context, $mw_key )} selected="selected"{/if}>{$mw_what|wash}</option>
    {/foreach}
    </select>
</div>

<div class="mw-field">
    <label for="mwNavigation">{'Part of the admin'|i18n( 'design/admin/setup/rad/module' )}</label>
    <select id="mwNavigation" name="navigation">
    {foreach $wizard_navigation_parts as $mw_part}
        <option value="{$mw_part|wash}"{if eq( $wizard_settings.navigation, $mw_part )} selected="selected"{/if}>{$mw_part|wash}</option>
    {/foreach}
    </select>
    <span class="mw-hint">{'Which left hand menu the entry goes in.'|i18n( 'design/admin/setup/rad/module' )}</span>
</div>

<div class="mw-row">
    <div class="mw-field">
        <label for="mwAuthor">{'Author'|i18n( 'design/admin/setup/rad/module' )}</label>
        <input type="text" id="mwAuthor" name="author" value="{$wizard_settings.author|wash}" />
    </div>
    <div class="mw-field">
        <label for="mwVendor">{'Composer vendor'|i18n( 'design/admin/setup/rad/module' )}</label>
        <input type="text" id="mwVendor" name="vendor" value="{$wizard_settings.vendor|wash}" />
    </div>
    <div class="mw-field">
        <label for="mwVersion">{'Version'|i18n( 'design/admin/setup/rad/module' )}</label>
        <input type="text" id="mwVersion" name="version" value="{$wizard_settings.version|wash}" />
    </div>
</div>

<div class="mw-field">
    <label for="mwLicence">{'Licence'|i18n( 'design/admin/setup/rad/module' )}</label>
    <select id="mwLicence" name="licence">
    {foreach $wizard_licences as $mw_key => $mw_label}
        <option value="{$mw_key|wash}"{if eq( $wizard_settings.licence, $mw_key )} selected="selected"{/if}>{$mw_label|wash}</option>
    {/foreach}
    </select>
</div>
</div>

<div class="mw-card">
<h2>{'What goes in it'|i18n( 'design/admin/setup/rad/module' )}</h2>
{foreach $wizard_parts as $mw_key => $mw_part}
    <label class="mw-pick">
        <input type="checkbox" name="Parts[]" value="{$mw_key|wash}"{if $wizard_settings.parts[$mw_key]} checked="checked"{/if} />
        <span>
            <span class="mw-pick-label">{$mw_part.label|i18n( 'design/admin/setup/rad/module' )|wash}</span>
            <span class="mw-meta">{$mw_part.description|i18n( 'design/admin/setup/rad/module' )|wash}</span>
        </span>
    </label>
{/foreach}
</div>

<div class="mw-card">
<h2>{'Switching it on'|i18n( 'design/admin/setup/rad/module' )}</h2>
<pre>{$wizard_activation|wash}</pre>
<h3>{'Then'|i18n( 'design/admin/setup/rad/module' )}</h3>
<pre>php bin/php/ezcache.php --clear-all</pre>
</div>

</div>
</div>

{if $wizard_file_count|gt( 0 )}
<div class="mw-card">
<h2>{'Every file, before it is written'|i18n( 'design/admin/setup/rad/module' )}</h2>
<span class="mw-meta">{$wizard_target|wash}</span>

<div class="mw-toolbar">
    <button type="button" class="mw-btn" id="mwOpenAll">{'Open all'|i18n( 'design/admin/setup/rad/module' )}</button>
    <button type="button" class="mw-btn" id="mwCloseAll">{'Close all'|i18n( 'design/admin/setup/rad/module' )}</button>
</div>

{foreach $wizard_files as $mw_file}
<div class="mw-file is-collapsed">
    <div class="mw-file-head">
        <span class="mw-file-toggle">+</span>
        <span class="mw-file-path">{$mw_file.path|wash}</span>
        <span class="mw-meta">{$mw_file.lines|wash} {'lines'|i18n( 'design/admin/setup/rad/module' )}, {$mw_file.bytes|wash} {'bytes'|i18n( 'design/admin/setup/rad/module' )}</span>
    </div>
    <pre>{$mw_file.contents|wash}</pre>
</div>
{/foreach}
</div>
{/if}

{* DESIGN: Content END *}</div></div></div>

<div class="controlbar">
{* DESIGN: Control bar START *}<div class="box-bc"><div class="box-ml">
<div class="block">
    {if $wizard_ready}
    <input class="defaultbutton" type="submit" name="CreateButton" value="{'Create in extension/'|i18n( 'design/admin/setup/rad/module' )}" />
    {else}
    <input class="button-disabled" type="submit" name="_Disabled" value="{'Create in extension/'|i18n( 'design/admin/setup/rad/module' )}" disabled="disabled" />
    {/if}

    {if $wizard_file_count|gt( 0 )}
        {if $wizard_can_archive}
    <input class="button" type="submit" name="DownloadButton" value="{'Download as zip'|i18n( 'design/admin/setup/rad/module' )}" />
        {/if}
    {/if}

    <input class="button" type="submit" name="PreviewButton" value="{'Refresh preview'|i18n( 'design/admin/setup/rad/module' )}" />
    <a class="mw-meta" href={'setup/rad'|ezurl}>{'Back to the RAD tools'|i18n( 'design/admin/setup/rad/module' )}</a>
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
        if ( divs[i].className.indexOf( 'mw-file' ) === 0 )
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
    var openAll = document.getElementById( 'mwOpenAll' ), closeAll = document.getElementById( 'mwCloseAll' );
    if ( openAll )  openAll.onclick  = function () { every( true ); return false; };
    if ( closeAll ) closeAll.onclick = function () { every( false ); return false; };
} )();
</script>
{/literal}
