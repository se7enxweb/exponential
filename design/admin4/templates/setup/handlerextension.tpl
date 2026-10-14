{* The handler wizard.

   One kind of handler at a time. What the kernel does by default, what a
   handler of your own would do instead, and every method it has to implement
   with a note on what each is for and when it is called. *}


<form method="post" action={'setup/handlerextension'|ezurl} name="HandlerWizard">
<input type="hidden" name="Submitted" value="1" />
<input type="hidden" name="kind" value="{$wizard_settings.kind|wash}" />

<div class="context-block a4-page a4-setup-wz">

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
<div class="a4-notice {if $hw_note.ok}is-ok{else}is-bad{/if}">
    <strong>{if $hw_note.ok}&#10003;{else}!{/if}</strong><span>{$hw_note.message|wash}</span>
</div>
{/foreach}

{foreach $wizard_problems as $hw_problem}
<div class="a4-notice is-bad"><span>{$hw_problem|wash}</span></div>
{/foreach}

{if $wizard_written|count|gt( 0 )}
<div class="a4-notice is-ok"><span>{'Written to %target. Switch it on with the lines below, clear the caches, and regenerate the extension autoloads.'|i18n( 'design/admin/setup/rad/handler',, hash( '%target', $wizard_target ) )}</span></div>
{/if}

{if $wizard_can_write|not}
<div class="a4-notice is-info"><span>{'The web server cannot write into extension/, so this page can only hand you an archive.'|i18n( 'design/admin/setup/rad/handler' )}</span></div>
{/if}

<div class="a4-setup-wz-grid">
<div class="a4-setup-wz-col">

<div class="a4-card">
<h2>{'The extension'|i18n( 'design/admin/setup/rad/handler' )}</h2>
<span class="a4-meta">{'The class name is what the ini will point at, so it has to be one nothing else on this installation already uses.'|i18n( 'design/admin/setup/rad/handler' )}</span>

<div class="a4-setup-wz-row">
    <div class="a4-field">
        <label for="hwName">{'Extension name'|i18n( 'design/admin/setup/rad/handler' )}</label>
        <input type="text" id="hwName" name="name" value="{$wizard_settings.name|wash}" placeholder="my_handler" autocomplete="off" />
    </div>
    <div class="a4-field">
        <label for="hwClass">{'Class name'|i18n( 'design/admin/setup/rad/handler' )}</label>
        <input type="text" id="hwClass" name="class" value="{$wizard_settings.class|wash}" autocomplete="off" />
    </div>
{if $wizard_recipe.aliased}
    <div class="a4-field">
        <label for="hwAlias">{'Alias'|i18n( 'design/admin/setup/rad/handler' )}</label>
        <input type="text" id="hwAlias" name="alias" value="{$wizard_settings.alias|wash}" autocomplete="off" />
        <span class="a4-help">{'The short word the setting names, rather than the class itself.'|i18n( 'design/admin/setup/rad/handler' )}</span>
    </div>
{/if}
</div>

<div class="a4-field">
    <label for="hwTitle">{'Title'|i18n( 'design/admin/setup/rad/handler' )}</label>
    <input type="text" id="hwTitle" name="title" value="{$wizard_settings.title|wash}" />
</div>

<div class="a4-field">
    <label for="hwSummary">{'Summary'|i18n( 'design/admin/setup/rad/handler' )}</label>
    <input type="text" id="hwSummary" name="summary" value="{$wizard_settings.summary|wash}" />
</div>

<div class="a4-setup-wz-row">
    <div class="a4-field">
        <label for="hwAuthor">{'Author'|i18n( 'design/admin/setup/rad/handler' )}</label>
        <input type="text" id="hwAuthor" name="author" value="{$wizard_settings.author|wash}" />
    </div>
    <div class="a4-field">
        <label for="hwVendor">{'Composer vendor'|i18n( 'design/admin/setup/rad/handler' )}</label>
        <input type="text" id="hwVendor" name="vendor" value="{$wizard_settings.vendor|wash}" />
    </div>
    <div class="a4-field">
        <label for="hwVersion">{'Version'|i18n( 'design/admin/setup/rad/handler' )}</label>
        <input type="text" id="hwVersion" name="version" value="{$wizard_settings.version|wash}" />
    </div>
</div>

<div class="a4-field">
    <label for="hwLicence">{'Licence'|i18n( 'design/admin/setup/rad/handler' )}</label>
    {include uri='design:setup/rad_licence_select.tpl' select_id='hwLicence' selected=$wizard_settings.licence groups=$wizard_licences}
</div>
</div>

