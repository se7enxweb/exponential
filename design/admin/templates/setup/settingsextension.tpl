{* The settings extension wizard.

   Six things that are told in settings rather than in code. None of them is
   hard; all of them are in a shape nobody remembers. *}

{literal}
<style type="text/css">
.exp-se {
    --se-ink: #1c1c1e; --se-muted: #6a6a72; --se-line: #e2e2e6;
    --se-accent: #2d6cdf; --se-ok: #1f8a4c; --se-bad: #b4232c; --se-gap: 1.6rem;
    color: var(--se-ink);
}
.exp-se h2 { font-size: 1.05rem; margin: 0 0 .15rem 0; }
.exp-se h3 { font-size: .9rem; margin: 1rem 0 .3rem 0; color: var(--se-muted); text-transform: uppercase; letter-spacing: .04em; }
.exp-se .se-meta { color: var(--se-muted); font-size: .92em; white-space: normal; }
.exp-se code, .exp-se pre { font-family: Menlo, Consolas, "DejaVu Sans Mono", monospace; overflow-wrap: anywhere; }
.exp-se pre { max-width: 100%; overflow-x: auto; white-space: pre; }
.exp-se label, .exp-se label * { white-space: normal; }

.se-note {
    display: flex; gap: .6rem; align-items: flex-start;
    border: 1px solid var(--se-line); border-left-width: 4px; border-radius: 6px;
    padding: .7rem .9rem; margin: 0 0 .8rem 0; background: #fcfcfd;
}
.se-note.is-ok { border-left-color: var(--se-ok); }
.se-note.is-bad { border-left-color: var(--se-bad); }
.se-note.is-info { border-left-color: var(--se-accent); }

