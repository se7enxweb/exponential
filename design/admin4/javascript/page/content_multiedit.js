/*
 * The multi-object edit form (content/multiedit.tpl): the buttons that expand and collapse every item, the items
 * that need fixing opened, and the autosave of the drafts. The autosave's settings and texts are data attributes of
 * #multiedit-autosave, which the template draws only when autosave is on. A file of its own, so the page runs under a
 * Content Security Policy without 'unsafe-inline' (doc/eng-us/features/6.0/content-security-policy.md).
 */
(function () {
    function panels()
    {
        return Array.prototype.slice.call(
            document.querySelectorAll( 'details.a4-content-multiedit-object' ) );
    }

    function setAll( open )
    {
        panels().forEach( function ( panel ) { panel.open = open; } );
    }

    var expand   = document.getElementById( 'multiedit-expand' ),
        collapse = document.getElementById( 'multiedit-collapse' );

    if ( expand )   expand.onclick   = function () { setAll( true ); };
    if ( collapse ) collapse.onclick = function () { setAll( false ); };

    // A form that has come back with something to fix opens the items that
    // need it, whatever the reader last collapsed: being told there is a
    // problem and then having to hunt for it is the worst of both.
    panels().forEach( function ( panel ) {
        if ( panel.className.indexOf( 'a4-content-multiedit-invalid' ) !== -1 )
            panel.open = true;
    } );
})();

// Autosave.
//
// ezautosave's own AutoSubmit posts to an endpoint naming one object and one
// version, which cannot say what this form holds. So the form is posted back
// to its own Save drafts action instead: one request, every draft stored, by
// the same code the button uses. The interval and the track-input preference
// come from autosave.ini, so an installation that has turned autosave off has
// it off here too.
(function () {
    var form     = document.forms.multiedit,
        place    = document.getElementById( 'multiedit-autosave' ),
        interval, track, texts;

    if ( !form || !place || !window.FormData || !window.fetch )
        return;

    // The settings: data attributes of the status element (autosave.ini, the template)
    interval = Number( place.getAttribute( 'data-autosave-interval' ) );
    track    = place.getAttribute( 'data-autosave-track' ) === '1';
    texts    = {
        saving: place.getAttribute( 'data-text-saving' ),
        saved:  place.getAttribute( 'data-text-saved' ),
        error:  place.getAttribute( 'data-text-error' )
    };
    if ( !( interval > 0 ) )
        return;

    var dirty = false, saving = false, debounce = null;

    function say( state, text )
    {
        place.className = 'a4-content-multiedit-autosave as-' + state;
        place.textContent = text;
    }

    function stamp()
    {
        var now = new Date();
        return ( '0' + now.getHours() ).slice( -2 ) + ':' + ( '0' + now.getMinutes() ).slice( -2 );
    }

    function save()
    {
        if ( saving || !dirty )
            return;

        saving = true;
        say( 'saving', texts.saving );

        var data = new FormData();

        // Files are left out on purpose. A file field cannot be re-sent
        // meaningfully every few minutes, and a datatype that receives no
        // upload keeps what it already has - which is what is wanted. The
        // buttons are left out too, except the one that means "store".
        Array.prototype.forEach.call( form.elements, function ( field ) {
            if ( !field.name || field.disabled || field.type === 'file' )
                return;
            if ( field.type === 'submit' || field.type === 'button' )
                return;
            if ( ( field.type === 'checkbox' || field.type === 'radio' ) && !field.checked )
                return;
            if ( field.multiple && field.options )
            {
                Array.prototype.forEach.call( field.options, function ( option ) {
                    if ( option.selected ) data.append( field.name, option.value );
                } );
                return;
            }
            data.append( field.name, field.value );
        } );

        data.append( 'MultiStoreButton', '1' );

        // The state is captured now: anything typed while the request is in
        // flight sets this again and goes out with the next save.
        dirty = false;

        fetch( form.getAttribute( 'action' ), {
            method: 'POST',
            body: data,
            credentials: 'same-origin'
        } ).then( function ( response ) {
            saving = false;
            if ( !response.ok )
                throw new Error( response.status );
            say( 'success', texts.saved.replace( '%time', stamp() ) );
        } ).catch( function () {
            saving = false;
            // Not saved: the next tick tries again.
            dirty = true;
            say( 'error', texts.error );
        } );
    }

    form.addEventListener( 'input', function () { dirty = true; } );
    form.addEventListener( 'change', function () {
        dirty = true;
        if ( !track )
            return;
        // Leaving a field saves, but not on every keystroke.
        if ( debounce ) clearTimeout( debounce );
        debounce = setTimeout( save, 2000 );
    } );

    setInterval( save, interval * 1000 );

    // Pressing a real button is a save of its own; do not race it.
    Array.prototype.forEach.call( form.elements, function ( field ) {
        if ( field.type === 'submit' )
            field.addEventListener( 'click', function () { dirty = false; } );
    } );
})();
