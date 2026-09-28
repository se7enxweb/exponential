<div class="context-block">
{* DESIGN: Header START *}<div class="box-header"><div class="box-ml">

<h1 class="context-title">{'System information'|i18n( 'design/admin/setup/info' )}</h1>

{* DESIGN: Mainline *}<div class="header-mainline"></div>

{* DESIGN: Header END *}</div></div>

{* DESIGN: Content START *}<div class="box-bc"><div class="box-ml"><div class="box-content">

<div class="context-attributes">

<table class="list" cellspacing="0">

<tr>
    <th><label>{'Exponential'|i18n( 'design/admin/setup/info' )}</label></th>
</tr>
<tr>
<td>
    <div class="block">
        <label>{'Site'|i18n( 'design/admin/setup/info' )}:</label>
        {ezini('SiteSettings','SiteURL')}
    </div>

    <div class="block">
        <label>{'Version'|i18n( 'design/admin/setup/info', 'Exponential version' )}:</label>
        {$ezpublish_version}
    </div>

    <div class="block">
        <label>{'Extensions'|i18n( 'design/admin/setup/info', 'Exponential extensions' )}:</label>
        {if $ezpublish_extensions}
            {foreach $ezpublish_extensions as $extension}
                {$extension}{delimiter}, {/delimiter}
            {/foreach}
        {else}
            {'Not in use.'|i18n( 'design/admin/setup/info' )}
        {/if}
    </div>

</td>
</tr>
</table>
<table class="list" cellspacing="0">
<tr>
    <th><label>{'PHP'|i18n( 'design/admin/setup/info' )}</label></th>
</tr>
<tr>
<td>
    <div class="block">
        <label>{'Version'|i18n( 'design/admin/setup/info', 'PHP version' )}:</label>
        {$php_version} (<a href={'/setup/info/php'|ezurl}>{'Details'|i18n( 'design/admin/setup/info', 'Detailed PHP information' )}</a>)
    </div>

    <div class="block">
        <label>{'Extensions'|i18n( 'design/admin/setup/info', 'PHP extensions' )}:</label>
        {foreach $php_loaded_extensions as $loadedExtension}{$loadedExtension}{delimiter}, {/delimiter}{/foreach}
    </div>

    <div class="block">
    <label>{'Miscellaneous'|i18n( 'design/admin/setup/info' )}:</label>
        {if $php_ini.safe_mode}
            {'Safe mode is on.'|i18n( 'design/admin/setup/info' )}<br/>
        {else}
            {'Safe mode is off.'|i18n( 'design/admin/setup/info' )}<br/>
        {/if}
        {if $php_ini.open_basedir}
            {'Basedir restriction is on and set to %1.'|i18n( 'design/admin/setup/info',, array( $php_ini.open_basedir ) )}<br/>
        {else}
            {'Basedir restriction is off.'|i18n( 'design/admin/setup/info' )}<br/>
        {/if}
        {if $php_ini.register_globals}
            {'Global variable registration is on.'|i18n( 'design/admin/setup/info' )}<br/>
        {else}
            {'Global variable registration is off.'|i18n( 'design/admin/setup/info' )}<br/>
        {/if}
        {if $php_ini.file_uploads}
            {'File uploading is enabled.'|i18n( 'design/admin/setup/info' )}<br/>
        {else}
            {'File uploading is disabled.'|i18n( 'design/admin/setup/info' )}<br/>
        {/if}
        {'Maximum size of post data (text and files) is %1.'|i18n( 'design/admin/setup/info',, array( $php_ini.post_max_size ) )}<br/>
        {if and( is_set( $php_ini.memory_limit ), $php_ini.memory_limit )}
            {'Script memory limit is %1.'|i18n( 'design/admin/setup/info' ,,array( $php_ini.memory_limit ) )}<br/>
        {else}
            {'Script memory limit is unlimited.'|i18n( 'design/admin/setup/info' )}<br/>
        {/if}
        {'Maximum execution time is %1 seconds.'|i18n( 'design/admin/setup/info',, array( $php_ini.max_execution_time ) )}<br/>
    </div>
</td>
</tr>
</table>

{* The opcode cache and APCu, for whatever serves the page: what matters when
   a change to a PHP file seems not to take, or a cache seems not to work.
   The figures are this server process's; another pool, engine or a
   command-line script has its own. Emptying them is on Setup > Caches. *}
<table id="php-caches" class="list" style="scroll-margin-top: calc(var(--header-height, 4rem) + 1rem);" cellspacing="0">
<tr>
    <th colspan="2"><label>{'OPcache and APCu'|i18n( 'design/admin/setup/info' )}</label></th>
