/*
 * The URL aliases of a node (content/urlalias.tpl): the field for a new alias (AliasText) gets the focus, with
 * its text selected.
 * A file of its own, so the page runs under a Content Security Policy without 'unsafe-inline'
 * (doc/en/us/features/6.0/content-security-policy.md).
 */
jQuery(function( $ )//called on document.ready
{
    with( document.aliasform )
    {
        for( var i = 0, l = elements.length; i < l; i++ )
        {
            if( elements[i].type == 'text' && elements[i].name == 'AliasText' )
            {
                elements[i].select();
                elements[i].focus();
                return;
            }
        }
    }
});
