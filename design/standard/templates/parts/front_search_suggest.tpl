{* The suggestions under a site's search field, for every site design: put this include inside the design's search
   form (method="post", action content/search, a field named SearchText), after the field. While the visitor types,
   javascript/page/search_suggest.js makes the field an ARIA combobox and fills the list from content/sitesuggest:
   content only, as far as the visitor may read it (expSearchSuggest::siteSuggest()). Up/Down, Enter, Escape, and
   Ctrl+/ or Cmd+/ to reach the field. The look is stylesheets/front_search.css, its --exps-* tokens with dark values;
   a design restyles it by setting those tokens. Nothing at all when site.ini [SiteSearchSuggestSettings] Enabled=false.
   Parameter: suggest_id (optional), when one page has more than one search form.
   Guide: doc/eng-us/help/searching.md ("Suggestions on the site"). *}
{if ezini( 'SiteSearchSuggestSettings', 'Enabled' )|eq( 'true' )}
{def $front_suggest_id = first_set( $suggest_id, 'site-search-suggest' )}
<link rel="stylesheet" href={'stylesheets/front_search.css'|ezdesign} />
<div class="exps-suggest" id="{$front_suggest_id|wash}" hidden="hidden" data-suggest-box="" data-suggest-prefix="exps-suggest"
     data-suggest-url={'content/sitesuggest'|ezurl} data-suggest-min="{ezini( 'SiteSearchSuggestSettings', 'MinLength' )|int}"
     data-text-all="{'View all results'|i18n( 'design/admin/search/suggest' )|wash}"
     data-text-count="{'%count results'|i18n( 'design/admin/search/suggest' )|wash}"
     data-text-count-one="{'One result'|i18n( 'design/admin/search/suggest' )|wash}"
     data-text-found="{'%count suggestions'|i18n( 'design/admin/search/suggest' )|wash}"
     data-text-none="{'No suggestions'|i18n( 'design/admin/search/suggest' )|wash}"
     data-text-failed="{'The suggestions could not be loaded.'|i18n( 'design/admin/search/suggest' )|wash}">
    <ul class="exps-suggest-list" id="{$front_suggest_id|wash}-list" role="listbox" aria-label="{'Suggestions'|i18n( 'design/admin/search/suggest' )|wash}"></ul>
</div>
<p class="exps-suggest-status exps-sr-only" id="{$front_suggest_id|wash}-status" role="status" aria-live="polite"></p>
<script src={'javascript/page/search_suggest.js'|ezdesign} defer="defer"></script>
{undef $front_suggest_id}
{/if}
