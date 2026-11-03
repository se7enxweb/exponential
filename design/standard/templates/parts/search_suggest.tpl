{* The suggestions under a header search field (page_search.tpl): the list the field controls as a combobox, filled by
   javascript/page/search_suggest.js from content/suggest, and a live region that says how many there are. The texts
   of the script travel here as data attributes; the titles of the groups come with the answer.
   Parameter: suggest_id, the id the field's aria-controls names. One file for every admin design, in design/standard
   (admin4 needs no other admin design). *}
<div class="a4-suggest" id="{$suggest_id|wash}" hidden="hidden"
     data-text-all="{'View all results'|i18n( 'design/admin/search/suggest' )|wash}"
     data-text-count="{'%count results'|i18n( 'design/admin/search/suggest' )|wash}"
     data-text-count-one="{'One result'|i18n( 'design/admin/search/suggest' )|wash}"
     data-text-found="{'%count suggestions'|i18n( 'design/admin/search/suggest' )|wash}"
     data-text-none="{'No suggestions'|i18n( 'design/admin/search/suggest' )|wash}"
     data-text-failed="{'The suggestions could not be loaded.'|i18n( 'design/admin/search/suggest' )|wash}">
    <ul class="a4-suggest-list" id="{$suggest_id|wash}-list" role="listbox" aria-label="{'Suggestions'|i18n( 'design/admin/search/suggest' )|wash}"></ul>
</div>
<p class="a4-suggest-status a4-sr-only" id="{$suggest_id|wash}-status" role="status" aria-live="polite"></p>
