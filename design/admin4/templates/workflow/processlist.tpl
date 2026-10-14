{* The workflow process list.

   A workflow process is a workflow that has started for one piece of content and has not finished: it waits for an
   approver, for the workflow cronjob, for the user, or it has stopped. The page leads with what is waiting and
   whether the cronjob that resumes it runs, then an overview, the controls, and one card per process grouped by
   the trigger that started it, each saying in words what it waits for. A process can be cancelled, in two steps:
   the first button only shows what cancelling would do.

   The same file is in design/admin and design/admin4, so every administration design resolves it. It is drawn
   with the shared components (stylesheets/components.css) and its own .a4-workflow-* rules in
   stylesheets/modules/workflow.css. Everything works without javascript: the filters are links, the cancel buttons submit the form, the
   folding sections are <details>. The script below only adds the search and "select all".
   User guide: doc/guides/workflows.md *}


{def $wfp_ctx = 'design/admin/workflow/processlist'
     $wfp_base = 'workflow/processlist'
     $wfp_self = cond( eq( $wfp_status_filter, 'waiting' ), 'workflow/processlist', concat( 'workflow/processlist/(status)/', $wfp_status_filter ) )
     $wfp_listed = 0}
{foreach $wfp_groups as $wfp_group}{set $wfp_listed = sum( $wfp_listed, $wfp_group.count )}{/foreach}

<div class="context-block a4-workflow-processes a4-page" data-shown="{'Showing %shown of %total processes on this page'|i18n( $wfp_ctx )|wash}">

<div class="box-header"><div class="box-ml">
<h1 class="context-title">{'Workflow processes'|i18n( $wfp_ctx )}</h1>
</div></div>

<div class="box-bc"><div class="box-ml"><div class="box-content">

<p class="a4-intro">{'A workflow process is one run of a workflow for one piece of content that has not finished yet: it waits for an approver, for the workflow cronjob or for the user, or it has stopped. This page shows each one, what it is waiting for, and lets you cancel one that is stuck.'|i18n( $wfp_ctx )}</p>

{foreach $wfp_feedback as $wfp_message}
<div class="a4-notice {if $wfp_message.ok}is-ok{else}is-bad{/if}" role="{if $wfp_message.ok}status{else}alert{/if}">{$wfp_message.message|wash}</div>
{/foreach}

{if $wfp_confirm_list}
{* ---- The confirmation step: nothing has been cancelled yet. ---- *}
<form method="post" action={$wfp_self|ezurl}>
<section class="a4-card is-bad a4-section" aria-labelledby="wfp-confirm-title">
    <h2 class="a4-h2" id="wfp-confirm-title">{if eq( $wfp_confirm_list|count, 1 )}{'Cancel this workflow process?'|i18n( $wfp_ctx )}{else}{'Cancel these %count workflow processes?'|i18n( $wfp_ctx,, hash( '%count', $wfp_confirm_list|count ) )}{/if}</h2>
    <p class="a4-muted">{'Nothing has been changed yet.'|i18n( $wfp_ctx )}</p>
    <ul class="a4-workflow-confirm-list">
    {foreach $wfp_confirm_list as $wfp_p}
        <li>
            <input type="hidden" name="ProcessIDList[]" value="{$wfp_p.id}" />
            <strong>{if $wfp_p.object.name|ne('')}{$wfp_p.object.name|wash}{else}{'Process %id'|i18n( $wfp_ctx,, hash( '%id', $wfp_p.id ) )}{/if}</strong>
            {if $wfp_p.object.version|gt(0)}<span class="a4-muted">&middot; {'version %version'|i18n( $wfp_ctx,, hash( '%version', $wfp_p.object.version ) )}{if $wfp_p.object.version_status_text|ne('')} ({$wfp_p.object.version_status_text|wash}){/if}</span>{/if}
            <br /><span class="a4-muted">{'Process %id'|i18n( $wfp_ctx,, hash( '%id', $wfp_p.id ) )} &middot; {$wfp_p.workflow.name|wash} &middot; {$wfp_p.status.label|wash}{if $wfp_p.user.name|ne('')} &middot; {'started by %user'|i18n( $wfp_ctx,, hash( '%user', $wfp_p.user.name|wash ) )}{/if}</span>
        </li>
    {/foreach}
    </ul>
    <h3 class="a4-h3">{'What cancelling does'|i18n( $wfp_ctx )}</h3>
    <ul class="a4-workflow-consequences">
        <li>{'The workflow stops where it is. Its remaining steps never run, and the operation it held back (publishing, for example) does not happen.'|i18n( $wfp_ctx )}</li>
        <li>{'A version that is waiting to be published becomes a draft again. The author finds it among their drafts and can publish it again, which starts the workflow anew.'|i18n( $wfp_ctx )}</li>
        <li>{"An approval request for it is closed in the approvers' collaboration inbox."|i18n( $wfp_ctx )}</li>
        <li>{'No content is deleted. The cancel cannot be undone.'|i18n( $wfp_ctx )}</li>
    </ul>
    <div class="a4-actions">
        <button type="submit" class="a4-btn a4-btn-danger" name="CancelProcessesButton" value="1">{if eq( $wfp_confirm_list|count, 1 )}{'Yes, cancel the process'|i18n( $wfp_ctx )}{else}{'Yes, cancel %count processes'|i18n( $wfp_ctx,, hash( '%count', $wfp_confirm_list|count ) )}{/if}</button>
        <button type="submit" class="a4-btn" name="KeepProcessesButton" value="1">{'No, keep them'|i18n( $wfp_ctx )}</button>
    </div>
