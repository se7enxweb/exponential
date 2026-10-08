{* Setup > System information: which server answered this page and how it runs PHP, the health checks with what to do,
   an overview card per area, and below it the detailed parts (OPcache and APCu, Velocity, the engine archive, the
   HTTP cache, database queries, the response cache, the database connection), folded.

   The facts come from expSystemReport ($system_report, masked: no secret and no full server path), shared with
   ./console exp:system:info. The figures belong to the process that answered: Apache's PHP-FPM pool and
   Exponential Velocity each have their own PHP, OPcache and settings. Sizes of var/, the cache directories and a
   database on a server are measured only on request (setup/info/sizes). The page itself changes nothing; the
   buttons of the HTTP cache, the query cache and the SQL profile post to it and need setup/managecache.

   The same file is in design/admin and design/admin4. The styling is setup/info_exp_style.tpl, scoped to
   .exp-sysinfo. Everything works without javascript, except the "Copy" button, which is only shown with it.
   Guide: doc/guides/system-information.md *}
{include uri='design:setup/info_exp_style.tpl'}
{def $si = $system_report
     $si_state_class = hash( 'ok', 'is-ok', 'warn', 'is-warn', 'fail', 'is-bad', 'info', 'is-info' )
     $si_state_label = hash( 'ok', 'OK'|i18n( 'design/admin/setup/info' ),
                             'warn', 'Warning'|i18n( 'design/admin/setup/info' ),
                             'fail', 'Failure'|i18n( 'design/admin/setup/info' ),
                             'info', 'Note'|i18n( 'design/admin/setup/info' ) )
     $si_open_checks = 0
     $si_level = ''}
<div class="context-block exp-sysinfo" id="exp-sysinfo"
     data-sysinfo-server="{$si.server_kind|wash}"
     data-sysinfo-label="{$si.server|wash}"
     data-sysinfo-sapi="{$si.facts.php.sapi|wash}"
     data-sysinfo-php="{$si.facts.php.version|wash}"
     data-sysinfo-memory-limit="{$si.facts.php.memory_limit|wash}"
     data-sysinfo-max-execution-time="{$si.facts.php.max_execution_time|wash}"
     data-sysinfo-opcache="{if $si.facts.opcache.enabled}on{else}off{/if}{if $si.facts.opcache.status} (statistics){/if}"
     data-sysinfo-file-update-protection="{$si.facts.opcache.file_update_protection|wash}"
     data-sysinfo-database="{$si.facts.database.engine|wash} {$si.facts.database.version|wash}"
     data-sysinfo-health="{$si.summary.ok|wash} ok, {$si.summary.warn|wash} warn, {$si.summary.fail|wash} fail, {$si.summary.info|wash} info">

<div class="box-header"><div class="box-tc"><div class="box-ml"><div class="box-mr"><div class="box-tl"><div class="box-tr">
<h1 class="context-title">{'System information'|i18n( 'design/admin/setup/info' )}</h1>
<div class="header-mainline"></div>
</div></div></div></div></div></div>

<div class="box-ml"><div class="box-mr"><div class="box-content">

<p class="exp-intro">{'What this installation runs on, as the server that answered this page sees it, and what needs attention. Nothing here changes the site. To ask for help, download the report: passwords, keys, session ids and full server paths are left out.'|i18n( 'design/admin/setup/info' )}</p>

{* Who answered: the one thing to know before reading any other figure *}
<div class="exp-served{if eq( $si.server_kind, 'velocity' )} is-velocity{/if}" role="note">
    <div class="exp-served-main">
        <p>{'This page was answered by'|i18n( 'design/admin/setup/info' )} <strong>{$si.server|wash}{if and( eq( $si.server_kind, 'velocity' ), $si.facts.server.version )} {$si.facts.server.version|wash}{/if}</strong>{if $si.facts.server.workers.pool} &middot; {'PHP-FPM pool %pool'|i18n( 'design/admin/setup/info',, hash( '%pool', $si.facts.server.workers.pool|wash ) )}{/if}</p>
        {if $si.worker_model}<p>{$si.worker_model|wash}.</p>{/if}
        <p>{'Each server has its own PHP, OPcache and settings: Apache with PHP-FPM and Exponential Velocity show different figures. Open this page on the other server to see its own.'|i18n( 'design/admin/setup/info' )}</p>
    </div>
    <span class="exp-badge is-info">PHP {$si.facts.php.version|wash} &middot; {$si.facts.php.sapi|wash}</span>
</div>

<div class="exp-actionbar">
    <button type="button" class="exp-btn exp-btn-primary" id="exp-sysinfo-copy" hidden data-done="{'The report was copied to the clipboard.'|i18n( 'design/admin/setup/info' )|wash}" data-manual="{'Select the text of the report below and copy it.'|i18n( 'design/admin/setup/info' )|wash}">{'Copy report as text'|i18n( 'design/admin/setup/info' )}</button>
    <a class="exp-btn" href={'/setup/info/report'|ezurl}>{'Download report (.txt)'|i18n( 'design/admin/setup/info' )}</a>
    <a class="exp-btn" href={'/setup/info/json'|ezurl}>{'Download as JSON'|i18n( 'design/admin/setup/info' )}</a>
    {if $si.sizes}
        <a class="exp-btn" href={'/setup/info'|ezurl}>{'Without sizes (faster)'|i18n( 'design/admin/setup/info' )}</a>
    {else}
        <a class="exp-btn" href={'/setup/info/sizes'|ezurl}>{'Measure sizes'|i18n( 'design/admin/setup/info' )}</a>
    {/if}
    <span class="exp-status-text" id="exp-sysinfo-copy-status" role="status" aria-live="polite"></span>
    <p class="exp-meta">{'Generated %time.'|i18n( 'design/admin/setup/info',, hash( '%time', $si.generated|wash ) )}
    {if $si.sizes}{'Sizes were measured for this view, within three seconds; a size marked ≥ was cut short.'|i18n( 'design/admin/setup/info' )}{else}{'Sizes of var/, the cache directories and a database on a server are measured only on request, as that reads every file.'|i18n( 'design/admin/setup/info' )}{/if}</p>
</div>

