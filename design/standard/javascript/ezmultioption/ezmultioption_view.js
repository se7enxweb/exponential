/*
 * The multi-option datatype in view (content/datatype/view/ezmultioption2.tpl): loaded once per attribute after its
 * selects and radio buttons, with the attribute id and its rules on this script element (data-attribute-id,
 * data-rules). Keeps the rules in the global rules<attribute id>, as the inline script did, initializes the options
 * (ezmultioption.js) and checks an option when it changes (the selects and radio buttons with
 * data-ezmultioption-attribute). A file of its own, so the page runs under a Content Security Policy without
 * 'unsafe-inline' (doc/features/6.0/content-security-policy.md).
 */
(function () {
    var script = document.currentScript;
    if ( !script )
        return;
    var attributeID = script.getAttribute( 'data-attribute-id' );
    var rules = [];
    try {
        rules = JSON.parse( script.getAttribute( 'data-rules' ) || '[]' ) || [];
    } catch ( e ) {
        rules = [];
    }
    window['rules' + attributeID] = rules;

    // One listener for every attribute on the page: the onchange="ezmultioption_check_option( this, rules<id>, <id> )"
    // the selects and radio buttons had
    if ( !window.ezmultioptionViewListening )
    {
        window.ezmultioptionViewListening = true;
        document.addEventListener( 'change', function ( event ) {
            var node = event.target;
            if ( !node || !node.getAttribute )
                return;
            var id = node.getAttribute( 'data-ezmultioption-attribute' );
            if ( id === null )
                return;
            ezmultioption_check_option( node, window['rules' + id] || [], parseInt( id, 10 ) );
        } );
    }

    initSelects();
    init_options( rules, parseInt( attributeID, 10 ) );
}());
