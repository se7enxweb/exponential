/*
 * admin4 setup/systemupgrade: the filter of the findings and the busy state of the check.
 * The texts are data-text-* attributes of #systemupgrade-script. A file of its own, so the page runs under a Content
 * Security Policy without 'unsafe-inline' (doc/en/us/features/6.0/content-security-policy.md).
 */
var expUpgradeText = ( function ( el ) {
    return {
        shown: el.getAttribute( 'data-text-shown' ),
        allShown: el.getAttribute( 'data-text-all-shown' ),
        busy: el.getAttribute( 'data-text-busy' )
    };
} )( document.getElementById( 'systemupgrade-script' ) );
(function () {
    var root = document.querySelector( '.a4-setup-upgrade' );
    if ( !root ) return;
    function each( selector, fn ) {
        var nodes = root.querySelectorAll( selector ), i;
        for ( i = 0; i < nodes.length; i++ ) fn( nodes[i], i );
    }
    function tr( name, values ) {
        var s = expUpgradeText[name], key;
        for ( key in values || {} ) s = s.split( '%' + key ).join( values[key] );
        return s;
    }
    each( '.a4-setup-js-only', function ( el ) { el.hidden = false; } );

    // A check takes a few seconds: say so, and keep it from being pressed twice. The downloads stay on this page.
    var form = document.getElementById( 'upgrade-form' );
    if ( form ) {
        form.addEventListener( 'submit', function ( event ) {
            var button = event.submitter;
            if ( !button || !button.classList.contains( 'a4-setup-run' ) ) return;
            var busy = document.getElementById( 'upgrade-busy' );
            if ( busy ) busy.hidden = false;
            button.classList.add( 'is-busy' );
            button.setAttribute( 'aria-busy', 'true' );
            button.textContent = expUpgradeText.busy;
            // a disabled button is not submitted: carry its name in a hidden field instead
            var carry = document.createElement( 'input' );
            carry.type = 'hidden'; carry.name = button.name; carry.value = button.value;
            form.appendChild( carry );
            each( '.a4-setup-run', function ( b ) { b.disabled = true; } );
        } );
    }

    var findings = document.getElementById( 'upgrade-findings' );
    if ( !findings ) return;
    var searchEl = document.getElementById( 'upgrade-search' );
    var areaEl = document.getElementById( 'upgrade-area' );
    var countEl = document.getElementById( 'upgrade-filter-count' );
    var noMatchEl = document.getElementById( 'upgrade-no-match' );
    var total = findings.querySelectorAll( 'tr[data-file]' ).length;

    function apply() {
        var term = searchEl ? searchEl.value.trim().toLowerCase() : '';
        var area = areaEl ? areaEl.value : '';
        var picked = root.querySelector( 'input[name="UpgradeFilterState"]:checked' );
        var state = picked ? picked.value : '';
        var shown = 0;
        each( '#upgrade-findings details[data-group]', function ( group ) {
            var groupShown = 0;
            var rows = group.querySelectorAll( 'tr[data-file]' ), i;
            var stateOk = state === '' || group.getAttribute( 'data-group' ) === state;
            for ( i = 0; i < rows.length; i++ ) {
                var row = rows[i];
                var ok = stateOk && ( area === '' || row.getAttribute( 'data-area' ) === area ) &&
                         ( term === '' || row.getAttribute( 'data-search' ).indexOf( term ) !== -1 );
                row.hidden = !ok;
                if ( ok ) groupShown++;
            }
            group.hidden = !stateOk || ( rows.length > 0 && groupShown === 0 && ( term !== '' || area !== '' ) );
            if ( ( term !== '' || area !== '' || state !== '' ) && groupShown > 0 ) group.open = true;
            shown += groupShown;
        } );
        var filtered = term !== '' || area !== '' || state !== '';
        if ( countEl ) countEl.textContent = filtered ? tr( 'shown', { shown: shown, count: total } ) : tr( 'allShown', { count: total } );
        if ( noMatchEl ) noMatchEl.hidden = !( filtered && shown === 0 );
    }
    if ( searchEl ) searchEl.addEventListener( 'input', apply );
    if ( areaEl ) areaEl.addEventListener( 'change', apply );
    each( 'input[name="UpgradeFilterState"]', function ( r ) { r.addEventListener( 'change', apply ); } );
    apply();
})();
