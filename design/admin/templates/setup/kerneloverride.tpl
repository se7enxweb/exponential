{* The kernel override wizard.

   The heaviest mechanism there is, and the last resort. The page says so,
   because a tool that makes something easy owes you the cost of it. *}


<form method="post" action={'setup/kerneloverride'|ezurl} name="KernelOverrideWizard">
<input type="hidden" name="Submitted" value="1" />

<div class="context-block a4-page a4-setup-wz">

{* DESIGN: Header START *}<div class="box-header"><div class="box-ml">
<h1 class="context-title">{'Kernel override wizard'|i18n( 'design/admin/setup/rad/override' )}</h1>
{* DESIGN: Mainline *}<div class="header-mainline"></div>
{* DESIGN: Header END *}</div></div>

{* DESIGN: Content START *}<div class="box-ml"><div class="box-mr"><div class="box-content">

<div class="a4-notice is-warn">
    <strong>!</strong>
    <span>{'An override is not a subclass. It replaces the kernel class under the same name, so there is no parent to call, nothing is inherited, and everything the original did has to keep being done by the copy. Every fix the kernel makes to that class afterwards is a fix this site does not get until somebody copies it across. Almost everything here has a lighter way in - a handler named by a setting, an event listener, a filter, a template override - and the RAD tools list them. This is for when none of them reaches the thing that has to change.'|i18n( 'design/admin/setup/rad/override' )}</span>
</div>

{if $wizard_readiness.allowed|not}
<div class="a4-notice is-bad"><strong>!</strong><span>{$wizard_readiness.message|wash}</span></div>
{elseif $wizard_readiness.generated|not}
<div class="a4-notice is-bad"><strong>!</strong><span>{$wizard_readiness.message|wash}</span></div>
{else}
<div class="a4-notice is-ok"><strong>&#10003;</strong><span>{$wizard_readiness.message|wash}</span></div>
{/if}

{foreach $wizard_feedback as $ko_note}
<div class="a4-notice {if $ko_note.ok}is-ok{else}is-bad{/if}">
    <strong>{if $ko_note.ok}&#10003;{else}!{/if}</strong><span>{$ko_note.message|wash}</span>
</div>
{/foreach}

{foreach $wizard_problems as $ko_problem}
<div class="a4-notice is-bad"><strong>!</strong><span>{$ko_problem|wash}</span></div>
{/foreach}

{if $wizard_written|count|gt( 0 )}
<div class="a4-notice is-info"><strong>&#10003;</strong><span>{'Written to %target. Switch it on, allow overrides in config.php, and generate the override map with ezpgenerateautoloads.php -o, which is a different run from the ordinary one.'|i18n( 'design/admin/setup/rad/override',, hash( '%target', $wizard_target ) )}</span></div>
{/if}

{if $wizard_can_write|not}
<div class="a4-notice is-info"><strong>i</strong><span>{'The web server cannot write into extension/, so this page can only hand you an archive.'|i18n( 'design/admin/setup/rad/override' )}</span></div>
{/if}

<div class="a4-setup-wz-grid">
<div class="a4-setup-wz-col">

<div class="a4-card">
<h2>{'Which class'|i18n( 'design/admin/setup/rad/override' )}</h2>
<span class="a4-meta">{'%count classes in this kernel. Type part of a name or a path; every word has to match.'|i18n( 'design/admin/setup/rad/override',, hash( '%count', $wizard_class_count ) )}</span>

<div class="a4-field">
    <input type="text" name="find" value="{$wizard_settings.find|wash}" placeholder="{'ezcontentobject, or classes/datatypes'|i18n( 'design/admin/setup/rad/override' )}" autocomplete="off" />
</div>

{if ne( $wizard_settings.find, '' )}
    {if $wizard_match_count|eq( 0 )}
    <p class="a4-meta">{'No class in this kernel matches that.'|i18n( 'design/admin/setup/rad/override' )}</p>
    {else}
    <h3>{'%count found'|i18n( 'design/admin/setup/rad/override',, hash( '%count', $wizard_match_count ) )}</h3>
    {foreach $wizard_matches as $ko_match}
    <label class="a4-setup-wz-match{if $ko_match.current} is-current{/if}">
        <input type="radio" name="class" value="{$ko_match.class|wash}"{if $ko_match.current} checked="checked"{/if} />
        <code>{$ko_match.class|wash}</code>
        <span class="a4-meta">{$ko_match.path|wash} &middot; {'%lines lines'|i18n( 'design/admin/setup/rad/override',, hash( '%lines', $ko_match.lines ) )}</span>
    </label>
    {/foreach}
    {/if}
{/if}

