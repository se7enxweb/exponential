{* admin4: no colon after a label; a heading without its own control is an .a4-label paragraph; the list's columns
   name its fields (th scope, aria-labelledby), the empty list is an .a4-empty *}
{default attribute_base=ContentObjectAttribute}

<div class="block">
<label for="ezcoa-{if ne( $attribute_base, 'ContentObjectAttribute' )}{$attribute_base}-{/if}{$attribute.contentclassattribute_id}_{$attribute.contentclass_attribute_identifier}">{'Name'|i18n( 'design/standard/content/datatype' )}</label>
<input id="ezcoa-{if ne( $attribute_base, 'ContentObjectAttribute' )}{$attribute_base}-{/if}{$attribute.contentclassattribute_id}_{$attribute.contentclass_attribute_identifier}" class="box ezcc-{$attribute.object.content_class.identifier} ezcca-{$attribute.object.content_class.identifier}_{$attribute.contentclass_attribute_identifier}" type="text" name="{$attribute_base}_data_option_name_{$attribute.id}" value="{$attribute.content.name|wash()}" />
</div>

<div class="block">
<p class="a4-label">{'Options'|i18n( 'design/standard/content/datatype' )}</p>

{section show=$attribute.content.option_list}
<table class="list" cellspacing="0">
<tr>
    <th scope="col" class="tight"><span class="a4-sr-only">{'Remove'|i18n( 'design/standard/content/datatype' )}</span></th>
    <th scope="col" id="ezcoa-{$attribute.id}_option_head">{'Option'|i18n( 'design/standard/content/datatype' )}</th>
    {if $attribute.is_information_collector|not}
    <th scope="col" id="ezcoa-{$attribute.id}_price_head">{'Additional price'|i18n( 'design/standard/content/datatype' )}</th>
    {/if}
</tr>

{section var=Options loop=$attribute.content.option_list sequence=array( bglight, bgdark )}
<tr class="{$Options.sequence}">

{* Remove. *}
<td>
<input id="ezcoa-{if ne( $attribute_base, 'ContentObjectAttribute' )}{$attribute_base}-{/if}{$attribute.contentclassattribute_id}_{$attribute.contentclass_attribute_identifier}_remove_{$Options.index}" class="ezcc-{$attribute.object.content_class.identifier} ezcca-{$attribute.object.content_class.identifier}_{$attribute.contentclass_attribute_identifier}" type="checkbox" name="{$attribute_base}_data_option_remove_{$attribute.id}[]" value="{$Options.item.id}" title="{'Select option for removal.'|i18n( 'design/standard/content/datatype' )}" aria-label="{'Select option for removal.'|i18n( 'design/standard/content/datatype' )|wash}" />
<input type="hidden" name="{$attribute_base}_data_option_id_{$attribute.id}[]" value="{$Options.item.id}" />
</td>

{* Option. *}
<td><input id="ezcoa-{if ne( $attribute_base, 'ContentObjectAttribute' )}{$attribute_base}-{/if}{$attribute.contentclassattribute_id}_{$attribute.contentclass_attribute_identifier}_{$Options.index}" class="box ezcc-{$attribute.object.content_class.identifier} ezcca-{$attribute.object.content_class.identifier}_{$attribute.contentclass_attribute_identifier}" type="text" aria-labelledby="ezcoa-{$attribute.id}_option_head" name="{$attribute_base}_data_option_value_{$attribute.id}[]" value="{$Options.item.value|wash()}" /></td>

{if $attribute.is_information_collector|not}
{* Price. *}
<td><input id="ezcoa-{if ne( $attribute_base, 'ContentObjectAttribute' )}{$attribute_base}-{/if}{$attribute.contentclassattribute_id}_{$attribute.contentclass_attribute_identifier}_{$Options.index}" class="box ezcc-{$attribute.object.content_class.identifier} ezcca-{$attribute.object.content_class.identifier}_{$attribute.contentclass_attribute_identifier}" type="text" aria-labelledby="ezcoa-{$attribute.id}_price_head" name="{$attribute_base}_data_option_additional_price_{$attribute.id}[]" value="{$Options.item.additional_price|wash}" /></td>
{/if}

</tr>
{/section}

</table>
{section-else}
<p class="a4-empty">{'There are no options.'|i18n( 'design/standard/content/datatype' )}</p>
{/section}


{if $attribute.content.option_list}
<input id="ezcoa-{if ne( $attribute_base, 'ContentObjectAttribute' )}{$attribute_base}-{/if}{$attribute.contentclassattribute_id}_{$attribute.contentclass_attribute_identifier}_remove_selected" class="button ezcc-{$attribute.object.content_class.identifier} ezcca-{$attribute.object.content_class.identifier}_{$attribute.contentclass_attribute_identifier}" type="submit" name="CustomActionButton[{$attribute.id}_remove_selected]" value="{'Remove selected'|i18n('design/standard/content/datatype')}" title="{'Remove selected options.'|i18n( 'design/standard/content/datatype' )}" />
{else}
<input class="button-disabled" type="submit" name="CustomActionButton[{$attribute.id}_remove_selected]" value="{'Remove selected'|i18n('design/standard/content/datatype')}" title="{'Remove selected options.'|i18n( 'design/standard/content/datatype' )}" disabled="disabled" />
{/if}

<input id="ezcoa-{if ne( $attribute_base, 'ContentObjectAttribute' )}{$attribute_base}-{/if}{$attribute.contentclassattribute_id}_{$attribute.contentclass_attribute_identifier}_new_option" class="button ezcc-{$attribute.object.content_class.identifier} ezcca-{$attribute.object.content_class.identifier}_{$attribute.contentclass_attribute_identifier}" type="submit" name="CustomActionButton[{$attribute.id}_new_option]" value="{'Add option'|i18n('design/standard/content/datatype')}" title="{'Add a new option.'|i18n( 'design/standard/content/datatype' )}" />

</div>
{/default}