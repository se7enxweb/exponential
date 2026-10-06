{* The content languages: the languages content can be written in.

   An overview first (how many, how many more fit, how many need a look, the default language for new content),
   then one card per language: how many objects and classes have a translation in it, which siteaccesses show it
   (site.ini [RegionalSettings] SiteLanguageList, the first one shows it first), whether it can be removed and, when
   few objects use it or no siteaccess lists it, what to do about that. Below: every siteaccess with its languages.

   Form field names, buttons and the action are the ones content/translations has always taken: DeleteIDArray[]
   with RemoveButton, NewButton for the add form. Everything works without javascript; the script at the end only
   asks before a removal. The same file is in design/admin and design/admin4. User guide:
   doc/guides/content-languages.md. *}
{include uri='design:content/languages_style.tpl'}

<div class="context-block content-translations exp-languages">

<div class="box-header"><div class="box-ml">
<h1 class="context-title">{'Content languages'|i18n( 'design/admin/content/translations' )}</h1>
<div class="header-mainline"></div>
</div></div>

<div class="box-bc"><div class="box-ml"><div class="box-content">

<p class="exp-intro">{'The languages content can be written in. Every object keeps one translation per language it has, and each siteaccess shows the languages in its SiteLanguageList, the first one first. The language of the administration interface itself is a different setting (Locale) and is not managed here.'|i18n( 'design/admin/content/translations' )}</p>

{foreach $language_feedback as $language_message}
<div class="exp-feedback {if $language_message.ok}is-ok{else}is-bad{/if}" role="{if $language_message.ok}status{else}alert{/if}">{$language_message.message|wash}</div>
{/foreach}

<section aria-labelledby="language-overview-title">
<h2 class="exp-sr" id="language-overview-title">{'Overview'|i18n( 'design/admin/content/translations' )}</h2>
<ul class="exp-figures">
    <li class="exp-figure"><strong>{$language_summary.languages}</strong><span>{'Content languages'|i18n( 'design/admin/content/translations' )}</span></li>
    <li class="exp-figure"><strong>{$language_summary.free}</strong><span>{'More can be added (at most %max)'|i18n( 'design/admin/content/translations',, hash( '%max', $language_summary.max ) )}</span></li>
    <li class="exp-figure{if $language_summary.attention|gt(0)} is-attention{/if}"><strong>{$language_summary.attention}</strong><span>{'Need a look'|i18n( 'design/admin/content/translations' )}</span></li>
    <li class="exp-figure"><strong>{$language_summary.removable}</strong><span>{'Unused, can be removed'|i18n( 'design/admin/content/translations' )}</span></li>
    <li class="exp-figure is-wide">
        {if $language_summary.default_locale|ne('')}
        <strong>{if $language_summary.default_name|ne('')}{$language_summary.default_name|wash}{else}{$language_summary.default_locale|wash}{/if} <code>{$language_summary.default_locale|wash}</code></strong>
        {else}
        <strong>&mdash;</strong>
        {/if}
        <span>{'Default for new content in this siteaccess (ContentObjectLocale)'|i18n( 'design/admin/content/translations' )}</span>
    </li>
</ul>
</section>

<form name="languageform" action={$module.functions.translations.uri|ezurl} method="post" id="language-form">

<section class="exp-section" aria-labelledby="language-list-title">
<div class="exp-section-head">
    <h2 class="exp-h2" id="language-list-title">{'Languages'|i18n( 'design/admin/content/translations' )}
        {if $translation_count|gt( $limit )}<span class="exp-meta">{'%from to %to of %count'|i18n( 'design/admin/content/translations',, hash( '%from', sum( $view_parameters.offset, 1 ), '%to', min( sum( $view_parameters.offset, $limit ), $translation_count ), '%count', $translation_count ) )}</span>{/if}</h2>
    <button type="submit" class="exp-btn exp-btn-primary" name="NewButton" value="{'Add language'|i18n( 'design/admin/content/translations' )}"><svg width="14" height="14" viewBox="0 0 16 16" aria-hidden="true" focusable="false"><path fill="currentColor" d="M7 2h2v5h5v2H9v5H7V9H2V7h5z"/></svg>{'Add language'|i18n( 'design/admin/content/translations' )}</button>
</div>

{if $language_rows|count|eq(0)}
<p class="exp-empty">{'There are no content languages yet. Add one to be able to create content.'|i18n( 'design/admin/content/translations' )}</p>
{else}
<ul class="exp-langs" id="language-list">
{foreach $language_rows as $language}
    {def $language_card = concat( 'language-', $language.id )}
