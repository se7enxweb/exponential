/*
 * The search of the object relation fields (content/datatype/edit/ezobjectrelation_ajax_search.tpl): finds objects
 * through ezjscore's ezjsc::search and adds the one chosen to the relation (list). Plain JavaScript on Exponential UI
 * (exp::core: Exp.ready, Exp.util.request, looked for once the page is parsed); no jQuery needed. The file keeps its
 * name, which the templates of the admin designs load.
 * The search is posted as ezjscore's $.ez() posted it: to ezjscore/call/ with the fields, then
 * ezjscServer_function_arguments=ezjsc::search and the form token.
 */
(function( window, document )
{
    var Exp;

    function each( list, fn )
    {
        Array.prototype.forEach.call( list, fn );
    }

    // as jQuery's .append( string ): HTML when it looks like HTML, else a text node
    function append( el, content )
    {
        if ( !content && content !== 0 ) return;
        content = String( content );
        if ( /<|&#?\w+;/.test( content ) ) el.insertAdjacentHTML( 'beforeend', content );
        else el.appendChild( document.createTextNode( content ) );
    }

    // as jQuery's .show(): an inline display: none goes, and an element still hidden by a stylesheet gets the
    // display of its kind
    function show( el )
    {
        if ( el.style.display === 'none' ) el.style.display = '';
        if ( el.style.display === '' && el.isConnected && window.getComputedStyle( el ).display === 'none' )
        {
            var probe = document.body.appendChild( document.createElement( el.nodeName ) ), display = window.getComputedStyle( probe ).display;
            probe.parentNode.removeChild( probe );
            el.style.display = display === 'none' ? 'block' : display;
        }
    }

    // as jQuery's .val( value ): null is ''
    function setValue( el, value )
    {
        el.value = value === null || value === undefined ? '' : String( value );
    }

    // as jQuery's .text( value ) and .html( value ): undefined reads instead of writing, so it changes nothing
    function setText( el, value )
    {
        if ( el && value !== undefined ) el.textContent = value;
    }

    // Exponential UI is looked for once the page is parsed: the older admin designs print this file in the head before
    // their own scripts, Exponential UI among them
    function start()
    {
        Exp = window.Exp;
        if ( !Exp || !Exp.ready || !Exp.util || !Exp.util.request )
        {
            if ( window.console ) window.console.error( 'ezajaxrelations: Exponential UI (exp::core) is not on the page' );
            return;
        }
        Exp.ready( init );
    }
    if ( document.readyState === 'loading' ) document.addEventListener( 'DOMContentLoaded', start );
    else start();

    function init()
    {
        // Attache click event to search button and show input fields
        each( document.querySelectorAll( 'input.ezobject-relation-search-btn' ), function( button )
        {
            button.addEventListener( 'click', _search );
            button.classList.remove( 'hide' );
        });

        // Enter in the search field searches (keydown and e.key: keypress and e.which are deprecated); show the fields
        each( document.querySelectorAll( 'input.ezobject-relation-search-text' ), function( input )
        {
            input.addEventListener( 'keydown', function( e )
            {
                if ( e.key === 'Enter' && !e.isComposing )
                {
                    return _search.call( this, e );
                }
            });
            input.classList.remove( 'hide' );
        });

        // Function handling search event
        function _search( e )
        {
            e.preventDefault();
            e.stopPropagation();
            var box = this.parentNode.parentNode, text = box.querySelector( 'input.ezobject-relation-search-text' );
            if ( text && text.value )
            {
                // the text is a POST parameter, not part of the call: in the call it lost its tags (strip_tags) and was
                // cut at '::', the separator of the call's arguments
                var params = { 'SearchStr': text.value, 'CallbackID': box.getAttribute( 'id' ), 'EncodingFetchSection': 1 };
                var node = box.querySelector( "*[name*='_for_object_start_node']" ), classes = box.querySelector( "input[name*='_for_object_class_constraint_list']" );
                var token = document.getElementById( 'ezxform_token_js' );
                if ( node ) params['SearchSubTreeArray'] = node.value;
                if ( classes ) params['SearchContentClassIdentifier'] = classes.value;
                params['ezjscServer_function_arguments'] = 'ezjsc::search';
                params['ezxform_token'] = token ? token.getAttribute( 'title' ) : '';
                // a failed request (an expired session, a server error) says so instead of doing nothing
                Exp.util.request( { url: Exp.config.call, method: 'POST', data: params, dataType: 'json' } ).then( _searchCallBack, function( xhr )
                {
                    var failed = document.getElementById( 'ezobjectrelation-search-failed-text' );
                    _error( failed ? failed.textContent : 'HTTP ' + ( xhr && xhr.status ? xhr.status : 0 ) );
                });
            }
            return false;
        }

        // An error: the page's toast (admin4.js), alert() where there is none
        function _error( message )
        {
            if ( !message ) return;
            if ( window.a4Toast ) window.a4Toast( message, 'bad' );
            else alert( message );
        }

        // Ajax search callback
        function _searchCallBack( data )
        {
            if ( data && data.content && typeof data.content === 'object' )
            {
                // the box's id, from the answer, as a selector: escaped, so an id the selector syntax does not take
                // (a digit first, a dot, a colon) finds its box instead of throwing
                var callbackID = String( data.content.CallbackID || '' );
                var boxID = '#' + ( window.CSS && window.CSS.escape ? window.CSS.escape( callbackID ) : callbackID.replace( /[^\w-]/g, '' ) );
                var boxElems = document.querySelectorAll( boxID + ' div.ezobject-relation-search-browse' );
                if ( data.content.SearchResultCount )
                {
                    var arr = data.content.SearchResult, pub = document.getElementById( 'ezobjectrelation-search-published-text' );
                    each( boxElems, function( boxElem )
                    {
                        boxElem.innerHTML = '';
                        arr.forEach( function( result )
                        {
                            // A result is a button (a link with href, so Tab reaches it and Enter presses it; Space too)
                            var aElem = document.createElement( 'a' );
                            var add = { id: result.id, name: result.name, className: result.class_name,
                                        sectionName: result.section.name, publishedTxt: pub ? pub.value : undefined };
                            aElem.setAttribute( 'href', '#' );
                            aElem.setAttribute( 'role', 'button' );
                            aElem.addEventListener( 'click', function( e )
                            {
                                e.preventDefault();
                                window.ezajaxrelationsSearchAddObject( this, boxID, add.id, add.name, add.className, add.sectionName, add.publishedTxt );
                            });
                            aElem.addEventListener( 'keydown', function( e )
                            {
                                if ( e.key === ' ' ) { e.preventDefault(); this.click(); }
                            });
                            append( aElem, result.name );
                            aElem.setAttribute( 'title', aElem.textContent );

                            boxElem.appendChild( aElem );
                            append( boxElem, '<br />' );
                        });
                        show( boxElem );
                    });
                }
                else
                {
                    // The message is the template's markup; the search string goes in as text.
                    var empty = document.getElementById( 'ezobjectrelation-search-empty-result-text' );
                    var parts = ( empty ? empty.innerHTML : '--search-string--' ).split( '--search-string--' );
                    each( boxElems, function( boxElem )
                    {
                        var message = document.createElement( 'p' );
                        message.className = 'ezobjectrelation-search-empty-result';
                        for ( var k = 0; k < parts.length; k++ )
                        {
                            if ( k > 0 )
                            {
                                var em = message.appendChild( document.createElement( 'em' ) );
                                setText( em, data.content.SearchString );
                            }
                            append( message, parts[k] );
                        }
                        boxElem.innerHTML = '';
                        boxElem.appendChild( message );
                        show( boxElem );
                    });
                }
            }
            else
            {
                // ezjscore reports a failure in error_text beside an empty content; no answer at all says nothing
                _error( data && ( data.error_text || ( data.content && data.content.error_text ) ) );
            }
        }

        // function for selecting a search result object; a result is added once (a second press of it added the same
        // object as a second relation row)
        function _addObject( link, boxID, id, name, className, sectionName, publishedTxt )
        {
            if ( link.getAttribute( 'aria-disabled' ) === 'true' ) return;
            link.setAttribute( 'aria-disabled', 'true' );
            link.className = 'disabled';
            var trs = Array.prototype.slice.call( document.querySelectorAll( boxID + ' table tbody tr:last-child' ) );
            var cells = function( rows ) { return rows.reduce( function( list, tr ) { return list.concat( Array.prototype.slice.call( tr.querySelectorAll( ':scope td' ) ) ); }, [] ); };
            var inputs = function( rows, selector ) { return rows.reduce( function( list, tr ) { return list.concat( Array.prototype.slice.call( tr.querySelectorAll( selector ) ) ); }, [] ); };
            var tds = cells( trs ), listMode = tds.length > 4;
            if ( listMode )
            {
                if ( tds[1].innerHTML !== '--name--' )
                {
                    trs = trs.map( function( tr )
                    {
                        var copy = tr.cloneNode( true );
                        tr.parentNode.insertBefore( copy, tr.nextSibling );
                        return copy;
                    });
                    tds = cells( trs ).slice( 1 );
                }
                else
                {
                    tds = tds.slice( 1 );
                }
                trs[0].className = trs[0].className === 'bgdark' ? 'bglight' : 'bgdark';
                var priority = inputs( trs, ':scope td:last-child input' );
                if ( priority.length )
                {
                    var next = parseInt( priority[0].value ) + 1;
                    priority.forEach( function( input ) { setValue( input, next ); } );
                }
                inputs( trs, ':scope td:first-child input' ).forEach( function( input ) { setValue( input, id ); } );
            }
            else
            {
                each( document.querySelectorAll( boxID + ' input[name*=_data_object_relation_id_]' ), function( input ) { setValue( input, id ); } );
            }
            // The name arrives HTML-encoded (ezjscAjaxContent); the class and section names and the date are text.
            if ( tds[0] && name !== undefined ) tds[0].innerHTML = name;
            setText( tds[1], className );
            setText( tds[2], sectionName );
            setText( tds[3], publishedTxt );
            each( document.querySelectorAll( boxID + ' table' ), function( table ) { table.classList.remove( 'hide' ); } );
            each( document.querySelectorAll( boxID + ' .ezobject-relation-remove-button' ), function( button )
            {
                button.classList.remove( 'button-disabled' );
                button.classList.add( 'button' );
                button.disabled = false;
            });
            each( document.querySelectorAll( boxID + ' .ezobject-relation-no-relation' ), function( el ) { el.classList.add( 'hide' ); } );
        }

        // register searchAdd gloablly as it is used on search links
        window.ezajaxrelationsSearchAddObject = _addObject;
    }
})( window, document );
