/*
 * admin4 translations tab: Update becomes the default button when the always-available checkbox changes.
 * A file of its own, so the page runs under a Content Security Policy without 'unsafe-inline'
 * (doc/eng-us/features/6.0/content-security-policy.md).
 */
(function()
{
    var checkbox = document.getElementById('tab-translations-alwaysavailable-checkbox');
    if ( !checkbox )
        return;
    checkbox.addEventListener('change', function()
    {
        var btn = document.getElementById('tab-translations-alwaysavailable-btn');
        if ( btn )
        {
            btn.classList.remove('button');
            btn.classList.add('defaultbutton');
        }
    });
})();
