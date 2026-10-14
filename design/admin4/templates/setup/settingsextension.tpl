{* The settings extension wizard.

   Six things that are told in settings rather than in code. None of them is
   hard; all of them are in a shape nobody remembers. *}


<form method="post" action={'setup/settingsextension'|ezurl} name="SettingsWizard">
<input type="hidden" name="Submitted" value="1" />

<div class="context-block a4-page a4-setup-wz">

{* DESIGN: Header START *}<div class="box-header"><div class="box-ml">
<h1 class="context-title">{'Settings extension wizard'|i18n( 'design/admin/setup/rad/settings' )}</h1>
{* DESIGN: Mainline *}<div class="header-mainline"></div>
{* DESIGN: Header END *}</div></div>

{* DESIGN: Content START *}<div class="box-ml"><div class="box-mr"><div class="box-content">

<div class="context-attributes">
<p>{'A good deal of what this system can be told to do differently is told in settings rather than in code. None of it is hard; all of it is in a shape nobody remembers, spread over half a dozen files, with a rule about where the file has to live for anything to read it at all. Tick what this extension should say and the files are written.'|i18n( 'design/admin/setup/rad/settings' )}</p>
</div>

{foreach $wizard_feedback as $se_note}
<div class="a4-notice {if $se_note.ok}is-ok{else}is-bad{/if}">
    <strong>{if $se_note.ok}&#10003;{else}!{/if}</strong><span>{$se_note.message|wash}</span>
</div>
{/foreach}

{foreach $wizard_problems as $se_problem}
<div class="a4-notice is-bad"><strong>!</strong><span>{$se_problem|wash}</span></div>
{/foreach}

{if $wizard_written|count|gt( 0 )}
<div class="a4-notice is-info"><strong>&#10003;</strong><span>{'Written to %target. Switch it on with the lines below and clear the caches.'|i18n( 'design/admin/setup/rad/settings',, hash( '%target', $wizard_target ) )}</span></div>
{/if}

{if $wizard_can_write|not}
<div class="a4-notice is-info"><strong>i</strong><span>{'The web server cannot write into extension/, so this page can only hand you an archive.'|i18n( 'design/admin/setup/rad/settings' )}</span></div>
{/if}

<div class="a4-card">
<h2>{'What this extension says'|i18n( 'design/admin/setup/rad/settings' )}</h2>
<span class="a4-meta">{'Each of these is one ini file. Tick one and the questions for it are below.'|i18n( 'design/admin/setup/rad/settings' )}</span>

{foreach $wizard_topics as $se_topic}
    <label class="a4-setup-wz-pick">
        <input type="checkbox" name="Parts[]" value="{$se_topic.key|wash}"{if $se_topic.chosen} checked="checked"{/if} />
        <span>
            <span class="a4-setup-wz-pick-label">{$se_topic.label|i18n( 'design/admin/setup/rad/settings' )|wash} <code class="a4-meta">{$se_topic.ini|wash}</code></span>
            <span class="a4-meta">{$se_topic.summary|i18n( 'design/admin/setup/rad/settings' )|wash} {$se_topic.what|i18n( 'design/admin/setup/rad/settings' )|wash}</span>
        </span>
    </label>
{/foreach}
</div>

<div class="a4-setup-wz-grid">
<div class="a4-setup-wz-col">

<div class="a4-card{if $wizard_settings.parts.image|not} is-off{/if}">
<h2>{'Image aliases'|i18n( 'design/admin/setup/rad/settings' )}</h2>
<span class="a4-meta">{'One per line: a name, a colon, then the filters separated by commas. Arguments follow the filter after an = sign.'|i18n( 'design/admin/setup/rad/settings' )}</span>

<div class="a4-field">
    <textarea name="aliases" rows="5" placeholder="card: geometry/scaledownonly=320;240&#10;square: geometry/scaleexact=200;200, colorspace/gray">{$wizard_raw.aliases|wash}</textarea>
</div>

<h3>{'Filters that ship'|i18n( 'design/admin/setup/rad/settings' )}</h3>
{foreach $wizard_filters as $se_name => $se_filter}
<div class="a4-setup-wz-ref">
    <code>{$se_name|wash}{if ne( $se_filter.takes, '' )}={$se_filter.takes|wash}{/if}</code>
    <span class="a4-meta">{$se_filter.what|i18n( 'design/admin/setup/rad/settings' )|wash}</span>
