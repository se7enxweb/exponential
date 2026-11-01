{* The migration/status view: the migrations of the installation and their state.
   Variables: report (expMigrationStatus::report(): migrations with kind, summary, runs, lock, warnings, errors, table,
   environment, engine, transaction), problem, pending, lock_text, job, can_run, can_rollback, view. *}
<div class="a4-page a4-setup-migration" id="exp-migration-status">

<div class="a4-pagehead">
    <div class="a4-pagehead-main">
        <h1 class="a4-pagehead-title context-title">{'Migrations'|i18n( 'design/admin/migration' )}</h1>
        <p class="a4-pagehead-sub">{'Changes to this installation written once as files (content, classes, roles, settings, schema) and applied to every installation of the site in the same order. A run takes a caption first, restores it when a migration fails, and records, commits and pushes what it did.'|i18n( 'design/admin/migration' )}</p>
    </div>
    <div class="a4-pagehead-actions">
        <a class="a4-btn" href={'migration/dryrun'|ezurl}>{'Dry run'|i18n( 'design/admin/migration' )}</a>
        {if and( $can_run, $pending|gt( 0 ) )}<a class="a4-btn a4-btn-primary" href={'migration/run'|ezurl}>{'Run %count pending'|i18n( 'design/admin/migration',, hash( '%count', $pending ) )|wash}</a>{/if}
    </div>
</div>

{include uri='design:migration/parts/tabs.tpl' view=$view can_run=$can_run can_rollback=$can_rollback}
{include uri='design:migration/parts/running.tpl' job=$job lock_text=$lock_text}

{if $problem}<div class="a4-notice is-bad" role="alert"><p>{'The status could not be read: %problem'|i18n( 'design/admin/migration',, hash( '%problem', $problem ) )|wash}</p></div>{/if}
{foreach $report.errors as $error}<div class="a4-notice is-bad" role="alert"><p>{$error|wash}</p></div>{/foreach}
{foreach $report.warnings as $warning}<div class="a4-notice is-warn" role="status"><p>{$warning|wash}</p></div>{/foreach}

{if $report.summary}
<section class="a4-card a4-section" aria-labelledby="migration-summary">
    <div class="a4-card-head"><h2 class="a4-h3 a4-card-title" id="migration-summary">{'Overview'|i18n( 'design/admin/migration' )}</h2></div>
    <div class="a4-card-body">
        <dl class="a4-facts">
            <div><dt>{'Migrations'|i18n( 'design/admin/migration' )}</dt><dd>{$report.summary.total|wash}</dd></div>
            <div><dt>{'Pending'|i18n( 'design/admin/migration' )}</dt><dd>{$report.summary.pending|wash}</dd></div>
            <div><dt>{'Done'|i18n( 'design/admin/migration' )}</dt><dd>{$report.summary.done|wash}</dd></div>
            <div><dt>{'Failed'|i18n( 'design/admin/migration' )}</dt><dd>{$report.summary.failed|wash}</dd></div>
            <div><dt>{'Not needed'|i18n( 'design/admin/migration' )}</dt><dd>{first_set( $report.summary['not-needed'], 0 )|wash}</dd></div>
            <div><dt>{'Environment'|i18n( 'design/admin/migration' )}</dt><dd>{if $report.environment}{$report.environment|wash}{else}<span class="a4-muted">{'none'|i18n( 'design/admin/migration' )}</span>{/if}</dd></div>
            <div><dt>{'Database'|i18n( 'design/admin/migration' )}</dt><dd>{$report.engine|wash} <span class="a4-meta">{'transactions: %mode'|i18n( 'design/admin/migration',, hash( '%mode', $report.transaction ) )|wash}</span></dd></div>
        </dl>
    </div>
</section>
{/if}

