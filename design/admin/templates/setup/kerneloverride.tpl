{* The kernel override wizard.

   The heaviest mechanism there is, and the last resort. The page says so,
   because a tool that makes something easy owes you the cost of it. *}

{literal}
<style type="text/css">
.exp-ko {
    --ko-ink: #1c1c1e; --ko-muted: #6a6a72; --ko-line: #e2e2e6;
    --ko-accent: #2d6cdf; --ko-ok: #1f8a4c; --ko-bad: #b4232c; --ko-warn: #b06a00;
    color: var(--ko-ink);
}
.exp-ko h2 { font-size: 1.05rem; margin: 0 0 .15rem 0; }
.exp-ko h3 { font-size: .9rem; margin: 1rem 0 .3rem 0; color: var(--ko-muted); text-transform: uppercase; letter-spacing: .04em; }
.exp-ko .ko-meta { color: var(--ko-muted); font-size: .92em; white-space: normal; }
.exp-ko code, .exp-ko pre { font-family: Menlo, Consolas, "DejaVu Sans Mono", monospace; overflow-wrap: anywhere; }
.exp-ko pre { max-width: 100%; overflow-x: auto; white-space: pre; }
.exp-ko label, .exp-ko label * { white-space: normal; }

.ko-note { display: flex; gap: .6rem; align-items: flex-start; border: 1px solid var(--ko-line); border-left-width: 4px; border-radius: 6px; padding: .7rem .9rem; margin: 0 0 .8rem 0; background: #fcfcfd; }
.ko-note.is-ok { border-left-color: var(--ko-ok); }
.ko-note.is-bad { border-left-color: var(--ko-bad); }
.ko-note.is-warn { border-left-color: var(--ko-warn); background: #fffbf3; }
.ko-note.is-info { border-left-color: var(--ko-accent); }

.ko-card { border: 1px solid var(--ko-line); border-radius: 8px; background: #fff; padding: 1rem 1.1rem; margin: 0 0 1.6rem 0; min-width: 0; }
.ko-card > .ko-meta { display: block; padding-bottom: .9rem; }
.ko-grid { display: flex; flex-wrap: wrap; gap: 1.6rem; align-items: flex-start; }
.ko-col { flex: 1 1 26rem; min-width: 0; }

.ko-field { display: flex; flex-direction: column; gap: .3rem; padding-bottom: .9rem; }
.ko-field label { font-size: .82em; color: var(--ko-muted); text-transform: uppercase; letter-spacing: .04em; font-weight: 600; }
.ko-field input[type=text], .ko-field select, .ko-field textarea {
    border: 1px solid var(--ko-line); border-radius: 6px; padding: .45rem .55rem;
    font: inherit; background: #fff; width: 100%; box-sizing: border-box;
}
.ko-field textarea { min-height: 4rem; resize: vertical; }
.ko-field .ko-hint { font-size: .85em; color: var(--ko-muted); text-transform: none; letter-spacing: 0; font-weight: normal; }
.ko-row { display: flex; flex-wrap: wrap; gap: .9rem; }
.ko-row > .ko-field { flex: 1 1 9rem; }

.ko-pick { display: flex; gap: .6rem; align-items: flex-start; padding: .45rem .55rem; border-radius: 6px; }
.ko-pick:hover { background: #f6f7f9; }
.ko-pick input { margin-top: .25rem; flex: 0 0 auto; }
.ko-pick > span { flex: 1 1 auto; min-width: 0; }
.ko-pick .ko-pick-label { display: block; font-weight: 600; }
.ko-pick .ko-meta { display: block; }

.ko-match { display: flex; gap: .6rem; align-items: baseline; padding: .4rem .55rem; border-radius: 6px; border-top: 1px solid #f0f0f3; }
.ko-match:hover { background: #f6f7f9; }
.ko-match.is-current { background: #eef4ff; }
.ko-match input { flex: 0 0 auto; }
.ko-match code { font-weight: 600; flex: 0 0 auto; }
.ko-match .ko-meta { flex: 1 1 10rem; min-width: 0; }

.ko-file { border: 1px solid var(--ko-line); border-radius: 8px; margin: 0 0 .6rem 0; background: #fff; }
.ko-file-head { display: flex; flex-wrap: wrap; align-items: center; gap: .6rem; padding: .55rem .8rem; cursor: pointer; }
.ko-file-head:hover { background: #f6f7f9; }
.ko-file-toggle { display: inline-flex; align-items: center; justify-content: center; width: 1.25rem; height: 1.25rem; border: 1px solid var(--ko-line); border-radius: 4px; font-weight: 700; background: #f4f4f5; line-height: 1; }
.ko-file-path { font-weight: 600; overflow-wrap: anywhere; min-width: 0; }
.ko-file pre { margin: 0; padding: .8rem 1rem; border-top: 1px solid var(--ko-line); background: #fbfbfc; font-size: .88em; line-height: 1.5; max-height: 28rem; overflow: auto; }
.ko-file.is-collapsed pre { display: none; }
.ko-toolbar { display: flex; flex-wrap: wrap; gap: .5rem; padding: .2rem 0 .9rem 0; }
.ko-btn { border: 1px solid var(--ko-line); background: #f4f4f5; border-radius: 6px; padding: .3rem .7rem; font: inherit; cursor: pointer; color: inherit; }
.ko-btn:hover { border-color: #9a9aa0; }
</style>
{/literal}

<form method="post" action={'setup/kerneloverride'|ezurl} name="KernelOverrideWizard">
<input type="hidden" name="Submitted" value="1" />

<div class="context-block exp-ko">

{* DESIGN: Header START *}<div class="box-header"><div class="box-ml">
<h1 class="context-title">{'Kernel override wizard'|i18n( 'design/admin/setup/rad/override' )}</h1>
{* DESIGN: Mainline *}<div class="header-mainline"></div>
{* DESIGN: Header END *}</div></div>

{* DESIGN: Content START *}<div class="box-ml"><div class="box-mr"><div class="box-content">

<div class="ko-note is-warn">
    <strong>!</strong>
    <span>{'An override is not a subclass. It replaces the kernel class under the same name, so there is no parent to call, nothing is inherited, and everything the original did has to keep being done by the copy. Every fix the kernel makes to that class afterwards is a fix this site does not get until somebody copies it across. Almost everything here has a lighter way in - a handler named by a setting, an event listener, a filter, a template override - and the RAD tools list them. This is for when none of them reaches the thing that has to change.'|i18n( 'design/admin/setup/rad/override' )}</span>
</div>

{if $wizard_readiness.allowed|not}
<div class="ko-note is-bad"><strong>!</strong><span>{$wizard_readiness.message|wash}</span></div>
{elseif $wizard_readiness.generated|not}
<div class="ko-note is-bad"><strong>!</strong><span>{$wizard_readiness.message|wash}</span></div>
{else}
<div class="ko-note is-ok"><strong>&#10003;</strong><span>{$wizard_readiness.message|wash}</span></div>
{/if}

{foreach $wizard_feedback as $ko_note}
<div class="ko-note {if $ko_note.ok}is-ok{else}is-bad{/if}">
    <strong>{if $ko_note.ok}&#10003;{else}!{/if}</strong><span>{$ko_note.message|wash}</span>
</div>
{/foreach}

{foreach $wizard_problems as $ko_problem}
<div class="ko-note is-bad"><strong>!</strong><span>{$ko_problem|wash}</span></div>
{/foreach}

{if $wizard_written|count|gt( 0 )}
<div class="ko-note is-info"><strong>&#10003;</strong><span>{'Written to %target. Switch it on, allow overrides in config.php, and generate the override map with ezpgenerateautoloads.php -o, which is a different run from the ordinary one.'|i18n( 'design/admin/setup/rad/override',, hash( '%target', $wizard_target ) )}</span></div>
{/if}

{if $wizard_can_write|not}
<div class="ko-note is-info"><strong>i</strong><span>{'The web server cannot write into extension/, so this page can only hand you an archive.'|i18n( 'design/admin/setup/rad/override' )}</span></div>
{/if}

<div class="ko-grid">
<div class="ko-col">

<div class="ko-card">
<h2>{'Which class'|i18n( 'design/admin/setup/rad/override' )}</h2>
<span class="ko-meta">{'%count classes in this kernel. Type part of a name or a path; every word has to match.'|i18n( 'design/admin/setup/rad/override',, hash( '%count', $wizard_class_count ) )}</span>

<div class="ko-field">
    <input type="text" name="find" value="{$wizard_settings.find|wash}" placeholder="ezcontentobject, or classes/datatypes" autocomplete="off" />
</div>

{if ne( $wizard_settings.find, '' )}
    {if $wizard_match_count|eq( 0 )}
    <p class="ko-meta">{'No class in this kernel matches that.'|i18n( 'design/admin/setup/rad/override' )}</p>
    {else}
    <h3>{'%count found'|i18n( 'design/admin/setup/rad/override',, hash( '%count', $wizard_match_count ) )}</h3>
    {foreach $wizard_matches as $ko_match}
    <label class="ko-match{if $ko_match.current} is-current{/if}">
        <input type="radio" name="class" value="{$ko_match.class|wash}"{if $ko_match.current} checked="checked"{/if} />
        <code>{$ko_match.class|wash}</code>
        <span class="ko-meta">{$ko_match.path|wash} &middot; {$ko_match.lines} {'lines'|i18n( 'design/admin/setup/rad/override' )}</span>
    </label>
    {/foreach}
    {/if}
{/if}

{if ne( $wizard_settings.class, '' )}
<h3>{'Chosen'|i18n( 'design/admin/setup/rad/override' )}</h3>
<p><code>{$wizard_settings.class|wash}</code><br />
<span class="ko-meta">{$wizard_settings.source|wash}<br />md5 {$wizard_checksum|wash}</span></p>
<span class="ko-meta">{'That checksum goes into the copy and into the drift check, so the day the kernel changes this file, the check says so.'|i18n( 'design/admin/setup/rad/override' )}</span>
{/if}
</div>

<div class="ko-card">
<h2>{'Why an override'|i18n( 'design/admin/setup/rad/override' )}</h2>
<span class="ko-meta">{'Whoever meets this at the next upgrade will want to know whether it is still needed, and by then nobody will remember. It goes in the file and in the README.'|i18n( 'design/admin/setup/rad/override' )}</span>

<div class="ko-field">
    <textarea name="reason" rows="3" placeholder="What has to change, and which lighter mechanism was tried first.">{$wizard_settings.reason|wash}</textarea>
</div>
</div>

</div>

<div class="ko-col">

<div class="ko-card">
<h2>{'The extension'|i18n( 'design/admin/setup/rad/override' )}</h2>

<div class="ko-field">
    <label for="koName">{'Extension name'|i18n( 'design/admin/setup/rad/override' )}</label>
    <input type="text" id="koName" name="name" value="{$wizard_settings.name|wash}" placeholder="my_override" autocomplete="off" />
</div>

<div class="ko-field">
    <label for="koTitle">{'Title'|i18n( 'design/admin/setup/rad/override' )}</label>
    <input type="text" id="koTitle" name="title" value="{$wizard_settings.title|wash}" />
</div>

<div class="ko-field">
    <label for="koSummary">{'Summary'|i18n( 'design/admin/setup/rad/override' )}</label>
    <input type="text" id="koSummary" name="summary" value="{$wizard_settings.summary|wash}" />
</div>

<div class="ko-row">
    <div class="ko-field">
        <label for="koAuthor">{'Author'|i18n( 'design/admin/setup/rad/override' )}</label>
        <input type="text" id="koAuthor" name="author" value="{$wizard_settings.author|wash}" />
    </div>
    <div class="ko-field">
        <label for="koVendor">{'Composer vendor'|i18n( 'design/admin/setup/rad/override' )}</label>
        <input type="text" id="koVendor" name="vendor" value="{$wizard_settings.vendor|wash}" />
    </div>
    <div class="ko-field">
        <label for="koVersion">{'Version'|i18n( 'design/admin/setup/rad/override' )}</label>
        <input type="text" id="koVersion" name="version" value="{$wizard_settings.version|wash}" />
    </div>
</div>

<div class="ko-field">
    <label for="koLicence">{'Licence'|i18n( 'design/admin/setup/rad/override' )}</label>
    <select id="koLicence" name="licence">
    {foreach $wizard_licences as $ko_key => $ko_label}
        <option value="{$ko_key|wash}"{if eq( $wizard_settings.licence, $ko_key )} selected="selected"{/if}>{$ko_label|wash}</option>
    {/foreach}
    </select>
    <span class="ko-hint">{'This carries kernel code, so what it is licensed under is not an afterthought.'|i18n( 'design/admin/setup/rad/override' )}</span>
</div>

<h3>{'What goes in it'|i18n( 'design/admin/setup/rad/override' )}</h3>
{foreach $wizard_parts as $ko_key => $ko_part}
    <label class="ko-pick">
        <input type="checkbox" name="Parts[]" value="{$ko_key|wash}"{if $wizard_settings.parts[$ko_key]} checked="checked"{/if} />
        <span>
            <span class="ko-pick-label">{$ko_part.label|i18n( 'design/admin/setup/rad/override' )|wash}</span>
            <span class="ko-meta">{$ko_part.description|i18n( 'design/admin/setup/rad/override' )|wash}</span>
        </span>
    </label>
{/foreach}
</div>

<div class="ko-card">
<h2>{'Switching it on'|i18n( 'design/admin/setup/rad/override' )}</h2>
<pre>{$wizard_activation|wash}</pre>
<h3>{'And config.php, which is not the default'|i18n( 'design/admin/setup/rad/override' )}</h3>
<pre>define( 'EZP_AUTOLOAD_ALLOW_KERNEL_OVERRIDE', true );</pre>
<h3>{'Then'|i18n( 'design/admin/setup/rad/override' )}</h3>
<pre>php bin/php/ezpgenerateautoloads.php -o
php bin/php/ezcache.php --clear-all</pre>
<span class="ko-meta">{'The -o run is a different one from the ordinary autoload generation, and writes var/autoload/ezp_override.php. Without it nothing here is loaded.'|i18n( 'design/admin/setup/rad/override' )}</span>
</div>

<div class="ko-card">
<h2>{'After every upgrade'|i18n( 'design/admin/setup/rad/override' )}</h2>
<span class="ko-meta">{'The check the extension ships. Exit status 1 means the kernel changed the file this was copied from and somebody has to decide which of those changes this override needs. Worth failing a build on.'|i18n( 'design/admin/setup/rad/override' )}</span>
{if ne( $wizard_settings.name, '' )}
<pre>php extension/{$wizard_settings.name|wash}/bin/checkdrift.php</pre>
{/if}
</div>

</div>
</div>

{if $wizard_file_count|gt( 0 )}
<div class="ko-card">
<h2>{'Every file, before it is written'|i18n( 'design/admin/setup/rad/override' )}</h2>
<span class="ko-meta">{$wizard_target|wash}</span>

<div class="ko-toolbar">
    <button type="button" class="ko-btn" id="koOpenAll">{'Open all'|i18n( 'design/admin/setup/rad/override' )}</button>
    <button type="button" class="ko-btn" id="koCloseAll">{'Close all'|i18n( 'design/admin/setup/rad/override' )}</button>
</div>

{foreach $wizard_files as $ko_file}
<div class="ko-file is-collapsed">
    <div class="ko-file-head">
        <span class="ko-file-toggle">+</span>
        <span class="ko-file-path">{$ko_file.path|wash}</span>
        <span class="ko-meta">{$ko_file.lines|wash} {'lines'|i18n( 'design/admin/setup/rad/override' )}, {$ko_file.bytes|wash} {'bytes'|i18n( 'design/admin/setup/rad/override' )}</span>
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
    <a class="ko-meta" href={'setup/rad'|ezurl}>{'Back to the RAD tools'|i18n( 'design/admin/setup/rad/override' )}</a>
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
        if ( ( ' ' + divs[i].className + ' ' ).indexOf( ' ko-file ' ) !== -1 )
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
