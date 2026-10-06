{* One content language: how much content and which classes have it, which siteaccesses show it, whether it can be
   removed and how to move content out of it, the objects and classes that have it, and its locale settings.

   The Remove button posts DeleteIDArray[] with RemoveButton to content/translations, as it always has. It is off
   while anything has a translation in the language (the kernel would refuse) and for the language the
   administration interface runs in. Works without javascript. The same file is in design/admin and design/admin4.
   User guide: doc/guides/content-languages.md. *}
{include uri='design:content/languages_style.tpl'}
{def $language = $language_row
     $locale_object = $translation.locale_object
     $is_interface = eq( false()|locale.locale_code, $locale_object.locale_code )}

<div class="context-block content-translations content-translations-view exp-languages">

<div class="box-header"><div class="box-ml">
<h1 class="context-title"><img src="{$language.locale|flag_icon}" width="18" height="12" alt="" /> <span class="exp-title-text">{$language.name|wash}</span> <code>{$translation.locale|wash}</code></h1>
<div class="header-mainline"></div>
</div></div>

<div class="box-bc"><div class="box-ml"><div class="box-content">

<p class="exp-intro"><a href={'/content/translations'|ezurl}>&larr; {'All content languages'|i18n( 'design/admin/content/translationview' )}</a></p>

<ul aria-label="{'State'|i18n( 'design/admin/content/translationview' )}" class="exp-badges exp-state">
    {if $language.is_default}<li class="exp-badge is-info">{'Default for new content'|i18n( 'design/admin/content/translationview' )}</li>{/if}
    {if $is_interface}<li class="exp-badge is-info">{'Language of this interface'|i18n( 'design/admin/content/translationview' )}</li>{/if}
    {if $language.unlisted}<li class="exp-badge is-warn">{'In no SiteLanguageList'|i18n( 'design/admin/content/translationview' )}</li>{/if}
    {if $language.few}<li class="exp-badge is-warn">{'Only %count objects'|i18n( 'design/admin/content/translationview',, hash( '%count', $language.objects ) )}</li>{/if}
    {if $language.removable}<li class="exp-badge is-ok">{'Unused'|i18n( 'design/admin/content/translationview' )}</li>{else}<li class="exp-badge">{'In use'|i18n( 'design/admin/content/translationview' )}</li>{/if}
</ul>

<section aria-labelledby="language-figures-title">
<h2 class="exp-sr" id="language-figures-title">{'Overview'|i18n( 'design/admin/content/translationview' )}</h2>
<ul class="exp-figures">
    <li class="exp-figure{if $language.few} is-attention{/if}"><strong>{$language.objects}</strong><span>{'Objects with a translation in it'|i18n( 'design/admin/content/translationview' )}</span></li>
    <li class="exp-figure"><strong>{$language.objects_main}</strong><span>{'Of them in it as main language'|i18n( 'design/admin/content/translationview' )}</span></li>
    <li class="exp-figure"><strong>{$language.objects_only}</strong><span>{'Only in this language'|i18n( 'design/admin/content/translationview' )}</span></li>
    <li class="exp-figure"><strong>{$language.classes}</strong><span>{'Classes with a name in it'|i18n( 'design/admin/content/translationview' )}</span></li>
    <li class="exp-figure"><strong>{$translation.id}</strong><span>{'ID, bit %bit of the language mask'|i18n( 'design/admin/content/translationview',, hash( '%bit', $language.bit ) )}</span></li>
</ul>
</section>

{* Which siteaccesses show it. *}
<section class="exp-section" aria-labelledby="language-sites-title">
<div class="exp-section-head"><h2 class="exp-h2" id="language-sites-title">{'Shown by'|i18n( 'design/admin/content/translationview' )}</h2></div>
{if $language.sites|count|gt(0)}
<ul class="exp-chips">{foreach $language.sites as $language_site}<li class="exp-chip{if $language.main_sites|contains( $language_site )} is-main{/if}">{$language_site|wash}</li>{/foreach}</ul>
{if $language.main_sites|count|gt(0)}<p class="exp-meta exp-legend">{'Highlighted: the siteaccesses that list it first, so show it first.'|i18n( 'design/admin/content/translationview' )}</p>{/if}
{else}
<div class="exp-feedback is-warn"><p>{'No siteaccess lists %locale in its SiteLanguageList, so no site shows content in it, except objects that are always available and have no translation in a language of the site.'|i18n( 'design/admin/content/translationview',, hash( '%locale', $translation.locale ) )|wash}</p>
<p>{'To show it, add SiteLanguageList[]=%locale to settings/siteaccess/<siteaccess>/site.ini.append.php. If it should not be used at all, move its content into another language and remove it.'|i18n( 'design/admin/content/translationview',, hash( '%locale', $translation.locale ) )|wash}</p></div>
{/if}
</section>

