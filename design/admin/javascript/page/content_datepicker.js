/*
 * The calendar buttons of the date and date and time fields (content/datatype/edit/ezdate.tpl, ezdatetime.tpl):
 * a click on a button with data-a4-datepicker opens Exponential UI's exp::datepicker, showDatePicker( base, id, type ),
 * with data-a4-datepicker-base, data-a4-datepicker-id and the type in data-a4-datepicker (date, datetime). One listener on the
 * document handles every button of the page. A file of its own, so the page runs under a Content Security Policy
 * without 'unsafe-inline' (doc/eng-us/features/6.0/content-security-policy.md).
 */
(function () {
    'use strict';
    if ( window.a4ContentDatepicker ) return;  // loaded once per page; a second copy would open the calendar twice
    window.a4ContentDatepicker = true;

    document.addEventListener( 'click', function ( e ) {
        var el = e.target;
        while ( el && el.nodeType !== 1 ) el = el.parentNode;
        var button = el && el.closest ? el.closest( '[data-a4-datepicker]' ) : null;
        if ( !button || typeof window.showDatePicker !== 'function' ) return;
        window.showDatePicker( button.getAttribute( 'data-a4-datepicker-base' ), button.getAttribute( 'data-a4-datepicker-id' ), button.getAttribute( 'data-a4-datepicker' ) );
    } );
})();
