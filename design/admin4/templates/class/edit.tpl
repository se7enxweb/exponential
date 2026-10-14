{* Edit a class (class/edit/<id>/(language)/<locale>).

   The bar at the top keeps OK, Apply (content.ini [ClassSettings] ApplyButton), Cancel and Add attribute in reach.
   Then what kept the class from being stored, the class's own settings (each field with what it is used for), and
   one card per attribute: its order buttons and position, name, identifier, description, flags, category and the
   settings of its type (class_attribute_edit_gui). Remove selected attributes and Add attribute are under the list.

   Every field and button name is the one the view has always read: ContentClassHasInput, ContentClass_name,
   ContentClass_identifier, ContentClass_description, ContentClass_contentobject_name, ContentClass_url_alias_name,
   ContentClass_is_container_*, ContentClass_default_sorting_*, ContentClass_always_available*,
   ContentAttribute_*[<id>], MoveTop/MoveUp/MoveDown/MoveBottom=<id> (submit buttons), DataTypeString, StoreButton, ApplyButton,
   DiscardButton, NewButton, RemoveButton and RedirectIfDiscarded (Cancel goes back to the page the form was opened
   from). The attribute list keeps the markup the order script relies on (#ezcca-edit-list, tr.ezcca-edit-list-item,
   th.wide, div.listbutton). Every template variable is unchanged. This is an edit view, which admin4 draws without its
   main card; .exp-standalone gives the page its own. The same file is in design/admin and design/admin4.
   Guide: doc/guides/class-groups.md *}

<form action={concat( $module.functions.edit.uri, '/', $class.id, '/(language)/', $language_code )|ezurl} method="post" id="ClassEdit" name="ClassEdit" class="a4-page exp-classgroups exp-classedit a4-standalone">
<input type="hidden" name="ContentClassHasInput" value="1" />

<div id="controlbar-top" class="controlbar controlbar-fixed a4-toolbar a4-class-editbar">
<div class="a4-actions">
    <button class="a4-btn a4-btn-primary" type="submit" name="StoreButton" value="1" title="{'Store changes and exit from edit mode.'|i18n( 'design/admin/class/edit' )|wash}">{'OK'|i18n( 'design/admin/class/edit' )}</button>
    {if eq( ezini( 'ClassSettings', 'ApplyButton', 'content.ini' ), 'enabled' )}
    <button class="a4-btn" type="submit" name="ApplyButton" value="1" title="{'Store changes and continue editing.'|i18n( 'design/admin/class/edit' )|wash}">{'Apply'|i18n( 'design/admin/class/edit' )}</button>
    {/if}
    <button class="a4-btn" type="submit" name="DiscardButton" value="1" title="{'Discard all changes and exit from edit mode.'|i18n( 'design/admin/class/edit' )|wash}">{'Cancel'|i18n( 'design/admin/class/edit' )}</button>
</div>
<div class="a4-actions">
    <label class="a4-sr-only" for="DataTypeStringTop">{'Attribute type'|i18n( 'design/admin/class/edit' )}</label>
    {include uri="design:class/datatypes.tpl" name='DataTypes' id_name='DataTypeStringTop' selection_name='DataTypeString' datatypes=$datatypes current=$datatype}
    <button class="a4-btn" type="submit" name="NewButton" id="NewButtonTop" value="1" title="{'Add a new attribute to the class. Use the menu on the left to select the attribute type.'|i18n( 'design/admin/class/edit' )|wash}">{'Add attribute'|i18n( 'design/admin/class/edit' )}</button>
</div>
</div>

<div class="context-block">
<div class="box-header"><div class="box-ml">
<div class="a4-title-row">
<h1 class="context-title" title="{'Class name and number of objects'|i18n( 'design/admin/class/view' )}">{$class.identifier|class_icon( 'normal', $class.name|wash )}&nbsp;{'Edit <%class_name> (%object_count objects)'|i18n( 'design/admin/class/edit',, hash( '%class_name', $class.nameList[$language_code], '%object_count', $class.object_count ) )|wash}</h1>
<span class="a4-meta">{'ID %id'|i18n( 'design/admin/class/grouplist',, hash( '%id', $class.id ) )}</span>
</div>
</div></div>

<div class="box-bc"><div class="box-ml"><div class="box-content">

{if $validation.processed}
    {if $validation.attributes}
