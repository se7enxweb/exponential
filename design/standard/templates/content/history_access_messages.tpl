{* The messages of content/history about access: a note for an editor who may edit the object but not read it, and
   what an action of the page did not do for some versions ($refused: action diff, copy, copy-language or remove).
   Included by content/history.tpl of every design. *}
{if and( is_set( $can_read ), $can_read|not )}
<div class="message-feedback history-edit-only">
    <h2>{'You may edit this object, but not read it'|i18n( 'design/admin/content/history' )}</h2>
    <p>{'The list shows every version of the object. You can compare and copy its published, archived and rejected versions and your own; a draft or pending version of someone else only with the policy content/versionread for it.'|i18n( 'design/admin/content/history' )}</p>
</div>
{/if}
{if and( is_set( $refused ), $refused )}
{def $refused_versions = $refused.versions|implode( ', ' )}
<div class="message-warning history-refused">
    {switch match=$refused.action}
    {case match='diff'}
    <h2><span class="time">[{currentdate()|l10n( shortdatetime )}]</span> {'The versions were not compared'|i18n( 'design/admin/content/history' )}</h2>
    <p>{'You may not read version %1.'|i18n( 'design/admin/content/history',, array( $refused_versions ) )|wash}</p>
    {/case}
    {case match='copy'}
    <h2><span class="time">[{currentdate()|l10n( shortdatetime )}]</span> {'Version %1 was not copied'|i18n( 'design/admin/content/history',, array( $refused_versions ) )|wash}</h2>
    <p>{'You may not read this version, so you cannot copy it.'|i18n( 'design/admin/content/history' )}</p>
    {/case}
    {case match='copy-language'}
    <h2><span class="time">[{currentdate()|l10n( shortdatetime )}]</span> {'Version %1 was not copied'|i18n( 'design/admin/content/history',, array( $refused_versions ) )|wash}</h2>
    <p>{'You may not edit the translation %1.'|i18n( 'design/admin/content/history',, array( $refused.language ) )|wash}</p>
    {/case}
    {case}
    <h2><span class="time">[{currentdate()|l10n( shortdatetime )}]</span> {'Some versions were not removed'|i18n( 'design/admin/content/history' )}</h2>
    <p>{'Not removed: %1. You may not remove these versions, or they are published or part of a workflow that is still running.'|i18n( 'design/admin/content/history',, array( $refused_versions ) )|wash}</p>
    {/case}
    {/switch}
</div>
{undef $refused_versions}
{/if}