{* Health checks *}
<section class="exp-section" aria-labelledby="exp-sysinfo-health">
    <div class="exp-section-head">
        <h2 class="exp-h2" id="exp-sysinfo-health">{'Health checks'|i18n( 'design/admin/setup/info' )}</h2>
        <p>{'What needs attention first, each with what to do. Checked for the server that answered.'|i18n( 'design/admin/setup/info' )}</p>
    </div>
    <ul class="exp-figures">
        <li class="exp-figure{if $si.summary.fail|gt( 0 )} is-bad{/if}"><strong>{$si.summary.fail|wash}</strong><span>{'failures'|i18n( 'design/admin/setup/info' )}</span></li>
        <li class="exp-figure{if $si.summary.warn|gt( 0 )} is-warn{/if}"><strong>{$si.summary.warn|wash}</strong><span>{'warnings'|i18n( 'design/admin/setup/info' )}</span></li>
        <li class="exp-figure is-info"><strong>{$si.summary.info|wash}</strong><span>{'notes'|i18n( 'design/admin/setup/info' )}</span></li>
        <li class="exp-figure is-ok"><strong>{$si.summary.ok|wash}</strong><span>{'in order'|i18n( 'design/admin/setup/info' )}</span></li>
    </ul>
    <ul class="exp-checks">
    {foreach $si.checks as $si_check}{if ne( $si_check.state, 'ok' )}
        <li class="exp-check {$si_state_class[$si_check.state]}">
            <span class="exp-badge {$si_state_class[$si_check.state]}">{$si_state_label[$si_check.state]|wash}</span>
            <div class="exp-check-text">
                <strong>{$si_check.title|wash}</strong>
                {if $si_check.detail}<p>{$si_check.detail|wash}</p>{/if}
                {if $si_check.fix}<p class="exp-fix">{$si_check.fix|wash}</p>{/if}
            </div>
        </li>
    {/if}{/foreach}
    </ul>
    {if $si.summary.ok|gt( 0 )}
    <details class="exp-ok-checks"{if and( $si.summary.fail|eq( 0 ), $si.summary.warn|eq( 0 ), $si.summary.info|eq( 0 ) )} open{/if}>
        <summary>{'%count checks in order'|i18n( 'design/admin/setup/info',, hash( '%count', $si.summary.ok ) )}</summary>
        <ul class="exp-checks">
        {foreach $si.checks as $si_check}{if eq( $si_check.state, 'ok' )}
            <li class="exp-check is-ok">
                <span class="exp-badge is-ok">{$si_state_label.ok|wash}</span>
                <div class="exp-check-text"><strong>{$si_check.title|wash}</strong>{if $si_check.detail}<p>{$si_check.detail|wash}</p>{/if}</div>
            </li>
        {/if}{/foreach}
        </ul>
    </details>
    {/if}
</section>

{* Overview *}
<section class="exp-section" aria-labelledby="exp-sysinfo-overview">
    <div class="exp-section-head">
        <h2 class="exp-h2" id="exp-sysinfo-overview">{'Overview'|i18n( 'design/admin/setup/info' )}</h2>
    </div>
    <ul class="exp-cards">
    {foreach $si.cards as $card}
        <li class="exp-card {$si_state_class[$card.state]}" id="exp-sysinfo-card-{$card.id|wash}">
            <div class="exp-card-head">
                <h3>{$card.title|wash}</h3>
                {if ne( $card.state, 'ok' )}<span class="exp-badge {$si_state_class[$card.state]}">{$si_state_label[$card.state]|wash}</span>{/if}
            </div>
            <dl class="exp-rows">
            {foreach $card.rows as $row}
                <dt>{$row.label|wash}</dt><dd>{$row.value|wash}</dd>
            {/foreach}
            </dl>
        </li>
    {/foreach}
    </ul>
</section>

{* Extensions *}
<details class="exp-panel" id="exp-sysinfo-extensions">
    <summary><h2 class="exp-h2">{'Active extensions'|i18n( 'design/admin/setup/info' )}</h2><span class="exp-muted">{$si.extensions|count} &middot; {'in load order, with the version their extension.xml, ezinfo.php or composer.json states'|i18n( 'design/admin/setup/info' )}</span></summary>
    <div class="exp-panel-body">
        <ul class="exp-ext-list">
        {foreach $si.extensions as $extension}
            <li><span>{$extension.extension|wash}</span><span>{if $extension.version}{$extension.version|wash}{else}&ndash;{/if}</span></li>
        {/foreach}
        </ul>
        <p class="exp-note">{'Their authors, licences and websites are on'|i18n( 'design/admin/setup/info' )} <a href={'/ezinfo/about'|ezurl}>{'About'|i18n( 'design/admin/setup/info' )}</a>.</p>
    </div>
</details>

{* PHP *}
<details class="exp-panel" id="exp-sysinfo-php">
    <summary><h2 class="exp-h2">{'PHP extensions and settings'|i18n( 'design/admin/setup/info' )}</h2><span class="exp-muted">{$php_loaded_extensions|count} {'extensions'|i18n( 'design/admin/setup/info' )}</span></summary>
    <div class="exp-panel-body">
        <ul class="exp-chips">
        {foreach $si.php_extensions as $loaded_extension}<li class="exp-chip">{$loaded_extension|wash}</li>{/foreach}
        </ul>
        <ul class="exp-inline">
            <li><code>file_uploads</code> {if $php_ini.file_uploads}{'on'|i18n( 'design/admin/setup/info' )}{else}{'off'|i18n( 'design/admin/setup/info' )}{/if}</li>
            <li><code>open_basedir</code> {if $php_ini.open_basedir}{'set'|i18n( 'design/admin/setup/info' )}{else}{'not set'|i18n( 'design/admin/setup/info' )}{/if}</li>
            <li><code>max_file_uploads</code> {$si.facts.php.max_file_uploads|wash}</li>
            <li><code>display_errors</code> {if $si.facts.php.display_errors}{'on'|i18n( 'design/admin/setup/info' )}{else}{'off'|i18n( 'design/admin/setup/info' )}{/if}</li>
            {if $si.facts.php.disabled_functions}<li><code>disable_functions</code> {$si.facts.php.disabled_functions|implode( ', ' )|wash}</li>{/if}
        </ul>
        {if $autoload_functions}
        <div>
            <h3>{'PHP autoload functions'|i18n( 'design/admin/setup/info' )}</h3>
            <ol class="exp-note">
            {foreach $autoload_functions as $key => $function}
                {if is_array( $function )}
                    {if $function[0]|is_object()}
                        {set $function = concat( $function[0]|get_class(), '::', $function[1] )}
                    {else}
                        {set $function=$function|implode( '::' )}
                    {/if}
                {/if}
                <li><code>{$function|wash}</code></li>
            {/foreach}
            </ol>
        </div>
        {/if}
        <p class="exp-note">{'The full PHP configuration of this server process, without the request and the environment (they carry the sign-in and the session cookie):'|i18n( 'design/admin/setup/info' )} <a href={'/setup/info/php'|ezurl} rel="nofollow">{'PHP details (phpinfo)'|i18n( 'design/admin/setup/info' )}</a></p>
    </div>
