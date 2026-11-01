{* Where a run is, in words (page/migration_log.js writes the same texts from the data-text-* attributes as it goes).
   Variables: progress (expMigrationProgress::fold(): phase, current, done, total, ended). *}
{if $progress.ended}{if $progress.total}{'%done of %total done'|i18n( 'design/admin/migration',, hash( '%done', $progress.done, '%total', $progress.total ) )|wash}{/if}
{elseif $progress.current}{'Running %id (%index of %total)'|i18n( 'design/admin/migration',, hash( '%id', $progress.current, '%index', $progress.done|inc, '%total', $progress.total ) )|wash}
{elseif eq( $progress.phase, 'caption-pre' )}{'Checking the code and taking a caption before the run'|i18n( 'design/admin/migration' )}
{elseif eq( $progress.phase, 'after-migrations' )}{'The migrations have run; restoring the caption if one failed'|i18n( 'design/admin/migration' )}
{elseif eq( $progress.phase, 'caption-post' )}{'Taking a caption after the run, committing, tagging and pushing'|i18n( 'design/admin/migration' )}
{elseif eq( $progress.phase, 'restore' )}{'Restoring the caption'|i18n( 'design/admin/migration' )}
{elseif $progress.total}{'%done of %total done'|i18n( 'design/admin/migration',, hash( '%done', $progress.done, '%total', $progress.total ) )|wash}
{/if}
