{* The migration/history view: the runs, newest first, and the runs started in the administration.
   Variables: runs (expMigrationStatus::runSummary() with kind, started_ts, finished_ts, resumable), state (the filter,
   '' for all), states, counts (state => runs; all: every run), ready (the table exists), problem, jobs (expMigrationJob::all()),
   running, can_run, can_rollback, view. *}
<div class="a4-page a4-setup-migration" id="exp-migration-history">

<div class="a4-pagehead">
    <div class="a4-pagehead-main">
        <h1 class="a4-pagehead-title context-title">{'Migration history'|i18n( 'design/admin/migration' )}</h1>
        <p class="a4-pagehead-sub">{'Every run with its user, environment, captions and what was committed, tagged and pushed. A failed or interrupted run can be resumed; a run with a caption taken before it can be taken back.'|i18n( 'design/admin/migration' )}</p>
    </div>
</div>

{include uri='design:migration/parts/tabs.tpl' view=$view can_run=$can_run can_rollback=$can_rollback}
{include uri='design:migration/parts/running.tpl' job=$running lock_text=''}

{if $problem}<div class="a4-notice is-bad" role="alert"><p>{'The runs could not be read: %problem'|i18n( 'design/admin/migration',, hash( '%problem', $problem ) )|wash}</p></div>{/if}
{if $ready|not}<div class="a4-notice is-warn" role="status"><p>{'The tables of the migrations do not exist yet: ./console exp:upgrade --apply makes them.'|i18n( 'design/admin/migration' )}</p></div>{/if}

<div class="a4-section">
    <span id="migration-state-label"><strong>{'Show'|i18n( 'design/admin/migration' )}</strong></span>
    <ul class="a4-tabs" aria-labelledby="migration-state-label">
        <li>{if eq( $state, '' )}<span class="current" aria-current="true">{'All'|i18n( 'design/admin/migration' )} ({$counts.all|wash})</span>{else}<a href={'migration/history'|ezurl}>{'All'|i18n( 'design/admin/migration' )} ({$counts.all|wash})</a>{/if}</li>
        {foreach $states as $name}
        <li>{if eq( $state, $name )}<span class="current" aria-current="true">{include uri='design:migration/parts/state_text.tpl' state=$name} ({$counts[$name]|wash})</span>{else}<a href={concat( 'migration/history/(state)/', $name )|ezurl}>{include uri='design:migration/parts/state_text.tpl' state=$name} ({$counts[$name]|wash})</a>{/if}</li>
        {/foreach}
    </ul>
</div>

