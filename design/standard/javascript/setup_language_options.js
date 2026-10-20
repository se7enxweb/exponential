/*
 * The setup wizard's language step (setup/init/language_options.tpl): the default language is not also an
 * additional one: choosing a language as the default clears and disables its checkbox (the server checks it as
 * well). A file of its own, so the page runs under a Content Security Policy without 'unsafe-inline'
 * (doc/en/us/features/6.0/content-security-policy.md).
 */
(function () {
    var table = document.getElementById( 'eZSetupLanguageTable' );
    if ( !table || !table.querySelectorAll )
        return;
    var radios = table.querySelectorAll( 'input[name="eZSetupDefaultLanguage"]' );
    function sync() {
        for ( var i = 0; i < radios.length; i++ ) {
            var box = document.getElementById( 'eZSetupLanguages_' + radios[i].value );
            if ( !box )
                continue;
            if ( radios[i].checked )
                box.checked = false;
            box.disabled = radios[i].checked;
        }
    }
    for ( var i = 0; i < radios.length; i++ )
        radios[i].onchange = sync;
    // A refused answer is shown as given, both boxes ticked: leave it visible
    if ( !document.querySelector( '.error' ) )
        sync();
})();