</div>
{/foreach}
</div>

<div class="a4-card{if $wizard_settings.parts.viewcache|not} is-off{/if}">
<h2>{'View cache clearing rules'|i18n( 'design/admin/setup/rad/settings' )}</h2>
<span class="a4-meta">{'One per line: a content class identifier, a colon, then the methods. Any word that is not a method is taken as a class identifier this one depends on.'|i18n( 'design/admin/setup/rad/settings' )}</span>

<div class="a4-field">
    <textarea name="rules" rows="4" placeholder="comment: relating, parent, forum_topic&#10;article: siblings">{$wizard_raw.rules|wash}</textarea>
</div>

<h3>{'Methods'|i18n( 'design/admin/setup/rad/settings' )}</h3>
{foreach $wizard_methods as $se_key => $se_what}
<div class="a4-setup-wz-ref">
    <code>{$se_key|wash}</code>
    <span class="a4-meta">{$se_what|i18n( 'design/admin/setup/rad/settings' )|wash}</span>
</div>
{/foreach}
</div>

<div class="a4-card{if $wizard_settings.parts.collect|not} is-off{/if}">
<h2>{'Information collection'|i18n( 'design/admin/setup/rad/settings' )}</h2>
<span class="a4-meta">{'One per line: a content class identifier, a type, and the word nomail if it should not be emailed.'|i18n( 'design/admin/setup/rad/settings' )}</span>

<div class="a4-field">
    <textarea name="forms" rows="3" placeholder="contact_form form&#10;reader_poll poll nomail">{$wizard_raw.forms|wash}</textarea>
</div>

<h3>{'Types'|i18n( 'design/admin/setup/rad/settings' )}</h3>
{foreach $wizard_types as $se_key => $se_what}
<div class="a4-setup-wz-ref">
    <code>{$se_key|wash}</code>
    <span class="a4-meta">{$se_what|i18n( 'design/admin/setup/rad/settings' )|wash}</span>
</div>
{/foreach}
</div>

</div>

<div class="a4-setup-wz-col">

<div class="a4-card{if $wizard_settings.parts.event|not} is-off{/if}">
<h2>{'Event listeners'|i18n( 'design/admin/setup/rad/settings' )}</h2>
<span class="a4-meta">{'A method is written for each of these. A filter event uses what the method returns, so one that forgets to return the value destroys it; a notify event ignores it.'|i18n( 'design/admin/setup/rad/settings' )}</span>

{foreach $wizard_event_groups as $se_group}
<h3>{$se_group.group|wash}</h3>
{foreach $se_group.events as $se_event}
    <label class="a4-setup-wz-pick">
        <input type="checkbox" name="Events[]" value="{$se_event.event|wash}"{if $se_event.chosen} checked="checked"{/if} />
        <span>
            <span class="a4-setup-wz-pick-label"><code>{$se_event.event|wash}</code> <span class="a4-setup-wz-tag is-muted{if eq( $se_event.kind, 'filter' )} is-filter{/if}">{$se_event.kind|wash}</span></span>
            <span class="a4-meta">{if $se_event.described}{$se_event.what|i18n( 'design/admin/setup/rad/settings' )|wash}{else}{$se_event.what|wash}{/if}</span>
        </span>
    </label>
{/foreach}
{/foreach}
</div>

<div class="a4-card{if $wizard_settings.parts.trigger|not} is-off{/if}">
<h2>{'Trigger operations'|i18n( 'design/admin/setup/rad/settings' )}</h2>
<span class="a4-meta">{'Which operations a workflow may be bound to in the admin. Separated by commas, spaces or newlines.'|i18n( 'design/admin/setup/rad/settings' )}</span>

<div class="a4-field">
    <input type="text" name="operations" value="{$wizard_raw.operations|wash}" placeholder="content_delete, content_hide, user_activation" autocomplete="off" />
    <span class="a4-help">{'Listing an operation makes it bindable and binds nothing. The binding is a row in the database, done in the admin, and does not travel with the extension.'|i18n( 'design/admin/setup/rad/settings' )}</span>
</div>
</div>

