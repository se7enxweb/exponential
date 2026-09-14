{* The handler wizard.

   One kind of handler at a time. What the kernel does by default, what a
   handler of your own would do instead, and every method it has to implement
   with a note on what each is for and when it is called. *}

{literal}
<style type="text/css">
.exp-hw {
    --hw-ink: #1c1c1e; --hw-muted: #6a6a72; --hw-line: #e2e2e6;
    --hw-accent: #2d6cdf; --hw-ok: #1f8a4c; --hw-bad: #b4232c; --hw-gap: 1.6rem;
    color: var(--hw-ink);
}
.exp-hw h2 { font-size: 1.05rem; margin: 0 0 .15rem 0; }
.exp-hw .hw-meta { color: var(--hw-muted); font-size: .92em; white-space: normal; }
.exp-hw code, .exp-hw pre { font-family: Menlo, Consolas, "DejaVu Sans Mono", monospace; overflow-wrap: anywhere; }
.exp-hw pre { max-width: 100%; overflow-x: auto; white-space: pre; }
.exp-hw label, .exp-hw label * { white-space: normal; }

.hw-note {
    display: flex; gap: .6rem; align-items: flex-start;
    border: 1px solid var(--hw-line); border-left-width: 4px; border-radius: 6px;
    padding: .7rem .9rem; margin: 0 0 .8rem 0; background: #fcfcfd;
}
.hw-note.is-ok { border-left-color: var(--hw-ok); }
.hw-note.is-bad { border-left-color: var(--hw-bad); }
.hw-note.is-info { border-left-color: var(--hw-accent); }

.hw-card {
    border: 1px solid var(--hw-line); border-radius: 8px; background: #fff;
    padding: 1rem 1.1rem; margin: 0 0 var(--hw-gap) 0; min-width: 0;
}
.hw-card > .hw-meta { display: block; padding-bottom: .9rem; }
.hw-grid { display: flex; flex-wrap: wrap; gap: var(--hw-gap); align-items: flex-start; }
.hw-col { flex: 1 1 24rem; min-width: 0; }

.hw-field { display: flex; flex-direction: column; gap: .3rem; padding-bottom: .9rem; }
.hw-field label { font-size: .82em; color: var(--hw-muted); text-transform: uppercase; letter-spacing: .04em; font-weight: 600; }
.hw-field input[type=text], .hw-field select {
    border: 1px solid var(--hw-line); border-radius: 6px; padding: .45rem .55rem;
    font: inherit; background: #fff; width: 100%; box-sizing: border-box;
}
.hw-field .hw-hint { font-size: .85em; color: var(--hw-muted); text-transform: none; letter-spacing: 0; font-weight: normal; }
.hw-row { display: flex; flex-wrap: wrap; gap: .9rem; }
.hw-row > .hw-field { flex: 1 1 9rem; }

.hw-facts { display: flex; flex-direction: column; gap: .3rem; padding-bottom: .6rem; }
.hw-fact { display: flex; flex-wrap: wrap; gap: .5rem; }
.hw-fact b { flex: 0 0 7rem; font-weight: 600; color: var(--hw-muted); font-size: .85em; text-transform: uppercase; letter-spacing: .03em; }
.hw-fact span { flex: 1 1 12rem; min-width: 0; }

