jQuery(function( $ )
{
    // Attache click event to search button and show input fields
    $('input.ezobject-relation-search-btn').on( 'click', _search ).removeClass('hide');

    // Enter in the search field searches (keydown and e.key: keypress and e.which are deprecated); show the fields
    $('input.ezobject-relation-search-text').on( 'keydown', function( e ){
        if ( e.key === 'Enter' && !e.isComposing )
        {
            return _search.call( this, e );
        }
    }).removeClass('hide');

    // Function handling search event
    function _search( e )
    {
        e.preventDefault();
        var box = $( this.parentNode.parentNode ), text = box.find('input.ezobject-relation-search-text');
        if ( text.val() )
        {
            var params = { 'CallbackID': box.attr('id'), 'EncodingFetchSection': 1 };
            var node = box.find("*[name*='_for_object_start_node']"), classes = box.find("input[name*='_for_object_class_constraint_list']");
            if ( node.length ) params['SearchSubTreeArray'] = node.val();
            if ( classes.length ) params['SearchContentClassIdentifier'] = classes.val();
            $.ez( 'ezjsc::search::' + text.val(), params, _searchCallBack );
        }
        return false;
    }

    // Ajax search callback
    function _searchCallBack( data )
    {
        if ( data && data.content !== '' )
        {
            var boxID = '#' + data.content.CallbackID, boxElem = $( boxID + ' div.ezobject-relation-search-browse'  );
            if ( data.content.SearchResultCount )
            {
                boxElem.empty();
                var arr = data.content.SearchResult, pub = $('#ezobjectrelation-search-published-text');
                for ( var i = 0, l = arr.length; i < l; i++ )
                {
                    var aElem = $( '<a></a>' );
                    aElem.on( 'click', { boxID: boxID,
                                           id: arr[i].id,
                                           name: arr[i].name,
                                           className: arr[i].class_name,
                                           sectionName: arr[i].section.name,
                                           publishedTxt: pub.val() }, function(e) {
                        ezajaxrelationsSearchAddObject( this, e.data.boxID, e.data.id, e.data.name, e.data.className, e.data.sectionName, e.data.publishedTxt );
                    } );
                    aElem.append( arr[i].name );
                    aElem.attr( 'title', aElem.text() );

                    boxElem.append( aElem );
                    boxElem.append( '<br />' );
                }
                boxElem.show();
            }
            else
            {
                // The message is the template's markup; the search string goes in as text.
                var parts = $('#ezobjectrelation-search-empty-result-text').html().split( '--search-string--' ),
                    message = $( '<p class="ezobjectrelation-search-empty-result"><\/p>' );
                for ( var k = 0; k < parts.length; k++ )
                {
                    if ( k > 0 ) message.append( $( '<em><\/em>' ).text( data.content.SearchString ) );
                    message.append( parts[k] );
                }
                boxElem.empty().append( message ).show();
            }
        }
        else
        {
            // ezjscore reports a failure in error_text beside an empty content; no answer at all says nothing
            var message = data && ( data.error_text || ( data.content && data.content.error_text ) );
            if ( message ) alert( message );
        }
    }

    // function for selecting a search result object
    function _addObject( link, boxID, id, name, className, sectionName, publishedTxt )
    {
        link.onclick = function(){return false;};
        link.className = 'disabled';
        var tr = $( boxID + ' table tbody tr:last-child' ), tds = tr.find('td'), listMode = tds.length > 4;
        if ( listMode )
        {
            if ( tds[1].innerHTML !== '--name--' )
            {
                tr = tr.clone(true).insertAfter(tr);
                tds = tr.find('td').slice( 1 );
            }
            else
            {
                tds = tds.slice( 1 );
            }
            tr[0].className = tr[0].className === 'bgdark' ? 'bglight' : 'bgdark';
            var priority = tr.find('td:last-child input');
            priority.val( parseInt( priority.val() ) + 1 );
            tr.find('td:first-child input').val( id );
        }
        else
        {
            $( boxID + ' input[name*=_data_object_relation_id_]' ).val( id )
        }
        // The name arrives HTML-encoded (ezjscAjaxContent); the class and section names and the date are text.
        tds.eq( 0 ).html( name );
        tds.eq( 1 ).text( className );
        tds.eq( 2 ).text( sectionName );
        tds.eq( 3 ).text( publishedTxt );
        $( boxID + ' table' ).removeClass('hide');
        $(boxID + ' .ezobject-relation-remove-button').removeClass('button-disabled').addClass('button').prop('disabled', false);
        $(boxID + ' .ezobject-relation-no-relation').addClass('hide');
    }

    // register searchAdd gloablly as it is used on search links
    window.ezajaxrelationsSearchAddObject = _addObject;
});