</section>
</form>

{else}

<form method="post" action={$wfp_self|ezurl}>

{* ---- What is waiting now, and whether the cronjob that resumes it runs. ---- *}
<section class="a4-toolbar" aria-labelledby="wfp-status-title">
    <h2 class="a4-sr-only" id="wfp-status-title">{'Now'|i18n( $wfp_ctx )}</h2>
    <div class="a4-workflow-status">
        {if $wfp_summary.waiting|gt(0)}
        <span class="a4-status{if $wfp_summary.stuck} is-warn{/if}"><span class="a4-dot{if $wfp_summary.stuck|not} is-info{/if}" aria-hidden="true"></span>{'Processes waiting: %count'|i18n( $wfp_ctx,, hash( '%count', $wfp_summary.waiting ) )}</span>
        <span class="a4-meta">{'The oldest started %age ago (%time).'|i18n( $wfp_ctx,, hash( '%age', $wfp_summary.oldest_age, '%time', $wfp_summary.oldest|l10n( shortdatetime ) ) )}</span>
        {else}
        <span class="a4-status is-ok"><span class="a4-dot" aria-hidden="true"></span>{'Nothing is waiting'|i18n( $wfp_ctx )}</span>
        {/if}
        {if $wfp_cronjob.found}
        <span class="a4-meta">
            {'Resumed by the workflow cronjob (%script) in the %part part:'|i18n( $wfp_ctx,, hash( '%script', 'workflow.php', '%part', $wfp_cronjob.label|wash ) )}
            {if $wfp_cronjob.scheduled}
                <strong>{if $wfp_cronjob.schedule_text|ne('')}{$wfp_cronjob.schedule_text|wash}{else}{'scheduled'|i18n( $wfp_ctx )}{/if}</strong>{if $wfp_cronjob.next_run|gt(0)}, {'next run %time'|i18n( $wfp_ctx,, hash( '%time', $wfp_cronjob.next_run|l10n( shortdatetime ) ) )}{/if}.
            {else}
                <strong>{'not scheduled in the crontab'|i18n( $wfp_ctx )}</strong>.
            {/if}
        </span>
        {/if}
    </div>
    {if $wfp_cronjob.found}
    <div class="a4-actions">
        <a class="a4-btn" href="{'setup/cronjobs'|ezurl( 'no' )}#{$wfp_cronjob.anchor|wash}">{'Open the cronjob'|i18n( $wfp_ctx )}</a>
    </div>
    {/if}
</section>

{if $wfp_cronjob.found|not}
<div class="a4-notice is-bad" role="alert">{'No cronjob part runs workflow.php, so processes waiting for the workflow cronjob never move on. Add Scripts[]=workflow.php to a part in cronjob.ini, for example [CronjobPart-frequent].'|i18n( $wfp_ctx )}</div>
{elseif and( $wfp_cronjob.scheduled|not, $wfp_summary.cron|gt(0) )}
<div class="a4-notice is-warn" role="status">{'%count processes wait for the workflow cronjob, but the crontab does not run the %part part. They move on only when it runs: schedule it, or run it now from the cronjobs page.'|i18n( $wfp_ctx,, hash( '%count', $wfp_summary.cron, '%part', $wfp_cronjob.label|wash ) )} <a href="{'setup/cronjobs'|ezurl( 'no' )}#{$wfp_cronjob.anchor|wash}">{'Go to the cronjobs page'|i18n( $wfp_ctx )}</a></div>
{/if}

