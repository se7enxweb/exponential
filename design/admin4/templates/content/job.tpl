{* The progress page of a content job (content/job/<id>): a subtree remove or copy running in the background.
   The page polls content/job/<id>?json=1 every two seconds and updates the bar, the counts, the message and the
   log; when the job finishes it opens the result, when it fails or is cancelled it reloads to show the buttons.
   It stops with a message when the job is gone or the session has ended (page/content_job.js). *}
{def $state_names = hash( 'queued',    'Waiting'|i18n( 'design/admin/content/job' ),
                          'running',   'Running'|i18n( 'design/admin/content/job' ),
                          'done',      'Done'|i18n( 'design/admin/content/job' ),
                          'failed',    'Failed'|i18n( 'design/admin/content/job' ),
                          'cancelled', 'Cancelled'|i18n( 'design/admin/content/job' ) )
     $finished = or( eq( $job.state, 'done' ), eq( $job.state, 'failed' ), eq( $job.state, 'cancelled' ) )}
<div class="context-block a4-page a4-content-job" id="exp-contentjob"
     data-status-url={concat( 'content/job/', $job.id, '?json=1' )|ezurl}
     data-state="{$job.state|wash}"{if $job.can_resume} data-can-resume="1"{/if}
     data-started="{$job.started|wash}"
     data-text-counts="{'%done of %total nodes'|i18n( 'design/admin/content/job' )|wash}"
     data-text-elapsed="{'%seconds s'|i18n( 'design/admin/content/job' )|wash}"
     data-text-opening="{'Opening the result in %seconds s'|i18n( 'design/admin/content/job' )|wash}"
     data-text-stopping="{'Stopping after the current batch'|i18n( 'design/admin/content/job' )|wash}"
     data-text-waiting="{'Waiting for the status...'|i18n( 'design/admin/content/job' )|wash}"
     data-text-gone="{'This job no longer exists or you may not see it. The status is not updated any more.'|i18n( 'design/admin/content/job' )|wash}"
     data-text-signed-out="{'You are no longer signed in. Sign in again and reload this page to follow the job.'|i18n( 'design/admin/content/job' )|wash}"
     data-text-error="{'The status could not be shown. Reload the page to follow the job.'|i18n( 'design/admin/content/job' )|wash}"
     data-states="{concat( $state_names.queued, '|', $state_names.running, '|', $state_names.done, '|', $state_names.failed, '|', $state_names.cancelled )|wash}">

{* DESIGN: Header START *}<div class="box-header"><div class="box-ml">

<h1 class="context-title">{$job.title|wash}</h1>

{* DESIGN: Mainline *}<div class="header-mainline"></div>

{* DESIGN: Header END *}</div></div>

{* DESIGN: Content START *}<div class="box-ml"><div class="box-mr"><div class="box-content">

{if $action_error}
<div class="message-error a4-content-job-action-error" role="alert"><h2>{$action_error|wash}</h2></div>
{/if}

<div class="block">
    <div class="a4-content-job-head">
        <span class="a4-badge a4-content-job-state {$job.state|wash}">{$state_names[$job.state]|wash}</span>
        <span class="a4-content-job-counts">{'%done of %total nodes'|i18n( 'design/admin/content/job',, hash( '%done', $job.done, '%total', $job.total ) )}</span>
        <span class="a4-content-job-elapsed"></span>
    </div>
    <div class="a4-meter a4-content-job-bar {$job.state|wash}" role="progressbar" aria-valuemin="0" aria-valuemax="100" aria-valuenow="{$job.percent}" aria-label="{'Progress'|i18n( 'design/admin/content/job' )|wash}"><span class="a4-pct-{$job.percent|round}"></span></div>
    <p class="a4-content-job-message" aria-live="polite">{if and( $job.cancel_requested, $finished|not )}{'Stopping after the current batch'|i18n( 'design/admin/content/job' )}{else}{$job.message|wash}{/if}</p>
</div>

<div class="message-feedback a4-content-job-done" role="status"{if ne( $job.state, 'done' )} hidden{/if}>
    <h2>{$job.done_text|wash}</h2>
    <p><a class="a4-content-job-open" href={$job.redirect_url|ezurl}>{$job.open_text|wash}</a> <span class="a4-content-job-auto"></span></p>
</div>

{if $job.warnings|count}
<div class="message-warning a4-content-job-warnings" role="status">
    <h2>{'Warnings'|i18n( 'design/admin/content/job' )}</h2>
    <ul>{foreach $job.warnings as $warning}<li>{$warning|wash}</li>{/foreach}</ul>
</div>
{/if}