<li class="exp-lang{if $language.attention} is-attention{/if}" id="{$language_card}" aria-labelledby="{$language_card}-title">
    <div class="exp-lang-head">
        <div class="exp-lang-title">
            <img src="{$language.locale|flag_icon}" width="18" height="12" alt="" />
            <h3 id="{$language_card}-title"><a href={concat( '/content/translations/', $language.id )|ezurl}>{$language.name|wash}</a></h3>
            <code>{$language.locale|wash}</code>
            <ul class="exp-badges">
                {if $language.is_default}<li class="exp-badge is-info">{'Default for new content'|i18n( 'design/admin/content/translations' )}</li>{/if}
                {if $language.main_sites|count|gt(0)}<li class="exp-badge is-info">{'Shown first by %count'|i18n( 'design/admin/content/translations',, hash( '%count', $language.main_sites|count ) )}</li>{/if}
                {if $language.unlisted}<li class="exp-badge is-warn">{'In no SiteLanguageList'|i18n( 'design/admin/content/translations' )}</li>{/if}
                {if $language.few}<li class="exp-badge is-warn">{'Only %count objects'|i18n( 'design/admin/content/translations',, hash( '%count', $language.objects ) )}</li>{/if}
                {if $language.removable}<li class="exp-badge is-ok">{'Unused'|i18n( 'design/admin/content/translations' )}</li>{else}<li class="exp-badge">{'In use'|i18n( 'design/admin/content/translations' )}</li>{/if}
            </ul>
        </div>
        {* Selected for the Remove selected button below. A language objects or classes still have cannot be removed
           (the kernel refuses it too), so its box is off and says why. *}
        {if $language.removable}
        <label class="exp-select"><input type="checkbox" name="DeleteIDArray[]" value="{$language.id}" data-name="{$language.name|wash} ({$language.locale|wash})" data-sites="{$language.sites|implode( ', ' )|wash}" /> {'Select for removal'|i18n( 'design/admin/content/translations' )}</label>
        {else}
        <label class="exp-select is-disabled"><input type="checkbox" disabled="disabled" aria-describedby="{$language_card}-why" /> <span class="exp-muted">{'Select for removal'|i18n( 'design/admin/content/translations' )}</span></label>
        {/if}
    </div>

    <dl class="exp-facts">
        <div>
            <dt>{'Objects'|i18n( 'design/admin/content/translations' )}</dt>
            <dd><strong>{$language.objects}</strong>{if $language.objects|gt(0)} <span class="exp-meta">{'%main in it as main language, %only only in it'|i18n( 'design/admin/content/translations',, hash( '%main', $language.objects_main, '%only', $language.objects_only ) )}</span>{/if}</dd>
        </div>
        <div>
            <dt>{'Classes'|i18n( 'design/admin/content/translations' )}</dt>
            <dd><strong>{$language.classes}</strong> <span class="exp-meta">{'with a name in it'|i18n( 'design/admin/content/translations' )}</span></dd>
        </div>
        <div>
            <dt>{'Language ID'|i18n( 'design/admin/content/translations' )}</dt>
            <dd>{$language.id} <span class="exp-meta">{'bit %bit of the language mask'|i18n( 'design/admin/content/translations',, hash( '%bit', $language.bit ) )}</span></dd>
        </div>
        <div class="exp-fact-wide">
            <dt>{'Shown by siteaccesses'|i18n( 'design/admin/content/translations' )}</dt>
            <dd>{if $language.sites|count|gt(0)}
                <ul class="exp-chips">{foreach $language.sites as $language_site}<li class="exp-chip{if $language.main_sites|contains( $language_site )} is-main{/if}">{$language_site|wash}</li>{/foreach}</ul>
                {if $language.main_sites|count|gt(0)}<span class="exp-meta">{'Highlighted: the siteaccesses that list it first, so show it first.'|i18n( 'design/admin/content/translations' )}</span>{/if}
                {else}<span class="exp-muted">{'None'|i18n( 'design/admin/content/translations' )}</span>{/if}</dd>
        </div>
    </dl>

    {* What to do about a language few objects use or no siteaccess shows. *}
    {if $language.hint|eq( 'unlisted_content' )}
    <p class="exp-hint"><strong>{'Not shown by any siteaccess.'|i18n( 'design/admin/content/translations' )}</strong> {'%count objects have a translation in %locale, but no siteaccess lists it, so visitors never see these translations. Either add %locale to the SiteLanguageList of the siteaccess that should show it, or move the content into a language your sites use and then remove this one.'|i18n( 'design/admin/content/translations',, hash( '%count', $language.objects, '%locale', $language.locale ) )|wash} <a href={concat( '/content/translations/', $language.id, '#language-move' )|ezurl}>{'How to move it'|i18n( 'design/admin/content/translations' )}</a></p>
    {elseif $language.hint|eq( 'unlisted_classes' )}
    <p class="exp-hint"><strong>{'Not shown by any siteaccess.'|i18n( 'design/admin/content/translations' )}</strong> {'No object is in %locale, but %count classes have a name in it, so it cannot be removed yet. Remove the %locale translation from these classes first; then the language can be removed.'|i18n( 'design/admin/content/translations',, hash( '%count', $language.classes, '%locale', $language.locale ) )|wash} <a href={concat( '/content/translations/', $language.id, '#language-classes' )|ezurl}>{'See the classes'|i18n( 'design/admin/content/translations' )}</a></p>
    {elseif $language.hint|eq( 'unlisted_empty' )}
    <p class="exp-hint is-quiet"><strong>{'Unused.'|i18n( 'design/admin/content/translations' )}</strong> {'Nothing is written in %locale and no siteaccess lists it. It can be removed safely, or added to a SiteLanguageList to start using it.'|i18n( 'design/admin/content/translations',, hash( '%locale', $language.locale ) )|wash}</p>
    {elseif $language.hint|eq( 'few' )}
    <p class="exp-hint"><strong>{'Only %count objects.'|i18n( 'design/admin/content/translations',, hash( '%count', $language.objects ) )}</strong> {'Few objects have a translation in %locale. If they were created in it by mistake, move them into the right language; the page of the language lists them.'|i18n( 'design/admin/content/translations',, hash( '%locale', $language.locale ) )|wash} <a href={concat( '/content/translations/', $language.id, '#language-objects' )|ezurl}>{'See the objects'|i18n( 'design/admin/content/translations' )}</a></p>
    {elseif $language.hint|eq( 'empty' )}
    <p class="exp-hint is-quiet"><strong>{'Not used yet.'|i18n( 'design/admin/content/translations' )}</strong> {'No object or class has a translation in %locale yet. Translate content into it from the editor.'|i18n( 'design/admin/content/translations',, hash( '%locale', $language.locale ) )|wash}</p>
    {/if}

    <p class="exp-why" id="{$language_card}-why">
    {if $language.removable}
        {if $language.sites|count|gt(0)}{'Can be removed, but %sites still list it: take it out of their SiteLanguageList as well.'|i18n( 'design/admin/content/translations',, hash( '%sites', $language.sites|implode( ', ' ) ) )|wash}{else}{'Can be removed: no object and no class has a translation in it.'|i18n( 'design/admin/content/translations' )}{/if}
    {else}
        {'Cannot be removed while %objects objects and %classes classes have a translation in it.'|i18n( 'design/admin/content/translations',, hash( '%objects', $language.objects, '%classes', $language.classes ) )}
    {/if}
    </p>
