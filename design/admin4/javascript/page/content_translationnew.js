/*
 * The new translation form (content/translationnew.tpl): typing narrows the list of languages, Enter picks the
 * first match, and the custom fields count only with Custom chosen. The texts of the count are data-text-* attributes
 * of #locale-search-count.
 * A file of its own, so the page runs under a Content Security Policy without 'unsafe-inline'
 * (doc/features/6.0/content-security-policy.md).
 */
(function () {
    var select = document.getElementById( 'localeSelector' );
    var search = document.getElementById( 'locale-search' );
    var count = document.getElementById( 'locale-search-count' );
    var custom = document.getElementById( 'language-custom' );
    if ( !select ) return;
    var expLocaleText = {
        shown: count ? count.getAttribute( 'data-text-shown' ) : '',
        all: count ? count.getAttribute( 'data-text-all' ) : ''
    };
    var nodes = document.querySelectorAll( '#language-new-form .a4-content-languages-js-only' ), i;
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
