{* Setup > Caches: every cache of the installation in groups, with what it holds, its id and tags, its directory,
   its size (on request), when it was last cleared and what clearing it reaches; clear selected, a group, by tag
   or all, each with an in-place confirmation that names what goes; the HTTP cache, the query cache, the SQL
   profile, Velocity's response cache and PHP's caches of the server that answered; the static cache; and the
   commands that do the same from a shell.

   The caches are described by expCacheCatalogue ($cache_groups, $cache_overview) and cleared by expCacheManager.
   Every POST name of the earlier page is kept (ClearAllCacheButton, ClearContentCacheButton, ClearINICacheButton,
   ClearTemplateCacheButton, ClearCacheButton with CacheList[], ClearQueryCacheButton, ClearHttpCacheButton,
   ResetOPcacheButton, ClearAPCuButton, RegenerateStaticCacheButton, StaticCacheSiteAccess); new are
   ClearGroupButton and ClearVelocityCacheButton. The form carries the form token.

   The same file is in design/admin and design/admin4. The styling is setup/cache_exp_style.tpl, scoped to
   .exp-cachepage. Everything works without javascript; the search, the group filter, the selection count and the
   confirmation of "Clear selected" need it. Guide: doc/guides/caches.md *}
{include uri='design:setup/cache_exp_style.tpl'}
{def $cp_tag_buttons = array( hash( 'tag', 'content', 'name', 'ClearContentCacheButton', 'label', 'Clear content caches'|i18n( 'design/admin/setup/cache' ) ),
                              hash( 'tag', 'template', 'name', 'ClearTemplateCacheButton', 'label', 'Clear template caches'|i18n( 'design/admin/setup/cache' ) ),
                              hash( 'tag', 'ini', 'name', 'ClearINICacheButton', 'label', 'Clear INI caches'|i18n( 'design/admin/setup/cache' ) ) )
     $cp_consequence = false()}
<form name="clearcacheform" id="clearcacheform" method="post" action={"/setup/cache/"|ezurl}>
<div class="context-block exp-cachepage" id="exp-cachepage" data-cache-count="{$cache_overview.caches|wash}" data-audit-file="{$cache_overview.audit_file|wash}" data-audit-records="{$cache_overview.audit_records|wash}"
     data-selected="{'selected'|i18n( 'design/admin/setup/cache' )|wash}" data-none="{'No cache is selected yet.'|i18n( 'design/admin/setup/cache' )|wash}" data-shown="{'caches shown'|i18n( 'design/admin/setup/cache' )|wash}">

<div class="box-header"><div class="box-tc"><div class="box-ml"><div class="box-mr"><div class="box-tl"><div class="box-tr">
<h1 class="context-title">{'Caches'|i18n( 'design/admin/setup/cache' )}</h1>
<div class="header-mainline"></div>
</div></div></div></div></div></div>

<div class="box-ml"><div class="box-mr"><div class="box-content">

<p class="exp-intro">{'Every cache of this installation, what it holds and what clearing it reaches. A cleared cache is made again on the next requests, which are slower until then. The files are in var/, which Apache with PHP-FPM and Exponential Velocity share: clearing them here reaches both servers. Velocity keeps settings in memory and serves pages from its own response cache, so some caches ask for one more step, named where it is needed.'|i18n( 'design/admin/setup/cache' )}</p>

{* What the last action did *}
{if $cache_result}
<div class="exp-feedback {if $cache_result.ok}is-ok{else}is-warn{/if}" role="status" id="exp-cache-result">
    <p><strong>{$cache_result.message|wash}</strong> <span>{'%ms ms'|i18n( 'design/admin/setup/cache',, hash( '%ms', $cache_result.ms|wash ) )} &middot; {currentdate()|l10n( shortdatetime )}</span></p>
    {if $cache_result.names}
    <ul>{foreach $cache_result.names as $cp_name}<li>{$cp_name|wash}</li>{/foreach}</ul>
    {/if}
    {if $cache_result.restart}<p>{'A running Velocity still holds the old settings in memory: restart it with %command.'|i18n( 'design/admin/setup/cache',, hash( '%command', '<code>./console exp:velocity restart</code>' ) )}</p>{/if}
    {if $cache_result.response_cache}<p>{'Velocity\'s response cache may serve pages made before this for a few seconds more; clear it below or with %command.'|i18n( 'design/admin/setup/cache',, hash( '%command', '<code>./console exp:velocity cache clear</code>' ) )}</p>{/if}
    {if $cache_result.command}<p>{'From a shell:'|i18n( 'design/admin/setup/cache' )} <code>{$cache_result.command|wash}</code></p>{/if}
</div>
{/if}

