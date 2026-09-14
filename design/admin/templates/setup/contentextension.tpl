{* The content extension wizard.

   A content class as a script rather than as an afternoon of clicking, plus
   the two other things that are about content rather than about machinery. *}

{literal}
<style type="text/css">
.exp-ce {
    --ce-ink: #1c1c1e; --ce-muted: #6a6a72; --ce-line: #e2e2e6;
    --ce-accent: #2d6cdf; --ce-ok: #1f8a4c; --ce-bad: #b4232c; --ce-gap: 1.6rem;
    color: var(--ce-ink);
}
.exp-ce h2 { font-size: 1.05rem; margin: 0 0 .15rem 0; }
.exp-ce h3 { font-size: .9rem; margin: 1rem 0 .3rem 0; color: var(--ce-muted); text-transform: uppercase; letter-spacing: .04em; }
.exp-ce .ce-meta { color: var(--ce-muted); font-size: .92em; white-space: normal; }
.exp-ce code, .exp-ce pre { font-family: Menlo, Consolas, "DejaVu Sans Mono", monospace; overflow-wrap: anywhere; }
.exp-ce pre { max-width: 100%; overflow-x: auto; white-space: pre; }
.exp-ce label, .exp-ce label * { white-space: normal; }

.ce-note {
    display: flex; gap: .6rem; align-items: flex-start;
    border: 1px solid var(--ce-line); border-left-width: 4px; border-radius: 6px;
    padding: .7rem .9rem; margin: 0 0 .8rem 0; background: #fcfcfd;
}
.ce-note.is-ok { border-left-color: var(--ce-ok); }
.ce-note.is-bad { border-left-color: var(--ce-bad); }
.ce-note.is-info { border-left-color: var(--ce-accent); }