{if ne( $wizard_settings.class, '' )}
<h3>{'Chosen'|i18n( 'design/admin/setup/rad/override' )}</h3>
<p><code>{$wizard_settings.class|wash}</code><br />
<span class="a4-meta">{$wizard_settings.source|wash}<br />{'md5 %wizard_checksum'|i18n( 'design/admin/setup/rad/override',, hash( '%wizard_checksum', $wizard_checksum|wash ) )}</span></p>
<span class="a4-meta">{'That checksum goes into the copy and into the drift check, so the day the kernel changes this file, the check says so.'|i18n( 'design/admin/setup/rad/override' )}</span>
{/if}
</div>

<div class="a4-card">
<h2>{'Why an override'|i18n( 'design/admin/setup/rad/override' )}</h2>
<span class="a4-meta">{'Whoever meets this at the next upgrade will want to know whether it is still needed, and by then nobody will remember. It goes in the file and in the README.'|i18n( 'design/admin/setup/rad/override' )}</span>

<div class="a4-field">
    <textarea name="reason" rows="3" placeholder="{'What has to change, and which lighter mechanism was tried first.'|i18n( 'design/admin/setup/rad/override' )}">{$wizard_settings.reason|wash}</textarea>
</div>
</div>

</div>

<div class="a4-setup-wz-col">

<div class="a4-card">
<h2>{'The extension'|i18n( 'design/admin/setup/rad/override' )}</h2>

<div class="a4-field">
    <label for="koName">{'Extension name'|i18n( 'design/admin/setup/rad/override' )}</label>
    <input type="text" id="koName" name="name" value="{$wizard_settings.name|wash}" placeholder="my_override" autocomplete="off" />
</div>

<div class="a4-field">
    <label for="koTitle">{'Title'|i18n( 'design/admin/setup/rad/override' )}</label>
    <input type="text" id="koTitle" name="title" value="{$wizard_settings.title|wash}" />
</div>

<div class="a4-field">
    <label for="koSummary">{'Summary'|i18n( 'design/admin/setup/rad/override' )}</label>
    <input type="text" id="koSummary" name="summary" value="{$wizard_settings.summary|wash}" />
</div>

<div class="a4-setup-wz-row">
    <div class="a4-field">
        <label for="koAuthor">{'Author'|i18n( 'design/admin/setup/rad/override' )}</label>
        <input type="text" id="koAuthor" name="author" value="{$wizard_settings.author|wash}" />
    </div>
    <div class="a4-field">
        <label for="koVendor">{'Composer vendor'|i18n( 'design/admin/setup/rad/override' )}</label>
        <input type="text" id="koVendor" name="vendor" value="{$wizard_settings.vendor|wash}" />
    </div>
    <div class="a4-field">
        <label for="koVersion">{'Version'|i18n( 'design/admin/setup/rad/override' )}</label>
        <input type="text" id="koVersion" name="version" value="{$wizard_settings.version|wash}" />
    </div>
</div>

<div class="a4-field">
    <label for="koLicence">{'Licence'|i18n( 'design/admin/setup/rad/override' )}</label>
    {include uri='design:setup/rad_licence_select.tpl' select_id='koLicence' selected=$wizard_settings.licence groups=$wizard_licences}
    <span class="a4-help">{'This carries kernel code, so what it is licensed under is not an afterthought.'|i18n( 'design/admin/setup/rad/override' )}</span>
</div>

<h3>{'What goes in it'|i18n( 'design/admin/setup/rad/override' )}</h3>
{foreach $wizard_parts as $ko_key => $ko_part}
    <label class="a4-setup-wz-pick">
        <input type="checkbox" name="Parts[]" value="{$ko_key|wash}"{if $wizard_settings.parts[$ko_key]} checked="checked"{/if} />
        <span>
            <span class="a4-setup-wz-pick-label">{$ko_part.label|i18n( 'design/admin/setup/rad/override' )|wash}</span>
            <span class="a4-meta">{$ko_part.description|i18n( 'design/admin/setup/rad/override' )|wash}</span>
        </span>
    </label>
{/foreach}
</div>

