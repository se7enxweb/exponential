{* Edit an RSS import (rss/edit_import/<id>).

   One form in the look of the RSS list, in the order the import is set up: the feed (name and source address, which
   Update analyses), where the items go (the destination and the user who owns them), what they become (the class
   and which part of each feed item fills which attribute), and Active. Each field says what it is used for; what
   keeps the import from being stored is listed at the top.

   Every field and button name is the one the view has always read (name, url, Class_ID, Class_Attribute_<id>,
   Object_Attribute_<key>, active, RSSImport_ID, StoreButton, RemoveButton, AnalyzeFeedButton, Update_Class,
   DestinationBrowse, UserBrowse), and every template variable is still set (rss_import, rss_class_array, step).
   Cancel (RemoveButton) discards the draft and goes back to the RSS list. This is an edit view, which admin4 draws
   without its main card; .exp-standalone gives the page its own. The same file is in design/admin and
   design/admin4. Guide: doc/guides/rss-feeds.md *}
{include uri='design:rss/exp_style.tpl'}

{def $errors = first_set( $validation_errors, array() )
     $imported = first_set( $rss_import_imported, false() )
     $analysed = and( is_set( $rss_import.import_description_array.rss_version ), $rss_import.import_description_array.rss_version )}

<form action={"rss/edit_import"|ezurl} method="post" name="RSSImport" class="exp-lists exp-rss exp-standalone" aria-labelledby="rss-import-edit-title">

<div class="context-block">
<div class="box-header"><div class="box-ml">
<div class="exp-title-row">
<h1 class="context-title" id="rss-import-edit-title">{'Edit <%rss_import_name> [RSS Import]'|i18n( 'design/admin/rss/edit_import',, hash( '%rss_import_name', $rss_import.name ) )|wash}</h1>
<span class="exp-meta">{'ID %id'|i18n( 'design/admin/rss/edit_import',, hash( '%id', $rss_import.id ) )}</span>
</div>
</div></div>

<div class="box-bc"><div class="box-ml"><div class="box-content">

<p class="exp-intro">{'An import reads the feed of another site. The rssimport cronjob creates an object below the destination for every item it has not seen before; objects it created stay when the import is changed or removed.'|i18n( 'design/admin/rss/edit_import' )}</p>

{if $errors|count}
<div class="exp-feedback is-bad" role="alert" id="rss-import-errors" tabindex="-1">
    <h2 class="exp-h2">{'Invalid input'|i18n( 'design/admin/rss/edit_import' )}</h2>
    <p>{'The import was not saved. Correct the following and press OK again:'|i18n( 'design/admin/rss/edit_import' )}</p>
    <ul>{foreach $errors as $error}<li>{$error|wash}</li>{/foreach}</ul>
</div>
{/if}

{if and( $imported, $imported.count|gt( 0 ) )}
<div class="exp-feedback is-info" role="status">{'This import has created %count objects so far, the newest on %time.'|i18n( 'design/admin/rss/edit_import',, hash( '%count', $imported.count, '%time', $imported.newest|l10n( shortdatetime ) ) )}</div>
{/if}

<input type="hidden" name="RSSImport_ID" value="{$rss_import.id}" />

{* ---- The feed ---- *}
<section class="exp-panel" aria-labelledby="rss-import-feed-title">
<div class="exp-section-head"><h2 class="exp-h2" id="rss-import-feed-title">{'1. The feed'|i18n( 'design/admin/rss/edit_import' )}</h2></div>
<div class="exp-form-fields exp-form-wide">
    <div class="exp-field">
        <label for="importName">{'Name'|i18n( 'design/admin/rss/edit_import' )}</label>
        <input id="importName" type="text" name="name" value="{$rss_import.name|wash}" maxlength="255" aria-describedby="importName-help" />
        <span class="exp-help" id="importName-help">{'Name of the RSS import. This name is used in the Administration Interface only, to distinguish the different imports from each other.'|i18n( 'design/admin/rss/edit_import' )}</span>
    </div>
    <div class="exp-field">
        <label for="rssImportUrl">{'Source URL'|i18n( 'design/admin/rss/edit_import' )}</label>
        <div class="exp-inline">
            <input id="rssImportUrl" type="text" name="url" value="{$rss_import.url|wash}" spellcheck="false" placeholder="https://" aria-describedby="rssImportUrl-help" />
            <button class="exp-btn" type="submit" name="AnalyzeFeedButton" value="1" title="{'Click this button to proceed and analyze the import feed.'|i18n( 'design/admin/rss/edit_import' )}">{'Update'|i18n( 'design/admin/rss/edit_import' )}</button>
        </div>
        <span class="exp-help" id="rssImportUrl-help">{'The http or https address of the feed. Update reads it and finds its format; the fields below appear once it has been read.'|i18n( 'design/admin/rss/edit_import' )}</span>
        {if $analysed}<span class="exp-badge is-ok">{'RSS Version'|i18n( 'design/admin/rss/edit_import' )}: {$rss_import.import_description_array.rss_version|wash}</span>
        {elseif $rss_import.url|ne( '' )}<span class="exp-badge is-warn">{'Not read yet: press Update.'|i18n( 'design/admin/rss/edit_import' )}</span>{/if}
    </div>
