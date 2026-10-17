/*
 * admin4 setup/rad: the search box over the extension points; it hides the cards and groups that do not match.
 * A file of its own, so the page runs under a Content Security Policy without 'unsafe-inline'
 * (doc/features/6.0/content-security-policy.md).
 */
( function () {
    var bar   = document.getElementById( 'radFind' ),
        input = document.getElementById( 'radFindInput' ),
        clear = document.getElementById( 'radFindClear' ),
        count = document.getElementById( 'radFindCount' ),
        none  = document.getElementById( 'radFindNone' );

    if ( !bar || !input ) return;

    // Collect the cards, and the text of each one, once. Reading textContent
    // per keystroke on sixty cards is wasteful and it never changes.
    // Whole class name only. a4-title-row and a4-setup-rad-point-what are children of
    // a card, and matching on a prefix would pick those up and hide the inside
    // of every card rather than the card.
    function hasClass( node, wanted )
    {
        return ( ' ' + ( node.className || '' ) + ' ' ).indexOf( ' ' + wanted + ' ' ) !== -1;
    }

    var cards = [], groups = [], i, all = document.getElementsByTagName( 'div' );

    for ( i = 0; i < all.length; i++ )
    {
        if ( hasClass( all[i], 'a4-setup-rad-point' ) )
            cards.push( { node: all[i], text: ( all[i].textContent || all[i].innerText || '' ).toLowerCase() } );
        else if ( hasClass( all[i], 'a4-setup-rad-group' ) )
            groups.push( all[i] );
    }

    if ( !cards.length ) return;

    // Only shown once it is known to work, so a browser that got this far is
    // the only one offered a box that does something.
    bar.hidden = false;

    function show( node, on )
    {
        var name = ( node.className || '' ).replace( / ?\bis-hidden\b/, '' );
        node.className = on ? name : name + ' is-hidden';
    }

    // Every word has to match, in any order and anywhere in the card. That is
    // what lets "cluster handler" and "handler cluster" both find the same
    // thing, which is how people actually type.
    function matches( text, words )
    {
        for ( var w = 0; w < words.length; w++ )
            if ( text.indexOf( words[w] ) === -1 )
                return false;

        return true;
    }

    function run()
    {
        var query = input.value.toLowerCase().replace( /^\s+|\s+$/g, '' ),
            words = query === '' ? [] : query.split( /\s+/ ),
            shown = 0, c, g;

        for ( c = 0; c < cards.length; c++ )
        {
            var on = words.length === 0 || matches( cards[c].text, words );
            show( cards[c].node, on );
            if ( on ) shown++;
        }

        // A group with nothing left in it goes too, or the page is a list of
        // headings with nothing under them.
        for ( g = 0; g < groups.length; g++ )
        {
            var points = groups[g].getElementsByTagName( 'div' ), any = false, p;

            for ( p = 0; p < points.length; p++ )
                if ( hasClass( points[p], 'a4-setup-rad-point' ) && !hasClass( points[p], 'is-hidden' ) )
                {
                    any = true;
                    break;
                }

            show( groups[g], any );
        }

        count.textContent = words.length === 0
                            ? ''
                            : ( count.getAttribute( 'data-text' ) || '%shown / %count' ).split( '%shown' ).join( shown ).split( '%count' ).join( cards.length );

        if ( none ) none.hidden = !( words.length && shown === 0 );
    }

    // input: typing, pasting, cutting and dictation alike, not only a key let go
    input.addEventListener( 'input', run );

    if ( clear )
        clear.onclick = function () { input.value = ''; run(); input.focus(); return false; };

    // Esc clears rather than leaving somebody with a filtered page and no
    // obvious way back to all of it.
    input.onkeydown = function ( event ) {
        if ( ( event || window.event ).keyCode === 27 ) { input.value = ''; run(); }
    };

    run();
} )();
