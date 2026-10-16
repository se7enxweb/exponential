{* The interface choices of the signed-in user (uiswitch, doc/features/6.0/ui-switch.md): one disclosure per provider
   the user may use on this siteaccess (fetch( 'uiswitch', 'toolbar' )), the display language first.

   The disclosure follows the header menus (admin4.js disclosure(), the menu pattern of #165): a button with
   aria-expanded and aria-controls opens the list below it; arrow keys, Home and End move through the choices, Escape
   and the focus leaving close it. The button stays hidden without the script, and the list is then shown open.
   Each choice is a submit button of a POST form to uiswitch/set (ezformtoken adds its token); the current one carries
   aria-current="true" and each language's name its own lang. RedirectURI brings the browser back to this page.

   Parameters: choices (the fetch result), prefix (id prefix, unique on the page). *}
{def $exp_ui_prefix = cond( is_set( $prefix ), $prefix, 'a4-uiswitch' )
     $exp_ui_back = cond( is_set( $requested_uri_string ), concat( '/', $requested_uri_string|trim( '/' ) ), '' )}
{foreach $choices as $exp_ui_choice}
{if and( is_set( $exp_ui_choice.groups ), $exp_ui_choice.groups|count )}
{include uri='design:uiswitch/grouped.tpl' choice=$exp_ui_choice prefix=$exp_ui_prefix back=$exp_ui_back}
{else}
<div class="a4-uiswitch-provider" data-provider="{$exp_ui_choice.identifier|wash}">
<span class="a4-uiswitch-label" id="{$exp_ui_prefix}-{$exp_ui_choice.identifier|wash}-label">{$exp_ui_choice.title|wash}</span>
<button type="button" class="a4-uiswitch-toggle" id="{$exp_ui_prefix}-{$exp_ui_choice.identifier|wash}-toggle" data-a4-disclosure="" aria-expanded="false" aria-controls="{$exp_ui_prefix}-{$exp_ui_choice.identifier|wash}-menu" aria-describedby="{$exp_ui_prefix}-{$exp_ui_choice.identifier|wash}-label" hidden><span class="a4-uiswitch-current">{$exp_ui_choice.current_label|wash}</span></button>
<form class="a4-uiswitch-menu" id="{$exp_ui_prefix}-{$exp_ui_choice.identifier|wash}-menu" method="post" action={'/uiswitch/set'|ezurl} aria-labelledby="{$exp_ui_prefix}-{$exp_ui_choice.identifier|wash}-label">
<input type="hidden" name="Provider" value="{$exp_ui_choice.identifier|wash}" />
{if $exp_ui_back|ne( '' )}<input type="hidden" name="RedirectURI" value="{$exp_ui_back|wash}" />{/if}
<ul class="a4-uiswitch-list">
{foreach $exp_ui_choice.values as $exp_ui_value}
<li><button type="submit" class="a4-uiswitch-option" name="Value" value="{$exp_ui_value.value|wash}"{if $exp_ui_value.lang|ne( '' )} lang="{$exp_ui_value.lang|wash}"{/if}{if $exp_ui_value.current} aria-current="true"{/if}>{$exp_ui_value.label|wash}</button></li>
{/foreach}
</ul>
</form>
</div>
{/if}
{/foreach}
{undef $exp_ui_prefix $exp_ui_back}
