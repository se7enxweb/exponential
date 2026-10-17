{* The page shown while an object waits in the asynchronous publishing queue (content/queued, content.ini
   [PublishingSettings] AsynchronousPublishing). Variables: $contentObjectId, $version, optional $redirect_uri,
   $content_object, $content_object_version, $content_class.

   ezasynchronouspublishing.js asks for the status and shows one of the placeholders; their ids and classes are the
   ones design/standard/templates/content/queued.tpl documents and must stay: #ezap-error, and .ezap-placeholder
   #ezap-message-publishing, #ezap-message-finished (with a#ezap-contentview-uri) and #ezap-message-deferred (with
   a#ezap-versionview-uri). The configuration is read from #ezap-config by javascript/content_queued.js; the hidden
   placeholders (.ezap-hidden) are in stylesheets/content_queued.css; both come from design/standard. *}
{exp_config()}
{ezscript_require( array( 'ezjsc::jquery', 'exp::core::shared', 'exp::io', 'ezasynchronouspublishing.js' ) )}
{ezcss_require( 'content_queued.css' )}

<div class="a4-page a4-standalone">
<div class="context-block">

<div class="box-header"><div class="box-ml">
<h1 class="context-title">{$content_object.name|wash}</h1>
</div></div>

<div class="box-bc"><div class="box-ml"><div class="box-content">

<div id="ezap-config" hidden="hidden"
     data-contentobject-id="{$contentObjectId|int}"
     data-version="{$version|int}"
     {if is_set( $redirect_uri )}data-redirect-uri="{$redirect_uri|wash}"{/if}
     data-failure-message="{'A fatal error occured while checking the version status, you can try to refresh this page or contact your administrator.'|i18n( 'design/standard/location' )|wash}"
     data-last-checked-message="{'(Checking every %ms%ms and checked %times% times so far)'|i18n( 'design/standard/location' )|wash}"></div>

{* error messages from the queue *}
<div id="ezap-error" class="a4-notice is-bad ezap-hidden" role="alert"></div>

{* while the object is being published *}
<div class="ezap-placeholder a4-notice is-info" id="ezap-message-publishing" role="status">
    <strong class="a4-notice-title">{'Your content is being published'|i18n( 'design/standard/content' )}</strong>
    {'Please wait while your content is being published'|i18n( 'design/standard/location' )}
</div>

{* when the object has been published *}
<div class="ezap-placeholder a4-notice is-ok ezap-hidden" id="ezap-message-finished" role="status">
    <strong class="a4-notice-title">{'Publishing finished'|i18n( 'design/standard/content' )}</strong>
    <p>{'Your content has been published successfully'|i18n( 'design/standard/location' )}</p>
    <p><a id="ezap-contentview-uri" href="#">{'View the published item'|i18n( 'design/standard/location' )}</a></p>
</div>

{* when publishing has been deferred to cron (for instance by an approval workflow) *}
<div class="ezap-placeholder a4-notice is-warn ezap-hidden" id="ezap-message-deferred" role="status">
    <strong class="a4-notice-title">{'Your content is pending an external action'|i18n( 'design/standard/content' )}</strong>
    <p>{'Publishing has been deferred to crontab and will be published when the operation resumes. The object is also listed in your dashboard under pending items.'|i18n( 'design/standard/location' )}</p>
    <p><a id="ezap-versionview-uri" href="#">{'View your pending content'|i18n( 'design/standard/location' )}</a></p>
</div>

</div></div></div>
</div>
</div>
{ezscript( array( 'content_queued.js' ) )}
