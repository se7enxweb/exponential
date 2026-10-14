{* The old "run the notification filter" page: still at /notification/runfilter, now with the result of the run and a way to the status page. *}
<div class="a4-page" id="exp-notify-runfilter">
    <div class="a4-notification-head"><div>
        <h1>{'Notification'|i18n( 'design/admin/notification/runfilter' )}</h1>
        <p>{'Handles the events that wait for the notification cronjob. The notification status page shows what waits and what was sent.'|i18n( 'design/admin/notification/runfilter' )}</p>
    </div></div>

{if $filter_proccessed}
    <div class="a4-notice is-ok" role="status"><p>[{currentdate()|l10n( shortdatetime )}] {'The notification filter processed all available notification events.'|i18n( 'design/admin/notification/runfilter' )}
    {if $run_result} {'%events events handled, %mails messages sent.'|i18n( 'design/admin/notification/runfilter',, hash( '%events', $run_result.events, '%mails', $run_result.mails ) )}{/if}</p></div>
{elseif and( $run_result, $run_result.result|ne( 'ok' ) )}
    <div class="a4-notice is-bad" role="status"><p>{$run_result.error|wash}</p></div>
{/if}
{if $time_event_created}
    <div class="a4-notice is-ok" role="status"><p>[{currentdate()|l10n( shortdatetime )}] {'The notification time event was spawned.'|i18n( 'design/admin/notification/runfilter' )}</p></div>
{/if}

    <form class="a4-card a4-stack" method="post" action={'notification/runfilter'|ezurl}>
        <p class="a4-help">{'The time event makes the digests that are due ready to be sent by the next run.'|i18n( 'design/admin/notification/runfilter' )}</p>
        <div class="a4-actions">
            <input class="a4-btn a4-btn-primary" type="submit" name="RunFilterButton" value="{'Run notification filter'|i18n( 'design/admin/notification/runfilter' )}" />
            <input class="a4-btn" type="submit" name="SpawnTimeEventButton" value="{'Spawn time event'|i18n( 'design/admin/notification/runfilter' )}" />
            <a class="a4-btn" href={'notification/status'|ezurl}>{'Notification status'|i18n( 'design/admin/notification/runfilter' )}</a>
        </div>
    </form>
</div>