<section class="a4-card is-flush a4-section" aria-labelledby="migration-list">
    <div class="a4-card-head"><h2 class="a4-h3 a4-card-title" id="migration-list">{'The migrations'|i18n( 'design/admin/migration' )}</h2>
        <p class="a4-meta">{'Site migrations are in migrations/, those of an extension in extension/<name>/migrations/. They run in the order of their names and what they require.'|i18n( 'design/admin/migration' )|wash}</p></div>
    {if $report.migrations|count}
    <div class="a4-table-wrap" role="region" aria-labelledby="migration-list" tabindex="0">
    <table class="a4-table">
        <thead><tr>
            <th scope="col">{'Migration'|i18n( 'design/admin/migration' )}</th>
            <th scope="col">{'Source'|i18n( 'design/admin/migration' )}</th>
            <th scope="col">{'State'|i18n( 'design/admin/migration' )}</th>
            <th scope="col">{'Last run'|i18n( 'design/admin/migration' )}</th>
        </tr></thead>
        <tbody>
        {foreach $report.migrations as $m}
        <tr>
            <td><code class="a4-code">{$m.id|wash}</code>{if $m.description}<br /><span class="a4-meta">{$m.description|wash}</span>{/if}
                {if $m.file}<br /><span class="a4-meta">{$m.file|wash}</span>{/if}
                {if $m.changed}<br /><span class="a4-warn-text">{'The file changed after it was applied.'|i18n( 'design/admin/migration' )}</span>{/if}
                {if $m.detail}<br /><span class="a4-meta">{$m.detail|wash}</span>{/if}</td>
            <td>{$m.source|wash}</td>
            <td>{include uri='design:migration/parts/state.tpl' state=$m.state kind=$m.kind}</td>
            <td>{if $m.record}<a href={concat( 'migration/log/', $m.record.run_id )|ezurl}><code class="a4-code">{$m.record.run_id|wash}</code></a>{else}<span class="a4-muted">-</span>{/if}</td>
        </tr>
        {/foreach}
        </tbody>
    </table>
    </div>
    {else}
    <div class="a4-card-body"><p class="a4-empty">{'No migration yet. ./console exp:migration:generate --skeleton <name> --apply writes the first one.'|i18n( 'design/admin/migration' )|wash}</p></div>
    {/if}
</section>

<section class="a4-card is-flush a4-section" aria-labelledby="migration-runs">
    <div class="a4-card-head"><h2 class="a4-h3 a4-card-title" id="migration-runs">{'The latest runs'|i18n( 'design/admin/migration' )}</h2>
        <p class="a4-meta"><a href={'migration/history'|ezurl}>{'Every run'|i18n( 'design/admin/migration' )}</a></p></div>
    {if $report.runs|count}
    <div class="a4-table-wrap" role="region" aria-labelledby="migration-runs" tabindex="0">
    <table class="a4-table">
        <thead><tr>
            <th scope="col">{'Run'|i18n( 'design/admin/migration' )}</th>
            <th scope="col">{'State'|i18n( 'design/admin/migration' )}</th>
            <th scope="col">{'Started'|i18n( 'design/admin/migration' )}</th>
            <th scope="col">{'By'|i18n( 'design/admin/migration' )}</th>
        </tr></thead>
        <tbody>
        {foreach $report.runs as $run}
        <tr>
            <td><a href={concat( 'migration/log/', $run.run_id )|ezurl}><code class="a4-code">{$run.run_id|wash}</code></a></td>
            <td>{include uri='design:migration/parts/state.tpl' state=$run.state kind=$run.kind}</td>
            <td>{if $run.started_ts}{$run.started_ts|l10n( 'shortdatetime' )}{else}<span class="a4-muted">-</span>{/if}</td>
            <td>{$run.run_by|wash}</td>
        </tr>
        {/foreach}
        </tbody>
    </table>
    </div>
    {else}
    <div class="a4-card-body"><p class="a4-empty">{'No run yet.'|i18n( 'design/admin/migration' )}</p></div>
    {/if}
</section>

</div>