<div class="a4-notice is-bad" role="alert" id="class-edit-errors" tabindex="-1">
    <p><strong>{'The class definition could not be stored.'|i18n( 'design/admin/class/edit' )}</strong> {'The following information is either missing or invalid'|i18n( 'design/admin/class/edit' )}:</p>
    <ul>
    {foreach $validation.attributes as $unvalidated}
        {if is_set( $unvalidated.reason )}
        <li>{'attribute \'%identifier\': (%id) %text'|i18n( 'design/admin/class/edit',, hash( '%identifier', $unvalidated.identifier|wash, '%id', $unvalidated.id|wash, '%text', $unvalidated.reason.text|wash ) )}
            {if $unvalidated.reason.list}<ul>{foreach $unvalidated.reason.list as $subitem}<li>{if is_set( $subitem.identifier )}{$subitem.identifier|wash}: {/if}{$subitem.text|wash}</li>{/foreach}</ul>{/if}
        </li>
        {else}
        <li>{'attribute \'%identifier\': %name (%id)'|i18n( 'design/admin/class/edit',, hash( '%identifier', $unvalidated.identifier|wash, '%name', $unvalidated.name|wash, '%id', $unvalidated.id|wash ) )}</li>
        {/if}
    {/foreach}
    </ul>
</div>
    {else}
<div class="a4-notice is-ok" role="status">{'The draft of the class definition was successfully stored.'|i18n( 'design/admin/class/edit' )}</div>
    {/if}
{elseif $validation.class_errors}
<div class="a4-notice is-bad" role="alert" id="class-edit-errors" tabindex="-1">
    <p><strong>{'The class definition contains the following errors'|i18n( 'design/admin/class/edit' )}:</strong></p>
    <ul>{foreach $validation.class_errors as $class_error}<li>{$class_error.text|wash}</li>{/foreach}</ul>
</div>
{/if}

<div class="a4-toolbar">
    {def $locale = fetch( 'content', 'locale', hash( 'locale_code', $language_code ) )}
    <p class="a4-meta">{'Last modified'|i18n( 'design/admin/class/edit' )}: {$class.modified|l10n( shortdatetime )}{if $class.modifier.contentobject}, {$class.modifier.contentobject.name|wash}{/if}
        &middot; {include uri='design:parts/language_badge.tpl' locale=$language_code badge_named=true()} {$locale.intl_language_name|wash}</p>
    {undef $locale}
    <p class="a4-meta">{'Nothing changes for the objects of this class until you press OK. Cancel throws the draft away.'|i18n( 'design/admin/class/edit' )}</p>
</div>

