{* A class (class/view/<id>[/(language)/<locale>]).

   Leads with Edit (in the language chosen), then the class's figures and settings, its attributes as cards (name,
   identifier, type, category, description, flags and the type's own settings), and the groups, translations and
   override templates of the class, each shown or hidden as before by the user preferences behind the chips.

   Every template variable (class, attributes, datatypes, language_code, validation, scheduled_script_id, module)
   and every field and button name (EditLanguage, _DefaultButton; the included windows post AddGroupButton,
   RemoveGroupButton, ContentClass_group, group_id_checked[] and the translation buttons) is unchanged. The same
   file is in design/admin and design/admin4. Guide: doc/guides/class-groups.md *}
{include uri='design:class/exp_style.tpl'}

<div class="context-block exp-lists exp-classgroups exp-classview">

<div class="box-header"><div class="box-ml">
<div class="exp-title-row">
<h1 class="context-title" title="{'Class name and number of objects'|i18n( 'design/admin/class/view' )}">{$class.identifier|class_icon( 'normal', $class.nameList[$language_code]|wash )}&nbsp;{$class.nameList[$language_code]|wash}</h1>
<code class="exp-title-key">{$class.identifier|wash}</code>
<span class="exp-meta">{'ID %id'|i18n( 'design/admin/class/grouplist',, hash( '%id', $class.id ) )}</span>
</div>
</div></div>

<div class="box-bc"><div class="box-ml"><div class="box-content">

{if and( $validation.processed, $validation.groups )}
<div class="exp-feedback is-warn" role="alert">
    <p><strong>{'Input did not validate'|i18n( 'design/admin/class/view' )}</strong></p>
    <ul>{foreach $validation.groups as $item}<li>{$item.text|wash}</li>{/foreach}</ul>
</div>
{/if}

{if $scheduled_script_id|gt( 0 )}
<div class="exp-feedback is-info" role="status">
    <p><strong>{'Class storing deferred'|i18n( 'design/admin/class/view' )}</strong></p>
    <p>{'The storing of the class has been deferred because existing objects need to be updated. The process has been scheduled to run in the background and will be started automatically. Please do not edit the class again until the process has finished. You can monitor the progress of the background process here:'|i18n( 'design/admin/class/view' )}
       <a href={concat( 'scriptmonitor/view/', $scheduled_script_id )|ezurl}>{'Background process monitor'|i18n( 'design/admin/class/view' )}</a></p>
</div>
{/if}

<form action={concat( '/class/edit/', $class.id )|ezurl} method="post" class="exp-actionbar">
    {def $locale = fetch( 'content', 'locale', hash( 'locale_code', $language_code ) )}
    <p class="exp-meta">{'Last modified: %time, %username'|i18n( 'design/admin/class/view',, hash( '%username', $class.modifier.contentobject.name, '%time', $class.modified|l10n( shortdatetime ) ) )|wash}
        &middot; <img src="{$language_code|flag_icon}" width="18" height="12" alt="" style="vertical-align: -1px;" /> {$locale.intl_language_name|wash}</p>
    {undef $locale}
    <div class="exp-actions">
    {def $languages = $class.prioritized_languages
         $availableLanguages = fetch( 'content', 'prioritized_languages' )}
    {if and( eq( $availableLanguages|count, 1 ), eq( $languages|count, 1 ), is_set( $languages[$availableLanguages[0].locale] ) )}
        <input type="hidden" name="EditLanguage" value="{$availableLanguages[0].locale|wash}" />
    {else}
        <label class="exp-sr" for="classViewEditLanguage">{'Language'|i18n( 'design/admin/class/view' )}</label>
        <select id="classViewEditLanguage" class="exp-select-inline" name="EditLanguage" title="{'Use this menu to select the language you want to use for editing then click the "Edit" button.'|i18n( 'design/admin/class/view' )|wash}">
        {foreach $languages as $language}
            <option value="{$language.locale|wash}">{$language.name|wash}</option>
        {/foreach}
        {if gt( $class.can_create_languages|count, 0 )}
            <option value="">{'Another language'|i18n( 'design/admin/class/view' )}</option>
        {/if}
        </select>
    {/if}
    {undef $languages $availableLanguages}
        <button class="exp-btn exp-btn-primary" type="submit" name="_DefaultButton" value="1" title="{'Edit this class.'|i18n( 'design/admin/class/view' )}">{'Edit'|i18n( 'design/admin/class/view' )}</button>
    </div>