<div class="a4-card{if $wizard_settings.parts.roots|not} is-off{/if}">
<h2>{'Additional extension roots'|i18n( 'design/admin/setup/rad/settings' )}</h2>
<span class="a4-meta">{'One per line, relative to the installation. extension/ is always first and is not named here; each of these is searched after it, and an extension of the same name in a later root shadows the one before it.'|i18n( 'design/admin/setup/rad/settings' )}</span>

<div class="a4-field">
    <textarea name="roots" rows="2" placeholder="extension_src">{$wizard_raw.roots|wash}</textarea>
    <span class="a4-help">{'The directory has to exist, and the autoloads have to be regenerated after adding one. Moving a package between roots is a copy and nothing else.'|i18n( 'design/admin/setup/rad/settings' )}</span>
</div>
</div>

<div class="a4-card{if $wizard_settings.parts.icons|not} is-off{/if}">
<h2>{'Icon theme'|i18n( 'design/admin/setup/rad/settings' )}</h2>
<span class="a4-meta">{'A set of icons this extension carries, searched before the ones that ship. Added to the search rather than made the current theme, so everything the site already draws keeps drawing.'|i18n( 'design/admin/setup/rad/settings' )}</span>

<div class="a4-field">
    <label for="seTheme">{'Theme name'|i18n( 'design/admin/setup/rad/settings' )}</label>
    <input type="text" id="seTheme" name="theme" value="{$wizard_settings.theme|wash}" autocomplete="off" />
</div>

<div class="a4-field">
    <label for="seSizes">{'Sizes'|i18n( 'design/admin/setup/rad/settings' )}</label>
    <textarea id="seSizes" name="sizes" rows="2" placeholder="normal 32x32&#10;small 16x16">{$wizard_raw.sizes|wash}</textarea>
    <span class="a4-help">{'One per line: a name, then the directory it lives in. A size written as width by height gives the img tag its dimensions as well.'|i18n( 'design/admin/setup/rad/settings' )}</span>
</div>
</div>

<div class="a4-card{if $wizard_settings.parts.siteaccess|not} is-off{/if}">
<h2>{'Siteaccess settings'|i18n( 'design/admin/setup/rad/settings' )}</h2>
<span class="a4-meta">{'Settings that apply to one siteaccess only and travel with this extension rather than living in settings/.'|i18n( 'design/admin/setup/rad/settings' )}</span>

<div class="a4-field">
    <label for="seSiteaccess">{'Siteaccess'|i18n( 'design/admin/setup/rad/settings' )}</label>
    <input type="text" id="seSiteaccess" name="siteaccess" value="{$wizard_settings.siteaccess|wash}" list="seSiteaccessList" autocomplete="off" />
    <datalist id="seSiteaccessList">
    {foreach $wizard_siteaccess_list as $se_sa}<option value="{$se_sa|wash}"></option>{/foreach}
    </datalist>
</div>

<div class="a4-field">
    <label for="seOverrides">{'Settings'|i18n( 'design/admin/setup/rad/settings' )}</label>
    <textarea id="seOverrides" name="overrides" rows="4" placeholder="site.ini [SiteSettings] DefaultPage=content/view/full/2&#10;site.ini [RegionalSettings] Locale=eng-GB">{$wizard_raw.overrides|wash}</textarea>
    <span class="a4-help">{'One per line, as: file.ini [Section] Setting=value'|i18n( 'design/admin/setup/rad/settings' )}</span>
</div>
</div>

<div class="a4-card">
<h2>{'The extension'|i18n( 'design/admin/setup/rad/settings' )}</h2>

<div class="a4-setup-wz-row">
    <div class="a4-field">
        <label for="seName">{'Extension name'|i18n( 'design/admin/setup/rad/settings' )}</label>
        <input type="text" id="seName" name="name" value="{$wizard_settings.name|wash}" placeholder="my_settings" autocomplete="off" />
    </div>
    <div class="a4-field">
        <label for="seClass">{'Listener class'|i18n( 'design/admin/setup/rad/settings' )}</label>
        <input type="text" id="seClass" name="class" value="{$wizard_settings.class|wash}" autocomplete="off" />
    </div>
</div>

<div class="a4-field">
    <label for="seTitle">{'Title'|i18n( 'design/admin/setup/rad/settings' )}</label>
    <input type="text" id="seTitle" name="title" value="{$wizard_settings.title|wash}" />
</div>