<section class="a4-card" aria-labelledby="class-edit-settings">
<div class="a4-card-head"><h2 class="a4-h2" id="class-edit-settings">{'The class'|i18n( 'design/admin/class/edit' )}</h2></div>
<div class="a4-card-body">
<div class="a4-grid is-wide">
    <div class="a4-field">
        <label for="className">{'Name'|i18n( 'design/admin/class/edit' )}</label>
        <input type="text" id="className" name="ContentClass_name" value="{$class.nameList[$language_code]|wash}" aria-describedby="className-help" />
        <span class="a4-help" id="className-help">{'Use this field to set the informal name of the class. The name field can contain whitespaces and special characters.'|i18n( 'design/admin/class/edit' )}</span>
    </div>
    <div class="a4-field">
        <label for="ContentClass_identifier">{'Identifier'|i18n( 'design/admin/class/edit' )}</label>
        <input type="text" id="ContentClass_identifier" name="ContentClass_identifier" value="{$class.identifier|wash}" maxlength="50" spellcheck="false" autocomplete="off" aria-describedby="ContentClass_identifier-help" />
        <span class="a4-help" id="ContentClass_identifier-help">{'Use this field to set the internal name of the class. The identifier will be used in templates and in PHP code. Allowed characters are letters, numbers and underscores.'|i18n( 'design/admin/class/edit' )}</span>
    </div>
    <div class="a4-field">
        <label for="classDescription">{'Description'|i18n( 'design/admin/class/edit' )}</label>
        <input type="text" id="classDescription" name="ContentClass_description" value="{$class.descriptionList[$language_code]|wash}" aria-describedby="classDescription-help" />
        <span class="a4-help" id="classDescription-help">{'Use this field to set the informal description of the class. The description field can contain whitespaces and special characters.'|i18n( 'design/admin/class/edit' )}</span>
    </div>
    <div class="a4-field">
        <label for="ContentClass_contentobject_name">{'Object name pattern'|i18n( 'design/admin/class/edit' )}</label>
        <input type="text" id="ContentClass_contentobject_name" name="ContentClass_contentobject_name" value="{$class.contentobject_name|wash}" spellcheck="false" aria-describedby="ContentClass_contentobject_name-help" />
        <span class="a4-help" id="ContentClass_contentobject_name-help">{'Use this field to configure how the name of the objects are generated. Type in the identifiers of the attributes that should be used. The identifiers must be enclosed in angle brackets. Text outside angle brackets will be included as it is shown here.'|i18n( 'design/admin/class/edit' )}</span>
    </div>
    <div class="a4-field">
        <label for="ContentClass_url_alias_name">{'URL alias name pattern'|i18n( 'design/admin/class/edit' )}</label>
        <input type="text" id="ContentClass_url_alias_name" name="ContentClass_url_alias_name" value="{$class.url_alias_name|wash}" spellcheck="false" aria-describedby="ContentClass_url_alias_name-help" />
        <span class="a4-help" id="ContentClass_url_alias_name-help">{'Use this field to configure how the url alias of the objects are generated (applies to nice URLs). Type in the identifiers of the attributes that should be used. The identifiers must be enclosed in angle brackets. Text outside angle brackets will be included as is.'|i18n( 'design/admin/class/edit' )}</span>
    </div>
    <div class="a4-field">
        {def $sort_fields = fetch( content, available_sort_fields )}
        <label for="ContentClass_default_sorting_field">{'Default sorting of children'|i18n( 'design/admin/class/edit' )}</label>
        <input type="hidden" name="ContentClass_default_sorting_exists" value="1" />
        <div class="a4-searchrow">
            <select id="ContentClass_default_sorting_field" name="ContentClass_default_sorting_field" aria-describedby="ContentClass_default_sorting-help">
            {foreach $sort_fields as $sf_key => $sf_item}
                <option value="{$sf_key|wash}"{if eq( $sf_key, $class.sort_field )} selected="selected"{/if}>{$sf_item|wash}</option>
            {/foreach}
            </select>
            <select id="ContentClass_default_sorting_order" name="ContentClass_default_sorting_order" aria-label="{'Order'|i18n( 'design/admin/class/edit' )}">
                <option value="0"{if eq( $class.sort_order, 0 )} selected="selected"{/if}>{'Descending'|i18n( 'design/admin/class/edit' )}</option>
                <option value="1"{if eq( $class.sort_order, 1 )} selected="selected"{/if}>{'Ascending'|i18n( 'design/admin/class/edit' )}</option>
            </select>
        </div>
        <span class="a4-help" id="ContentClass_default_sorting-help">{'Use these controls to set the default sorting method for the sub items of instances of the content class.'|i18n( 'design/admin/class/edit' )}</span>
        {undef $sort_fields}
    </div>
    <div class="a4-field">
        <input type="hidden" name="ContentClass_is_container_exists" value="1" />
        <label class="a4-check"><input type="checkbox" id="ContentClass_is_container_checked" name="ContentClass_is_container_checked" value="{$class.is_container}"{if $class.is_container|eq( 1 )} checked="checked"{/if} /> {'Container'|i18n( 'design/admin/class/edit' )}</label>
        <span class="a4-help">{'Use this checkbox to allow instances of the class to have sub items. If checked, it will be possible to create new sub items. If not checked, the sub items will not be displayed.'|i18n( 'design/admin/class/edit' )}</span>
    </div>
    <div class="a4-field">
        <input type="hidden" name="ContentClass_always_available_exists" value="1" />
        <label class="a4-check"><input type="checkbox" id="ContentClass_always_available" name="ContentClass_always_available"{if $class.always_available|eq( 1 )} checked="checked"{/if} /> {'Default object availability'|i18n( 'design/standard/class/edit' )}</label>
        <span class="a4-help">{'Use this checkbox to set the default availability for the objects of this class. The availability controls whether an object should be shown even if it does not exist in one of the languages specified by the "SiteLanguageList" setting. If this is the case, the system will use the main language of the object.'|i18n( 'design/admin/class/edit' )}</span>
    </div>
