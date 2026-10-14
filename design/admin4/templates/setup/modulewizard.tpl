{* The module wizard.

   A module is three things that have to agree with each other, plus two ini
   lines that make the kernel look for any of it. *}


<form method="post" action={'setup/modulewizard'|ezurl} name="ModuleWizard">
<input type="hidden" name="Submitted" value="1" />

<div class="context-block a4-page a4-setup-wz">

{* DESIGN: Header START *}<div class="box-header"><div class="box-ml">
<h1 class="context-title">{'Module wizard'|i18n( 'design/admin/setup/rad/module' )}</h1>
{* DESIGN: Mainline *}<div class="header-mainline"></div>
{* DESIGN: Header END *}</div></div>

{* DESIGN: Content START *}<div class="box-ml"><div class="box-mr"><div class="box-content">

<div class="context-attributes">
<p>{'A module is how this system serves a page that is not content: a declaration, a script per view, a template per view, and the two ini lines that make the kernel look for any of it. When they disagree the failure is quiet - a view with no script is a blank page, a view naming a policy no module declares can be reached by nobody, and a module the ini does not list is not there at all.'|i18n( 'design/admin/setup/rad/module' )}</p>
</div>

{foreach $wizard_feedback as $mw_note}
<div class="a4-notice {if $mw_note.ok}is-ok{else}is-bad{/if}">
    <strong>{if $mw_note.ok}&#10003;{else}!{/if}</strong><span>{$mw_note.message|wash}</span>
</div>
{/foreach}

{foreach $wizard_problems as $mw_problem}
<div class="a4-notice is-bad"><span>{$mw_problem|wash}</span></div>
{/foreach}

{if $wizard_written|count|gt( 0 )}
<div class="a4-notice is-ok"><span>{'Written to %target. Switch it on with the lines below and clear the caches - the module list is itself cached.'|i18n( 'design/admin/setup/rad/module',, hash( '%target', $wizard_target ) )}</span></div>
{/if}

{if $wizard_can_write|not}
<div class="a4-notice is-info"><span>{'The web server cannot write into extension/, so this page can only hand you an archive.'|i18n( 'design/admin/setup/rad/module' )}</span></div>
{/if}

<div class="a4-setup-wz-grid">
<div class="a4-setup-wz-col">

<div class="a4-card">
<h2 id="a4f-h-views">{'The views'|i18n( 'design/admin/setup/rad/module' )}</h2>
<span class="a4-meta" id="a4f-m-views">{'One per line: a name, a colon, then what it needs. A word in lower case is a policy; a word starting with a capital is a parameter in the address; a capital word ending in ? is a named parameter that may be left out.'|i18n( 'design/admin/setup/rad/module' )}</span>

<div class="a4-field">
    <textarea aria-labelledby="a4f-h-views" aria-describedby="a4f-m-views" name="views" rows="5" placeholder="list: read Offset? Limit?&#10;view: read Id&#10;edit: edit Id">{$wizard_raw.views|wash}</textarea>
</div>

<h3>{'What that produces'|i18n( 'design/admin/setup/rad/module' )}</h3>
{if $wizard_views|count|eq( 0 )}
<p class="a4-meta">{'Name a view and its address appears here.'|i18n( 'design/admin/setup/rad/module' )}</p>
{/if}
{foreach $wizard_views as $mw_view}
<div class="a4-setup-wz-view">
    <code class="a4-setup-wz-address">{$mw_view.address|wash}</code>
    {if $mw_view.policies|count|eq( 0 )}
        <span class="a4-setup-wz-tag is-open">{'no policy check'|i18n( 'design/admin/setup/rad/module' )}</span>
    {else}
        {foreach $mw_view.policies as $mw_policy}<span class="a4-setup-wz-tag{if $mw_policy|contains( $mw_view.undeclared )} is-bad{/if}">{$mw_policy|wash}</span> {/foreach}
    {/if}
    <span class="a4-meta">{$mw_view.script|wash}</span>
</div>
{/foreach}
</div>

<div class="a4-card">
<h2 id="a4f-h-policies">{'The policies'|i18n( 'design/admin/setup/rad/module' )}</h2>
<span class="a4-meta" id="a4f-m-policies">{'One per line: a name, a colon, then any limitations. Every policy a view asks for has to be here, or nobody can be granted it and the view is reachable by nobody.'|i18n( 'design/admin/setup/rad/module' )}</span>

<div class="a4-field">
    <textarea aria-labelledby="a4f-h-policies" aria-describedby="a4f-m-policies" name="policies" rows="3" placeholder="read: Section, Class&#10;edit: Section">{$wizard_raw.policies|wash}</textarea>
</div>

<h3>{'Limitations'|i18n( 'design/admin/setup/rad/module' )}</h3>
{foreach $wizard_limitations as $mw_key => $mw_limitation}
<div class="a4-setup-wz-ref">
    <code>{$mw_key|wash}</code>
    <span class="a4-meta">{$mw_limitation.what|i18n( 'design/admin/setup/rad/module' )|wash}</span>
</div>
{/foreach}
</div>

</div>

<div class="a4-setup-wz-col">

<div class="a4-card">
<h2>{'The module'|i18n( 'design/admin/setup/rad/module' )}</h2>

<div class="a4-setup-wz-row">
    <div class="a4-field">
        <label for="mwName">{'Extension name'|i18n( 'design/admin/setup/rad/module' )}</label>
        <input type="text" id="mwName" name="name" value="{$wizard_settings.name|wash}" placeholder="my_module" autocomplete="off" />
    </div>
    <div class="a4-field">
        <label for="mwModule">{'Module name'|i18n( 'design/admin/setup/rad/module' )}</label>
        <input type="text" id="mwModule" name="module" value="{$wizard_settings.module|wash}" autocomplete="off" />
        <span class="a4-help">{'The first part of every address it answers.'|i18n( 'design/admin/setup/rad/module' )}</span>
    </div>