{* ---- The overview ---- *}
<section aria-labelledby="wfp-overview-title">
<h2 class="a4-sr-only" id="wfp-overview-title">{'Overview'|i18n( $wfp_ctx )}</h2>
<ul class="a4-figures">
    <li class="a4-figure"><strong>{$wfp_summary.waiting}</strong><span>{'Waiting'|i18n( $wfp_ctx )}</span></li>
    <li class="a4-figure"><strong>{$wfp_summary.cron}</strong><span>{'For the workflow cronjob'|i18n( $wfp_ctx )}</span></li>
    <li class="a4-figure"><strong>{$wfp_summary.person}</strong><span>{'For the user'|i18n( $wfp_ctx )}</span></li>
    <li class="a4-figure"><strong>{$wfp_summary.parent}</strong><span>{'For a parent workflow'|i18n( $wfp_ctx )}</span></li>
    <li class="a4-figure{if $wfp_summary.failed|gt(0)} is-attention{/if}"><strong>{$wfp_summary.failed}</strong><span>{'Failed'|i18n( $wfp_ctx )}</span></li>
    <li class="a4-figure{if $wfp_summary.stopped|gt( $wfp_summary.failed )} is-attention{/if}"><strong>{$wfp_summary.stopped}</strong><span>{'Stopped, will not move on'|i18n( $wfp_ctx )}</span></li>
    <li class="a4-figure a4-workflow-figure-wide">
        {if $wfp_summary.oldest|gt(0)}
        <strong>{'%age ago'|i18n( $wfp_ctx,, hash( '%age', $wfp_summary.oldest_age ) )}</strong>
        <span>{'Oldest waiting, since %time'|i18n( $wfp_ctx,, hash( '%time', $wfp_summary.oldest|l10n( shortdatetime ) ) )}</span>
        {else}
        <strong>&mdash;</strong>
        <span>{'Oldest waiting'|i18n( $wfp_ctx )}</span>
        {/if}
    </li>
</ul>
</section>

{* ---- The controls: which processes, a search, the page size, and cancelling several at once. ---- *}
<section aria-labelledby="wfp-controls-title">
<h2 class="a4-sr-only" id="wfp-controls-title">{'Filter and select'|i18n( $wfp_ctx )}</h2>
<div class="a4-filterbar">
    <nav class="a4-field a4-field-wide" aria-labelledby="wfp-show-label">
        <span class="a4-label" id="wfp-show-label">{'Show'|i18n( $wfp_ctx )}</span>
        <ul class="a4-tabs">
            <li><a href={$wfp_base|ezurl}{if eq( $wfp_status_filter, 'waiting' )} class="is-current" aria-current="page"{/if}>{'Waiting'|i18n( $wfp_ctx )} <span class="a4-count">({$wfp_summary.waiting})</span></a></li>
            <li><a href={concat( $wfp_base, '/(status)/stopped' )|ezurl}{if eq( $wfp_status_filter, 'stopped' )} class="is-current" aria-current="page"{/if}>{'Stopped'|i18n( $wfp_ctx )} <span class="a4-count">({$wfp_summary.stopped})</span></a></li>
            <li><a href={concat( $wfp_base, '/(status)/all' )|ezurl}{if eq( $wfp_status_filter, 'all' )} class="is-current" aria-current="page"{/if}>{'All'|i18n( $wfp_ctx )} <span class="a4-count">({$wfp_summary.total})</span></a></li>
        </ul>
    </nav>
    <div class="a4-field a4-workflow-js-only" hidden>
        <label for="wfp-search">{'Find a process'|i18n( $wfp_ctx )}</label>
        <input type="search" id="wfp-search" autocomplete="off" spellcheck="false" aria-controls="wfp-list" aria-describedby="wfp-filter-count" placeholder="{'Content, user, workflow or status'|i18n( $wfp_ctx )}" />
    </div>
    <div class="a4-field">
        <span class="a4-label" id="wfp-pagesize-label">{'Processes per page'|i18n( $wfp_ctx )}</span>
        {* The sizes come from admininterface.ini [PaginationSettings] and the preference stores the position in
           that list, so a site can offer the sizes its own editors want. *}
        <p class="a4-sizes" role="group" aria-labelledby="wfp-pagesize-label">
        {foreach $limit_choices as $limit_index => $limit_option}
            {if eq( $limit_index|inc, $limit_choice )}
            <span class="current" aria-current="true">{$limit_option}</span>
            {else}
            <a href={concat( '/user/preferences/set/admin_workflow_processlist_limit/', $limit_index|inc )|ezurl} title="{'Show %count items per page.'|i18n( 'design/admin/node/view/full',, hash( '%count', $limit_option ) )}">{$limit_option}</a>
            {/if}
        {/foreach}
        </p>
    </div>
    {if $wfp_listed|gt(0)}
    <div class="a4-workflow-selectbar">
        <label class="a4-check a4-workflow-js-only" hidden><input type="checkbox" id="wfp-select-all" aria-controls="wfp-list" /> {'Select all on this page'|i18n( $wfp_ctx )}</label>
        <button type="submit" class="a4-btn a4-btn-outline-danger" id="wfp-cancel-selected" name="ConfirmCancelSelectedButton" value="1">{'Cancel selected processes...'|i18n( $wfp_ctx )}</button>
        <span class="a4-muted">{'You confirm on the next page before anything happens.'|i18n( $wfp_ctx )}</span>
    </div>
    {/if}
    <p class="a4-filter-count a4-workflow-js-only" id="wfp-filter-count" aria-live="polite" hidden></p>