{* Overview *}
<ul class="exp-figures">
    <li class="exp-figure"><strong>{$cache_overview.caches|wash}</strong><span>{'caches'|i18n( 'design/admin/setup/cache' )}</span></li>
    <li class="exp-figure"><strong>{$cache_overview.enabled|wash}</strong><span>{'enabled'|i18n( 'design/admin/setup/cache' )}</span></li>
    <li class="exp-figure"><strong>{$cache_overview.groups|wash}</strong><span>{'groups'|i18n( 'design/admin/setup/cache' )}</span></li>
    {if $cache_overview.measured}
    <li class="exp-figure"><strong>{$cache_overview.size_text|wash}</strong><span>{'%files files on disk'|i18n( 'design/admin/setup/cache',, hash( '%files', $cache_overview.files|wash ) )}</span></li>
    {else}
    <li class="exp-figure"><strong>&ndash;</strong><span><a href={'/setup/cache/(sizes)/1'|ezurl}>{'Measure the sizes'|i18n( 'design/admin/setup/cache' )}</a></span></li>
    {/if}
    <li class="exp-figure is-date"><strong>{if $cache_overview.last_cleared_text}{$cache_overview.last_cleared_text|wash}{else}&ndash;{/if}</strong><span>{'last clear'|i18n( 'design/admin/setup/cache' )}</span></li>
</ul>

{* Clear all and by tag, each confirmed in place *}
<div class="exp-actionbar">
    <details class="exp-confirm">
        <summary class="exp-btn exp-btn-primary">{'Clear all caches…'|i18n( 'design/admin/setup/cache' )}</summary>
        <div class="exp-confirm-body">
            <p><strong>{'These %count caches are cleared:'|i18n( 'design/admin/setup/cache',, hash( '%count', $cache_all.names|count ) )}</strong></p>
            <ul>{foreach $cache_all.names as $cp_name}<li>{$cp_name|wash}</li>{/foreach}</ul>
            <p>{'The site is slow until they are made again, on every server that shares var/.'|i18n( 'design/admin/setup/cache' )}{if $cache_all.restart} {'Restart Velocity afterwards: it keeps settings in memory.'|i18n( 'design/admin/setup/cache' )}{/if}</p>
            <p><code>php bin/php/ezcache.php --clear-all --allow-root-user</code></p>
            <input class="exp-btn exp-btn-danger" type="submit" name="ClearAllCacheButton" value="{'Clear all caches'|i18n( 'design/admin/setup/cache' )}" />
        </div>
    </details>
    {foreach $cp_tag_buttons as $cp_tag}
    {set $cp_consequence = $cache_tags[$cp_tag.tag]}
    <details class="exp-confirm">
        <summary class="exp-btn">{$cp_tag.label|wash}…</summary>
        <div class="exp-confirm-body">
            <p><strong>{'The caches tagged %tag:'|i18n( 'design/admin/setup/cache',, hash( '%tag', $cp_tag.tag ) )}</strong></p>
            <ul>{foreach $cp_consequence.names as $cp_name}<li>{$cp_name|wash}</li>{/foreach}</ul>
            {if $cp_consequence.restart}<p>{'Restart Velocity afterwards: it keeps settings in memory.'|i18n( 'design/admin/setup/cache' )}</p>{/if}
            <p><code>php bin/php/ezcache.php --clear-tag={$cp_tag.tag|wash} --allow-root-user</code></p>
            <input class="exp-btn exp-btn-danger" type="submit" name="{$cp_tag.name|wash}" value="{$cp_tag.label|wash}" />
        </div>
    </details>
    {/foreach}
    {if $cache_sizes}
        <a class="exp-btn" href={'/setup/cache'|ezurl}>{'Without sizes (faster)'|i18n( 'design/admin/setup/cache' )}</a>
    {else}
        <a class="exp-btn" href={'/setup/cache/(sizes)/1'|ezurl}>{'Measure sizes'|i18n( 'design/admin/setup/cache' )}</a>
    {/if}
    <p class="exp-meta">{if $cache_sizes}{'Sizes were measured for this view, within three seconds; a size marked ≥ was cut short.'|i18n( 'design/admin/setup/cache' )}{else}{'Sizes are measured only on request, as that reads every file.'|i18n( 'design/admin/setup/cache' )}{/if}</p>
<p class="exp-meta">{if $cache_overview.audit}{'Last cleared comes from the audit trail, with who cleared: clears through this page, System information, ./console exp:cache and bin/php/ezcache.php (which exp:velocity deploy runs) are recorded; a shell is named by its operating system user.'|i18n( 'design/admin/setup/cache' )}{if $cache_overview.audit_who|not} {'Who cleared is shown to users who may read the system channel of the audit.'|i18n( 'design/admin/setup/cache' )}{/if}{else}{'Last cleared comes from the expiry times the kernel records; the audit trail is not available here.'|i18n( 'design/admin/setup/cache' )}{/if}</p>
</div>

