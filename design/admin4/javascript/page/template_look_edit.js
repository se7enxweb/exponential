/*
 * The edit form of the template look (override/templates/template_look_edit.tpl): its first text field gets the
 * focus, with the text selected. A file of its own, so the page runs under a Content Security Policy without
 * 'unsafe-inline' (doc/eng-us/features/6.0/content-security-policy.md).
 */
jQuery(function( $ )//called on document.ready
{
    var form = document.editform;
    if ( !form )
        return;
    for( var i = 0, elements = form.elements, l = elements.length; i < l; i++ )
    {
        if( elements[i].type == 'text' )
        {
            elements[i].select();
            elements[i].focus();
            return;
        }
    }
});
