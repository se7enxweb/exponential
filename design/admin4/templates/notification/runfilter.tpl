{* The old "run the notification filter" page: still at /notification/runfilter, now with the result of the run and a way to the status page. *}
{include uri='design:notification/parts/style.tpl'}
<div class="nf" id="exp-notify-runfilter">
    <div class="nf-head"><div>
        <h1>{'Notification'|i18n( 'design/admin/notification/runfilter' )}</h1>
        <p>{'Handles the events that wait for the notification cronjob. The notification status page shows what waits and what was sent.'|i18n( 'design/admin/notification/runfilter' )}</p>
    </div></div>

{if $filter_proccessed}
    <div class="nf-notice nf-notice-success" role="status"><p>[{currentdate()|l10n( shortdatetime )}] {'The notification filter processed all available notification events.'|i18n( 'design/admin/notification/runfilter' )}
    {if $run_result} {'%events events handled, %mails messages sent.'|i18n( 'design/admin/notification/runfilter',, hash( '%events', $run_result.events, '%mails', $run_result.mails ) )}{/if}</p></div>
{elseif and( $run_result, $run_result.result|ne( 'ok' ) )}
    <div class="nf-notice nf-notice-error" role="status"><p>{$run_result.error|wash}</p></div>
{/if}
{if $time_event_created}
    <div class="nf-notice nf-notice-success" role="status"><p>[{currentdate()|l10n( shortdatetime )}] {'The notification time event was spawned.'|i18n( 'design/admin/notification/runfilter' )}</p></div>
{/if}

    <form class="nf-card" method="post" action={'notification/runfilter'|ezurl}>
        <p class="nf-lead">{'The time event makes the digests that are due ready to be sent by the next run.'|i18n( 'design/admin/notification/runfilter' )}</p>
        <div class="nf-actions">
            <input class="nf-btn primary" type="submit" name="RunFilterButton" value="{'Run notification filter'|i18n( 'design/admin/notification/runfilter' )}" />
            <input class="nf-btn" type="submit" name="SpawnTimeEventButton" value="{'Spawn time event'|i18n( 'design/admin/notification/runfilter' )}" />
            <a class="nf-btn" href={'notification/status'|ezurl}>{'Notification status'|i18n( 'design/admin/notification/runfilter' )}</a>
        </div>
    </form>
</div>