</tr>
<tr>
{* Side by side while both fit, APCu below OPcache on narrow screens. *}
<td colspan="2" style="padding: 0;">
<div style="display: flex; flex-wrap: wrap;">
{foreach hash( 'opcache', 'OPcache', 'apcu', 'APCu' ) as $key => $title}
{def $cache=$php_caches[$key]}
<div style="flex: 1 1 24em; min-width: 0; padding: 0.6em 1em;">
    <div style="margin-bottom: 0.5em;">
        <strong style="font-size: 1.1em;">{$title|wash}</strong>
        <small>&nbsp;{if eq( $key, 'opcache' )}{'compiled PHP scripts'|i18n( 'design/admin/setup/info' )}{else}{'data in shared memory'|i18n( 'design/admin/setup/info' )}{/if}</small>
        &nbsp;
        {if $cache|not}
            <span style="padding: 1px 7px; border-radius: 9px; background: #e0e0e0; color: #555; font-size: 0.85em;">{'not installed'|i18n( 'design/admin/setup/info' )}</span>
        {elseif $cache.enabled}
            <span style="padding: 1px 7px; border-radius: 9px; background: #d8f0d8; color: #1e6b1e; font-size: 0.85em;">{'enabled'|i18n( 'design/admin/setup/info' )}</span>
            {if $cache.version}<small>&nbsp;{$cache.version|wash}</small>{/if}
        {else}
            <span style="padding: 1px 7px; border-radius: 9px; background: #fbe3c8; color: #8a4b00; font-size: 0.85em;">{'off'|i18n( 'design/admin/setup/info' )}</span>
            <br /><small>{$cache.why_off|wash}</small>
        {/if}
    </div>

    {if and( $cache, $cache.bars )}
    <table cellspacing="0" style="width: 100%; margin-bottom: 0.4em;">
    {foreach $cache.bars as $bar}
    <tr>
        <td style="width: 7.5em; padding: 2px 0; white-space: nowrap;"><small>{$bar.label|i18n( 'design/admin/setup/info' )}</small></td>
        <td style="padding: 2px 0.6em 2px 0;">
            {* Green while there is room; amber from 75 %, red from 90 % --
               for the hit rate the other way round, a high rate is good. *}
            {def $level=cond( eq( $bar.label, 'Hit rate' ),
                              cond( ge( $bar.percent, 90 ), '#4a9d4a', ge( $bar.percent, 60 ), '#d99a26', '#c9483b' ),
                              cond( ge( $bar.percent, 90 ), '#c9483b', ge( $bar.percent, 75 ), '#d99a26', '#4a9d4a' ) )}
            <div style="background: #e6e6e6; border-radius: 3px; height: 9px; overflow: hidden;">
                <div style="width: {$bar.percent}%; background: {$level}; height: 9px;"></div>
            </div>
            {undef $level}
        </td>
        <td style="width: 45%; padding: 2px 0; white-space: nowrap;"><small>{$bar.text|wash}</small></td>
    </tr>
    {/foreach}
    </table>
    {/if}

    {if and( $cache, $cache.figures )}
    <div><small>{foreach $cache.figures as $name => $value}{$name|wash}: {$value|wash}{delimiter} &middot; {/delimiter}{/foreach}</small></div>
    {/if}

    {if and( $cache, $cache.settings )}
    <div style="margin-top: 0.3em; color: #666;"><small>{foreach $cache.settings as $name => $value}<code>{$name|wash}</code> {$value|wash}{delimiter} &middot; {/delimiter}{/foreach}</small></div>
    {/if}
</div>
{undef $cache}
{/foreach}
</div>
</td>
</tr>
<tr>
<td colspan="2"><small>{'These figures belong to the server process that answered this page; a command-line script, another php-fpm pool or another engine has caches of its own.'|i18n( 'design/admin/setup/info' )}
{if $can_flush_caches}
    {'Both can be emptied on'|i18n( 'design/admin/setup/info' )} <a href="{'/setup/cache'|ezurl( 'no' )}#php-caches">{'Setup &gt; Caches'|i18n( 'design/admin/setup/info' )}</a>.
{/if}
</small></td>
</tr>
</table>

<table class="list" cellspacing="0">
<tr>
<th><label>{'PHP autoload functions'|i18n( 'design/admin/setup/info' )}</label></th>
</tr>
<tr>
<td>
{if $autoload_functions}
    <ol>
    {foreach $autoload_functions as $key => $function}
        {if is_array( $function )}
            {if $function[0]|is_object()}
                {set $function = concat( $function[0]|get_class(), '::', $function[1] )}
            {else}
                {set $function=$function|implode( '::' )}
            {/if}
        {/if}
        <li>{$function}</li>
    {/foreach}
    </ol>
{/if}
</td>
</tr>
</table>

<table class="list" cellspacing="0">
<tr>
    <th><label>{'Web server (software)'|i18n( 'design/admin/setup/info', 'Web server title' )}</label></th>
</tr>
<tr>
<td>
    {if $webserver_info}

    <div class="block">
        <label>{'Name'|i18n( 'design/admin/setup/info', 'Web server name')}:</label>
        {$webserver_info.name}
    </div>

    <div class="block">
        <label>{'Version'|i18n( 'design/admin/setup/info', 'Web server version')}:</label>
        {$webserver_info.version}
    </div>

    <div class="block">
    <label>{'Modules'|i18n( 'design/admin/setup/info', 'Web server modules')}:</label>
    {if $webserver_info.modules}
        {section loop=$webserver_info.modules}{$:item}{delimiter}, {/delimiter}{/section}
    {else}
        {'The modules of the web server could not be detected.'|i18n( 'design/admin/setup/info', 'Web server modules')}
    {/if}
    </div>

    {else}
        {'Exponential was unable to extract information from the web server.'|i18n( 'design/admin/setup/info' )}
    {/if}
</td>
</tr>
</table>

{* The Velocity engine serving this page, and what it answers itself. Only this
   one in detail: a site runs one engine, and a second or third one running
   beside it is a test setup, named in a single line. *}
{if $velocity_info}
<table class="list" cellspacing="0">
<tr>
    <th><label>{$velocity_info.brand|wash} &mdash; {$velocity_info.engine_name|wash}</label></th>
