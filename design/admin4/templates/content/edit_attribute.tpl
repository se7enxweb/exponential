{default $view_parameters            = array()
         $attribute_categorys        = ezini( 'ClassAttributeSettings', 'CategoryList', 'content.ini' )
         $attribute_default_category = ezini( 'ClassAttributeSettings', 'DefaultCategory', 'content.ini' )}
{def $exp_attribute_templates = cond( ezini_hasvariable( 'EditSettings', 'AttributeTemplates', 'content.ini' ), ezini( 'EditSettings', 'AttributeTemplates', 'content.ini' ), array() )}

{* Each attribute is a card that names the kind of its input in its corner, and a short one shares its row with a
   short neighbour. The kind, its icon and which datatypes are short come from admininterface.ini
   [ContentEditAttributeSettings], so an extension adds its own datatypes there *}
{def $a4_kind_of = ezini( 'ContentEditAttributeSettings', 'DatatypeKind', 'admininterface.ini' )
     $a4_kind_names = ezini( 'ContentEditAttributeSettings', 'KindName', 'admininterface.ini' )
     $a4_kind_icons = ezini( 'ContentEditAttributeSettings', 'KindIcon', 'admininterface.ini' )
     $a4_kind_default = ezini( 'ContentEditAttributeSettings', 'DefaultKind', 'admininterface.ini' )
     $a4_compact = ezini( 'ContentEditAttributeSettings', 'CompactDatatypes', 'admininterface.ini' )
     $a4_nav_from = ezini( 'ContentEditAttributeSettings', 'FieldNavigationFrom', 'admininterface.ini' )|int
     $a4_kind = ''
     $a4_attr_count = 0}

{* What the required mark means: once per page (edit, multiedit, register, template look), when a field is required *}
{def $a4_has_required = false()}
{foreach $content_attributes_grouped_data_map as $a4_group}{foreach $a4_group as $a4_attribute}{set $a4_attr_count = $a4_attr_count|inc}{if $a4_attribute.is_required}{set $a4_has_required = true()}{/if}{/foreach}{/foreach}
{if $a4_has_required}{run-once}<p class="a4-required-hint"><span class="a4-required-mark" aria-hidden="true">*</span> {'Required field'|i18n( 'design/admin/content/edit' )}</p>{/run-once}{/if}
{undef $a4_has_required}

{* The fields of a long form at a glance, each a link to its card; one in a collapsed group opens it
   (content_edit_attribute.js) *}
{if and( $a4_nav_from|gt( 0 ), $a4_attr_count|ge( $a4_nav_from ) )}
<nav class="a4-content-attr-nav" aria-label="{'Fields of this form'|i18n( 'design/admin/content/edit_attribute' )}">
    <ul>
    {foreach $content_attributes_grouped_data_map as $a4_group}{foreach $a4_group as $a4_attribute}
        {set $a4_kind = first_set( $a4_kind_of[$a4_attribute.data_type_string], $a4_kind_default )}
        <li><a href="#ezcca-attr-{$a4_attribute.id}"{if $a4_attribute.has_validation_error} class="is-invalid"{/if}><span class="a4-icon a4-bi-{first_set( $a4_kind_icons[$a4_kind], $a4_kind_icons[$a4_kind_default] )|wash}" aria-hidden="true"></span>{first_set( $a4_attribute.contentclass_attribute.nameList[$content_language], $a4_attribute.contentclass_attribute.name )|wash}{if $a4_attribute.is_required}<span class="a4-required-mark" aria-hidden="true">*</span>{/if}</a></li>
    {/foreach}{/foreach}
    </ul>
</nav>
{/if}

