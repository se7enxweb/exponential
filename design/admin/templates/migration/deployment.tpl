{* The migration/deployment view: one deploy to remotes, live while its job runs, then its report, its gate and its
   rollback. Variables: deploy_id, report (expMigrationRemoteAdmin::decorate() of the stored report, or of a preflight's;
   null while a new deploy runs), stored (bool: a report is kept), job (expMigrationJob::forDeploy(), or null), job_running,
   job_errors (what an ended job printed as errors when it gave no report), progress (expMigrationProgress::fold(),
   with hosts), meter, progress_offset, live (the remotes of the progress: id, state, phase, kind), messages,
   can_deploy (migration/deploy), can_run, can_rollback, view.
   While the job runs, page/migration_deploy.js follows it (migration/stream) and reloads the page when it has ended. *}
<div class="a4-page a4-setup-migration" id="exp-migration-deployment">

<div class="a4-pagehead">
    <div class="a4-pagehead-main">
        <h1 class="a4-pagehead-title context-title">{'Deploy %deploy'|i18n( 'design/admin/migration',, hash( '%deploy', $deploy_id ) )|wash}</h1>
        <p class="a4-pagehead-sub">{'Every remote of the deploy with its state. A deploy started here goes on in the background when this page is closed; open the page again to follow it.'|i18n( 'design/admin/migration' )}</p>
    </div>
    <div class="a4-pagehead-actions"><a class="a4-btn" href={'migration/deploy'|ezurl}>{'All deploys'|i18n( 'design/admin/migration' )}</a></div>
</div>

{include uri='design:migration/parts/tabs.tpl' view=$view can_run=$can_run can_rollback=$can_rollback}

{foreach $messages as $m}<div class="a4-notice is-bad" role="alert"><p>{$m.text|wash}</p></div>{/foreach}

