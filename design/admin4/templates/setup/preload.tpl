{* Setup > Preload: warms the caches of a site by requesting its pages as a visitor would.

   What is running now, the form that starts a run or shows what it would do (the dry run), the live output of a
   run, which server's caches a run warms and how to run it from the shell, and the last runs with what each did.
   A run never happens inside a web request: setup/preloadjob (or this page's form) starts it in the background,
   and the page follows it.

   The same file is in design/admin and design/admin4, so every administration design resolves it. The styling is
   setup/preload_exp_style.tpl, scoped to .exp-preload. Everything works without javascript: the form posts Start,
   Dry run and Stop, and a reload shows how a run is doing. The script below follows a run as it goes.
   Guide: doc/guides/preloading-caches.md *}
{include uri='design:setup/preload_exp_style.tpl'}
{def $preload_states = hash(
        'finished', array( 'is-ok', 'Finished'|i18n( 'design/admin/setup/preload' ) ),
        'stopped', array( 'is-warn', 'Stopped'|i18n( 'design/admin/setup/preload' ) ),
        'failed', array( 'is-bad', 'Failed'|i18n( 'design/admin/setup/preload' ) ),
        'died', array( 'is-bad', 'Ended without finishing'|i18n( 'design/admin/setup/preload' ) ),
        'running', array( 'is-info', 'Running'|i18n( 'design/admin/setup/preload' ) ),
        'starting', array( 'is-info', 'Starting'|i18n( 'design/admin/setup/preload' ) ) )
     $preload_sources = hash(
        'page', 'Setup > Preload'|i18n( 'design/admin/setup/preload' ),
        'shell', 'Shell or cron'|i18n( 'design/admin/setup/preload' ) )
     $preload_min = 0
     $preload_sec = 0
     $preload_last = cond( $runs|count|gt( 0 ), $runs[0], false() )}
<div class="context-block exp-preload" id="exp-preload"
     data-job-url={$job_url|ezurl()}
     data-running-id="{if $running}{$running.id|wash}{/if}"
     data-max-pages="{if $running}{$running.max_pages|wash}{/if}">

<div class="box-header"><div class="box-tc"><div class="box-ml"><div class="box-mr"><div class="box-tl"><div class="box-tr">
<h1 class="context-title">{'Preload'|i18n( 'design/admin/setup/preload' )}</h1>
<div class="header-mainline"></div>
</div></div></div></div></div></div>

<div class="box-ml"><div class="box-mr"><div class="box-content">

<p class="exp-intro">{'Requests the pages of a site as a visitor would, so its caches are warm before the first visitor arrives: the page views, the image aliases and the compiled templates. It starts at the section pages and follows the links of the site, and lists the links that are broken with the pages that link to them.'|i18n( 'design/admin/setup/preload' )}</p>

{* What the last action said *}
{foreach $feedback as $preload_message}
{switch match=$preload_message.type}
{case match='started'}<div class="exp-feedback is-ok" role="status"><p>{'The preload of %siteaccess was started in the background. Its progress is shown below; you can leave this page and come back.'|i18n( 'design/admin/setup/preload',, hash( '%siteaccess', $preload_message.message|wash ) )}</p></div>{/case}
{case match='stopping'}<div class="exp-feedback is-ok" role="status"><p>{'The preload was asked to stop. It ends after the page it is requesting.'|i18n( 'design/admin/setup/preload' )}</p></div>{/case}
{case match='not_running'}<div class="exp-feedback is-warn" role="status"><p>{'That preload is not running any more.'|i18n( 'design/admin/setup/preload' )}</p></div>{/case}
{case match='busy'}<div class="exp-feedback is-warn" role="alert"><p>{'A preload is already running. Only one runs at a time: wait for it to end, or stop it first.'|i18n( 'design/admin/setup/preload' )}</p></div>{/case}
{case match='unknown_siteaccess'}<div class="exp-feedback is-bad" role="alert"><p>{'Choose one of the sites in the list.'|i18n( 'design/admin/setup/preload' )}</p></div>{/case}
{case}<div class="exp-feedback is-bad" role="alert"><p><strong>{'The preload could not be started.'|i18n( 'design/admin/setup/preload' )}</strong> {$preload_message.message|wash}</p></div>{/case}
{/switch}
{/foreach}

{if $targets|count|eq( 0 )}
<div class="exp-feedback is-bad" role="alert">
    <p><strong>{'There is no site to warm.'|i18n( 'design/admin/setup/preload' )}</strong>
    {'Add the siteaccesses of your sites to site.ini [SiteAccessSettings] RelatedSiteAccessList and give each a SiteURL.'|i18n( 'design/admin/setup/preload' )}</p>
</div>
{/if}

{* What is running now *}
<div class="exp-statusbar" id="exp-preload-status">
    <div class="exp-status">
    {if $running}
        <div class="exp-status-line">
            <span class="exp-pill is-running"><span class="exp-dot" aria-hidden="true"></span>{'Running'|i18n( 'design/admin/setup/preload' )}</span>
            <span class="exp-meta">{'%site, started %time from %source'|i18n( 'design/admin/setup/preload',, hash( '%site', cond( $running.siteaccess|ne( '' ), $running.siteaccess, $running.base_url )|wash, '%time', $running.started|l10n( shortdatetime ), '%source', cond( is_set( $preload_sources[$running.source] ), $preload_sources[$running.source], $running.source|wash ) ) )}</span>
        </div>
        <div class="exp-progress" aria-hidden="true"><span id="exp-preload-bar" style="width: {if $running.max_pages|gt( 0 )}{min( 100, $running.counts.fetched|mul( 100 )|div( $running.max_pages )|round )}{else}0{/if}%"></span></div>
        <p class="exp-meta" id="exp-preload-count" aria-live="polite">{'%pages of at most %max pages warmed, %broken broken'|i18n( 'design/admin/setup/preload',, hash( '%pages', $running.counts.fetched, '%max', $running.max_pages, '%broken', $running.counts.broken ) )}</p>
        <p class="exp-current" id="exp-preload-current">{$running.current|wash}</p>
    {else}
        <div class="exp-status-line">
            <span class="exp-pill"><span class="exp-dot" aria-hidden="true"></span>{'Idle'|i18n( 'design/admin/setup/preload' )}</span>
            {if $preload_last}
            <span class="exp-meta">{'Last run %time: %site'|i18n( 'design/admin/setup/preload',, hash( '%time', $preload_last.started|l10n( shortdatetime ), '%site', cond( $preload_last.siteaccess|ne( '' ), $preload_last.siteaccess, $preload_last.base_url )|wash ) )}
                <span class="exp-badge {$preload_states[$preload_last.state][0]}">{$preload_states[$preload_last.state][1]}</span></span>
            {else}
            <span class="exp-meta">{'No site has been preloaded yet.'|i18n( 'design/admin/setup/preload' )}</span>
            {/if}
        </div>
    {/if}
    </div>
    <div class="exp-actions">
    {if $running}
        <a class="exp-btn" href={'setup/preload'|ezurl()} id="exp-preload-refresh">{'Refresh'|i18n( 'design/admin/setup/preload' )}</a>
        {if $running.id|ne( '' )}
        <form method="post" action={'setup/preload'|ezurl()}>
            <input type="hidden" name="JobID" value="{$running.id|wash}" />
            <button class="exp-btn exp-btn-outline-danger" type="submit" name="Action" value="stop" id="exp-preload-stop">{'Stop'|i18n( 'design/admin/setup/preload' )}</button>
        </form>
        {/if}
    {/if}
    </div>
</div>

{* The live output of the run that is followed *}
<section class="exp-section" id="exp-preload-live"{if $running|not} hidden{/if} aria-labelledby="exp-preload-live-title">
    <div class="exp-section-head">
        <h2 class="exp-h2" id="exp-preload-live-title">{'Output'|i18n( 'design/admin/setup/preload' )}</h2>
        <span class="exp-meta" id="exp-preload-state" aria-live="polite"></span>
    </div>
    <ul class="exp-figures" id="exp-preload-figures" hidden></ul>
    <pre class="exp-console" id="exp-preload-console" tabindex="0" aria-label="{'Output of the preload'|i18n( 'design/admin/setup/preload' )|wash}"></pre>
    <div id="exp-preload-broken"></div>
</section>

{* The form *}
<form method="post" action={'setup/preload'|ezurl()} class="exp-card" id="exp-preload-form" aria-labelledby="exp-preload-form-title">
    <div class="exp-section-head">
        <h2 class="exp-h2" id="exp-preload-form-title">{'Start a preload'|i18n( 'design/admin/setup/preload' )}</h2>
        <p>{'Choose the site and how far to go. A dry run shows the address and the starting pages without requesting anything.'|i18n( 'design/admin/setup/preload' )}</p>
    </div>
    <div class="exp-fields">
        <div class="exp-field exp-field-site">
            <label for="preload-siteaccess">{'Site to warm'|i18n( 'design/admin/setup/preload' )}</label>
            <select id="preload-siteaccess" name="SiteAccess"{if $targets|count|eq( 0 )} disabled="disabled"{/if}>
            {foreach $targets as $preload_target}
                <option value="{$preload_target.name|wash}"{if eq( $preload_target.name, $selected_siteaccess )} selected="selected"{/if}>{$preload_target.name|wash} &mdash; {$preload_target.url|wash}</option>
            {/foreach}
            </select>
            <span class="exp-help">{'The siteaccesses of site.ini RelatedSiteAccessList, at the address their SiteURL and the siteaccess matching give them.'|i18n( 'design/admin/setup/preload' )}</span>
        </div>
        <div class="exp-field">
            <label for="preload-max-pages">{'Page limit'|i18n( 'design/admin/setup/preload' )}</label>
            <input id="preload-max-pages" name="MaxPages" type="number" min="1" max="{$max_pages_limit}" step="1" inputmode="numeric" value="{$form.max_pages|wash}" aria-describedby="preload-max-pages-help" />
            <span class="exp-help" id="preload-max-pages-help">{'The most pages one run requests, 1 to %max.'|i18n( 'design/admin/setup/preload',, hash( '%max', $max_pages_limit ) )}</span>
        </div>
        <div class="exp-field">
            <label for="preload-max-depth">{'Link depth'|i18n( 'design/admin/setup/preload' )}</label>
            <input id="preload-max-depth" name="MaxDepth" type="number" min="0" max="{$max_depth_limit}" step="1" inputmode="numeric" value="{$form.max_depth|wash}" aria-describedby="preload-max-depth-help" />
            <span class="exp-help" id="preload-max-depth-help">{'How many links from a starting page are followed; 0 warms the starting pages only.'|i18n( 'design/admin/setup/preload' )}</span>
        </div>
        <div class="exp-field exp-field-wide">
            <span class="exp-label">{'What to warm'|i18n( 'design/admin/setup/preload' )}</span>
            <label class="exp-check" for="preload-images">
                <input id="preload-images" type="checkbox" name="Images" value="1"{if $form.images} checked="checked"{/if} />
                <span><strong>{'Also check the images on each page'|i18n( 'design/admin/setup/preload' )}</strong>
                <span class="exp-help">{'The pages make their image aliases when they are rendered. This also requests every image they show, once, and lists the ones that are missing with the pages that show them.'|i18n( 'design/admin/setup/preload' )}</span></span>
            </label>
        </div>
    </div>
    <div class="exp-formbar">
        <button class="exp-btn exp-btn-primary" type="submit" name="Action" value="start" id="preload-start"{if or( $running, $targets|count|eq( 0 ) )} disabled="disabled"{/if}>{'Start preloading'|i18n( 'design/admin/setup/preload' )}</button>
        <button class="exp-btn" type="submit" name="Action" value="dryrun" id="preload-dryrun"{if $targets|count|eq( 0 )} disabled="disabled"{/if}>{'Dry run'|i18n( 'design/admin/setup/preload' )}</button>
        <p class="exp-meta" id="preload-start-note">{if $running}{'A preload is running; the next one can start when it has ended.'|i18n( 'design/admin/setup/preload' )}{else}{'The run goes on in the background. One preload runs at a time.'|i18n( 'design/admin/setup/preload' )}{/if}</p>
    </div>
</form>

{* The dry run *}
<section class="exp-card" id="exp-preload-plan"{if $plan|not} hidden{/if} aria-labelledby="exp-preload-plan-title">
    <div class="exp-section-head">
        <h2 class="exp-h2" id="exp-preload-plan-title">{'Dry run'|i18n( 'design/admin/setup/preload' )}</h2>
        <p>{'Nothing was requested. A run would start at these pages and follow their links to the same site, up to the limits.'|i18n( 'design/admin/setup/preload' )}</p>
    </div>
    <div id="exp-preload-plan-body">
    {if $plan}
        {if $plan.reached|not}
        <div class="exp-feedback is-warn"><p>{'The siteaccess matching in site.ini does not send this address to %siteaccess, so the pages warmed may be those of another siteaccess. Check SiteURL and the match settings of the siteaccess.'|i18n( 'design/admin/setup/preload',, hash( '%siteaccess', $plan.siteaccess|wash ) )}</p></div>
        {/if}
        <dl class="exp-facts">
            <div><dt>{'Siteaccess'|i18n( 'design/admin/setup/preload' )}</dt><dd>{$plan.siteaccess|wash}</dd></div>
            <div><dt>{'Address'|i18n( 'design/admin/setup/preload' )}</dt><dd><code>{$plan.base_url|wash}</code></dd></div>
            <div><dt>{'Prefix'|i18n( 'design/admin/setup/preload' )}</dt><dd>{if $plan.prefix|ne( '' )}<code>{$plan.prefix|wash}</code>{else}{'none, matched by host'|i18n( 'design/admin/setup/preload' )}{/if}</dd></div>
            <div><dt>{'Limits'|i18n( 'design/admin/setup/preload' )}</dt><dd>{'%pages pages, link depth %depth'|i18n( 'design/admin/setup/preload',, hash( '%pages', $plan.options.max_pages, '%depth', $plan.options.max_depth ) )}{if $plan.options.images}, {'images checked'|i18n( 'design/admin/setup/preload' )}{/if}</dd></div>
        </dl>
        <h3 style="margin-top: 14px;">{'Starting pages'|i18n( 'design/admin/setup/preload' )}</h3>
        <ul class="exp-urls">{foreach $plan.start_urls as $preload_url}<li>{$preload_url|wash}</li>{/foreach}</ul>
        <h3 style="margin-top: 14px;">{'The same from the shell'|i18n( 'design/admin/setup/preload' )}</h3>
        <pre class="exp-code">{$plan.command|wash} --dry-run</pre>
    {/if}
    </div>
</section>

<div class="exp-columns">
    <section class="exp-card" aria-labelledby="exp-preload-server-title">
        <h2 class="exp-h2" id="exp-preload-server-title">{'Which caches it warms'|i18n( 'design/admin/setup/preload' )}</h2>
        <p style="margin-top: 6px;">{'The run requests each page at the address of the site, so the server that answers that address renders it: here %address.'|i18n( 'design/admin/setup/preload',, hash( '%address', cond( is_set( $start_urls[0] ), $start_urls[0], $base_url )|wash ) )}</p>
        <ul class="exp-points">
            <li>{'The page view cache, the image aliases and the compiled templates are files of this installation. Apache with PHP-FPM and Exponential Velocity share them, so a run warms them for both, whichever server answers.'|i18n( 'design/admin/setup/preload' )}</li>
            {if $servers.velocity}
            <li>{'Velocity is running here (ports %port). Its own response cache keeps a page for %ttl seconds only, so warming it ahead of visitors does not last; the shared caches above are what a run is for.'|i18n( 'design/admin/setup/preload',, hash( '%port', $servers.velocity_port, '%ttl', cond( $servers.velocity_ttl|is_null, '?', $servers.velocity_ttl ) ) )}</li>
            {/if}
            <li>{'Pages for signed-in users are not warmed: the run is an anonymous visitor.'|i18n( 'design/admin/setup/preload' )}</li>
        </ul>
    </section>
    <section class="exp-card" aria-labelledby="exp-preload-shell-title">
        <h2 class="exp-h2" id="exp-preload-shell-title">{'From the shell or cron'|i18n( 'design/admin/setup/preload' )}</h2>
        <p style="margin-top: 6px;">{'The same run, from the installation directory. It waits for nobody: when a preload is already running it says so and ends. Its runs are listed below with the others.'|i18n( 'design/admin/setup/preload' )}</p>
        <pre class="exp-code" id="exp-preload-command">{$command_line|wash}</pre>
        <p style="margin-top: 8px;" class="exp-meta">{'For cron, after the nightly cache clear for example:'|i18n( 'design/admin/setup/preload' )}</p>
        <pre class="exp-code">30 4 * * * cd {$root_dir|wash} &amp;&amp; {$command_line|wash} -q</pre>
        <p style="margin-top: 8px;" class="exp-meta">{'Runs are kept in %dir (the last 20).'|i18n( 'design/admin/setup/preload',, hash( '%dir', concat( '<code>', $runs_directory|wash, '</code>' ) ) )}</p>
    </section>
</div>

{* The last runs *}
<section class="exp-section" id="exp-preload-runs" aria-labelledby="exp-preload-runs-title">
    <div class="exp-section-head">
        <h2 class="exp-h2" id="exp-preload-runs-title">{'Last runs'|i18n( 'design/admin/setup/preload' )}</h2>
        <p>{'From this page, the shell and cron, newest first. Image aliases made counts the alias files the pages created while the run requested them.'|i18n( 'design/admin/setup/preload' )}</p>
    </div>
    {if $runs|count|eq( 0 )}
    <p class="exp-empty">{'No runs yet. Start one above, or run the command from the shell.'|i18n( 'design/admin/setup/preload' )}</p>
    {else}
    <div class="exp-table-wrap">
    <table class="exp-table">
        <caption class="exp-sr">{'Last runs'|i18n( 'design/admin/setup/preload' )}</caption>
        <thead><tr>
            <th scope="col">{'Run'|i18n( 'design/admin/setup/preload' )}</th>
            <th scope="col">{'Result'|i18n( 'design/admin/setup/preload' )}</th>
            <th scope="col" class="exp-num">{'Duration'|i18n( 'design/admin/setup/preload' )}</th>
            <th scope="col" class="exp-num">{'Pages'|i18n( 'design/admin/setup/preload' )}</th>
            <th scope="col" class="exp-num">{'Image aliases made'|i18n( 'design/admin/setup/preload' )}</th>
            <th scope="col" class="exp-num">{'Failures'|i18n( 'design/admin/setup/preload' )}</th>
        </tr></thead>
        <tbody>
        {foreach $runs as $preload_run}
        {set $preload_min = cond( $preload_run.seconds|is_null, 0, $preload_run.seconds|div( 60 )|floor )
             $preload_sec = cond( $preload_run.seconds|is_null, 0, $preload_run.seconds|sub( $preload_min|mul( 60 ) )|round )}
        <tr>
            <td class="exp-run"><span class="exp-nowrap">{$preload_run.started|l10n( shortdatetime )}</span><br />{if $preload_run.siteaccess|ne( '' )}<strong>{$preload_run.siteaccess|wash}</strong> {/if}<span class="exp-meta exp-url-text">{$preload_run.base_url|wash}</span><br /><span class="exp-meta">{cond( is_set( $preload_sources[$preload_run.source] ), $preload_sources[$preload_run.source], $preload_run.source|wash )}</span></td>
            <td><span class="exp-badge {cond( is_set( $preload_states[$preload_run.state] ), $preload_states[$preload_run.state][0], '' )}">{cond( is_set( $preload_states[$preload_run.state] ), $preload_states[$preload_run.state][1], $preload_run.state|wash )}</span>
                {if $preload_run.message|ne( '' )}<br /><span class="exp-meta">{$preload_run.message|wash}</span>{/if}</td>
            <td class="exp-num">{if $preload_run.seconds|is_null}&mdash;{elseif $preload_min|gt( 0 )}{'%min min %sec s'|i18n( 'design/admin/setup/preload',, hash( '%min', $preload_min, '%sec', $preload_sec ) )}{else}{'%sec s'|i18n( 'design/admin/setup/preload',, hash( '%sec', $preload_run.seconds ) )}{/if}</td>
            <td class="exp-num">{$preload_run.counts.fetched}{if $preload_run.max_pages|gt( 0 )}<span class="exp-meta"> / {$preload_run.max_pages}</span>{/if}</td>
            <td class="exp-num">{if $preload_run.aliases|is_null}<span class="exp-meta">{'not counted'|i18n( 'design/admin/setup/preload' )}</span>{else}{$preload_run.aliases}{/if}{if $preload_run.images}<br /><span class="exp-meta">{'%count images checked'|i18n( 'design/admin/setup/preload',, hash( '%count', $preload_run.counts.images ) )}</span>{/if}</td>
            <td class="exp-num">{if $preload_run.failures|count|gt( 0 )}<span class="exp-badge is-bad">{$preload_run.failures|count|sum( $preload_run.failures_more )}</span>{else}0{/if}{if $preload_run.counts.denied|gt( 0 )}<br /><span class="exp-meta">{'%count denied'|i18n( 'design/admin/setup/preload',, hash( '%count', $preload_run.counts.denied ) )}</span>{/if}</td>
        </tr>
        {if $preload_run.failures|count|gt( 0 )}
        <tr class="exp-detail-row"><td colspan="6">
            <details class="exp-failures">
                <summary>{'The addresses that failed, with the pages that link to them'|i18n( 'design/admin/setup/preload' )}</summary>
                <div class="exp-table-wrap">
                <table class="exp-table">
                    <thead><tr>
                        <th scope="col">{'Address'|i18n( 'design/admin/setup/preload' )}</th>
                        <th scope="col" class="exp-num">{'Status'|i18n( 'design/admin/setup/preload' )}</th>
                        <th scope="col">{'Linked from'|i18n( 'design/admin/setup/preload' )}</th>
                    </tr></thead>
                    <tbody>
                    {foreach $preload_run.failures as $preload_failure}
                    <tr>
                        <td class="exp-url">{if $preload_failure.url|begins_with( 'http' )}<a href="{$preload_failure.url|wash}" rel="noopener noreferrer" target="_blank">{$preload_failure.url|wash}</a>{else}{$preload_failure.url|wash}{/if}</td>
                        <td class="exp-num">{if $preload_failure.status|gt( 0 )}{$preload_failure.status}{else}<span class="exp-meta">{'no response'|i18n( 'design/admin/setup/preload' )}</span>{/if}</td>
                        <td class="exp-url">{if $preload_failure.referrers|count|eq( 0 )}<span class="exp-meta">{'a starting page; nothing on the site links to it'|i18n( 'design/admin/setup/preload' )}</span>{else}{foreach $preload_failure.referrers as $preload_from}{if $preload_from|begins_with( 'http' )}<a href="{$preload_from|wash}" rel="noopener noreferrer" target="_blank">{$preload_from|wash}</a>{else}{$preload_from|wash}{/if}{delimiter}<br />{/delimiter}{/foreach}{if $preload_failure.more|gt( 0 )}<br /><span class="exp-meta">{'... and %count more'|i18n( 'design/admin/setup/preload',, hash( '%count', $preload_failure.more ) )}</span>{/if}{/if}</td>
                    </tr>
                    {/foreach}
                    {if $preload_run.failures_more|gt( 0 )}
                    <tr><td colspan="3" class="exp-meta">{'%count more are in the output of the run.'|i18n( 'design/admin/setup/preload',, hash( '%count', $preload_run.failures_more ) )}</td></tr>
                    {/if}
                    </tbody>
                </table>
                </div>
            </details>
        </td></tr>
        {/if}
        {/foreach}
        </tbody>
    </table>
    </div>
    {/if}
</section>

{* What the script writes itself, translated; %name is replaced *}
<div id="exp-preload-text" hidden
     data-running="{'Running'|i18n( 'design/admin/setup/preload' )|wash}"
     data-finished="{'Finished'|i18n( 'design/admin/setup/preload' )|wash}"
     data-stopped="{'Stopped'|i18n( 'design/admin/setup/preload' )|wash}"
     data-failed="{'Failed'|i18n( 'design/admin/setup/preload' )|wash}"
     data-count="{'%pages of at most %max pages warmed, %broken broken'|i18n( 'design/admin/setup/preload' )|wash}"
     data-pages="{'Pages warmed'|i18n( 'design/admin/setup/preload' )|wash}"
     data-aliases="{'Image aliases made'|i18n( 'design/admin/setup/preload' )|wash}"
     data-broken="{'Broken links'|i18n( 'design/admin/setup/preload' )|wash}"
     data-images="{'Images checked'|i18n( 'design/admin/setup/preload' )|wash}"
     data-duration="{'Seconds'|i18n( 'design/admin/setup/preload' )|wash}"
     data-not-counted="{'not counted'|i18n( 'design/admin/setup/preload' )|wash}"
     data-no-broken="{'No broken links were found.'|i18n( 'design/admin/setup/preload' )|wash}"
     data-broken-head="{'Broken links and the pages that link to them'|i18n( 'design/admin/setup/preload' )|wash}"
     data-col-link="{'Address'|i18n( 'design/admin/setup/preload' )|wash}"
     data-col-status="{'Status'|i18n( 'design/admin/setup/preload' )|wash}"
     data-col-from="{'Linked from'|i18n( 'design/admin/setup/preload' )|wash}"
     data-no-response="{'no response'|i18n( 'design/admin/setup/preload' )|wash}"
     data-start-page="{'a starting page; nothing on the site links to it'|i18n( 'design/admin/setup/preload' )|wash}"
     data-more="{'... and %count more'|i18n( 'design/admin/setup/preload' )|wash}"
     data-lost="{'The progress could not be read. Reload the page to see how the run is doing.'|i18n( 'design/admin/setup/preload' )|wash}"
     data-reload="{'Show it in the list of runs'|i18n( 'design/admin/setup/preload' )|wash}"
     data-idle-note="{'The run goes on in the background. One preload runs at a time.'|i18n( 'design/admin/setup/preload' )|wash}"></div>

{undef $preload_states $preload_sources $preload_min $preload_sec $preload_last}

</div></div></div>
</div>

{literal}
<script>
(function () {
    'use strict';
    var root = document.getElementById( 'exp-preload' );
    if ( !root || !window.XMLHttpRequest || !window.JSON ) return;
    var text = document.getElementById( 'exp-preload-text' ).dataset;
    var jobUrl = root.getAttribute( 'data-job-url' );
    var live = document.getElementById( 'exp-preload-live' );
    var consoleEl = document.getElementById( 'exp-preload-console' );
    var brokenEl = document.getElementById( 'exp-preload-broken' );
    var figuresEl = document.getElementById( 'exp-preload-figures' );
    var stateEl = document.getElementById( 'exp-preload-state' );
    var barEl = document.getElementById( 'exp-preload-bar' );
    var countEl = document.getElementById( 'exp-preload-count' );
    var currentEl = document.getElementById( 'exp-preload-current' );
    var startBtn = document.getElementById( 'preload-start' );
    var noteEl = document.getElementById( 'preload-start-note' );
    var jobId = root.getAttribute( 'data-running-id' ) || '';
    var offset = 0, timer = null, failuresInRow = 0;

    function tr( s, values ) {
        for ( var key in values || {} ) s = s.split( '%' + key ).join( values[key] );
        return s;
    }
    function el( tag, cls, txt ) {
        var e = document.createElement( tag );
        if ( cls ) e.className = cls;
        if ( txt !== undefined && txt !== null ) e.appendChild( document.createTextNode( String( txt ) ) );
        return e;
    }
    // Only an http address of the crawl becomes a link; anything else is text.
    function link( url ) {
        if ( !/^https?:\/\//i.test( url ) ) return document.createTextNode( url );
        var a = el( 'a', '', url );
        a.href = url; a.target = '_blank'; a.rel = 'noopener noreferrer';
        return a;
    }
    function write( type, message ) {
        var atBottom = consoleEl.scrollTop + consoleEl.clientHeight >= consoleEl.scrollHeight - 8;
        consoleEl.appendChild( el( 'div', 'is-' + String( type ).replace( /[^a-z-]/g, '' ), message ) );
        // follow the tail only while the reader is at the bottom
        if ( atBottom ) consoleEl.scrollTop = consoleEl.scrollHeight;
    }
    function renderBroken( broken ) {
        brokenEl.textContent = '';
        if ( !broken || !broken.length ) { var none = el( 'p', 'exp-meta', text.noBroken ); none.style.marginTop = '8px'; brokenEl.appendChild( none ); return; }
        brokenEl.appendChild( el( 'h3', '', text.brokenHead ) );
        var wrap = el( 'div', 'exp-table-wrap' ), table = el( 'table', 'exp-table' ), head = el( 'tr' );
        wrap.style.marginTop = '8px';
        [ text.colLink, text.colStatus, text.colFrom ].forEach( function ( label ) { head.appendChild( el( 'th', '', label ) ); } );
        var thead = el( 'thead' ); thead.appendChild( head ); table.appendChild( thead );
        var tbody = el( 'tbody' );
        broken.forEach( function ( entry ) {
            var row = el( 'tr' ), cell = el( 'td', 'exp-url' );
            cell.appendChild( link( entry.url ) ); row.appendChild( cell );
            row.appendChild( el( 'td', 'exp-num', entry.status ? entry.status : text.noResponse ) );
            var from = el( 'td', 'exp-url' ), list = entry.referrers || [];
            if ( !list.length ) from.appendChild( el( 'span', 'exp-meta', text.startPage ) );
            list.forEach( function ( url, i ) { if ( i ) from.appendChild( el( 'br' ) ); from.appendChild( link( url ) ); } );
            if ( entry.more > 0 ) { from.appendChild( el( 'br' ) ); from.appendChild( el( 'span', 'exp-meta', tr( text.more, { count: entry.more } ) ) ); }
            row.appendChild( from ); tbody.appendChild( row );
        } );
        table.appendChild( tbody ); wrap.appendChild( table ); brokenEl.appendChild( wrap );
    }
    function figure( value, label, attention ) {
        var li = el( 'li', 'exp-figure' + ( attention ? ' is-attention' : '' ) );
        li.appendChild( el( 'strong', '', value ) ); li.appendChild( el( 'span', '', label ) );
        figuresEl.appendChild( li );
    }
    function showStatus( status ) {
        if ( !status ) return;
        var c = status.counts || {};
        var max = status.max_pages || 0;
        if ( barEl && max ) barEl.style.width = Math.min( 100, Math.round( ( c.fetched || 0 ) * 100 / max ) ) + "%";
        if ( countEl ) countEl.textContent = tr( text.count, { pages: c.fetched || 0, max: max, broken: c.broken || 0 } );
        if ( currentEl ) currentEl.textContent = status.current || "";
        if ( status.running ) return;
        figuresEl.textContent = '';
        figure( c.fetched || 0, text.pages );
        figure( status.aliases === null ? text.notCounted : status.aliases, text.aliases );
        figure( ( c.broken || 0 ) + ( c.images_broken || 0 ), text.broken, ( c.broken || 0 ) + ( c.images_broken || 0 ) > 0 );
        if ( status.images ) figure( c.images || 0, text.images );
        if ( status.seconds !== null ) figure( status.seconds, text.duration );
        figuresEl.hidden = false;
    }
    function finish( status ) {
        timer = null; jobId = '';
        var state = status ? status.state : 'failed';
        stateEl.textContent = state === 'finished' ? text.finished : ( state === 'stopped' ? text.stopped : text.failed );
        showStatus( status );
        var bar = document.getElementById( 'exp-preload-status' );
        if ( bar ) {
            var pill = bar.querySelector( '.exp-pill' );
            if ( pill ) { pill.classList.remove( 'is-running' ); pill.lastChild.textContent = stateEl.textContent; }
            var stop = document.getElementById( 'exp-preload-stop' );
            if ( stop ) stop.hidden = true;
            var refresh = document.getElementById( 'exp-preload-refresh' );
            if ( refresh ) { refresh.textContent = text.reload; refresh.hidden = false; }
        }
        if ( startBtn ) startBtn.disabled = false;
        if ( noteEl ) noteEl.textContent = text.idleNote;
    }
    function poll() {
        if ( !jobId ) return;
        var request = new XMLHttpRequest();
        request.open( 'GET', jobUrl + '/' + jobId + '/' + offset, true );
        request.setRequestHeader( 'X-Requested-With', 'XMLHttpRequest' );
        request.onload = function () {
            var answer = null;
            try { answer = JSON.parse( request.responseText ); } catch ( e ) {}
            if ( !answer || !answer.events ) {
                if ( ++failuresInRow > 5 ) { write( 'error', text.lost ); timer = null; return; }
                timer = window.setTimeout( poll, 3000 ); return;
            }
            failuresInRow = 0;
            answer.events.forEach( function ( event ) {
                try {
                    if ( event.type === 'report' ) renderBroken( event.broken );
                    write( event.type, event.message );
                } catch ( e ) { write( 'error', String( e ) ); }
            } );
            offset = answer.offset;
            showStatus( answer.status );
            if ( answer.done ) finish( answer.status );
            else timer = window.setTimeout( poll, 1000 );
        };
        request.onerror = function () { timer = window.setTimeout( poll, 3000 ); };
        request.send();
    }
    // Start, Dry run and Stop post the form; the page the server draws next shows the run, and this follows it.
    if ( jobId ) {
        live.hidden = false;
        stateEl.textContent = text.running;
        var refresh = document.getElementById( 'exp-preload-refresh' );
        if ( refresh ) refresh.hidden = true;
        poll();
    }
})();
</script>
{/literal}
