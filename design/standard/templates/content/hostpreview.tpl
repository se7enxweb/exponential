{* The page of content/hostpreview when the preview cannot be shown: the one-time token of the preview on the site's
   own host was used already (a reload), has expired, belongs to another siteaccess or names a version that is gone.
   message-warning is the class the site designs give their notices. *}
<div class="message-warning exp-hostpreview" role="alert">
    <h1>{'Preview not available'|i18n( 'kernel/content/hostpreview' )}</h1>
    {switch match=$state}
    {case match='used'}
    <p>{'This preview address works once. Open the preview again in the administration to see the version once more.'|i18n( 'kernel/content/hostpreview' )}</p>
    {/case}
    {case match='expired'}
    <p>{'This preview address has expired. Open the preview again in the administration.'|i18n( 'kernel/content/hostpreview' )}</p>
    {/case}
    {case match='gone'}
    <p>{'The version of this preview is not there any more.'|i18n( 'kernel/content/hostpreview' )}</p>
    {/case}
    {case}
    <p>{'This is not a working preview address.'|i18n( 'kernel/content/hostpreview' )}</p>
    {/case}
    {/switch}
</div>