{if $job}
<section class="a4-card a4-section" aria-labelledby="deploy-job">
    <div class="a4-card-head"><h2 class="a4-h3 a4-card-title" id="deploy-job">{'Progress'|i18n( 'design/admin/migration' )}</h2>
        <p class="a4-meta">{switch match=$job.action}
            {case match='dry-run'}{'Preflight'|i18n( 'design/admin/migration' )}{/case}
            {case match='continue'}{'Next stage'|i18n( 'design/admin/migration' )}{/case}
            {case match='rollback'}{'Rollback'|i18n( 'design/admin/migration' )}{/case}
            {case match='transfer-only'}{'Transfer only'|i18n( 'design/admin/migration' )}{/case}
            {case}{'Deploy'|i18n( 'design/admin/migration' )}{/case}
            {/switch} &middot; {'Started by %user at %time.'|i18n( 'design/admin/migration',, hash( '%user', $job.user, '%time', $job.started|l10n( 'shortdatetime' ) ) )|wash}</p></div>
    <div class="a4-card-body a4-stack"
         {if $job_running}data-migration-deploy-stream="{concat( 'migration/stream/', $job.id, '/(offset)/0/(log)/0' )|ezurl( 'no' )}"
         data-text-prepare="{'Checking the settings and the credentials'|i18n( 'design/admin/migration' )|wash}"
         data-text-preflight="{'Preflight: reading the state of every remote'|i18n( 'design/admin/migration' )|wash}"
         data-text-transfer="{'Sending the migration files'|i18n( 'design/admin/migration' )|wash}"
         data-text-verify="{'Verifying what arrived'|i18n( 'design/admin/migration' )|wash}"
         data-text-switch="{'Running the migrations on every remote'|i18n( 'design/admin/migration' )|wash}"
         data-text-rollback="{'Taking the remotes back'|i18n( 'design/admin/migration' )|wash}"
         data-text-done="{'The deploy has ended. Loading its report.'|i18n( 'design/admin/migration' )|wash}"
         data-text-lost="{'The progress could not be followed. Reload the page to see how the deploy is doing.'|i18n( 'design/admin/migration' )|wash}"{/if}>
        <p>{include uri='design:migration/parts/job_result.tpl' job=$job}
           <span class="a4-meta" id="exp-deploy-phase" aria-live="polite"></span></p>
        {if or( $job_running, $progress.total|gt( 0 ) )}
        <div class="a4-meter is-ok a4-setup-progress" aria-hidden="true"><span id="exp-deploy-bar" class="a4-pct-{$meter}"></span></div>
        {/if}
        {if $job_running}
        <div class="a4-table-wrap" role="region" aria-labelledby="deploy-job" tabindex="0">
        <table class="a4-table">
            <thead><tr>
                <th scope="col">{'Remote'|i18n( 'design/admin/migration' )}</th>
                <th scope="col">{'State'|i18n( 'design/admin/migration' )}</th>
                <th scope="col">{'Phase'|i18n( 'design/admin/migration' )}</th>
            </tr></thead>
            <tbody id="exp-deploy-hosts">
            {foreach $live as $h}
            <tr data-deploy-host="{$h.id|wash}"><td><code class="a4-code">{$h.id|wash}</code></td>
                <td>{include uri='design:migration/parts/deploy_state.tpl' state=$h.state kind=$h.kind}</td><td class="a4-meta">{$h.phase|wash}</td></tr>
            {/foreach}
            </tbody>
        </table>
        </div>
        {* the words of the states for the live table *}
        <ul hidden="hidden" id="exp-deploy-state-words">
        {foreach array( 'selected', 'prepared', 'ready', 'waiting', 'sent', 'verified', 'applied', 'restored', 'rolled-back', 'unchanged', 'skipped', 'failed', 'refused', 'unreachable', 'unknown', 'out-of-sync' ) as $w}
            <li data-state="{$w}" data-kind="{cond( array( 'ready', 'sent', 'verified', 'applied', 'rolled-back', 'unchanged' )|contains( $w ), 'ok', array( 'waiting', 'skipped', 'restored' )|contains( $w ), 'warn', array( 'failed', 'refused', 'unreachable', 'unknown', 'out-of-sync' )|contains( $w ), 'bad', 'info' )}">{include uri='design:migration/parts/deploy_state_text.tpl' state=$w}</li>
        {/foreach}
        </ul>
        <p class="a4-meta">{'This page follows the deploy as it goes.'|i18n( 'design/admin/migration' )} <a href={concat( 'migration/deployment/', $deploy_id )|ezurl}>{'Reload'|i18n( 'design/admin/migration' )}</a></p>
        {/if}
    </div>
</section>
{/if}

{if $report}
<section class="a4-card a4-section{if eq( $report.kind, 'bad' )} is-bad{/if}" aria-labelledby="deploy-report">
    <div class="a4-card-head"><h2 class="a4-h3 a4-card-title" id="deploy-report">{if eq( $report.mode, 'dry-run' )}{'Preflight'|i18n( 'design/admin/migration' )}{else}{'Report'|i18n( 'design/admin/migration' )}{/if}</h2></div>
    <div class="a4-card-body a4-stack">
        <dl class="a4-facts is-rows">
            <dt>{'State'|i18n( 'design/admin/migration' )}</dt><dd>{include uri='design:migration/parts/deploy_state.tpl' state=$report.state kind=$report.kind}</dd>
            <dt>{'In sync'|i18n( 'design/admin/migration' )}</dt><dd>{if is_null( $report.in_sync )}{'unknown'|i18n( 'design/admin/migration' )}{elseif $report.in_sync}{'yes'|i18n( 'design/admin/migration' )}{else}<span class="a4-badge is-bad">{'no'|i18n( 'design/admin/migration' )}</span>{/if}</dd>
            {if $report.stage_count|gt( 0 )}<dt>{'Stages'|i18n( 'design/admin/migration' )}</dt><dd>{$report.stage_count|wash}{if $report.next_stage_number} &middot; {'stage %stage waits at its gate'|i18n( 'design/admin/migration',, hash( '%stage', $report.next_stage_number ) )|wash}{/if}</dd>{/if}
            <dt>{'By'|i18n( 'design/admin/migration' )}</dt><dd>{if $report.user}{$report.user|wash}{else}-{/if}</dd>
            <dt>{'Started'|i18n( 'design/admin/migration' )}</dt><dd>{$report.started_at|wash}</dd>
            <dt>{'Ended'|i18n( 'design/admin/migration' )}</dt><dd>{if $report.finished_at}{$report.finished_at|wash}{else}-{/if}</dd>
            {foreach $report.gates as $gate}<dt>{'Gate %stage'|i18n( 'design/admin/migration',, hash( '%stage', $gate.stage ) )|wash}</dt><dd>{'approved by %user at %time'|i18n( 'design/admin/migration',, hash( '%user', $gate.by, '%time', $gate.at ) )|wash}</dd>{/foreach}
        </dl>
        {foreach $report.errors as $error}<div class="a4-notice is-bad" role="alert"><p>{$error|wash}</p></div>{/foreach}
        {foreach $report.warnings as $warning}<div class="a4-notice is-warn" role="status"><p>{$warning|wash}</p></div>{/foreach}
    </div>