{* Moving content out of the language, then removing it. *}
<section class="exp-section" aria-labelledby="language-remove-title" id="language-move">
<div class="exp-section-head"><h2 class="exp-h2" id="language-remove-title">{'Removing this language'|i18n( 'design/admin/content/translationview' )}</h2></div>
{if $language.removable}
<div class="exp-feedback is-ok"><p>{'No object and no class has a translation in %locale, so it can be removed. Nothing else is deleted.'|i18n( 'design/admin/content/translationview',, hash( '%locale', $translation.locale ) )|wash}</p>
{if $language.sites|count|gt(0)}<p>{'%sites still list it: take it out of their SiteLanguageList as well, or they will list a language that does not exist.'|i18n( 'design/admin/content/translationview',, hash( '%sites', $language.sites|implode( ', ' ) ) )|wash}</p>{/if}
{if $is_interface}<p>{'It is the language this administration interface runs in, so it is not removed from here.'|i18n( 'design/admin/content/translationview' )}</p>{/if}
</div>
{else}
<div class="exp-feedback is-info">
<p>{'%name cannot be removed while %objects objects and %classes classes have a translation in it; the kernel refuses it. To take it out of use without losing anything:'|i18n( 'design/admin/content/translationview',, hash( '%name', $language.name, '%objects', $language.objects, '%classes', $language.classes ) )|wash}</p>
<ol class="exp-steps">
    <li>{'For each object below, open it and translate it into the language it should be in (Edit, then choose that language and translate from %locale). Publish.'|i18n( 'design/admin/content/translationview',, hash( '%locale', $translation.locale ) )|wash}</li>
    <li>{'In the Translations window of the object, make the new language its main language, then remove the %locale translation.'|i18n( 'design/admin/content/translationview',, hash( '%locale', $translation.locale ) )|wash}</li>
    <li>{'For each class below, open it (Setup, Classes); in its Translations window make another language the main one if needed (Set main), then remove the %locale translation. A class edit that was never stored counts too: edit that class and store or cancel the edit.'|i18n( 'design/admin/content/translationview',, hash( '%locale', $translation.locale ) )|wash}</li>
    <li>{'When both counts are 0, remove the language here and take it out of every SiteLanguageList.'|i18n( 'design/admin/content/translationview' )}</li>
</ol>
</div>
{/if}
<form name="languageform" action={$module.functions.translations.uri|ezurl} method="post" class="exp-actions">
    <input type="hidden" name="DeleteIDArray[]" value="{$translation.id}" />
    {if and( $language.removable, $is_interface|not )}
    <button type="submit" class="exp-btn exp-btn-danger" name="RemoveButton" value="{'Remove'|i18n( 'design/admin/content/translationview' )}">{'Remove %name'|i18n( 'design/admin/content/translationview',, hash( '%name', $language.name ) )|wash}</button>
    {else}
    <button type="submit" class="exp-btn exp-btn-danger button-disabled" name="RemoveButton" value="{'Remove'|i18n( 'design/admin/content/translationview' )}" disabled="disabled">{'Remove %name'|i18n( 'design/admin/content/translationview',, hash( '%name', $language.name ) )|wash}</button>
    {/if}
</form>
</section>

