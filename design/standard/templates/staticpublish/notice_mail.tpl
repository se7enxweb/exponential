{* The notice of static publishing after a publish or rollback (expStaticNotifier::message()), sent when the target's
   Notify[] asks for it (failure, success). Category admin. Plain text only: the layout is design:mail/layout_text.tpl,
   as in the kernel's other notices to administrators (user/new_signin_admin_mail.tpl), without an HTML part.
   Variables: failed (bool), kind (publish, rollback), target_id, target_name, run_id, state (done, unchanged, failed),
   commit, tag, push_state (pushed, pending, disabled, no-remote, none), public_url, errors (list of texts), log_url
   (the run's log in the administration, absolute; '' when unknown), site_name.
   Sets subject. The words of the result and the push are those of the history in the administration
   (staticpublish/parts/run_state.tpl). *}
{def $sp_target = cond( $target_name|ne( '' ), concat( $target_name, ' (', $target_id, ')' ), $target_id )
     $sp_subject = cond( $failed,
                         'Static publishing of %target failed on %site'|i18n( 'design/standard/staticpublish/mail',, hash( '%target', $sp_target, '%site', $site_name ) ),
                         'Static publishing of %target succeeded on %site'|i18n( 'design/standard/staticpublish/mail',, hash( '%target', $sp_target, '%site', $site_name ) ) )
     $sp_result = 'published'|i18n( 'design/admin/staticpublish' )
     $sp_push = 'not pushed'|i18n( 'design/admin/staticpublish' )
     $sp_rows = array()
     $sp_paragraphs = array()
     $sp_buttons = array()}
{if $failed}
    {set $sp_result = 'failed'|i18n( 'design/admin/staticpublish' )}
{elseif eq( $state, 'unchanged' )}
    {set $sp_result = 'nothing changed'|i18n( 'design/admin/staticpublish' )}
{elseif eq( $kind, 'rollback' )}
    {set $sp_result = 'rolled back'|i18n( 'design/admin/staticpublish' )}
{/if}
{switch match=$push_state}
{case match='pushed'}{set $sp_push = 'pushed'|i18n( 'design/admin/staticpublish' )}{/case}
{case match='pending'}{set $sp_push = 'push pending'|i18n( 'design/admin/staticpublish' )}{/case}
{case match='no-remote'}{set $sp_push = 'no remote'|i18n( 'design/admin/staticpublish' )}{/case}
{case}{/case}
{/switch}
{set $sp_rows = array( hash( 'label', 'Target'|i18n( 'design/admin/staticpublish' ), 'value', $sp_target ),
                       hash( 'label', 'Id'|i18n( 'design/admin/staticpublish' ), 'value', $run_id ),
                       hash( 'label', 'Result'|i18n( 'design/admin/migration' ), 'value', $sp_result ) )}
{if and( $failed, eq( $state, 'unchanged' ) )}
    {set $sp_rows = $sp_rows|append( hash( 'label', 'Changes'|i18n( 'design/admin/staticpublish' ), 'value', 'nothing changed'|i18n( 'design/admin/staticpublish' ) ) )}
{/if}
{if $commit|ne( '' )}
    {set $sp_rows = $sp_rows|append( hash( 'label', 'Commit'|i18n( 'design/admin/staticpublish' ), 'value', $commit ) )}
{/if}
{if $tag|ne( '' )}
    {set $sp_rows = $sp_rows|append( hash( 'label', 'Tag'|i18n( 'design/admin/staticpublish' ), 'value', $tag ) )}
{/if}
{set $sp_rows = $sp_rows|append( hash( 'label', 'Push'|i18n( 'design/admin/staticpublish' ), 'value', $sp_push ) )}
{if $public_url|ne( '' )}
    {set $sp_rows = $sp_rows|append( hash( 'label', 'Public address'|i18n( 'design/admin/staticpublish' ), 'value', $public_url ) )}
{/if}
{if $errors|count}
    {def $sp_errors = concat( 'Errors'|i18n( 'design/admin/setup/cronjobs' ), ':' )}
    {foreach $errors as $sp_error}{set $sp_errors = concat( $sp_errors, "\n", '- ', $sp_error|explode( "\r" )|implode( " " )|explode( "\n" )|implode( " " ) )}{/foreach}
    {set $sp_paragraphs = array( $sp_errors )}
    {undef $sp_errors}
{/if}
{if $log_url|ne( '' )}
    {set $sp_buttons = array( hash( 'label', 'Log'|i18n( 'design/admin/content/job' ), 'url', $log_url ) )}
{/if}
{set-block scope=root variable=subject}{$sp_subject}{/set-block}
{include uri='design:mail/layout_text.tpl'
    mail_title=$sp_subject mail_rows=$sp_rows mail_paragraphs=$sp_paragraphs mail_buttons=$sp_buttons
    mail_footer='This mail was sent by %site because the static publishing target %target asks for it.'|i18n( 'design/standard/staticpublish/mail',, hash( '%site', $site_name, '%target', $target_id ) )}
{undef $sp_target $sp_subject $sp_result $sp_push $sp_rows $sp_paragraphs $sp_buttons}
