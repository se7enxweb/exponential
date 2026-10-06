{* Shown instead of the class edit form while someone else edits the class.

   Says who and until when; Retry (RetryButton) asks again, Cancel (CancelConflictButton) leaves. The same file is in
   design/admin and design/admin4. *}
{include uri='design:class/exp_style.tpl'}

<form action={concat( 'class/edit/', $class.id )|ezurl} method="post" name="ClassEdit" class="exp-lists exp-classgroups exp-standalone">
<div class="context-block">

<div class="box-header"><div class="box-ml">
<h1 class="context-title">{'Edit <%class_name> [Class]'|i18n( 'design/admin/class/edit_denied',, hash( '%class_name', $class.name ) )|wash}</h1>
</div></div>

<div class="box-bc"><div class="box-ml"><div class="box-content">

<div class="exp-feedback is-warn" role="status">
    <p><strong>{'Class edit conflict'|i18n( 'design/admin/class/edit_denied' )}</strong></p>
    <p>{'This class is already being edited by someone else.'|i18n( 'design/admin/class/edit_denied' )|wash} {'The class is temporarily locked and thus it cannot be edited by you.'|i18n( 'design/admin/class/edit_denied' )}</p>
    <p>{'The class will be available for editing after it has been stored by the current modifier or when it is unlocked by the system.'|i18n( 'design/admin/class/edit_denied' )}</p>
</div>

<div class="exp-panel">
<dl class="exp-facts">
    <div><dt>{'Class'|i18n( 'design/admin/class/edit_denied' )}</dt><dd>{$class.name|wash}</dd></div>
    <div><dt>{'Current modifier'|i18n( 'design/admin/class/edit_denied' )}</dt><dd>{if $class.modifier.contentobject}<a href={$class.modifier.contentobject.main_node.url_alias|ezurl}>{$class.modifier.contentobject.name|wash}</a>{/if}</dd></div>
    <div><dt>{'Unlock time'|i18n( 'design/admin/class/edit_denied' )}</dt><dd>{sum( $class.modified, $lock_timeout )|l10n( shortdatetime )}</dd></div>
</dl>
<p class="exp-help" style="margin-top: 10px;">{'Possible actions'|i18n( 'design/admin/class/edit_denied' )}: {'Contact the person who is editing the class.'|i18n( 'design/admin/class/edit_denied' )} {'Wait until the lock expires and try again.'|i18n( 'design/admin/class/edit_denied' )}</p>
</div>

<div class="exp-bottombar">
    <div class="exp-actions">
        <button class="exp-btn exp-btn-primary" type="submit" name="RetryButton" value="1">{'Retry'|i18n( 'design/admin/class/edit_denied' )}</button>
        <button class="exp-btn" type="submit" name="CancelConflictButton" value="1">{'Cancel'|i18n( 'design/admin/class/edit_denied' )}</button>
    </div>
</div>

</div></div></div>
</div>
</form>