</div>
</section>

{* ---- The processes, by the trigger that started them ---- *}
<div id="wfp-list">
{if $wfp_groups}
{foreach $wfp_groups as $wfp_gi => $wfp_group}
<section class="a4-workflow-group" aria-labelledby="wfp-group-{$wfp_gi}-title" data-group="1">
    <div class="a4-workflow-group-head">
        <h2 class="a4-h2" id="wfp-group-{$wfp_gi}-title">{$wfp_group.label|wash}</h2>
        {if $wfp_group.key|ne('')}<code class="a4-workflow-group-key" title="{'The trigger: module, operation and when'|i18n( $wfp_ctx )}">{$wfp_group.key|wash}</code>{/if}
        <span class="a4-badge">{'%count processes'|i18n( $wfp_ctx,, hash( '%count', $wfp_group.count ) )}</span>
    </div>
    {if $wfp_group.key|eq('')}<p class="a4-muted a4-workflow-group-note">{'These processes no longer say which trigger started them: their memento is gone, or the trigger was removed since. The workflow cronjob cannot resume them by their operation.'|i18n( $wfp_ctx )}</p>{/if}
    <ul class="a4-cards">
    {foreach $wfp_group.processes as $wfp_p}
    {def $wfp_title = cond( $wfp_p.object.name|ne(''), $wfp_p.object.name, 'Process %id'|i18n( $wfp_ctx,, hash( '%id', $wfp_p.id ) ) )}
    <li class="a4-card{if eq( $wfp_p.status.tone, 'bad' )} is-bad{elseif or( eq( $wfp_p.status.tone, 'warn' ), $wfp_p.stuck )} is-warn{/if}" id="wfp-process-{$wfp_p.id}" data-search="{$wfp_p.search|wash}">
        <div class="a4-card-head">
            <div class="a4-card-title">
                <label class="a4-check"><input type="checkbox" name="SelectedProcessIDList[]" value="{$wfp_p.id}" /><span class="a4-sr-only">{'Select process %id (%name)'|i18n( $wfp_ctx,, hash( '%id', $wfp_p.id, '%name', $wfp_title|wash ) )}</span></label>
                <div class="a4-workflow-card-name">
                    <h3 class="a4-h3">{if and( $wfp_p.object.exists, $wfp_p.object.version|gt(0) )}<a href={concat( 'content/versionview/', $wfp_p.object.id, '/', $wfp_p.object.version )|ezurl} title="{'View the version this process runs for'|i18n( $wfp_ctx )}">{$wfp_title|wash}</a>{else}{$wfp_title|wash}{/if}</h3>
                    <ul class="a4-badges">
                        <li class="a4-badge is-{$wfp_p.status.tone}">{$wfp_p.status.label|wash}</li>
                        {if $wfp_p.stuck}<li class="a4-badge is-warn">{'No change for %age'|i18n( $wfp_ctx,, hash( '%age', $wfp_p.idle ) )}</li>{/if}
                        {if $wfp_p.collaboration_id|gt(0)}<li class="a4-badge is-info">{'Approval'|i18n( $wfp_ctx )}</li>{/if}
                    </ul>
                </div>
            </div>
            <div class="a4-actions">
                <button type="submit" class="a4-btn a4-btn-small a4-btn-outline-danger" name="ConfirmCancelProcessButton" value="{$wfp_p.id}"
                        aria-label="{'Cancel process %id (%name)...'|i18n( $wfp_ctx,, hash( '%id', $wfp_p.id, '%name', $wfp_title|wash ) )}">{'Cancel...'|i18n( $wfp_ctx )}</button>
            </div>
        </div>

        <p class="a4-workflow-waiting">{$wfp_p.waiting_for|wash}{if $wfp_p.collaboration_id|gt(0)} <a href={concat( 'collaboration/item/full/', $wfp_p.collaboration_id )|ezurl}>{'Open the approval request'|i18n( $wfp_ctx )}</a>{/if}</p>

        <dl class="a4-facts">
            <div>
                <dt>{'Content'|i18n( $wfp_ctx )}</dt>
                <dd>{if $wfp_p.object.id|gt(0)}{if $wfp_p.object.name|ne('')}{$wfp_p.object.name|wash}{else}{'Object %id'|i18n( $wfp_ctx,, hash( '%id', $wfp_p.object.id ) )}{/if}{if $wfp_p.object.version|gt(0)}<br /><span class="a4-muted">{'version %version'|i18n( $wfp_ctx,, hash( '%version', $wfp_p.object.version ) )}{if $wfp_p.object.version_status_text|ne('')}, {$wfp_p.object.version_status_text|wash}{/if}</span>{/if}{if $wfp_p.object.exists|not}<br /><span class="a4-muted">{'The object no longer exists.'|i18n( $wfp_ctx )}</span>{/if}{else}<span class="a4-muted">{'None'|i18n( $wfp_ctx )}</span>{/if}</dd>
            </div>
            <div>
                <dt>{'Started by'|i18n( $wfp_ctx )}</dt>
                <dd>{if $wfp_p.user.name|ne('')}{if $wfp_p.user.node_url|ne('')}<a href={$wfp_p.user.node_url|ezurl}>{$wfp_p.user.name|wash}</a>{else}{$wfp_p.user.name|wash}{/if}{elseif $wfp_p.user.id|gt(0)}{'User %id'|i18n( $wfp_ctx,, hash( '%id', $wfp_p.user.id ) )}{else}<span class="a4-muted">{'Unknown'|i18n( $wfp_ctx )}</span>{/if}</dd>
            </div>
            <div>
                <dt>{'Workflow'|i18n( $wfp_ctx )}</dt>
                <dd>{if $wfp_p.workflow.name|ne('')}<a href={concat( $module.functions.view.uri, '/', $wfp_p.workflow.id )|ezurl}>{$wfp_p.workflow.name|wash}</a>{else}{'Workflow %id (missing)'|i18n( $wfp_ctx,, hash( '%id', $wfp_p.workflow.id ) )}{/if}</dd>
            </div>
            <div>
                <dt>{'Current step'|i18n( $wfp_ctx )}</dt>
                <dd>{if $wfp_p.current_event.type_name|ne('')}{if $wfp_p.workflow.event_count|gt(0)}{'Step %position of %count'|i18n( $wfp_ctx,, hash( '%position', $wfp_p.current_event.position, '%count', $wfp_p.workflow.event_count ) )}: {/if}{$wfp_p.current_event.type_name|wash}{if $wfp_p.current_event.description|ne('')} <span class="a4-muted">&ldquo;{$wfp_p.current_event.description|wash}&rdquo;</span>{/if}{else}<span class="a4-muted">{'None'|i18n( $wfp_ctx )}</span>{/if}</dd>
            </div>
            <div>
                <dt>{'Started'|i18n( $wfp_ctx )}</dt>
                <dd>{$wfp_p.created|l10n( shortdatetime )}<br /><span class="a4-muted">{'%age ago'|i18n( $wfp_ctx,, hash( '%age', $wfp_p.age ) )}</span></dd>
            </div>
            <div>
                <dt>{'Last change'|i18n( $wfp_ctx )}</dt>
                <dd>{if $wfp_p.modified|gt(0)}{$wfp_p.modified|l10n( shortdatetime )}{else}{$wfp_p.created|l10n( shortdatetime )}{/if}<br /><span class="a4-muted">{'%age ago'|i18n( $wfp_ctx,, hash( '%age', $wfp_p.idle ) )}</span></dd>
            </div>
        </dl>

        <details class="a4-workflow-tech">
            <summary>{'Technical details'|i18n( $wfp_ctx )}</summary>
            <dl class="a4-facts">
                <div><dt>{'Process ID'|i18n( $wfp_ctx )}</dt><dd>{$wfp_p.id}</dd></div>
                <div><dt>{'Process status'|i18n( $wfp_ctx )}</dt><dd>({$wfp_p.status.code}) {$wfp_p.status.name|wash}</dd></div>
                <div><dt>{'Current event'|i18n( $wfp_ctx )}</dt><dd>{if $wfp_p.current_event.id|gt(0)}{'status : (%event_status)'|i18n( $wfp_ctx,, hash( '%event_status', $wfp_p.current_event.status.code ) )} {$wfp_p.current_event.status.name|wash}<br />{'event type :'|i18n( $wfp_ctx )} {$wfp_p.current_event.type|wash}{if $wfp_p.current_event.information|ne('')}<br />{'information :'|i18n( $wfp_ctx )} {$wfp_p.current_event.information|wash}{/if}{else}-{/if}</dd></div>
                <div><dt>{'Last event'|i18n( $wfp_ctx )}</dt><dd>{if $wfp_p.last_event.id|gt(0)}{'status : (%last_event_status)'|i18n( $wfp_ctx,, hash( '%last_event_status', $wfp_p.last_event.status.code ) )} {$wfp_p.last_event.status.name|wash}<br />{'event type :'|i18n( $wfp_ctx )} {$wfp_p.last_event.type|wash}{if $wfp_p.last_event.description|ne('')}<br />{'description :'|i18n( $wfp_ctx )} {$wfp_p.last_event.description|wash}{/if}{else}-{/if}</dd></div>
                <div><dt>{'Memento key'|i18n( $wfp_ctx )}</dt><dd>{if $wfp_p.memento_key|ne('')}<code>{$wfp_p.memento_key|wash}</code>{else}-{/if}</dd></div>
            </dl>
        </details>
    </li>
    {undef $wfp_title}
    {/foreach}
    </ul>