.ce-card { border: 1px solid var(--ce-line); border-radius: 8px; background: #fff; padding: 1rem 1.1rem; margin: 0 0 var(--ce-gap) 0; min-width: 0; }
.ce-card > .ce-meta { display: block; padding-bottom: .9rem; }
.ce-card.is-off { opacity: .55; }
.ce-grid { display: flex; flex-wrap: wrap; gap: var(--ce-gap); align-items: flex-start; }
.ce-col { flex: 1 1 26rem; min-width: 0; }

.ce-field { display: flex; flex-direction: column; gap: .3rem; padding-bottom: .9rem; }
.ce-field label { font-size: .82em; color: var(--ce-muted); text-transform: uppercase; letter-spacing: .04em; font-weight: 600; }
.ce-field input[type=text], .ce-field select, .ce-field textarea {
    border: 1px solid var(--ce-line); border-radius: 6px; padding: .45rem .55rem;
    font: inherit; background: #fff; width: 100%; box-sizing: border-box;
}
.ce-field textarea { font-family: Menlo, Consolas, "DejaVu Sans Mono", monospace; font-size: .9em; min-height: 6rem; resize: vertical; }
.ce-field .ce-hint { font-size: .85em; color: var(--ce-muted); text-transform: none; letter-spacing: 0; font-weight: normal; }
.ce-row { display: flex; flex-wrap: wrap; gap: .9rem; }
.ce-row > .ce-field { flex: 1 1 9rem; }

.ce-pick { display: flex; gap: .6rem; align-items: flex-start; padding: .45rem .55rem; border-radius: 6px; }
.ce-pick:hover { background: #f6f7f9; }
.ce-pick input { margin-top: .25rem; flex: 0 0 auto; }
.ce-pick > span { flex: 1 1 auto; min-width: 0; }
.ce-pick .ce-pick-label { display: block; font-weight: 600; }
.ce-pick .ce-meta { display: block; }

.ce-scroll { overflow-x: auto; max-width: 100%; }
.ce-table { width: 100%; border-collapse: collapse; font-size: .92em; }
.ce-table th { text-align: left; font-size: .78em; text-transform: uppercase; letter-spacing: .04em; color: var(--ce-muted); border-bottom: 1px solid var(--ce-line); padding: .35rem .5rem; white-space: nowrap; }
.ce-table td { border-bottom: 1px solid #f2f2f4; padding: .35rem .5rem; }
.ce-table .is-bad { color: var(--ce-bad); font-weight: 600; }

.ce-types { display: flex; flex-wrap: wrap; gap: .3rem; padding-top: .3rem; }
.ce-types code { border: 1px solid var(--ce-line); border-radius: 4px; padding: .1rem .35rem; font-size: .85em; background: #fbfbfc; }

.ce-file { border: 1px solid var(--ce-line); border-radius: 8px; margin: 0 0 .6rem 0; background: #fff; }
.ce-file-head { display: flex; flex-wrap: wrap; align-items: center; gap: .6rem; padding: .55rem .8rem; cursor: pointer; }
.ce-file-head:hover { background: #f6f7f9; }
.ce-file-toggle { display: inline-flex; align-items: center; justify-content: center; width: 1.25rem; height: 1.25rem; border: 1px solid var(--ce-line); border-radius: 4px; font-weight: 700; background: #f4f4f5; line-height: 1; }
.ce-file-path { font-weight: 600; overflow-wrap: anywhere; min-width: 0; }
.ce-file pre { margin: 0; padding: .8rem 1rem; border-top: 1px solid var(--ce-line); background: #fbfbfc; font-size: .88em; line-height: 1.5; }
.ce-file.is-collapsed pre { display: none; }
.ce-toolbar { display: flex; flex-wrap: wrap; gap: .5rem; padding: .2rem 0 .9rem 0; }
.ce-btn { border: 1px solid var(--ce-line); background: #f4f4f5; border-radius: 6px; padding: .3rem .7rem; font: inherit; cursor: pointer; color: inherit; }
.ce-btn:hover { border-color: #9a9aa0; }
</style>
{/literal}

<form method="post" action={'setup/contentextension'|ezurl} name="ContentWizard">
<input type="hidden" name="Submitted" value="1" />

<div class="context-block exp-ce">

{* DESIGN: Header START *}<div class="box-header"><div class="box-ml">
<h1 class="context-title">{'Content extension wizard'|i18n( 'design/admin/setup/rad/content' )}</h1>
{* DESIGN: Mainline *}<div class="header-mainline"></div>
{* DESIGN: Header END *}</div></div>

{* DESIGN: Content START *}<div class="box-ml"><div class="box-mr"><div class="box-content">

<div class="context-attributes">
<p>{'A content class built in the admin exists on the machine it was built on and nowhere else, and the only record of how it was made is whatever somebody wrote down. Written as a script it is reviewable, re-runnable, and the same on every installation it is run on. The same extension can carry custom tags for rich text and a translation.'|i18n( 'design/admin/setup/rad/content' )}</p>
</div>

{foreach $wizard_feedback as $ce_note}
<div class="ce-note {if $ce_note.ok}is-ok{else}is-bad{/if}">
    <strong>{if $ce_note.ok}&#10003;{else}!{/if}</strong><span>{$ce_note.message|wash}</span>
</div>
{/foreach}

{foreach $wizard_problems as $ce_problem}
<div class="ce-note is-bad"><strong>!</strong><span>{$ce_problem|wash}</span></div>
{/foreach}

{if $wizard_written|count|gt( 0 )}
<div class="ce-note is-info"><strong>&#10003;</strong><span>{'Written to %target. Switch it on with the lines below, clear the caches, and run the class script once.'|i18n( 'design/admin/setup/rad/content',, hash( '%target', $wizard_target ) )}</span></div>
{/if}

{if $wizard_can_write|not}
<div class="ce-note is-info"><strong>i</strong><span>{'The web server cannot write into extension/, so this page can only hand you an archive.'|i18n( 'design/admin/setup/rad/content' )}</span></div>
{/if}

<div class="ce-card">
<h2>{'What this extension carries'|i18n( 'design/admin/setup/rad/content' )}</h2>
{foreach $wizard_topics as $ce_topic}
    <label class="ce-pick">
        <input type="checkbox" name="Parts[]" value="{$ce_topic.key|wash}"{if $ce_topic.chosen} checked="checked"{/if} />
        <span>
            <span class="ce-pick-label">{$ce_topic.label|i18n( 'design/admin/setup/rad/content' )|wash}</span>
            <span class="ce-meta">{$ce_topic.summary|i18n( 'design/admin/setup/rad/content' )|wash} {$ce_topic.what|i18n( 'design/admin/setup/rad/content' )|wash}</span>
        </span>
    </label>
{/foreach}
</div>

<div class="ce-grid">
<div class="ce-col">

<div class="ce-card{if $wizard_settings.parts.class|not} is-off{/if}">
<h2>{'The content class'|i18n( 'design/admin/setup/rad/content' )}</h2>
<span class="ce-meta">{'The identifier cannot be changed once content exists, and neither can an attribute datatype. Getting those right first is worth more than getting them quickly.'|i18n( 'design/admin/setup/rad/content' )}</span>

<div class="ce-row">
    <div class="ce-field">
        <label for="ceClass">{'Identifier'|i18n( 'design/admin/setup/rad/content' )}</label>
        <input type="text" id="ceClass" name="class" value="{$wizard_settings.class|wash}" placeholder="press_release" autocomplete="off" />
    </div>
    <div class="ce-field">
        <label for="ceClassName">{'Name'|i18n( 'design/admin/setup/rad/content' )}</label>
        <input type="text" id="ceClassName" name="class_name" value="{$wizard_settings.class_name|wash}" />
    </div>
    <div class="ce-field">
        <label for="ceGroup">{'Class group'|i18n( 'design/admin/setup/rad/content' )}</label>
        <select id="ceGroup" name="class_group">
        {foreach $wizard_groups as $ce_id => $ce_group}
            <option value="{$ce_id|wash}"{if eq( $wizard_settings.class_group, $ce_id )} selected="selected"{/if}>{$ce_group|wash}</option>
        {/foreach}
        </select>
        <span class="ce-hint">{'Without a group the class exists and appears nowhere.'|i18n( 'design/admin/setup/rad/content' )}</span>
    </div>
</div>

<div class="ce-field">
    <label for="ceAttributes">{'Attributes'|i18n( 'design/admin/setup/rad/content' )}</label>
    <textarea id="ceAttributes" name="attributes" rows="6" placeholder="title, ezstring, Title, required&#10;body, ezxmltext, Body&#10;published_at, ezdatetime, Published&#10;author_email, ezemail, Author, collect">{$wizard_raw.attributes|wash}</textarea>
    <span class="ce-hint">{'One per line: identifier, datatype, name, then any of required, nosearch, collect, notranslate.'|i18n( 'design/admin/setup/rad/content' )}</span>
</div>

<div class="ce-field">
    <label for="cePattern">{'Object name pattern'|i18n( 'design/admin/setup/rad/content' )}</label>
    <input type="text" id="cePattern" name="pattern" value="{$wizard_settings.pattern|wash}" placeholder="&lt;title&gt;" autocomplete="off" />
    <span class="ce-hint">{'What every object of this class is called. The name ends up in the url, and changing this later renames nothing that already exists.'|i18n( 'design/admin/setup/rad/content' )}</span>
</div>

{if $wizard_attributes|count|gt( 0 )}
<h3>{'As the script will make them'|i18n( 'design/admin/setup/rad/content' )}</h3>
<div class="ce-scroll">
<table class="ce-table">
<tr>
    <th>{'Identifier'|i18n( 'design/admin/setup/rad/content' )}</th>
    <th>{'Datatype'|i18n( 'design/admin/setup/rad/content' )}</th>
    <th>{'Name'|i18n( 'design/admin/setup/rad/content' )}</th>
    <th>{'Flags'|i18n( 'design/admin/setup/rad/content' )}</th>
</tr>
{foreach $wizard_attributes as $ce_attribute}
<tr>
    <td><code>{$ce_attribute.identifier|wash}</code></td>
    <td><code{if $ce_attribute.known|not} class="is-bad"{/if}>{$ce_attribute.type|wash}</code></td>
    <td>{$ce_attribute.name|wash}</td>
    <td class="ce-meta">{if $ce_attribute.required}required {/if}{if $ce_attribute.searchable}searchable {/if}{if $ce_attribute.collector}collects {/if}{if $ce_attribute.translatable|not}not translatable{/if}</td>
</tr>
{/foreach}
</table>
</div>
{/if}

<h3>{'Datatypes on this installation'|i18n( 'design/admin/setup/rad/content' )}</h3>
<div class="ce-types">
{foreach $wizard_datatypes as $ce_type}<code>{$ce_type.type|wash}</code>{/foreach}
</div>
</div>

</div>

<div class="ce-col">

<div class="ce-card{if $wizard_settings.parts.customtag|not} is-off{/if}">
<h2>{'Custom tags'|i18n( 'design/admin/setup/rad/content' )}</h2>
<span class="ce-meta">{'One per line: a name, a colon, then its attributes. Add the word inline for a tag that sits inside a paragraph rather than replacing one.'|i18n( 'design/admin/setup/rad/content' )}</span>

<div class="ce-field">
    <textarea name="tags" rows="4" placeholder="factbox: title&#10;highlight: inline">{$wizard_raw.tags|wash}</textarea>
    <span class="ce-hint">{'A template is written for each, and the ini that allows it. Anything not listed as an attribute cannot be set at all, which is the only validation a custom tag has.'|i18n( 'design/admin/setup/rad/content' )}</span>
</div>
</div>

<div class="ce-card{if $wizard_settings.parts.translation|not} is-off{/if}">
<h2>{'Translation'|i18n( 'design/admin/setup/rad/content' )}</h2>

<div class="ce-field">
    <label for="ceLocale">{'Locale'|i18n( 'design/admin/setup/rad/content' )}</label>
    <input type="text" id="ceLocale" name="locale" value="{$wizard_settings.locale|wash}" placeholder="nor-NO" autocomplete="off" />
    <span class="ce-hint">{'Three letters, a dash, two letters.'|i18n( 'design/admin/setup/rad/content' )}</span>
</div>

<div class="ce-field">
    <label for="ceStrings">{'Strings'|i18n( 'design/admin/setup/rad/content' )}</label>
    <textarea id="ceStrings" name="strings" rows="5" placeholder="design/standard/content|Read more&#10;extension/my_ext|Send">{$wizard_raw.strings|wash}</textarea>
    <span class="ce-hint">{'One per line: the context, a vertical bar, then the English. Every one is written unfinished, so the file changes nothing until it is filled in.'|i18n( 'design/admin/setup/rad/content' )}</span>
</div>
</div>

<div class="ce-card">
<h2>{'The extension'|i18n( 'design/admin/setup/rad/content' )}</h2>

<div class="ce-field">
    <label for="ceName">{'Extension name'|i18n( 'design/admin/setup/rad/content' )}</label>
    <input type="text" id="ceName" name="name" value="{$wizard_settings.name|wash}" placeholder="my_content" autocomplete="off" />
</div>

<div class="ce-field">
    <label for="ceTitle">{'Title'|i18n( 'design/admin/setup/rad/content' )}</label>
    <input type="text" id="ceTitle" name="title" value="{$wizard_settings.title|wash}" />
</div>

<div class="ce-field">
    <label for="ceSummary">{'Summary'|i18n( 'design/admin/setup/rad/content' )}</label>
    <input type="text" id="ceSummary" name="summary" value="{$wizard_settings.summary|wash}" />
</div>

<div class="ce-row">
    <div class="ce-field">
        <label for="ceAuthor">{'Author'|i18n( 'design/admin/setup/rad/content' )}</label>
        <input type="text" id="ceAuthor" name="author" value="{$wizard_settings.author|wash}" />
    </div>
    <div class="ce-field">
        <label for="ceVendor">{'Composer vendor'|i18n( 'design/admin/setup/rad/content' )}</label>
        <input type="text" id="ceVendor" name="vendor" value="{$wizard_settings.vendor|wash}" />
    </div>
    <div class="ce-field">
        <label for="ceVersion">{'Version'|i18n( 'design/admin/setup/rad/content' )}</label>
        <input type="text" id="ceVersion" name="version" value="{$wizard_settings.version|wash}" />
    </div>
</div>

<div class="ce-field">
    <label for="ceLicence">{'Licence'|i18n( 'design/admin/setup/rad/content' )}</label>
    <select id="ceLicence" name="licence">
    {foreach $wizard_licences as $ce_key => $ce_label}
        <option value="{$ce_key|wash}"{if eq( $wizard_settings.licence, $ce_key )} selected="selected"{/if}>{$ce_label|wash}</option>
    {/foreach}
    </select>
</div>

<h3>{'What else goes in it'|i18n( 'design/admin/setup/rad/content' )}</h3>
{foreach $wizard_parts as $ce_key => $ce_part}
    {if or( eq( $ce_key, 'settings' ), eq( $ce_key, 'readme' ), eq( $ce_key, 'ezinfo' ), eq( $ce_key, 'extension_xml' ), eq( $ce_key, 'composer' ), eq( $ce_key, 'gitignore' ), eq( $ce_key, 'licence' ) )}
    <label class="ce-pick">
        <input type="checkbox" name="Parts[]" value="{$ce_key|wash}"{if $wizard_settings.parts[$ce_key]} checked="checked"{/if} />
        <span>
            <span class="ce-pick-label">{$ce_part.label|i18n( 'design/admin/setup/rad/content' )|wash}</span>
            <span class="ce-meta">{$ce_part.description|i18n( 'design/admin/setup/rad/content' )|wash}</span>
        </span>
    </label>
    {/if}
{/foreach}
</div>

<div class="ce-card">
<h2>{'Switching it on'|i18n( 'design/admin/setup/rad/content' )}</h2>
<pre>{$wizard_activation|wash}</pre>
{if $wizard_settings.class|ne( '' )}
<h3>{'Then, once'|i18n( 'design/admin/setup/rad/content' )}</h3>
<pre>php extension/{$wizard_settings.name|wash}/bin/install_{$wizard_settings.class|wash}.php -s &lt;siteaccess&gt;
php bin/php/ezcache.php --clear-all</pre>
{/if}
</div>

</div>
</div>

{if $wizard_file_count|gt( 0 )}
<div class="ce-card">
<h2>{'Every file, before it is written'|i18n( 'design/admin/setup/rad/content' )}</h2>
<span class="ce-meta">{$wizard_target|wash}</span>

<div class="ce-toolbar">
    <button type="button" class="ce-btn" id="ceOpenAll">{'Open all'|i18n( 'design/admin/setup/rad/content' )}</button>
    <button type="button" class="ce-btn" id="ceCloseAll">{'Close all'|i18n( 'design/admin/setup/rad/content' )}</button>
</div>

{foreach $wizard_files as $ce_file}
<div class="ce-file is-collapsed">
    <div class="ce-file-head">
        <span class="ce-file-toggle">+</span>
        <span class="ce-file-path">{$ce_file.path|wash}</span>
        <span class="ce-meta">{$ce_file.lines|wash} {'lines'|i18n( 'design/admin/setup/rad/content' )}, {$ce_file.bytes|wash} {'bytes'|i18n( 'design/admin/setup/rad/content' )}</span>
    </div>
    <pre>{$ce_file.contents|wash}</pre>
</div>
{/foreach}
</div>
{/if}

{* DESIGN: Content END *}</div></div></div>

<div class="controlbar">
{* DESIGN: Control bar START *}<div class="box-bc"><div class="box-ml">
<div class="block">
    {if $wizard_ready}
    <input class="defaultbutton" type="submit" name="CreateButton" value="{'Create in extension/'|i18n( 'design/admin/setup/rad/content' )}" />
    {else}
    <input class="button-disabled" type="submit" name="_Disabled" value="{'Create in extension/'|i18n( 'design/admin/setup/rad/content' )}" disabled="disabled" />
    {/if}

    {if $wizard_file_count|gt( 0 )}
        {if $wizard_can_archive}
    <input class="button" type="submit" name="DownloadButton" value="{'Download as zip'|i18n( 'design/admin/setup/rad/content' )}" />
        {/if}
    {/if}

    <input class="button" type="submit" name="PreviewButton" value="{'Refresh preview'|i18n( 'design/admin/setup/rad/content' )}" />
    <a class="ce-meta" href={'setup/rad'|ezurl}>{'Back to the RAD tools'|i18n( 'design/admin/setup/rad/content' )}</a>
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
        if ( divs[i].className.indexOf( 'ce-file' ) === 0 )
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
    var openAll = document.getElementById( 'ceOpenAll' ), closeAll = document.getElementById( 'ceCloseAll' );
    if ( openAll )  openAll.onclick  = function () { every( true ); return false; };
    if ( closeAll ) closeAll.onclick = function () { every( false ); return false; };
} )();
</script>
{/literal}