</tr>
<tr>
<td>
    <div class="block">
        <label>{'Engine'|i18n( 'design/admin/setup/info' )}:</label>
        {$velocity_info.engine_name|wash} (<code>{$velocity_info.engine|wash}</code>) &mdash; {$velocity_info.role_text|wash}.
        {if $velocity_info.is_default}
            {'It is the default engine ([ServerSettings] Engine), which exp:velocity start uses without --engine.'|i18n( 'design/admin/setup/info' )}
        {else}
            {'The default engine is %default; this one runs with %command.'|i18n( 'design/admin/setup/info',, hash( '%default', $velocity_info.default|wash, '%command', concat( '<code>exp:velocity start --engine=', $velocity_info.engine|wash, '</code>' ) ) )}
        {/if}
        {if $velocity_info.velocity|not}
            <br /><small>{'This server was not started by exp:velocity (it answers on another port than velocity.ini gives this engine), so the status below is limited to what the request itself shows.'|i18n( 'design/admin/setup/info' )}</small>
        {/if}
    </div>

    <div class="block">
        <label>{'Address'|i18n( 'design/admin/setup/info' )}:</label>
        <a href={$velocity_info.url}>{$velocity_info.url|wash}</a>
        &mdash; {'reachable from %reach'|i18n( 'design/admin/setup/info',, hash( '%reach', $velocity_info.reach|wash ) )}
    </div>

    {if $velocity_info.version}
    <div class="block">
        <label>{'Version'|i18n( 'design/admin/setup/info' )}:</label>
        {$velocity_info.version|wash}
    </div>
    {/if}

    {if $velocity_info.pid}
    <div class="block">
        <label>{'Process'|i18n( 'design/admin/setup/info' )}:</label>
        {'pid %pid, %processes process(es)'|i18n( 'design/admin/setup/info',, hash( '%pid', $velocity_info.pid|wash, '%processes', $velocity_info.processes|wash ) )}
        {if $velocity_info.log}<br /><small>{'Console log'|i18n( 'design/admin/setup/info' )}: {$velocity_info.log|wash}</small>{/if}
        {if $velocity_info.config}<br /><small>{'Configuration'|i18n( 'design/admin/setup/info' )}: {$velocity_info.config|wash}</small>{/if}
    </div>
    {/if}

    {* What the server answers itself, with who may open it: an admin view
       that opens here may well answer 403 from another machine, and that is
       intended, not a fault. Tokens are never put into these links. *}
    <div class="block">
        <label>{'Views of the server'|i18n( 'design/admin/setup/info' )}:</label>
        <table class="list" cellspacing="0">
        <tr>
            <th>{'View'|i18n( 'design/admin/setup/info' )}</th>
            <th>{'Type'|i18n( 'design/admin/setup/info' )}</th>
            <th>{'What it is'|i18n( 'design/admin/setup/info' )}</th>
            <th>{'Who may open it'|i18n( 'design/admin/setup/info' )}</th>
        </tr>
        {foreach $velocity_info.views as $view sequence array( 'bglight', 'bgdark' ) as $style}
        <tr class="{$style}">
            <td>{if $view.url}<a href={$view.url} target="_blank">{$view.path|wash}</a>{else}<code>{$view.path|wash}</code>{/if}</td>
            <td>{$view.type|wash}</td>
            <td>{$view.description|wash}</td>
            <td>{$view.access|wash}</td>
        </tr>
        {/foreach}
        </table>
    </div>

    {if $velocity_info.notes}
    <div class="block">
        <label>{'Notes'|i18n( 'design/admin/setup/info' )}:</label>
        {foreach $velocity_info.notes as $note}{$note|wash}{delimiter}<br />{/delimiter}{/foreach}
    </div>
    {/if}

    {if $velocity_info.others}
    <div class="block">
        <label>{'Other engines running'|i18n( 'design/admin/setup/info' )}:</label>
        {foreach $velocity_info.others as $other}
            {$other.name|wash} (<code>{$other.engine|wash}</code>, {$other.role|wash})
            {foreach $other.urls as $url}<a href="{$url|wash}">{$url|wash}</a>{delimiter} {'and'|i18n( 'design/admin/setup/info' )} {/delimiter}{/foreach}
            &mdash; <small>{'stop it with %command'|i18n( 'design/admin/setup/info',, hash( '%command', concat( '<code>exp:velocity stop --engine=', $other.engine|wash, '</code>' ) ) )}</small>{delimiter}<br />{/delimiter}
        {/foreach}
        <br /><small>{'Started from this installation as well. A site is served by one engine; another one running is usually left from a test or a benchmark.'|i18n( 'design/admin/setup/info' )}</small>
    </div>
    {/if}
</td>
</tr>
</table>
{/if}

