{* The template extension wizard.

   Four things can be added to the template language, and they are easy to
   confuse. This page asks for each by name and shows what a template would
   write to reach it. *}

{literal}
<style type="text/css">
.exp-tw {
    --tw-ink: #1c1c1e; --tw-muted: #6a6a72; --tw-line: #e2e2e6;
    --tw-accent: #2d6cdf; --tw-ok: #1f8a4c; --tw-bad: #b4232c; --tw-gap: 1.6rem;
    color: var(--tw-ink);
}
.exp-tw h2 { font-size: 1.05rem; margin: 0 0 .15rem 0; }
.exp-tw h3 { font-size: .92rem; margin: 1rem 0 .3rem 0; color: var(--tw-muted); text-transform: uppercase; letter-spacing: .04em; }
.exp-tw .tw-meta { color: var(--tw-muted); font-size: .92em; white-space: normal; }
.exp-tw code, .exp-tw pre { font-family: Menlo, Consolas, "DejaVu Sans Mono", monospace; overflow-wrap: anywhere; }
.exp-tw pre { max-width: 100%; overflow-x: auto; white-space: pre; }
.exp-tw label, .exp-tw label * { white-space: normal; }

.tw-note {
    display: flex; gap: .6rem; align-items: flex-start;
    border: 1px solid var(--tw-line); border-left-width: 4px; border-radius: 6px;
    padding: .7rem .9rem; margin: 0 0 .8rem 0; background: #fcfcfd;
}
.tw-note.is-ok { border-left-color: var(--tw-ok); }
.tw-note.is-bad { border-left-color: var(--tw-bad); }
.tw-note.is-info { border-left-color: var(--tw-accent); }

.tw-card {
    border: 1px solid var(--tw-line); border-radius: 8px; background: #fff;
    padding: 1rem 1.1rem; margin: 0 0 var(--tw-gap) 0; min-width: 0;
}
.tw-card > .tw-meta { display: block; padding-bottom: .9rem; }
.tw-grid { display: flex; flex-wrap: wrap; gap: var(--tw-gap); align-items: flex-start; }
.tw-col { flex: 1 1 26rem; min-width: 0; }

.tw-field { display: flex; flex-direction: column; gap: .3rem; padding-bottom: .9rem; }
.tw-field label { font-size: .82em; color: var(--tw-muted); text-transform: uppercase; letter-spacing: .04em; font-weight: 600; }
.tw-field input[type=text], .tw-field select, .tw-field textarea {
    border: 1px solid var(--tw-line); border-radius: 6px; padding: .45rem .55rem;
    font: inherit; background: #fff; width: 100%; box-sizing: border-box;
}
.tw-field textarea { font-family: Menlo, Consolas, "DejaVu Sans Mono", monospace; font-size: .9em; min-height: 4.5rem; resize: vertical; }
.tw-field .tw-hint { font-size: .85em; color: var(--tw-muted); text-transform: none; letter-spacing: 0; font-weight: normal; }
.tw-row { display: flex; flex-wrap: wrap; gap: .9rem; }
.tw-row > .tw-field { flex: 1 1 9rem; }