{if eq( $job.state, 'failed' )}
<div class="message-error a4-content-job-error" role="alert">
    <h2>{'The job stopped with an error.'|i18n( 'design/admin/content/job' )}</h2>
    <pre>{$job.error|wash}</pre>
    {if $job.error_node_id}<p><a href={concat( 'content/view/full/', $job.error_node_id )|ezurl}>{'Open the node that failed'|i18n( 'design/admin/content/job' )}</a></p>{/if}
    <p>{'Everything up to the last finished batch is kept. Resume continues from there; nothing is done twice.'|i18n( 'design/admin/content/job' )}</p>
</div>
{/if}

{if eq( $job.state, 'cancelled' )}
<div class="message-warning" role="status">
    {if eq( $job.type, 'copy' )}
        <h2>{'The copy was cancelled.'|i18n( 'design/admin/content/job' )}</h2>
        {if $job.partial_node_id}
        <p>{'The part copied so far is kept under %target.'|i18n( 'design/admin/content/job',, hash( '%target', $job.target_name ) )|wash}
           <a href={concat( 'content/view/full/', $job.partial_node_id )|ezurl}>{'Open the partial copy'|i18n( 'design/admin/content/job' )}</a></p>
        <p>{'Remove the partial copy starts a job that deletes it. To copy the subtree, start the copy again.'|i18n( 'design/admin/content/job' )}</p>
        {else}
        <p>{if gt( $job.done, 0 )}{'The partial copy is no longer there (it has been removed).'|i18n( 'design/admin/content/job' )}{else}{'Nothing had been copied yet.'|i18n( 'design/admin/content/job' )}{/if}</p>
        {/if}
    {else}
        <h2>{'The removal was cancelled.'|i18n( 'design/admin/content/job' )}</h2>
        <p>{'What was not removed yet is still in place. Remove it again to remove the rest.'|i18n( 'design/admin/content/job' )}</p>
    {/if}
</div>
{/if}

<div class="a4-content-job-groups">
{foreach $job.details as $group}
<section class="a4-content-job-group" data-group="{$group.id|wash}">
<h2>{$group.title|wash}</h2>
<table class="list a4-content-job-details" cellspacing="0">
{foreach $group.rows as $row}
<tr data-key="{$row.key|wash}"><th scope="row">{$row.label|wash}</th>
    <td{if eq( $row.key, 'batch' )} class="a4-content-job-batch"{/if}>{if $row.url}<a href={$row.url|ezurl}>{$row.text|wash}</a>{elseif $row.mono}<code>{$row.text|wash}</code>{else}{$row.text|wash}{/if}</td></tr>
{/foreach}
</table>
</section>
{/foreach}
</div>
<p class="a4-content-job-jobid">{'Job'|i18n( 'design/admin/content/job' )} <code>{$job.id|wash}</code></p>
{* the module name in a variable: a server process started before the audit classes existed (Velocity) then gets no audit link instead of an error *}
{def $cj_audit_module = 'audit'}{if fetch( $cj_audit_module, 'can_read', hash( 'channel', 'content' ) )}<p class="a4-content-job-audit"><a href={concat( 'audit/console/(job)/', $job.id )|ezurl}>{'Audit trail of this job'|i18n( 'design/admin/content/job' )}</a></p>{/if}{undef $cj_audit_module}

<h2>{'Log'|i18n( 'design/admin/content/job' )} <a class="a4-content-job-download" href={concat( 'content/job/', $job.id, '?log=1' )|ezurl}>{'Download the whole log'|i18n( 'design/admin/content/job' )}</a></h2>
<pre class="a4-pre a4-content-job-log" aria-live="off">{$job.log|wash}</pre>

{* DESIGN: Content END *}</div></div></div>

<div class="controlbar">
{* DESIGN: Control bar START *}<div class="box-bc"><div class="box-ml">
<form method="post" action={concat( 'content/job/', $job.id )|ezurl}>
<div class="block">
    {if $job.can_cancel}
        <input class="button" type="submit" name="CancelJobButton" value="{'Cancel'|i18n( 'design/admin/content/job' )}" title="{'Stop after the current batch. What is done so far is kept.'|i18n( 'design/admin/content/job' )|wash}" />
    {/if}
    {if $job.can_resume}
        <input class="defaultbutton" type="submit" name="ResumeJobButton" value="{'Resume'|i18n( 'design/admin/content/job' )}" title="{'Continue from the last finished batch.'|i18n( 'design/admin/content/job' )|wash}" />
    {/if}
    {if $job.can_remove_partial}
        <input class="button" type="submit" name="RemovePartialCopyButton" value="{'Remove the partial copy'|i18n( 'design/admin/content/job' )}" title="{'Start a job that removes what the cancelled copy created.'|i18n( 'design/admin/content/job' )|wash}" />
    {/if}
    <a class="button" href={'content/jobs'|ezurl}>{'All jobs'|i18n( 'design/admin/content/job' )}</a>
</div>
</form>
{* DESIGN: Control bar END *}</div></div>
</div>

</div>

{if or( $finished|not, $job.just_finished )}
{ezscript( array( 'page/content_job.js' ) )}
{/if}