</details>

{* Site addresses *}
<details class="exp-panel" id="exp-sysinfo-site"{if $site_info.configured_placeholder} open{/if}>
    <summary><h2 class="exp-h2">{'Site addresses'|i18n( 'design/admin/setup/info' )}</h2><span class="exp-muted">{$site_info.siteaccess|wash}</span></summary>
    <div class="exp-panel-body">
        <dl class="exp-rows">
            <dt>{'Site'|i18n( 'design/admin/setup/info' )}</dt>
            <dd>{if $site_info.public_url}<a href="{$site_info.public_url|wash}">{$site_info.public_url|wash}</a> ({$site_info.public_siteaccess|wash}){else}&mdash;{/if}</dd>
            {if and( $site_info.url, ne( $site_info.siteaccess, $site_info.public_siteaccess ) )}
            <dt>{'This page is served from'|i18n( 'design/admin/setup/info' )}</dt>
            <dd><a href="{$site_info.url|wash}">{$site_info.url|wash}</a> ({$site_info.siteaccess|wash})</dd>
            {/if}
            <dt>{'Site URL setting'|i18n( 'design/admin/setup/info' )}</dt>
            <dd>{if $site_info.configured}<code>{$site_info.configured|wash}</code>{else}<em>{'(empty)'|i18n( 'design/admin/setup/info' )}</em>{/if} <span class="exp-muted">site.ini [SiteSettings] SiteURL, {$site_info.siteaccess|wash}</span></dd>
        </dl>
        {if $site_info.configured_placeholder}
        <div class="exp-feedback is-warn"><p><strong>{'This is not an address visitors can reach.'|i18n( 'design/admin/setup/info' )}</strong>
        {'Mails, feeds and links made outside a request (cronjobs, notifications) use this setting. Set it in settings/siteaccess/%siteaccess/site.ini.append.php.'|i18n( 'design/admin/setup/info',, hash( '%siteaccess', $site_info.siteaccess|wash ) )}</p></div>
        {/if}
    </div>
</details>

{* The opcode cache and APCu, for whatever serves the page *}
<details class="exp-panel" id="php-caches">
    <summary><h2 class="exp-h2">{'OPcache and APCu'|i18n( 'design/admin/setup/info' )}</h2><span class="exp-muted">{'figures of the process that answered'|i18n( 'design/admin/setup/info' )}</span></summary>
    <div class="exp-panel-body">
    <div class="exp-two">
    {foreach hash( 'opcache', 'OPcache', 'apcu', 'APCu' ) as $key => $title}
    {def $cache=$php_caches[$key]}
    <div class="exp-box">
        <div class="exp-subhead">
            <h3>{$title|wash}</h3>
            <small>{if eq( $key, 'opcache' )}{'compiled PHP scripts'|i18n( 'design/admin/setup/info' )}{else}{'data in shared memory'|i18n( 'design/admin/setup/info' )}{/if}</small>
            {if $cache|not}
                <span class="exp-badge">{'not installed'|i18n( 'design/admin/setup/info' )}</span>
            {elseif $cache.enabled}
                <span class="exp-badge is-ok">{'enabled'|i18n( 'design/admin/setup/info' )}</span>{if $cache.version}<small>{$cache.version|wash}</small>{/if}
            {else}
                <span class="exp-badge is-warn">{'off'|i18n( 'design/admin/setup/info' )}</span>
            {/if}
        </div>
        {if and( $cache, $cache.enabled|not )}<p class="exp-note">{$cache.why_off|wash}</p>{/if}
        {if and( $cache, $cache.bars )}
        <dl class="exp-bars">
        {foreach $cache.bars as $bar}
            {* Green while there is room; amber from 75 %, red from 90 % -- for the hit rate the other way round *}
            {set $si_level = cond( eq( $bar.label, 'Hit rate' ),
                                   cond( ge( $bar.percent, 90 ), '', ge( $bar.percent, 60 ), 'is-warn', 'is-bad' ),
                                   cond( ge( $bar.percent, 90 ), 'is-bad', ge( $bar.percent, 75 ), 'is-warn', '' ) )}
            <dt>{$bar.label|i18n( 'design/admin/setup/info' )}</dt>
            <dd><div class="exp-bar {$si_level}" role="img" aria-label="{$bar.percent|wash} %"><span style="width: {$bar.percent|int}%"></span></div></dd>
            <dd class="exp-bar-text">{$bar.text|wash}</dd>
        {/foreach}
        </dl>
        {/if}
        {if and( $cache, $cache.figures )}
        <ul class="exp-inline">{foreach $cache.figures as $name => $value}<li>{$name|wash}: {$value|wash}</li>{/foreach}</ul>
        {/if}
        {if and( $cache, $cache.settings )}
        <ul class="exp-inline">{foreach $cache.settings as $name => $value}<li><code>{$name|wash}</code> {$value|wash}</li>{/foreach}</ul>
        {/if}
    </div>
    {undef $cache}
    {/foreach}
    </div>
    <p class="exp-note">{'These figures belong to the server process that answered this page; a command-line script, another php-fpm pool or another engine has caches of its own.'|i18n( 'design/admin/setup/info' )}
    {if $can_flush_caches}
        {'Both can be emptied on'|i18n( 'design/admin/setup/info' )} <a href="{'/setup/cache'|ezurl( 'no' )}#php-caches">{'Setup &gt; Caches'|i18n( 'design/admin/setup/info' )}</a>.
    {/if}</p>
    </div>
</details>

