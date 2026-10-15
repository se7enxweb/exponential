{* uiswitch/set refused a change (kernel/private/classes/views/uiswitch/set.php): reason is method, cross-site,
   invalid, unavailable or denied. *}
<div class="message-warning">
<h2>{'The interface was not changed'|i18n( 'design/standard/uiswitch' )}</h2>
<p>{switch match=$reason}
{case match='method'}{'A change is only accepted from the form of the toolbar.'|i18n( 'design/standard/uiswitch' )}{/case}
{case match='cross-site'}{'The request came from another site.'|i18n( 'design/standard/uiswitch' )}{/case}
{case match='invalid'}{'This choice is not offered here.'|i18n( 'design/standard/uiswitch' )}{/case}
{case match='unavailable'}{'This choice is not available on this site.'|i18n( 'design/standard/uiswitch' )}{/case}
{case}{'You are not allowed to make this choice.'|i18n( 'design/standard/uiswitch' )}{/case}
{/switch}</p>
<p><a href={'/content/dashboard'|ezurl}>{'Back to the dashboard'|i18n( 'design/standard/uiswitch' )}</a></p>
</div>
