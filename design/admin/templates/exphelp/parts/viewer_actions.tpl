{* Print and download of a help page (exphelp/viewer.tpl, doc/en/us/features/6.0/help-system.md "Print and PDF").
   "Print this page" is a button exphelp.js shows and handles (data-exphelp-print: window.print()); without
   JavaScript it stays hidden and the browser's own print command does the same. "Download as PDF" is a plain
   link to exphelp/pdf/<page id>, which works without JavaScript. On paper the page's address is printed under the
   facts (a4-help-print-url, hidden on screen); help.css leaves the buttons out.
   Variables: actions_page (document), actions_pdf (bool: offer the PDF).
   The same file is in design/admin and design/admin4. *}
<div class="a4-help-actions" role="group" aria-label="{'Print or download this page'|i18n( 'kernel/exphelp' )|wash}">
    <button type="button" class="a4-btn a4-btn-small a4-btn-ghost" data-exphelp-print hidden><span class="a4-icon a4-bi-printer" aria-hidden="true"></span> {'Print this page'|i18n( 'kernel/exphelp' )}</button>
    {if $actions_pdf}
    <a class="a4-btn a4-btn-small a4-btn-ghost" href={concat( 'exphelp/pdf/', $actions_page.id )|ezurl} type="application/pdf" download data-exphelp-pdf><span class="a4-icon a4-bi-file-earmark-pdf" aria-hidden="true"></span> {'Download as PDF'|i18n( 'kernel/exphelp' )}</a>
    {/if}
</div>
<p class="a4-help-print-url">{'Address:'|i18n( 'kernel/exphelp' )} <span dir="ltr">{concat( 'exphelp/viewer/', $actions_page.id )|ezurl( 'no', 'full' )}</span></p>