{if and( $velocity_info, eq( $velocity_info.engine, 'qbix' ) )}
{* Where the running engine came from -- shown for the qbix engine only (the
   user's choice), where it is switched with [ServerSettings] EnginePhar. Worth its own block rather than a line
   in Miscellaneous: when a kernel edit appears to do nothing, or a stack trace
   names a phar:// path, this is the first thing to read. *}
<table class="list" cellspacing="0">
<tr>
    <th><label>{'Phar App Engine'|i18n( 'design/admin/setup/info' )}</label></th>
</tr>
<tr>
<td>
    {* Say which one is running before anything else, and say why.
       The panel used to list an archive, its version and whether it matched the
       working tree without ever making clear that none of it was in use, so it
       read as though the archive might be running and might be stale. *}
    <div class="block">
        <label>{'Loaded from'|i18n( 'design/admin/setup/info' )}:</label>
        {if eq( $engine_info.source, 'archive' )}
            {'The archive'|i18n( 'design/admin/setup/info' )} &mdash; {$engine_info.archive|wash}
        {else}
            {'Individual files on disk &mdash; the archive below is not being used, because the EXP_ENGINE_PHAR environment variable is not set'|i18n( 'design/admin/setup/info' )}
        {/if}
        {* Where that variable is set depends on the server running this page,
           so say it for that server rather than in general. *}
        <br /><small>{'Served by %server.'|i18n( 'design/admin/setup/info',, hash( '%server', $engine_info.server|wash ) )}
        {if eq( $engine_info.source, 'archive' )}
            {'To run from the files on disk again: %command.'|i18n( 'design/admin/setup/info',, hash( '%command', $engine_info.switch_off|wash ) )}
        {else}
            {'To run from the archive: %command.'|i18n( 'design/admin/setup/info',, hash( '%command', $engine_info.switch_on|wash ) )}
        {/if}
        </small>
    </div>

    <div class="block">
        <label>{'Installation root'|i18n( 'design/admin/setup/info' )}:</label>
        {$engine_info.root|wash}
    </div>

    {if eq( $engine_info.source, 'archive' )}
    <div class="block">
        <label>{'Archive'|i18n( 'design/admin/setup/info' )}:</label>
        {'%files files, %bytes bytes, built %built'|i18n( 'design/admin/setup/info',, hash( '%files', $engine_info.archive_files, '%bytes', $engine_info.archive_bytes, '%built', $engine_info.archive_built|wash ) )}
    </div>
    {else}
    <div class="block">
        <label>{'Archive on disk'|i18n( 'design/admin/setup/info' )}:</label>
        {$engine_info.archive|wash}
    </div>
    {/if}

    <div class="block">
        <label>{'Archive was built from'|i18n( 'design/admin/setup/info' )}:</label>
        {if $engine_info.version}{$engine_info.version|wash}{else}&ndash;{/if}
    </div>

    {* Only worth saying when it would change what somebody does. An archive
       that is not running cannot explain a surprising result, so the mismatch
       is framed as "rebuild before switching" rather than as a failure. *}
    <div class="block">
        <label>{'Archive is current'|i18n( 'design/admin/setup/info' )}:</label>
        {$engine_info.matches_repo|wash}
        {* Why, and what to do -- a bare "no" leaves a reader to work out which
           of two version strings is which and whether it matters. *}
        {if $engine_info.stale_reason}
            <br /><small>{'Why'|i18n( 'design/admin/setup/info' )}: {$engine_info.stale_reason|wash}.</small>
            <br /><small>{'To fix'|i18n( 'design/admin/setup/info' )}: <code>{$engine_info.stale_fix|wash}</code></small>
            {if ne( $engine_info.source, 'archive' )}
                <br /><small>{'Nothing is running from the archive at the moment, so this is not affecting the site.'|i18n( 'design/admin/setup/info' )}</small>
            {else}
                <br /><small>{'The site is running from this archive, so what is on disk is not what is being served.'|i18n( 'design/admin/setup/info' )}</small>
            {/if}
        {/if}
    </div>

    {* Two unrelated facts, which were on one line and read as one. The wrapper
       is off because letting any path-taking function reach inside an archive
       is a real hazard on a site that accepts image uploads; phar.readonly is
       a separate php.ini setting about whether an archive can be written. *}
    <div class="block">
        <label>{'Phar stream wrapper'|i18n( 'design/admin/setup/info' )}:</label>
        {$engine_info.phar_wrapper|wash}
        {if eq( $engine_info.phar_wrapper, 'unregistered' )}
            <br /><small>{'Deliberate: with it registered, a file that is both a valid image and a valid archive can be executed through a phar:// path.'|i18n( 'design/admin/setup/info' )}</small>
        {/if}
    </div>

    <div class="block">
        <label>{'Writing archives (phar.readonly)'|i18n( 'design/admin/setup/info' )}:</label>
        {$engine_info.phar_readonly|wash}
    </div>

</td>
</tr>
</table>
{/if}

{if $http_cache}
<table class="list" cellspacing="0" id="http-cache">
<tr>
    <th><label>{'HTTP cache (role-aware)'|i18n( 'design/admin/setup/info' )}</label></th>
</tr>
<tr>
<td style="padding: 0.6em 1em;">
    <div style="margin-bottom: 0.5em;">
        <strong style="font-size: 1.1em;">{'Whole pages, per permission context'|i18n( 'design/admin/setup/info' )}</strong>
        &nbsp;
        {if $http_cache.enabled|not}
            <span style="padding: 1px 7px; border-radius: 9px; background: #e0e0e0; color: #555; font-size: 0.85em;">{'disabled'|i18n( 'design/admin/setup/info' )}</span>
            <br /><small>{'Switch it on with Enabled=enabled in settings/httpcache.ini (an override); every page is rendered until then.'|i18n( 'design/admin/setup/info' )}</small>
        {elseif $http_cache.started|not}
            <span style="padding: 1px 7px; border-radius: 9px; background: #fbe3c8; color: #8a4b00; font-size: 0.85em;">{'enabled, nothing stored yet'|i18n( 'design/admin/setup/info' )}</span>
            <br /><small>{'The first page requested on a cached siteaccess starts it.'|i18n( 'design/admin/setup/info' )}</small>
        {else}
            <span style="padding: 1px 7px; border-radius: 9px; background: #d8f0d8; color: #1e6b1e; font-size: 0.85em;">{'enabled'|i18n( 'design/admin/setup/info' )}</span>
        {/if}
    </div>

    {if $http_cache.message}
    <div style="margin: 0.4em 0; padding: 0.4em 0.7em; border-radius: 4px; background: #eef6ee; color: #1e6b1e;"><small>{$http_cache.message|wash}</small></div>
    {/if}

    {if $http_cache.started}
    {if $http_cache.bars}
    <table cellspacing="0" style="width: 100%; margin-bottom: 0.4em;">
    {foreach $http_cache.bars as $bar}
    <tr>
        <td style="width: 7.5em; padding: 2px 0; white-space: nowrap;"><small>{$bar.label|i18n( 'design/admin/setup/info' )}</small></td>
        <td style="padding: 2px 0.6em 2px 0;">
            {def $level=cond( ge( $bar.percent, 90 ), '#4a9d4a', ge( $bar.percent, 60 ), '#d99a26', '#c9483b' )}
            <div style="background: #e6e6e6; border-radius: 3px; height: 9px; overflow: hidden;">
                <div style="width: {$bar.percent}%; background: {$level}; height: 9px;"></div>
            </div>
            {undef $level}
        </td>
        <td style="width: 45%; padding: 2px 0; white-space: nowrap;"><small>{$bar.text|wash}</small></td>
    </tr>
    {/foreach}
    </table>
    {elseif $http_cache.stats|not}
    <div><small>{'No server has counted yet: hit counts need APCu in the PHP that serves the site.'|i18n( 'design/admin/setup/info' )}</small></div>
    {/if}

    <div><small>{foreach $http_cache.figures as $name => $value}{$name|wash}: <strong>{$value|wash}</strong>{delimiter} &middot; {/delimiter}{/foreach}</small></div>

    {if $http_cache.reasons}
    <div style="margin-top: 0.5em;"><small>{'Why requests were not served from the cache'|i18n( 'design/admin/setup/info' )}:</small></div>
    <table cellspacing="0" style="width: 100%; max-width: 40em;">
    {foreach $http_cache.reasons as $r}
    <tr>
        <td style="width: 14em; padding: 1px 0; white-space: nowrap;"><small><code>{$r.reason|wash}</code></small></td>
        <td style="padding: 1px 0.6em 1px 0;">
            <div style="background: #e6e6e6; border-radius: 3px; height: 7px; overflow: hidden;"><div style="width: {$r.percent}%; background: #8a9bb0; height: 7px;"></div></div>
        </td>
        <td style="width: 6em; padding: 1px 0; text-align: right;"><small>{$r.count|wash}</small></td>
    </tr>
    {/foreach}
    </table>
    {/if}

    <div style="margin-top: 0.4em; color: #666;"><small>{foreach $http_cache.settings as $name => $value}<code>{$name|wash}</code> {$value|wash}{delimiter} &middot; {/delimiter}{/foreach}</small></div>
    <div style="color: #666;"><small><code>{$http_cache.dir|wash}</code></small></div>

    {if $can_flush_caches}
    <form method="post" action={'/setup/info'|ezurl} style="margin-top: 0.7em;">
        <button type="submit" class="button" name="HttpCacheAction" value="purge" onclick="return confirm( '{'Purge every cached page?'|i18n( 'design/admin/setup/info' )|wash( javascript )}' );">{'Purge all pages'|i18n( 'design/admin/setup/info' )}</button>
        <button type="submit" class="button" name="HttpCacheAction" value="gc">{'Remove dead entries'|i18n( 'design/admin/setup/info' )}</button>
        {if $http_cache.stats}<button type="submit" class="button" name="HttpCacheAction" value="reset">{'Reset counters'|i18n( 'design/admin/setup/info' )}</button>{/if}
    </form>
    {/if}
    {/if}
</td>
</tr>
</table>
{/if}

{if $sql_profile}
<table class="list" cellspacing="0" id="database-queries">
<tr>
    <th><label>{'Database queries'|i18n( 'design/admin/setup/info' )}</label></th>
</tr>
<tr>
<td style="padding: 0.6em 1em;">
    <div style="margin-bottom: 0.5em;">
        <strong style="font-size: 1.1em;">{'SQL statements per request'|i18n( 'design/admin/setup/info' )}</strong>
        <small>&nbsp;<code>{$sql_profile.engine|wash}</code></small>
        &nbsp;
        {if $sql_profile.mongo}
            <span style="padding: 1px 7px; border-radius: 9px; background: #e0e0e0; color: #555; font-size: 0.85em;">{'MongoDB: not an SQL engine'|i18n( 'design/admin/setup/info' )}</span>
            <br /><small>{'The query cache is for the SQL engines. The MongoDB driver keeps its own statement profile (var/tmp/mongo_profile.on).'|i18n( 'design/admin/setup/info' )}</small>
        {elseif $sql_profile.on}
            <span style="padding: 1px 7px; border-radius: 9px; background: #d8f0d8; color: #1e6b1e; font-size: 0.85em;">{'profile on'|i18n( 'design/admin/setup/info' )}</span>
        {else}
            <span style="padding: 1px 7px; border-radius: 9px; background: #e0e0e0; color: #555; font-size: 0.85em;">{'profile off'|i18n( 'design/admin/setup/info' )}</span>
        {/if}
    </div>

    {if $sql_profile.message}
    <div style="margin: 0.4em 0; padding: 0.4em 0.7em; border-radius: 4px; background: #eef6ee; color: #1e6b1e;"><small>{$sql_profile.message|wash}</small></div>
    {/if}

    {if and( $sql_profile.mongo|not, $query_cache )}
    <div style="margin: 0.3em 0 0.8em 0; padding: 0.5em 0.8em; border: 1px solid #e3e3e3; border-radius: 4px;">
        <strong>{'Query cache'|i18n( 'design/admin/setup/info' )}</strong>
        &nbsp;
        {if $query_cache.enabled}
            <span style="padding: 1px 7px; border-radius: 9px; background: #d8f0d8; color: #1e6b1e; font-size: 0.85em;">{$query_cache.mode|wash}</span>
        {else}
            <span style="padding: 1px 7px; border-radius: 9px; background: #e0e0e0; color: #555; font-size: 0.85em;">{'off'|i18n( 'design/admin/setup/info' )}</span>
        {/if}
        <small>&nbsp;{'settings/querycache.ini'|i18n( 'design/admin/setup/info' )}: MaxAge={$query_cache.max_age}&nbsp;s, MaxRows={$query_cache.max_rows}{if $query_cache.exclude}, ExcludeTables={$query_cache.exclude|implode( ', ' )|wash}{/if}</small>
        <div style="margin-top: 0.3em;"><small>
            {if $query_cache.mode|eq( 'shared' )}
                {if $query_cache.apcu}
                    {'%entries results held in APCu (%size) by this server'|i18n( 'design/admin/setup/info',, hash( '%entries', concat( '<strong>', $query_cache.entries, '</strong>' ), '%size', concat( $query_cache.memory_kb, '&nbsp;KB' ) ) )}
                {else}
                    <span style="color: #a33;">{'APCu is not available to this server: "shared" works as "request" here.'|i18n( 'design/admin/setup/info' )}</span>
                {/if}
                &middot;
            {/if}
            {'generation %generation'|i18n( 'design/admin/setup/info',, hash( '%generation', concat( '<strong>', $query_cache.generation, '</strong>' ) ) )}{if $query_cache.cleared}, {'last cleared %date'|i18n( 'design/admin/setup/info',, hash( '%date', $query_cache.cleared|l10n( shortdatetime ) ) )}{/if}
            &middot; {'%tables tables written since'|i18n( 'design/admin/setup/info',, hash( '%tables', concat( '<strong>', $query_cache.tables_tracked, '</strong>' ) ) )}
            {if $query_cache.state_exists|not}<span style="color: #666;">({'no state file yet'|i18n( 'design/admin/setup/info' )})</span>{/if}
        </small></div>
        {if $query_cache.counters}
        <div style="margin-top: 0.2em;"><small>
            {'This server since %date: %requests requests, %hits hits, %misses misses'|i18n( 'design/admin/setup/info',, hash( '%date', cond( $query_cache.counters.since, $query_cache.counters.since|l10n( shortdatetime ), '-' ), '%requests', concat( '<strong>', $query_cache.counters.requests, '</strong>' ), '%hits', concat( '<strong>', $query_cache.counters.hits, '</strong>' ), '%misses', concat( '<strong>', $query_cache.counters.misses, '</strong>' ) ) )}{if $query_cache.counters.hit_rate|ne( '' )} ({'%rate hit rate'|i18n( 'design/admin/setup/info',, hash( '%rate', concat( '<strong>', $query_cache.counters.hit_rate, '&nbsp;%</strong>' ) ) )}){/if},
            {'%uncacheable not cacheable, %writes writes'|i18n( 'design/admin/setup/info',, hash( '%uncacheable', $query_cache.counters.uncacheable, '%writes', $query_cache.counters.writes ) )}
        </small></div>
        {/if}
        {if $query_cache.recent_writes}
        <div style="margin-top: 0.2em; color: #555;"><small>
            {'Last written'|i18n( 'design/admin/setup/info' )}:
            {foreach $query_cache.recent_writes as $w}<code>{$w.table|wash}</code> {$w.ago}&nbsp;s{delimiter}, {/delimiter}{/foreach}
        </small></div>
        {/if}
        {if $can_flush_caches}
        <form method="post" action={'/setup/info'|ezurl} style="margin-top: 0.5em;">
            <button type="submit" class="button" name="QueryCacheAction" value="clear">{'Clear the query cache'|i18n( 'design/admin/setup/info' )}</button>
            {if $query_cache.counters}<button type="submit" class="button" name="QueryCacheAction" value="reset">{'Reset the counters'|i18n( 'design/admin/setup/info' )}</button>{/if}
        </form>
        {/if}
    </div>
    {/if}

    {if $sql_profile.mongo|not}
    {if $sql_profile.summary}
    <div><small>
        {'Over the last %n profiled requests: %statements statements, %repeats exact repeats (%repeat_pct), %db_ms in the database'|i18n( 'design/admin/setup/info',, hash( '%n', $sql_profile.summary.requests, '%statements', concat( '<strong>', $sql_profile.summary.statements, '</strong>' ), '%repeats', concat( '<strong>', $sql_profile.summary.repeats, '</strong>' ), '%repeat_pct', concat( $sql_profile.summary.repeat_pct, '&nbsp;%' ), '%db_ms', concat( '<strong>', $sql_profile.summary.db_ms, '&nbsp;ms</strong>' ) ) )}
        &middot; {'a per-request memo would save %memo_ms, a shared query cache about %shared_ms'|i18n( 'design/admin/setup/info',, hash( '%memo_ms', concat( '<strong>', $sql_profile.summary.memo_ms, '&nbsp;ms</strong>' ), '%shared_ms', concat( '<strong>', $sql_profile.summary.shared_ms, '&nbsp;ms</strong>' ) ) )}
    </small></div>

    <table class="list" cellspacing="0" style="margin-top: 0.4em;">
    <tr>
        <th><small>{'Time'|i18n( 'design/admin/setup/info' )}</small></th>
        <th><small>{'Request'|i18n( 'design/admin/setup/info' )}</small></th>
        <th style="text-align: right;"><small>{'Statements'|i18n( 'design/admin/setup/info' )}</small></th>
        <th style="text-align: right;"><small>{'Distinct'|i18n( 'design/admin/setup/info' )}</small></th>
        <th style="text-align: right;"><small>{'Repeats'|i18n( 'design/admin/setup/info' )}</small></th>
        <th style="text-align: right;"><small>{'In the database'|i18n( 'design/admin/setup/info' )}</small></th>
        <th style="text-align: right;"><small>{'Memo saves'|i18n( 'design/admin/setup/info' )}</small></th>
        <th style="text-align: right;"><small>{'Shared cache saves'|i18n( 'design/admin/setup/info' )}</small></th>
    </tr>
    {foreach $sql_profile.rows as $row sequence array( 'bglight', 'bgdark' ) as $style}
    <tr class="{$style}">
        <td><small>{$row.time|wash}</small></td>
        <td style="max-width: 22em; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;"><small><code>{$row.uri|wash}</code></small></td>
        <td style="text-align: right;"><small>{$row.statements}</small></td>
        <td style="text-align: right;"><small>{$row.distinct}</small></td>
        <td style="text-align: right;"><small>{$row.repeats}</small></td>
        <td style="text-align: right;"><small>{$row.db_ms}&nbsp;ms</small></td>
        <td style="text-align: right;"><small>{$row.memo_ms}&nbsp;ms</small></td>
        <td style="text-align: right;"><small>{$row.shared_ms}&nbsp;ms</small></td>
    </tr>
    {/foreach}
    </table>
    {elseif $sql_profile.on}
    <div><small>{'On, and nothing profiled yet: open a few pages.'|i18n( 'design/admin/setup/info' )}</small></div>
    {else}
    <div><small>{'Switch the profile on to see how many statements each request runs, how many are exact repeats, and what a query cache would save.'|i18n( 'design/admin/setup/info' )}</small></div>
    {/if}

    <div style="margin-top: 0.4em; color: #666;"><small>{'The profile counts the statements that reached the database: with the query cache on, a cached answer is not in it. "Memo saves" is what the request mode would save, "shared cache saves" what the shared mode would (about 15 µs per answer). Counters are per server, since each server has its own APCu. The log is var/tmp/sql_profile.log; see doc/bc/6.0/sql-query-cache.md.'|i18n( 'design/admin/setup/info' )}</small></div>

    {if $can_flush_caches}
    <form method="post" action={'/setup/info'|ezurl} style="margin-top: 0.7em;">
        {if $sql_profile.on}
            <button type="submit" class="button" name="SQLProfileAction" value="off">{'Switch the SQL profile off'|i18n( 'design/admin/setup/info' )}</button>
        {else}
            <button type="submit" class="button" name="SQLProfileAction" value="on">{'Switch the SQL profile on'|i18n( 'design/admin/setup/info' )}</button>
        {/if}
    </form>
    {/if}
    {/if}
</td>
</tr>
</table>
{/if}

{if $response_cache}
<table class="list" cellspacing="0">
<tr>
    <th><label>{'Response cache (web server)'|i18n( 'design/admin/setup/info' )}</label></th>
</tr>
<tr>
<td>
    <div class="block">
        <label>{'Status'|i18n( 'design/admin/setup/info' )}:</label>
        {if $response_cache.enabled}
            {'enabled'|i18n( 'design/admin/setup/info' )}
            {if $response_cache.default_ttl|gt( 0 )}
                &mdash; {'pages without a lifetime of their own are kept for %seconds seconds'|i18n( 'design/admin/setup/info', '', hash( '%seconds', $response_cache.default_ttl ) )}
            {else}
                &mdash; {'only responses that bring their own max-age are kept; Exponential sends no-cache, so its pages are not'|i18n( 'design/admin/setup/info' )}
            {/if}
        {else}
            {'disabled &mdash; every request is rendered'|i18n( 'design/admin/setup/info' )}
        {/if}
    </div>

    {if $response_cache.enabled}
    <div class="block">
        <label>{'Hits'|i18n( 'design/admin/setup/info' )}:</label>
        {if $response_cache.stats_available}
            {'%hits hits, %misses misses, %rate since the server started'|i18n( 'design/admin/setup/info',, hash( '%hits', $response_cache.hits, '%misses', $response_cache.misses, '%rate', concat( $response_cache.hit_rate, '&nbsp;%' ) ) )}
        {else}
            {'not available'|i18n( 'design/admin/setup/info' )}
        {/if}
    </div>

    <div class="block">
        <label>{'Shared memory (APCu)'|i18n( 'design/admin/setup/info' )}:</label>
        {if $response_cache.apcu_usable|not}
            {if $response_cache.apcu_configured}
                {'configured, but APCu is not enabled for this PHP process (apc.enable_cli) -- entries are kept on disk only'|i18n( 'design/admin/setup/info' )}
            {else}
                {'not used'|i18n( 'design/admin/setup/info' )}
            {/if}
        {elseif $response_cache.apcu_configured|not}
            {'available, but switched off for the response cache'|i18n( 'design/admin/setup/info' )}
        {else}
            {'%pages pages, %size (entries up to %max_size; segment %segment, %free free)'|i18n( 'design/admin/setup/info',, hash( '%pages', $response_cache.apcu_entries, '%size', $response_cache.apcu_bytes|si( byte ), '%max_size', $response_cache.apcu_max_size|si( byte ), '%segment', $response_cache.apcu_segment|si( byte ), '%free', $response_cache.apcu_free|si( byte ) ) )}
        {/if}
    </div>

    <div class="block">
        <label>{'On disk'|i18n( 'design/admin/setup/info' )}:</label>
        {if $response_cache.file_readable|not}
            {'the directory does not exist yet or cannot be read'|i18n( 'design/admin/setup/info' )}
        {else}
            {if $response_cache.file_counted_all|not}{'at least %files files, %size'|i18n( 'design/admin/setup/info',, hash( '%files', $response_cache.file_entries, '%size', $response_cache.file_bytes|si( byte ) ) )}{else}{'%files files, %size'|i18n( 'design/admin/setup/info',, hash( '%files', $response_cache.file_entries, '%size', $response_cache.file_bytes|si( byte ) ) )}{/if}
        {/if}
        <br /><small><code>{$response_cache.dir|wash}</code>{if $response_cache.dir_mode} &mdash; {'directories %mode'|i18n( 'design/admin/setup/info',, hash( '%mode', $response_cache.dir_mode|wash ) )}{/if}{if $response_cache.file_mode}, {'files %mode'|i18n( 'design/admin/setup/info',, hash( '%mode', $response_cache.file_mode|wash ) )}{/if}</small>
    </div>

    <div class="block">
        <label>{'Not cached for visitors carrying'|i18n( 'design/admin/setup/info' )}:</label>
        {if $response_cache.skip_cookies}
            {foreach $response_cache.skip_cookies as $cookie}<code>{$cookie|wash}</code>{delimiter}, {/delimiter}{/foreach}
            <small>({'matched as a prefix'|i18n( 'design/admin/setup/info' )})</small>
        {else}
            <strong>{'no cookie -- signed-in visitors would be served from the cache'|i18n( 'design/admin/setup/info' )}</strong>
        {/if}
    </div>

    <div class="block">
        <label>{'Further settings'|i18n( 'design/admin/setup/info' )}:</label>
        {'stale pages served while one request renders'|i18n( 'design/admin/setup/info' )}: {if $response_cache.stale_while_revalidate|gt( 0 )}{$response_cache.stale_while_revalidate}&nbsp;s{else}{'no'|i18n( 'design/admin/setup/info' )}{/if};
        {'not-found pages remembered'|i18n( 'design/admin/setup/info' )}: {if $response_cache.negative_ttl|gt( 0 )}{$response_cache.negative_ttl}&nbsp;s{else}{'no'|i18n( 'design/admin/setup/info' )}{/if};
        {'HTML minified'|i18n( 'design/admin/setup/info' )}: {if $response_cache.minify_html}{'yes'|i18n( 'design/admin/setup/info' )}{else}{'no'|i18n( 'design/admin/setup/info' )}{/if};
        {'expired files swept'|i18n( 'design/admin/setup/info' )}: {if $response_cache.sweep_every|gt( 0 )}{'every %seconds s'|i18n( 'design/admin/setup/info', '', hash( '%seconds', $response_cache.sweep_every ) )}{if $response_cache.sweep_max_age|gt( 0 )}, {'nothing older than %seconds s'|i18n( 'design/admin/setup/info', '', hash( '%seconds', $response_cache.sweep_max_age ) )}{/if}{else}{'no'|i18n( 'design/admin/setup/info' )}{/if}
    </div>
    {/if}
</td>
</tr>
</table>
{/if}

<table class="list" cellspacing="0">
<tr>
    <th><label>{'Web server (hardware)'|i18n( 'design/admin/setup/info' )}</label></th>
</tr>
<tr>
<td>
    <div class="block">
        <label>{'CPU'|i18n( 'design/admin/setup/info', 'CPU info' )}:</label>
        {$system_info.cpu_type} {if $system_info.cpu_speed|is_null|not}{'%speed MHz'|i18n( 'design/admin/setup/info',, hash( '%speed', $system_info.cpu_speed ) )}{/if}
    </div>

    <div class="block">
        <label>{'Memory'|i18n( 'design/admin/setup/info', 'Memory info' )}:</label>
        {$system_info.memory_size|si( byte )}
    </div>
</td>
</tr>
</table>

<table class="list" cellspacing="0">
<tr>
<th><label>{'Database'|i18n( 'design/admin/setup/info' )}</label></th>
</tr>
<tr>
<td>
<div class="block">
    <label>{'Type'|i18n( 'design/admin/setup/info', 'Database type' )}:</label>
    {$database_info}
</div>

<div class="block">
    <label>{'Server'|i18n( 'design/admin/setup/info', 'Database server' )}:</label>
    {$database_object.database_server}
</div>

<div class="block">
    <label>{'Socket path'|i18n( 'design/admin/setup/info', 'Database socket path' )}:</label>
    {if $database_object.database_socket_path}
        {$database_object.database_socket_path}
    {else}
        {'Not in use.'|i18n( 'design/admin/setup/info' )}
    {/if}
</div>

<div class="block">
    <label>{'Database name'|i18n( 'design/admin/setup/info', 'Database name' )}:</label>
    {$database_object.database_name}
</div>

<div class="block">
    <label>{'Connection retry count'|i18n( 'design/admin/setup/info', 'Database retry count' )}:</label>
    {$database_object.retry_count}
</div>

<div class="block">
    <label>{'Character set'|i18n( 'design/admin/setup/info', 'Database charset' )}:</label>
    {$database_charset|wash}{if $database_object.is_internal_charset} ({'Internal'|i18n( 'design/admin/setup/info' )}){/if}
</div>

</td>
</tr>
</table>
<table class="list" cellspacing="0">
<tr>
<th><label>{'Slave database (read only)'|i18n( 'design/admin/setup/info' )}</label></th>
</tr>
<tr>
<td>
{if $database_object.use_slave_server}

    <div class="block">
        <label>{'Server'|i18n( 'design/admin/setup/info', 'Database server' )}:</label>
        {$database_object.slave_database_server}
    </div>

    <div class="block">
        <label>{'Database'|i18n( 'design/admin/setup/info', 'Database name' )}:</label>
        {$database_object.slave_database_name}
    </div>

{else}

    {'There is no slave database in use.'|i18n( 'design/admin/setup/info' )}

{/if}
</td>
</tr>
</table>


</div>

{* DESIGN: Content END *}</div></div></div>

</div>
