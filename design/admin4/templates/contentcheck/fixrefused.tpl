{* A refused request to contentcheck/fixfiles. Variables: reason (method, token), node_id. The same file is in
   design/admin and design/admin4. *}
<div class="context-block a4-page a4-contentcheck">

<div class="box-header"><div class="box-ml">
<h1 class="context-title">{'Resolve missing files'|i18n( 'kernel/contentcheck' )}</h1>
</div></div>

<div class="box-bc"><div class="box-ml"><div class="box-content">
<div class="a4-notice is-warn" role="alert">{if eq( $reason, 'method' )}{'This page only takes the form of the missing files of the broken relations.'|i18n( 'kernel/contentcheck' )}{else}{'The form has expired. Open the list again and choose the rows once more.'|i18n( 'kernel/contentcheck' )}{/if}</div>
<p><a class="a4-btn" href={$back_uri|ezurl}>{'Back to the list'|i18n( 'kernel/contentcheck' )}</a></p>
</div></div></div>
</div>
