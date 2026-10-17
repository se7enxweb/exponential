/*
 * admin4 pdf/edit: the focus when the page opens - the first invalid field (or the error box) after a failed save, the
 * title otherwise. The template says which in data-error on #pdf-edit-script. A file of its own, so the page runs
 * under a Content Security Policy without 'unsafe-inline' (doc/features/6.0/content-security-policy.md).
 */
var expPdfEditScript = document.getElementById( 'pdf-edit-script' );
var expPdfEditError = !!expPdfEditScript && expPdfEditScript.getAttribute( 'data-error' ) === '1';
(function () {
    var box = document.getElementById( 'pdf-edit-errors' );
    if ( expPdfEditError && box ) {
        var first = document.querySelector( '.exp-pdf [aria-invalid="true"]' );
        ( first && first.focus ? first : box ).focus();
        return;
    }
    var title = document.getElementById( 'pdfTitle' );
    if ( title ) { title.focus(); title.select(); }
})();
