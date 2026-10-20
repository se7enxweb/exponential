/*
 * admin4 section/edit: proposes and checks the identifier of the section.
 * A file of its own, so the page runs under a Content Security Policy without 'unsafe-inline'
 * (doc/en/us/features/6.0/content-security-policy.md). The state and the texts come from #section-edit-data.
 */
var expSectionEditData = document.getElementById( 'section-edit-data' );
var expSectionEditNew = expSectionEditData.getAttribute( 'data-section-edit-new' ) === '1';
var expSectionEditError = expSectionEditData.getAttribute( 'data-section-edit-error' ) === '1';
var expSectionEditText = {
    pattern: expSectionEditData.getAttribute( 'data-section-edit-text-pattern' ),
    empty: expSectionEditData.getAttribute( 'data-section-edit-text-empty' )
};
(function () {
    var nameEl = document.getElementById( 'sectionName' );
    var idEl = document.getElementById( 'SectionIdentifier' );
    if ( !nameEl || !idEl ) return;
    if ( expSectionEditError ) {
        var box = document.getElementById( 'section-edit-error' );
        idEl.focus(); idEl.select();
        if ( box ) box.setAttribute( 'aria-live', 'assertive' );
    } else {
        nameEl.focus(); nameEl.select();
    }
    // A new section's identifier follows its name ("Members area" -> "members_area") until it is typed in.
    var typed = !expSectionEditNew || idEl.value !== '';
    idEl.addEventListener( 'input', function () { typed = idEl.value !== ''; idEl.setCustomValidity( '' ); } );
    nameEl.addEventListener( 'input', function () {
        if ( typed ) return;
        var s = nameEl.value.toLowerCase();
        if ( s.normalize ) s = s.normalize( 'NFD' ).replace( /[̀-ͯ]/g, '' );
        s = s.replace( /ß/g, 'ss' ).replace( /[^a-z0-9_]+/g, '_' ).replace( /^[^a-z]+/, '' ).replace( /_+$/, '' );
        idEl.value = s;
    } );
    // The same checks as the server, before the round trip; the server still has the last word.
    document.getElementById( 'section-edit-form' ).addEventListener( 'submit', function ( e ) {
        var store = e.submitter ? e.submitter.name === 'StoreButton' : true;
        if ( !store ) return;
        var v = idEl.value.trim();
        var msg = v === '' ? expSectionEditText.empty : ( /^[A-Za-z][A-Za-z0-9_]*$/.test( v ) ? '' : expSectionEditText.pattern );
        idEl.setCustomValidity( msg );
        if ( msg ) { e.preventDefault(); idEl.setAttribute( 'aria-invalid', 'true' ); idEl.reportValidity(); idEl.focus(); }
    } );
})();
