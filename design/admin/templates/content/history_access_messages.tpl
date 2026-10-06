{* The messages of content/history about access, as notices in the look of the redesigned administration pages: a
   note for an editor who may edit the object but not read it, and what an action of the page did not do
   ($refused: action diff, copy, copy-language, remove or remove-none). The same file is in design/admin and
   design/admin4; design/standard has the plain one. Included by content/history.tpl. *}
{if and( is_set( $can_read ), $can_read|not )}
<div class="exp-feedback is-info history-edit-only" role="note">
    <p><strong>{'You may edit this object, but not read it'|i18n( 'design/admin/content/history' )}</strong></p>
    <p>{'The list shows every version of the object. You can compare and copy its published, archived and rejected versions and your own; a draft or pending version of someone else only with the policy content/versionread for it.'|i18n( 'design/admin/content/history' )}</p>
</div>
{/if}
{if and( is_set( $refused ), $refused )}
{def $refused_versions = $refused.versions|implode( ', ' )}
<div class="exp-feedback is-warn history-refused" role="alert">
    {switch match=$refused.action}
    {case match='diff'}
    <p><strong>{'The versions were not compared'|i18n( 'design/admin/content/history' )}</strong></p>
    <p>{'You may not read version %1.'|i18n( 'design/admin/content/history',, array( $refused_versions ) )|wash}</p>
    {/case}
    {case match='copy'}
    <p><strong>{'Version %1 was not copied'|i18n( 'design/admin/content/history',, array( $refused_versions ) )|wash}</strong></p>
    <p>{'You may not read this version, so you cannot copy it.'|i18n( 'design/admin/content/history' )}</p>
    {/case}
    {case match='copy-language'}
    <p><strong>{'Version %1 was not copied'|i18n( 'design/admin/content/history',, array( $refused_versions ) )|wash}</strong></p>
    <p>{'You may not edit the translation %1.'|i18n( 'design/admin/content/history',, array( $refused.language ) )|wash}</p>
    {/case}
    {case match='remove-none'}
    <p><strong>{'No version was removed'|i18n( 'design/admin/content/history' )}</strong></p>
    <p>{'Tick the versions to remove in the list first.'|i18n( 'design/admin/content/history' )}</p>
    {/case}
    {case}
    <p><strong>{'Some versions were not removed'|i18n( 'design/admin/content/history' )}</strong></p>
    <p>{'Not removed: %1. You may not remove these versions, or they are published or part of a workflow that is still running.'|i18n( 'design/admin/content/history',, array( $refused_versions ) )|wash}</p>
    {/case}
    {/switch}
</div>
{undef $refused_versions}
{/if}