</section>

<section class="a4-card is-flush a4-section" aria-labelledby="deploy-remotes">
    <div class="a4-card-head"><h2 class="a4-h3 a4-card-title" id="deploy-remotes">{'Remotes'|i18n( 'design/admin/migration' )}</h2></div>
    <div class="a4-table-wrap" role="region" aria-labelledby="deploy-remotes" tabindex="0">
    <table class="a4-table">
        <thead><tr>
            {if $report.stage_count|gt( 0 )}<th scope="col">{'Stage'|i18n( 'design/admin/migration' )}</th>{/if}
            <th scope="col">{'Remote'|i18n( 'design/admin/migration' )}</th>
            <th scope="col">{'State'|i18n( 'design/admin/migration' )}</th>
            <th scope="col">{'Files sent'|i18n( 'design/admin/migration' )}</th>
            <th scope="col">{'Migrations'|i18n( 'design/admin/migration' )}</th>
        </tr></thead>
        <tbody>
        {foreach $report.remote_list as $r}
        <tr>
            {if $report.stage_count|gt( 0 )}<td class="a4-num">{$r.stage|wash}</td>{/if}
            <td><code class="a4-code">{$r.id|wash}</code> <span class="a4-meta">{$r.environment|wash}</span><br /><span class="a4-meta">{$r.address|wash}</span>
                {if $r.version}<br /><span class="a4-meta">{'Kernel version'|i18n( 'design/admin/migration' )} {$r.version|wash}</span>{/if}</td>
            <td>{include uri='design:migration/parts/deploy_state.tpl' state=$r.state kind=$r.kind}
                {foreach $r.problems as $problem}<br /><span class="a4-warn-text">{$problem|wash}</span>{/foreach}
                {foreach $r.fixes as $fix}<br /><span class="a4-meta">{$fix|wash}</span>{/foreach}</td>
            <td class="a4-num">{$r.send|count}</td>
            <td>{if $r.applied|count}{'applied: %list'|i18n( 'design/admin/migration',, hash( '%list', $r.applied|implode( ', ' ) ) )|wash}
                {elseif $r.pending|count}{'to apply: %list'|i18n( 'design/admin/migration',, hash( '%list', $r.pending|implode( ', ' ) ) )|wash}
                {else}<span class="a4-muted">-</span>{/if}
                {if $r.caption}<br /><span class="a4-meta">{'Caption before'|i18n( 'design/admin/migration' )} <code class="a4-code">{$r.caption|wash}</code></span>{/if}</td>
        </tr>
        {/foreach}
        </tbody>
    </table>
    </div>
</section>