<div class="a4-field">
    <label for="seSummary">{'Summary'|i18n( 'design/admin/setup/rad/settings' )}</label>
    <input type="text" id="seSummary" name="summary" value="{$wizard_settings.summary|wash}" />
</div>

<div class="a4-setup-wz-row">
    <div class="a4-field">
        <label for="seAuthor">{'Author'|i18n( 'design/admin/setup/rad/settings' )}</label>
        <input type="text" id="seAuthor" name="author" value="{$wizard_settings.author|wash}" />
    </div>
    <div class="a4-field">
        <label for="seVendor">{'Composer vendor'|i18n( 'design/admin/setup/rad/settings' )}</label>
        <input type="text" id="seVendor" name="vendor" value="{$wizard_settings.vendor|wash}" />
    </div>
    <div class="a4-field">
        <label for="seVersion">{'Version'|i18n( 'design/admin/setup/rad/settings' )}</label>
        <input type="text" id="seVersion" name="version" value="{$wizard_settings.version|wash}" />
    </div>
</div>

<div class="a4-field">
    <label for="seLicence">{'Licence'|i18n( 'design/admin/setup/rad/settings' )}</label>
    {include uri='design:setup/rad_licence_select.tpl' select_id='seLicence' selected=$wizard_settings.licence groups=$wizard_licences}
</div>

<h3>{'What else goes in it'|i18n( 'design/admin/setup/rad/settings' )}</h3>
{foreach $wizard_parts as $se_key => $se_part}
    {if or( eq( $se_key, 'listener' ), eq( $se_key, 'readme' ), eq( $se_key, 'ezinfo' ), eq( $se_key, 'extension_xml' ), eq( $se_key, 'composer' ), eq( $se_key, 'gitignore' ), eq( $se_key, 'licence' ) )}
    <label class="a4-setup-wz-pick">
        <input type="checkbox" name="Parts[]" value="{$se_key|wash}"{if $wizard_settings.parts[$se_key]} checked="checked"{/if} />
        <span>
            <span class="a4-setup-wz-pick-label">{$se_part.label|i18n( 'design/admin/setup/rad/settings' )|wash}</span>
            <span class="a4-meta">{$se_part.description|i18n( 'design/admin/setup/rad/settings' )|wash}</span>
        </span>
    </label>
    {/if}
{/foreach}
</div>

<div class="a4-card">
<h2>{'Switching it on'|i18n( 'design/admin/setup/rad/settings' )}</h2>
<pre>{$wizard_activation|wash}</pre>
<h3>{'Then'|i18n( 'design/admin/setup/rad/settings' )}</h3>
<pre>php bin/php/ezcache.php --clear-all</pre>
</div>

</div>
</div>

{if $wizard_file_count|gt( 0 )}
<div class="a4-card">
<h2>{'Every file, before it is written'|i18n( 'design/admin/setup/rad/settings' )}</h2>
<span class="a4-meta">{$wizard_target|wash}</span>

<div class="a4-actions">
    <button type="button" class="a4-btn a4-btn-small" id="seOpenAll">{'Open all'|i18n( 'design/admin/setup/rad/settings' )}</button>
    <button type="button" class="a4-btn a4-btn-small" id="seCloseAll">{'Close all'|i18n( 'design/admin/setup/rad/settings' )}</button>
</div>

{foreach $wizard_files as $se_file}
<div class="a4-setup-wz-file is-collapsed">
    <div class="a4-setup-wz-file-head">
        <span class="a4-setup-wz-file-toggle">+</span>
        <span class="a4-setup-wz-file-path">{$se_file.path|wash}</span>
        <span class="a4-meta">{'%lines lines, %bytes bytes'|i18n( 'design/admin/setup/rad/settings',, hash( '%lines', $se_file.lines|wash, '%bytes', $se_file.bytes|wash ) )}</span>
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
    <a class="a4-meta" href={'setup/rad'|ezurl}>{'Back to the RAD tools'|i18n( 'design/admin/setup/rad/settings' )}</a>
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
    var openAll = document.getElementById( 'seOpenAll' ), closeAll = document.getElementById( 'seCloseAll' );
    if ( openAll )  openAll.onclick  = function () { every( true ); return false; };
    if ( closeAll ) closeAll.onclick = function () { every( false ); return false; };
} )();
</script>
{/literal}