{* The Velocity engine serving this page, and what it answers itself *}
{if $velocity_info}
<details class="exp-panel" id="exp-sysinfo-velocity" open>
    <summary><h2 class="exp-h2">{$velocity_info.brand|wash} &mdash; {$velocity_info.engine_name|wash}</h2>{if $velocity_info.version}<span class="exp-muted">{$velocity_info.version|wash}</span>{/if}</summary>
    <div class="exp-panel-body">
        <dl class="exp-rows">
            <dt>{'Engine'|i18n( 'design/admin/setup/info' )}</dt>
            <dd>{$velocity_info.engine_name|wash} (<code>{$velocity_info.engine|wash}</code>) &mdash; {$velocity_info.role_text|wash}.
                {if $velocity_info.is_default}
                    {'It is the default engine ([ServerSettings] Engine), which exp:velocity start uses without --engine.'|i18n( 'design/admin/setup/info' )}
                {else}
                    {'The default engine is %default; this one runs with %command.'|i18n( 'design/admin/setup/info',, hash( '%default', $velocity_info.default|wash, '%command', concat( '<code>exp:velocity start --engine=', $velocity_info.engine|wash, '</code>' ) ) )}
                {/if}
                {if $velocity_info.velocity|not}<br /><span class="exp-muted">{'This server was not started by exp:velocity (it answers on another port than velocity.ini gives this engine), so the status below is limited to what the request itself shows.'|i18n( 'design/admin/setup/info' )}</span>{/if}
            </dd>
            <dt>{'Address'|i18n( 'design/admin/setup/info' )}</dt>
            <dd><a href="{$velocity_info.url|wash}">{$velocity_info.url|wash}</a> &mdash; {'reachable from %reach'|i18n( 'design/admin/setup/info',, hash( '%reach', $velocity_info.reach|wash ) )}</dd>
            {if $velocity_info.pid}
            <dt>{'Process'|i18n( 'design/admin/setup/info' )}</dt>
            <dd>{'pid %pid, %processes process(es)'|i18n( 'design/admin/setup/info',, hash( '%pid', $velocity_info.pid|wash, '%processes', $velocity_info.processes|wash ) )}</dd>
            {/if}
            {if $velocity_info.log}<dt>{'Console log'|i18n( 'design/admin/setup/info' )}</dt><dd><code>{$velocity_info.log|wash}</code></dd>{/if}
            {if $velocity_info.config}<dt>{'Configuration'|i18n( 'design/admin/setup/info' )}</dt><dd><code>{$velocity_info.config|wash}</code></dd>{/if}
        </dl>
        {* What the server answers itself, with who may open it. Tokens are never put into these links. *}
        <div>
            <h3>{'Views of the server'|i18n( 'design/admin/setup/info' )}</h3>
            <div class="exp-table-wrap">
            <table class="exp-table exp-table-views">
            <thead><tr>
                <th scope="col">{'View'|i18n( 'design/admin/setup/info' )}</th>
                <th scope="col">{'Type'|i18n( 'design/admin/setup/info' )}</th>
                <th scope="col">{'What it is'|i18n( 'design/admin/setup/info' )}</th>
                <th scope="col">{'Who may open it'|i18n( 'design/admin/setup/info' )}</th>
            </tr></thead>
            <tbody>
            {foreach $velocity_info.views as $view}
            <tr>
                <td>{if $view.url}<a href="{$view.url|wash}" target="_blank" rel="noopener">{$view.path|wash}</a>{else}<code>{$view.path|wash}</code>{/if}</td>
                <td>{$view.type|wash}</td>
                <td>{$view.description|wash}</td>
                <td>{$view.access|wash}</td>
            </tr>
            {/foreach}
            </tbody>
            </table>
            </div>
        </div>
        {if $velocity_info.notes}
        <ul class="exp-note">{foreach $velocity_info.notes as $note}<li>{$note|wash}</li>{/foreach}</ul>
        {/if}
        {if $velocity_info.others}
        <div>
            <h3>{'Other engines running'|i18n( 'design/admin/setup/info' )}</h3>
            <ul class="exp-note">
            {foreach $velocity_info.others as $other}
                <li>{$other.name|wash} (<code>{$other.engine|wash}</code>, {$other.role|wash})
                {foreach $other.urls as $url}<a href="{$url|wash}">{$url|wash}</a>{delimiter} {'and'|i18n( 'design/admin/setup/info' )} {/delimiter}{/foreach}
                &mdash; {'stop it with %command'|i18n( 'design/admin/setup/info',, hash( '%command', concat( '<code>exp:velocity stop --engine=', $other.engine|wash, '</code>' ) ) )}</li>
            {/foreach}
            </ul>
            <p class="exp-note">{'Started from this installation as well. A site is served by one engine; another one running is usually left from a test or a benchmark.'|i18n( 'design/admin/setup/info' )}</p>
        </div>
        {/if}
    </div>
</details>
{/if}

{if and( $velocity_info, eq( $velocity_info.engine, 'qbix' ) )}
{* Where the running engine came from: when a kernel edit appears to do nothing, this is the first thing to read *}
<details class="exp-panel" id="exp-sysinfo-engine">
    <summary><h2 class="exp-h2">{'Phar App Engine'|i18n( 'design/admin/setup/info' )}</h2><span class="exp-muted">{if eq( $engine_info.source, 'archive' )}{'The archive'|i18n( 'design/admin/setup/info' )}{else}{'Individual files on disk'|i18n( 'design/admin/setup/info' )}{/if}</span></summary>
    <div class="exp-panel-body">
        <dl class="exp-rows">
            <dt>{'Loaded from'|i18n( 'design/admin/setup/info' )}</dt>
            <dd>{if eq( $engine_info.source, 'archive' )}
                    {'The archive'|i18n( 'design/admin/setup/info' )} &mdash; <code>{$engine_info.archive|wash}</code>
                {else}
                    {'Individual files on disk &mdash; the archive below is not being used, because the EXP_ENGINE_PHAR environment variable is not set'|i18n( 'design/admin/setup/info' )}
                {/if}
                <br /><span class="exp-muted">{'Served by %server.'|i18n( 'design/admin/setup/info',, hash( '%server', $engine_info.server|wash ) )}
                {if eq( $engine_info.source, 'archive' )}
                    {'To run from the files on disk again: %command.'|i18n( 'design/admin/setup/info',, hash( '%command', $engine_info.switch_off|wash ) )}
                {else}
                    {'To run from the archive: %command.'|i18n( 'design/admin/setup/info',, hash( '%command', $engine_info.switch_on|wash ) )}
                {/if}</span></dd>
            <dt>{'Installation root'|i18n( 'design/admin/setup/info' )}</dt><dd><code>{$engine_info.root|wash}</code></dd>
            {if eq( $engine_info.source, 'archive' )}
            <dt>{'Archive'|i18n( 'design/admin/setup/info' )}</dt>
            <dd>{if $engine_info.archive_files|gt( 0 )}{'%files files, %bytes bytes, built %built'|i18n( 'design/admin/setup/info',, hash( '%files', $engine_info.archive_files, '%bytes', $engine_info.archive_bytes, '%built', $engine_info.archive_built|wash ) )}{else}{'%size, built %built'|i18n( 'design/admin/setup/info',, hash( '%size', $engine_info.archive_bytes|si( byte ), '%built', $engine_info.archive_built|wash ) )}{/if}</dd>
            {else}
            <dt>{'Archive on disk'|i18n( 'design/admin/setup/info' )}</dt><dd><code>{$engine_info.archive|wash}</code></dd>
            {/if}
            <dt>{'Archive was built from'|i18n( 'design/admin/setup/info' )}</dt><dd>{if $engine_info.version}{$engine_info.version|wash}{else}&ndash;{/if}</dd>
            <dt>{'Archive is current'|i18n( 'design/admin/setup/info' )}</dt>
            <dd>{$engine_info.matches_repo|wash}
                {if $engine_info.stale_reason}
                    <br /><span class="exp-muted">{'Why'|i18n( 'design/admin/setup/info' )}: {$engine_info.stale_reason|wash}.</span>
                    <br />{'To fix'|i18n( 'design/admin/setup/info' )}: <code>{$engine_info.stale_fix|wash}</code>
                    <br /><span class="exp-muted">{if ne( $engine_info.source, 'archive' )}{'Nothing is running from the archive at the moment, so this is not affecting the site.'|i18n( 'design/admin/setup/info' )}{else}{'The site is running from this archive, so what is on disk is not what is being served.'|i18n( 'design/admin/setup/info' )}{/if}</span>
                {/if}</dd>
            <dt>{'Phar stream wrapper'|i18n( 'design/admin/setup/info' )}</dt>
            <dd>{$engine_info.phar_wrapper|wash}{if eq( $engine_info.phar_wrapper, 'unregistered' )}<br /><span class="exp-muted">{'Deliberate: with it registered, a file that is both a valid image and a valid archive can be executed through a phar:// path.'|i18n( 'design/admin/setup/info' )}</span>{/if}</dd>
            <dt>{'Writing archives (phar.readonly)'|i18n( 'design/admin/setup/info' )}</dt><dd>{$engine_info.phar_readonly|wash}</dd>
        </dl>
    </div>
