{* A refused request to contentcheck/storageaction. Variables: reason (method, token, policy), node_id. The same
   file is in design/admin and design/admin4. *}
<div class="context-block a4-page a4-contentcheck">

<div class="box-header"><div class="box-ml">
<h1 class="context-title">{'Storage files'|i18n( 'kernel/contentcheck' )}</h1>
</div></div>

<div class="box-bc"><div class="box-ml"><div class="box-content">
<div class="a4-notice is-warn" role="alert">{if eq( $reason, 'method' )}{'This page only takes the forms of the storage files.'|i18n( 'kernel/contentcheck' )}{elseif eq( $reason, 'policy' )}{'You may not do this with storage files.'|i18n( 'kernel/contentcheck' )}{else}{'The form has expired. Open the list again and tick the files once more.'|i18n( 'kernel/contentcheck' )}{/if}</div>
<p><a class="a4-btn" href={concat( '/contentcheck/storage/', $node_id )|ezurl}>{'Back to the list'|i18n( 'kernel/contentcheck' )}</a></p>
</div></div></div>
</div>