{* The objects that have it. *}
<details class="exp-fold" id="language-objects"{if and( $language.objects|gt(0), $language.objects|le( $list_limit ) )} open="open"{/if}>
    <summary>
        <h2 class="exp-h2" id="language-objects-title">{'Objects with a translation in it'|i18n( 'design/admin/content/translationview' )}</h2>
        <span class="exp-meta">{if $language.objects|gt( $list_limit )}{'The %shown changed last, of %count'|i18n( 'design/admin/content/translationview',, hash( '%shown', $language_objects|count, '%count', $language.objects ) )}{else}{'%count objects'|i18n( 'design/admin/content/translationview',, hash( '%count', $language.objects ) )}{/if}</span>
    </summary>
    <div class="exp-fold-body">
    {if $language_objects|count|gt(0)}
    <div class="exp-table-wrap" role="region" aria-labelledby="language-objects-title" tabindex="0">
    <table class="exp-table">
    <thead><tr>
        <th scope="col">{'Object'|i18n( 'design/admin/content/translationview' )}</th>
        <th scope="col">{'Class'|i18n( 'design/admin/content/translationview' )}</th>
        <th scope="col">{'Main language'|i18n( 'design/admin/content/translationview' )}</th>
        <th scope="col">{'Languages'|i18n( 'design/admin/content/translationview' )}</th>
    </tr></thead>
    <tbody>
    {foreach $language_objects as $language_object}
    <tr>
        <td>{if $language_object.node_id|gt(0)}<a href={concat( '/content/view/full/', $language_object.node_id )|ezurl}>{$language_object.name|wash}</a>{else}{$language_object.name|wash} <span class="exp-meta">({'no location'|i18n( 'design/admin/content/translationview' )})</span>{/if}
            {if $language_object.always_available}<br /><span class="exp-meta">{'always available'|i18n( 'design/admin/content/translationview' )}</span>{/if}</td>
        <td>{$language_object.class_name|wash}</td>
        <td><code>{$language_object.main_locale|wash}</code>{if $language_object.is_main} <span class="exp-badge is-warn">{'this one'|i18n( 'design/admin/content/translationview' )}</span>{/if}</td>
        <td><ul class="exp-chips">{foreach $language_object.languages as $object_locale}<li class="exp-chip">{$object_locale|wash}</li>{/foreach}</ul></td>
    </tr>
    {/foreach}
    </tbody>
    </table>
    </div>
    {else}
    <p class="exp-empty">{'No object has a translation in this language.'|i18n( 'design/admin/content/translationview' )}</p>
    {/if}
    </div>
</details>

{* The classes that have it. *}
<details class="exp-fold" id="language-classes"{if and( $language.classes|gt(0), $language.objects|eq(0) )} open="open"{/if}>
    <summary>
        <h2 class="exp-h2" id="language-classes-title">{'Classes with a name in it'|i18n( 'design/admin/content/translationview' )}</h2>
        <span class="exp-meta">{'%count classes'|i18n( 'design/admin/content/translationview',, hash( '%count', $language_classes|count ) )}</span>
    </summary>
    <div class="exp-fold-body">
    {if $language_classes|count|gt(0)}
    <div class="exp-table-wrap" role="region" aria-labelledby="language-classes-title" tabindex="0">
    <table class="exp-table">
    <thead><tr>
        <th scope="col">{'Class'|i18n( 'design/admin/content/translationview' )}</th>
        <th scope="col">{'Identifier'|i18n( 'design/admin/content/translationview' )}</th>
        <th scope="col">{'Main language'|i18n( 'design/admin/content/translationview' )}</th>
    </tr></thead>
    <tbody>
    {foreach $language_classes as $language_class}
    <tr>
        <td><a href={concat( '/class/view/', $language_class.id )|ezurl}>{$language_class.name|wash}</a>{if $language_class.is_draft} <span class="exp-badge is-warn" title="{'A class edit that was never stored; it counts until it is stored or cancelled.'|i18n( 'design/admin/content/translationview' )}">{'unsaved edit'|i18n( 'design/admin/content/translationview' )}</span>{/if}</td>
        <td><code>{$language_class.identifier|wash}</code></td>
        <td><code>{$language_class.main_locale|wash}</code>{if $language_class.is_main} <span class="exp-badge is-warn">{'this one'|i18n( 'design/admin/content/translationview' )}</span>{/if}</td>
    </tr>
    {/foreach}
    </tbody>
    </table>
    </div>
    {else}
    <p class="exp-empty">{'No class has a name in this language.'|i18n( 'design/admin/content/translationview' )}</p>
    {/if}
    </div>
</details>

