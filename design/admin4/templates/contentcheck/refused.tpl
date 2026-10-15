{* A refused request to contentcheck/trash. Variable: reason (method, token). The same file is in design/admin and
   design/admin4. *}
<div class="context-block a4-page a4-contentcheck">

<div class="box-header"><div class="box-ml">
<h1 class="context-title">{'Move to the trash'|i18n( 'kernel/contentcheck' )}</h1>
</div></div>

<div class="box-bc"><div class="box-ml"><div class="box-content">
<div class="a4-notice is-warn" role="alert">{if eq( $reason, 'method' )}{'This page only takes the form of the list of unreferenced objects.'|i18n( 'kernel/contentcheck' )}{else}{'The form has expired. Open the list again and tick the objects once more.'|i18n( 'kernel/contentcheck' )}{/if}</div>
<p><a class="a4-btn" href={'/contentcheck/unreferenced'|ezurl}>{'Back to the list'|i18n( 'kernel/contentcheck' )}</a></p>
</div></div></div>
</div>
