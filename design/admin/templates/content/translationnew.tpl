{* Adding a content language: a language from share/locale, found by typing part of its name, its code or its
   country, or a custom one with a name and a locale code of its own.

   Posts to content/translations as it always has: LocaleID (-1 for a custom language), TranslationName and
   TranslationLocale for a custom one, StoreButton (ChangeButton when editing) and CancelButton. Without javascript
   the list is a plain list box and both parts of the form are open; a language chosen in the list wins over the
   custom fields. The same file is in design/admin and design/admin4. User guide: doc/guides/content-languages.md. *}
{include uri='design:content/languages_style.tpl'}
{def $chosen = cond( is_set( $selected_locale ), $selected_locale, '' )
     $choice_count = 0}
{if $chosen|eq('')}{set $chosen = '-1'}{/if}

<form name="languageform" action={concat( 'content/translations' )|ezurl} method="post" id="language-new-form">

<div class="context-block content-translations content-translations-new exp-languages">

<div class="box-header"><div class="box-ml">
<h1 class="context-title">{'Add a content language'|i18n( 'design/admin/content/translationnew' )}</h1>
<div class="header-mainline"></div>
</div></div>

<div class="box-bc"><div class="box-ml"><div class="box-content">

<p class="exp-intro">{'Adds a language content can be translated into. It does not translate anything and changes no siteaccess: afterwards, translate content into it in the editor and add it to the SiteLanguageList of the siteaccesses that should show it.'|i18n( 'design/admin/content/translationnew' )}
{if is_set( $language_slots )} {'%used of at most %max languages are in use.'|i18n( 'design/admin/content/translationnew',, hash( '%used', $language_slots.used, '%max', $language_slots.max ) )}{/if}</p>

{if and( is_set( $error ), $error|ne('') )}
<div class="exp-feedback is-bad" role="alert" id="language-new-error">{$error|wash}</div>
{/if}

<div class="exp-form">

<fieldset class="exp-panel" id="language-pick">
    <legend>{'Language and country'|i18n( 'design/admin/content/translationnew' )}</legend>
    <p class="exp-meta">{'The locales in share/locale. Languages already added are listed but cannot be chosen again.'|i18n( 'design/admin/content/translationnew' )}</p>
    <div class="exp-field exp-js-only" hidden>
        <label for="locale-search">{'Find a language'|i18n( 'design/admin/content/translationnew' )}</label>
        <input type="search" id="locale-search" autocomplete="off" spellcheck="false" aria-controls="localeSelector" aria-describedby="locale-search-count" placeholder="{'Name, code or country, e.g. German, ger, Austria'|i18n( 'design/admin/content/translationnew' )}" />
        <span class="exp-meta" id="locale-search-count" aria-live="polite"></span>
    </div>
    <div class="exp-field">
        <label for="localeSelector">{'Translation'|i18n( 'design/admin/content/translationnew' )}</label>
        <select id="localeSelector" name="LocaleID" size="12" aria-describedby="locale-choice-hint">
            <option value="-1"{if $chosen|eq('-1')} selected="selected"{/if}>{'Custom: a name and a locale code of its own (below)'|i18n( 'design/admin/content/translationnew' )}</option>
            {if is_set( $locale_choices )}
            {foreach $locale_choices as $choice}
            <option value="{$choice.code|wash}" data-search="{$choice.search|wash}"{if $choice.exists} disabled="disabled"{elseif $chosen|eq( $choice.code )} selected="selected"{/if}>{$choice.label|wash}{if $choice.native_name|ne('')}{if $choice.native_name|ne( $choice.label )} &middot; {$choice.native_name|wash}{/if}{/if}{if $choice.country|ne('')} &middot; {$choice.country|wash}{/if} &middot; {$choice.code|wash}{if $choice.exists} &middot; {'already added'|i18n( 'design/admin/content/translationnew' )}{/if}</option>
            {/foreach}
            {else}
            {* Reached from an older view that does not hand the choices over. *}
            {foreach fetch( content, locale_list, hash( with_variations, false() ) ) as $choice}
            <option value="{$choice.locale_full_code|wash}">{$choice.intl_language_name|wash}{if $choice.country_variation} [{$choice.language_comment|wash}]{/if} &middot; {$choice.locale_full_code|wash}</option>
            {/foreach}
            {/if}
        </select>
        <span class="exp-meta" id="locale-choice-hint">{'The name of the language is taken from the locale. A language chosen here wins over the custom fields below.'|i18n( 'design/admin/content/translationnew' )}</span>
    </div>
</fieldset>