</div>
</div>
</section>

<section class="a4-section" aria-labelledby="class-edit-attributes">
<div class="a4-section-head">
    <h2 class="a4-h2" id="class-edit-attributes">{'Class attributes'|i18n( 'design/admin/class/edit' )}</h2>
    <p>{'The fields of every object of this class. The arrows and the position numbers set the order; tick attributes and press Remove selected attributes to remove them with their content in every object when the class is stored.'|i18n( 'design/admin/class/edit' )}</p>
</div>
{if $attributes}
{def $attribute_categorys        = ezini( 'ClassAttributeSettings', 'CategoryList', 'content.ini' )
     $attribute_default_category = ezini( 'ClassAttributeSettings', 'DefaultCategory', 'content.ini' )
     $priority_value = 0}
<table id="ezcca-edit-list" data-move-failed="{'The attribute could not be moved; the order is as it was.'|i18n( 'design/admin/class/edit' )|wash}" class="special exp-attr-list a4-class-attr-list" cellspacing="0" summary="{'List of class attributes'|i18n( 'design/admin/class/edit' )}">
<tbody>
{section var=Attributes loop=$attributes sequence=array( bglight, bgdark )}
{set $priority_value = $priority_value|sum( 10 )}
<tr class="ezcca-edit-list-item {$Attributes.sequence}"{if $last_changed_id|eq( $Attributes.item.id )} id="LastChangedID"{/if}>
<td>
<table cellspacing="0" class="a4-class-attr" summary="{'Class attribute item'|i18n( 'design/admin/class/edit' )}">
<tr>
    <th scope="col" class="tight"><input type="checkbox" name="ContentAttribute_id_checked[{$Attributes.item.id}]" value="{$Attributes.item.id}" aria-label="{'Select attribute for removal. Click the "Remove selected attributes" button to remove the selected attributes.'|i18n( 'design/admin/class/edit' )|wash}" /></th>
    <th scope="col" class="wide">{$Attributes.number}. {$Attributes.item.name|wash} [{$Attributes.item.data_type.information.name|wash}] (id:{$Attributes.item.id})</th>
    <th scope="col" class="tight">
      <div class="listbutton">
          <button type="submit" class="a4-icon-btn" name="MoveTop" value="{$Attributes.item.id}" title="{'Move this attribute to the top.'|i18n( 'design/admin/class/edit' )|wash}" aria-label="{'Top'|i18n( 'design/admin/class/edit' )|wash}"><span class="a4-icon a4-bi-arrow-bar-up is-medium" aria-hidden="true"></span></button>
          <button type="submit" class="a4-icon-btn" name="MoveUp" value="{$Attributes.item.id}" title="{'Use the order buttons to set the order of the class attributes. The up arrow moves the attribute one place up. The down arrow moves the attribute one place down.'|i18n( 'design/admin/class/edit' )|wash}" aria-label="{'Up'|i18n( 'design/admin/class/edit' )|wash}"><span class="a4-icon a4-bi-arrow-up is-medium" aria-hidden="true"></span></button>
          <button type="submit" class="a4-icon-btn" name="MoveDown" value="{$Attributes.item.id}" title="{'Use the order buttons to set the order of the class attributes. The up arrow moves the attribute one place up. The down arrow moves the attribute one place down.'|i18n( 'design/admin/class/edit' )|wash}" aria-label="{'Down'|i18n( 'design/admin/class/edit' )|wash}"><span class="a4-icon a4-bi-arrow-down is-medium" aria-hidden="true"></span></button>
          <button type="submit" class="a4-icon-btn" name="MoveBottom" value="{$Attributes.item.id}" title="{'Move this attribute to the bottom.'|i18n( 'design/admin/class/edit' )|wash}" aria-label="{'Bottom'|i18n( 'design/admin/class/edit' )|wash}"><span class="a4-icon a4-bi-arrow-bar-down is-medium" aria-hidden="true"></span></button>
          <input size="2" maxlength="4" type="text" name="ContentAttribute_priority[{$Attributes.item.id}]" value="{$priority_value}" aria-label="{'Position'|i18n( 'design/admin/class/edit' )}" />
      </div>
    </th>
</tr>
<tr>
<td colspan="3" class="a4-class-attr-body">
<input type="hidden" name="ContentAttribute_id[{$Attributes.item.id}]" value="{$Attributes.item.id}" />
<input type="hidden" name="ContentAttribute_position[{$Attributes.item.id}]" value="{$Attributes.item.placement}" />

