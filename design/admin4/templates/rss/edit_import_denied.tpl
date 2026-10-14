{* Shown instead of rss/edit_import while another user edits the same import (content.ini DraftTimeout).

   Says who holds it and until when; Retry asks again (RetryButton, as before), Back goes to the RSS list. The same
   file is in design/admin and design/admin4, in the look of the RSS pages. Guide: doc/guides/rss-feeds.md *}

<form action={concat( '/rss/edit_import/', $rss_import.id )|ezurl} method="post" class="a4-page exp-rss a4-standalone">
<div class="context-block">
<div class="box-header"><div class="box-ml">
<h1 class="context-title">{'RSS import is locked'|i18n( 'design/standard/rss' )}</h1>
</div></div>
<div class="box-bc"><div class="box-ml"><div class="box-content">

<div class="a4-notice is-warn" role="status">
<p>{'The RSS import %name is currently locked by %user and was last modified on %datetime.'|i18n( 'design/standard/rss',,
    hash( '%name', $rss_import.name|wash,
          '%user', $rss_import.modifier.contentobject.name|wash,
          '%datetime', $rss_import.modified|l10n( shortdatetime ) ) )}</p>
<p>{'The RSS import will be available for editing once it is stored by the modifier or when it is automatically unlocked on %datetime.'|i18n( 'design/standard/rss',,
    hash( '%datetime', sum( $rss_import.modified, $lock_timeout )|l10n( shortdatetime ) ) )}</p>
</div>

<div class="a4-toolbar is-bottom">
    <div class="a4-actions">
        <button class="a4-btn a4-btn-primary" type="submit" name="RetryButton" value="1">{'Retry'|i18n( 'design/standard/rss' )}</button>
        <a class="a4-btn" href={'rss/list'|ezurl}>{'Back to the RSS list'|i18n( 'design/admin/rss/list' )}</a>
    </div>
</div>

</div></div></div>
</div>
</form>
