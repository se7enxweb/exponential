{* Why a content finder action was not done or is not offered. Variable: reason (a reason of
   expContentFinderAction::plan() or execute(), or of the action view: method, token). *}
{switch match=$reason}
{case match='not_found'}{'Nothing found, or you have no access to what was found.'|i18n( 'kernel/contentfinder' )}{/case}
{case match='no_policy'}{'You do not have the policies this action needs.'|i18n( 'kernel/contentfinder' )}{/case}
{case match='not_removable'}{'You may not remove every location of this object and everything below it.'|i18n( 'kernel/contentfinder' )}{/case}
{case match='trash_not_allowed'}{'This object cannot go to the trash (it is a user or has users below it); it can only be deleted permanently.'|i18n( 'kernel/contentfinder' )}{/case}
{case match='too_many'}{'There are too many nodes below it for one removal here; remove it from the content structure, where a large removal runs in the background.'|i18n( 'kernel/contentfinder' )}{/case}
{case match='pending'}{'Something below it is waiting for approval; remove it once that is decided.'|i18n( 'kernel/contentfinder' )}{/case}
{case match='job_lock'}{'This part of the content tree is being changed by a background job. Try again when the job is done.'|i18n( 'kernel/contentfinder' )}{/case}
{case match='not_in_trash'}{'The object is not in the trash.'|i18n( 'kernel/contentfinder' )}{/case}
{case match='version_not_removable'}{'This version cannot be removed: it is the published one, a workflow still holds it, or you may not remove it.'|i18n( 'kernel/contentfinder' )}{/case}
{case match='changed'}{'The object changed after the confirmation page was shown. Check it again.'|i18n( 'kernel/contentfinder' )}{/case}
{case match='confirm_permanent'}{'Tick the box to confirm that this cannot be undone.'|i18n( 'kernel/contentfinder' )}{/case}
{case match='method'}{'An action can only be sent from a form of the finder.'|i18n( 'kernel/contentfinder' )}{/case}
{case match='token'}{'The form has expired or did not come from this site. Open the finder again.'|i18n( 'kernel/contentfinder' )}{/case}
{case match='unknown_action'}{'This is not an action of the finder.'|i18n( 'kernel/contentfinder' )}{/case}
{case}{'The action failed. The error log has the details.'|i18n( 'kernel/contentfinder' )}{/case}
{/switch}
