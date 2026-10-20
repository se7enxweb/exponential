/*
 * The search results (content/searchresult.tpl): the box that selects every result, and "Edit selected", which
 * posts the ticked results to the multi-object editor (data-multiedit-url of the button) in a form of its own, with
 * the request token. A file of its own, so the page runs under a Content Security Policy without 'unsafe-inline'
 * (doc/en/us/features/6.0/content-security-policy.md).
 */
(function () {
    var all    = document.getElementById( 'ezsearch-select-all' ),
        button = document.getElementById( 'ezsearch-multiedit' );

    function boxes()
    {
        return Array.prototype.slice.call( document.querySelectorAll( 'input.ezsearch-select' ) );
    }

    if ( all )
        all.onclick = function () {
            var checked = this.checked;
            boxes().forEach( function ( box ) { box.checked = checked; } );
        };

    if ( !button )
        return;

    button.onclick = function () {
        var chosen = boxes().filter( function ( box ) { return box.checked; } );

        if ( chosen.length === 0 )
            return;

        var form = document.createElement( 'form' );
        form.method = 'post';
        form.action = button.getAttribute( 'data-multiedit-url' );

        chosen.forEach( function ( box ) {
            var field = document.createElement( 'input' );
            field.type  = 'hidden';
            field.name  = 'MultiEditNodeIDArray[]';
            field.value = box.value;
            form.appendChild( field );
        } );

        var back = document.createElement( 'input' );
        back.type  = 'hidden';
        back.name  = 'MultiEditReturnURI';
        back.value = window.location.pathname + window.location.search;
        form.appendChild( back );

        // The request token, read from the meta tag the form token extension
        // puts on every page. Without it the post is refused before the view
        // is ever reached.
        var token = document.querySelector( 'meta[name="csrf-token"]' ),
            param = document.querySelector( 'meta[name="csrf-param"]' );

        if ( token )
        {
            var field = document.createElement( 'input' );
            field.type  = 'hidden';
            field.name  = param ? param.content : 'ezxform_token';
            field.value = token.content;
            form.appendChild( field );
        }

        document.body.appendChild( form );
        form.submit();
    };
})();