</div>
</section>

{* ---- Where the items go ---- *}
<section class="exp-panel" aria-labelledby="rss-import-dest-title">
<div class="exp-section-head"><h2 class="exp-h2" id="rss-import-dest-title">{'2. Where the items go'|i18n( 'design/admin/rss/edit_import' )}</h2></div>
<div class="exp-form-fields exp-form-wide">
    <div class="exp-field">
        <label for="rssImportDest">{'Destination path'|i18n( 'design/admin/rss/edit_import' )}</label>
        <div class="exp-inline">
            <input type="text" id="rssImportDest" readonly="readonly" value="{$rss_import.destination_path|wash}" placeholder="{'No location chosen'|i18n( 'design/admin/rss/edit_import' )}" aria-describedby="rssImportDest-help" />
            <button class="exp-btn" type="submit" name="DestinationBrowse" value="1">{'Browse'|i18n( 'design/admin/rss/edit_import' )}</button>
        </div>
        <span class="exp-help" id="rssImportDest-help">{'Click this button to select the destination node where objects created by the import are located.'|i18n( 'design/admin/rss/edit_import' )}</span>
    </div>
    {if $analysed}
    <div class="exp-field">
        <span class="exp-label">{'Imported objects will be owned by'|i18n( 'design/admin/rss/edit_import' )}</span>
        <div class="exp-inline">
            <input type="text" readonly="readonly" value="{$rss_import.object_owner.contentobject.name|wash}" aria-label="{'Imported objects will be owned by'|i18n( 'design/admin/rss/edit_import' )}" />
            <button class="exp-btn" type="submit" name="UserBrowse" value="1">{'Change user'|i18n( 'design/admin/rss/edit_import' )}</button>
        </div>
        <span class="exp-help">{'Click this button to select the user who should own the objects created by the import.'|i18n( 'design/admin/rss/edit_import' )}</span>
    </div>
    {/if}
</div>
</section>

{if $analysed}
{* ---- What they become ---- *}
<section class="exp-panel" aria-labelledby="rss-import-class-title">
<div class="exp-section-head">
    <h2 class="exp-h2" id="rss-import-class-title">{'3. What each item becomes'|i18n( 'design/admin/rss/edit_import' )}</h2>
    <p>{'Choose the class of the new objects and press Set; then choose which part of a feed item fills each attribute. Ignore leaves an attribute empty.'|i18n( 'design/admin/rss/edit_import' )}</p>
</div>
<div class="exp-form-fields exp-form-wide">
    <div class="exp-field">
        <label for="rssImportClassId">{'Class'|i18n( 'design/admin/rss/edit_import' )}</label>
        <div class="exp-inline">
            <select id="rssImportClassId" name="Class_ID">
            {section name=ContentClass loop=$rss_class_array}
                <option value="{$:item.id}"{if eq( $:item.id, $rss_import.class_id )} selected="selected"{/if}>{$:item.name|wash}</option>
            {/section}
            </select>
            <button class="exp-btn" type="submit" name="Update_Class" value="1">{'Set'|i18n( 'design/admin/rss/edit_import' )}</button>
        </div>
        <span class="exp-help">{'Click this button to load the correct values into the drop-down fields below. Use the drop-down menu on the left to select the class.'|i18n( 'design/admin/rss/edit_import' )}</span>
    </div>
