{* The interface choices of the signed-in user in the classic admin design (uiswitch): one POST form per provider
   the user may use on this siteaccess (fetch( 'uiswitch', 'toolbar' )), the display language first.

   No script is needed: every choice is a submit button of a form to uiswitch/set (ezformtoken adds its token), the
   current one marked with aria-current="true" and the primary button look, each language's name in its own lang.
   RedirectURI brings the browser back to this page. Same data contract and form as admin4's uiswitch/choices.tpl,
   which wins on the admin4 chains; only the shared component classes of components.css are used.

   Parameters: choices (the fetch result), prefix (id prefix, unique on the page). *}
{def $exp_ui_prefix = cond( is_set( $prefix ), $prefix, 'uiswitch-classic' )
     $exp_ui_back = cond( is_set( $requested_uri_string ), concat( '/', $requested_uri_string|trim( '/' ) ), '' )}
{foreach $choices as $exp_ui_choice}
{if and( is_set( $exp_ui_choice.groups ), $exp_ui_choice.groups|count )}
{include uri='design:uiswitch/grouped.tpl' choice=$exp_ui_choice prefix=$exp_ui_prefix back=$exp_ui_back}
{else}
<div class="block" data-provider="{$exp_ui_choice.identifier|wash}">
<p class="a4-meta" id="{$exp_ui_prefix}-{$exp_ui_choice.identifier|wash}-label">{$exp_ui_choice.title|wash}</p>
<form id="{$exp_ui_prefix}-{$exp_ui_choice.identifier|wash}-form" method="post" action={'/uiswitch/set'|ezurl} aria-labelledby="{$exp_ui_prefix}-{$exp_ui_choice.identifier|wash}-label">
<input type="hidden" name="Provider" value="{$exp_ui_choice.identifier|wash}" />
{if $exp_ui_back|ne( '' )}<input type="hidden" name="RedirectURI" value="{$exp_ui_back|wash}" />{/if}
<ul class="a4-plain a4-stack">
{foreach $exp_ui_choice.values as $exp_ui_value}
<li><button type="submit" class="a4-btn a4-btn-small{if $exp_ui_value.current} a4-btn-primary{/if}" name="Value" value="{$exp_ui_value.value|wash}"{if $exp_ui_value.lang|ne( '' )} lang="{$exp_ui_value.lang|wash}"{/if}{if $exp_ui_value.current} aria-current="true"{/if}>{$exp_ui_value.label|wash}</button></li>
{/foreach}
</ul>
</form>
</div>
{/if}
{/foreach}
{undef $exp_ui_prefix $exp_ui_back}