</details>
{/if}

{if $http_cache}
<details class="exp-panel" id="http-cache"{if $http_cache.message} open{/if}>
    <summary><h2 class="exp-h2">{'HTTP cache (role-aware)'|i18n( 'design/admin/setup/info' )}</h2>
        {if $http_cache.enabled|not}<span class="exp-badge">{'disabled'|i18n( 'design/admin/setup/info' )}</span>
        {elseif $http_cache.started|not}<span class="exp-badge is-warn">{'enabled, nothing stored yet'|i18n( 'design/admin/setup/info' )}</span>
        {else}<span class="exp-badge is-ok">{'enabled'|i18n( 'design/admin/setup/info' )}</span>{/if}
        <span class="exp-muted">{'Whole pages, per permission context'|i18n( 'design/admin/setup/info' )}</span></summary>
    <div class="exp-panel-body">
        {if $http_cache.enabled|not}
            <p class="exp-note">{'Switch it on with Enabled=enabled in settings/httpcache.ini (an override); every page is rendered until then.'|i18n( 'design/admin/setup/info' )}</p>
        {elseif $http_cache.started|not}
            <p class="exp-note">{'The first page requested on a cached siteaccess starts it.'|i18n( 'design/admin/setup/info' )}</p>
        {/if}
        {if $http_cache.message}<div class="exp-feedback {if and( $cache_action, $cache_action.ok|not )}is-warn{else}is-ok{/if}" role="status"><p>{$http_cache.message|wash}</p></div>{/if}
        {if $http_cache.started}
            {if $http_cache.bars}
            <dl class="exp-bars">
            {foreach $http_cache.bars as $bar}
                {set $si_level = cond( ge( $bar.percent, 90 ), '', ge( $bar.percent, 60 ), 'is-warn', 'is-bad' )}
                <dt>{$bar.label|i18n( 'design/admin/setup/info' )}</dt>
                <dd><div class="exp-bar {$si_level}" role="img" aria-label="{$bar.percent|wash} %"><span style="width: {$bar.percent|int}%"></span></div></dd>
                <dd class="exp-bar-text">{$bar.text|wash}</dd>
            {/foreach}
            </dl>
            {elseif $http_cache.stats|not}
            <p class="exp-note">{'No server has counted yet: hit counts need APCu in the PHP that serves the site.'|i18n( 'design/admin/setup/info' )}</p>
            {/if}
            <ul class="exp-inline">{foreach $http_cache.figures as $name => $value}<li>{$name|wash}: <strong>{$value|wash}</strong></li>{/foreach}</ul>
            {if $http_cache.reasons}
            <div>
                <p class="exp-note">{'Why requests were not served from the cache'|i18n( 'design/admin/setup/info' )}:</p>
                <dl class="exp-bars">
                {foreach $http_cache.reasons as $r}
                    <dt><code>{$r.reason|wash}</code></dt>
                    <dd><div class="exp-bar is-neutral" role="img" aria-label="{$r.percent|wash} %"><span style="width: {$r.percent|int}%"></span></div></dd>
                    <dd class="exp-bar-text">{$r.count|wash}</dd>
                {/foreach}
                </dl>
            </div>
            {/if}
            <ul class="exp-inline">{foreach $http_cache.settings as $name => $value}<li><code>{$name|wash}</code> {$value|wash}</li>{/foreach}<li><code>{$http_cache.dir|wash}</code></li></ul>
            {if $can_flush_caches}
            <form method="post" action={'/setup/info'|ezurl} class="exp-sysinfo-form">
                <button type="submit" class="exp-btn exp-btn-small" name="HttpCacheAction" value="purge" onclick="return confirm( '{'Purge every cached page?'|i18n( 'design/admin/setup/info' )|wash( javascript )}' );">{'Purge all pages'|i18n( 'design/admin/setup/info' )}</button>
                <button type="submit" class="exp-btn exp-btn-small" name="HttpCacheAction" value="gc">{'Remove dead entries'|i18n( 'design/admin/setup/info' )}</button>
                {if $http_cache.stats}<button type="submit" class="exp-btn exp-btn-small" name="HttpCacheAction" value="reset">{'Reset counters'|i18n( 'design/admin/setup/info' )}</button>{/if}
            </form>
            {/if}
        {/if}
    </div>
</details>
{/if}