</div>

{if $rss_import.class_id|gt( 0 )}
{def $import_description_array = $rss_import.import_description_array
     $field_map = $rss_import.field_map}
<h3 style="margin-top: 18px;">{'Class attributes'|i18n( 'design/admin/rss/edit_import' )}</h3>
<div class="exp-mapping">
{foreach $rss_import.class_attributes as $class_attribute}
    <div class="exp-field">
        <label for="rssImportClassAttributeId_{$class_attribute.id}">{$class_attribute.name|wash}</label>
        <select id="rssImportClassAttributeId_{$class_attribute.id}" name="Class_Attribute_{$class_attribute.id}">
            <option value="-1">{'Ignore'|i18n( 'design/admin/rss/edit_import' )}</option>
        {foreach $field_map as $key => $value}
            <option value="{$key|wash}"{if and( is_set( $import_description_array.class_attributes[$class_attribute.id] ), $import_description_array.class_attributes[$class_attribute.id]|eq( $key ) )} selected="selected"{/if}>{$value|wash}</option>
        {/foreach}
        </select>
    </div>
{/foreach}
</div>

<h3 style="margin-top: 18px;">{'Object attributes'|i18n( 'design/admin/rss/edit_import' )}</h3>
<div class="exp-mapping">
{foreach $rss_import.object_attribute_list as $key => $object_attribute}
    <div class="exp-field">
        <label for="rssImportObjectAttributeId_{$key|wash}">{$object_attribute|wash}</label>
        <select id="rssImportObjectAttributeId_{$key|wash}" name="Object_Attribute_{$key|wash}">
            <option value="-1">{'Ignore'|i18n( 'design/admin/rss/edit_import' )}</option>
        {foreach $field_map as $key2 => $value}
            <option value="{$key2|wash}"{if and( is_set( $import_description_array.object_attributes[$key] ), $import_description_array.object_attributes[$key]|eq( $key2 ) )} selected="selected"{/if}>{$value|wash}</option>
        {/foreach}
        </select>
    </div>
{/foreach}
</div>
{undef $import_description_array $field_map}
{/if}
</section>

<section class="exp-panel" aria-labelledby="rss-import-active-title">
<div class="exp-section-head"><h2 class="exp-h2" id="rss-import-active-title">{'4. Run it'|i18n( 'design/admin/rss/edit_import' )}</h2></div>
<div class="exp-field">
    <label class="exp-check"><input type="checkbox" id="rssImportActive" name="active"{if $rss_import.active|eq( 1 )} checked="checked"{/if} aria-describedby="rssImportActive-help" /> {'Active'|i18n( 'design/admin/rss/edit_import' )}</label>
    <span class="exp-help" id="rssImportActive-help">{'Use this checkbox to control if the RSS feed is active or not. An inactive feed will not be automatically updated.'|i18n( 'design/admin/rss/edit_import' )}</span>
</div>
</section>
{/if}

<div class="exp-bottombar">
    <div class="exp-actions">
        <button class="exp-btn exp-btn-primary" type="submit" name="StoreButton" value="1" title="{'Apply the changes and return to the RSS overview.'|i18n( 'design/admin/rss/edit_import' )}">{'OK'|i18n( 'design/admin/rss/edit_import' )}</button>
        <button class="exp-btn" type="submit" name="RemoveButton" value="1" title="{'Cancel the changes and return to the RSS overview.'|i18n( 'design/admin/rss/edit_import' )}">{'Cancel'|i18n( 'design/admin/rss/edit_import' )}</button>
    </div>
    <p class="exp-meta">{'OK saves the import and goes back to the RSS list. Cancel throws away the changes made since the import was opened and goes back to the list.'|i18n( 'design/admin/rss/edit_import' )}</p>
</div>

</div></div></div>
</div>
</form>
{undef $errors $imported $analysed}

<script type="text/javascript">
var expRssImportFocus = {if $errors|count}'rss-import-errors'{else}'importName'{/if};
{literal}
( function () {
    var focus = document.getElementById( expRssImportFocus );
    if ( focus ) { focus.focus(); if ( focus.select ) focus.select(); }
} )();
{/literal}
</script>