</section>
{/foreach}
<p class="a4-empty" id="wfp-no-match" hidden><strong>{'No process matches the search.'|i18n( $wfp_ctx )}</strong>{'Only the processes on this page are searched.'|i18n( $wfp_ctx )}</p>
{else}
<div class="a4-empty">
    {if eq( $wfp_status_filter, 'waiting' )}
    <strong>{'No processes are waiting.'|i18n( $wfp_ctx )}</strong>
    {'That is normal: a process exists only while a workflow waits for someone or something, and is removed when it finishes.'|i18n( $wfp_ctx )}
    {if $wfp_summary.stopped|gt(0)}<br /><a href={concat( $wfp_base, '/(status)/stopped' )|ezurl}>{'Show the %count stopped processes'|i18n( $wfp_ctx,, hash( '%count', $wfp_summary.stopped ) )}</a>{/if}
    {elseif eq( $wfp_status_filter, 'stopped' )}
    <strong>{'No stopped processes.'|i18n( $wfp_ctx )}</strong>
    {'Nothing has failed or been left behind.'|i18n( $wfp_ctx )}
    {else}
    <strong>{'There are no workflow processes.'|i18n( $wfp_ctx )}</strong>
    {/if}
</div>
{/if}
</div>

<div class="a4-pager">
{include name=navigator
         uri='design:navigator/google.tpl'
         page_uri='/workflow/processlist'
         item_count=$list_count
         view_parameters=$view_parameters
         item_limit=$page_limit}
