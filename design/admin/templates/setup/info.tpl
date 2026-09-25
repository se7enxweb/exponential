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
    <th><label>{'Velocity'|i18n( 'design/admin/setup/info' )}: {$velocity_info.engine|wash} ({$velocity_info.role|wash})</label></th>
</tr>
<tr>
<td>
    <div class="block">
        <label>{'Engine'|i18n( 'design/admin/setup/info' )}:</label>
        {$velocity_info.engine|wash} &mdash; {$velocity_info.role_text|wash}.
        {if $velocity_info.is_default}
            {'It is the default engine ([ServerSettings] Engine), which exp:velocity start uses without --engine.'|i18n( 'design/admin/setup/info' )}
        {else}
            {'The default engine is'|i18n( 'design/admin/setup/info' )} {$velocity_info.default|wash}; {'this one runs with'|i18n( 'design/admin/setup/info' )} <code>exp:velocity start --engine={$velocity_info.engine|wash}</code>.
        {/if}
        {if $velocity_info.velocity|not}
            <br /><small>{'This server was not started by exp:velocity (it answers on another port than velocity.ini gives this engine), so the status below is limited to what the request itself shows.'|i18n( 'design/admin/setup/info' )}</small>
        {/if}
    </div>

    <div class="block">
        <label>{'Address'|i18n( 'design/admin/setup/info' )}:</label>
        <a href={$velocity_info.url}>{$velocity_info.url|wash}</a>
        &mdash; {'reachable from'|i18n( 'design/admin/setup/info' )} {$velocity_info.reach|wash}
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
        {'pid'|i18n( 'design/admin/setup/info' )} {$velocity_info.pid|wash}, {$velocity_info.processes|wash} {'process(es)'|i18n( 'design/admin/setup/info' )}
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
        <small>{'Also running from this installation (a test setup; a site runs one engine)'|i18n( 'design/admin/setup/info' )}:
        {foreach $velocity_info.others as $other}{$other|wash}{delimiter}, {/delimiter}{/foreach}.</small>
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
            {'Individual files on disk'|i18n( 'design/admin/setup/info' )}
            &mdash;
            {'the archive below is not being used, because the EXP_ENGINE_PHAR environment variable is not set'|i18n( 'design/admin/setup/info' )}
        {/if}
        {* Where that variable is set depends on the server running this page,
           so say it for that server rather than in general. *}
        <br /><small>{'Served by'|i18n( 'design/admin/setup/info' )} {$engine_info.server|wash}.
        {if eq( $engine_info.source, 'archive' )}
            {'To run from the files on disk again'|i18n( 'design/admin/setup/info' )}: {$engine_info.switch_off|wash}.
        {else}
            {'To run from the archive'|i18n( 'design/admin/setup/info' )}: {$engine_info.switch_on|wash}.
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
        {$engine_info.archive_files} {'files'|i18n( 'design/admin/setup/info' )},
        {$engine_info.archive_bytes} {'bytes'|i18n( 'design/admin/setup/info' )},
        {'built'|i18n( 'design/admin/setup/info' )} {$engine_info.archive_built|wash}
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
            {'disabled'|i18n( 'design/admin/setup/info' )} &mdash; {'every request is rendered'|i18n( 'design/admin/setup/info' )}
        {/if}
    </div>

    {if $response_cache.enabled}
    <div class="block">
        <label>{'Hits'|i18n( 'design/admin/setup/info' )}:</label>
        {if $response_cache.stats_available}
            {$response_cache.hits} {'hits'|i18n( 'design/admin/setup/info' )},
            {$response_cache.misses} {'misses'|i18n( 'design/admin/setup/info' )},
            {$response_cache.hit_rate}&nbsp;% {'since the server started'|i18n( 'design/admin/setup/info' )}
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
            {$response_cache.apcu_entries} {'pages'|i18n( 'design/admin/setup/info' )},
            {$response_cache.apcu_bytes|si( byte )}
            ({'entries up to'|i18n( 'design/admin/setup/info' )} {$response_cache.apcu_max_size|si( byte )};
            {'segment'|i18n( 'design/admin/setup/info' )} {$response_cache.apcu_segment|si( byte )},
            {$response_cache.apcu_free|si( byte )} {'free'|i18n( 'design/admin/setup/info' )})
        {/if}
    </div>

    <div class="block">
        <label>{'On disk'|i18n( 'design/admin/setup/info' )}:</label>
        {if $response_cache.file_readable|not}
            {'the directory does not exist yet or cannot be read'|i18n( 'design/admin/setup/info' )}
        {else}
            {if $response_cache.file_counted_all|not}{'at least'|i18n( 'design/admin/setup/info' )} {/if}{$response_cache.file_entries} {'files'|i18n( 'design/admin/setup/info' )},
            {$response_cache.file_bytes|si( byte )}
        {/if}
        <br /><small><code>{$response_cache.dir|wash}</code>{if $response_cache.dir_mode} &mdash; {'directories'|i18n( 'design/admin/setup/info' )} {$response_cache.dir_mode|wash}{/if}{if $response_cache.file_mode}, {'files'|i18n( 'design/admin/setup/info' )} {$response_cache.file_mode|wash}{/if}</small>
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
        {$system_info.cpu_type} {if $system_info.cpu_speed|is_null|not}{$system_info.cpu_speed} MHz{/if}
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