</form>

<ul class="exp-figures">
    <li class="exp-figure"><strong>{$class.object_count}</strong><span>{'Object count'|i18n( 'design/admin/class/view' )}</span></li>
    <li class="exp-figure"><strong>{$attributes|count}</strong><span>{'Attributes'|i18n( 'design/admin/class/view' )}</span></li>
    <li class="exp-figure"><strong>{$class.ingroup_list|count}</strong><span>{'Class groups'|i18n( 'design/admin/class/view' )}</span></li>
    <li class="exp-figure"><strong>{$class.prioritized_languages|count}</strong><span>{'Translations'|i18n( 'design/admin/class/view' )}</span></li>
</ul>

<section class="exp-panel" aria-labelledby="class-view-settings">
<div class="exp-section-head"><h2 class="exp-h2" id="class-view-settings">{'Settings'|i18n( 'design/admin/class/view' )}</h2></div>
<dl class="exp-facts">
    <div><dt>{'Name'|i18n( 'design/admin/class/view' )}</dt><dd>{$class.nameList[$language_code]|wash}</dd></div>
    <div><dt>{'Identifier'|i18n( 'design/admin/class/view' )}</dt><dd><code>{$class.identifier|wash}</code></dd></div>
    <div><dt>{'Object name pattern'|i18n( 'design/admin/class/view' )}</dt><dd><code>{$class.contentobject_name|wash}</code></dd></div>
    <div><dt>{'URL alias name pattern'|i18n( 'design/admin/class/view' )}</dt><dd>{if $class.url_alias_name|ne( '' )}<code>{$class.url_alias_name|wash}</code>{else}<span class="exp-muted">{'as the object name'|i18n( 'design/admin/class/view' )}</span>{/if}</dd></div>
    <div><dt>{'Container'|i18n( 'design/admin/class/view' )}</dt><dd>{if $class.is_container|eq( 1 )}{'Yes'|i18n( 'design/admin/class/view' )}{else}{'No'|i18n( 'design/admin/class/view' )}{/if}</dd></div>
    <div><dt>{'Default object availability'|i18n( 'design/admin/class/view' )}</dt><dd>{if $class.always_available|eq( 0 )}{'Not available'|i18n( 'design/admin/class/view' )}{else}{'Available'|i18n( 'design/admin/class/view' )}{/if}</dd></div>
    <div><dt>{'Default sorting of children'|i18n( 'design/admin/class/view' )}</dt><dd>{def $sort_fields = fetch( content, available_sort_fields )}{if is_set( $sort_fields[$class.sort_field] )}{$sort_fields[$class.sort_field]|wash}{else}{$class.sort_field|wash}{/if} / {if eq( $class.sort_order, 0 )}{'Descending'|i18n( 'design/admin/class/edit' )}{else}{'Ascending'|i18n( 'design/admin/class/edit' )}{/if}{undef $sort_fields}</dd></div>
    <div style="grid-column: 1 / -1;"><dt>{'Description'|i18n( 'design/admin/class/view' )}</dt><dd>{if $class.descriptionList[$language_code]|ne( '' )}{$class.descriptionList[$language_code]|wash}{else}<span class="exp-muted">{'None'|i18n( 'design/admin/class/view' )}</span>{/if}</dd></div>
</dl>
</section>

<section class="exp-section" aria-labelledby="class-view-attributes">
<div class="exp-section-head">
    <h2 class="exp-h2" id="class-view-attributes">{'Attributes'|i18n( 'design/admin/class/view' )}</h2>
    <p>{'The fields every object of this class has, in the order the edit form shows them.'|i18n( 'design/admin/class/view' )}</p>