</div>

<div class="a4-field">
    <label for="mwTitle">{'Title'|i18n( 'design/admin/setup/rad/module' )}</label>
    <input type="text" id="mwTitle" name="title" value="{$wizard_settings.title|wash}" />
</div>

<div class="a4-field">
    <label for="mwSummary">{'Summary'|i18n( 'design/admin/setup/rad/module' )}</label>
    <input type="text" id="mwSummary" name="summary" value="{$wizard_settings.summary|wash}" />
</div>

<div class="a4-field">
    <label for="mwContext">{'Where it sits'|i18n( 'design/admin/setup/rad/module' )}</label>
    <select id="mwContext" name="context">
    {foreach $wizard_contexts as $mw_key => $mw_what}
        <option value="{$mw_key|wash}"{if eq( $wizard_settings.context, $mw_key )} selected="selected"{/if}>{$mw_what|wash}</option>
    {/foreach}
    </select>
</div>

<div class="a4-field">
    <label for="mwNavigation">{'Part of the admin'|i18n( 'design/admin/setup/rad/module' )}</label>
    <select id="mwNavigation" name="navigation">
    {foreach $wizard_navigation_parts as $mw_part}
        <option value="{$mw_part|wash}"{if eq( $wizard_settings.navigation, $mw_part )} selected="selected"{/if}>{$mw_part|wash}</option>
    {/foreach}
    </select>
    <span class="a4-help">{'Which left hand menu the entry goes in.'|i18n( 'design/admin/setup/rad/module' )}</span>
</div>

<div class="a4-setup-wz-row">
    <div class="a4-field">
        <label for="mwAuthor">{'Author'|i18n( 'design/admin/setup/rad/module' )}</label>
        <input type="text" id="mwAuthor" name="author" value="{$wizard_settings.author|wash}" />
    </div>
    <div class="a4-field">
        <label for="mwVendor">{'Composer vendor'|i18n( 'design/admin/setup/rad/module' )}</label>
        <input type="text" id="mwVendor" name="vendor" value="{$wizard_settings.vendor|wash}" />
    </div>
    <div class="a4-field">
        <label for="mwVersion">{'Version'|i18n( 'design/admin/setup/rad/module' )}</label>
        <input type="text" id="mwVersion" name="version" value="{$wizard_settings.version|wash}" />
    </div>
</div>

<div class="a4-field">
    <label for="mwLicence">{'Licence'|i18n( 'design/admin/setup/rad/module' )}</label>
    {include uri='design:setup/rad_licence_select.tpl' select_id='mwLicence' selected=$wizard_settings.licence groups=$wizard_licences}
</div>
</div>

<div class="a4-card">
<h2>{'What goes in it'|i18n( 'design/admin/setup/rad/module' )}</h2>
{foreach $wizard_parts as $mw_key => $mw_part}
    <label class="a4-setup-wz-pick">
        <input type="checkbox" name="Parts[]" value="{$mw_key|wash}"{if $wizard_settings.parts[$mw_key]} checked="checked"{/if} />
        <span>
            <span class="a4-setup-wz-pick-label">{$mw_part.label|i18n( 'design/admin/setup/rad/module' )|wash}</span>
            <span class="a4-meta">{$mw_part.description|i18n( 'design/admin/setup/rad/module' )|wash}</span>
        </span>
    </label>
{/foreach}
</div>

<div class="a4-card">
<h2>{'Switching it on'|i18n( 'design/admin/setup/rad/module' )}</h2>
<pre>{$wizard_activation|wash}</pre>
<h3>{'Then'|i18n( 'design/admin/setup/rad/module' )}</h3>
<pre>php bin/php/ezcache.php --clear-all</pre>
</div>

</div>
</div>

{if $wizard_file_count|gt( 0 )}
<div class="a4-card">
<h2>{'Every file, before it is written'|i18n( 'design/admin/setup/rad/module' )}</h2>
<span class="a4-meta">{$wizard_target|wash}</span>

<div class="a4-actions">
    <button type="button" class="a4-btn a4-btn-small" id="mwOpenAll">{'Open all'|i18n( 'design/admin/setup/rad/module' )}</button>
    <button type="button" class="a4-btn a4-btn-small" id="mwCloseAll">{'Close all'|i18n( 'design/admin/setup/rad/module' )}</button>
</div>

{foreach $wizard_files as $mw_file}
<div class="a4-setup-wz-file is-collapsed">
    <div class="a4-setup-wz-file-head">
        <span class="a4-setup-wz-file-toggle">+</span>
        <span class="a4-setup-wz-file-path">{$mw_file.path|wash}</span>
        <span class="a4-meta">{'%lines lines, %bytes bytes'|i18n( 'design/admin/setup/rad/module',, hash( '%lines', $mw_file.lines|wash, '%bytes', $mw_file.bytes|wash ) )}</span>
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
    <a class="a4-meta" href={'setup/rad'|ezurl}>{'Back to the RAD tools'|i18n( 'design/admin/setup/rad/module' )}</a>
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
    var openAll = document.getElementById( 'mwOpenAll' ), closeAll = document.getElementById( 'mwCloseAll' );
    if ( openAll )  openAll.onclick  = function () { every( true ); return false; };
    if ( closeAll ) closeAll.onclick = function () { every( false ); return false; };
} )();
</script>
{/literal}
