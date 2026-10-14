{default $view_parameters            = array()
         $attribute_categorys        = ezini( 'ClassAttributeSettings', 'CategoryList', 'content.ini' )
         $attribute_default_category = ezini( 'ClassAttributeSettings', 'DefaultCategory', 'content.ini' )}

{* What the required mark means: once per page (edit, multiedit, register, template look), when a field is required *}
{def $a4_has_required = false()}
{foreach $content_attributes_grouped_data_map as $a4_group}{foreach $a4_group as $a4_attribute}{if $a4_attribute.is_required}{set $a4_has_required = true()}{/if}{/foreach}{/foreach}
{if $a4_has_required}{run-once}<p class="a4-required-hint"><span class="a4-required-mark" aria-hidden="true">*</span> {'Required field'|i18n( 'design/admin/content/edit' )}</p>{/run-once}{/if}
{undef $a4_has_required}

{foreach $content_attributes_grouped_data_map as $attribute_group => $content_attributes_grouped}
{if $attribute_group|ne( $attribute_default_category )}
    <fieldset class="ezcca-collapsible ezcca-attributes-group-{$attribute_group|wash}">
    <legend><a href="JavaScript:void(0);">{$attribute_categorys[$attribute_group]}</a></legend>
    <div class="ezcca-collapsible-fieldset-content">
{/if}
{foreach $content_attributes_grouped as $attribute_identifier => $attribute}
{def $contentclass_attribute = $attribute.contentclass_attribute
     $a4_view_only = and( eq( $attribute.can_translate, 0 ), ne( $object.initial_language_code, $attribute.language_code ) )
     $a4_own_fieldset = and( $a4_view_only|not, $is_translating_content|not, $attribute.display_info.edit.grouped_input )}
{* A named group, the summary's link target; a grouped input names itself with its fieldset and legend *}
<div class="block a4-content-attr ezcca-edit-datatype-{$attribute.data_type_string} ezcca-edit-{$attribute_identifier}{if $attribute.is_required} is-required{/if}{if $attribute.has_validation_error} is-invalid{/if}" id="ezcca-attr-{$attribute.id}" tabindex="-1"{if $a4_own_fieldset|not} role="group" aria-labelledby="ezcca-name-{$attribute.id}"{if and( $attribute.has_validation_error, $a4_view_only|not )} aria-describedby="ezcca-error-{$attribute.id}"{/if}{/if}>
{* Show view GUI if we can't edit, otherwise: show edit GUI. *}
{if $a4_view_only}
    <label><span id="ezcca-name-{$attribute.id}">{first_set( $contentclass_attribute.nameList[$content_language], $contentclass_attribute.name )|wash}</span>
        {if $attribute.can_translate|not} <span class="nontranslatable">({'not translatable'|i18n( 'design/admin/content/edit_attribute' )})</span>{/if}:
        {if $contentclass_attribute.description} <span class="classattribute-description">{first_set( $contentclass_attribute.descriptionList[$content_language], $contentclass_attribute.description)|wash}</span>{/if}
    </label>
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
        <label{if $attribute.has_validation_error} class="message-error"{/if}><span id="ezcca-name-{$attribute.id}">{first_set( $contentclass_attribute.nameList[$content_language], $contentclass_attribute.name )|wash}</span>
            {if $attribute.is_required} <span class="a4-required-mark" aria-hidden="true">*</span><span class="a4-sr-only">({'required'|i18n( 'design/admin/content/edit_attribute' )})</span>{/if}
            {if $attribute.is_information_collector} <span class="collector">({'information collector'|i18n( 'design/admin/content/edit_attribute' )})</span>{/if}:
            {if $contentclass_attribute.description} <span class="classattribute-description">{first_set( $contentclass_attribute.descriptionList[$content_language], $contentclass_attribute.description)|wash}</span>{/if}
        </label>
        {if $attribute.has_validation_error}<p class="a4-field-error a4-content-attr-error" id="ezcca-error-{$attribute.id}"><span class="a4-icon a4-bi-x-circle-fill" aria-hidden="true"></span> {if $attribute.validation_error}{$attribute.validation_error|wash}{else}{'Required data is either missing or is invalid'|i18n( 'design/admin/content/edit' )}{/if}</p>{/if}
        <div class="original">
        {attribute_view_gui attribute_base=$attribute_base attribute=$from_content_attributes_grouped_data_map[$attribute_group][$attribute_identifier] view_parameters=$view_parameters}
        </div>
        <div class="translation">
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
            <fieldset{if $attribute.has_validation_error} aria-describedby="ezcca-error-{$attribute.id}"{/if}>
            <legend{if $attribute.has_validation_error} class="message-error"{/if}><span class="long-legend-wrap"><span id="ezcca-name-{$attribute.id}">{first_set( $contentclass_attribute.nameList[$content_language], $contentclass_attribute.name )|wash}</span>
                {if $attribute.is_required} <span class="a4-required-mark" aria-hidden="true">*</span><span class="a4-sr-only">({'required'|i18n( 'design/admin/content/edit_attribute' )})</span>{/if}
                {if $attribute.is_information_collector} <span class="collector">({'information collector'|i18n( 'design/admin/content/edit_attribute' )})</span>{/if}
                {if $contentclass_attribute.description} <span class="classattribute-description">{first_set( $contentclass_attribute.descriptionList[$content_language], $contentclass_attribute.description)|wash}</span>{/if}
                </span>
            </legend>
            {if $attribute.has_validation_error}<p class="a4-field-error a4-content-attr-error" id="ezcca-error-{$attribute.id}"><span class="a4-icon a4-bi-x-circle-fill" aria-hidden="true"></span> {if $attribute.validation_error}{$attribute.validation_error|wash}{else}{'Required data is either missing or is invalid'|i18n( 'design/admin/content/edit' )}{/if}</p>{/if}
            {attribute_edit_gui attribute_base=$attribute_base attribute=$attribute view_parameters=$view_parameters}
            <input type="hidden" name="ContentObjectAttribute_id[]" value="{$attribute.id}" />
            </fieldset>
        {else}
            <label{if $attribute.has_validation_error} class="message-error"{/if}><span id="ezcca-name-{$attribute.id}">{first_set( $contentclass_attribute.nameList[$content_language], $contentclass_attribute.name )|wash}</span>
                {if $attribute.is_required} <span class="a4-required-mark" aria-hidden="true">*</span><span class="a4-sr-only">({'required'|i18n( 'design/admin/content/edit_attribute' )})</span>{/if}
                {if $attribute.is_information_collector} <span class="collector">({'information collector'|i18n( 'design/admin/content/edit_attribute' )})</span>{/if}:
                {if $contentclass_attribute.description} <span class="classattribute-description">{first_set( $contentclass_attribute.descriptionList[$content_language], $contentclass_attribute.description)|wash}</span>{/if}
            </label>
            {if $attribute.has_validation_error}<p class="a4-field-error a4-content-attr-error" id="ezcca-error-{$attribute.id}"><span class="a4-icon a4-bi-x-circle-fill" aria-hidden="true"></span> {if $attribute.validation_error}{$attribute.validation_error|wash}{else}{'Required data is either missing or is invalid'|i18n( 'design/admin/content/edit' )}{/if}</p>{/if}
            {attribute_edit_gui attribute_base=$attribute_base attribute=$attribute view_parameters=$view_parameters}
            <input type="hidden" name="ContentObjectAttribute_id[]" value="{$attribute.id}" />
        {/if}
    {/if}
{/if}
</div>
{undef $contentclass_attribute $a4_view_only $a4_own_fieldset}
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
    // An attribute the summary names is invalid even when the datatype set no message (an unknown error):
    // mark it, so its group opens below and the link does not lead into a collapsed group
    $('#a4-error-summary a[href^="#ezcca-attr-"]').each( function(){
        $( document.getElementById( this.getAttribute('href').slice( 1 ) ) ).addClass('is-invalid');
    } );

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
    // Collapse by default, unless the group has at least one attribute with a validation error
    $('fieldset.ezcca-collapsible').each( function(){
        if ( $(this).find('label.message-error, legend.message-error, .a4-content-attr.is-invalid').length == 0 )
        {
            $(this).addClass('ezcca-collapsed').find('div.ezcca-collapsible-fieldset-content').hide();
        }
    } );

    // The datatype templates draw the controls; required and invalid are set on them here, once for all
    // datatypes: aria-required, aria-invalid and the attribute's message as description. The original
    // (read-only) side of a translation is left alone.
    // The data controls only: no buttons, file or reset inputs, no remove/select checkboxes of lists (relations,
    // matrix rows); a datatype template can opt a control out with the class a4-no-field-mark.
    var controls = 'input:not([type=hidden]):not([type=submit]):not([type=button]):not([type=image]):not([type=reset]):not([type=file]), select, textarea';
    function dataControls( group )
    {
        return $( group ).find( controls ).not('.original *, .a4-no-field-mark, .a4-no-field-mark *').filter( function(){
            return !( /^(checkbox|radio)$/.test( this.type ) && /delete|remove|selection/i.test( this.name || '' ) );
        } );
    }
    $('.a4-content-attr.is-required').each( function(){
        dataControls( this ).attr( 'aria-required', 'true' );
    } );
    $('.a4-content-attr.is-invalid').each( function(){
        var error = this.id.replace( 'ezcca-attr-', 'ezcca-error-' );
        if ( !document.getElementById( error ) ) error = '';
        dataControls( this ).each( function(){
            this.setAttribute( 'aria-invalid', 'true' );
            if ( error )
            {
                var described = ( this.getAttribute('aria-describedby') || '' ).split(' ').filter( Boolean );
                if ( described.indexOf( error ) === -1 ) described.push( error );
                this.setAttribute( 'aria-describedby', described.join(' ') );
            }
        } );
    } );
    // A failed store: the summary takes the focus, so it is read and its links lead to the fields
    $('#a4-error-summary').trigger('focus');
});

{/literal}
</script>
{* /if *}
{/run-once}
{/default}
