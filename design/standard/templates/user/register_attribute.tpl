{* One field of the sign-up form (user/register, every design that includes it). Param: attribute.
   - Attributes of the class's "meta" category (Meta Data and other editorial settings) are not shown: a visitor who
     signs up has nothing to enter there.
   - The user account (ezuser) gets no heading of its own: username, e-mail and passwords carry their own labels;
     register=true() leaves out the editor's User ID and account status (content/datatype/edit/ezuser.tpl).
   - An image (ezimage) is one plain upload field with its description, without the editor's "There is no image file"
     line, remove button and image tools. *}
{if $attribute.contentclass_attribute.category|ne( 'meta' )}
<input type="hidden" name="ContentObjectAttribute_id[]" value="{$attribute.id}" />
{if eq( $attribute.data_type_string, 'ezimage' )}
<div class="block register-image">
    <label for="register-image-{$attribute.id}">{$attribute.contentclass_attribute.name|wash}{if $attribute.is_required} <span class="required">*</span>{else} <span class="register-optional">({'optional'|i18n( 'design/standard/rss/edit' )})</span>{/if}</label>
    {if $attribute.has_content}<div class="register-image-current">{attribute_view_gui attribute=$attribute image_class='small'}</div>{/if}
    <input type="file" id="register-image-{$attribute.id}" name="ContentObjectAttribute_data_imagename_{$attribute.id}" accept="image/jpeg,image/png,image/webp,image/gif" />
    <label for="register-image-alt-{$attribute.id}">{'Alternative image text'|i18n( 'design/standard/content/datatype' )}</label>
    <input type="text" class="box" id="register-image-alt-{$attribute.id}" name="ContentObjectAttribute_data_imagealttext_{$attribute.id}" maxlength="255" value="{$attribute.content.alternative_text|wash}" />
</div>
{elseif eq( $attribute.data_type_string, 'ezuser' )}
<div class="block">
    {attribute_edit_gui attribute=$attribute register=true()}
</div>
{else}
<div class="block">
    <label>{$attribute.contentclass_attribute.name|wash}{if $attribute.is_required} <span class="required">*</span>{/if}</label><div class="labelbreak"></div>
    {attribute_edit_gui attribute=$attribute}
</div>
{/if}
{/if}
