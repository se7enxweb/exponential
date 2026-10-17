/*
 * The result bars of a poll (content/datatype/result/count/ezoption.tpl): each .resultbar gets the width its
 * data-result-width names, in percent (it was an inline style="width: ...%"). A file of its own, so the page runs
 * under a Content Security Policy without 'unsafe-inline' (doc/features/6.0/content-security-policy.md).
 */
(function () {
    var bars = document.querySelectorAll( '.resultbar[data-result-width]' );
    for ( var i = 0; i < bars.length; i++ )
    {
        bars[i].style.width = bars[i].getAttribute( 'data-result-width' ) + '%';
    }
}());