<section class="a4-card is-flush a4-section" aria-labelledby="migration-history-runs">
    <div class="a4-card-head"><h2 class="a4-h3 a4-card-title" id="migration-history-runs">{'Runs'|i18n( 'design/admin/migration' )}</h2></div>
    {if $runs|count}
    <div class="a4-table-wrap" role="region" aria-labelledby="migration-history-runs" tabindex="0">
    <table class="a4-table">
        <thead><tr>
            <th scope="col">{'Run'|i18n( 'design/admin/migration' )}</th>
            <th scope="col">{'State'|i18n( 'design/admin/migration' )}</th>
            <th scope="col">{'Started'|i18n( 'design/admin/migration' )}</th>
            <th scope="col">{'By'|i18n( 'design/admin/migration' )}</th>
            <th scope="col">{'Captions'|i18n( 'design/admin/migration' )}</th>
            <th scope="col">{'Repository'|i18n( 'design/admin/migration' )}</th>
            <th scope="col"><span class="a4-sr-only">{'Actions'|i18n( 'design/admin/migration' )}</span></th>
        </tr></thead>
        <tbody>
        {foreach $runs as $run}
        <tr>
            <td><a href={concat( 'migration/log/', $run.run_id )|ezurl}><code class="a4-code">{$run.run_id|wash}</code></a>
                {if $run.environment}<br /><span class="a4-meta">{$run.environment|wash}</span>{/if}
                {if $run.message}<br /><span class="a4-meta">{$run.message|shorten( 160 )|wash}</span>{/if}</td>
            <td>{include uri='design:migration/parts/state.tpl' state=$run.state kind=$run.kind}</td>
            <td>{if $run.started_ts}{$run.started_ts|l10n( 'shortdatetime' )}{else}<span class="a4-muted">-</span>{/if}</td>
            <td>{$run.run_by|wash}</td>
            <td>{if $run.caption_pre}<span class="a4-meta">{'before'|i18n( 'design/admin/migration' )}</span> <code class="a4-code">{$run.caption_pre|wash}</code>{/if}
                {if $run.caption_post}<br /><span class="a4-meta">{'after'|i18n( 'design/admin/migration' )}</span> <code class="a4-code">{$run.caption_post|wash}</code>{/if}
                {if and( $run.caption_pre|not, $run.caption_post|not )}<span class="a4-muted">-</span>{/if}</td>
            <td>{if $run.commit_id}<code class="a4-code">{$run.commit_id|shorten( 12, '' )|wash}</code>{/if}
                {if $run.tag_name}<br /><code class="a4-code">{$run.tag_name|wash}</code>{/if}
                {if $run.push_state}<br /><span class="a4-meta">{'push: %state'|i18n( 'design/admin/migration',, hash( '%state', $run.push_state ) )|wash}</span>{/if}
                {if and( $run.commit_id|not, $run.tag_name|not, $run.push_state|not )}<span class="a4-muted">-</span>{/if}</td>
            <td class="a4-nowrap">
                {if and( $can_run, $run.resumable, $running|not )}
                <form method="post" action={'migration/run'|ezurl}>
                    <input type="hidden" name="ResumeRunID" value="{$run.run_id|wash}" />
                    <button type="submit" class="a4-btn a4-btn-small" name="ResumeButton" value="1">{'Resume'|i18n( 'design/admin/migration' )}<span class="a4-sr-only"> {$run.run_id|wash}</span></button>
                </form>
                {/if}
                {if and( $can_rollback, $run.caption_pre, ne( $run.state, 'running' ) )}<a class="a4-btn a4-btn-small" href={concat( 'migration/rollback/', $run.run_id )|ezurl}>{'Roll back'|i18n( 'design/admin/migration' )}<span class="a4-sr-only"> {$run.run_id|wash}</span></a>{/if}
            </td>
        </tr>
        {/foreach}
        </tbody>
    </table>
    </div>
    {else}
    <div class="a4-card-body"><p class="a4-empty">{'No run in this state.'|i18n( 'design/admin/migration' )}</p></div>
    {/if}
</section>

{if $jobs|count}
<section class="a4-card is-flush a4-section" aria-labelledby="migration-history-jobs">
    <div class="a4-card-head"><h2 class="a4-h3 a4-card-title" id="migration-history-jobs">{'Started here'|i18n( 'design/admin/migration' )}</h2>
        <p class="a4-meta">{'Runs, resumes and rollbacks started on these pages, with the report of each.'|i18n( 'design/admin/migration' )}</p></div>
    <div class="a4-table-wrap" role="region" aria-labelledby="migration-history-jobs" tabindex="0">
    <table class="a4-table">
        <thead><tr>
            <th scope="col">{'Started'|i18n( 'design/admin/migration' )}</th>
            <th scope="col">{'What'|i18n( 'design/admin/migration' )}</th>
            <th scope="col">{'By'|i18n( 'design/admin/migration' )}</th>
            <th scope="col">{'Result'|i18n( 'design/admin/migration' )}</th>
        </tr></thead>
        <tbody>
        {foreach $jobs as $j}
        <tr>
            <td><a href={concat( 'migration/log/', $j.id )|ezurl}>{$j.started|l10n( 'shortdatetime' )}</a></td>
            <td>{switch match=$j.kind}{case match='rollback'}{'Rollback'|i18n( 'design/admin/migration' )}{/case}{case match='resume'}{'Resume'|i18n( 'design/admin/migration' )}{/case}{case}{'Run'|i18n( 'design/admin/migration' )}{/case}{/switch}
                {if $j.target} <code class="a4-code">{$j.target|wash}</code>{/if}</td>
            <td>{$j.user|wash}</td>
            <td>{include uri='design:migration/parts/job_result.tpl' job=$j}</td>
        </tr>
        {/foreach}
        </tbody>
    </table>
    </div>
</section>
{/if}

</div>
