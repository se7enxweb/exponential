{* The count and the server-side selection of a result list of the content checks (shared: unreferenced, storage,
   missingfiles), to put in the form's toolbar next to the actions. "Select all on this page" reloads the page with
   /(selectall)/1 (every row box ticked, without JavaScript); "Select none" without it. contentcheck_select.js keeps the
   count up to date while boxes are ticked.

     {include uri='design:contentcheck/select_count.tpl' select_all=$select_all total=<row boxes on the page>
              page_url=concat( $page_uri, '/(offset)/', $offset, $page_uri_suffix )}

   Variables: select_all (bool), total (int), page_url (the page's address with its view parameters).
   The same file is in design/admin and design/admin4. *}
    <p class="a4-meta" data-a4-select-count="1" data-a4-count-text="{'%selected of %total selected'|i18n( 'kernel/contentcheck' )|wash}" aria-live="polite">{'%selected of %total selected'|i18n( 'kernel/contentcheck',, hash( '%selected', cond( $select_all, $total, 0 ), '%total', $total ) )}</p>
    <p class="a4-row">
        <a class="a4-btn a4-btn-small a4-btn-ghost" href={concat( $page_url, '/(selectall)/1' )|ezurl}>{'Select all on this page'|i18n( 'kernel/contentcheck' )}</a>
        <a class="a4-btn a4-btn-small a4-btn-ghost" href={$page_url|ezurl}>{'Select none'|i18n( 'kernel/contentcheck' )}</a>
    </p>
{ezscript_require( 'contentcheck_select.js' )}
