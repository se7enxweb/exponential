/*
 * staticpublish/target: the wizard of a static publishing target, one step at a time. Without this file every step
 * shows at once and the form works the same. The step list (.a4-steps) marks the current step and those before it;
 * Back and Next move between the steps; a step can be opened from the list. The example link of step 5 follows the
 * link mode and the base path as they are typed (expStaticAdmin::linkExample() writes the first one). When the form
 * comes back with errors every step stays open, so each error is in sight. A file of its own, so the page runs under
 * a Content Security Policy without 'unsafe-inline'; the same file in design/admin and design/admin4.
 */
(function () {
    'use strict';
    var form = document.getElementById( 'sp-wizard' );
    if ( !form ) return;
    var steps = Array.prototype.slice.call( form.querySelectorAll( '[data-sp-step]' ) );
    var items = Array.prototype.slice.call( document.querySelectorAll( '#sp-wizard-steps li' ) );

    // the live example of a rewritten link (step 5)
    var example = document.getElementById( 'sp-link-example' );
    var box = document.getElementById( 'sp-link-preview' );
    var basePath = document.getElementById( 'sp-basepath' );
    function normalise( path ) {
        var parts = String( path || '' ).replace( /\\/g, '/' ).split( '/' ).filter( function ( p ) { return p !== '' && p !== '.' && p !== '..'; } );
        return parts.length ? '/' + parts.join( '/' ) + '/' : '/';
    }
    function updateExample() {
        if ( !example || !box ) return;
        var mode = form.querySelector( 'input[name="Target[Links]"]:checked' );
        example.textContent = mode && mode.value === 'base-path' ? normalise( basePath ? basePath.value : '' ) + 'about-us/team/' : box.getAttribute( 'data-relative' );
    }
    form.addEventListener( 'input', updateExample );
    form.addEventListener( 'change', updateExample );
    updateExample();

    if ( !steps.length || document.querySelector( '.a4-error-summary' ) ) return;

    function show( n, focus ) {
        n = Math.max( 1, Math.min( steps.length, n ) );
        steps.forEach( function ( s ) { s.hidden = parseInt( s.getAttribute( 'data-sp-step' ), 10 ) !== n; } );
        items.forEach( function ( li, i ) {
            li.classList.toggle( 'is-current', i + 1 === n );
            li.classList.toggle( 'is-done', i + 1 < n );
            if ( i + 1 === n ) li.setAttribute( 'aria-current', 'step' ); else li.removeAttribute( 'aria-current' );
        } );
        if ( focus ) {
            var heading = steps[n - 1].querySelector( 'h2' );
            if ( heading ) { heading.setAttribute( 'tabindex', '-1' ); heading.focus(); }
        }
    }
    Array.prototype.forEach.call( form.querySelectorAll( '[data-sp-nav]' ), function ( el ) { el.hidden = false; } );
    form.addEventListener( 'click', function ( event ) {
        var button = event.target.closest( '[data-sp-next]' );
        if ( !button ) return;
        event.preventDefault();
        show( parseInt( button.getAttribute( 'data-sp-next' ), 10 ), true );
    } );
    items.forEach( function ( li, i ) {
        var link = li.querySelector( 'a' );
        if ( link ) link.addEventListener( 'click', function ( event ) { event.preventDefault(); show( i + 1, true ); } );
    } );
    var start = /^#sp-step-(\d)$/.exec( window.location.hash || '' );
    // the step a GitHub action of the form came back to (data-sp-start), else the first
    var back = parseInt( form.getAttribute( 'data-sp-start' ) || '0', 10 );
    show( start ? parseInt( start[1], 10 ) : ( back > 0 ? back : 1 ), false );
}());