<div class="a4-grid is-wide">
    <div class="a4-field">
        <label for="ContentAttribute_name_{$Attributes.item.id}">{'Name'|i18n( 'design/admin/class/edit' )}</label>
        <input type="text" id="ContentAttribute_name_{$Attributes.item.id}" name="ContentAttribute_name[{$Attributes.item.id}]" value="{$Attributes.item.nameList[$language_code]|wash}" />
    </div>
    <div class="a4-field">
        <label for="ContentAttribute_identifier_{$Attributes.item.id}">{'Identifier'|i18n( 'design/admin/class/edit' )}</label>
        <input type="text" id="ContentAttribute_identifier_{$Attributes.item.id}" name="ContentAttribute_identifier[{$Attributes.item.id}]" value="{$Attributes.item.identifier|wash}" maxlength="50" spellcheck="false" autocomplete="off" />
    </div>
    <div class="a4-field">
        <label for="ContentAttribute_description_{$Attributes.item.id}">{'Description'|i18n( 'design/admin/class/edit' )}</label>
        <input type="text" id="ContentAttribute_description_{$Attributes.item.id}" name="ContentAttribute_description[{$Attributes.item.id}]" value="{$Attributes.item.descriptionList[$language_code]|wash}" />
    </div>
    <div class="a4-field">
        <label for="ContentAttribute_category_{$Attributes.item.id}">{'Category'|i18n( 'design/admin/class/edit' )}</label>
        <select id="ContentAttribute_category_{$Attributes.item.id}" name="ContentAttribute_category_select[{$Attributes.item.id}]" title="{'Use this category to group attributes together in edit interface, some categories might also be hidden in full view if they are for instance only meta attributes.'|i18n( 'design/admin/class/edit' )|wash}">
            <option value="">{'Default'|i18n( 'design/admin/class/edit' )} ({$attribute_categorys[ $attribute_default_category ]|wash})</option>
        {foreach $attribute_categorys as $categoryIdentifier => $categoryName}
            <option value="{$categoryIdentifier|wash}"{if $categoryIdentifier|eq( $Attributes.item.category )} selected="selected"{/if}>{$categoryName|wash}</option>
        {/foreach}
        </select>
    </div>
</div>

<div class="a4-class-flags">
    <label class="a4-check" title="{'Use this checkbox to specify whether the user should be forced to enter information into the attribute.'|i18n( 'design/admin/class/edit' )|wash}"><input type="checkbox" id="ContentAttribute_is_required_{$Attributes.item.id}" name="ContentAttribute_is_required_checked[{$Attributes.item.id}]" value="{$Attributes.item.id}"{if $Attributes.item.is_required} checked="checked"{/if} /> {'Required'|i18n( 'design/admin/class/edit' )}</label>
    {if $Attributes.item.data_type.is_indexable}
    <label class="a4-check" title="{'Use this checkbox to specify whether the contents of the attribute should be indexed by the search engine.'|i18n( 'design/admin/class/edit' )|wash}"><input type="checkbox" id="ContentAttribute_is_searchable_{$Attributes.item.id}" name="ContentAttribute_is_searchable_checked[{$Attributes.item.id}]" value="{$Attributes.item.id}"{if $Attributes.item.is_searchable} checked="checked"{/if} /> {'Searchable'|i18n( 'design/admin/class/edit' )}</label>
    {else}
    <label class="a4-check is-off" title="{'The <%datatype_name> datatype does not support search indexing.'|i18n( 'design/admin/class/edit',, hash( '%datatype_name', $Attributes.item.data_type.information.name ) )|wash}"><input type="checkbox" id="ContentAttribute_is_searchable_{$Attributes.item.id}" name="ContentAttribute_is_searchable_checked[{$Attributes.item.id}]" value="" disabled="disabled" /> {'Searchable'|i18n( 'design/admin/class/edit' )}</label>
    {/if}
    {if $Attributes.item.data_type.is_information_collector}
    <label class="a4-check" title="{'Use this checkbox to specify whether the attribute should collect input from users.'|i18n( 'design/admin/class/edit' )|wash}"><input type="checkbox" id="ContentAttribute_is_information_collector_{$Attributes.item.id}" name="ContentAttribute_is_information_collector_checked[{$Attributes.item.id}]" value="{$Attributes.item.id}"{if $Attributes.item.is_information_collector} checked="checked"{/if} /> {'Information collector'|i18n( 'design/admin/class/edit' )}</label>
    {else}
    <label class="a4-check is-off" title="{'The <%datatype_name> datatype cannot be used as an information collector.'|i18n( 'design/admin/class/edit',, hash( '%datatype_name', $Attributes.item.data_type.information.name ) )|wash}"><input type="checkbox" id="ContentAttribute_is_information_collector_{$Attributes.item.id}" name="ContentAttribute_is_information_collector_checked[{$Attributes.item.id}]" value="" disabled="disabled" /> {'Information collector'|i18n( 'design/admin/class/edit' )}</label>
    {/if}
    <label class="a4-check{if $Attributes.item.data_type.properties.translation_allowed|not} is-off{/if}" title="{'Use this checkbox for attributes that contain non-translatable content.'|i18n( 'design/admin/class/edit' )|wash}"><input type="checkbox" id="ContentAttribute_can_translate_{$Attributes.item.id}" name="ContentAttribute_can_translate_checked[{$Attributes.item.id}]" value="{$Attributes.item.id}"{if or( $Attributes.item.can_translate|eq( 0 ), $Attributes.item.data_type.properties.translation_allowed|not )} checked="checked"{/if}{if $Attributes.item.data_type.properties.translation_allowed|not} disabled="disabled"{/if} /> {'Disable translation'|i18n( 'design/admin/class/edit' )}</label>