.hw-method { border-top: 1px solid #f0f0f3; padding: .55rem 0; }
.hw-method code { font-weight: 600; display: block; padding-bottom: .15rem; }

.hw-part { display: flex; gap: .6rem; align-items: flex-start; padding: .5rem .55rem; border-radius: 6px; }
.hw-part:hover { background: #f6f7f9; }
.hw-part input { margin-top: .25rem; flex: 0 0 auto; }
.hw-part > span { flex: 1 1 auto; min-width: 0; }
.hw-part .hw-part-label { display: block; font-weight: 600; }
.hw-part .hw-meta { display: block; }

.hw-file { border: 1px solid var(--hw-line); border-radius: 8px; margin: 0 0 .6rem 0; background: #fff; }
.hw-file-head { display: flex; flex-wrap: wrap; align-items: center; gap: .6rem; padding: .55rem .8rem; cursor: pointer; }
.hw-file-head:hover { background: #f6f7f9; }
.hw-file-toggle {
    display: inline-flex; align-items: center; justify-content: center;
    width: 1.25rem; height: 1.25rem; border: 1px solid var(--hw-line);
    border-radius: 4px; font-weight: 700; background: #f4f4f5; line-height: 1;
}
.hw-file-path { font-weight: 600; overflow-wrap: anywhere; min-width: 0; }
.hw-file pre { margin: 0; padding: .8rem 1rem; border-top: 1px solid var(--hw-line); background: #fbfbfc; font-size: .88em; line-height: 1.5; }
.hw-file.is-collapsed pre { display: none; }
.hw-toolbar { display: flex; flex-wrap: wrap; gap: .5rem; padding: .2rem 0 .9rem 0; }
.hw-btn { border: 1px solid var(--hw-line); background: #f4f4f5; border-radius: 6px; padding: .3rem .7rem; font: inherit; cursor: pointer; color: inherit; }
.hw-btn:hover { border-color: #9a9aa0; }
</style>
{/literal}

<form method="post" action={'setup/handlerextension'|ezurl} name="HandlerWizard">
<input type="hidden" name="Submitted" value="1" />
<input type="hidden" name="kind" value="{$wizard_settings.kind|wash}" />

<div class="context-block exp-hw">

{* DESIGN: Header START *}<div class="box-header"><div class="box-ml">
<h1 class="context-title">{if $wizard_recipe}{$wizard_recipe.title|i18n( 'design/admin/setup/rad/handler' )|wash}{else}{'Handler wizard'|i18n( 'design/admin/setup/rad/handler' )}{/if}</h1>
{* DESIGN: Mainline *}<div class="header-mainline"></div>
{* DESIGN: Header END *}</div></div>

{* DESIGN: Content START *}<div class="box-ml"><div class="box-mr"><div class="box-content">

{if $wizard_recipe}
<div class="context-attributes">
<p>{$wizard_recipe.what|i18n( 'design/admin/setup/rad/handler' )|wash} {$wizard_recipe.why|i18n( 'design/admin/setup/rad/handler' )|wash}</p>
</div>
{/if}

<div class="context-toolbar"><div class="button-left">
<p class="table-preferences">
{foreach $wizard_kinds as $hw_kind}
    {if $hw_kind.current}<span class="current">{$hw_kind.title|i18n( 'design/admin/setup/rad/handler' )|wash}</span>
    {else}<a href={$hw_kind.url|ezurl}>{$hw_kind.title|i18n( 'design/admin/setup/rad/handler' )|wash}</a>{/if}
{/foreach}
</p>
</div></div>

{foreach $wizard_feedback as $hw_note}
<div class="hw-note {if $hw_note.ok}is-ok{else}is-bad{/if}">
    <strong>{if $hw_note.ok}&#10003;{else}!{/if}</strong><span>{$hw_note.message|wash}</span>
</div>
{/foreach}

{foreach $wizard_problems as $hw_problem}
<div class="hw-note is-bad"><strong>!</strong><span>{$hw_problem|wash}</span></div>
{/foreach}

{if $wizard_written|count|gt( 0 )}
<div class="hw-note is-info"><strong>&#10003;</strong><span>{'Written to %target. Switch it on with the lines below, clear the caches, and regenerate the extension autoloads.'|i18n( 'design/admin/setup/rad/handler',, hash( '%target', $wizard_target ) )}</span></div>
{/if}

{if $wizard_can_write|not}
<div class="hw-note is-info"><strong>i</strong><span>{'The web server cannot write into extension/, so this page can only hand you an archive.'|i18n( 'design/admin/setup/rad/handler' )}</span></div>
{/if}

<div class="hw-grid">
<div class="hw-col">

<div class="hw-card">
<h2>{'The extension'|i18n( 'design/admin/setup/rad/handler' )}</h2>
<span class="hw-meta">{'The class name is what the ini will point at, so it has to be one nothing else on this installation already uses.'|i18n( 'design/admin/setup/rad/handler' )}</span>

<div class="hw-row">
    <div class="hw-field">
        <label for="hwName">{'Extension name'|i18n( 'design/admin/setup/rad/handler' )}</label>
        <input type="text" id="hwName" name="name" value="{$wizard_settings.name|wash}" placeholder="my_handler" autocomplete="off" />
    </div>
    <div class="hw-field">
        <label for="hwClass">{'Class name'|i18n( 'design/admin/setup/rad/handler' )}</label>
        <input type="text" id="hwClass" name="class" value="{$wizard_settings.class|wash}" autocomplete="off" />
    </div>
{if $wizard_recipe.aliased}
    <div class="hw-field">
        <label for="hwAlias">{'Alias'|i18n( 'design/admin/setup/rad/handler' )}</label>
        <input type="text" id="hwAlias" name="alias" value="{$wizard_settings.alias|wash}" autocomplete="off" />
        <span class="hw-hint">{'The short word the setting names, rather than the class itself.'|i18n( 'design/admin/setup/rad/handler' )}</span>
    </div>
{/if}
</div>

<div class="hw-field">
    <label for="hwTitle">{'Title'|i18n( 'design/admin/setup/rad/handler' )}</label>
    <input type="text" id="hwTitle" name="title" value="{$wizard_settings.title|wash}" />
</div>

<div class="hw-field">
    <label for="hwSummary">{'Summary'|i18n( 'design/admin/setup/rad/handler' )}</label>
    <input type="text" id="hwSummary" name="summary" value="{$wizard_settings.summary|wash}" />
</div>

<div class="hw-row">
    <div class="hw-field">
        <label for="hwAuthor">{'Author'|i18n( 'design/admin/setup/rad/handler' )}</label>
        <input type="text" id="hwAuthor" name="author" value="{$wizard_settings.author|wash}" />
    </div>
    <div class="hw-field">
        <label for="hwVendor">{'Composer vendor'|i18n( 'design/admin/setup/rad/handler' )}</label>
        <input type="text" id="hwVendor" name="vendor" value="{$wizard_settings.vendor|wash}" />
    </div>
    <div class="hw-field">
        <label for="hwVersion">{'Version'|i18n( 'design/admin/setup/rad/handler' )}</label>
        <input type="text" id="hwVersion" name="version" value="{$wizard_settings.version|wash}" />
    </div>
</div>

<div class="hw-field">
    <label for="hwLicence">{'Licence'|i18n( 'design/admin/setup/rad/handler' )}</label>
    <select id="hwLicence" name="licence">
    {foreach $wizard_licences as $hw_key => $hw_label}
        <option value="{$hw_key|wash}"{if eq( $wizard_settings.licence, $hw_key )} selected="selected"{/if}>{$hw_label|wash}</option>
    {/foreach}
    </select>
</div>
</div>

<div class="hw-card">
<h2>{'What goes in it'|i18n( 'design/admin/setup/rad/handler' )}</h2>
{foreach $wizard_parts as $hw_key => $hw_part}
    <label class="hw-part">
        <input type="checkbox" name="Parts[]" value="{$hw_key|wash}"{if $wizard_settings.parts[$hw_key]} checked="checked"{/if} />
        <span>
            <span class="hw-part-label">{$hw_part.label|i18n( 'design/admin/setup/rad/handler' )|wash}</span>
            <span class="hw-meta">{$hw_part.description|i18n( 'design/admin/setup/rad/handler' )|wash}</span>
        </span>
    </label>
{/foreach}
</div>

</div>

<div class="hw-col">

{if $wizard_recipe}
<div class="hw-card">
<h2>{'What it replaces'|i18n( 'design/admin/setup/rad/handler' )}</h2>
<div class="hw-facts">
    <div class="hw-fact"><b>{'Extends'|i18n( 'design/admin/setup/rad/handler' )}</b><span><code>{$wizard_recipe.base|wash}</code></span></div>
    <div class="hw-fact"><b>{'Kernel'|i18n( 'design/admin/setup/rad/handler' )}</b><span class="hw-meta"><code>{$wizard_recipe.source|wash}</code></span></div>
    <div class="hw-fact"><b>{'Setting'|i18n( 'design/admin/setup/rad/handler' )}</b><span><code>{$wizard_recipe.ini|wash} [{$wizard_recipe.section|wash}] {$wizard_recipe.variable|wash}{if $wizard_recipe.aliased}[{$wizard_settings.alias|wash}]{/if}</code></span></div>
</div>
{if ne( $wizard_recipe.note, '' )}
<p class="hw-meta">{$wizard_recipe.note|i18n( 'design/admin/setup/rad/handler' )|wash}</p>
{/if}
</div>

<div class="hw-card">
<h2>{'What has to be written'|i18n( 'design/admin/setup/rad/handler' )} ({$wizard_methods|count})</h2>
<span class="hw-meta">{'Every one of these is a method the kernel calls. Each is generated with this note beside it, and returns something that leaves the system working the way it did before.'|i18n( 'design/admin/setup/rad/handler' )}</span>

{foreach $wizard_methods as $hw_method}
<div class="hw-method">
    <code>{$hw_method.signature|wash}</code>
    <span class="hw-meta">{$hw_method.what|i18n( 'design/admin/setup/rad/handler' )|wash}</span>
</div>
{/foreach}
</div>
{/if}

<div class="hw-card">
<h2>{'Switching it on'|i18n( 'design/admin/setup/rad/handler' )}</h2>
<span class="hw-meta">{'Add this to settings/override/site.ini.append.php. The extension brings its own setting naming the class.'|i18n( 'design/admin/setup/rad/handler' )}</span>
<pre>{$wizard_activation|wash}</pre>
</div>

</div>
</div>

{if $wizard_file_count|gt( 0 )}
<div class="hw-card">
<h2>{'Every file, before it is written'|i18n( 'design/admin/setup/rad/handler' )}</h2>
<span class="hw-meta">{$wizard_target|wash}</span>

<div class="hw-toolbar">
    <button type="button" class="hw-btn" id="hwOpenAll">{'Open all'|i18n( 'design/admin/setup/rad/handler' )}</button>
    <button type="button" class="hw-btn" id="hwCloseAll">{'Close all'|i18n( 'design/admin/setup/rad/handler' )}</button>
</div>

{foreach $wizard_files as $hw_file}
<div class="hw-file is-collapsed">
    <div class="hw-file-head">
        <span class="hw-file-toggle">+</span>
        <span class="hw-file-path">{$hw_file.path|wash}</span>
        <span class="hw-meta">{$hw_file.lines|wash} {'lines'|i18n( 'design/admin/setup/rad/handler' )}, {$hw_file.bytes|wash} {'bytes'|i18n( 'design/admin/setup/rad/handler' )}</span>
    </div>
    <pre>{$hw_file.contents|wash}</pre>
</div>
{/foreach}
</div>
{/if}

{* DESIGN: Content END *}</div></div></div>

<div class="controlbar">
{* DESIGN: Control bar START *}<div class="box-bc"><div class="box-ml">
<div class="block">
    {if $wizard_ready}
    <input class="defaultbutton" type="submit" name="CreateButton" value="{'Create in extension/'|i18n( 'design/admin/setup/rad/handler' )}" />
    {else}
    <input class="button-disabled" type="submit" name="_Disabled" value="{'Create in extension/'|i18n( 'design/admin/setup/rad/handler' )}" disabled="disabled" />
    {/if}

    {if $wizard_file_count|gt( 0 )}
        {if $wizard_can_archive}
    <input class="button" type="submit" name="DownloadButton" value="{'Download as zip'|i18n( 'design/admin/setup/rad/handler' )}" />
        {/if}
    {/if}

    <input class="button" type="submit" name="PreviewButton" value="{'Refresh preview'|i18n( 'design/admin/setup/rad/handler' )}" />
    <a class="hw-meta" href={'setup/rad'|ezurl}>{'Back to the RAD tools'|i18n( 'design/admin/setup/rad/handler' )}</a>
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
        if ( divs[i].className.indexOf( 'hw-file' ) === 0 )
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
    var openAll = document.getElementById( 'hwOpenAll' ), closeAll = document.getElementById( 'hwCloseAll' );
    if ( openAll )  openAll.onclick  = function () { every( true ); return false; };
    if ( closeAll ) closeAll.onclick = function () { every( false ); return false; };
} )();
</script>
{/literal}
