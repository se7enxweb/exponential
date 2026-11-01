{* The migration/dryrun view: what a run would do, and its simulation.
   Variables: report (the runner's report: mode dry-run or simulate, migrations with plan, simulation, kind, action_kind;
   captions, errors, warnings, summary; null after a problem), simulated (bool), supported (the database can simulate),
   messages, problem, can_run, can_rollback, view. *}
<div class="a4-page a4-setup-migration" id="exp-migration-dryrun">

<div class="a4-pagehead">
    <div class="a4-pagehead-main">
        <h1 class="a4-pagehead-title context-title">{if $simulated}{'Simulated run'|i18n( 'design/admin/migration' )}{else}{'Dry run'|i18n( 'design/admin/migration' )}{/if}</h1>
        <p class="a4-pagehead-sub">{if $simulated}{'The pending migrations ran in a transaction that was rolled back afterwards: nothing was kept. Below is what each one changed in the database.'|i18n( 'design/admin/migration' )}{else}{'What a run would do now. Nothing is changed: each pending migration lists the changes it would make, and the run what it would do before and after.'|i18n( 'design/admin/migration' )}{/if}</p>
    </div>
    {if $can_run}
    <div class="a4-pagehead-actions">
        {if $supported}
        <form method="post" action={'migration/dryrun'|ezurl}>
            <button type="submit" class="a4-btn" name="SimulateButton" value="1">{'Simulate'|i18n( 'design/admin/migration' )}</button>
        </form>
        {/if}
        <a class="a4-btn a4-btn-primary" href={'migration/run'|ezurl}>{'Run'|i18n( 'design/admin/migration' )}</a>
    </div>
    {/if}
</div>

{include uri='design:migration/parts/tabs.tpl' view=$view can_run=$can_run can_rollback=$can_rollback}

{foreach $messages as $m}
<div class="a4-notice is-{if eq( $m.kind, 'ok' )}ok{elseif eq( $m.kind, 'warn' )}warn{else}bad{/if}" role="{if eq( $m.kind, 'bad' )}alert{else}status{/if}"><p>{$m.text|wash}</p></div>
{/foreach}
{if $problem}<div class="a4-notice is-bad" role="alert"><p>{'The dry run failed: %problem'|i18n( 'design/admin/migration',, hash( '%problem', $problem ) )|wash}</p></div>{/if}
{if and( $can_run, $supported|not )}<p class="a4-meta a4-section">{'A simulation needs PostgreSQL or SQLite, whose schema changes can be rolled back; on this database the plan below is what a run would do.'|i18n( 'design/admin/migration' )}</p>{/if}

{if $report}
{foreach $report.errors as $error}<div class="a4-notice is-bad" role="alert"><p>{$error|wash}</p></div>{/foreach}
{foreach $report.warnings as $warning}<div class="a4-notice is-warn" role="status"><p>{$warning|wash}</p></div>{/foreach}

<section class="a4-card is-flush a4-section" aria-labelledby="migration-plan">
    <div class="a4-card-head"><h2 class="a4-h3 a4-card-title" id="migration-plan">{'The migrations'|i18n( 'design/admin/migration' )}</h2></div>
    {if $report.migrations|count}
    <div class="a4-table-wrap" role="region" aria-labelledby="migration-plan" tabindex="0">
    <table class="a4-table">
        <thead><tr>
            <th scope="col">{'Migration'|i18n( 'design/admin/migration' )}</th>
            <th scope="col">{'State'|i18n( 'design/admin/migration' )}</th>
            <th scope="col">{'What it would do'|i18n( 'design/admin/migration' )}</th>
        </tr></thead>
        <tbody>
        {foreach $report.migrations as $m}
        <tr>
            <td><code class="a4-code">{$m.id|wash}</code><br /><span class="a4-meta">{$m.source|wash}</span>{if $m.description}<br /><span class="a4-meta">{$m.description|wash}</span>{/if}</td>
            <td>{if and( is_set( $m.action ), ne( $m.action, 'none' ) )}{include uri='design:migration/parts/state.tpl' state=$m.action kind=$m.action_kind}{else}{include uri='design:migration/parts/state.tpl' state=$m.state kind=$m.kind}{/if}</td>
            <td>{if $m.plan|count}<ul class="a4-plain">{foreach $m.plan as $line}<li>{$line|wash}</li>{/foreach}</ul>{/if}
                {if is_set( $m.simulation )}
                    <p class="a4-meta">{'Simulation: %result in %ms ms'|i18n( 'design/admin/migration',, hash( '%result', $m.simulation.result, '%ms', $m.simulation.duration_ms ) )|wash}</p>
                    {if or( $m.simulation.messages|count, $m.simulation.diff|count )}<pre class="a4-pre">{$m.simulation.messages|merge( $m.simulation.diff )|implode( "\n" )|wash}</pre>{/if}
                {/if}
                {foreach $m.messages as $line}<p class="a4-meta">{$line|wash}</p>{/foreach}
                {if and( $m.plan|count|not, is_set( $m.simulation )|not, $m.messages|count|not )}<span class="a4-muted">-</span>{/if}</td>
        </tr>
        {/foreach}
        </tbody>
    </table>
    </div>
    {else}
    <div class="a4-card-body"><p class="a4-empty">{'No migration yet.'|i18n( 'design/admin/migration' )}</p></div>
    {/if}
</section>

{if is_set( $report.captions )}
<section class="a4-card a4-section" aria-labelledby="migration-flow">
    <div class="a4-card-head"><h2 class="a4-h3 a4-card-title" id="migration-flow">{'Before and after the run'|i18n( 'design/admin/migration' )}</h2></div>
    <div class="a4-card-body">
        <dl class="a4-facts is-rows">
            <dt>{'Caption before'|i18n( 'design/admin/migration' )}</dt><dd>{if eq( $report.captions.pre, 'would-take' )}{'taken, and restored when a migration fails'|i18n( 'design/admin/migration' )}{else}{'off'|i18n( 'design/admin/migration' )}{/if}</dd>
            <dt>{'Caption after'|i18n( 'design/admin/migration' )}</dt><dd>{if eq( $report.captions.post, 'would-take' )}{'taken when the run passed'|i18n( 'design/admin/migration' )}{else}{'off'|i18n( 'design/admin/migration' )}{/if}</dd>
        </dl>
    </div>
</section>
{/if}

{if $report.summary}
<p class="a4-meta a4-section">{'%total migrations: %pending pending, %done done, %failed failed.'|i18n( 'design/admin/migration',, hash( '%total', $report.summary.total, '%pending', $report.summary.pending, '%done', $report.summary.done, '%failed', $report.summary.failed ) )|wash}</p>
{/if}
{/if}

</div>
