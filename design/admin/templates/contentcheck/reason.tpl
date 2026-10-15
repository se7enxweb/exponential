{* Why an object is not moved to the trash (contentcheck/confirm.tpl). Variable: reason. *}
{switch match=$reason}
{case match='not_found'}{'Not found, or you have no access to it.'|i18n( 'kernel/contentcheck' )}{/case}
{case match='not_published'}{'It is not published.'|i18n( 'kernel/contentcheck' )}{/case}
{case match='referenced'}{'Another object refers to it now.'|i18n( 'kernel/contentcheck' )}{/case}
{case match='not_removable'}{'You may not remove it, or one of its locations.'|i18n( 'kernel/contentcheck' )}{/case}
{case match='has_children'}{'It has sub-items. Remove it from the content tree if that is meant.'|i18n( 'kernel/contentcheck' )}{/case}
{case match='trash_not_allowed'}{'Its class does not allow the trash.'|i18n( 'kernel/contentcheck' )}{/case}
{case match='pending'}{'It has a version waiting to be published.'|i18n( 'kernel/contentcheck' )}{/case}
{case match='job_lock'}{'A content job is working on it.'|i18n( 'kernel/contentcheck' )}{/case}
{case}{'It cannot be moved.'|i18n( 'kernel/contentcheck' )}{/case}
{/switch}
