{* The run going on in the background and the holder of the installation lock. Variables: job (expMigrationJob::running()
   or null), lock_text (who holds the lock, '' when nobody does). *}
{if $job}
<div class="a4-notice" role="status">
    <p class="a4-notice-title"><span class="a4-status is-running"><span class="a4-dot" aria-hidden="true"></span>{'A run is going on'|i18n( 'design/admin/migration' )}</span></p>
    <p>{'%kind started by %user at %time.'|i18n( 'design/admin/migration',, hash( '%kind', cond( eq( $job.kind, 'rollback' ), 'Rollback'|i18n( 'design/admin/migration' ), eq( $job.kind, 'resume' ), 'Resume'|i18n( 'design/admin/migration' ), 'Run'|i18n( 'design/admin/migration' ) ), '%user', $job.user, '%time', $job.started|l10n( 'shortdatetime' ) ) )|wash}
       <a href={concat( 'migration/log/', $job.id )|ezurl}>{'Follow it'|i18n( 'design/admin/migration' )}</a></p>
</div>
{elseif $lock_text}
<div class="a4-notice is-warn" role="status">
    <p class="a4-notice-title">{'The installation is locked'|i18n( 'design/admin/migration' )}</p>
    <p>{'Another run holds the lock (an upgrade or a run on the console): %holder'|i18n( 'design/admin/migration',, hash( '%holder', $lock_text ) )|wash}</p>
</div>
{/if}
