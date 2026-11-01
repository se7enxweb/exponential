{* A state or an action as text. Variables: state (done, pending, not-needed, failed, unknown, running, rolled-back, or an
   action: applied, would-apply, undone, would-undo, not-run, blocked, refused, accepted, would-accept, simulated). *}
{switch match=$state}
{case match='done'}{'done'|i18n( 'design/admin/migration' )}{/case}
{case match='pending'}{'pending'|i18n( 'design/admin/migration' )}{/case}
{case match='not-needed'}{'not needed'|i18n( 'design/admin/migration' )}{/case}
{case match='failed'}{'failed'|i18n( 'design/admin/migration' )}{/case}
{case match='unknown'}{'unknown'|i18n( 'design/admin/migration' )}{/case}
{case match='running'}{'running'|i18n( 'design/admin/migration' )}{/case}
{case match='rolled-back'}{'rolled back'|i18n( 'design/admin/migration' )}{/case}
{case match='applied'}{'applied'|i18n( 'design/admin/migration' )}{/case}
{case match='would-apply'}{'would be applied'|i18n( 'design/admin/migration' )}{/case}
{case match='undone'}{'taken back'|i18n( 'design/admin/migration' )}{/case}
{case match='would-undo'}{'would be taken back'|i18n( 'design/admin/migration' )}{/case}
{case match='not-run'}{'not run'|i18n( 'design/admin/migration' )}{/case}
{case match='blocked'}{'waits for another'|i18n( 'design/admin/migration' )}{/case}
{case match='refused'}{'refused'|i18n( 'design/admin/migration' )}{/case}
{case match='accepted'}{'change accepted'|i18n( 'design/admin/migration' )}{/case}
{case match='would-accept'}{'change would be accepted'|i18n( 'design/admin/migration' )}{/case}
{case match='simulated'}{'simulated'|i18n( 'design/admin/migration' )}{/case}
{case}{$state|wash}{/case}
{/switch}
