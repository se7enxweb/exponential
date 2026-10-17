{* Why a field version is not resolved (contentcheck/fixconfirm.tpl, fixprogress.tpl, missingfiles.tpl of the broken relations). Variable:
   reason. The same file is in design/admin and design/admin4. *}
{switch match=$reason}
{case match='not_found'}{'Not found, or you have no access to it.'|i18n( 'kernel/contentcheck' )}{/case}
{case match='not_allowed'}{'You may not edit this object.'|i18n( 'kernel/contentcheck' )}{/case}
{case match='fixed'}{'Nothing is wrong with it any more.'|i18n( 'kernel/contentcheck' )}{/case}
{case match='no_original'}{'The original file is missing: restore or upload it first.'|i18n( 'kernel/contentcheck' )}{/case}
{case match='no_aliases'}{'This kind of field has no image aliases.'|i18n( 'kernel/contentcheck' )}{/case}
{case match='no_source'}{'No other version of this field still has its file.'|i18n( 'kernel/contentcheck' )}{/case}
{case match='not_missing'}{'Its file is not missing.'|i18n( 'kernel/contentcheck' )}{/case}
{case match='no_file'}{'No file was given, or the file to copy is gone.'|i18n( 'kernel/contentcheck' )}{/case}
{case match='wrong_type'}{'The file must have the same extension as the expected path, and an image must be an image.'|i18n( 'kernel/contentcheck' )}{/case}
{case match='too_large'}{'The file is too large.'|i18n( 'kernel/contentcheck' )}{/case}
{case match='exists'}{'A file is at the expected path now; it was left alone.'|i18n( 'kernel/contentcheck' )}{/case}
{case match='write'}{'The file could not be written to the storage.'|i18n( 'kernel/contentcheck' )}{/case}
{case}{'It could not be done. The debug output says why.'|i18n( 'kernel/contentcheck' )}{/case}
{/switch}
