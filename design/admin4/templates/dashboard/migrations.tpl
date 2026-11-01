{* The dashboard block "migrations" (dashboard.ini [DashboardBlock_migrations], shown only with migration/read): how many
   migrations are pending or failed, the orphans (recorded, their file gone), the run going on and the latest run, with
   links to Setup > Migrations (migration/status) and the run's page (migration/log/<run id>). Drawn with the
   administration's shared components (components.css): no CSS in the template. *}
{* The module name is a variable, so the fetch is not compiled into a direct class call: a server process started
   before the class existed (a Velocity worker before its restart) gets an empty block, not an error. *}
{def $migration_module = 'migration'
     $migrations = fetch( $migration_module, 'summary' )}
{if $migrations|count}
<div>
<h2>{'Migrations'|i18n( 'design/admin/dashboard/migrations' )}</h2>

{if $migrations.problem}
<p class="a4-notice is-bad" role="alert">{'The status could not be read: %problem'|i18n( 'design/admin/dashboard/migrations',, hash( '%problem', $migrations.problem ) )|wash}</p>
{elseif $migrations.ready|not}
<p class="a4-notice is-warn" role="status">{'The tables of the migrations do not exist yet: ./console exp:upgrade --apply makes them.'|i18n( 'design/admin/dashboard/migrations' )}</p>
{/if}
{if $migrations.failed}
<p class="a4-notice is-bad" role="alert"><a href={'migration/status'|ezurl}>{'%count migrations failed'|i18n( 'design/admin/dashboard/migrations',, hash( '%count', $migrations.failed ) )|wash}</a></p>
{/if}
{if $migrations.running}
<p class="a4-notice" role="status"><span class="a4-status is-running"><span class="a4-dot" aria-hidden="true"></span><a href={concat( 'migration/log/', $migrations.running.id )|ezurl}>{'A run is going on, started by %user'|i18n( 'design/admin/dashboard/migrations',, hash( '%user', $migrations.running.user ) )|wash}</a></span></p>
{elseif $migrations.lock}
<p class="a4-notice is-warn" role="status">{'The installation is locked: %holder'|i18n( 'design/admin/dashboard/migrations',, hash( '%holder', $migrations.lock ) )|wash}</p>
{/if}

<dl class="a4-facts">
    <div><dt>{'Pending'|i18n( 'design/admin/dashboard/migrations' )}</dt><dd>{if $migrations.pending}<span class="a4-badge is-warn">{$migrations.pending|wash}</span>{else}0{/if}</dd></div>
    <div><dt>{'Failed'|i18n( 'design/admin/dashboard/migrations' )}</dt><dd>{if $migrations.failed}<span class="a4-badge is-bad">{$migrations.failed|wash}</span>{else}0{/if}</dd></div>
    <div><dt>{'Orphans'|i18n( 'design/admin/dashboard/migrations' )}</dt><dd>{if $migrations.orphans}<span class="a4-badge is-warn">{$migrations.orphans|wash}</span>{else}0{/if}</dd></div>
    <div><dt>{'Last run'|i18n( 'design/admin/dashboard/migrations' )}</dt>
        <dd>{if $migrations.run}<a href={concat( 'migration/log/', $migrations.run.run_id )|ezurl}>{if $migrations.run.started_ts}{$migrations.run.started_ts|l10n( 'shortdatetime' )}{else}{$migrations.run.run_id|wash}{/if}</a>
            {include uri='design:migration/parts/state.tpl' state=$migrations.run.state kind=$migrations.run.kind}{else}<span class="a4-muted">{'none yet'|i18n( 'design/admin/dashboard/migrations' )}</span>{/if}</dd></div>
</dl>
<p class="a4-card-body"><a href={'migration/status'|ezurl}>{'All migrations'|i18n( 'design/admin/dashboard/migrations' )}</a></p>
</div>
{/if}
{undef $migration_module $migrations}
