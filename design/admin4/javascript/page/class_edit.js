/*
 * admin4 class/edit: focuses the error list or the attribute changed last, and moves attributes in place.
 * Plain JavaScript on a4Dom (a4_dom.js, loaded by the page head), no jQuery. A file of its own, so the page runs under
 * a Content Security Policy without 'unsafe-inline' (doc/eng-us/features/6.0/content-security-policy.md).
 */
(function( fn )
{
    if ( !window.a4Dom )
    {
        if ( window.console ) window.console.error( 'class_edit.js: a4_dom.js is not on the page' );
        return;
    }
    window.a4Dom.ready( fn );
})(function()//called on document.ready
{
    var each = window.a4Dom.each;

    var errors = document.getElementById( 'class-edit-errors' );
    if ( errors ) errors.focus();

    var el = document.querySelector( '#LastChangedID input[name^=ContentAttribute_name]' );
    if ( el )
    {
        window.scrollTo( 0, Math.max( el.getBoundingClientRect().top + window.pageYOffset - 180, 0 ) );
        el.focus();
    }

    // The type menu at the top and the one at the bottom share a name: only the one beside the button pressed counts.
    var newTop = document.getElementById( 'NewButtonTop' );
    if ( newTop )
    {
        newTop.addEventListener( 'click', function()
        {
            var bottom = document.getElementById( 'DataTypeString' );
            if ( bottom ) bottom.disabled = true;
        } );
    }

    var list = document.getElementById( 'ezcca-edit-list' );
    if ( !list )
        return;

    var failed = list.getAttribute( 'data-move-failed' ) || '';
    var status = document.createElement( 'p' );
    status.className = 'ezcca-move-status a4-class-move-status';
    status.setAttribute( 'role', 'status' );
    status.setAttribute( 'aria-live', 'polite' );
    list.parentNode.insertBefore( status, list );

    function say( text, error )
    {
        status.textContent = text;
        status.classList.toggle( 'is-error', !!error );
    }

    function rows()
    {
        return Array.prototype.slice.call( list.querySelectorAll( ':scope > tbody > tr.ezcca-edit-list-item' ) );
    }

    // The numbers in front of the names and the row colours follow the rows.
    function renumberAttributes()
    {
        rows().forEach( function( tr, i )
        {
            var th = tr.querySelector( ':scope > td > table > tbody > tr > th.wide, :scope > td > table > tr > th.wide' );
            if ( th ) th.innerHTML = th.innerHTML.replace( /^\s*\d+\./, ( i + 1 ) + '.' );
            tr.classList.remove( 'bglight', 'bgdark' );
            tr.classList.add( i % 2 ? 'bgdark' : 'bglight' );
        } );
    }

    function moveButtons()
    {
        // admin4's buttons (name="MoveUp", value=the attribute id) and the classic inputs (name="MoveUp_<id>")
        return list.querySelectorAll( 'div.listbutton input[name^=Move], div.listbutton button[name^=Move]' );
    }

    function buttons( on )
    {
        each( moveButtons(), function( button )
        {
            button.disabled = !on;
            button.classList.toggle( 'disabled', !on );
        } );
    }

    // The priority fields follow the rows, so Apply and OK store the order shown.
    function priorities()
    {
        rows().forEach( function( tr, i )
        {
            each( tr.querySelectorAll( 'input[name^=ContentAttribute_priority]' ), function( input ){ input.value = ( i + 1 ) * 10; } );
        } );
    }

    // Move up, down, to the top or to the bottom, in place; the server stores it, and a move it did not store is
    // put back, so the page never shows an order that is not saved. One listener on the list for every move button
    // (a4Dom.on); while a move is saved the buttons are disabled and the list is aria-busy.
    window.a4Dom.on( list, 'click', 'div.listbutton input[name^=Move], div.listbutton button[name^=Move]', function( e )
    {
        e.preventDefault();
        e.stopPropagation();
        if ( this.disabled )
            return;
        var pressed = this, tr = this.closest( 'tr.ezcca-edit-list-item' ), param = this.name.split( '_' ), action = param[0];
        var id = param.length > 1 ? param[1] : this.value;
        var all = rows(), from = all.indexOf( tr ), last = all.length - 1;
        var to = { MoveUp: from - 1, MoveDown: from + 1, MoveTop: 0, MoveBottom: last }[ action ];
        if ( from === -1 || to === undefined || to < 0 || to > last || to === from )
            return;
        buttons( false );
        var place = function( index )
        {
            var others = rows().filter( function( row ){ return row !== tr; } );
            if ( index >= others.length )
                others[others.length - 1].parentNode.insertBefore( tr, others[others.length - 1].nextSibling );
            else
                others[index].parentNode.insertBefore( tr, others[index] );
            renumberAttributes();
            priorities();
        };
        place( to );

        // a form POST (the Content-Type jQuery's $.ajax() wrote) with the form token, the CSRF header and
        // X-Requested-With, which the server answers with X-Class-Attribute-Moved instead of the page
        var form = document.getElementById( 'ClassEdit' ), data = [ [ 'ContentClassHasInput', '0' ] ];
        data.push( [ action, id ] );
        list.setAttribute( 'aria-busy', 'true' );

        window.a4Dom.post( ( form && form.getAttribute( 'action' ) ) || window.location.href, data ).then( function( response )
        {
            if ( !response.ok )
            {
                place( from );
                say( failed + ' (HTTP ' + response.status + ')', true );
            }
            else if ( response.headers.get( 'X-Class-Attribute-Moved' ) !== '1' )
            {
                place( from );
                say( failed, true );
            }
            else
                say( '', false );
        }, function()
        {
            place( from );
            say( failed + ' (HTTP 0)', true );
        } ).then( function()
        {
            list.removeAttribute( 'aria-busy' );
            buttons( true );
            // disabling the pressed button took the focus away; a keyboard user goes on from the same button
            pressed.focus();
        } );
    } );
});