</div>

<div class="a4-class-datatype">{class_attribute_edit_gui class_attribute=$Attributes.item}</div>
</td>
</tr>
</table>
</td>
</tr>
{/section}
</tbody>
</table>
{undef $attribute_categorys $attribute_default_category $priority_value}
{else}
<p class="a4-empty">{'This class does not have any attributes.'|i18n( 'design/admin/class/edit' )} {'Choose a type below and press Add attribute.'|i18n( 'design/admin/class/edit' )}</p>
{/if}
</section>

<div class="a4-toolbar is-bottom">
    <div class="a4-actions">
        <button class="a4-btn a4-btn-outline-danger" type="submit" name="RemoveButton" value="1" title="{'Remove the selected attributes.'|i18n( 'design/admin/class/edit' )|wash}"{if $attributes|not} disabled="disabled"{/if}>{'Remove selected attributes'|i18n( 'design/admin/class/edit' )}</button>
        <label class="a4-sr-only" for="DataTypeString">{'Attribute type'|i18n( 'design/admin/class/edit' )}</label>
        {include uri="design:class/datatypes.tpl" name=DataTypes id_name=DataTypeString datatypes=$datatypes current=$datatype}
        <button class="a4-btn" type="submit" name="NewButton" value="1" title="{'Add a new attribute to the class. Use the menu on the left to select the attribute type.'|i18n( 'design/admin/class/edit' )|wash}">{'Add attribute'|i18n( 'design/admin/class/edit' )}</button>
    </div>
</div>
<div class="a4-toolbar is-bottom">
    <div class="a4-actions">
        <button class="a4-btn a4-btn-primary" type="submit" name="StoreButton" value="1" title="{'Store changes and exit from edit mode.'|i18n( 'design/admin/class/edit' )|wash}">{'OK'|i18n( 'design/admin/class/edit' )}</button>
        {if eq( ezini( 'ClassSettings', 'ApplyButton', 'content.ini' ), 'enabled' )}
        <button class="a4-btn" type="submit" name="ApplyButton" value="1" title="{'Store changes and continue editing.'|i18n( 'design/admin/class/edit' )|wash}">{'Apply'|i18n( 'design/admin/class/edit' )}</button>
        {/if}
        <button class="a4-btn" type="submit" name="DiscardButton" value="1" title="{'Discard all changes and exit from edit mode.'|i18n( 'design/admin/class/edit' )|wash}">{'Cancel'|i18n( 'design/admin/class/edit' )}</button>
    </div>
    <p class="a4-meta">{'OK stores the class and updates its objects; Apply stores it and keeps the form open; Cancel throws away the changes and goes back to the page you came from.'|i18n( 'design/admin/class/edit' )}</p>
</div>

</div></div></div>
</div>