{if $sql_profile}
<details class="exp-panel" id="database-queries"{if $sql_profile.message} open{/if}>
    <summary><h2 class="exp-h2">{'Database queries'|i18n( 'design/admin/setup/info' )}</h2>
        {if $sql_profile.mongo}<span class="exp-badge">{'MongoDB: not an SQL engine'|i18n( 'design/admin/setup/info' )}</span>
        {elseif $sql_profile.on}<span class="exp-badge is-ok">{'profile on'|i18n( 'design/admin/setup/info' )}</span>
        {else}<span class="exp-badge">{'profile off'|i18n( 'design/admin/setup/info' )}</span>{/if}
        <span class="exp-muted"><code>{$sql_profile.engine|wash}</code></span></summary>
    <div class="exp-panel-body">
        {if $sql_profile.mongo}<p class="exp-note">{'The query cache is for the SQL engines. The MongoDB driver keeps its own statement profile (var/tmp/mongo_profile.on).'|i18n( 'design/admin/setup/info' )}</p>{/if}
        {if $sql_profile.message}<div class="exp-feedback {if and( $cache_action, $cache_action.ok|not )}is-warn{else}is-ok{/if}" role="status"><p>{$sql_profile.message|wash}</p></div>{/if}

        {if and( $sql_profile.mongo|not, $query_cache )}
        <div class="exp-box">
            <div class="exp-subhead"><h3>{'Query cache'|i18n( 'design/admin/setup/info' )}</h3>
                {if $query_cache.enabled}<span class="exp-badge is-ok">{$query_cache.mode|wash}</span>{else}<span class="exp-badge">{'off'|i18n( 'design/admin/setup/info' )}</span>{/if}
                <small>{'settings/querycache.ini'|i18n( 'design/admin/setup/info' )}: MaxAge={$query_cache.max_age|wash}&nbsp;s, MaxRows={$query_cache.max_rows|wash}{if $query_cache.exclude}, ExcludeTables={$query_cache.exclude|implode( ', ' )|wash}{/if}</small></div>
            <p class="exp-note">
                {if $query_cache.mode|eq( 'shared' )}
                    {if $query_cache.apcu}
                        {'%entries results held in APCu (%size) by this server'|i18n( 'design/admin/setup/info',, hash( '%entries', concat( '<strong>', $query_cache.entries|wash, '</strong>' ), '%size', concat( $query_cache.memory_kb|wash, '&nbsp;KB' ) ) )}
                    {else}
                        {'APCu is not available to this server: "shared" works as "request" here.'|i18n( 'design/admin/setup/info' )}
                    {/if}
                    &middot;
                {/if}
                {'generation %generation'|i18n( 'design/admin/setup/info',, hash( '%generation', concat( '<strong>', $query_cache.generation|wash, '</strong>' ) ) )}{if $query_cache.cleared}, {'last cleared %date'|i18n( 'design/admin/setup/info',, hash( '%date', $query_cache.cleared|l10n( shortdatetime ) ) )}{/if}
                &middot; {'%tables tables written since'|i18n( 'design/admin/setup/info',, hash( '%tables', concat( '<strong>', $query_cache.tables_tracked|wash, '</strong>' ) ) )}
                {if $query_cache.state_exists|not}({'no state file yet'|i18n( 'design/admin/setup/info' )}){/if}
            </p>
            {if $query_cache.counters}
            <p class="exp-note">
                {'This server since %date: %requests requests, %hits hits, %misses misses'|i18n( 'design/admin/setup/info',, hash( '%date', cond( $query_cache.counters.since, $query_cache.counters.since|l10n( shortdatetime ), '-' ), '%requests', concat( '<strong>', $query_cache.counters.requests|wash, '</strong>' ), '%hits', concat( '<strong>', $query_cache.counters.hits|wash, '</strong>' ), '%misses', concat( '<strong>', $query_cache.counters.misses|wash, '</strong>' ) ) )}{if $query_cache.counters.hit_rate|ne( '' )} ({'%rate hit rate'|i18n( 'design/admin/setup/info',, hash( '%rate', concat( '<strong>', $query_cache.counters.hit_rate|wash, '&nbsp;%</strong>' ) ) )}){/if},
                {'%uncacheable not cacheable, %writes writes'|i18n( 'design/admin/setup/info',, hash( '%uncacheable', $query_cache.counters.uncacheable|wash, '%writes', $query_cache.counters.writes|wash ) )}
            </p>
            {/if}
            {if $query_cache.recent_writes}
            <ul class="exp-inline"><li>{'Last written'|i18n( 'design/admin/setup/info' )}:</li>{foreach $query_cache.recent_writes as $w}<li><code>{$w.table|wash}</code> {$w.ago|wash}&nbsp;s</li>{/foreach}</ul>
            {/if}
            {if $can_flush_caches}
            <form method="post" action={'/setup/info'|ezurl} class="exp-sysinfo-form" style="margin-top: 10px;">
                <button type="submit" class="exp-btn exp-btn-small" name="QueryCacheAction" value="clear">{'Clear the query cache'|i18n( 'design/admin/setup/info' )}</button>
                {if $query_cache.counters}<button type="submit" class="exp-btn exp-btn-small" name="QueryCacheAction" value="reset">{'Reset the counters'|i18n( 'design/admin/setup/info' )}</button>{/if}
            </form>
            {/if}
        </div>
        {/if}

        {if $sql_profile.mongo|not}
            {if $sql_profile.summary}
            <p class="exp-note">
                {'Over the last %n profiled requests: %statements statements, %repeats exact repeats (%repeat_pct), %db_ms in the database'|i18n( 'design/admin/setup/info',, hash( '%n', $sql_profile.summary.requests|wash, '%statements', concat( '<strong>', $sql_profile.summary.statements|wash, '</strong>' ), '%repeats', concat( '<strong>', $sql_profile.summary.repeats|wash, '</strong>' ), '%repeat_pct', concat( $sql_profile.summary.repeat_pct|wash, '&nbsp;%' ), '%db_ms', concat( '<strong>', $sql_profile.summary.db_ms|wash, '&nbsp;ms</strong>' ) ) )}
                &middot; {'a per-request memo would save %memo_ms, a shared query cache about %shared_ms'|i18n( 'design/admin/setup/info',, hash( '%memo_ms', concat( '<strong>', $sql_profile.summary.memo_ms|wash, '&nbsp;ms</strong>' ), '%shared_ms', concat( '<strong>', $sql_profile.summary.shared_ms|wash, '&nbsp;ms</strong>' ) ) )}
            </p>
            <div class="exp-table-wrap">
            <table class="exp-table">
            <thead><tr>
                <th scope="col">{'Time'|i18n( 'design/admin/setup/info' )}</th>
                <th scope="col">{'Request'|i18n( 'design/admin/setup/info' )}</th>
                <th scope="col" class="exp-num">{'Statements'|i18n( 'design/admin/setup/info' )}</th>
                <th scope="col" class="exp-num">{'Distinct'|i18n( 'design/admin/setup/info' )}</th>
                <th scope="col" class="exp-num">{'Repeats'|i18n( 'design/admin/setup/info' )}</th>
                <th scope="col" class="exp-num">{'In the database'|i18n( 'design/admin/setup/info' )}</th>
                <th scope="col" class="exp-num">{'Memo saves'|i18n( 'design/admin/setup/info' )}</th>
                <th scope="col" class="exp-num">{'Shared cache saves'|i18n( 'design/admin/setup/info' )}</th>
            </tr></thead>
            <tbody>
            {foreach $sql_profile.rows as $row}
            <tr>
                <td>{$row.time|wash}</td>
                <td><code>{$row.uri|wash}</code></td>
                <td class="exp-num">{$row.statements|wash}</td>
                <td class="exp-num">{$row.distinct|wash}</td>
                <td class="exp-num">{$row.repeats|wash}</td>
                <td class="exp-num">{$row.db_ms|wash}&nbsp;ms</td>
                <td class="exp-num">{$row.memo_ms|wash}&nbsp;ms</td>
                <td class="exp-num">{$row.shared_ms|wash}&nbsp;ms</td>
            </tr>
            {/foreach}
            </tbody>
            </table>
            </div>
            {elseif $sql_profile.on}
            <p class="exp-note">{'On, and nothing profiled yet: open a few pages.'|i18n( 'design/admin/setup/info' )}</p>
            {else}
            <p class="exp-note">{'Switch the profile on to see how many statements each request runs, how many are exact repeats, and what a query cache would save.'|i18n( 'design/admin/setup/info' )}</p>
            {/if}
            <p class="exp-note">{'The profile counts the statements that reached the database: with the query cache on, a cached answer is not in it. "Memo saves" is what the request mode would save, "shared cache saves" what the shared mode would (about 15 µs per answer). Counters are per server, since each server has its own APCu. The log is var/tmp/sql_profile.log; see doc/bc/6.0/sql-query-cache.md.'|i18n( 'design/admin/setup/info' )}</p>
            {if $can_flush_caches}
            <form method="post" action={'/setup/info'|ezurl} class="exp-sysinfo-form">
                {if $sql_profile.on}
                    <button type="submit" class="exp-btn exp-btn-small" name="SQLProfileAction" value="off">{'Switch the SQL profile off'|i18n( 'design/admin/setup/info' )}</button>
                {else}
                    <button type="submit" class="exp-btn exp-btn-small" name="SQLProfileAction" value="on">{'Switch the SQL profile on'|i18n( 'design/admin/setup/info' )}</button>
                {/if}
            </form>
            {/if}
        {/if}
    </div>
