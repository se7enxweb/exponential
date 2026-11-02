{* The notice of a deploy of migrations to remotes (expMigrationDeployNotifier::message()), sent when migration.ini
   [RemoteSettings] Notify[] names its kind (failure, gate, success). Category admin. Plain text only: the layout is
   design:mail/layout_text.tpl, as in the kernel's other notices to administrators.
   Variables: kind (failure, gate, success), deploy_id, state (deployed, gated, rolled-back, out-of-sync, failed,
   refused, transferred), mode, in_sync (yes, no, unknown), user, remotes (list of id, environment, state, problems),
   stage (the stage that waits, 0 for none), stages, errors (list of texts), url (the deploy's page in the
   administration, absolute; '' when unknown), site_name.
   Sets subject. The words of the state are those of Setup > Migrations > Deploys (migration/parts/deploy_state.tpl). *}
{def $dn_subject = 'The deploy %deploy failed on %site'|i18n( 'design/standard/migration/mail',, hash( '%deploy', $deploy_id, '%site', $site_name ) )
     $dn_rows = array()
     $dn_remotes = concat( 'Remotes'|i18n( 'design/admin/migration' ), ':' )
     $dn_paragraphs = array()
     $dn_buttons = array()}
{if eq( $kind, 'gate' )}
    {set $dn_subject = 'The deploy %deploy waits at a gate on %site'|i18n( 'design/standard/migration/mail',, hash( '%deploy', $deploy_id, '%site', $site_name ) )}
{elseif eq( $kind, 'success' )}
    {set $dn_subject = 'The deploy %deploy succeeded on %site'|i18n( 'design/standard/migration/mail',, hash( '%deploy', $deploy_id, '%site', $site_name ) )}
{/if}
{def $dn_state = $state}
{switch match=$state}
{case match='deployed'}{set $dn_state = 'deployed'|i18n( 'design/admin/migration' )}{/case}
{case match='transferred'}{set $dn_state = 'transferred'|i18n( 'design/admin/migration' )}{/case}
{case match='gated'}{set $dn_state = 'waits at a gate'|i18n( 'design/admin/migration' )}{/case}
{case match='rolled-back'}{set $dn_state = 'rolled back'|i18n( 'design/admin/migration' )}{/case}
{case match='out-of-sync'}{set $dn_state = 'out of sync'|i18n( 'design/admin/migration' )}{/case}
{case match='failed'}{set $dn_state = 'failed'|i18n( 'design/admin/migration' )}{/case}
{case match='refused'}{set $dn_state = 'refused'|i18n( 'design/admin/migration' )}{/case}
{case}{/case}
{/switch}
{set $dn_rows = array( hash( 'label', 'Deploy'|i18n( 'design/admin/migration' ), 'value', $deploy_id ),
                       hash( 'label', 'Result'|i18n( 'design/admin/migration' ), 'value', $dn_state ),
                       hash( 'label', 'In sync'|i18n( 'design/admin/migration' ), 'value', $in_sync|i18n( 'design/admin/migration' ) ) )}
{if $stage|gt( 0 )}
    {set $dn_rows = $dn_rows|append( hash( 'label', 'Next stage'|i18n( 'design/admin/migration' ), 'value', '%stage of %stages'|i18n( 'design/admin/migration',, hash( '%stage', $stage, '%stages', $stages ) ) ) )}
{/if}
{foreach $remotes as $dn_remote}
    {set $dn_remotes = concat( $dn_remotes, "\n", '- ', $dn_remote.id, ' (', $dn_remote.environment, '): ', $dn_remote.state )}
    {foreach $dn_remote.problems as $dn_problem}{set $dn_remotes = concat( $dn_remotes, "\n", '  ', $dn_problem|explode( "\n" )|implode( " " ) )}{/foreach}
{/foreach}
{set $dn_paragraphs = array( $dn_remotes )}
{if $errors|count}
    {def $dn_errors = concat( 'Errors'|i18n( 'design/admin/setup/cronjobs' ), ':' )}
    {foreach $errors as $dn_error}{set $dn_errors = concat( $dn_errors, "\n", '- ', $dn_error|explode( "\r" )|implode( " " )|explode( "\n" )|implode( " " ) )}{/foreach}
    {set $dn_paragraphs = $dn_paragraphs|append( $dn_errors )}
    {undef $dn_errors}
{/if}
{if $url|ne( '' )}
    {set $dn_buttons = array( hash( 'label', 'Deploy'|i18n( 'design/admin/migration' ), 'url', $url ) )}
{/if}
{set-block scope=root variable=subject}{$dn_subject}{/set-block}
{include uri='design:mail/layout_text.tpl'
    mail_title=$dn_subject mail_rows=$dn_rows mail_paragraphs=$dn_paragraphs mail_buttons=$dn_buttons
    mail_footer='This mail was sent by %site because migration.ini [RemoteSettings] Notify[] asks for it.'|i18n( 'design/standard/migration/mail',, hash( '%site', $site_name ) )}
{undef $dn_subject $dn_state $dn_rows $dn_remotes $dn_paragraphs $dn_buttons}