{* Search and group filter (javascript; without it every cache is shown) *}
<div class="exp-toolbar" id="exp-cache-toolbar" hidden>
    <div class="exp-field">
        <label for="exp-cache-search">{'Search'|i18n( 'design/admin/setup/cache' )}</label>
        <input type="search" id="exp-cache-search" placeholder="{'Name, id, tag or directory'|i18n( 'design/admin/setup/cache' )}" autocomplete="off" />
    </div>
    <fieldset class="exp-field">
        <legend>{'Groups'|i18n( 'design/admin/setup/cache' )}</legend>
        <div class="exp-filter-chips">
            <label class="exp-filter-chip"><input type="radio" name="exp-cache-group-filter" value="" checked="checked" /><span>{'All'|i18n( 'design/admin/setup/cache' )}</span></label>
            {foreach $cache_groups as $cp_key => $cp_group}
            <label class="exp-filter-chip"><input type="radio" name="exp-cache-group-filter" value="{$cp_key|wash}" /><span>{$cp_group.title|wash}</span></label>
            {/foreach}
        </div>
    </fieldset>
    <p class="exp-filter-count" id="exp-cache-count" role="status" aria-live="polite"></p>
</div>

{* The groups *}
{foreach $cache_groups as $cp_key => $cp_group}
<section class="exp-group" id="exp-cache-group-{$cp_key|wash}" data-group="{$cp_key|wash}" aria-labelledby="exp-cache-group-title-{$cp_key|wash}">
    <div class="exp-group-head">
        <h2 class="exp-h2" id="exp-cache-group-title-{$cp_key|wash}">{$cp_group.title|wash}</h2>
        <span class="exp-muted">{if eq( $cp_group.count, 1 )}{'1 cache'|i18n( 'design/admin/setup/cache' )}{else}{'%count caches'|i18n( 'design/admin/setup/cache',, hash( '%count', $cp_group.count ) )}{/if}{if $cp_group.measured} &middot; {$cp_group.size_text|wash}, {'%files files'|i18n( 'design/admin/setup/cache',, hash( '%files', $cp_group.files|wash ) )}{/if}</span>
        {if $cp_group.restart}<span class="exp-badge is-warn">{'restart Velocity after clearing'|i18n( 'design/admin/setup/cache' )}</span>{/if}
        <p>{$cp_group.intro|wash}</p>
    </div>

    {if $cp_group.items}
    <div class="exp-table-wrap">
    <table class="exp-caches">
    <thead><tr>
        <th scope="col"><span class="exp-sr">{'Select'|i18n( 'design/admin/setup/cache' )}</span></th>
        <th scope="col">{'Cache'|i18n( 'design/admin/setup/cache' )}</th>
        <th scope="col" class="exp-num">{'Size'|i18n( 'design/admin/setup/cache' )}</th>
        <th scope="col">{'Last cleared'|i18n( 'design/admin/setup/cache' )}</th>
    </tr></thead>
    <tbody>
    {foreach $cp_group.items as $cp_item}
    <tr data-search="{$cp_item.search|wash}" class="exp-cache-row{if $cp_item.enabled|not} is-disabled{/if}">
        <td class="exp-check-cell">
            {if $cp_item.enabled}
            <input type="checkbox" name="CacheList[]" value="{$cp_item.id|wash}" id="exp-cache-{$cp_item.id|wash}" data-name="{$cp_item.name|wash}" />
            {else}
            <input type="checkbox" name="CacheList[]" value="{$cp_item.id|wash}" id="exp-cache-{$cp_item.id|wash}" disabled="disabled" />
            {/if}
        </td>
        <td>
            <label class="exp-cache-name" for="exp-cache-{$cp_item.id|wash}">{$cp_item.name|wash}</label>
            {if $cp_item.holds}<span class="exp-cache-holds">{$cp_item.holds|wash}</span>{/if}
            <span class="exp-cache-meta"><code>{$cp_item.id|wash}</code>{if $cp_item.tags} &middot; {'tags'|i18n( 'design/admin/setup/cache' )} {$cp_item.tags|implode( ', ' )|wash}{/if} &middot; {if $cp_item.path}<code>{$cp_item.path|wash}</code>{if $cp_item.exists|not} ({'not there yet'|i18n( 'design/admin/setup/cache' )}){/if}{else}{'no directory'|i18n( 'design/admin/setup/cache' )}{/if}</span>
            {if $cp_item.enabled|not}<span class="exp-badge">{'disabled in the settings'|i18n( 'design/admin/setup/cache' )}</span>{/if}
            {if $cp_item.restart}<span class="exp-badge is-warn">{'restart Velocity after clearing'|i18n( 'design/admin/setup/cache' )}</span>{/if}
            {if $cp_item.response_cache}<span class="exp-badge is-info">{'also in Velocity\'s response cache'|i18n( 'design/admin/setup/cache' )}</span>{/if}
        </td>
        <td class="exp-num" data-label="{'Size'|i18n( 'design/admin/setup/cache' )}">{if $cp_item.measured}{$cp_item.size_text|wash}<br /><span class="exp-muted">{cond( eq( $cp_item.files, 1 ), '1 file'|i18n( 'design/admin/setup/cache' ), '%files files'|i18n( 'design/admin/setup/cache',, hash( '%files', $cp_item.files|wash ) ) )}</span>{else}<span class="exp-muted">&ndash;</span>{/if}</td>
        <td class="exp-cleared-cell" data-cleared="{$cp_item.last_cleared|wash}" data-source="{$cp_item.last_cleared_source|wash}" data-audit="{$cp_item.last_audit|wash}" data-label="{'Last cleared'|i18n( 'design/admin/setup/cache' )}">{if $cp_item.last_cleared_text}<span class="exp-cleared-when">{$cp_item.last_cleared_text|wash}</span>{if $cp_item.last_cleared_by}<span class="exp-cleared-by">{if $cp_item.last_cleared_shell}{'from a shell, %who'|i18n( 'design/admin/setup/cache',, hash( '%who', $cp_item.last_cleared_by|wash ) )}{else}{'by %who'|i18n( 'design/admin/setup/cache',, hash( '%who', $cp_item.last_cleared_by|wash ) )}{/if}</span>{/if}{else}<span class="exp-muted">{'not recorded'|i18n( 'design/admin/setup/cache' )}</span>{/if}</td>
    </tr>
    {/foreach}
    </tbody>
    </table>
    </div>
    {/if}

    {if $cp_group.ids}
    <div class="exp-group-foot">
        <details class="exp-confirm">
            <summary class="exp-btn">{'Clear this group…'|i18n( 'design/admin/setup/cache' )}</summary>
            <div class="exp-confirm-body">
                <p><strong>{'These %count caches are cleared:'|i18n( 'design/admin/setup/cache',, hash( '%count', $cp_group.ids|count ) )}</strong></p>
                <ul>{foreach $cp_group.items as $cp_item}{if $cp_item.enabled}<li>{$cp_item.name|wash}</li>{/if}{/foreach}</ul>
                {if $cp_group.restart}<p>{'Restart Velocity afterwards: it keeps settings in memory.'|i18n( 'design/admin/setup/cache' )}</p>{/if}
                <button class="exp-btn exp-btn-danger" type="submit" name="ClearGroupButton" value="{$cp_key|wash}">{'Clear %group'|i18n( 'design/admin/setup/cache',, hash( '%group', $cp_group.title|wash ) )}</button>
            </div>
        </details>
        <code>{$cp_group.command|wash}</code>
    </div>
    {/if}

    {if eq( $cp_key, 'velocity' )}
    {* The server's own caches and the buttons System information has too (expCacheManager::$sharedActions) *}
    <ul class="exp-server-rows" id="cache-maintenance">
        <li>
            <div class="exp-server-text"><strong>{'Whole pages (HTTP cache)'|i18n( 'design/admin/setup/cache' )}</strong> <span class="exp-badge {if $http_cache_enabled}is-ok{/if}">{if $http_cache_enabled}{'enabled'|i18n( 'design/admin/setup/cache' )}{else}{'off'|i18n( 'design/admin/setup/cache' )}{/if}</span>
                <small>{'Hit rates and entries are on'|i18n( 'design/admin/setup/cache' )} <a href="{'/setup/info'|ezurl( 'no' )}#http-cache">{'System information'|i18n( 'design/admin/setup/cache' )}</a>. {'Publishing already purges the pages it affects.'|i18n( 'design/admin/setup/cache' )}</small></div>
            <div class="exp-actions">
                <input class="exp-btn exp-btn-small" type="submit" name="ClearHttpCacheButton" value="{'Clear HTTP cache'|i18n( 'design/admin/setup/cache' )}"{if $http_cache_enabled|not} disabled="disabled"{/if} />
                <button class="exp-btn exp-btn-small" type="submit" name="HttpCacheAction" value="gc"{if $http_cache_enabled|not} disabled="disabled"{/if}>{'Remove dead entries'|i18n( 'design/admin/setup/cache' )}</button>
                <button class="exp-btn exp-btn-small" type="submit" name="HttpCacheAction" value="reset"{if $http_cache_enabled|not} disabled="disabled"{/if}>{'Reset counters'|i18n( 'design/admin/setup/cache' )}</button>
            </div>
        </li>
        <li>
            <div class="exp-server-text"><strong>{'Database query results (SQL query cache)'|i18n( 'design/admin/setup/cache' )}</strong> <span class="exp-badge {if $query_cache_enabled}is-ok{/if}">{if $query_cache_enabled}{$query_cache_mode|wash}{else}{'off'|i18n( 'design/admin/setup/cache' )}{/if}</span>
                <small>{'Writes already invalidate the tables they touch; clear it after changing the database outside Exponential.'|i18n( 'design/admin/setup/cache' )}</small></div>
            <div class="exp-actions">
                <input class="exp-btn exp-btn-small" type="submit" name="ClearQueryCacheButton" value="{'Clear query cache'|i18n( 'design/admin/setup/cache' )}" />
                <button class="exp-btn exp-btn-small" type="submit" name="QueryCacheAction" value="reset">{'Reset the counters'|i18n( 'design/admin/setup/cache' )}</button>
            </div>
        </li>
        <li>
            <div class="exp-server-text"><strong>{'SQL profile of every request'|i18n( 'design/admin/setup/cache' )}</strong> <span class="exp-badge {if $sql_profile_on}is-info{/if}">{if $sql_profile_on}{'on'|i18n( 'design/admin/setup/cache' )}{else}{'off'|i18n( 'design/admin/setup/cache' )}{/if}</span>
                <small>{'Each request writes how many statements it ran to var/tmp/sql_profile.log, on every server.'|i18n( 'design/admin/setup/cache' )}</small></div>
            <div class="exp-actions">
            {if $sql_profile_on}
                <button class="exp-btn exp-btn-small" type="submit" name="SQLProfileAction" value="off">{'Switch the SQL profile off'|i18n( 'design/admin/setup/cache' )}</button>
            {else}
                <button class="exp-btn exp-btn-small" type="submit" name="SQLProfileAction" value="on">{'Switch the SQL profile on'|i18n( 'design/admin/setup/cache' )}</button>
            {/if}
            </div>
        </li>
        {if $velocity_cache}
        <li>
            <div class="exp-server-text"><strong>{'Velocity response cache'|i18n( 'design/admin/setup/cache' )}</strong>
                <span class="exp-badge">{'%files files, %size'|i18n( 'design/admin/setup/cache',, hash( '%files', $velocity_cache.files|wash, '%size', $velocity_cache.size|wash ) )}</span>
                <small>{'Pages Velocity answers without PHP, for a few seconds each.'|i18n( 'design/admin/setup/cache' )} {if $velocity_cache.cleared}{'Last cleared %time.'|i18n( 'design/admin/setup/cache',, hash( '%time', $velocity_cache.cleared|wash ) )}{/if} <code>./console exp:velocity cache clear</code></small></div>
            <div class="exp-actions">
                <input class="exp-btn exp-btn-small" type="submit" name="ClearVelocityCacheButton" value="{'Clear Velocity\'s response cache'|i18n( 'design/admin/setup/cache' )}" />
            </div>
        </li>
        {/if}
        <li id="php-caches">
            <div class="exp-server-text"><strong>{'OPcache (compiled PHP scripts)'|i18n( 'design/admin/setup/cache' )}</strong> <span class="exp-badge">{$php_cache_state.opcache.text|wash}</span>
                <small>{'Of the server process that answered this page only; another server or pool keeps its own.'|i18n( 'design/admin/setup/cache' )}</small></div>
            <div class="exp-actions">
                <input class="exp-btn exp-btn-small" type="submit" name="ResetOPcacheButton" value="{'Reset OPcache'|i18n( 'design/admin/setup/cache' )}"{if $php_cache_state.opcache.available|not} disabled="disabled"{/if} />
            </div>
        </li>
        <li>
            <div class="exp-server-text"><strong>{'APCu (data in shared memory)'|i18n( 'design/admin/setup/cache' )}</strong> <span class="exp-badge">{$php_cache_state.apcu.text|wash}</span>
                <small>{'Every entry any application stored there is gone, including the memory tier of Velocity\'s response cache.'|i18n( 'design/admin/setup/cache' )}</small></div>
            <div class="exp-actions">
                <input class="exp-btn exp-btn-small" type="submit" name="ClearAPCuButton" value="{'Empty APCu'|i18n( 'design/admin/setup/cache' )}"{if $php_cache_state.apcu.available|not} disabled="disabled"{/if} />
            </div>
        </li>
    </ul>
    {/if}
</section>
{/foreach}

{* Clear selected: the existing ClearCacheButton with CacheList[]; with javascript it names the selection first *}
<div class="exp-bottombar" id="exp-cache-selected">
    <label class="exp-btn exp-btn-small" for="exp-cache-select-all" hidden id="exp-cache-select-all-label"><input type="checkbox" id="exp-cache-select-all" /> {'Select all shown'|i18n( 'design/admin/setup/cache' )}</label>
    <span class="exp-meta" id="exp-cache-selection" role="status" aria-live="polite">{'Tick caches in the lists above, then clear them.'|i18n( 'design/admin/setup/cache' )}</span>
    <input class="exp-btn exp-btn-primary" type="submit" name="ClearCacheButton" id="exp-cache-clear-selected" value="{'Clear selected'|i18n( 'design/admin/setup/cache' )}" />
</div>
<div class="exp-feedback is-warn" id="exp-cache-confirm" hidden role="alertdialog" aria-labelledby="exp-cache-confirm-title">
    <p><strong id="exp-cache-confirm-title">{'These caches are cleared:'|i18n( 'design/admin/setup/cache' )}</strong></p>
    <ul id="exp-cache-confirm-list"></ul>
    <div class="exp-actions">
        <button class="exp-btn exp-btn-danger" type="button" id="exp-cache-confirm-yes">{'Clear them'|i18n( 'design/admin/setup/cache' )}</button>
        <button class="exp-btn" type="button" id="exp-cache-confirm-no">{'Cancel'|i18n( 'design/admin/setup/cache' )}</button>
    </div>
</div>

{* The static cache *}
<details class="exp-panel" id="exp-static-cache">
    <summary><h2 class="exp-h2">{'Static content cache'|i18n( 'design/admin/setup/cache' )}</h2><span class="exp-muted">{'pages stored as files, answered by the web server without the CMS'|i18n( 'design/admin/setup/cache' )}</span></summary>
    <div class="exp-panel-body">
    {if $static_cache_siteaccess_list|count|eq(0)}
        <div class="exp-feedback is-warn"><p><strong>{'No site can be cached.'|i18n( 'design/admin/setup/cache' )}</strong> {'Every siteaccess either requires a login or has no SiteSettings/SiteURL, so there is no page to fetch and store.'|i18n( 'design/admin/setup/cache' )}</p></div>
    {else}
        <p class="exp-note">{'Pages are written to'|i18n( 'design/admin/setup/cache' )}: <code>{$static_cache_storage_dir|wash}</code></p>
        <div class="exp-static-grid">
            <div class="exp-field">
                <label for="staticcache-siteaccess">{'Site to generate'|i18n( 'design/admin/setup/cache' )}</label>
                <select id="staticcache-siteaccess" name="StaticCacheSiteAccess">
                {foreach $static_cache_siteaccess_list as $static_cache_target}
                    <option value="{$static_cache_target.name|wash}">{$static_cache_target.name|wash} &mdash; {$static_cache_target.url|wash}</option>
                {/foreach}
                    <option value="">{'All sites'|i18n( 'design/admin/setup/cache' )}</option>
                </select>
            </div>
            <div class="exp-field">
                <label for="staticcache-max-pages">{'Pages'|i18n( 'design/admin/setup/cache' )}</label>
                <input id="staticcache-max-pages" type="text" size="6" value="2500" />
            </div>
            <div class="exp-field">
                <label for="staticcache-max-depth">{'Link depth'|i18n( 'design/admin/setup/cache' )}</label>
                <input id="staticcache-max-depth" type="text" size="4" value="12" />
            </div>
        </div>
        <div class="exp-actions">
            <input class="exp-btn exp-btn-primary" id="staticcache-start" type="submit" name="RegenerateStaticCacheButton" value="{'Create new'|i18n( 'design/admin/setup/cache' )}" title="{'Fetches every url of the chosen site that the site itself links to and stores the page, so the web server can answer the next visitor from a file instead of starting the CMS. This can take some time on a large site. If you encounter time-out problems, use the &quot;bin/php/makestaticcache.php&quot; shell script.'|i18n( 'design/admin/setup/cache' )}" />
            <input class="exp-btn" id="staticcache-stop" type="button" disabled="disabled" value="{'Stop'|i18n( 'design/admin/setup/cache' )}" />
            <span id="staticcache-status"></span>
        </div>
        {if $static_cache_enabled|not}
        <div class="exp-feedback is-warn"><p>{'Generated pages will not be refreshed when an editor publishes, because site.ini [ContentSettings] StaticCache is not enabled.'|i18n( 'design/admin/setup/cache' )}</p></div>
        {/if}
        <pre id="staticcache-console" style="background:#1b1b1b;color:#d8d8d8;padding:.8em;height:20em;overflow:auto;font:12px/1.5 monospace;border:1px solid #444;border-radius:9px;margin:0;white-space:pre-wrap;word-break:break-word;display:none;"></pre>
<script type="text/javascript">
(function () {ldelim}
    var streamUrl = {$static_cache_stream_url|ezurl()};
    var consoleEl = document.getElementById( 'staticcache-console' );
    var startBtn  = document.getElementById( 'staticcache-start' );
    var stopBtn   = document.getElementById( 'staticcache-stop' );
    var statusEl  = document.getElementById( 'staticcache-status' );
    var saEl      = document.getElementById( 'staticcache-siteaccess' );
    var source    = null;
    var lines     = 0;

    if ( !startBtn || typeof window.EventSource === 'undefined' )
        return; {* No EventSource: the button stays an ordinary form submit. *}

    var colours = {ldelim}
        phase:        '#7fd1ff',
        'phase-item': '#ffffff',
        ok:           '#9bdc7a',
        warn:         '#e8c765',
        error:        '#ff8a80',
        info:         '#9a9a9a',
        done:         '#7fd1ff'
    {rdelim};

    function write( type, text )
    {ldelim}
        var atBottom = consoleEl.scrollTop + consoleEl.clientHeight >= consoleEl.scrollHeight - 8;
        var line = document.createElement( 'div' );
        line.style.color = colours[type] || '#d8d8d8';
        if ( type === 'phase' || type === 'done' )
            line.style.fontWeight = 'bold';
        line.appendChild( document.createTextNode( text ) );
        consoleEl.appendChild( line );
        lines++;
        {* Only follow the tail while the operator is already at the bottom. *}
        if ( atBottom )
            consoleEl.scrollTop = consoleEl.scrollHeight;
    {rdelim}

    function finish( message )
    {ldelim}
        if ( source ) {ldelim} source.close(); source = null; {rdelim}
        startBtn.disabled = false;
        stopBtn.disabled  = true;
        statusEl.innerHTML = '';
        statusEl.appendChild( document.createTextNode( message ) );
    {rdelim}

    {* Run it in the page instead of posting the form, so every page appears as
       it is written rather than after one long silent request. *}
    startBtn.onclick = function ( event )
    {ldelim}
        if ( event && event.preventDefault ) event.preventDefault();
        if ( source ) return false;

        consoleEl.style.display = 'block';
        consoleEl.innerHTML = '';
        lines = 0;

        var pages = parseInt( document.getElementById( 'staticcache-max-pages' ).value, 10 );
        var depth = parseInt( document.getElementById( 'staticcache-max-depth' ).value, 10 );
        if ( isNaN( pages ) || pages < 1 ) pages = 2500;
        if ( isNaN( depth ) || depth < 0 ) depth = 12;

        var url = streamUrl + '?SiteAccess=' + encodeURIComponent( saEl ? saEl.value : '' )
                + '&MaxPages=' + pages + '&MaxDepth=' + depth;

        startBtn.disabled = true;
        stopBtn.disabled  = false;
        statusEl.innerHTML = '';
        statusEl.appendChild( document.createTextNode( '{'running…'|i18n( 'design/admin/setup/cache' )|wash( javascript )}' ) );

        source = new EventSource( url );

        source.onmessage = function ( messageEvent )
        {ldelim}
            var payload;
            try {ldelim} payload = JSON.parse( messageEvent.data ); {rdelim}
            catch ( e ) {ldelim} return; {rdelim}
            write( payload.type, payload.message );
            if ( payload.type === 'done' )
                finish( '{'finished'|i18n( 'design/admin/setup/cache' )|wash( javascript )}' );
        {rdelim};

        source.addEventListener( 'end', function () {ldelim} finish( '{'finished'|i18n( 'design/admin/setup/cache' )|wash( javascript )}' ); {rdelim} );

        source.onerror = function ()
        {ldelim}
            {* EventSource reconnects on its own, which would start the run
               again from the beginning; closing here keeps one run to a press. *}
            if ( lines === 0 )
                write( 'error', '{'Could not open the stream. Check that you have the setup/managecache policy.'|i18n( 'design/admin/setup/cache' )|wash( javascript )}' );
            else
                write( 'warn', '{'Stream closed.'|i18n( 'design/admin/setup/cache' )|wash( javascript )}' );
            finish( '{'stopped'|i18n( 'design/admin/setup/cache' )|wash( javascript )}' );
        {rdelim};

        return false;
    {rdelim};

    {* Closing the stream is all the browser can do: the run is already under
       way on the server and the pages it has written stay written. *}
    stopBtn.onclick = function () {ldelim}
        write( 'warn', '{'Stopped by operator. Pages written so far are kept.'|i18n( 'design/admin/setup/cache' )|wash( javascript )}' );
        finish( '{'stopped'|i18n( 'design/admin/setup/cache' )|wash( javascript )}' );
        return false;
    {rdelim};
{rdelim})();
</script>
    {/if}
    </div>
</details>

{* The same from a shell *}
<details class="exp-panel" id="exp-cache-commands">
    <summary><h2 class="exp-h2">{'From a shell'|i18n( 'design/admin/setup/cache' )}</h2><span class="exp-muted">{'the commands that do what this page does'|i18n( 'design/admin/setup/cache' )}</span></summary>
    <div class="exp-panel-body">
        <ul class="exp-commands">
            <li>{'One cache or several, by id'|i18n( 'design/admin/setup/cache' )}<code>php bin/php/ezcache.php --clear-id=template-block,content --allow-root-user</code></li>
            <li>{'By tag'|i18n( 'design/admin/setup/cache' )}<code>php bin/php/ezcache.php --clear-tag=template --allow-root-user</code></li>
            <li>{'Everything'|i18n( 'design/admin/setup/cache' )}<code>php bin/php/ezcache.php --clear-all --allow-root-user</code></li>
            <li>{'What would be cleared, with sizes, without clearing'|i18n( 'design/admin/setup/cache' )}<code>./console exp:cache clear --id=content --dry-run --allow-root-user</code></li>
            <li>{'Velocity\'s response cache'|i18n( 'design/admin/setup/cache' )}<code>./console exp:velocity cache clear</code></li>
            <li>{'Velocity, after settings changed'|i18n( 'design/admin/setup/cache' )}<code>./console exp:velocity restart --allow-root-user</code></li>
        </ul>
    </div>
</details>

</div></div></div>
</div>
</form>

{literal}
<script>
(function () {
    var page = document.getElementById('exp-cachepage');
    if (!page) return;
    var rows = Array.prototype.slice.call(page.querySelectorAll('tr.exp-cache-row'));
    var groups = Array.prototype.slice.call(page.querySelectorAll('section.exp-group'));
    var toolbar = document.getElementById('exp-cache-toolbar');
    var search = document.getElementById('exp-cache-search');
    var count = document.getElementById('exp-cache-count');
    var selection = document.getElementById('exp-cache-selection');
    var selectAll = document.getElementById('exp-cache-select-all');
    var clearButton = document.getElementById('exp-cache-clear-selected');
    var confirmBox = document.getElementById('exp-cache-confirm');
    var confirmList = document.getElementById('exp-cache-confirm-list');
    var texts = page.dataset;
    toolbar.hidden = false;
    document.getElementById('exp-cache-select-all-label').hidden = false;

    function boxes(onlyShown) {
        return rows.filter(function (r) { return !onlyShown || !r.hidden; })
                   .map(function (r) { return r.querySelector('input[name="CacheList[]"]'); })
                   .filter(function (b) { return b && !b.disabled; });
    }
    function chosen() { return boxes(false).filter(function (b) { return b.checked; }); }
    function updateSelection() {
        var n = chosen().length;
        selection.textContent = n ? n + ' ' + texts.selected : texts.none;
    }
    function filter() {
        var q = (search.value || '').trim().toLowerCase();
        var g = (page.querySelector('input[name="exp-cache-group-filter"]:checked') || {}).value || '';
        var shown = 0;
        rows.forEach(function (r) {
            var inGroup = !g || r.closest('section.exp-group').dataset.group === g;
            var hit = !q || r.dataset.search.indexOf(q) !== -1;
            r.hidden = !(inGroup && hit);
            if (!r.hidden) shown++;
        });
        groups.forEach(function (s) {
            var visible = !g || s.dataset.group === g;
            if (q && visible) visible = s.querySelectorAll('tr.exp-cache-row:not([hidden])').length > 0 || s.dataset.group === 'velocity' && !q;
            s.hidden = !visible;
        });
        count.textContent = shown + ' / ' + rows.length + ' ' + texts.shown;
    }
    search.addEventListener('input', filter);
    page.querySelectorAll('input[name="exp-cache-group-filter"]').forEach(function (r) { r.addEventListener('change', filter); });
    page.addEventListener('change', function (e) { if (e.target.name === 'CacheList[]') updateSelection(); });
    selectAll.addEventListener('change', function () {
        boxes(true).forEach(function (b) { b.checked = selectAll.checked; });
        updateSelection();
    });
    // Clear selected: name what goes, in place, before the form is sent
    var confirmed = false;
    clearButton.addEventListener('click', function (e) {
        if (confirmed) return;
        e.preventDefault();
        var list = chosen();
        confirmList.innerHTML = '';
        if (!list.length) { selection.textContent = texts.none; return; }
        list.forEach(function (b) { var li = document.createElement('li'); li.textContent = b.dataset.name || b.value; confirmList.appendChild(li); });
        confirmBox.hidden = false;
        document.getElementById('exp-cache-confirm-yes').focus();
    });
    document.getElementById('exp-cache-confirm-yes').addEventListener('click', function () {
        confirmed = true;
        clearButton.click();
    });
    document.getElementById('exp-cache-confirm-no').addEventListener('click', function () {
        confirmBox.hidden = true;
        clearButton.focus();
    });
    filter();
    updateSelection();
})();
</script>
{/literal}
{undef $cp_tag_buttons $cp_consequence}
