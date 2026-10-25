{* The mail of the workflow event "Approve user accounts" to each approver (doc/eng-us/bc/6.0/user-account-approval.md):
   a new account waits for approval. It carries the link to the approval item in the administration and nothing that
   approves or denies: the approver signs in and decides there.

   Variables: approver (eZUser), requester (eZUser, who published), object (the account's content object), account
   (its eZUser), group_names (the groups it would be placed in), approval_url (the item's address), site_name. The
   subject, content_type (text/html) and body_text (the plain-text part) are set in set-blocks, as in
   user/unactivated_resend_mail.tpl; the look is design:mail/layout.tpl and design:mail/layout_text.tpl. *}
{def $ua_name = first_set( $account_name, $object.name, "" )
     $ua_requester = cond( is_set( $requester.contentobject ), $requester.contentobject.name, '' )
     $ua_groups = $group_names|implode( ', ' )
     $ua_rows = array()
     $ua_buttons = array()
     $ua_note = ''}
{set-block scope=root variable=subject}{'New user account waiting for approval: %name'|i18n( 'design/standard/workflow/eventtype/expuserapprove',, hash( '%name', $ua_name ) )}{/set-block}
{set-block scope=root variable=content_type}text/html{/set-block}
{set $ua_rows = array( hash( 'label', 'Account'|i18n( 'design/standard/workflow/eventtype/expuserapprove' ), 'value', $ua_name ),
                       hash( 'label', 'Username'|i18n( 'design/standard/workflow/eventtype/expuserapprove' ), 'value', cond( is_set( $account.login ), $account.login, '' ) ),
                       hash( 'label', 'Group'|i18n( 'design/standard/workflow/eventtype/expuserapprove' ), 'value', $ua_groups ),
                       hash( 'label', 'Requested by'|i18n( 'design/standard/workflow/eventtype/expuserapprove' ), 'value', $ua_requester ) )
     $ua_buttons = array( hash( 'label', 'Open the approval'|i18n( 'design/standard/workflow/eventtype/expuserapprove' ), 'url', $approval_url, 'kind', 'primary' ) )
     $ua_note = 'If the button does not work, copy this address into your browser: %url'|i18n( 'design/standard/workflow/eventtype/expuserapprove', '', hash( '%url', $approval_url ) )}
{set-block scope=root variable=body_text}{include uri='design:mail/layout_text.tpl'
    mail_title='A new user account waits for your approval'|i18n( 'design/standard/workflow/eventtype/expuserapprove' )
    mail_intro='%requester created the account "%name" in %groups. It is not published and cannot sign in until an approver accepts it.'|i18n( 'design/standard/workflow/eventtype/expuserapprove',, hash( '%requester', $ua_requester, '%name', $ua_name, '%groups', $ua_groups ) )
    mail_rows=$ua_rows
    mail_notice='Sign in to the administration and approve or deny it in your collaboration list. This link does not sign you in and decides nothing by itself.'|i18n( 'design/standard/workflow/eventtype/expuserapprove' )
    mail_buttons=$ua_buttons
    mail_links_note=$ua_note
    mail_footer='This mail was sent by %site because you approve new accounts of these groups.'|i18n( 'design/standard/workflow/eventtype/expuserapprove', '', hash( '%site', $site_name ) )}{/set-block}
{include uri='design:mail/layout.tpl'
    mail_title='A new user account waits for your approval'|i18n( 'design/standard/workflow/eventtype/expuserapprove' )
    mail_intro='%requester created the account "%name" in %groups. It is not published and cannot sign in until an approver accepts it.'|i18n( 'design/standard/workflow/eventtype/expuserapprove',, hash( '%requester', $ua_requester, '%name', $ua_name, '%groups', $ua_groups ) )
    mail_rows=$ua_rows
    mail_notice='Sign in to the administration and approve or deny it in your collaboration list. This link does not sign you in and decides nothing by itself.'|i18n( 'design/standard/workflow/eventtype/expuserapprove' )
    mail_buttons=$ua_buttons
    mail_links_note=$ua_note
    mail_footer='This mail was sent by %site because you approve new accounts of these groups.'|i18n( 'design/standard/workflow/eventtype/expuserapprove', '', hash( '%site', $site_name ) )}
{undef $ua_name $ua_requester $ua_groups $ua_rows $ua_buttons $ua_note}
