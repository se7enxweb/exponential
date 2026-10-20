/*
 * admin4 class/edit: focuses the error list or the attribute changed last, and moves attributes in place.
 * A file of its own, so the page runs under a Content Security Policy without 'unsafe-inline'
 * (doc/en/us/features/6.0/content-security-policy.md).
 */
jQuery(function( $ )//called on document.ready
{
    var errors = document.getElementById( 'class-edit-errors' );
    if ( errors ) errors.focus();

    // .length, not .size(): jQuery 3 removed .size().
    var el = $('#LastChangedID input[name^=ContentAttribute_name]');
    if ( el.length ) {
        window.scrollTo(0, Math.max( el.offset().top - 180, 0 ));
        el.trigger( 'focus' );
    }

    var list = $('#ezcca-edit-list');
    var status = $('<p class="ezcca-move-status a4-class-move-status" role="status" aria-live="polite"></p>').insertBefore( list ).data( 'failed', list.attr( 'data-move-failed' ) );

    // The numbers in front of the names and the row colours follow the rows.
    function renumberAttributes()
    {
        list.children('tbody').children('tr.ezcca-edit-list-item').each( function( i )
        {
            var th = $(this).find('> td > table > tbody > tr > th.wide, > td > table > tr > th.wide').first();
            th.html( th.html().replace( /^\s*\d+\./, ( i + 1 ) + '.' ) );
            $(this).removeClass( 'bglight bgdark' ).addClass( i % 2 ? 'bgdark' : 'bglight' );
        });
    }

    function buttons( on )
    {
        list.find('div.listbutton input[name^=Move]').prop( 'disabled', !on ).toggleClass( 'disabled', !on );
    }

    // Move up, down, to the top or to the bottom, in place; the server stores it, and a move it did not store is
    // put back, so the page never shows an order that is not saved. The priority fields follow the rows, so Apply
    // and OK store the order shown.
    function rows()
    {
        return list.children('tbody').children('tr.ezcca-edit-list-item');
    }
    function priorities()
    {
        rows().each( function( i ) { $(this).find('input[name^=ContentAttribute_priority]').val( ( i + 1 ) * 10 ); } );
    }
    list.find('div.listbutton input[name^=Move]').on('click', function( e )
    {
        e.preventDefault();
        var tr = $(this).closest('tr.ezcca-edit-list-item'), param = this.name.split('_'), action = param[0];
        var all = rows(), from = all.index( tr ), last = all.length - 1;
        var to = { MoveUp: from - 1, MoveDown: from + 1, MoveTop: 0, MoveBottom: last }[ action ];
        if ( to === undefined || to < 0 || to > last || to === from )
            return false;
        buttons( false );
        var place = function( index )
        {
            var others = rows().not( tr );
            if ( index >= others.length ) others.last().after( tr ); else others.eq( index ).before( tr );
            renumberAttributes();
            priorities();
        };
        place( to );

        var postVar = { 'ContentClassHasInput': 0 }, _tokenNode = document.getElementById('ezxform_token_js');
        var meta = document.querySelector('meta[name="csrf-token"]');
        postVar[ action ] = param[1];
        if ( _tokenNode ) postVar['ezxform_token'] = _tokenNode.getAttribute('title');
        $.ajax({
            type: 'POST',
            url: $('#ClassEdit').attr('action'),
            data: postVar,
            headers: meta ? { 'X-CSRF-Token': meta.getAttribute('content') } : {}
        }).done( function( data, text, xhr )
        {
            if ( xhr.getResponseHeader( 'X-Class-Attribute-Moved' ) !== '1' )
            {
                place( from );
                status.text( status.data( 'failed' ) ).addClass( 'is-error' );
                return;
            }
            status.text( '' ).removeClass( 'is-error' );
        }).fail( function( xhr )
        {
            place( from );
            status.text( status.data( 'failed' ) + ' (HTTP ' + xhr.status + ')' ).addClass( 'is-error' );
        }).always( function()
        {
            buttons( true );
        });
        return false;
    });

    // The type menu at the top and the one at the bottom share a name: only the one beside the button pressed counts.
    jQuery('#NewButtonTop').on('click', function()
    {
        jQuery('#DataTypeString').prop('disabled', true);
    });
});
