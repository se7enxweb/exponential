/*
 * The ordering of the sub-items on the node view (ordering.tpl): the Set button is highlighted while the sort
 * field or order differs from the stored one.
 * A file of its own, so the page runs under a Content Security Policy without 'unsafe-inline'
 * (doc/eng-us/features/6.0/content-security-policy.md).
 */
Array.prototype.forEach.call( document.querySelectorAll('#ezasi-sort-field, #ezasi-sort-order'), function( el ){
    el.setAttribute( 'initial', el.value );
    el.addEventListener('change', function(){
        var t = this, o = document.getElementById(this.id === 'ezasi-sort-field' ? 'ezasi-sort-order' : 'ezasi-sort-field'), s = document.getElementById('ezasi-sort-set');
        if ( !s ) return;
        // signal in gui if user needs to save this or not
        if ( t.value === t.getAttribute('initial') && ( !o || o.value === o.getAttribute('initial') ) )
        {
            s.classList.remove('defaultbutton');
            s.classList.add('button');
        }
        else
        {
            s.classList.remove('button');
            s.classList.add('defaultbutton');
        }
    });
} );
