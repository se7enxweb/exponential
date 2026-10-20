/*
 * admin4 setup/extensions: the search, the order of the active extensions (drag and drop, arrows) and the pending plan.
 * A file of its own, so the page runs under a Content Security Policy without 'unsafe-inline'
 * (doc/eng-us/features/6.0/content-security-policy.md).
 */
(function () {
    'use strict';
    var form = document.getElementById( 'extension-form' );
    var list = document.getElementById( 'ext-list' );
    if ( !form || !list ) return;
    var root = form.closest( '.a4-setup-extpage' );
    root.querySelectorAll( '.a4-setup-js-only' ).forEach( function ( el ) { el.hidden = false; } );
    root.querySelectorAll( '.a4-setup-nojs-only' ).forEach( function ( el ) { el.hidden = true; } );

    var planBox = document.getElementById( 'ext-plan-inputs' );
    var pending = document.getElementById( 'ext-pending' );
    var pendingText = document.getElementById( 'ext-pending-text' );
    var reviewBottom = document.getElementById( 'ext-review-bottom' );
    var live = document.getElementById( 'ext-live' );
    var countLine = document.getElementById( 'ext-count' );
    var none = document.getElementById( 'ext-none' );
    var search = document.getElementById( 'ext-search' );
    var initial = planBox.getAttribute( 'data-initial' );
    var serverChanged = planBox.getAttribute( 'data-server-changed' ) === '1';
    var reviewOpen = !!document.getElementById( 'ext-review' );
    var dragged = null;

    function cards() { return Array.prototype.slice.call( list.querySelectorAll( 'li.a4-setup-ext' ) ); }
    function card( name ) { return cards().filter( function ( li ) { return li.getAttribute( 'data-name' ) === name; } )[0]; }
    function plan() { return Array.prototype.map.call( planBox.querySelectorAll( 'input' ), function ( i ) { return i.value; } ); }
    function setPlan( names ) {
        planBox.textContent = '';
        names.forEach( function ( n ) {
            var i = document.createElement( 'input' );
            i.type = 'hidden'; i.name = 'ExtensionPlan[]'; i.value = n;
            planBox.appendChild( i );
        } );
    }
    function sortMode() { var r = form.querySelector( 'input[name="ExtensionSort"]:checked' ); return r ? r.value : 'order'; }
    function filterMode() { var r = form.querySelector( 'input[name="ExtensionFilter"]:checked' ); return r ? r.value : 'all'; }

    // Positions, the arrows' disabled state and the pending bar follow the plan
    function renumber() {
        var names = plan();
        names.forEach( function ( n, i ) {
            var li = card( n ); if ( !li ) return;
            var num = li.querySelector( '.a4-setup-ext-n' ); if ( num ) num.textContent = i + 1;
            var up = li.querySelector( '.a4-setup-move[data-move="-1"]' ), down = li.querySelector( '.a4-setup-move[data-move="1"]' );
            if ( up ) up.disabled = i === 0;
            if ( down ) down.disabled = i === names.length - 1;
        } );
        var dirty = serverChanged || names.join( ',' ) !== initial;
        if ( !reviewOpen ) pending.hidden = !dirty;
        pending.classList.toggle( 'is-dirty', dirty );
        if ( reviewBottom ) reviewBottom.disabled = !dirty;
        if ( !dirty ) cards().forEach( function ( li ) { li.classList.remove( 'is-changed' ); } );
    }

    // The address carries the sort and the filter, as /setup/extensions/(sort)/name/(dir)/desc/(show)/active does
    var baseUrl = form.getAttribute( 'action' ).replace( /\/\(.*$/, '' );
    function keepInAddress() {
        if ( !window.history || !history.replaceState ) return;
        var s = sortMode().split( '-' ), f = filterMode(), url = baseUrl;
        if ( s[0] !== 'order' || s[1] ) url += '/(sort)/' + s[0];
        if ( s[1] === 'desc' ) url += '/(dir)/desc';
        if ( f !== 'all' ) url += '/(show)/' + f;
        try { history.replaceState( history.state, '', url + location.hash ); } catch ( err ) {}
    }

    // Cards in the order chosen: the active ones by the plan, then the others by name; or all by name, A to Z or Z to A
    function arrange() {
        var names = plan();
        var byName = function ( a, b ) { return a.getAttribute( 'data-name' ).localeCompare( b.getAttribute( 'data-name' ), undefined, { numeric: true, sensitivity: 'base' } ); };
        var all = cards();
        var sorted;
        if ( sortMode() === 'name' ) sorted = all.sort( byName );
        else if ( sortMode() === 'name-desc' ) sorted = all.sort( byName ).reverse();
        else {
            var active = names.map( card ).filter( Boolean );
            var rest = all.filter( function ( li ) { return active.indexOf( li ) === -1; } ).sort( byName );
            sorted = active.concat( rest );
        }
        sorted.forEach( function ( li ) { list.appendChild( li ); } );
        var grips = sortMode() === 'order';
        list.querySelectorAll( '.a4-grip' ).forEach( function ( g ) { g.hidden = !grips; } );
    }

    function filter() {
        var q = ( search.value || '' ).trim().toLowerCase(), f = filterMode(), shown = 0, all = cards();
        all.forEach( function ( li ) {
            var ok = true, level = li.getAttribute( 'data-level' ), active = li.getAttribute( 'data-active' ) === '1';
            if ( f === 'active' && !active ) ok = false;
            if ( f === 'inactive' && active ) ok = false;
            if ( f === 'problems' && level !== 'warn' && level !== 'bad' ) ok = false;
            if ( ok && q && li.getAttribute( 'data-search' ).indexOf( q ) === -1 ) ok = false;
            li.hidden = !ok;
            if ( ok ) shown++;
        } );
        none.hidden = shown > 0;
        countLine.textContent = list.getAttribute( 'data-count' ).replace( '%shown', shown ).replace( '%total', all.length );
    }

    function move( name, delta, focusEl ) {
        var names = plan(), i = names.indexOf( name ), j = i + delta;
        if ( i < 0 || j < 0 || j >= names.length ) return;
        names.splice( i, 1 ); names.splice( j, 0, name );
        setPlan( names );
        if ( sortMode() === 'order' ) arrange();
        var li = card( name ); if ( li ) li.classList.add( 'is-changed' );
        renumber();
        if ( focusEl ) focusEl.focus();
        live.textContent = list.getAttribute( 'data-moved' ).replace( '%name', name ).replace( '%position', j + 1 );
    }

    // The arrows move in place; activate and deactivate still ask the server, which places by the dependencies
    list.addEventListener( 'click', function ( e ) {
        var b = e.target.closest ? e.target.closest( '.a4-setup-move' ) : null;
        if ( !b ) return;
        e.preventDefault();
        var li = b.closest( 'li.a4-setup-ext' );
        move( li.getAttribute( 'data-name' ), parseInt( b.getAttribute( 'data-move' ), 10 ), b );
        if ( b.disabled ) { var other = li.querySelector( '.a4-setup-move:not([disabled])' ); if ( other ) other.focus(); }
    } );

    // The grip: drag, or the arrow keys
    list.addEventListener( 'keydown', function ( e ) {
        var g = e.target.closest ? e.target.closest( '.a4-grip' ) : null;
        if ( !g || ( e.key !== 'ArrowUp' && e.key !== 'ArrowDown' ) ) return;
        e.preventDefault();
        move( g.closest( 'li.a4-setup-ext' ).getAttribute( 'data-name' ), e.key === 'ArrowUp' ? -1 : 1, g );
    } );
    list.addEventListener( 'dragstart', function ( e ) {
        var g = e.target.closest ? e.target.closest( '.a4-grip' ) : null;
        if ( !g ) { e.preventDefault(); return; }
        dragged = g.closest( 'li.a4-setup-ext' );
        e.dataTransfer.effectAllowed = 'move';
        try { e.dataTransfer.setData( 'text/plain', dragged.getAttribute( 'data-name' ) ); e.dataTransfer.setDragImage( dragged, 20, 20 ); } catch ( err ) {}
        setTimeout( function () { if ( dragged ) dragged.classList.add( 'is-dragging' ); }, 0 );
    } );
    list.addEventListener( 'dragover', function ( e ) {
        if ( !dragged ) return;
        var over = e.target.closest ? e.target.closest( 'li.a4-setup-ext' ) : null;
        if ( !over || over === dragged || over.getAttribute( 'data-active' ) !== '1' ) return;
        e.preventDefault();
        e.dataTransfer.dropEffect = 'move';
        var box = over.getBoundingClientRect();
        list.insertBefore( dragged, ( e.clientY - box.top ) > box.height / 2 ? over.nextSibling : over );
    } );
    list.addEventListener( 'drop', function ( e ) { if ( dragged ) e.preventDefault(); } );
    list.addEventListener( 'dragend', function () {
        if ( !dragged ) return;
        dragged.classList.remove( 'is-dragging' );
        var name = dragged.getAttribute( 'data-name' );
        dragged = null;
        var order = cards().filter( function ( li ) { return li.getAttribute( 'data-active' ) === '1'; } ).map( function ( li ) { return li.getAttribute( 'data-name' ); } );
        var before = plan().join( ',' );
        setPlan( order );
        renumber();
        if ( order.join( ',' ) !== before ) {
            var li = card( name ); if ( li ) li.classList.add( 'is-changed' );
            live.textContent = list.getAttribute( 'data-moved' ).replace( '%name', name ).replace( '%position', order.indexOf( name ) + 1 );
        }
    } );

    // Filter, search and sort without a reload
    form.addEventListener( 'change', function ( e ) {
        if ( e.target.name === 'ExtensionSort' ) { arrange(); filter(); keepInAddress(); }
        else if ( e.target.name === 'ExtensionFilter' ) { filter(); keepInAddress(); }
    } );
    search.addEventListener( 'input', filter );
    search.addEventListener( 'keydown', function ( e ) { if ( e.key === 'Enter' ) e.preventDefault(); } );

    arrange();
    renumber();
    filter();
    keepInAddress();
    var review = document.getElementById( 'ext-review' );
    if ( review && !location.hash ) review.focus();
})();
