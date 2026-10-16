{* Default preview template for admin interface. *}
{* Will be used if there is no suitable override for a specific class. *}

{* Display all the attributes using their default template. *}
{section var=Attributes loop=$node.object.contentobject_attributes}
    <div class="block">
    {if $Attributes.item.display_info.view.grouped_input}
    <fieldset>
        <legend>{$Attributes.item.contentclass_attribute.name|wash}{if $Attributes.item.is_information_collector} <span class="collector">({'information collector'|i18n( 'design/admin/content/edit_attribute' )})</span>{/if}</legend>
        {set-block variable=exp_text_attrs}{include uri='design:parts/exp_content_direction.tpl' exp_locale_code=$Attributes.item.language_code}{/set-block}{if $exp_text_attrs|ne( '' )}<div class="exp-content-text"{$exp_text_attrs}>{/if}
        {attribute_view_gui attribute=$Attributes.item}
        {if $exp_text_attrs|ne( '' )}</div>{/if}{undef $exp_text_attrs}
    </fieldset>
    {else}
        <label>{$Attributes.item.contentclass_attribute.name|wash}{if $Attributes.item.is_information_collector} <span class="collector">({'information collector'|i18n( 'design/admin/content/edit_attribute' )})</span>{/if}:</label>
        {set-block variable=exp_text_attrs}{include uri='design:parts/exp_content_direction.tpl' exp_locale_code=$Attributes.item.language_code}{/set-block}{if $exp_text_attrs|ne( '' )}<div class="exp-content-text"{$exp_text_attrs}>{/if}
        {attribute_view_gui attribute=$Attributes.item}
        {if $exp_text_attrs|ne( '' )}</div>{/if}{undef $exp_text_attrs}
    {/if}
    </div>
{/section}
