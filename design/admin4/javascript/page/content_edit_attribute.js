/*
 * The attributes of the edit form (content/edit_attribute.tpl): the groups collapse and expand, a group with an
 * invalid attribute stays open, and the data controls are marked required and invalid (aria-required, aria-invalid,
 * the attribute's message as description). The error summary takes the focus after a failed store.
 * A file of its own, so the page runs under a Content Security Policy without 'unsafe-inline'
 * (doc/eng-us/features/6.0/content-security-policy.md).
 */
(function()
{
    var each = function( list, fn ){ Array.prototype.forEach.call( list, function( el ){ fn.call( el, el ); } ); };

    // The toggle link of a group tells a screen reader whether the group is open
    function expanded( group, open )
    {
        var link = group.querySelector('legend a');
        if ( link ) link.setAttribute( 'aria-expanded', open ? 'true' : 'false' );
    }

    // shown and hidden as jQuery's show() and hide() did: hidden is display none, shown is the stylesheet's display
    // (or block when the stylesheet hides it)
    function show( el )
    {
        el.style.display = '';
        if ( window.getComputedStyle( el ).display === 'none' ) el.style.display = 'block';
    }
    function hide( el )
    {
        el.style.display = 'none';
    }

    // A group's content slides open or closed in 150 ms (height, vertical padding and margin, clipped meanwhile);
    // with reduced motion it opens or closes at once (as jQuery.fx.off does for jQuery's effects in admin4.js).
    // A second slide while one runs first finishes the running one.
    function slide( el, open, done )
    {
        var running = el.a4Slide;
        if ( running )
        {
            el.a4Slide = null;
            running.cancel();
            running.a4Finish();
        }
        var hidden = window.getComputedStyle( el ).display === 'none';
        var finish = function(){
            if ( !open ) hide( el );
            if ( done ) done.call( el );
        };
        if ( open ) show( el );
        if ( hidden === open && el.animate && !( window.matchMedia && window.matchMedia('(prefers-reduced-motion: reduce)').matches ) )
        {
            var style = window.getComputedStyle( el ), full = {}, none = {}, overflow = el.style.overflow;
            each( [ 'height', 'paddingTop', 'paddingBottom', 'marginTop', 'marginBottom' ], function( name ){
                full[name] = style[name];
                none[name] = '0px';
            } );
            el.style.overflow = 'hidden';
            var animation = el.animate( open ? [ none, full ] : [ full, none ], { duration: 150, easing: 'cubic-bezier(0.37, 0, 0.63, 1)' } );
            animation.a4Finish = function(){
                el.style.overflow = overflow;
                finish();
            };
            animation.onfinish = function(){
                if ( el.a4Slide !== animation ) return;
                el.a4Slide = null;
                animation.a4Finish();
            };
            el.a4Slide = animation;
            return;
        }
        finish();
    }

    function init()
    {
        // An attribute the summary names is invalid even when the datatype set no message (an unknown error):
        // mark it, so its group opens below and the link does not lead into a collapsed group
        each( document.querySelectorAll('#a4-error-summary a[href^="#ezcca-attr-"]'), function(){
            var attribute = document.getElementById( this.getAttribute('href').slice( 1 ) );
            if ( attribute ) attribute.classList.add('is-invalid');
        } );

        each( document.querySelectorAll('fieldset.ezcca-collapsible legend a'), function( link ){
            link.addEventListener( 'click', function( e )
            {
                // the link only toggles its group (it was href="JavaScript:void(0);", which a Content Security Policy blocks)
                e.preventDefault();
                var container = this.parentNode.parentNode, inner = container.querySelectorAll('div.ezcca-collapsible-fieldset-content');
                if ( container.classList.contains('ezcca-collapsed') )
                {
                    container.classList.remove('ezcca-collapsed');
                    expanded( container, true );
                    each( inner, function(){ slide( this, true ); } );
                }
                else
                {
                    expanded( container, false );
                    each( inner, function(){
                        slide( this, false, function(){
                            this.parentNode.classList.add('ezcca-collapsed');
                        });
                    } );
                }
            });
        } );
        // Collapse by default, unless the group has at least one attribute with a validation error
        each( document.querySelectorAll('fieldset.ezcca-collapsible'), function(){
            if ( !this.querySelector('label.message-error, legend.message-error, .a4-content-attr.is-invalid') )
            {
                this.classList.add('ezcca-collapsed');
                each( this.querySelectorAll('div.ezcca-collapsible-fieldset-content'), hide );
            }
            expanded( this, !this.classList.contains('ezcca-collapsed') );
        } );

        // A link to an attribute (the list of fields above the form, the error summary) opens the collapsed group the
        // attribute stands in, so it does not lead to a hidden card
        document.addEventListener( 'click', function( e ){
            var link = e.target && e.target.closest ? e.target.closest('a[href^="#ezcca-attr-"]') : null;
            if ( !link ) return;
            var target = document.getElementById( link.getAttribute('href').slice( 1 ) );
            if ( !target ) return;
            for ( var group = target.parentElement; group; group = group.parentElement )
            {
                if ( !group.matches('fieldset.ezcca-collapsible.ezcca-collapsed') ) continue;
                group.classList.remove('ezcca-collapsed');
                expanded( group, true );
                var inner = group.querySelector('div.ezcca-collapsible-fieldset-content');
                if ( inner ) show( inner );
            }
        } );

        // The datatype templates draw the controls; required and invalid are set on them here, once for all
        // datatypes: aria-required, aria-invalid and the attribute's message as description. The original
        // (read-only) side of a translation is left alone.
        // The data controls only: no buttons, file or reset inputs, no remove/select checkboxes of lists (relations,
        // matrix rows); a datatype template can opt a control out with the class a4-no-field-mark.
        var controls = 'input:not([type=hidden]):not([type=submit]):not([type=button]):not([type=image]):not([type=reset]):not([type=file]), select, textarea';
        function dataControls( group )
        {
            return Array.prototype.filter.call( group.querySelectorAll( controls ), function( control ){
                return !control.matches('.original *, .a4-no-field-mark, .a4-no-field-mark *')
                    && !( /^(checkbox|radio)$/.test( control.type ) && /delete|remove|selection/i.test( control.name || '' ) );
            } );
        }
        each( document.querySelectorAll('.a4-content-attr.is-required'), function(){
            each( dataControls( this ), function(){ this.setAttribute( 'aria-required', 'true' ); } );
        } );
        each( document.querySelectorAll('.a4-content-attr.is-invalid'), function(){
            var error = this.id.replace( 'ezcca-attr-', 'ezcca-error-' );
            if ( !document.getElementById( error ) ) error = '';
            each( dataControls( this ), function(){
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
        each( document.querySelectorAll('.a4-content-attr'), function(){
            var name = document.getElementById( this.id.replace( 'ezcca-attr-', 'ezcca-name-' ) );
            if ( !name ) return;
            each( dataControls( this ), function(){
                if ( ( this.labels && this.labels.length ) || this.title || this.getAttribute('aria-label') || this.getAttribute('aria-labelledby') ) return;
                var label = name.textContent.trim(), cell = this.closest('td, th'), table = cell && cell.closest('table'), row = table && table.querySelector('tr');
                var head = row && row.cells[cell.cellIndex];
                if ( head && head.tagName === 'TH' && head !== cell && head.textContent.trim() ) label += ': ' + head.textContent.trim();
                if ( label ) this.setAttribute( 'aria-label', label );
            } );
        } );
        // A failed store: the summary takes the focus, so it is read and its links lead to the fields
        var summary = document.getElementById('a4-error-summary');
        if ( summary ) summary.focus();
    }
    if ( document.readyState !== 'loading' ) init(); else document.addEventListener( 'DOMContentLoaded', init );
})();