</li>
    {undef $language_card}
{/foreach}
</ul>
{/if}

{* Paged; the size is admininterface.ini [PaginationSettings]. *}
{if $translation_count|gt( $limit )}
<div class="context-toolbar exp-pager">
{include name=TranslationNavigator
         uri='design:navigator/google.tpl'
         page_uri='/content/translations'
         item_count=$translation_count
         view_parameters=$view_parameters
         item_limit=$limit}
</div>
{/if}

<div class="exp-bar controlbar">
    <p class="exp-meta">{'Removing a language takes away the possibility to write in it. It never deletes content: a language that any object or class has is refused.'|i18n( 'design/admin/content/translations' )}</p>
    <div class="exp-actions">
        <button type="submit" class="exp-btn exp-btn-danger" name="RemoveButton" id="language-remove" value="{'Remove selected'|i18n( 'design/admin/content/translations' )}" title="{'Remove selected languages.'|i18n( 'design/admin/content/translations' )}"{if $language_summary.removable|eq(0)} disabled="disabled"{/if}>{'Remove selected'|i18n( 'design/admin/content/translations' )}</button>
        <button type="submit" class="exp-btn" name="NewButton" value="{'Add language'|i18n( 'design/admin/content/translations' )}" title="{'Add a new language. The new language can then be used when translating content.'|i18n( 'design/admin/content/translations' )}">{'Add language'|i18n( 'design/admin/content/translations' )}</button>
    </div>
</div>
</section>

</form>

