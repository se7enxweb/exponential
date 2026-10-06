{* The translations of a class, on the class page: view or edit one, set the main language, remove the ticked
   ones (class/translation asks first). Field and button names (LanguageID[], InitialLanguageID,
   RemoveTranslationButton, UpdateInitialLanguageButton, ContentClassID, ContentClassLanguageCode) are unchanged.
   The same file is in design/admin and design/admin4. *}
{def $translations = $class.prioritized_languages
     $translations_count = $translations|count}

<form name="translationsform" method="post" action={'class/translation'|ezurl}>
<input type="hidden" name="ContentClassID" value="{$class.id}" />
<input type="hidden" name="ContentClassLanguageCode" value="{$language_code|wash}" />

<section class="exp-section" aria-labelledby="class-view-translations">
<div class="exp-section-head">
    <h2 class="exp-h2" id="class-view-translations">{'Translations (%translations)'|i18n( 'design/admin/class/view',, hash( '%translations', $translations_count ) )}</h2>
    <p>{'The class name, description and attribute names in each language. The main language cannot be removed.'|i18n( 'design/admin/class/view' )}</p>
</div>
<div class="exp-table-wrap">
<table class="exp-table">
<thead><tr>
    <th scope="col"><span class="exp-sr">{'Select'|i18n( 'design/admin/class/classlist' )}</span></th>
    <th scope="col">{'Language'|i18n( 'design/admin/class/view' )}</th>
    <th scope="col">{'Locale'|i18n( 'design/admin/class/view' )}</th>
    <th scope="col">{'Main'|i18n( 'design/admin/class/view' )}</th>
    <th scope="col"><span class="exp-sr">{'Edit'|i18n( 'design/admin/class/view' )}</span></th>
</tr></thead>
<tbody>
{foreach $translations as $translation}
<tr>
    <td><input type="checkbox" name="LanguageID[]" value="{$translation.id}"{if $translation.id|eq( $class.initial_language_id )} disabled="disabled"{/if} aria-label="{$translation.name|wash}" /></td>
    <td><img src="{$translation.locale|flag_icon}" width="18" height="12" alt="" />&nbsp;<a href={concat( 'class/view/', $class.id, '/(language)/', $translation.locale )|ezurl} title="{'View translation.'|i18n( 'design/admin/class/view' )}">{if eq( $translation.locale, $language_code )}<strong>{$translation.name|wash}</strong>{else}{$translation.name|wash}{/if}</a></td>
    <td><code>{$translation.locale|wash}</code></td>
    <td><input type="radio"{if $translation.id|eq( $class.initial_language_id )} checked="checked"{/if} name="InitialLanguageID" value="{$translation.id}" aria-label="{'Use these radio buttons to select the desired main language.'|i18n( 'design/admin/class/view' )}" /></td>
    <td><a class="exp-btn exp-btn-small" href={concat( 'class/edit/', $class.id, '/(language)/', $translation.locale )|ezurl} title="{'Edit in <%language_name>.'|i18n( 'design/admin/class/view',, hash( '%language_name', $translation.locale_object.intl_language_name ) )|wash}">{'Edit'|i18n( 'design/admin/class/view' )}</a></td>
</tr>
{/foreach}
</tbody>
</table>
</div>
<div class="exp-bottombar">
    <div class="exp-actions">
        <button class="exp-btn exp-btn-outline-danger" type="submit" name="RemoveTranslationButton" value="1" title="{'Remove selected languages from the list above.'|i18n( 'design/admin/class/view' )}"{if $translations_count|le( 1 )} disabled="disabled"{/if}>{'Remove selected'|i18n( 'design/admin/class/view' )}</button>
        <button class="exp-btn" type="submit" name="UpdateInitialLanguageButton" value="1" title="{'Select the desired main language using the radio buttons above then click this button to store the setting.'|i18n( 'design/admin/class/view' )}"{if $translations_count|le( 1 )} disabled="disabled"{/if}>{'Set main'|i18n( 'design/admin/class/view' )}</button>
    </div>
</div>
</section>
</form>
{undef $translations $translations_count}