</details>
{/if}

{if $response_cache}
<details class="exp-panel" id="exp-sysinfo-response-cache">
    <summary><h2 class="exp-h2">{'Response cache (web server)'|i18n( 'design/admin/setup/info' )}</h2>
        {if $response_cache.enabled}<span class="exp-badge is-ok">{'enabled'|i18n( 'design/admin/setup/info' )}</span>{else}<span class="exp-badge">{'disabled'|i18n( 'design/admin/setup/info' )}</span>{/if}</summary>
    <div class="exp-panel-body">
        <dl class="exp-rows">
            <dt>{'Status'|i18n( 'design/admin/setup/info' )}</dt>
            <dd>{if $response_cache.enabled}
                    {if $response_cache.default_ttl|gt( 0 )}
                        {'pages without a lifetime of their own are kept for %seconds seconds'|i18n( 'design/admin/setup/info', '', hash( '%seconds', $response_cache.default_ttl|wash ) )}
                    {else}
                        {'only responses that bring their own max-age are kept; Exponential sends no-cache, so its pages are not'|i18n( 'design/admin/setup/info' )}
                    {/if}
                {else}
                    {'disabled &mdash; every request is rendered'|i18n( 'design/admin/setup/info' )}
                {/if}</dd>
            {if $response_cache.enabled}
            <dt>{'Hits'|i18n( 'design/admin/setup/info' )}</dt>
            <dd>{if $response_cache.stats_available}{'%hits hits, %misses misses, %rate since the server started'|i18n( 'design/admin/setup/info',, hash( '%hits', $response_cache.hits|wash, '%misses', $response_cache.misses|wash, '%rate', concat( $response_cache.hit_rate|wash, '&nbsp;%' ) ) )}{else}{'not available'|i18n( 'design/admin/setup/info' )}{/if}</dd>
            <dt>{'Shared memory (APCu)'|i18n( 'design/admin/setup/info' )}</dt>
            <dd>{if $response_cache.apcu_usable|not}
                    {if $response_cache.apcu_configured}{'configured, but APCu is not enabled for this PHP process (apc.enable_cli) -- entries are kept on disk only'|i18n( 'design/admin/setup/info' )}{else}{'not used'|i18n( 'design/admin/setup/info' )}{/if}
                {elseif $response_cache.apcu_configured|not}
                    {'available, but switched off for the response cache'|i18n( 'design/admin/setup/info' )}
                {else}
                    {'%pages pages, %size (entries up to %max_size; segment %segment, %free free)'|i18n( 'design/admin/setup/info',, hash( '%pages', $response_cache.apcu_entries|wash, '%size', $response_cache.apcu_bytes|si( byte ), '%max_size', $response_cache.apcu_max_size|si( byte ), '%segment', $response_cache.apcu_segment|si( byte ), '%free', $response_cache.apcu_free|si( byte ) ) )}
                {/if}</dd>
            <dt>{'On disk'|i18n( 'design/admin/setup/info' )}</dt>
            <dd>{if $response_cache.file_readable|not}
                    {'the directory does not exist yet or cannot be read'|i18n( 'design/admin/setup/info' )}
                {elseif $response_cache.file_counted_all|not}
                    {'at least %files files, %size'|i18n( 'design/admin/setup/info',, hash( '%files', $response_cache.file_entries|wash, '%size', $response_cache.file_bytes|si( byte ) ) )}
                {else}
                    {'%files files, %size'|i18n( 'design/admin/setup/info',, hash( '%files', $response_cache.file_entries|wash, '%size', $response_cache.file_bytes|si( byte ) ) )}
                {/if}
                <br /><code>{$response_cache.dir|wash}</code>{if $response_cache.dir_mode} &mdash; {'directories %mode'|i18n( 'design/admin/setup/info',, hash( '%mode', $response_cache.dir_mode|wash ) )}{/if}{if $response_cache.file_mode}, {'files %mode'|i18n( 'design/admin/setup/info',, hash( '%mode', $response_cache.file_mode|wash ) )}{/if}</dd>
            <dt>{'Not cached for visitors carrying'|i18n( 'design/admin/setup/info' )}</dt>
            <dd>{if $response_cache.skip_cookies}
                    {foreach $response_cache.skip_cookies as $cookie}<code>{$cookie|wash}</code>{delimiter}, {/delimiter}{/foreach} <span class="exp-muted">({'matched as a prefix'|i18n( 'design/admin/setup/info' )})</span>
                {else}
                    <strong>{'no cookie -- signed-in visitors would be served from the cache'|i18n( 'design/admin/setup/info' )}</strong>
                {/if}</dd>
            <dt>{'Further settings'|i18n( 'design/admin/setup/info' )}</dt>
            <dd>{'stale pages served while one request renders'|i18n( 'design/admin/setup/info' )}: {if $response_cache.stale_while_revalidate|gt( 0 )}{$response_cache.stale_while_revalidate|wash}&nbsp;s{else}{'no'|i18n( 'design/admin/setup/info' )}{/if};
                {'not-found pages remembered'|i18n( 'design/admin/setup/info' )}: {if $response_cache.negative_ttl|gt( 0 )}{$response_cache.negative_ttl|wash}&nbsp;s{else}{'no'|i18n( 'design/admin/setup/info' )}{/if};
                {'HTML minified'|i18n( 'design/admin/setup/info' )}: {if $response_cache.minify_html}{'yes'|i18n( 'design/admin/setup/info' )}{else}{'no'|i18n( 'design/admin/setup/info' )}{/if};
                {'expired files swept'|i18n( 'design/admin/setup/info' )}: {if $response_cache.sweep_every|gt( 0 )}{'every %seconds s'|i18n( 'design/admin/setup/info', '', hash( '%seconds', $response_cache.sweep_every|wash ) )}{if $response_cache.sweep_max_age|gt( 0 )}, {'nothing older than %seconds s'|i18n( 'design/admin/setup/info', '', hash( '%seconds', $response_cache.sweep_max_age|wash ) )}{/if}{else}{'no'|i18n( 'design/admin/setup/info' )}{/if}</dd>
            {/if}
        </dl>
    </div>