</div>

</form>

{* ---- What the statuses mean ---- *}
<details class="a4-fold" id="wfp-legend">
    <summary><h2 class="a4-h2">{'What the statuses mean'|i18n( $wfp_ctx )}</h2></summary>
    <div class="a4-fold-body">
        <dl class="a4-workflow-legend">
            <div><dt><span class="a4-badge is-info">{'Waiting for the workflow cronjob'|i18n( $wfp_ctx )}</span></dt><dd>{'A step asked to be run again later, for example an approval that has not been decided yet. The workflow cronjob runs it each time it comes round.'|i18n( $wfp_ctx )}</dd></div>
            <div><dt><span class="a4-badge is-warn">{'Waiting for the user'|i18n( $wfp_ctx )}</span></dt><dd>{'A step showed the user a page or sent them elsewhere and waits for them. If they never come back, the process stays; cancel it when you are sure.'|i18n( $wfp_ctx )}</dd></div>
            <div><dt><span class="a4-badge is-info">{'Waiting for its parent workflow'|i18n( $wfp_ctx )}</span></dt><dd>{'Started by a multiplexer step of another workflow; it moves on with that one.'|i18n( $wfp_ctx )}</dd></div>
            <div><dt><span class="a4-badge is-bad">{'Failed'|i18n( $wfp_ctx )}</span></dt><dd>{'A step rejected the content or went wrong. Nothing runs it again; cancel it to tidy up and to give a held-back version back to its author.'|i18n( $wfp_ctx )}</dd></div>
            <div><dt><span class="a4-badge is-warn">{'Marked as running'|i18n( $wfp_ctx )}</span></dt><dd>{'Only true while a request runs it. Hours later it means the run was cut off.'|i18n( $wfp_ctx )}</dd></div>
            <div><dt><span class="a4-badge is-muted">{'Cancelled'|i18n( $wfp_ctx )}, {'Reset'|i18n( $wfp_ctx )}, {'Done'|i18n( $wfp_ctx )}</span></dt><dd>{'Finished one way or another; such a process is normally removed at once or by the next cronjob run.'|i18n( $wfp_ctx )}</dd></div>
        </dl>
    </div>
