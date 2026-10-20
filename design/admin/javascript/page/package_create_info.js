/*
 * admin and admin4 package/create, step info: the description and the link of the license follow the license picked in
 * the list. The same file is in design/admin and design/admin4. A file of its own, so the page runs under a Content
 * Security Policy without 'unsafe-inline' (doc/en/us/features/6.0/content-security-policy.md).
 */
(function()
{
    var select = document.getElementById( 'PackageLicence' ),
        link = document.getElementById( 'PackageLicenceLink' ),
        linkLine = document.getElementById( 'PackageLicenceLinkLine' ),
        description = document.getElementById( 'PackageLicenceDescription' );
    if ( !select || !link )
        return;
    function update()
    {
        var option = select.options[select.selectedIndex];
        if ( !option )
            return;
        var url = option.getAttribute( 'data-url' ) || '',
            text = option.getAttribute( 'data-description' ) || '';
        link.href = url || '#';
        link.textContent = option.getAttribute( 'data-name' ) || option.value;
        linkLine.hidden = !url;
        description.firstChild.nodeValue = text;
        description.hidden = !text;
    }
    if ( !description.firstChild || description.firstChild.nodeType !== 3 )
        description.insertBefore( document.createTextNode( '' ), description.firstChild );
    select.addEventListener( 'change', update );
})();