{* Every siteaccess with its languages, as it reads them itself. *}
<details class="exp-fold" id="language-sites" open="open">
    <summary>
        <h2 class="exp-h2" id="language-sites-title">{'Siteaccesses and their languages'|i18n( 'design/admin/content/translations' )}</h2>
        <span class="exp-meta">{'site.ini [RegionalSettings] SiteLanguageList of each siteaccess, first language first'|i18n( 'design/admin/content/translations' )}</span>
    </summary>
    <div class="exp-fold-body">
        <p class="exp-meta">{'A siteaccess shows an object in the first of its languages the object has. With ShowUntranslatedObjects disabled, objects in none of them are hidden, unless they are always available.'|i18n( 'design/admin/content/translations' )} {'Only the siteaccesses in site.ini [SiteAccessSettings] AvailableSiteAccessList are counted; a settings folder that is not listed there serves nothing.'|i18n( 'design/admin/content/translations' )}</p>
        {if $site_languages|count|gt(0)}
        <div class="exp-table-wrap" role="region" aria-labelledby="language-sites-title" tabindex="0">
        <table class="exp-table">
        <thead>
        <tr>
            <th scope="col">{'Siteaccess'|i18n( 'design/admin/content/translations' )}</th>
            <th scope="col">{'Languages, in order'|i18n( 'design/admin/content/translations' )}</th>
        </tr>
        </thead>
        <tbody>
        {foreach $site_languages as $site_row}
        <tr>
            <td><code>{$site_row.siteaccess|wash}</code></td>
            <td>{if $site_row.languages|count|gt(0)}
                <ul class="exp-chips">{foreach $site_row.languages as $site_language}<li class="exp-chip{if $site_language.known|not} is-unknown{elseif $site_language.main} is-main{/if}">{$site_language.locale|wash}{if $site_language.known|not} <span>&middot; {'not a content language'|i18n( 'design/admin/content/translations' )}</span>{/if}</li>{/foreach}</ul>
                {else}<span class="exp-muted">{'No SiteLanguageList: the siteaccess uses the default of site.ini'|i18n( 'design/admin/content/translations' )}</span>{/if}</td>
        </tr>
        {/foreach}
        </tbody>
        </table>
        </div>
        {else}
        <p class="exp-empty">{'No siteaccess is listed in site.ini [SiteAccessSettings] AvailableSiteAccessList.'|i18n( 'design/admin/content/translations' )}</p>
        {/if}
    </div>
</details>

<details class="exp-fold" id="language-about">
    <summary>
        <h2 class="exp-h2">{'Content languages and interface languages'|i18n( 'design/admin/content/translations' )}</h2>
        <span class="exp-meta">{'What this page manages, and what it does not'|i18n( 'design/admin/content/translations' )}</span>
    </summary>
    <div class="exp-fold-body">
        <p>{'A content language is a language your articles, folders and other objects can be written in. Each one has an ID that is a single bit, so an object records all of its translations in one number, its language mask; the lowest bit marks an object as always available, shown even where none of its languages is listed.'|i18n( 'design/admin/content/translations' )}</p>
        <p>{'The texts of the administration interface and the templates (buttons, menus) come from translation files (.ts) and follow the Locale setting of a siteaccess instead. A site can show German content with an English interface and the other way round.'|i18n( 'design/admin/content/translations' )}</p>
        <p>{'Adding a language here does not translate anything and does not change any siteaccess: translate content in the editor, and list the language in SiteLanguageList where it should be shown. The user guide is doc/guides/content-languages.md.'|i18n( 'design/admin/content/translations' )}</p>
    </div>
</details>

</div></div></div>

</div>

<script type="text/javascript">
var expLanguageText = {ldelim}
    confirm: '{'Remove these languages?'|i18n( 'design/admin/content/translations' )|wash( javascript )}',
    listed: '{'Still listed by siteaccesses: %sites. Take it out of their SiteLanguageList as well.'|i18n( 'design/admin/content/translations' )|wash( javascript )}',
    none: '{'Select at least one unused language to remove.'|i18n( 'design/admin/content/translations' )|wash( javascript )}'
{rdelim};
{literal}
(function () {
    var form = document.getElementById( 'language-form' );
    if ( !form ) return;
    var removing = false;
    form.addEventListener( 'click', function ( e ) {
        removing = !!( e.target.closest && e.target.closest( '#language-remove' ) );
    } );
    // Before a removal, say which languages go and which siteaccesses still list them.
    form.addEventListener( 'submit', function ( e ) {
        if ( !removing ) return;
        var picked = form.querySelectorAll( 'input[name="DeleteIDArray[]"]:checked' ), lines = [], i;
        if ( !picked.length ) { e.preventDefault(); window.alert( expLanguageText.none ); return; }
        for ( i = 0; i < picked.length; i++ ) {
            var line = '- ' + picked[i].getAttribute( 'data-name' );
            var sites = picked[i].getAttribute( 'data-sites' );
            if ( sites ) line += '\n  ' + expLanguageText.listed.split( '%sites' ).join( sites );
            lines.push( line );
        }
        if ( !window.confirm( expLanguageText.confirm + '\n\n' + lines.join( '\n' ) ) ) e.preventDefault();
    } );
})();
{/literal}
</script>
