/*
 * admin4 translations tab: Set main becomes the default button when another main language is chosen.
 * A file of its own, so the page runs under a Content Security Policy without 'unsafe-inline'
 * (doc/eng-us/features/6.0/content-security-policy.md).
 */
(function()
{
    var radios = document.querySelectorAll('#tab-translations-list input.main-translation-radio');
    for ( var i = 0; i < radios.length; i++ )
    {
        radios[i].addEventListener('change', function()
        {
            var btn = document.getElementById('tab-translations-list-set-main');
            if ( !btn )
                return;
            if ( this.className === 'main-translation-radio' )
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
