{* admin4: the parts stand in one row (.a4-content-parts), each label above its field; no colon after a label *}
{default attribute_base=ContentObjectAttribute}
<div class="block a4-content-parts">

{if ne( $attribute_base, 'ContentObjectAttribute' )}
    {def $id_base = concat( 'ezcoa-', $attribute_base, '-', $attribute.contentclassattribute_id, '_', $attribute.contentclass_attribute_identifier )}
{else}
    {def $id_base = concat( 'ezcoa-', $attribute.contentclassattribute_id, '_', $attribute.contentclass_attribute_identifier )}
{/if}


    <div>
        <label for="{$id_base}_hour">{'Hour'|i18n( 'design/standard/content/datatype' )}</label>
        <input id="{$id_base}_hour" class="ezcc-{$attribute.object.content_class.identifier} ezcca-{$attribute.object.content_class.identifier}_{$attribute.contentclass_attribute_identifier}" type="text" name="{$attribute_base}_time_hour_{$attribute.id}" size="3" value="{if $attribute.content.is_valid}{$attribute.content.hour}{/if}" />
    </div>

    <div>
        <label for="{$id_base}_minute">{'Minute'|i18n( 'design/standard/content/datatype' )}</label>
        <input id="{$id_base}_minute" class="ezcc-{$attribute.object.content_class.identifier} ezcca-{$attribute.object.content_class.identifier}_{$attribute.contentclass_attribute_identifier}" type="text" name="{$attribute_base}_time_minute_{$attribute.id}" size="3" value="{if $attribute.content.is_valid}{$attribute.content.minute}{/if}" />
    </div>

    {if $attribute.contentclass_attribute.data_int2|eq(1)}
    <div>
        <label for="{$id_base}_second">{'Second'|i18n( 'design/standard/content/datatype' )}</label>
        <input id="{$id_base}_second" class="ezcc-{$attribute.object.content_class.identifier} ezcca-{$attribute.object.content_class.identifier}_{$attribute.contentclass_attribute_identifier}" type="text" name="{$attribute_base}_time_second_{$attribute.id}" size="3" value="{if $attribute.content.is_valid}{$attribute.content.second}{/if}" />
    </div>
    {/if}


</div>
{/default}