/*
 * admin4 workflow/edit, event "Wait until date": the attribute list follows the chosen class, so the Update
 * attributes button is hidden. A file of its own, so the page runs under a Content Security Policy without
 * 'unsafe-inline' (doc/eng-us/features/6.0/content-security-policy.md). The ids and the attributes of every class come from
 * the .exp-waituntildate-data element of each event; the file can be loaded once per event.
 */
(function ()
{
    var holders = document.querySelectorAll( '.exp-waituntildate-data' );
    for ( var i = 0; i < holders.length; i++ )
    {
        var holder = holders[i];
        if ( holder.getAttribute( 'data-ready' ) )
            continue;
        holder.setAttribute( 'data-ready', '1' );
        setup( holder );
    }

    function setup( holder )
    {
        document.getElementById( holder.getAttribute( 'data-update-button' ) ).style.display = 'none';
        var classSelect = document.getElementById( holder.getAttribute( 'data-class-select' ) );
        var classAttributeSelect = document.getElementById( holder.getAttribute( 'data-attribute-select' ) );
        var classAttributesHash = {}, pairs = JSON.parse( holder.getAttribute( 'data-classes' ) || '[]' );
        for ( var c = 0; c < pairs.length; c++ )
        {
            var names = classAttributesHash[pairs[c][0]] = {};
            for ( var a = 0; a < pairs[c][1].length; a++ )
                names[pairs[c][1][a][0]] = pairs[c][1][a][1];
        }
        classSelect.onchange = ( function()
        {
            classAttributeSelect.options.length = 0;
            for ( var classAttributeId in classAttributesHash[classSelect.options[classSelect.selectedIndex].value] )
            {
                classAttributeSelect.options.add( new Option( classAttributesHash[classSelect.value][classAttributeId], classAttributeId ) );
            }
        }
        );
    }
})();
