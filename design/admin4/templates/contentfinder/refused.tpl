{* A request to contentfinder/action that was refused before anything was looked at: not a POST (405), or without
   the form tokens (403). Variable: reason (method, token, no_policy, unknown_action, not_found). *}
<div class="context-block a4-page a4-contentfinder" id="exp-contentfinder-refused">
{* DESIGN: Header START *}<div class="box-header"><div class="box-ml">
<h1 class="context-title">{'Content finder'|i18n( 'kernel/contentfinder' )}</h1>
{* DESIGN: Mainline *}<div class="header-mainline"></div>
{* DESIGN: Header END *}</div></div>
{* DESIGN: Content START *}<div class="box-ml"><div class="box-mr"><div class="box-content">
<div class="message-error" role="alert"><h2>{'The action was refused.'|i18n( 'kernel/contentfinder' )} {include uri='design:contentfinder/reason.tpl' reason=$reason}</h2></div>
<p><a href={'contentfinder/search'|ezurl}>{'Back to the finder'|i18n( 'kernel/contentfinder' )}</a></p>
{* DESIGN: Content END *}</div></div></div>
</div>
