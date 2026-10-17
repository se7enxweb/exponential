{* The error digest of the log viewer (setup/logsdigest; doc/features/6.0/log-viewer.md, "Error digest"): per log, the
   distinct errors and warnings of the last hours, normalized and counted, most frequent first, each linked to the log's
   view filtered to it and to its newest entry; a group can be marked known (hidden for this user) and listed again.
   Variables: digest_logs (key, id, label, groups (id, level, text, sample, literal, count, first, last, known, link,
   entry_link), total, hidden, scanned, stopped, view_link), digest_key, digest_hours, digest_hour_choices,
   digest_since, digest_show_known, digest_hidden, digest_known_count, digest_levels, digest_self, digest_toggle_known,
   digest_base, digest_token_field, digest_token.

   The same file is in design/admin and design/admin4. *}
<div class="context-block a4-page a4-setup-logs a4-setup-logdigest">

<div class="box-header"><div class="box-ml">
<h1 class="context-title">{'Error digest'|i18n( 'design/admin/setup/logs' )}</h1>
<div class="header-mainline"></div>
</div></div>

<div class="box-bc"><div class="box-ml"><div class="box-content">
{include uri='design:setup/logs_nav.tpl' log_nav_current='digest'}

<p class="a4-setup-logdigest-intro">{'The distinct errors and warnings of the last hours, with numbers, ids, quoted values and paths set aside so that repeats of one problem count as one line. Messages are always masked here.'|i18n( 'design/admin/setup/logs' )}</p>

<div class="a4-row a4-setup-logdigest-filters">
    <nav class="a4-setup-logdigest-hours" aria-label="{'Time window'|i18n( 'design/admin/setup/logs' )|wash}">
        <span class="a4-setup-logdigest-label">{'Last'|i18n( 'design/admin/setup/logs' )}</span>
        <ul class="a4-setup-inline">
        {foreach $digest_hour_choices as $digest_h}
            <li><a class="a4-btn a4-btn-small{if $digest_h|eq( $digest_hours )} a4-btn-primary{/if}"{if $digest_h|eq( $digest_hours )} aria-current="page"{/if} href={concat( $digest_base, '/(hours)/', $digest_h, cond( $digest_show_known, '/(known)/1', '' ) )|ezurl}>{'%count h'|i18n( 'design/admin/setup/logs',, hash( '%count', $digest_h ) )|wash}</a></li>
        {/foreach}
        </ul>
    </nav>
    <a class="a4-btn a4-btn-small a4-btn-ghost" href={$digest_toggle_known|ezurl}>{if $digest_show_known}{'Hide known messages'|i18n( 'design/admin/setup/logs' )}{else}{'Show known messages'|i18n( 'design/admin/setup/logs' )}{/if}</a>
</div>

<p class="a4-meta">{'Since %time; levels: %levels.'|i18n( 'design/admin/setup/logs',, hash( '%time', $digest_since|l10n( 'shortdatetime' ), '%levels', $digest_levels|implode( ', ' ) ) )|wash}
{if and( $digest_hidden|gt( 0 ), $digest_show_known|not )} {'%count known messages are hidden.'|i18n( 'design/admin/setup/logs',, hash( '%count', $digest_hidden ) )|wash}{/if}</p>

{if $digest_logs|count|not}
<div class="a4-notice is-info" role="status">{'There is no log this digest can read.'|i18n( 'design/admin/setup/logs' )}</div>
{/if}

