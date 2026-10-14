{* Admin block (explayouts). Markup copied from admin4 page_topmenu.tpl lines, keep in step with it. *}
    {* admin4: light or dark mode. The choice is kept in this browser (localStorage "exp-admin4-theme"); the
       script at the top of page_head_style.tpl applies it before the page is drawn and wires this button. *}
    <div id="header-theme" class="header-theme">
        <button type="button" id="a4-theme-toggle" class="a4-theme-toggle" aria-pressed="false"
                title="{'Switch between light and dark mode'|i18n( 'design/admin/pagelayout' )|wash}"
                data-label-light="{'Light mode'|i18n( 'design/admin/pagelayout' )|wash}"
                data-label-dark="{'Dark mode'|i18n( 'design/admin/pagelayout' )|wash}">
            <span class="a4-icon a4-bi-sun a4-icon-sun" aria-hidden="true"></span>
            <span class="a4-icon a4-bi-moon a4-icon-moon" aria-hidden="true"></span>
            <span class="a4-theme-label">{'Light mode'|i18n( 'design/admin/pagelayout' )}</span>
        </button>
    </div>