.se-card { border: 1px solid var(--se-line); border-radius: 8px; background: #fff; padding: 1rem 1.1rem; margin: 0 0 var(--se-gap) 0; min-width: 0; }
.se-card > .se-meta { display: block; padding-bottom: .9rem; }
.se-card.is-off { opacity: .55; }
.se-grid { display: flex; flex-wrap: wrap; gap: var(--se-gap); align-items: flex-start; }
.se-col { flex: 1 1 26rem; min-width: 0; }

.se-field { display: flex; flex-direction: column; gap: .3rem; padding-bottom: .9rem; }
.se-field label { font-size: .82em; color: var(--se-muted); text-transform: uppercase; letter-spacing: .04em; font-weight: 600; }
.se-field input[type=text], .se-field select, .se-field textarea {
    border: 1px solid var(--se-line); border-radius: 6px; padding: .45rem .55rem;
    font: inherit; background: #fff; width: 100%; box-sizing: border-box;
}
.se-field textarea { font-family: Menlo, Consolas, "DejaVu Sans Mono", monospace; font-size: .9em; min-height: 5rem; resize: vertical; }
.se-field .se-hint { font-size: .85em; color: var(--se-muted); text-transform: none; letter-spacing: 0; font-weight: normal; }
.se-row { display: flex; flex-wrap: wrap; gap: .9rem; }
.se-row > .se-field { flex: 1 1 9rem; }

.se-pick { display: flex; gap: .6rem; align-items: flex-start; padding: .45rem .55rem; border-radius: 6px; }
.se-pick:hover { background: #f6f7f9; }
.se-pick input { margin-top: .25rem; flex: 0 0 auto; }
.se-pick > span { flex: 1 1 auto; min-width: 0; }
.se-pick .se-pick-label { display: block; font-weight: 600; }
.se-pick .se-meta { display: block; }
.se-kind { font-size: .72em; letter-spacing: .05em; text-transform: uppercase; font-weight: 700; color: #fff; background: var(--se-muted); border-radius: 3px; padding: .05rem .3rem; vertical-align: .1em; }
.se-kind.is-filter { background: var(--se-accent); }

.se-ref { border-top: 1px solid #f0f0f3; padding: .35rem 0; display: flex; flex-wrap: wrap; gap: .5rem; align-items: baseline; }
.se-ref code { flex: 0 0 13rem; font-weight: 600; }
.se-ref .se-meta { flex: 1 1 12rem; min-width: 0; }

.se-file { border: 1px solid var(--se-line); border-radius: 8px; margin: 0 0 .6rem 0; background: #fff; }
.se-file-head { display: flex; flex-wrap: wrap; align-items: center; gap: .6rem; padding: .55rem .8rem; cursor: pointer; }
.se-file-head:hover { background: #f6f7f9; }
.se-file-toggle { display: inline-flex; align-items: center; justify-content: center; width: 1.25rem; height: 1.25rem; border: 1px solid var(--se-line); border-radius: 4px; font-weight: 700; background: #f4f4f5; line-height: 1; }
.se-file-path { font-weight: 600; overflow-wrap: anywhere; min-width: 0; }
.se-file pre { margin: 0; padding: .8rem 1rem; border-top: 1px solid var(--se-line); background: #fbfbfc; font-size: .88em; line-height: 1.5; }
.se-file.is-collapsed pre { display: none; }
.se-toolbar { display: flex; flex-wrap: wrap; gap: .5rem; padding: .2rem 0 .9rem 0; }
.se-btn { border: 1px solid var(--se-line); background: #f4f4f5; border-radius: 6px; padding: .3rem .7rem; font: inherit; cursor: pointer; color: inherit; }
.se-btn:hover { border-color: #9a9aa0; }
</style>
{/literal}

<form method="post" action={'setup/settingsextension'|ezurl} name="SettingsWizard">
<input type="hidden" name="Submitted" value="1" />

<div class="context-block exp-se">

{* DESIGN: Header START *}<div class="box-header"><div class="box-ml">
<h1 class="context-title">{'Settings extension wizard'|i18n( 'design/admin/setup/rad/settings' )}</h1>
{* DESIGN: Mainline *}<div class="header-mainline"></div>
{* DESIGN: Header END *}</div></div>

{* DESIGN: Content START *}<div class="box-ml"><div class="box-mr"><div class="box-content">

<div class="context-attributes">
<p>{'A good deal of what this system can be told to do differently is told in settings rather than in code. None of it is hard; all of it is in a shape nobody remembers, spread over half a dozen files, with a rule about where the file has to live for anything to read it at all. Tick what this extension should say and the files are written.'|i18n( 'design/admin/setup/rad/settings' )}</p>
</div>

{foreach $wizard_feedback as $se_note}
<div class="se-note {if $se_note.ok}is-ok{else}is-bad{/if}">
    <strong>{if $se_note.ok}&#10003;{else}!{/if}</strong><span>{$se_note.message|wash}</span>
</div>
{/foreach}

{foreach $wizard_problems as $se_problem}
<div class="se-note is-bad"><strong>!</strong><span>{$se_problem|wash}</span></div>
{/foreach}

{if $wizard_written|count|gt( 0 )}
<div class="se-note is-info"><strong>&#10003;</strong><span>{'Written to %target. Switch it on with the lines below and clear the caches.'|i18n( 'design/admin/setup/rad/settings',, hash( '%target', $wizard_target ) )}</span></div>
{/if}

{if $wizard_can_write|not}
<div class="se-note is-info"><strong>i</strong><span>{'The web server cannot write into extension/, so this page can only hand you an archive.'|i18n( 'design/admin/setup/rad/settings' )}</span></div>
{/if}

<div class="se-card">
<h2>{'What this extension says'|i18n( 'design/admin/setup/rad/settings' )}</h2>
<span class="se-meta">{'Each of these is one ini file. Tick one and the questions for it are below.'|i18n( 'design/admin/setup/rad/settings' )}</span>

{foreach $wizard_topics as $se_topic}
    <label class="se-pick">
        <input type="checkbox" name="Parts[]" value="{$se_topic.key|wash}"{if $se_topic.chosen} checked="checked"{/if} />
        <span>
            <span class="se-pick-label">{$se_topic.label|i18n( 'design/admin/setup/rad/settings' )|wash} <code class="se-meta">{$se_topic.ini|wash}</code></span>
            <span class="se-meta">{$se_topic.summary|i18n( 'design/admin/setup/rad/settings' )|wash} {$se_topic.what|i18n( 'design/admin/setup/rad/settings' )|wash}</span>
        </span>
    </label>
{/foreach}
</div>

<div class="se-grid">
<div class="se-col">

<div class="se-card{if $wizard_settings.parts.image|not} is-off{/if}">
<h2>{'Image aliases'|i18n( 'design/admin/setup/rad/settings' )}</h2>
<span class="se-meta">{'One per line: a name, a colon, then the filters separated by commas. Arguments follow the filter after an = sign.'|i18n( 'design/admin/setup/rad/settings' )}</span>

<div class="se-field">
    <textarea name="aliases" rows="5" placeholder="card: geometry/scaledownonly=320;240&#10;square: geometry/scaleexact=200;200, colorspace/gray">{$wizard_raw.aliases|wash}</textarea>
</div>

<h3>{'Filters that ship'|i18n( 'design/admin/setup/rad/settings' )}</h3>
{foreach $wizard_filters as $se_name => $se_filter}
<div class="se-ref">
    <code>{$se_name|wash}{if ne( $se_filter.takes, '' )}={$se_filter.takes|wash}{/if}</code>
    <span class="se-meta">{$se_filter.what|i18n( 'design/admin/setup/rad/settings' )|wash}</span>
</div>
{/foreach}
</div>

<div class="se-card{if $wizard_settings.parts.viewcache|not} is-off{/if}">
<h2>{'View cache clearing rules'|i18n( 'design/admin/setup/rad/settings' )}</h2>
<span class="se-meta">{'One per line: a content class identifier, a colon, then the methods. Any word that is not a method is taken as a class identifier this one depends on.'|i18n( 'design/admin/setup/rad/settings' )}</span>

<div class="se-field">
    <textarea name="rules" rows="4" placeholder="comment: relating, parent, forum_topic&#10;article: siblings">{$wizard_raw.rules|wash}</textarea>
</div>

<h3>{'Methods'|i18n( 'design/admin/setup/rad/settings' )}</h3>
{foreach $wizard_methods as $se_key => $se_what}
<div class="se-ref">
    <code>{$se_key|wash}</code>
    <span class="se-meta">{$se_what|i18n( 'design/admin/setup/rad/settings' )|wash}</span>
</div>
{/foreach}
</div>

<div class="se-card{if $wizard_settings.parts.collect|not} is-off{/if}">
<h2>{'Information collection'|i18n( 'design/admin/setup/rad/settings' )}</h2>
<span class="se-meta">{'One per line: a content class identifier, a type, and the word nomail if it should not be emailed.'|i18n( 'design/admin/setup/rad/settings' )}</span>

<div class="se-field">
    <textarea name="forms" rows="3" placeholder="contact_form form&#10;reader_poll poll nomail">{$wizard_raw.forms|wash}</textarea>
</div>

<h3>{'Types'|i18n( 'design/admin/setup/rad/settings' )}</h3>
{foreach $wizard_types as $se_key => $se_what}
<div class="se-ref">
    <code>{$se_key|wash}</code>
    <span class="se-meta">{$se_what|i18n( 'design/admin/setup/rad/settings' )|wash}</span>
</div>
{/foreach}
</div>

</div>

<div class="se-col">

<div class="se-card{if $wizard_settings.parts.event|not} is-off{/if}">
<h2>{'Event listeners'|i18n( 'design/admin/setup/rad/settings' )}</h2>
<span class="se-meta">{'A method is written for each of these. A filter event uses what the method returns, so one that forgets to return the value destroys it; a notify event ignores it.'|i18n( 'design/admin/setup/rad/settings' )}</span>

{foreach $wizard_event_groups as $se_group}
<h3>{$se_group.group|wash}</h3>
{foreach $se_group.events as $se_event}
    <label class="se-pick">
        <input type="checkbox" name="Events[]" value="{$se_event.event|wash}"{if $se_event.chosen} checked="checked"{/if} />
        <span>
            <span class="se-pick-label"><code>{$se_event.event|wash}</code> <span class="se-kind{if eq( $se_event.kind, 'filter' )} is-filter{/if}">{$se_event.kind|wash}</span></span>
            <span class="se-meta">{$se_event.what|i18n( 'design/admin/setup/rad/settings' )|wash}</span>
        </span>
    </label>
{/foreach}
{/foreach}
</div>

<div class="se-card{if $wizard_settings.parts.trigger|not} is-off{/if}">
<h2>{'Trigger operations'|i18n( 'design/admin/setup/rad/settings' )}</h2>
<span class="se-meta">{'Which operations a workflow may be bound to in the admin. Separated by commas, spaces or newlines.'|i18n( 'design/admin/setup/rad/settings' )}</span>

<div class="se-field">
    <input type="text" name="operations" value="{$wizard_raw.operations|wash}" placeholder="content_delete, content_hide, user_activation" autocomplete="off" />
    <span class="se-hint">{'Listing an operation makes it bindable and binds nothing. The binding is a row in the database, done in the admin, and does not travel with the extension.'|i18n( 'design/admin/setup/rad/settings' )}</span>
</div>
</div>

<div class="se-card{if $wizard_settings.parts.siteaccess|not} is-off{/if}">
<h2>{'Siteaccess settings'|i18n( 'design/admin/setup/rad/settings' )}</h2>
<span class="se-meta">{'Settings that apply to one siteaccess only and travel with this extension rather than living in settings/.'|i18n( 'design/admin/setup/rad/settings' )}</span>

<div class="se-field">
    <label for="seSiteaccess">{'Siteaccess'|i18n( 'design/admin/setup/rad/settings' )}</label>
    <input type="text" id="seSiteaccess" name="siteaccess" value="{$wizard_settings.siteaccess|wash}" list="seSiteaccessList" autocomplete="off" />
    <datalist id="seSiteaccessList">
    {foreach $wizard_siteaccess_list as $se_sa}<option value="{$se_sa|wash}"></option>{/foreach}
    </datalist>
</div>

<div class="se-field">
    <label for="seOverrides">{'Settings'|i18n( 'design/admin/setup/rad/settings' )}</label>
    <textarea id="seOverrides" name="overrides" rows="4" placeholder="site.ini [SiteSettings] DefaultPage=content/view/full/2&#10;site.ini [RegionalSettings] Locale=eng-GB">{$wizard_raw.overrides|wash}</textarea>
    <span class="se-hint">{'One per line, as: file.ini [Section] Setting=value'|i18n( 'design/admin/setup/rad/settings' )}</span>
</div>
</div>

<div class="se-card">
<h2>{'The extension'|i18n( 'design/admin/setup/rad/settings' )}</h2>

<div class="se-row">
    <div class="se-field">
        <label for="seName">{'Extension name'|i18n( 'design/admin/setup/rad/settings' )}</label>
        <input type="text" id="seName" name="name" value="{$wizard_settings.name|wash}" placeholder="my_settings" autocomplete="off" />
    </div>
    <div class="se-field">
        <label for="seClass">{'Listener class'|i18n( 'design/admin/setup/rad/settings' )}</label>
        <input type="text" id="seClass" name="class" value="{$wizard_settings.class|wash}" autocomplete="off" />
    </div>
</div>

<div class="se-field">
    <label for="seTitle">{'Title'|i18n( 'design/admin/setup/rad/settings' )}</label>
    <input type="text" id="seTitle" name="title" value="{$wizard_settings.title|wash}" />
</div>

<div class="se-field">
    <label for="seSummary">{'Summary'|i18n( 'design/admin/setup/rad/settings' )}</label>
    <input type="text" id="seSummary" name="summary" value="{$wizard_settings.summary|wash}" />
</div>

<div class="se-row">
    <div class="se-field">
        <label for="seAuthor">{'Author'|i18n( 'design/admin/setup/rad/settings' )}</label>
        <input type="text" id="seAuthor" name="author" value="{$wizard_settings.author|wash}" />
    </div>
    <div class="se-field">
        <label for="seVendor">{'Composer vendor'|i18n( 'design/admin/setup/rad/settings' )}</label>
        <input type="text" id="seVendor" name="vendor" value="{$wizard_settings.vendor|wash}" />
    </div>
    <div class="se-field">
        <label for="seVersion">{'Version'|i18n( 'design/admin/setup/rad/settings' )}</label>
        <input type="text" id="seVersion" name="version" value="{$wizard_settings.version|wash}" />
    </div>
</div>

<div class="se-field">
    <label for="seLicence">{'Licence'|i18n( 'design/admin/setup/rad/settings' )}</label>
    <select id="seLicence" name="licence">
    {foreach $wizard_licences as $se_key => $se_label}
        <option value="{$se_key|wash}"{if eq( $wizard_settings.licence, $se_key )} selected="selected"{/if}>{$se_label|wash}</option>
    {/foreach}
    </select>
</div>

<h3>{'What else goes in it'|i18n( 'design/admin/setup/rad/settings' )}</h3>
{foreach $wizard_parts as $se_key => $se_part}
    {if or( eq( $se_key, 'listener' ), eq( $se_key, 'readme' ), eq( $se_key, 'ezinfo' ), eq( $se_key, 'extension_xml' ), eq( $se_key, 'composer' ), eq( $se_key, 'gitignore' ), eq( $se_key, 'licence' ) )}
    <label class="se-pick">
        <input type="checkbox" name="Parts[]" value="{$se_key|wash}"{if $wizard_settings.parts[$se_key]} checked="checked"{/if} />
        <span>
            <span class="se-pick-label">{$se_part.label|i18n( 'design/admin/setup/rad/settings' )|wash}</span>
            <span class="se-meta">{$se_part.description|i18n( 'design/admin/setup/rad/settings' )|wash}</span>
        </span>
    </label>
    {/if}
{/foreach}
</div>

<div class="se-card">
<h2>{'Switching it on'|i18n( 'design/admin/setup/rad/settings' )}</h2>
<pre>{$wizard_activation|wash}</pre>
<h3>{'Then'|i18n( 'design/admin/setup/rad/settings' )}</h3>
<pre>php bin/php/ezcache.php --clear-all</pre>
</div>

</div>
</div>

{if $wizard_file_count|gt( 0 )}
<div class="se-card">
<h2>{'Every file, before it is written'|i18n( 'design/admin/setup/rad/settings' )}</h2>
<span class="se-meta">{$wizard_target|wash}</span>

<div class="se-toolbar">
    <button type="button" class="se-btn" id="seOpenAll">{'Open all'|i18n( 'design/admin/setup/rad/settings' )}</button>
    <button type="button" class="se-btn" id="seCloseAll">{'Close all'|i18n( 'design/admin/setup/rad/settings' )}</button>
</div>

{foreach $wizard_files as $se_file}
<div class="se-file is-collapsed">
    <div class="se-file-head">
        <span class="se-file-toggle">+</span>
        <span class="se-file-path">{$se_file.path|wash}</span>
        <span class="se-meta">{$se_file.lines|wash} {'lines'|i18n( 'design/admin/setup/rad/settings' )}, {$se_file.bytes|wash} {'bytes'|i18n( 'design/admin/setup/rad/settings' )}</span>
    </div>
    <pre>{$se_file.contents|wash}</pre>
</div>
{/foreach}
</div>
{/if}

{* DESIGN: Content END *}</div></div></div>

<div class="controlbar">
{* DESIGN: Control bar START *}<div class="box-bc"><div class="box-ml">
<div class="block">
    {if $wizard_ready}
    <input class="defaultbutton" type="submit" name="CreateButton" value="{'Create in extension/'|i18n( 'design/admin/setup/rad/settings' )}" />
    {else}
    <input class="button-disabled" type="submit" name="_Disabled" value="{'Create in extension/'|i18n( 'design/admin/setup/rad/settings' )}" disabled="disabled" />
    {/if}

    {if $wizard_file_count|gt( 0 )}
        {if $wizard_can_archive}
    <input class="button" type="submit" name="DownloadButton" value="{'Download as zip'|i18n( 'design/admin/setup/rad/settings' )}" />
        {/if}
    {/if}

    <input class="button" type="submit" name="PreviewButton" value="{'Refresh preview'|i18n( 'design/admin/setup/rad/settings' )}" />
    <a class="se-meta" href={'setup/rad'|ezurl}>{'Back to the RAD tools'|i18n( 'design/admin/setup/rad/settings' )}</a>
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
        if ( divs[i].className.indexOf( 'se-file' ) === 0 )
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
    var openAll = document.getElementById( 'seOpenAll' ), closeAll = document.getElementById( 'seCloseAll' );
    if ( openAll )  openAll.onclick  = function () { every( true ); return false; };
    if ( closeAll ) closeAll.onclick = function () { every( false ); return false; };
} )();
</script>
{/literal}