.tw-pick { display: flex; gap: .6rem; align-items: flex-start; padding: .5rem .55rem; border-radius: 6px; }
.tw-pick:hover { background: #f6f7f9; }
.tw-pick input { margin-top: .25rem; flex: 0 0 auto; }
.tw-pick > span { flex: 1 1 auto; min-width: 0; }
.tw-pick .tw-pick-label { display: block; font-weight: 600; }
.tw-pick .tw-meta { display: block; }

.tw-use { border-top: 1px solid #f0f0f3; padding: .7rem 0; }
.tw-use-kind {
    display: inline-block; font-size: .72em; letter-spacing: .06em; text-transform: uppercase;
    font-weight: 700; color: #fff; background: var(--tw-accent); border-radius: 3px;
    padding: .1rem .35rem; vertical-align: .1em;
}
.tw-use pre { margin: .35rem 0 .25rem 0; padding: .55rem .7rem; background: #fbfbfc; border: 1px solid var(--tw-line); border-radius: 6px; font-size: .9em; }

.tw-file { border: 1px solid var(--tw-line); border-radius: 8px; margin: 0 0 .6rem 0; background: #fff; }
.tw-file-head { display: flex; flex-wrap: wrap; align-items: center; gap: .6rem; padding: .55rem .8rem; cursor: pointer; }
.tw-file-head:hover { background: #f6f7f9; }
.tw-file-toggle {
    display: inline-flex; align-items: center; justify-content: center;
    width: 1.25rem; height: 1.25rem; border: 1px solid var(--tw-line);
    border-radius: 4px; font-weight: 700; background: #f4f4f5; line-height: 1;
}
.tw-file-path { font-weight: 600; overflow-wrap: anywhere; min-width: 0; }
.tw-file pre { margin: 0; padding: .8rem 1rem; border-top: 1px solid var(--tw-line); background: #fbfbfc; font-size: .88em; line-height: 1.5; }
.tw-file.is-collapsed pre { display: none; }
.tw-toolbar { display: flex; flex-wrap: wrap; gap: .5rem; padding: .2rem 0 .9rem 0; }
.tw-btn { border: 1px solid var(--tw-line); background: #f4f4f5; border-radius: 6px; padding: .3rem .7rem; font: inherit; cursor: pointer; color: inherit; }
.tw-btn:hover { border-color: #9a9aa0; }
</style>
{/literal}

<form method="post" action={'setup/templateoperator'|ezurl} name="TemplateWizard">
<input type="hidden" name="Submitted" value="1" />

<div class="context-block exp-tw">

{* DESIGN: Header START *}<div class="box-header"><div class="box-ml">
<h1 class="context-title">{'Template extension wizard'|i18n( 'design/admin/setup/rad/template' )}</h1>
{* DESIGN: Mainline *}<div class="header-mainline"></div>
{* DESIGN: Header END *}</div></div>

{* DESIGN: Content START *}<div class="box-ml"><div class="box-mr"><div class="box-content">

<div class="context-attributes">
<p>{'Four things can be added to the template language from an extension, and they are easy to confuse. An operator takes a value and gives one back. A function writes output where it stands and may have a body. A fetch function reads something, with a policy check first. A fetch alias is a fetch with its arguments already decided. Name any mixture of them below.'|i18n( 'design/admin/setup/rad/template' )}</p>
</div>

{foreach $wizard_feedback as $tw_note}
<div class="tw-note {if $tw_note.ok}is-ok{else}is-bad{/if}">
    <strong>{if $tw_note.ok}&#10003;{else}!{/if}</strong><span>{$tw_note.message|wash}</span>
</div>
{/foreach}

{foreach $wizard_problems as $tw_problem}
<div class="tw-note is-bad"><strong>!</strong><span>{$tw_problem|wash}</span></div>
{/foreach}

{if $wizard_written|count|gt( 0 )}
<div class="tw-note is-info"><strong>&#10003;</strong><span>{'Written to %target. Switch it on with the lines below, regenerate the extension autoloads, and clear the caches - the operator list is itself cached.'|i18n( 'design/admin/setup/rad/template',, hash( '%target', $wizard_target ) )}</span></div>
{/if}

{if $wizard_can_write|not}
<div class="tw-note is-info"><strong>i</strong><span>{'The web server cannot write into extension/, so this page can only hand you an archive.'|i18n( 'design/admin/setup/rad/template' )}</span></div>
{/if}

<div class="tw-grid">
<div class="tw-col">

<div class="tw-card">
<h2>{'What to add'|i18n( 'design/admin/setup/rad/template' )}</h2>
<span class="tw-meta">{'Several names in a box, separated by commas, spaces or newlines. One class is written per kind, answering to all of its names.'|i18n( 'design/admin/setup/rad/template' )}</span>

<div class="tw-field">
    <label for="twOperators">{'Operators'|i18n( 'design/admin/setup/rad/template' )}</label>
    <input type="text" id="twOperators" name="operators" value="{$wizard_raw.operators|wash}" placeholder="myoperator, myother" autocomplete="off" />
    <span class="tw-hint">{'{$value|myoperator} — takes the value on the left and gives one back.'|i18n( 'design/admin/setup/rad/template' )}</span>
</div>

<div class="tw-field">
    <label for="twFunctions">{'Functions'|i18n( 'design/admin/setup/rad/template' )}</label>
    <input type="text" id="twFunctions" name="functions" value="{$wizard_raw.functions|wash}" placeholder="myfunction" autocomplete="off" />
    <span class="tw-hint">{'{myfunction arg=1} — writes output where it stands.'|i18n( 'design/admin/setup/rad/template' )}</span>
</div>

<div class="tw-field">
    <label for="twFetches">{'Fetch functions'|i18n( 'design/admin/setup/rad/template' )}</label>
    <input type="text" id="twFetches" name="fetches" value="{$wizard_raw.fetches|wash}" placeholder="thing, thing_list" autocomplete="off" />
    <span class="tw-hint">{'{fetch( module, thing )} — reads something, with a policy check first. Needs a module of its own.'|i18n( 'design/admin/setup/rad/template' )}</span>
</div>

<div class="tw-field">
    <label for="twAliases">{'Fetch aliases'|i18n( 'design/admin/setup/rad/template' )}</label>
    <input type="text" id="twAliases" name="aliases" value="{$wizard_raw.aliases|wash}" placeholder="news_list" autocomplete="off" />
    <span class="tw-hint">{'{fetch_alias( news_list )} — a fetch with its arguments fixed in an ini.'|i18n( 'design/admin/setup/rad/template' )}</span>
</div>

<div class="tw-field">
    <label for="twParameters">{'Parameters'|i18n( 'design/admin/setup/rad/template' )}</label>
    <textarea id="twParameters" name="parameters" rows="4" placeholder="limit integer required&#10;prefix string">{$wizard_raw.parameters|wash}</textarea>
    <span class="tw-hint">{'One per line: a name, then a type, then the word required if it is. Types: string, integer, float, boolean, array, any.'|i18n( 'design/admin/setup/rad/template' )}</span>
</div>

<label class="tw-pick">
    <input type="checkbox" name="UseInput" value="1"{if $wizard_settings.input} checked="checked"{/if} />
    <span><span class="tw-pick-label">{'Operators take input'|i18n( 'design/admin/setup/rad/template' )}</span>
    <span class="tw-meta">{'There is a value on the left of the pipe. Off means the operator is written {myoperator()} and produces a value out of its parameters alone.'|i18n( 'design/admin/setup/rad/template' )}</span></span>
</label>

<label class="tw-pick">
    <input type="checkbox" name="UseOutput" value="1"{if $wizard_settings.output} checked="checked"{/if} />
    <span><span class="tw-pick-label">{'Operators produce output'|i18n( 'design/admin/setup/rad/template' )}</span>
    <span class="tw-meta">{'What they leave behind is printed rather than only used. Shown in the examples as a reminder to decide who washes it.'|i18n( 'design/admin/setup/rad/template' )}</span></span>
</label>

<label class="tw-pick">
    <input type="checkbox" name="HasChildren" value="1"{if $wizard_settings.children} checked="checked"{/if} />
    <span><span class="tw-pick-label">{'Functions have a body'|i18n( 'design/admin/setup/rad/template' )}</span>
    <span class="tw-meta">{'{myfunction}...{/myfunction}, with the body handed over unprocessed to draw none, one or many times. This is how section and foreach work.'|i18n( 'design/admin/setup/rad/template' )}</span></span>
</label>
</div>

<div class="tw-card">
<h2>{'What the compiler may assume'|i18n( 'design/admin/setup/rad/template' )}</h2>
<span class="tw-meta">{'A template is compiled once and run many times. What is promised here decides how much work happens at compile time and how much on every request, for ever.'|i18n( 'design/admin/setup/rad/template' )}</span>

{foreach $wizard_hints as $tw_hint}
    <label class="tw-pick">
        <input type="checkbox" name="Hints[]" value="{$tw_hint.key|wash}"{if $tw_hint.chosen} checked="checked"{/if} />
        <span>
            <span class="tw-pick-label">{$tw_hint.label|i18n( 'design/admin/setup/rad/template' )|wash}</span>
            <span class="tw-meta">{$tw_hint.what|i18n( 'design/admin/setup/rad/template' )|wash}</span>
        </span>
    </label>
{/foreach}
</div>

<div class="tw-card">
<h2>{'The extension'|i18n( 'design/admin/setup/rad/template' )}</h2>

<div class="tw-row">
    <div class="tw-field">
        <label for="twName">{'Extension name'|i18n( 'design/admin/setup/rad/template' )}</label>
        <input type="text" id="twName" name="name" value="{$wizard_settings.name|wash}" placeholder="my_operators" autocomplete="off" />
    </div>
    <div class="tw-field">
        <label for="twClass">{'Class name'|i18n( 'design/admin/setup/rad/template' )}</label>
        <input type="text" id="twClass" name="class" value="{$wizard_settings.class|wash}" autocomplete="off" />
    </div>
    <div class="tw-field">
        <label for="twModule">{'Module for the fetches'|i18n( 'design/admin/setup/rad/template' )}</label>
        <input type="text" id="twModule" name="module" value="{$wizard_settings.module|wash}" autocomplete="off" />
    </div>
</div>

<div class="tw-field">
    <label for="twTitle">{'Title'|i18n( 'design/admin/setup/rad/template' )}</label>
    <input type="text" id="twTitle" name="title" value="{$wizard_settings.title|wash}" />
</div>

<div class="tw-field">
    <label for="twSummary">{'Summary'|i18n( 'design/admin/setup/rad/template' )}</label>
    <input type="text" id="twSummary" name="summary" value="{$wizard_settings.summary|wash}" />
</div>

<div class="tw-row">
    <div class="tw-field">
        <label for="twAuthor">{'Author'|i18n( 'design/admin/setup/rad/template' )}</label>
        <input type="text" id="twAuthor" name="author" value="{$wizard_settings.author|wash}" />
    </div>
    <div class="tw-field">
        <label for="twVendor">{'Composer vendor'|i18n( 'design/admin/setup/rad/template' )}</label>
        <input type="text" id="twVendor" name="vendor" value="{$wizard_settings.vendor|wash}" />
    </div>
    <div class="tw-field">
        <label for="twVersion">{'Version'|i18n( 'design/admin/setup/rad/template' )}</label>
        <input type="text" id="twVersion" name="version" value="{$wizard_settings.version|wash}" />
    </div>
</div>

<div class="tw-field">
    <label for="twLicence">{'Licence'|i18n( 'design/admin/setup/rad/template' )}</label>
    <select id="twLicence" name="licence">
    {foreach $wizard_licences as $tw_key => $tw_label}
        <option value="{$tw_key|wash}"{if eq( $wizard_settings.licence, $tw_key )} selected="selected"{/if}>{$tw_label|wash}</option>
    {/foreach}
    </select>
</div>
</div>

<div class="tw-card">
<h2>{'What goes in it'|i18n( 'design/admin/setup/rad/template' )}</h2>
{foreach $wizard_parts as $tw_key => $tw_part}
    <label class="tw-pick">
        <input type="checkbox" name="Parts[]" value="{$tw_key|wash}"{if $wizard_settings.parts[$tw_key]} checked="checked"{/if} />
        <span>
            <span class="tw-pick-label">{$tw_part.label|i18n( 'design/admin/setup/rad/template' )|wash}</span>
            <span class="tw-meta">{$tw_part.description|i18n( 'design/admin/setup/rad/template' )|wash}</span>
        </span>
    </label>
{/foreach}
</div>

</div>

<div class="tw-col">

<div class="tw-card">
<h2>{'What a template will write'|i18n( 'design/admin/setup/rad/template' )} ({$wizard_usage|count})</h2>
<span class="tw-meta">{'Exactly as it would appear in a template, with the parameters named above already in place.'|i18n( 'design/admin/setup/rad/template' )}</span>

{if $wizard_usage|count|eq( 0 )}
<p class="tw-meta">{'Name an operator, a function, a fetch or an alias and it appears here.'|i18n( 'design/admin/setup/rad/template' )}</p>
{/if}

{foreach $wizard_usage as $tw_use}
<div class="tw-use">
    <span class="tw-use-kind">{$tw_use.kind|wash}</span> <code>{$tw_use.name|wash}</code>
    <pre>{$tw_use.example|wash}</pre>
    <span class="tw-meta">{$tw_use.what|i18n( 'design/admin/setup/rad/template' )|wash}</span>
</div>
{/foreach}
</div>

<div class="tw-card">
<h2>{'Switching it on'|i18n( 'design/admin/setup/rad/template' )}</h2>
<span class="tw-meta">{'Add this to settings/override/site.ini.append.php. The extension brings its own site.ini adding itself to ExtensionAutoloadPath, which is the line that is forgotten and the usual reason an operator is reported unknown.'|i18n( 'design/admin/setup/rad/template' )}</span>
<pre>{$wizard_activation|wash}</pre>

<h3>{'Then'|i18n( 'design/admin/setup/rad/template' )}</h3>
<pre>php bin/php/ezpgenerateautoloads.php --extension={$wizard_settings.name|wash}
php bin/php/ezcache.php --clear-all</pre>
</div>

</div>
</div>

{if $wizard_file_count|gt( 0 )}
<div class="tw-card">
<h2>{'Every file, before it is written'|i18n( 'design/admin/setup/rad/template' )}</h2>
<span class="tw-meta">{$wizard_target|wash}</span>

<div class="tw-toolbar">
    <button type="button" class="tw-btn" id="twOpenAll">{'Open all'|i18n( 'design/admin/setup/rad/template' )}</button>
    <button type="button" class="tw-btn" id="twCloseAll">{'Close all'|i18n( 'design/admin/setup/rad/template' )}</button>
</div>

{foreach $wizard_files as $tw_file}
<div class="tw-file is-collapsed">
    <div class="tw-file-head">
        <span class="tw-file-toggle">+</span>
        <span class="tw-file-path">{$tw_file.path|wash}</span>
        <span class="tw-meta">{$tw_file.lines|wash} {'lines'|i18n( 'design/admin/setup/rad/template' )}, {$tw_file.bytes|wash} {'bytes'|i18n( 'design/admin/setup/rad/template' )}</span>
    </div>
    <pre>{$tw_file.contents|wash}</pre>
</div>
{/foreach}
</div>
{/if}

{* DESIGN: Content END *}</div></div></div>

<div class="controlbar">
{* DESIGN: Control bar START *}<div class="box-bc"><div class="box-ml">
<div class="block">
    {if $wizard_ready}
    <input class="defaultbutton" type="submit" name="CreateButton" value="{'Create in extension/'|i18n( 'design/admin/setup/rad/template' )}" />
    {else}
    <input class="button-disabled" type="submit" name="_Disabled" value="{'Create in extension/'|i18n( 'design/admin/setup/rad/template' )}" disabled="disabled" />
    {/if}

    {if $wizard_file_count|gt( 0 )}
        {if $wizard_can_archive}
    <input class="button" type="submit" name="DownloadButton" value="{'Download as zip'|i18n( 'design/admin/setup/rad/template' )}" />
        {/if}
    {/if}

    <input class="button" type="submit" name="PreviewButton" value="{'Refresh preview'|i18n( 'design/admin/setup/rad/template' )}" />
    <a class="tw-meta" href={'setup/rad'|ezurl}>{'Back to the RAD tools'|i18n( 'design/admin/setup/rad/template' )}</a>
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
        if ( divs[i].className.indexOf( 'tw-file' ) === 0 )
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
    var openAll = document.getElementById( 'twOpenAll' ), closeAll = document.getElementById( 'twCloseAll' );
    if ( openAll )  openAll.onclick  = function () { every( true ); return false; };
    if ( closeAll ) closeAll.onclick = function () { every( false ); return false; };
} )();
</script>
{/literal}