</details>

{/if}

</div></div></div>

</div>

<script type="text/javascript">
{literal}
(function () {
    var root = document.querySelector( '.a4-workflow-processes' );
    if ( !root ) return;
    var each = function ( selector, fn ) { Array.prototype.forEach.call( root.querySelectorAll( selector ), fn ); };
    each( '.a4-workflow-js-only', function ( el ) { el.hidden = false; } );

    var boxes = function () { return root.querySelectorAll( 'input[name="SelectedProcessIDList[]"]' ); };
    var all = root.querySelector( '#wfp-select-all' );
    var cancelSelected = root.querySelector( '#wfp-cancel-selected' );
    var label = cancelSelected ? cancelSelected.textContent : '';
    var update = function () {
        var list = boxes(), checked = 0, visible = 0;
        Array.prototype.forEach.call( list, function ( box ) {
            if ( !box.closest( '.a4-card[data-search]' ).hidden ) { visible++; if ( box.checked ) checked++; }
        } );
        if ( all ) { all.checked = visible > 0 && checked === visible; all.indeterminate = checked > 0 && checked < visible; }
        if ( cancelSelected ) cancelSelected.textContent = checked > 0 ? label.replace( /\.\.\.$/, '' ) + ' (' + checked + ')...' : label;
    };
    if ( all ) all.addEventListener( 'change', function () {
        Array.prototype.forEach.call( boxes(), function ( box ) { if ( !box.closest( '.a4-card[data-search]' ).hidden ) box.checked = all.checked; } );
        update();
    } );
    root.addEventListener( 'change', function ( e ) { if ( e.target && e.target.name === 'SelectedProcessIDList[]' ) update(); } );

    var search = root.querySelector( '#wfp-search' );
    var count = root.querySelector( '#wfp-filter-count' );
    var noMatch = root.querySelector( '#wfp-no-match' );
    if ( search ) search.addEventListener( 'input', function () {
        var words = search.value.toLowerCase().split( /\s+/ ).filter( Boolean ), shown = 0, total = 0;
        each( '.a4-card[data-search]', function ( card ) {
            var text = card.getAttribute( 'data-search' ) || '';
            var hit = words.every( function ( w ) { return text.indexOf( w ) !== -1; } );
            card.hidden = !hit; total++; if ( hit ) shown++;
        } );
        each( '.a4-workflow-group', function ( group ) { group.hidden = !group.querySelector( '.a4-card[data-search]:not([hidden])' ); } );
        if ( noMatch ) noMatch.hidden = shown > 0;
        if ( count ) count.textContent = words.length ? root.getAttribute( 'data-shown' ).replace( '%shown', shown ).replace( '%total', total ) : '';
        update();
    } );
})();
{/literal}
</script>
