    {* admin4: light or dark mode. The choice is kept in this browser (localStorage "exp-admin4-theme"); the
       script at the top of page_head_style.tpl applies it before the page is drawn and wires this button. *}
    <div id="header-theme" class="header-theme">
        <button type="button" id="a4-theme-toggle" class="a4-theme-toggle" aria-pressed="false"
                title="{'Switch between light and dark mode'|i18n( 'design/admin/pagelayout' )|wash}"
                data-label-light="{'Light mode'|i18n( 'design/admin/pagelayout' )|wash}"
                data-label-dark="{'Dark mode'|i18n( 'design/admin/pagelayout' )|wash}">
            <svg class="a4-icon-sun" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="18" height="18" aria-hidden="true"><circle cx="12" cy="12" r="4.5"/><path d="M12 1.5v2.5M12 20v2.5M1.5 12h2.5M20 12h2.5M4.6 4.6l1.8 1.8M17.6 17.6l1.8 1.8M4.6 19.4l1.8-1.8M17.6 6.4l1.8-1.8" stroke-width="2" stroke-linecap="round" fill="none"/></svg>
            <svg class="a4-icon-moon" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="18" height="18" aria-hidden="true"><path d="M20.5 14.6A8.5 8.5 0 0 1 9.4 3.5a8.5 8.5 0 1 0 11.1 11.1z"/></svg>
            <span class="a4-theme-label">{'Light mode'|i18n( 'design/admin/pagelayout' )}</span>
        </button>
    </div>
