/*
 * The locations tab of the node view (locations.tpl): the Set main button is highlighted once another location
 * is chosen as the main one.
 * A file of its own, so the page runs under a Content Security Policy without 'unsafe-inline'
 * (doc/eng-us/features/6.0/content-security-policy.md).
 */
(function()
{
    var radios = document.querySelectorAll('#tab-locations-list input.main-locations-radio');
    for ( var i = 0; i < radios.length; i++ )
    {
        radios[i].addEventListener('change', function()
        {
            var btn = document.getElementById('tab-locations-list-set-main');
            if ( !btn )
                return;
            if ( this.className === 'main-locations-radio' )
            {
                btn.classList.remove('button');
                btn.classList.add('defaultbutton');
            }
            else
            {
                btn.classList.remove('defaultbutton');
                btn.classList.add('button');
            }
        });
    }
})();