{foreach $digest_logs as $digest_log}
<section class="a4-card a4-setup-logdigest-log" aria-labelledby="digest-{$digest_log.key|wash}">
    <header class="a4-card-head">
        <h2 id="digest-{$digest_log.key|wash}"><a href={$digest_log.view_link|ezurl}>{$digest_log.label|wash}</a></h2>
        <span class="a4-meta">{'%count entries in %groups messages'|i18n( 'design/admin/setup/logs',, hash( '%count', $digest_log.total, '%groups', $digest_log.groups|count ) )|wash}</span>
    </header>
    {if $digest_log.stopped}
    <p class="a4-notice is-warn" role="status">{'Only the first %size of the window were read; the file holds more.'|i18n( 'design/admin/setup/logs',, hash( '%size', $digest_log.scanned|si( byte ) ) )|wash}</p>
    {/if}
    {if $digest_log.groups|count}
    <div class="a4-setup-logdigest-scroll">
    <table class="a4-table a4-setup-logdigest-table">
        <caption class="a4-sr-only">{'Messages of %name'|i18n( 'design/admin/setup/logs',, hash( '%name', $digest_log.label ) )|wash}</caption>
        <thead>
            <tr>
                <th scope="col" class="a4-setup-logdigest-count">{'Count'|i18n( 'design/admin/setup/logs' )}</th>
                <th scope="col">{'Level'|i18n( 'design/admin/setup/logs' )}</th>
                <th scope="col">{'Message'|i18n( 'design/admin/setup/logs' )}</th>
                <th scope="col">{'Last seen'|i18n( 'design/admin/setup/logs' )}</th>
                <th scope="col"><span class="a4-sr-only">{'Actions'|i18n( 'design/admin/setup/logs' )}</span></th>
            </tr>
        </thead>
        <tbody>
        {foreach $digest_log.groups as $digest_g}
            <tr{if $digest_g.known} class="is-known"{/if}>
                <td class="a4-setup-logdigest-count">{$digest_g.count|wash}</td>
                <td><span class="a4-badge {if $digest_g.level|eq( 'error' )}is-bad{elseif $digest_g.level|eq( 'warning' )}is-warn{else}is-info{/if}">{switch match=$digest_g.level}{case match='error'}{'Error'|i18n( 'design/admin/setup/logs' )}{/case}{case match='warning'}{'Warning'|i18n( 'design/admin/setup/logs' )}{/case}{case match='notice'}{'Notice'|i18n( 'design/admin/setup/logs' )}{/case}{case}{'Debug'|i18n( 'design/admin/setup/logs' )}{/case}{/switch}</span>{if $digest_g.known} <span class="a4-tag">{'Known'|i18n( 'design/admin/setup/logs' )}</span>{/if}</td>
                <td class="a4-setup-logdigest-msg"><code>{$digest_g.text|wash}</code>
                    {if $digest_g.sample|ne( $digest_g.text )}<span class="a4-setup-logdigest-sample">{$digest_g.sample|wash}</span>{/if}</td>
                <td class="a4-setup-logdigest-last"><a href={$digest_g.entry_link|ezurl} title="{'Open the newest entry'|i18n( 'design/admin/setup/logs' )|wash}">{$digest_g.last|l10n( 'shortdatetime' )}</a></td>
                <td class="a4-setup-logdigest-actions">
                    <a class="a4-btn a4-btn-small a4-btn-primary" href={$digest_g.link|ezurl}>{'Show'|i18n( 'design/admin/setup/logs' )}<span class="a4-sr-only"> {$digest_g.text|wash}</span></a>
                    <form method="post" action={$digest_self|ezurl} class="a4-setup-logdigest-form">
                        <input type="hidden" name="{$digest_token_field|wash}" value="{$digest_token|wash}" />
                        {if $digest_g.known}
                        <button type="submit" class="a4-btn a4-btn-small" name="UnknownButton" value="{$digest_g.id|wash}">{'List again'|i18n( 'design/admin/setup/logs' )}</button>
                        {else}
                        <button type="submit" class="a4-btn a4-btn-small a4-btn-ghost" name="KnownButton" value="{$digest_g.id|wash}">{'Mark as known'|i18n( 'design/admin/setup/logs' )}</button>
                        {/if}
                    </form>
                </td>
            </tr>
        {/foreach}
        </tbody>
    </table>
    </div>
    {else}
    <p class="a4-meta">{'No error or warning in this window.'|i18n( 'design/admin/setup/logs' )}</p>
    {/if}
</section>
{/foreach}

</div></div></div>
</div>
