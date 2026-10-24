{* The dashboard block "cronjobs" (dashboard.ini [DashboardBlock_cronjobs], shown only with setup/managecronjobs): when
   each cronjob part the cron job runs last started (var/log/cronjob-lastrun.json, written by runcronjobs.php), and a
   warning when no part has run yet or a part has not run for longer than its schedule allows
   (doc/eng-us/bc/6.0/installer-crontab.md). Drawn with the administration's shared components (components.css):
   no CSS in the template. *}
{* The module name is a variable, so the fetch is not compiled into a direct class call: a server process started
   before the class existed (a Velocity worker before its restart) gets an empty block, not an error. *}
{def $cron_module = 'setup'
     $cron = fetch( $cron_module, 'cronjob_status' )}
{if $cron|count}
<div>
<h2>{'Cronjobs'|i18n( 'design/admin/dashboard/cronjobs' )}</h2>

{if eq( $cron.overall, 'never' )}
<p class="a4-notice is-bad" role="alert">{'No cronjob part has run yet: the job queue, the imports, the notifications and the search index wait. Run php bin/php/installcrontab.php --add, or add the lines of --show to the crontab.'|i18n( 'design/admin/dashboard/cronjobs' )}</p>
{elseif eq( $cron.overall, 'late' )}
<p class="a4-notice is-warn" role="alert">{'A cronjob part has not run for longer than its schedule allows: check the crontab (php bin/php/installcrontab.php --show) and the logs var/log/cronjob-*.log.'|i18n( 'design/admin/dashboard/cronjobs' )}</p>
{/if}

{if $cron.parts|count}
<table class="list" cellpadding="0" cellspacing="0" border="0">
    <tr>
        <th>{'Part'|i18n( 'design/admin/dashboard/cronjobs' )}</th>
        <th>{'Schedule'|i18n( 'design/admin/dashboard/cronjobs' )}</th>
        <th>{'Last started'|i18n( 'design/admin/dashboard/cronjobs' )}</th>
        <th>{'State'|i18n( 'design/admin/dashboard/cronjobs' )}</th>
    </tr>
    {foreach $cron.parts as $p sequence array( 'bglight', 'bgdark' ) as $style}
    <tr class="{$style}">
        <td>{$p.part|wash}{if $p.siteaccess} <span class="a4-nowrap">(-s {$p.siteaccess|wash})</span>{/if}</td>
        <td class="a4-nowrap">{$p.schedule|wash}</td>
        <td class="a4-nowrap">{if $p.started}{$p.started|l10n( 'shortdatetime' )}{else}{'never'|i18n( 'design/admin/dashboard/cronjobs' )}{/if}</td>
        <td><span class="a4-badge {if eq( $p.state, 'ok' )}is-ok{elseif eq( $p.state, 'late' )}is-warn{else}is-bad{/if}">{if eq( $p.state, 'ok' )}{'on time'|i18n( 'design/admin/dashboard/cronjobs' )}{elseif eq( $p.state, 'late' )}{'late'|i18n( 'design/admin/dashboard/cronjobs' )}{else}{'never ran'|i18n( 'design/admin/dashboard/cronjobs' )}{/if}</span></td>
    </tr>
    {/foreach}
</table>
{else}
<p>{'The installation has no cronjob part for the cron job to run.'|i18n( 'design/admin/dashboard/cronjobs' )}</p>
{/if}
<p class="a4-card-body"><a href={'setup/cronjobs'|ezurl}>{'Setup > Cronjobs'|i18n( 'design/admin/dashboard/cronjobs' )}</a></p>
</div>
{/if}
{undef $cron_module $cron}
