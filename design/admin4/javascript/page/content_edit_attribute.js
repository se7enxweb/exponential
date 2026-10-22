/*
 * The attributes of the edit form (content/edit_attribute.tpl): the groups collapse and expand, a group with an
 * invalid attribute stays open, and the data controls are marked required and invalid (aria-required, aria-invalid,
 * the attribute's message as description). The error summary takes the focus after a failed store.
 * A file of its own, so the page runs under a Content Security Policy without 'unsafe-inline'
 * (doc/eng-us/features/6.0/content-security-policy.md).
 */
jQuery(function( $ )
{
    // An attribute the summary names is invalid even when the datatype set no message (an unknown error):
    // mark it, so its group opens below and the link does not lead into a collapsed group
    $('#a4-error-summary a[href^="#ezcca-attr-"]').each( function(){
        $( document.getElementById( this.getAttribute('href').slice( 1 ) ) ).addClass('is-invalid');
    } );

    $('fieldset.ezcca-collapsible legend a').on( 'click', function( e )
    {
        // the link only toggles its group (it was href="JavaScript:void(0);", which a Content Security Policy blocks)
        e.preventDefault();
        var container = $( this.parentNode.parentNode ), inner = container.find('div.ezcca-collapsible-fieldset-content');
        if ( container.hasClass('ezcca-collapsed') )
        {
            container.removeClass('ezcca-collapsed');
            inner.slideDown( 150 );
        }
        else
        {
            inner.slideUp( 150, function(){
                $( this.parentNode ).addClass('ezcca-collapsed');
            });
        }
    });
    // Collapse by default, unless the group has at least one attribute with a validation error
    $('fieldset.ezcca-collapsible').each( function(){
        if ( $(this).find('label.message-error, legend.message-error, .a4-content-attr.is-invalid').length == 0 )
        {
            $(this).addClass('ezcca-collapsed').find('div.ezcca-collapsible-fieldset-content').hide();
        }
    } );

    // A link to an attribute (the list of fields above the form, the error summary) opens the collapsed group the
    // attribute stands in, so it does not lead to a hidden card
    $( document ).on( 'click', 'a[href^="#ezcca-attr-"]', function(){
        var target = document.getElementById( this.getAttribute('href').slice( 1 ) );
        if ( !target ) return;
        $( target ).parents('fieldset.ezcca-collapsible.ezcca-collapsed').each( function(){
            $( this ).removeClass('ezcca-collapsed').find('div.ezcca-collapsible-fieldset-content').first().show();
        } );
    } );

    // The datatype templates draw the controls; required and invalid are set on them here, once for all
    // datatypes: aria-required, aria-invalid and the attribute's message as description. The original
    // (read-only) side of a translation is left alone.
    // The data controls only: no buttons, file or reset inputs, no remove/select checkboxes of lists (relations,
    // matrix rows); a datatype template can opt a control out with the class a4-no-field-mark.
    var controls = 'input:not([type=hidden]):not([type=submit]):not([type=button]):not([type=image]):not([type=reset]):not([type=file]), select, textarea';
    function dataControls( group )
    {
        return $( group ).find( controls ).not('.original *, .a4-no-field-mark, .a4-no-field-mark *').filter( function(){
            return !( /^(checkbox|radio)$/.test( this.type ) && /delete|remove|selection/i.test( this.name || '' ) );
        } );
    }
    $('.a4-content-attr.is-required').each( function(){
        dataControls( this ).attr( 'aria-required', 'true' );
    } );
    $('.a4-content-attr.is-invalid').each( function(){
        var error = this.id.replace( 'ezcca-attr-', 'ezcca-error-' );
        if ( !document.getElementById( error ) ) error = '';
        dataControls( this ).each( function(){
            this.setAttribute( 'aria-invalid', 'true' );
            if ( error )
            {
                var described = ( this.getAttribute('aria-describedby') || '' ).split(' ').filter( Boolean );
                if ( described.indexOf( error ) === -1 ) described.push( error );
                this.setAttribute( 'aria-describedby', described.join(' ') );
            }
        } );
    } );
    // A control a datatype template draws without a name of its own (no label, title or ARIA name: the author list's
    // name and email fields, the selects of an enhanced selection) is named by its attribute's name and, in a table,
    // by its column's header too, so no field of the form is announced without a name (WCAG 4.1.2)
    $('.a4-content-attr').each( function(){
        var name = document.getElementById( this.id.replace( 'ezcca-attr-', 'ezcca-name-' ) );
        if ( !name ) return;
        dataControls( this ).each( function(){
            if ( ( this.labels && this.labels.length ) || this.title || this.getAttribute('aria-label') || this.getAttribute('aria-labelledby') ) return;
            var label = $( name ).text().trim(), cell = $( this ).closest('td, th')[0], row = cell && $( cell ).closest('table').find('tr').first()[0];
            var head = row && row.cells[cell.cellIndex];
            if ( head && head.tagName === 'TH' && head !== cell && $( head ).text().trim() ) label += ': ' + $( head ).text().trim();
            if ( label ) this.setAttribute( 'aria-label', label );
        } );
    } );
    // A failed store: the summary takes the focus, so it is read and its links lead to the fields
    $('#a4-error-summary').trigger('focus');
});