<a href="#columns" class="scroll-to-top a4-class-top">&uarr;&nbsp;{'Go to the top'|i18n( 'design/admin/content/edit' )}</a>
{if and( is_set( $redirect_if_discarded ), $redirect_if_discarded )}<input type="hidden" name="RedirectIfDiscarded" value="{$redirect_if_discarded|wash}" />{/if}
</form>

{literal}
<script type="text/javascript">
jQuery(function( $ )//called on document.ready
{
    var errors = document.getElementById( 'class-edit-errors' );
    if ( errors ) errors.focus();

    // .length, not .size(): jQuery 3 removed .size().
    var el = $('#LastChangedID input[name^=ContentAttribute_name]');
    if ( el.length ) {
        window.scrollTo(0, Math.max( el.offset().top - 180, 0 ));
        el.trigger( 'focus' );
    }

    var list = $('#ezcca-edit-list');
    var status = $('<p class="ezcca-move-status a4-class-move-status" role="status" aria-live="polite"></p>').insertBefore( list ).data( 'failed', list.attr( 'data-move-failed' ) );

    // The numbers in front of the names and the row colours follow the rows.
    function renumberAttributes()
    {
        list.children('tbody').children('tr.ezcca-edit-list-item').each( function( i )
        {
            var th = $(this).find('> td > table > tbody > tr > th.wide, > td > table > tr > th.wide').first();
            th.html( th.html().replace( /^\s*\d+\./, ( i + 1 ) + '.' ) );
            $(this).removeClass( 'bglight bgdark' ).addClass( i % 2 ? 'bgdark' : 'bglight' );
        });
    }

    function buttons( on )
    {
        list.find('div.listbutton input[name^=Move]').prop( 'disabled', !on ).toggleClass( 'disabled', !on );
    }

    // Move up, down, to the top or to the bottom, in place; the server stores it, and a move it did not store is
    // put back, so the page never shows an order that is not saved. The priority fields follow the rows, so Apply
    // and OK store the order shown.
    function rows()
    {
        return list.children('tbody').children('tr.ezcca-edit-list-item');
    }
    function priorities()
    {
        rows().each( function( i ) { $(this).find('input[name^=ContentAttribute_priority]').val( ( i + 1 ) * 10 ); } );
    }
    list.find('div.listbutton input[name^=Move]').on('click', function( e )
    {
        e.preventDefault();
        var tr = $(this).closest('tr.ezcca-edit-list-item'), param = this.name.split('_'), action = param[0];
        var all = rows(), from = all.index( tr ), last = all.length - 1;
        var to = { MoveUp: from - 1, MoveDown: from + 1, MoveTop: 0, MoveBottom: last }[ action ];
        if ( to === undefined || to < 0 || to > last || to === from )
            return false;
        buttons( false );
        var place = function( index )
        {
            var others = rows().not( tr );
            if ( index >= others.length ) others.last().after( tr ); else others.eq( index ).before( tr );
            renumberAttributes();
            priorities();
        };
        place( to );

        var postVar = { 'ContentClassHasInput': 0 }, _tokenNode = document.getElementById('ezxform_token_js');
        var meta = document.querySelector('meta[name="csrf-token"]');
        postVar[ action ] = param[1];
        if ( _tokenNode ) postVar['ezxform_token'] = _tokenNode.getAttribute('title');
        $.ajax({
            type: 'POST',
            url: $('#ClassEdit').attr('action'),
            data: postVar,
            headers: meta ? { 'X-CSRF-Token': meta.getAttribute('content') } : {}
        }).done( function( data, text, xhr )
        {
            if ( xhr.getResponseHeader( 'X-Class-Attribute-Moved' ) !== '1' )
            {
                place( from );
                status.text( status.data( 'failed' ) ).addClass( 'is-error' );
                return;
            }
            status.text( '' ).removeClass( 'is-error' );
        }).fail( function( xhr )
        {
            place( from );
            status.text( status.data( 'failed' ) + ' (HTTP ' + xhr.status + ')' ).addClass( 'is-error' );
        }).always( function()
        {
            buttons( true );
        });
        return false;
    });

    // The type menu at the top and the one at the bottom share a name: only the one beside the button pressed counts.
    jQuery('#NewButtonTop').on('click', function()
    {
        jQuery('#DataTypeString').prop('disabled', true);
    });
});
</script>
{/literal}