<fieldset class="exp-panel" id="language-custom">
    <legend>{'Custom language'|i18n( 'design/admin/content/translationnew' )}</legend>
    <p class="exp-meta">{'For a language that is not in the list. Choose "Custom" above, then give it a name and a locale code; a share/locale file with that code must exist.'|i18n( 'design/admin/content/translationnew' )}</p>
    <div class="exp-field-row">
        <div class="exp-field">
            <label for="field1">{'Name of custom translation'|i18n( 'design/admin/content/translationnew' )}</label>
            <input id="field1" type="text" name="TranslationName" value="{if is_set( $custom_name )}{$custom_name|wash}{/if}" size="20" />
        </div>
        <div class="exp-field">
            <label for="field2">{'Locale for custom translation'|i18n( 'design/admin/content/translationnew' )}</label>
            <input id="field2" type="text" name="TranslationLocale" value="{if is_set( $custom_locale )}{$custom_locale|wash}{/if}" size="8" aria-describedby="field2-hint" autocomplete="off" spellcheck="false" />
            <span class="exp-meta" id="field2-hint">{'Three letters for the language, a dash, two for the country: ger-CH'|i18n( 'design/admin/content/translationnew' )}</span>
        </div>
    </div>
</fieldset>

<div class="exp-panel">
    <h2 class="exp-h2">{'After adding it'|i18n( 'design/admin/content/translationnew' )}</h2>
    <ol class="exp-steps">
        <li>{'Translate content: open an object, choose Edit and pick the new language; the editor offers to start from an existing translation.'|i18n( 'design/admin/content/translationnew' )}</li>
        <li>{'Show it: add SiteLanguageList[]=<code> to the site.ini of each siteaccess that should show it; first in the list means shown first.'|i18n( 'design/admin/content/translationnew' )|wash}</li>
        <li>{'Optionally give it a siteaccess of its own with Locale set to it, so the templates speak it too.'|i18n( 'design/admin/content/translationnew' )}</li>
    </ol>
</div>

</div>

<div class="exp-bar controlbar">
    <span class="exp-meta">{'Nothing is translated or shown until you do the steps above.'|i18n( 'design/admin/content/translationnew' )}</span>
    <div class="exp-actions">
    {if $is_edit}
        <button type="submit" class="exp-btn exp-btn-primary" name="ChangeButton" value="{'OK'|i18n( 'design/admin/content/translationnew' )}">{'OK'|i18n( 'design/admin/content/translationnew' )}</button>
    {else}
        <button type="submit" class="exp-btn exp-btn-primary" name="StoreButton" value="{'OK'|i18n( 'design/admin/content/translationnew' )}">{'Add language'|i18n( 'design/admin/content/translationnew' )}</button>
    {/if}
        <button type="submit" class="exp-btn" name="CancelButton" value="{'Cancel'|i18n( 'design/admin/content/translationnew' )}">{'Cancel'|i18n( 'design/admin/content/translationnew' )}</button>
    </div>
</div>

</div></div></div>

</div>

</form>
{undef $chosen $choice_count}

<script type="text/javascript">
var expLocaleText = {ldelim}
    shown: '{'%shown of %count languages match'|i18n( 'design/admin/content/translationnew' )|wash( javascript )}',
    all: '{'%count languages'|i18n( 'design/admin/content/translationnew' )|wash( javascript )}'
{rdelim};
{literal}
(function () {
    var select = document.getElementById( 'localeSelector' );
    var search = document.getElementById( 'locale-search' );
    var count = document.getElementById( 'locale-search-count' );
    var custom = document.getElementById( 'language-custom' );
    if ( !select ) return;
    var nodes = document.querySelectorAll( '#language-new-form .exp-js-only' ), i;
    for ( i = 0; i < nodes.length; i++ ) nodes[i].hidden = false;

    // The custom fields only count with "Custom" chosen; off otherwise, as the old form did.
    function syncCustom() {
        if ( custom ) custom.disabled = select.value !== '-1' && select.value !== '';
    }
    select.addEventListener( 'change', syncCustom );
    syncCustom();

    // Typing narrows the list to the languages whose name, code or country hold every word typed.
    function filter() {
        var words = search.value.toLowerCase().split( /\s+/ ).filter( Boolean );
        var shown = 0, total = 0, first = null;
        for ( var j = 0; j < select.options.length; j++ ) {
            var option = select.options[j];
            if ( option.value === '-1' ) continue;
            total++;
            var hay = option.getAttribute( 'data-search' ) || option.text.toLowerCase();
            var ok = true;
            for ( var k = 0; ok && k < words.length; k++ ) ok = hay.indexOf( words[k] ) !== -1;
            option.hidden = !ok;
            if ( ok ) { shown++; if ( !first && !option.disabled ) first = option; }
        }
        count.textContent = words.length
            ? expLocaleText.shown.split( '%shown' ).join( shown ).split( '%count' ).join( total )
            : expLocaleText.all.split( '%count' ).join( total );
        return first;
    }
    if ( search ) {
        search.addEventListener( 'input', filter );
        // Enter picks the first match instead of sending the form; arrow down moves into the list.
        search.addEventListener( 'keydown', function ( e ) {
            if ( e.key === 'Enter' ) {
                e.preventDefault();
                var first = filter();
                if ( first ) { select.value = first.value; syncCustom(); select.focus(); }
            } else if ( e.key === 'ArrowDown' ) {
                e.preventDefault();
                select.focus();
            }
        } );
        filter();
    }
})();
{/literal}
</script>
