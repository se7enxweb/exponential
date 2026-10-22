/*
 * The details tab of the node view (details.tpl): the Set buttons of the states and the section are highlighted
 * once their choice changes.
 * A file of its own, so the page runs under a Content Security Policy without 'unsafe-inline'
 * (doc/eng-us/features/6.0/content-security-policy.md).
 */
(function()
{
    function highlight( id )
    {
        var btn = document.getElementById( id );
        if ( btn && !btn.disabled )
        {
            btn.classList.remove('button');
            btn.classList.add('defaultbutton');
        }
    }
    var fields = document.querySelectorAll('#tab-details-states-list select, #tab-details-states-subtree');
    for ( var i = 0; i < fields.length; i++ )
    {
        fields[i].addEventListener('change', function()
        {
            highlight('tab-details-set-states');
        });
    }
    var section = document.getElementById('selected-section-id');
    if ( section )
    {
        section.addEventListener('change', function()
        {
            highlight('tab-details-set-section');
        });
    }
})();
