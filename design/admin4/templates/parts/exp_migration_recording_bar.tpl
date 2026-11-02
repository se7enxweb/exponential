{* The bar "Recording..." (admin4, and admin4l through its fallback; the same file is in design/admin): shown on every
   page of the administration while the signed-in user's own changes are recorded for a migration, or while the
   site-wide recording window is open (then every user's changes are recorded, so every user sees it). Included outside
   every cache-block (page_mainarea.tpl, the pagelayout of design/admin). fetch( 'migration', 'recording' ) reads a small
   file, no query. Stop posts to migration/recording and comes back to this page. The shared .a4-notice.is-bad.no-icon
   with .a4-setup-recording-bar of stylesheets/modules/setup.css, no CSS of its own.
   Guide: doc/eng-us/guides/migrations-generators-and-recording.md *}
{def $exp_recording = fetch( 'migration', 'recording' )}
{if or( $exp_recording.mine, $exp_recording.site )}
<div class="a4-notice is-bad no-icon a4-setup-recording-bar" id="exp-migration-recording-bar" role="status" aria-live="polite">
    <p><span class="a4-dot" aria-hidden="true"></span> <strong>{'Recording…'|i18n( 'design/admin/migration' )}</strong></p>
    <p>{if $exp_recording.mine}{'Your changes are recorded for a migration.'|i18n( 'design/admin/migration' )}{else}{'Every change on this site is recorded for a migration (site-wide window).'|i18n( 'design/admin/migration' )}{/if}</p>
    {if or( $exp_recording.mine, $exp_recording.can_stop_site )}
    {def $exp_recording_id = cond( $exp_recording.mine, $exp_recording.mine.recording_id, $exp_recording.site.recording_id )}
    <form method="post" action={'migration/recording'|ezurl}>
        <input type="hidden" name="StopRecordingID" value="{$exp_recording_id|wash}" />
        <input type="hidden" name="RedirectURI" value="{concat( '/', $module_result.uri|trim( '/' ) )|wash}" />
        <a class="a4-btn a4-btn-small" href={concat( 'migration/recording/', $exp_recording_id )|ezurl}>{'Show'|i18n( 'design/admin/migration' )}</a>
        <button type="submit" class="a4-btn a4-btn-small a4-btn-primary" name="StopButton" value="1">{'Stop recording'|i18n( 'design/admin/migration' )}</button>
    </form>
    {undef $exp_recording_id}
    {/if}
</div>
{/if}
{undef $exp_recording}