<div class="a4-card">
<h2>{'Switching it on'|i18n( 'design/admin/setup/rad/override' )}</h2>
<pre>{$wizard_activation|wash}</pre>
<h3>{'And config.php, which is not the default'|i18n( 'design/admin/setup/rad/override' )}</h3>
<pre>define( 'EZP_AUTOLOAD_ALLOW_KERNEL_OVERRIDE', true );</pre>
<h3>{'Then'|i18n( 'design/admin/setup/rad/override' )}</h3>
<pre>php bin/php/ezpgenerateautoloads.php -o
php bin/php/ezcache.php --clear-all</pre>
<span class="a4-meta">{'The -o run is a different one from the ordinary autoload generation, and writes var/autoload/ezp_override.php. Without it nothing here is loaded.'|i18n( 'design/admin/setup/rad/override' )}</span>
</div>

<div class="a4-card">
<h2>{'After every upgrade'|i18n( 'design/admin/setup/rad/override' )}</h2>
<span class="a4-meta">{'The check the extension ships. Exit status 1 means the kernel changed the file this was copied from and somebody has to decide which of those changes this override needs. Worth failing a build on.'|i18n( 'design/admin/setup/rad/override' )}</span>
{if ne( $wizard_settings.name, '' )}
<pre>php extension/{$wizard_settings.name|wash}/bin/checkdrift.php</pre>
{/if}
</div>

</div>
</div>

{if $wizard_file_count|gt( 0 )}
<div class="a4-card">
<h2>{'Every file, before it is written'|i18n( 'design/admin/setup/rad/override' )}</h2>
<span class="a4-meta">{$wizard_target|wash}</span>

<div class="a4-actions">
    <button type="button" class="a4-btn a4-btn-small" id="koOpenAll">{'Open all'|i18n( 'design/admin/setup/rad/override' )}</button>
    <button type="button" class="a4-btn a4-btn-small" id="koCloseAll">{'Close all'|i18n( 'design/admin/setup/rad/override' )}</button>
</div>

{foreach $wizard_files as $ko_file}
<div class="a4-setup-wz-file is-collapsed">
    <div class="a4-setup-wz-file-head">
        <span class="a4-setup-wz-file-toggle">+</span>
        <span class="a4-setup-wz-file-path">{$ko_file.path|wash}</span>
        <span class="a4-meta">{'%lines lines, %bytes bytes'|i18n( 'design/admin/setup/rad/override',, hash( '%lines', $ko_file.lines|wash, '%bytes', $ko_file.bytes|wash ) )}</span>
    </div>
    <pre>{$ko_file.contents|wash}</pre>
</div>
{/foreach}
</div>
{/if}

{* DESIGN: Content END *}</div></div></div>

<div class="controlbar">
{* DESIGN: Control bar START *}<div class="box-bc"><div class="box-ml">
<div class="block">
    {if $wizard_ready}
    <input class="defaultbutton" type="submit" name="CreateButton" value="{'Create in extension/'|i18n( 'design/admin/setup/rad/override' )}" />
    {else}
    <input class="button-disabled" type="submit" name="_Disabled" value="{'Create in extension/'|i18n( 'design/admin/setup/rad/override' )}" disabled="disabled" />
    {/if}

    {if $wizard_file_count|gt( 0 )}
        {if $wizard_can_archive}
    <input class="button" type="submit" name="DownloadButton" value="{'Download as zip'|i18n( 'design/admin/setup/rad/override' )}" />
        {/if}
    {/if}

    <input class="button" type="submit" name="PreviewButton" value="{'Find'|i18n( 'design/admin/setup/rad/override' )}" />
    <a class="a4-meta" href={'setup/rad'|ezurl}>{'Back to the RAD tools'|i18n( 'design/admin/setup/rad/override' )}</a>
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
        if ( ( ' ' + divs[i].className + ' ' ).indexOf( ' a4-setup-wz-file ' ) !== -1 )
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
    var openAll = document.getElementById( 'koOpenAll' ), closeAll = document.getElementById( 'koCloseAll' );
    if ( openAll )  openAll.onclick  = function () { every( true ); return false; };
    if ( closeAll ) closeAll.onclick = function () { every( false ); return false; };
} )();
</script>
{/literal}
