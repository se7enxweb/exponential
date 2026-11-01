{* The migration/log view: one run, or one rollback started in the administration.
   Variables: run_id, run (expMigrationStatus::runSummary() with kind, started_ts, finished_ts, log_file, resumable; null
   for a rollback), records (the rows of exp_migration this run wrote, with kind), job (expMigrationJob::get(), null for a
   run started on the console), report (the job's report once it has ended), restore_lines, progress
   (expMigrationProgress::fold()), meter (its step for .a4-pct-*), progress_offset, log (expMigrationAdmin::logTail():
   text, size, truncated, exists), job_running, job_kind ('' without a job), job_errors (what an ended job printed as
   errors), problem, can_run, can_rollback, view.
   While the job runs, page/migration_log.js follows it (migration/stream) and reloads the page when it has ended. *}
<div class="a4-page a4-setup-migration" id="exp-migration-log-page">

<div class="a4-pagehead">
    <div class="a4-pagehead-main">
        {if eq( $job_kind, 'rollback' )}
        <h1 class="a4-pagehead-title context-title">{'Rollback of %target'|i18n( 'design/admin/migration',, hash( '%target', $job.target ) )|wash}</h1>
        {else}
        <h1 class="a4-pagehead-title context-title">{'Run %run'|i18n( 'design/admin/migration',, hash( '%run', $run_id ) )|wash}</h1>
        {/if}
        <p class="a4-pagehead-sub">{'What the run did, its record and its log. A run started here goes on in the background when this page is closed; open the page again to follow it.'|i18n( 'design/admin/migration' )}</p>
    </div>
    {if $run}
    <div class="a4-pagehead-actions">
        {if and( $can_run, $run.resumable, $job_running|not )}
        <form method="post" action={'migration/run'|ezurl}>
            <input type="hidden" name="ResumeRunID" value="{$run.run_id|wash}" />
            <button type="submit" class="a4-btn" name="ResumeButton" value="1">{'Resume this run'|i18n( 'design/admin/migration' )}</button>
        </form>
        {/if}
        {if and( $can_rollback, $run.caption_pre, ne( $run.state, 'running' ) )}<a class="a4-btn" href={concat( 'migration/rollback/', $run.run_id )|ezurl}>{'Roll back this run'|i18n( 'design/admin/migration' )}</a>{/if}
    </div>
    {/if}
</div>

{include uri='design:migration/parts/tabs.tpl' view=$view can_run=$can_run can_rollback=$can_rollback}

{if $problem}<div class="a4-notice is-bad" role="alert"><p>{'The record could not be read: %problem'|i18n( 'design/admin/migration',, hash( '%problem', $problem ) )|wash}</p></div>{/if}

{if $job}
<section class="a4-card a4-section" aria-labelledby="migration-job">
    <div class="a4-card-head"><h2 class="a4-h3 a4-card-title" id="migration-job">{'Progress'|i18n( 'design/admin/migration' )}</h2>
        <p class="a4-meta">{'Started by %user at %time.'|i18n( 'design/admin/migration',, hash( '%user', $job.user, '%time', $job.started|l10n( 'shortdatetime' ) ) )|wash}</p></div>
    <div class="a4-card-body a4-stack"
         {if $job.running}data-migration-stream="{concat( 'migration/stream/', $run_id, '/(offset)/', $progress_offset, '/(log)/', $log.size )|ezurl( 'no' )}"
         data-text-caption-pre="{'Checking the code and taking a caption before the run'|i18n( 'design/admin/migration' )|wash}"
         data-text-migrations="{'Running %id (%index of %total)'|i18n( 'design/admin/migration' )|wash}"
         data-text-between="{'%done of %total done'|i18n( 'design/admin/migration' )|wash}"
         data-text-after-migrations="{'The migrations have run; restoring the caption if one failed'|i18n( 'design/admin/migration' )|wash}"
         data-text-caption-post="{'Taking a caption after the run, committing, tagging and pushing'|i18n( 'design/admin/migration' )|wash}"
         data-text-restore="{'Restoring the caption'|i18n( 'design/admin/migration' )|wash}"
         data-text-done="{'The run has ended. Loading its report.'|i18n( 'design/admin/migration' )|wash}"
         data-text-lost="{'The progress could not be followed. Reload the page to see how the run is doing.'|i18n( 'design/admin/migration' )|wash}"{/if}>
        <p>{include uri='design:migration/parts/job_result.tpl' job=$job}
           <span class="a4-meta" id="exp-migration-phase" aria-live="polite">{include uri='design:migration/parts/phase.tpl' progress=$progress}</span></p>
        {if or( $job.running, $progress.total|gt( 0 ) )}
        <div class="a4-meter is-ok a4-setup-progress" aria-hidden="true"><span id="exp-migration-bar" class="a4-pct-{$meter}"></span></div>
        {/if}
        {if $job.running}<p class="a4-meta" id="exp-migration-follow">{'This page follows the run as it goes.'|i18n( 'design/admin/migration' )} <a href={concat( 'migration/log/', $run_id )|ezurl}>{'Reload'|i18n( 'design/admin/migration' )}</a></p>{/if}
    </div>
</section>
{/if}

{if $report}
<section class="a4-card a4-section{if $report.errors|count} is-bad{/if}" aria-labelledby="migration-report">
    <div class="a4-card-head"><h2 class="a4-h3 a4-card-title" id="migration-report">{'Report'|i18n( 'design/admin/migration' )}</h2></div>
    <div class="a4-card-body a4-stack">
        {foreach $report.errors as $error}<div class="a4-notice is-bad" role="alert"><p>{$error|wash}</p></div>{/foreach}
        {foreach $report.warnings as $warning}<div class="a4-notice is-warn" role="status"><p>{$warning|wash}</p></div>{/foreach}
        {if and( is_set( $report.migrations ), $report.migrations|count )}
        <div class="a4-table-wrap" role="region" aria-labelledby="migration-report" tabindex="0">
        <table class="a4-table">
            <thead><tr>
                <th scope="col">{'Migration'|i18n( 'design/admin/migration' )}</th>
                <th scope="col">{'Result'|i18n( 'design/admin/migration' )}</th>
                <th scope="col">{'Time'|i18n( 'design/admin/migration' )}</th>
            </tr></thead>
            <tbody>
            {foreach $report.migrations as $m}
            <tr>
                <td><code class="a4-code">{$m.id|wash}</code>{foreach $m.messages as $message}<br /><span class="a4-meta">{$message|wash}</span>{/foreach}</td>
                <td>{if ne( $m.action, 'none' )}{include uri='design:migration/parts/state.tpl' state=$m.action kind=$m.action_kind}{else}{include uri='design:migration/parts/state.tpl' state=$m.state kind=$m.kind}{/if}</td>
                <td class="a4-num">{if $m.duration_ms}{'%ms ms'|i18n( 'design/admin/migration',, hash( '%ms', $m.duration_ms ) )|wash}{else}<span class="a4-muted">-</span>{/if}</td>
            </tr>
            {/foreach}
            </tbody>
        </table>
        </div>
        {/if}
        {if $restore_lines|count}<pre class="a4-pre">{$restore_lines|implode( "\n" )|wash}</pre>{/if}
    </div>
</section>
{/if}

{if $job_errors}
<section class="a4-card a4-section" aria-labelledby="migration-errors">
    <div class="a4-card-head"><h2 class="a4-h3 a4-card-title" id="migration-errors">{'What the run printed as errors'|i18n( 'design/admin/migration' )}</h2></div>
    <div class="a4-card-body"><pre class="a4-pre">{$job_errors|wash}</pre></div>
</section>
{/if}

{if $run}
<section class="a4-card a4-section" aria-labelledby="migration-record">
    <div class="a4-card-head"><h2 class="a4-h3 a4-card-title" id="migration-record">{'Record'|i18n( 'design/admin/migration' )}</h2></div>
    <div class="a4-card-body">
        <dl class="a4-facts is-rows">
            <dt>{'State'|i18n( 'design/admin/migration' )}</dt><dd>{include uri='design:migration/parts/state.tpl' state=$run.state kind=$run.kind}</dd>
            <dt>{'By'|i18n( 'design/admin/migration' )}</dt><dd>{$run.run_by|wash}</dd>
            <dt>{'Environment'|i18n( 'design/admin/migration' )}</dt><dd>{if $run.environment}{$run.environment|wash}{else}<span class="a4-muted">{'none'|i18n( 'design/admin/migration' )}</span>{/if}</dd>
            <dt>{'Started'|i18n( 'design/admin/migration' )}</dt><dd>{if $run.started_ts}{$run.started_ts|l10n( 'shortdatetime' )}{else}-{/if}</dd>
            <dt>{'Ended'|i18n( 'design/admin/migration' )}</dt><dd>{if $run.finished_ts}{$run.finished_ts|l10n( 'shortdatetime' )}{else}-{/if}</dd>
            <dt>{'Caption before'|i18n( 'design/admin/migration' )}</dt><dd>{if $run.caption_pre}<code class="a4-code">{$run.caption_pre|wash}</code>{else}-{/if}</dd>
            <dt>{'Caption after'|i18n( 'design/admin/migration' )}</dt><dd>{if $run.caption_post}<code class="a4-code">{$run.caption_post|wash}</code>{else}-{/if}</dd>
            <dt>{'Commit'|i18n( 'design/admin/migration' )}</dt><dd>{if $run.commit_id}<code class="a4-code">{$run.commit_id|wash}</code>{else}-{/if}</dd>
            <dt>{'Tag'|i18n( 'design/admin/migration' )}</dt><dd>{if $run.tag_name}<code class="a4-code">{$run.tag_name|wash}</code>{else}-{/if}</dd>
            <dt>{'Push'|i18n( 'design/admin/migration' )}</dt><dd>{if $run.push_state}{$run.push_state|wash}{else}-{/if}</dd>
            {if $run.message}<dt>{'Message'|i18n( 'design/admin/migration' )}</dt><dd>{$run.message|wash}</dd>{/if}
        </dl>
    </div>
</section>
{/if}

{if $records|count}
<section class="a4-card is-flush a4-section" aria-labelledby="migration-records">
    <div class="a4-card-head"><h2 class="a4-h3 a4-card-title" id="migration-records">{'Migrations of this run'|i18n( 'design/admin/migration' )}</h2></div>
    <div class="a4-table-wrap" role="region" aria-labelledby="migration-records" tabindex="0">
    <table class="a4-table">
        <thead><tr>
            <th scope="col">{'Migration'|i18n( 'design/admin/migration' )}</th>
            <th scope="col">{'Source'|i18n( 'design/admin/migration' )}</th>
            <th scope="col">{'State'|i18n( 'design/admin/migration' )}</th>
            <th scope="col">{'Time'|i18n( 'design/admin/migration' )}</th>
        </tr></thead>
        <tbody>
        {foreach $records as $r}
        <tr>
            <td><code class="a4-code">{$r.migration_id|wash}</code>{if $r.message}<br /><span class="a4-meta">{$r.message|wash}</span>{/if}</td>
            <td>{$r.source|wash}</td>
            <td>{include uri='design:migration/parts/state.tpl' state=$r.state kind=$r.kind}</td>
            <td class="a4-num">{'%ms ms'|i18n( 'design/admin/migration',, hash( '%ms', $r.duration_ms ) )|wash}</td>
        </tr>
        {/foreach}
        </tbody>
    </table>
    </div>
</section>
{/if}

{if or( $log.exists, and( $job_running, ne( $job_kind, 'rollback' ) ) )}
<section class="a4-card a4-section" aria-labelledby="migration-runlog">
    <div class="a4-card-head"><h2 class="a4-h3 a4-card-title" id="migration-runlog">{'Run log'|i18n( 'design/admin/migration' )}</h2>
        {if $log.truncated}<p class="a4-meta">{'Only the end of the log is shown.'|i18n( 'design/admin/migration' )}</p>{/if}</div>
    <div class="a4-card-body"><pre class="a4-pre is-tall" id="exp-migration-runlog" tabindex="0">{$log.text|wash}</pre></div>
</section>
{/if}

</div>
{if $job_running}{ezscript( array( 'page/migration_log.js' ) )}{/if}
