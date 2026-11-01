{* The notice of content/sharedpreview: above a draft shown through a share link (state active), or the whole page
   of a link that does not work (invalid, expired, revoked, gone). message-warning is the class the site designs give
   their notices. *}
{if eq( $state, 'active' )}
<div class="message-warning exp-sharedpreview" role="note">
    <p><strong>{'Preview of a draft'|i18n( 'kernel/content/sharedpreview' )}</strong>: {'This is not published. Please do not pass the link on.'|i18n( 'kernel/content/sharedpreview' )}</p>
    <p>{'The link works until %time.'|i18n( 'kernel/content/sharedpreview', , hash( '%time', $expires|l10n( 'shortdatetime' ) ) )|wash}</p>
</div>
{else}
<div class="message-warning exp-sharedpreview" role="alert">
    <h1>{'Preview not available'|i18n( 'kernel/content/sharedpreview' )}</h1>
    {switch match=$state}
    {case match='expired'}
    <p>{'This preview link has expired. Ask the person who sent it for a new one.'|i18n( 'kernel/content/sharedpreview' )}</p>
    {/case}
    {case match='revoked'}
    <p>{'This preview link was withdrawn. Ask the person who sent it for a new one.'|i18n( 'kernel/content/sharedpreview' )}</p>
    {/case}
    {case match='gone'}
    <p>{'The draft of this preview link is not there any more: it was published or discarded.'|i18n( 'kernel/content/sharedpreview' )}</p>
    {/case}
    {case}
    <p>{'This is not a working preview link. Check that it was copied in full.'|i18n( 'kernel/content/sharedpreview' )}</p>
    {/case}
    {/switch}
</div>
{/if}
