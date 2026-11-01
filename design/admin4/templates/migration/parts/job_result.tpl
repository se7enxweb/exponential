{* How a run started in the administration ended. Variables: job (expMigrationJob::get()). *}
{if $job.running}<span class="a4-status is-running"><span class="a4-dot" aria-hidden="true"></span>{'running'|i18n( 'design/admin/migration' )}</span>
{elseif $job.reported|not}<span class="a4-badge is-warn">{'ended without a report'|i18n( 'design/admin/migration' )}</span>
{elseif eq( $job.exit, 0 )}<span class="a4-badge is-ok">{'passed'|i18n( 'design/admin/migration' )}</span>
{elseif eq( $job.exit, 1 )}<span class="a4-badge is-bad">{'failed'|i18n( 'design/admin/migration' )}</span>
{elseif eq( $job.exit, 2 )}<span class="a4-badge is-bad">{'refused'|i18n( 'design/admin/migration' )}</span>
{elseif eq( $job.exit, 3 )}<span class="a4-badge is-bad">{'the database was not reachable'|i18n( 'design/admin/migration' )}</span>
{else}<span class="a4-badge is-bad">{'internal error'|i18n( 'design/admin/migration' )}</span>{/if}
