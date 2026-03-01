{if is_unset( $attribute_base )}
    {def $attribute_base = 'ContentObjectAttribute'}
{/if}
{def $data_text = cond( is_set( $#collection_attributes[$attribute.id] ), $#collection_attributes[$attribute.id].data_text, $attribute.content )}
{if ne( $label, '' )}
<label for="{$attribute_base}_data_text_{$attribute.id}">{$label}</label>
{/if}
<textarea class="box {$css_class}" type="text" cols="70" name="{$attribute_base}_data_text_{$attribute.id}" rows="{$attribute.contentclass_attribute.data_int1}" placeholder="{$placeholder}">{$data_text|wash( xhtml )}</textarea>
{undef $data_text}