{* The locale: how dates, numbers and money are written in it (share/locale/<code>.ini). *}
<details class="exp-fold" id="language-locale">
    <summary>
        <h2 class="exp-h2">{'%locale [Locale]'|i18n( 'design/admin/content/translationview',, hash( '%locale', $translation.locale ) )}</h2>
        <span class="exp-meta">{'How dates, numbers and money are written in it, from share/locale'|i18n( 'design/admin/content/translationview' )}</span>
    </summary>
    <div class="exp-fold-body">
    {def $locale_fields = array(
        hash( 'label', 'Locale'|i18n( 'design/admin/content/translationview' ), 'value', $translation.locale ),
        hash( 'label', 'Charset'|i18n( 'design/admin/content/translationview' ), 'value', $locale_object.charset ),
        hash( 'label', 'Allowed charsets'|i18n( 'design/admin/content/translationview' ), 'value', $locale_object.allowed_charsets|implode( ', ' ) ),
        hash( 'label', 'Country/region name'|i18n( 'design/admin/content/translationview' ), 'value', $locale_object.country_name ),
        hash( 'label', 'Country/region comment'|i18n( 'design/admin/content/translationview' ), 'value', $locale_object.country_comment ),
        hash( 'label', 'Country/region code'|i18n( 'design/admin/content/translationview' ), 'value', $locale_object.country_code ),
        hash( 'label', 'Country/region variation'|i18n( 'design/admin/content/translationview' ), 'value', $locale_object.country_variation ),
        hash( 'label', 'Language name'|i18n( 'design/admin/content/translationview' ), 'value', $locale_object.language_name ),
        hash( 'label', 'International language name'|i18n( 'design/admin/content/translationview' ), 'value', $locale_object.intl_language_name ),
        hash( 'label', 'Language code'|i18n( 'design/admin/content/translationview' ), 'value', $locale_object.language_code ),
        hash( 'label', 'Language comment'|i18n( 'design/admin/content/translationview' ), 'value', $locale_object.language_comment ),
        hash( 'label', 'Locale code'|i18n( 'design/admin/content/translationview' ), 'value', $locale_object.locale_code ),
        hash( 'label', 'Full locale code'|i18n( 'design/admin/content/translationview' ), 'value', $locale_object.locale_full_code ),
        hash( 'label', 'HTTP locale code'|i18n( 'design/admin/content/translationview' ), 'value', $locale_object.http_locale_code ),
        hash( 'label', 'Decimal symbol'|i18n( 'design/admin/content/translationview' ), 'value', $locale_object.decimal_symbol ),
        hash( 'label', 'Thousands separator'|i18n( 'design/admin/content/translationview' ), 'value', $locale_object.thousands_separator ),
        hash( 'label', 'Decimal count'|i18n( 'design/admin/content/translationview' ), 'value', $locale_object.decimal_count ),
        hash( 'label', 'Negative symbol'|i18n( 'design/admin/content/translationview' ), 'value', $locale_object.negative_symbol ),
        hash( 'label', 'Positive symbol'|i18n( 'design/admin/content/translationview' ), 'value', $locale_object.positive_symbol ),
        hash( 'label', 'Currency decimal symbol'|i18n( 'design/admin/content/translationview' ), 'value', $locale_object.currency_decimal_symbol ),
        hash( 'label', 'Currency thousands separator'|i18n( 'design/admin/content/translationview' ), 'value', $locale_object.currency_thousands_separator ),
        hash( 'label', 'Currency decimal count'|i18n( 'design/admin/content/translationview' ), 'value', $locale_object.currency_decimal_count ),
        hash( 'label', 'Currency negative symbol'|i18n( 'design/admin/content/translationview' ), 'value', $locale_object.currency_negative_symbol ),
        hash( 'label', 'Currency positive symbol'|i18n( 'design/admin/content/translationview' ), 'value', $locale_object.currency_positive_symbol ),
        hash( 'label', 'Currency symbol'|i18n( 'design/admin/content/translationview' ), 'value', $locale_object.currency_symbol ),
        hash( 'label', 'Currency name'|i18n( 'design/admin/content/translationview' ), 'value', $locale_object.currency_name ),
        hash( 'label', 'Currency short name'|i18n( 'design/admin/content/translationview' ), 'value', $locale_object.currency_short_name ),
        hash( 'label', 'First day of week'|i18n( 'design/admin/content/translationview' ), 'value', cond( $locale_object.is_monday_first, 'Monday'|i18n( 'design/admin/content/translationview' ), 'Sunday'|i18n( 'design/admin/content/translationview' ) ) ),
        hash( 'label', 'Weekday names'|i18n( 'design/admin/content/translationview' ), 'value', $locale_object.weekday_name_list|implode( ', ' ) ),
        hash( 'label', 'Month names'|i18n( 'design/admin/content/translationview' ), 'value', $locale_object.month_name_list|implode( ', ' ) ) )}
    <dl class="exp-locale-grid">
    {foreach $locale_fields as $locale_field}
        <div><dt>{$locale_field.label|wash}</dt><dd>{if and( is_set( $locale_field.value ), $locale_field.value|ne('') )}{$locale_field.value|wash}{else}<i class="exp-muted">{'Not set'|i18n( 'design/admin/content/translationview' )}</i>{/if}</dd></div>
    {/foreach}
    </dl>
    {undef $locale_fields}
    </div>
</details>

</div></div></div>

</div>
{undef $language $locale_object $is_interface}