</div>
{if $attributes|count|eq( 0 )}
<p class="exp-empty">{'This class does not have any attributes.'|i18n( 'design/admin/class/edit' )}</p>
{else}
{def $attribute_categorys        = ezini( 'ClassAttributeSettings', 'CategoryList', 'content.ini' )
     $attribute_default_category = ezini( 'ClassAttributeSettings', 'DefaultCategory', 'content.ini' )}
<ol class="exp-secs exp-attrs">
{foreach $attributes as $index => $attribute}
<li class="exp-sec">
    <input type="hidden" name="ContentAttribute_id[]" value="{$attribute.id}" />
    <input type="hidden" name="ContentAttribute_position[]" value="{$attribute.placement}" />
    <div class="exp-sec-head">
        <div class="exp-sec-title">
            <h3>{inc( $index )}. {$attribute.nameList[$language_code]|wash}</h3>
            <code class="exp-title-key">{$attribute.identifier|wash}</code>
            <ul class="exp-badges">
                <li class="exp-badge is-info">{$attribute.data_type.information.name|wash}</li>
                {if $attribute.is_required}<li class="exp-badge is-warn">{'Is required'|i18n( 'design/admin/class/view' )}</li>{/if}
                {if and( $attribute.data_type.is_indexable, $attribute.is_searchable )}<li class="exp-badge is-ok">{'Is searchable'|i18n( 'design/admin/class/view' )}</li>{/if}
                {if and( $attribute.data_type.is_information_collector, $attribute.is_information_collector )}<li class="exp-badge is-ok">{'Collects information'|i18n( 'design/admin/class/view' )}</li>{/if}
                {if or( $attribute.can_translate|eq( 0 ), $attribute.data_type.properties.translation_allowed|not )}<li class="exp-badge">{'Translation is disabled'|i18n( 'design/admin/class/view' )}</li>{/if}
            </ul>
            <span class="exp-meta">(id:{$attribute.id})</span>
        </div>
    </div>
    <dl class="exp-facts">
        <div><dt>{'Category'|i18n( 'design/admin/class/view' )}</dt><dd>{if $attribute.category|not}{'Default'|i18n( 'design/admin/class/edit' )} ({$attribute_categorys[ $attribute_default_category ]|wash}){elseif is_set( $attribute_categorys[ $attribute.category ] )}{$attribute_categorys[ $attribute.category ]|wash}{else}{$attribute_categorys[ $attribute_default_category ]|wash}{/if}</dd></div>
        <div><dt>{'Flags'|i18n( 'design/admin/class/view' )}</dt><dd>{if $attribute.is_required}{'Is required'|i18n( 'design/admin/class/view' )}{else}{'Is not required'|i18n( 'design/admin/class/view' )}{/if}, {if and( $attribute.data_type.is_indexable, $attribute.is_searchable )}{'Is searchable'|i18n( 'design/admin/class/view' )}{else}{'Is not searchable'|i18n( 'design/admin/class/view' )}{/if}, {if and( $attribute.data_type.is_information_collector, $attribute.is_information_collector )}{'Collects information'|i18n( 'design/admin/class/view' )}{else}{'Does not collect information'|i18n( 'design/admin/class/view' )}{/if}</dd></div>
        <div style="grid-column: 1 / -1;"><dt>{'Description'|i18n( 'design/admin/class/view' )}</dt><dd>{if $attribute.descriptionList[$language_code]|ne( '' )}{$attribute.descriptionList[$language_code]|wash}{else}<span class="exp-muted">{'None'|i18n( 'design/admin/class/view' )}</span>{/if}</dd></div>
    </dl>
    <div class="exp-datatype">{class_attribute_view_gui class_attribute=$attribute}</div>
</li>
{/foreach}
</ol>
{undef $attribute_categorys $attribute_default_category}
{/if}
</section>

{include uri="design:class/windows.tpl"}

</div></div></div>
</div>