<div class="a4-card">
<h2>{'What goes in it'|i18n( 'design/admin/setup/rad/handler' )}</h2>
{foreach $wizard_parts as $hw_key => $hw_part}
    <label class="a4-setup-wz-pick">
        <input type="checkbox" name="Parts[]" value="{$hw_key|wash}"{if $wizard_settings.parts[$hw_key]} checked="checked"{/if} />
        <span>
            <span class="a4-setup-wz-pick-label">{$hw_part.label|i18n( 'design/admin/setup/rad/handler' )|wash}</span>
            <span class="a4-meta">{$hw_part.description|i18n( 'design/admin/setup/rad/handler' )|wash}</span>
        </span>
    </label>
{/foreach}
</div>

</div>

<div class="a4-setup-wz-col">

{if $wizard_recipe}
<div class="a4-card">
<h2>{'What it replaces'|i18n( 'design/admin/setup/rad/handler' )}</h2>
<div class="a4-setup-wz-facts">
    <div class="a4-setup-wz-fact"><b>{'Extends'|i18n( 'design/admin/setup/rad/handler' )}</b><span><code>{$wizard_recipe.base|wash}</code></span></div>
    <div class="a4-setup-wz-fact"><b>{'Kernel'|i18n( 'design/admin/setup/rad/handler' )}</b><span class="a4-meta"><code>{$wizard_recipe.source|wash}</code></span></div>
    <div class="a4-setup-wz-fact"><b>{'Setting'|i18n( 'design/admin/setup/rad/handler' )}</b><span><code>{$wizard_recipe.ini|wash} [{$wizard_recipe.section|wash}] {$wizard_recipe.variable|wash}{if $wizard_recipe.aliased}[{$wizard_settings.alias|wash}]{/if}</code></span></div>
</div>
{if ne( $wizard_recipe.note, '' )}
<p class="a4-meta">{$wizard_recipe.note|i18n( 'design/admin/setup/rad/handler' )|wash}</p>
{/if}
</div>

<div class="a4-card">
<h2>{'What has to be written'|i18n( 'design/admin/setup/rad/handler' )} ({$wizard_methods|count})</h2>
<span class="a4-meta">{'Every one of these is a method the kernel calls. Each is generated with this note beside it, and returns something that leaves the system working the way it did before.'|i18n( 'design/admin/setup/rad/handler' )}</span>

{foreach $wizard_methods as $hw_method}
<div class="a4-setup-wz-method">
    <code>{$hw_method.signature|wash}</code>
    <span class="a4-meta">{$hw_method.what|i18n( 'design/admin/setup/rad/handler' )|wash}</span>
</div>
{/foreach}
</div>
{/if}

<div class="a4-card">
<h2>{'Switching it on'|i18n( 'design/admin/setup/rad/handler' )}</h2>
<span class="a4-meta">{'Add this to settings/override/site.ini.append.php. The extension brings its own setting naming the class.'|i18n( 'design/admin/setup/rad/handler' )}</span>
<pre>{$wizard_activation|wash}</pre>
</div>

</div>
</div>

{if $wizard_file_count|gt( 0 )}
<div class="a4-card">
<h2>{'Every file, before it is written'|i18n( 'design/admin/setup/rad/handler' )}</h2>
<span class="a4-meta">{$wizard_target|wash}</span>

<div class="a4-actions">
    <button type="button" class="a4-btn a4-btn-small" id="hwOpenAll">{'Open all'|i18n( 'design/admin/setup/rad/handler' )}</button>
    <button type="button" class="a4-btn a4-btn-small" id="hwCloseAll">{'Close all'|i18n( 'design/admin/setup/rad/handler' )}</button>
</div>

{foreach $wizard_files as $hw_file}
<div class="a4-setup-wz-file is-collapsed">
    <div class="a4-setup-wz-file-head">
        <span class="a4-setup-wz-file-toggle">+</span>
        <span class="a4-setup-wz-file-path">{$hw_file.path|wash}</span>
        <span class="a4-meta">{'%lines lines, %bytes bytes'|i18n( 'design/admin/setup/rad/handler',, hash( '%lines', $hw_file.lines|wash, '%bytes', $hw_file.bytes|wash ) )}</span>
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
    <a class="a4-meta" href={'setup/rad'|ezurl}>{'Back to the RAD tools'|i18n( 'design/admin/setup/rad/handler' )}</a>
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
    var openAll = document.getElementById( 'hwOpenAll' ), closeAll = document.getElementById( 'hwCloseAll' );
    if ( openAll )  openAll.onclick  = function () { every( true ); return false; };
    if ( closeAll ) closeAll.onclick = function () { every( false ); return false; };
} )();
</script>
{/literal}