{if $report.phase_list|count}
<section class="a4-card a4-section" aria-labelledby="deploy-phases">
    <div class="a4-card-head"><h2 class="a4-h3 a4-card-title" id="deploy-phases">{'Phases'|i18n( 'design/admin/migration' )}</h2></div>
    <div class="a4-card-body">
        <dl class="a4-facts is-rows">
        {foreach $report.phase_list as $p}<dt><code class="a4-code">{$p.name|wash}</code></dt><dd>{if $p.ok}<span class="a4-badge is-ok">{'passed'|i18n( 'design/admin/migration' )}</span>{else}<span class="a4-badge is-bad">{'failed'|i18n( 'design/admin/migration' )}</span>{/if} <span class="a4-meta">{'%ms ms'|i18n( 'design/admin/migration',, hash( '%ms', $p.duration_ms ) )|wash}</span></dd>{/foreach}
        </dl>
    </div>
</section>
{/if}

{if and( $can_deploy, $job_running|not, $stored )}
{if $report.actions.continue}
<form method="post" action={concat( 'migration/deployment/', $deploy_id )|ezurl} class="a4-form">
<section class="a4-card a4-section is-attention" aria-labelledby="deploy-gate">
    <div class="a4-card-head"><h2 class="a4-h3 a4-card-title" id="deploy-gate">{'Gate: stage %stage of %stages'|i18n( 'design/admin/migration',, hash( '%stage', $report.next_stage_number, '%stages', $report.stage_count ) )|wash}</h2>
        <p class="a4-meta">{'The stages before it are deployed. Check them, then approve the next stage: its remotes are checked again and deployed, all or nothing.'|i18n( 'design/admin/migration' )}</p></div>
    <div class="a4-card-body"><p>{'Next'|i18n( 'design/admin/migration' )}: {foreach $report.actions.next as $n}<span class="a4-tag">{$n|wash}</span> {/foreach}</p></div>
    <div class="a4-card-foot a4-actions">
        <label class="a4-check"><input type="checkbox" name="ConfirmApprove" value="1" /> <span>{'Yes, deploy the next stage now'|i18n( 'design/admin/migration' )}</span></label>
        <button type="submit" class="a4-btn a4-btn-primary" name="ApproveButton" value="1">{'Approve'|i18n( 'design/admin/migration' )}</button>
    </div>
</section>
</form>
{/if}
{if $report.actions.rollback}
<form method="post" action={concat( 'migration/deployment/', $deploy_id )|ezurl} class="a4-form">
<section class="a4-card a4-section" aria-labelledby="deploy-rollback">
    <div class="a4-card-head"><h2 class="a4-h3 a4-card-title" id="deploy-rollback">{'Roll this deploy back'|i18n( 'design/admin/migration' )}</h2>
        <p class="a4-meta">{'Every remote that applied it (%list) goes back to the caption taken before its run, all at once.'|i18n( 'design/admin/migration',, hash( '%list', $report.actions.applied|implode( ', ' ) ) )|wash}</p></div>
    <div class="a4-card-foot a4-actions">
        <label class="a4-check"><input type="checkbox" name="ConfirmRollback" value="1" /> <span>{'Yes, roll this deploy back now'|i18n( 'design/admin/migration' )}</span></label>
        <button type="submit" class="a4-btn" name="RollbackButton" value="1">{'Roll back'|i18n( 'design/admin/migration' )}</button>
    </div>
</section>
</form>
{/if}
{/if}
{/if}

{if $job_errors}
<section class="a4-card a4-section" aria-labelledby="deploy-errors">
    <div class="a4-card-head"><h2 class="a4-h3 a4-card-title" id="deploy-errors">{'What the run printed as errors'|i18n( 'design/admin/migration' )}</h2></div>
    <div class="a4-card-body"><pre class="a4-pre">{$job_errors|wash}</pre></div>
</section>
{/if}

</div>
{if $job_running}{ezscript( array( 'page/migration_deploy.js' ) )}{/if}
