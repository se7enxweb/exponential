/*
 * admin4 workflow/processlist: the filter, the selection and the cancel buttons of the process list.
 * A file of its own, so the page runs under a Content Security Policy without 'unsafe-inline'
 * (doc/en/us/features/6.0/content-security-policy.md).
 */
(function () {
    var root = document.querySelector( '.a4-workflow-processes' );
    if ( !root ) return;
    var each = function ( selector, fn ) { Array.prototype.forEach.call( root.querySelectorAll( selector ), fn ); };
    each( '.a4-workflow-js-only', function ( el ) { el.hidden = false; } );

    var boxes = function () { return root.querySelectorAll( 'input[name="SelectedProcessIDList[]"]' ); };
    var all = root.querySelector( '#wfp-select-all' );
    var cancelSelected = root.querySelector( '#wfp-cancel-selected' );
    var label = cancelSelected ? cancelSelected.textContent : '';
    var update = function () {
        var list = boxes(), checked = 0, visible = 0;
        Array.prototype.forEach.call( list, function ( box ) {
            if ( !box.closest( '.a4-card[data-search]' ).hidden ) { visible++; if ( box.checked ) checked++; }
        } );
        if ( all ) { all.checked = visible > 0 && checked === visible; all.indeterminate = checked > 0 && checked < visible; }
        if ( cancelSelected ) cancelSelected.textContent = checked > 0 ? label.replace( /\.\.\.$/, '' ) + ' (' + checked + ')...' : label;
    };
    if ( all ) all.addEventListener( 'change', function () {
        Array.prototype.forEach.call( boxes(), function ( box ) { if ( !box.closest( '.a4-card[data-search]' ).hidden ) box.checked = all.checked; } );
        update();
    } );
    root.addEventListener( 'change', function ( e ) { if ( e.target && e.target.name === 'SelectedProcessIDList[]' ) update(); } );

    var search = root.querySelector( '#wfp-search' );
    var count = root.querySelector( '#wfp-filter-count' );
    var noMatch = root.querySelector( '#wfp-no-match' );
    if ( search ) search.addEventListener( 'input', function () {
        var words = search.value.toLowerCase().split( /\s+/ ).filter( Boolean ), shown = 0, total = 0;
        each( '.a4-card[data-search]', function ( card ) {
            var text = card.getAttribute( 'data-search' ) || '';
            var hit = words.every( function ( w ) { return text.indexOf( w ) !== -1; } );
            card.hidden = !hit; total++; if ( hit ) shown++;
        } );
        each( '.a4-workflow-group', function ( group ) { group.hidden = !group.querySelector( '.a4-card[data-search]:not([hidden])' ); } );
        if ( noMatch ) noMatch.hidden = shown > 0;
        if ( count ) count.textContent = words.length ? root.getAttribute( 'data-shown' ).replace( '%shown', shown ).replace( '%total', total ) : '';
        update();
    } );
})();
