{* The state of a deploy to remotes, or of one remote in it, as text (the words of the deploy notice,
   expMigrationDeployNotifier::stateText()). Variables: state (a deploy: planned, transferred, deployed, gated,
   rolled-back, out-of-sync, failed, refused; a remote: selected, prepared, ready, waiting, sent, verified, applied,
   restored, rolled-back, unchanged, skipped, failed, refused, unreachable, unknown, out-of-sync). *}
{switch match=$state}
{case match='planned'}{'planned'|i18n( 'design/admin/migration' )}{/case}
{case match='transferred'}{'transferred'|i18n( 'design/admin/migration' )}{/case}
{case match='deployed'}{'deployed'|i18n( 'design/admin/migration' )}{/case}
{case match='gated'}{'waits at a gate'|i18n( 'design/admin/migration' )}{/case}
{case match='rolled-back'}{'rolled back'|i18n( 'design/admin/migration' )}{/case}
{case match='out-of-sync'}{'out of sync'|i18n( 'design/admin/migration' )}{/case}
{case match='failed'}{'failed'|i18n( 'design/admin/migration' )}{/case}
{case match='refused'}{'refused'|i18n( 'design/admin/migration' )}{/case}
{case match='selected'}{'selected'|i18n( 'design/admin/migration' )}{/case}
{case match='prepared'}{'prepared'|i18n( 'design/admin/migration' )}{/case}
{case match='ready'}{'ready'|i18n( 'design/admin/migration' )}{/case}
{case match='waiting'}{'waits for its stage'|i18n( 'design/admin/migration' )}{/case}
{case match='sent'}{'files sent'|i18n( 'design/admin/migration' )}{/case}
{case match='verified'}{'verified'|i18n( 'design/admin/migration' )}{/case}
{case match='applied'}{'applied'|i18n( 'design/admin/migration' )}{/case}
{case match='restored'}{'failed, restored itself'|i18n( 'design/admin/migration' )}{/case}
{case match='unchanged'}{'unchanged'|i18n( 'design/admin/migration' )}{/case}
{case match='skipped'}{'not deployed'|i18n( 'design/admin/migration' )}{/case}
{case match='unreachable'}{'not reachable'|i18n( 'design/admin/migration' )}{/case}
{case match='unknown'}{'unknown'|i18n( 'design/admin/migration' )}{/case}
{case}{$state|wash}{/case}
{/switch}
