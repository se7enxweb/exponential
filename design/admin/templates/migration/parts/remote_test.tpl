{* The result of Test connection of a remote. Variables: test (expMigrationRemoteAdmin::testConnection(): ok, state,
   version, migrations (done, pending, failed), lock, problems, warnings). *}
{if $test.ok}
<div class="a4-notice is-ok" role="status"><p>{'The remote answered.'|i18n( 'design/admin/migration' )}</p></div>
{else}
<div class="a4-notice is-bad" role="alert"><p>{'The remote cannot be used now.'|i18n( 'design/admin/migration' )}</p>
    <ul>{foreach $test.problems as $problem}<li>{$problem|wash}</li>{/foreach}</ul></div>
{/if}
{foreach $test.warnings as $warning}<div class="a4-notice is-warn" role="status"><p>{$warning|wash}</p></div>{/foreach}
{if $test.version}
<dl class="a4-facts is-rows">
    <dt>{'Kernel version'|i18n( 'design/admin/migration' )}</dt><dd>{$test.version|wash}</dd>
    <dt>{'Migrations'|i18n( 'design/admin/migration' )}</dt><dd>{'%done done, %pending pending, %failed failed'|i18n( 'design/admin/migration',, hash( '%done', $test.migrations.done, '%pending', $test.migrations.pending, '%failed', $test.migrations.failed ) )|wash}</dd>
    <dt>{'Lock'|i18n( 'design/admin/migration' )}</dt><dd>{if $test.lock}<span class="a4-badge is-warn">{'a run holds the lock there'|i18n( 'design/admin/migration' )}</span>{else}{'free'|i18n( 'design/admin/migration' )}{/if}</dd>
</dl>
{/if}
