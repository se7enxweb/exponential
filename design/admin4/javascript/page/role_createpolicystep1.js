/*
 * admin4 role/createpolicystep1: offers the functions of the chosen module already in step one.
 * A file of its own, so the page runs under a Content Security Policy without 'unsafe-inline'
 * (doc/eng-us/features/6.0/content-security-policy.md). The modules and their functions come from #createpolicy-modules.
 */
(function () {
    var form = document.getElementById( 'createpolicyform' );
    var moduleSel = document.getElementById( 'ezrole-createpolizy-module' );
    var fnSel = document.getElementById( 'ezrole-createpolizy-function' );
    var warn = document.getElementById( 'ezrole-createpolizy-everything' );
    var data = document.getElementById( 'createpolicy-modules' );
    if ( !form || !moduleSel || !fnSel || !data ) return;
    // module name -> its function names; [] when the list is empty
    var expPolicyModules = JSON.parse( data.getAttribute( 'data-modules' ) || '{}' ), expPolicyEveryFunction = data.getAttribute( 'data-text-every-function' );
    var nodes = form.querySelectorAll( '.exp-js-only' ), i;
    for ( i = 0; i < nodes.length; i++ ) nodes[i].hidden = false;
    function setOptions( list, disable ) {
        while ( fnSel.firstChild ) fnSel.removeChild( fnSel.firstChild );
        list.forEach( function ( item ) { var o = document.createElement( 'option' ); o.value = item; o.textContent = item; fnSel.appendChild( o ); } );
        fnSel.disabled = !!disable;
        if ( disable ) { fnSel.innerHTML = ''; var o = document.createElement( 'option' ); o.value = '*'; o.textContent = expPolicyEveryFunction; fnSel.appendChild( o ); }
    }
    function update() {
        var list = expPolicyModules[ moduleSel.value ];
        var has = !!( list && list.length );
        setOptions( has ? list : [], !has );
        form.querySelectorAll( '.button-function' ).forEach( function ( b ) { b.disabled = !has; } );
        form.querySelector( '.button-module' ).disabled = has;
        if ( warn ) warn.hidden = moduleSel.value !== '*';
    }
    moduleSel.addEventListener( 'change', update );
    update();
})();
