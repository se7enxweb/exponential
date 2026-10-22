{default $view_parameters            = array()
         $attribute_categorys        = ezini( 'ClassAttributeSettings', 'CategoryList', 'content.ini' )
         $attribute_default_category = ezini( 'ClassAttributeSettings', 'DefaultCategory', 'content.ini' )}
{def $exp_attribute_templates = cond( ezini_hasvariable( 'EditSettings', 'AttributeTemplates', 'content.ini' ), ezini( 'EditSettings', 'AttributeTemplates', 'content.ini' ), array() )}

{foreach $content_attributes_grouped_data_map as $attribute_group => $content_attributes_grouped}
{if $attribute_group|ne( $attribute_default_category )}
    <fieldset class="ezcca-collapsible ezcca-attributes-group-{$attribute_group|wash}">
    <legend><a>{$attribute_categorys[$attribute_group]|wash}</a></legend>
    <div class="ezcca-collapsible-fieldset-content">
{/if}
{foreach $content_attributes_grouped as $attribute_identifier => $attribute}
{def $contentclass_attribute = $attribute.contentclass_attribute
     $exp_own_fieldset = and( or( ne( $attribute.can_translate, 0 ), eq( $object.initial_language_code, $attribute.language_code ) ), $is_translating_content|not, $attribute.display_info.edit.grouped_input )}
{* A named group; a grouped input names itself with its fieldset and legend *}
<div class="block ezcca-edit-datatype-{$attribute.data_type_string} ezcca-edit-{$attribute_identifier}"{if $exp_own_fieldset|not} role="group" aria-labelledby="ezcca-name-{$attribute.id}"{/if}>
{* The templates of content.ini [EditSettings] AttributeTemplates[] (extensions' tools of a field, drawn with the page so
   nothing moves when scripts start): after the label, or first in the block when the field has its own fieldset or
   is being translated *}
{set-block variable=exp_attribute_hook}{foreach $exp_attribute_templates as $exp_attribute_template}{include uri=concat( 'design:', $exp_attribute_template )}{/foreach}{/set-block}
{if and( and( eq( $attribute.can_translate, 0 ), ne( $object.initial_language_code, $attribute.language_code ) )|not, or( $is_translating_content, $attribute.display_info.edit.grouped_input ) )}{$exp_attribute_hook}{/if}
{* Show view GUI if we can't edit, otherwise: show edit GUI. *}
{if and( eq( $attribute.can_translate, 0 ), ne( $object.initial_language_code, $attribute.language_code ) )}
    <label><span id="ezcca-name-{$attribute.id}">{first_set( $contentclass_attribute.nameList[$content_language], $contentclass_attribute.name )|wash}</span>
        {if $attribute.can_translate|not} <span class="nontranslatable">({'not translatable'|i18n( 'design/admin/content/edit_attribute' )})</span>{/if}:
        {if $contentclass_attribute.description} <span class="classattribute-description">{first_set( $contentclass_attribute.descriptionList[$content_language], $contentclass_attribute.description)|wash}</span>{/if}
    </label>
    {$exp_attribute_hook}
    {if $is_translating_content}
        <div class="original">
        {attribute_view_gui attribute_base=$attribute_base attribute=$attribute view_parameters=$view_parameters}
        <input type="hidden" name="ContentObjectAttribute_id[]" value="{$attribute.id}" />
        </div>
    {else}
        {attribute_view_gui attribute_base=$attribute_base attribute=$attribute view_parameters=$view_parameters}
        <input type="hidden" name="ContentObjectAttribute_id[]" value="{$attribute.id}" />
    {/if}
{else}
    {if $is_translating_content}
        <label{if $attribute.has_validation_error} class="message-error"{/if}{if $attribute.display_info.edit.grouped_input|not} for="ezcoa-{if ne( $attribute_base, 'ContentObjectAttribute' )}{$attribute_base}-{/if}{$attribute.contentclassattribute_id}_{$attribute.contentclass_attribute_identifier}"{/if}><span id="ezcca-name-{$attribute.id}">{first_set( $contentclass_attribute.nameList[$content_language], $contentclass_attribute.name )|wash}</span>
            {if $attribute.is_required} <span class="required">({'required'|i18n( 'design/admin/content/edit_attribute' )})</span>{/if}
            {if $attribute.is_information_collector} <span class="collector">({'information collector'|i18n( 'design/admin/content/edit_attribute' )})</span>{/if}:
            {if $contentclass_attribute.description} <span class="classattribute-description">{first_set( $contentclass_attribute.descriptionList[$content_language], $contentclass_attribute.description)|wash}</span>{/if}
        </label>
        <div class="original"{include uri='design:parts/exp_content_direction.tpl' exp_locale_code=$from_content_attributes_grouped_data_map[$attribute_group][$attribute_identifier].language_code exp_lang_always=true()}>
        {attribute_view_gui attribute_base=$attribute_base attribute=$from_content_attributes_grouped_data_map[$attribute_group][$attribute_identifier] view_parameters=$view_parameters}
        </div>
        <div class="translation"{include uri='design:parts/exp_content_direction.tpl' exp_locale_code=$attribute.language_code exp_lang_always=true()}>
        {if $attribute.display_info.edit.grouped_input}
            <fieldset>
            {attribute_edit_gui attribute_base=$attribute_base attribute=$attribute view_parameters=$view_parameters}
            <input type="hidden" name="ContentObjectAttribute_id[]" value="{$attribute.id}" />
            </fieldset>
        {else}
            {attribute_edit_gui attribute_base=$attribute_base attribute=$attribute view_parameters=$view_parameters}
            <input type="hidden" name="ContentObjectAttribute_id[]" value="{$attribute.id}" />
        {/if}
        </div>
    {else}
        {if $attribute.display_info.edit.grouped_input}
            <fieldset>
            <legend{if $attribute.has_validation_error} class="message-error"{/if}><span class="long-legend-wrap"><span id="ezcca-name-{$attribute.id}">{first_set( $contentclass_attribute.nameList[$content_language], $contentclass_attribute.name )|wash}</span>
                {if $attribute.is_required} <span class="required">({'required'|i18n( 'design/admin/content/edit_attribute' )})</span>{/if}
                {if $attribute.is_information_collector} <span class="collector">({'information collector'|i18n( 'design/admin/content/edit_attribute' )})</span>{/if}
                {if $contentclass_attribute.description} <span class="classattribute-description">{first_set( $contentclass_attribute.descriptionList[$content_language], $contentclass_attribute.description)|wash}</span>{/if}
                </span>
            </legend>
            {set-block variable=exp_text_attrs}{include uri='design:parts/exp_content_direction.tpl' exp_locale_code=$attribute.language_code}{/set-block}{if $exp_text_attrs|ne( '' )}<div class="exp-content-text"{$exp_text_attrs}>{/if}
            {attribute_edit_gui attribute_base=$attribute_base attribute=$attribute view_parameters=$view_parameters}
            {if $exp_text_attrs|ne( '' )}</div>{/if}{undef $exp_text_attrs}
            <input type="hidden" name="ContentObjectAttribute_id[]" value="{$attribute.id}" />
            </fieldset>
        {else}
            <label{if $attribute.has_validation_error} class="message-error"{/if} for="ezcoa-{if ne( $attribute_base, 'ContentObjectAttribute' )}{$attribute_base}-{/if}{$attribute.contentclassattribute_id}_{$attribute.contentclass_attribute_identifier}"><span id="ezcca-name-{$attribute.id}">{first_set( $contentclass_attribute.nameList[$content_language], $contentclass_attribute.name )|wash}</span>
                {if $attribute.is_required} <span class="required">({'required'|i18n( 'design/admin/content/edit_attribute' )})</span>{/if}
                {if $attribute.is_information_collector} <span class="collector">({'information collector'|i18n( 'design/admin/content/edit_attribute' )})</span>{/if}:
                {if $contentclass_attribute.description} <span class="classattribute-description">{first_set( $contentclass_attribute.descriptionList[$content_language], $contentclass_attribute.description)|wash}</span>{/if}
            </label>
            {$exp_attribute_hook}
            {set-block variable=exp_text_attrs}{include uri='design:parts/exp_content_direction.tpl' exp_locale_code=$attribute.language_code}{/set-block}{if $exp_text_attrs|ne( '' )}<div class="exp-content-text"{$exp_text_attrs}>{/if}
            {attribute_edit_gui attribute_base=$attribute_base attribute=$attribute view_parameters=$view_parameters}
            {if $exp_text_attrs|ne( '' )}</div>{/if}{undef $exp_text_attrs}
            <input type="hidden" name="ContentObjectAttribute_id[]" value="{$attribute.id}" />
        {/if}
    {/if}
{/if}
</div>
{undef $contentclass_attribute $exp_own_fieldset $exp_attribute_hook}
{/foreach}
{if $attribute_group|ne( $attribute_default_category )}
    </div>
    </fieldset>
{/if}
{/foreach}
{run-once}
{* if is_set( $content_attributes_grouped_data_map[1] ) *}
<script type="text/javascript">
{literal}

jQuery(function( $ )
{
    $('fieldset.ezcca-collapsible legend a').on( 'click', function()
    {
        var container = $( this.parentNode.parentNode ), inner = container.find('div.ezcca-collapsible-fieldset-content');
        if ( container.hasClass('ezcca-collapsed') )
        {
            container.removeClass('ezcca-collapsed');
            inner.slideDown( 150 );
        }
        else
        {
            inner.slideUp( 150, function(){
                $( this.parentNode ).addClass('ezcca-collapsed');
            });
        }
    });
    // Collapse by default, unless the group has at least one attribute with label.message-error
    $('fieldset.ezcca-collapsible').each( function(){
        if ( $(this).find('label.message-error').length == 0 )
        {
            $(this).addClass('ezcca-collapsed').find('div.ezcca-collapsible-fieldset-content').hide();
        }
    } );
});

{/literal}
</script>
{* /if *}
{/run-once}
{undef $exp_attribute_templates}
{/default}