</details>
{/if}

{* The database connection: what the overview card leaves out *}
<details class="exp-panel" id="exp-sysinfo-database">
    <summary><h2 class="exp-h2">{'Database connection'|i18n( 'design/admin/setup/info' )}</h2><span class="exp-muted">{$database_info|wash}</span></summary>
    <div class="exp-panel-body">
        <dl class="exp-rows">
            <dt>{'Type'|i18n( 'design/admin/setup/info', 'Database type' )}</dt><dd>{$database_info|wash}</dd>
            {if $database_object.database_server}<dt>{'Server'|i18n( 'design/admin/setup/info', 'Database server' )}</dt><dd>{$database_object.database_server|wash}</dd>{/if}
            {if $database_object.database_socket_path}<dt>{'Socket path'|i18n( 'design/admin/setup/info', 'Database socket path' )}</dt><dd><code>{$database_object.database_socket_path|wash}</code></dd>{/if}
            <dt>{'Database name'|i18n( 'design/admin/setup/info', 'Database name' )}</dt><dd>{$database_object.database_name|wash}</dd>
            <dt>{'Character set'|i18n( 'design/admin/setup/info', 'Database charset' )}</dt><dd>{$database_charset|wash}{if $database_object.is_internal_charset} ({'Internal'|i18n( 'design/admin/setup/info' )}){/if}</dd>
            {if $database_object.database_server}<dt>{'Connection retry count'|i18n( 'design/admin/setup/info', 'Database retry count' )}</dt><dd>{$database_object.retry_count|wash}</dd>{/if}
            <dt>{'Read replica'|i18n( 'design/admin/setup/info' )}</dt>
            <dd>{if $database_object.use_slave_server}{$database_object.slave_database_server|wash} / {$database_object.slave_database_name|wash}{else}{'There is no slave database in use.'|i18n( 'design/admin/setup/info' )}{/if}</dd>
        </dl>
    </div>
</details>

{* The report for support *}
<details class="exp-panel" id="exp-sysinfo-report">
    <summary><h2 class="exp-h2">{'Report for support'|i18n( 'design/admin/setup/info' )}</h2><span class="exp-muted">{'the text the download and the Copy button give'|i18n( 'design/admin/setup/info' )}</span></summary>
    <div class="exp-panel-body">
        <p class="exp-note">{'Passwords, keys, tokens, session ids, credentials in addresses and the full paths of the server are left out; paths inside the installation are relative to it.'|i18n( 'design/admin/setup/info' )}</p>
        <label class="exp-sr" for="exp-sysinfo-report-text">{'Report for support'|i18n( 'design/admin/setup/info' )}</label>
        <textarea class="exp-report" id="exp-sysinfo-report-text" readonly rows="18" spellcheck="false">{$si.text|wash}</textarea>
    </div>
</details>

</div></div></div>
</div>

{literal}
<script>
(function () {
    var button = document.getElementById('exp-sysinfo-copy');
    var text = document.getElementById('exp-sysinfo-report-text');
    var status = document.getElementById('exp-sysinfo-copy-status');
    if (!button || !text) return;
    button.hidden = false;
    button.addEventListener('click', function () {
        var done = function () { status.textContent = button.getAttribute('data-done'); };
        var fallback = function () {
            var details = document.getElementById('exp-sysinfo-report');
            if (details) details.open = true;
            text.focus(); text.select();
            try { if (document.execCommand('copy')) { done(); return; } } catch (e) {}
            status.textContent = button.getAttribute('data-manual');
        };
        if (navigator.clipboard && window.isSecureContext) {
            navigator.clipboard.writeText(text.value).then(done, fallback);
        } else {
            fallback();
        }
    });
})();
</script>
{/literal}
{undef $si $si_state_class $si_state_label $si_open_checks $si_level}
