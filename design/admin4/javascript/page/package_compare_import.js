/*
 * admin4 package/compare (the import confirmation): All/None per item over the plain value checkboxes, and a class
 * left out takes the values that need it along. A file of its own, so the page runs under a Content Security Policy
 * without 'unsafe-inline' (doc/features/6.0/content-security-policy.md).
 */
(function () {
    var blocks = document.querySelectorAll( '.pcmp-import details.pcmp-values' );
    for ( var i = 0; i < blocks.length; i++ )
    {
        var bar = blocks[i].querySelector( '.pcmp-values-all' );
        if ( !bar ) continue;
        bar.hidden = false;
        bar.addEventListener( 'click', function ( event )
        {
            var mode = event.target.getAttribute( 'data-pcmp-values' );
            if ( !mode ) return;
            var boxes = this.parentNode.querySelectorAll( 'input[name="ImportValue[]"]:not([disabled])' );
            for ( var j = 0; j < boxes.length; j++ ) boxes[j].checked = mode === 'all';
        } );
    }
    // A class left out takes the values that need it along (the import leaves them out either way)
    var classes = document.querySelectorAll( '.pcmp-import input[data-pcmp-class]' );
    for ( var k = 0; k < classes.length; k++ )
    {
        classes[k].addEventListener( 'change', function ()
        {
            var needing = document.querySelectorAll( '.pcmp-import input[data-needs-class="' + this.getAttribute( 'data-pcmp-class' ) + '"]' );
            for ( var m = 0; m < needing.length; m++ ) needing[m].checked = this.checked;
        } );
    }
})();