{foreach $content_attributes_grouped_data_map as $attribute_group => $content_attributes_grouped}
{if $attribute_group|ne( $attribute_default_category )}
    <fieldset class="ezcca-collapsible ezcca-attributes-group-{$attribute_group|wash}">
    <legend><a href="#">{$attribute_categorys[$attribute_group]}</a></legend>
    <div class="ezcca-collapsible-fieldset-content">
{/if}
<div class="a4-content-attrs">
{foreach $content_attributes_grouped as $attribute_identifier => $attribute}
{def $contentclass_attribute = $attribute.contentclass_attribute
     $a4_view_only = and( eq( $attribute.can_translate, 0 ), ne( $object.initial_language_code, $attribute.language_code ) )
     $a4_own_fieldset = and( $a4_view_only|not, $is_translating_content|not, $attribute.display_info.edit.grouped_input )}
{set $a4_kind = first_set( $a4_kind_of[$attribute.data_type_string], $a4_kind_default )}
{* A named group, the summary's link target; a grouped input names itself with its fieldset and legend *}
<div class="block a4-content-attr ezcca-edit-datatype-{$attribute.data_type_string} ezcca-edit-{$attribute_identifier} a4-content-attr-kind-{$a4_kind|wash}{if and( $is_translating_content|not, $a4_compact|contains( $attribute.data_type_string ) )} is-compact{/if}{if $attribute.is_required} is-required{/if}{if $attribute.has_validation_error} is-invalid{/if}" id="ezcca-attr-{$attribute.id}" tabindex="-1"{if $a4_own_fieldset|not} role="group" aria-labelledby="ezcca-name-{$attribute.id}"{if and( $attribute.has_validation_error, $a4_view_only|not )} aria-describedby="ezcca-error-{$attribute.id}"{/if}{/if}>
{* The kind of input: a visual hint beside the name, which the control itself states to a screen reader *}
<span class="a4-content-attr-kind" aria-hidden="true"><span class="a4-icon a4-bi-{first_set( $a4_kind_icons[$a4_kind], $a4_kind_icons[$a4_kind_default] )|wash}"></span>{first_set( $a4_kind_names[$a4_kind], $a4_kind_names[$a4_kind_default] )|i18n( 'design/admin/content/edit_attribute' )|wash}</span>
{* The templates of content.ini [EditSettings] AttributeTemplates[] (extensions' tools of a field, drawn with the page so
   nothing moves when scripts start): after the label, or first in the block when the field has its own fieldset or
   is being translated *}
{set-block variable=exp_attribute_hook}{foreach $exp_attribute_templates as $exp_attribute_template}{include uri=concat( 'design:', $exp_attribute_template )}{/foreach}{/set-block}
{if and( $a4_view_only|not, or( $is_translating_content, $attribute.display_info.edit.grouped_input ) )}{$exp_attribute_hook}{/if}
{* Show view GUI if we can't edit, otherwise: show edit GUI. *}
{if $a4_view_only}
    <label class="a4-content-attr-label"><span id="ezcca-name-{$attribute.id}">{first_set( $contentclass_attribute.nameList[$content_language], $contentclass_attribute.name )|wash}</span>
        {if $attribute.can_translate|not} <span class="nontranslatable">({'not translatable'|i18n( 'design/admin/content/edit_attribute' )})</span>{/if}
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
        <label class="a4-content-attr-label{if $attribute.has_validation_error} message-error{/if}"{if $attribute.display_info.edit.grouped_input|not} for="ezcoa-{if ne( $attribute_base, 'ContentObjectAttribute' )}{$attribute_base}-{/if}{$attribute.contentclassattribute_id}_{$attribute.contentclass_attribute_identifier}"{/if}><span id="ezcca-name-{$attribute.id}">{first_set( $contentclass_attribute.nameList[$content_language], $contentclass_attribute.name )|wash}</span>
            {if $attribute.is_required} <span class="a4-required-mark" aria-hidden="true">*</span><span class="a4-sr-only">({'required'|i18n( 'design/admin/content/edit_attribute' )})</span>{/if}
            {if $attribute.is_information_collector} <span class="collector">({'information collector'|i18n( 'design/admin/content/edit_attribute' )})</span>{/if}
            {if $contentclass_attribute.description} <span class="classattribute-description">{first_set( $contentclass_attribute.descriptionList[$content_language], $contentclass_attribute.description)|wash}</span>{/if}
        </label>
        {if $attribute.has_validation_error}<p class="a4-field-error a4-content-attr-error" id="ezcca-error-{$attribute.id}"><span class="a4-icon a4-bi-x-circle-fill" aria-hidden="true"></span> {if $attribute.validation_error}{$attribute.validation_error|wash}{else}{'Required data is either missing or is invalid'|i18n( 'design/admin/content/edit' )}{/if}</p>{/if}
        <div class="original"{include uri='design:parts/exp_content_direction.tpl' exp_locale_code=$from_content_attributes_grouped_data_map[$attribute_group][$attribute_identifier].language_code exp_lang_always=true()}>
        {attribute_view_gui attribute_base=$attribute_base attribute=$from_content_attributes_grouped_data_map[$attribute_group][$attribute_identifier] view_parameters=$view_parameters}
        </div>
        <div class="translation"{include uri='design:parts/exp_content_direction.tpl' exp_locale_code=$attribute.language_code exp_lang_always=true()}>
        {if $attribute.display_info.edit.grouped_input}
            <fieldset class="a4-content-attr-group">
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
            <fieldset class="a4-content-attr-group"{if $attribute.has_validation_error} aria-describedby="ezcca-error-{$attribute.id}"{/if}>
            <legend class="a4-content-attr-label{if $attribute.has_validation_error} message-error{/if}"><span class="long-legend-wrap"><span id="ezcca-name-{$attribute.id}">{first_set( $contentclass_attribute.nameList[$content_language], $contentclass_attribute.name )|wash}</span>
                {if $attribute.is_required} <span class="a4-required-mark" aria-hidden="true">*</span><span class="a4-sr-only">({'required'|i18n( 'design/admin/content/edit_attribute' )})</span>{/if}
                {if $attribute.is_information_collector} <span class="collector">({'information collector'|i18n( 'design/admin/content/edit_attribute' )})</span>{/if}
                {if $contentclass_attribute.description} <span class="classattribute-description">{first_set( $contentclass_attribute.descriptionList[$content_language], $contentclass_attribute.description)|wash}</span>{/if}
                </span>
            </legend>
            {if $attribute.has_validation_error}<p class="a4-field-error a4-content-attr-error" id="ezcca-error-{$attribute.id}"><span class="a4-icon a4-bi-x-circle-fill" aria-hidden="true"></span> {if $attribute.validation_error}{$attribute.validation_error|wash}{else}{'Required data is either missing or is invalid'|i18n( 'design/admin/content/edit' )}{/if}</p>{/if}
            {set-block variable=exp_text_attrs}{include uri='design:parts/exp_content_direction.tpl' exp_locale_code=$attribute.language_code}{/set-block}{if $exp_text_attrs|ne( '' )}<div class="exp-content-text"{$exp_text_attrs}>{/if}
            {attribute_edit_gui attribute_base=$attribute_base attribute=$attribute view_parameters=$view_parameters}
            {if $exp_text_attrs|ne( '' )}</div>{/if}{undef $exp_text_attrs}
            <input type="hidden" name="ContentObjectAttribute_id[]" value="{$attribute.id}" />
            </fieldset>
        {else}
            <label class="a4-content-attr-label{if $attribute.has_validation_error} message-error{/if}" for="ezcoa-{if ne( $attribute_base, 'ContentObjectAttribute' )}{$attribute_base}-{/if}{$attribute.contentclassattribute_id}_{$attribute.contentclass_attribute_identifier}"><span id="ezcca-name-{$attribute.id}">{first_set( $contentclass_attribute.nameList[$content_language], $contentclass_attribute.name )|wash}</span>
                {if $attribute.is_required} <span class="a4-required-mark" aria-hidden="true">*</span><span class="a4-sr-only">({'required'|i18n( 'design/admin/content/edit_attribute' )})</span>{/if}
                {if $attribute.is_information_collector} <span class="collector">({'information collector'|i18n( 'design/admin/content/edit_attribute' )})</span>{/if}
                {if $contentclass_attribute.description} <span class="classattribute-description">{first_set( $contentclass_attribute.descriptionList[$content_language], $contentclass_attribute.description)|wash}</span>{/if}
            </label>
            {$exp_attribute_hook}
            {if $attribute.has_validation_error}<p class="a4-field-error a4-content-attr-error" id="ezcca-error-{$attribute.id}"><span class="a4-icon a4-bi-x-circle-fill" aria-hidden="true"></span> {if $attribute.validation_error}{$attribute.validation_error|wash}{else}{'Required data is either missing or is invalid'|i18n( 'design/admin/content/edit' )}{/if}</p>{/if}
            {set-block variable=exp_text_attrs}{include uri='design:parts/exp_content_direction.tpl' exp_locale_code=$attribute.language_code}{/set-block}{if $exp_text_attrs|ne( '' )}<div class="exp-content-text"{$exp_text_attrs}>{/if}
            {attribute_edit_gui attribute_base=$attribute_base attribute=$attribute view_parameters=$view_parameters}
            {if $exp_text_attrs|ne( '' )}</div>{/if}{undef $exp_text_attrs}
            <input type="hidden" name="ContentObjectAttribute_id[]" value="{$attribute.id}" />
        {/if}
    {/if}
{/if}
</div>
{undef $contentclass_attribute $a4_view_only $a4_own_fieldset $exp_attribute_hook}
{/foreach}
</div>
{if $attribute_group|ne( $attribute_default_category )}
    </div>
    </fieldset>
{/if}
{/foreach}
{run-once}
{* if is_set( $content_attributes_grouped_data_map[1] ) *}
{ezscript( array( 'page/content_edit_attribute.js' ) )}
{* /if *}
{/run-once}
{undef $exp_attribute_templates}
{undef $a4_kind_of $a4_kind_names $a4_kind_icons $a4_kind_default $a4_compact $a4_nav_from $a4_kind $a4_attr_count}
{/default}
