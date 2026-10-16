{* One interface choice with a long, grouped list (uiswitch; the language provider with GroupList[] or AllLanguages,
   doc/features/6.0/ui-switch-languages.md): a native list box with a heading per group and an Apply button, in a POST
   form to uiswitch/set (ezformtoken adds its token).

   A native <select> because the list has 100 and more entries: the browser gives it type-ahead (typing the first
   letters of a name jumps to it), arrow keys, and the screen reader announces the group of each entry. Nothing is
   sent before the button is pressed, so moving through the list never reloads the page. The current choice is
   selected where it first appears. The standard design ends every admin chain, so admin4, admin4l, AdminUI and the classic
   admin show the same markup, with the shared component classes of components.css only.

   Parameters: choice (one entry of fetch( 'uiswitch', 'toolbar' ) with groups), prefix (id prefix, unique on the
   page), back (the local path to come back to, or ''). *}
{def $exp_ui_id = concat( $prefix, '-', $choice.identifier|wash )}
<div class="a4-uiswitch-provider" data-provider="{$choice.identifier|wash}">
<form class="a4-stack" id="{$exp_ui_id}-form" method="post" action={'/uiswitch/set'|ezurl}>
<label class="a4-uiswitch-label a4-meta" for="{$exp_ui_id}-select">{$choice.title|wash}</label>
<input type="hidden" name="Provider" value="{$choice.identifier|wash}" />
{if $back|ne( '' )}<input type="hidden" name="RedirectURI" value="{$back|wash}" />{/if}
<select class="a4-input" id="{$exp_ui_id}-select" name="Value">
{foreach $choice.groups as $exp_ui_group}
<optgroup label="{$exp_ui_group.label|wash}">
{foreach $exp_ui_group.values as $exp_ui_value}
<option value="{$exp_ui_value.value|wash}"{if $exp_ui_value.current} selected="selected"{/if}>{$exp_ui_value.label|wash}</option>
{/foreach}
</optgroup>
{/foreach}
</select>
<button type="submit" class="a4-btn a4-btn-small">{'Apply'|i18n( 'design/admin/toolbar/uiswitch/language' )}</button>
</form>
</div>
{undef $exp_ui_id}
