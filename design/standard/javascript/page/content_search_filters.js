/*
 * The filter bar of the search page (content/search.tpl, form[data-search-filters]). The page works without this
 * script; it only makes the form easier to use:
 * - Apply ([data-search-apply]) stays disabled until a filter of the bar (#search-filters) changed;
 * - a date filter shows its from and to dates ([data-search-range]) only for a custom range, and typing a date
 *   chooses the custom range;
 * - the sort order ([data-search-sort]) searches again when it changes; its button ([data-search-sort-button]),
 *   for a page without scripts, is hidden;
 * - empty fields are not sent, so the address of a search names only what is set;
 * - on a narrow page the bar is folded under a "Show filters" button ([data-search-toggle]).
 * No jQuery, no inline script (doc/eng-us/features/6.0/content-security-policy.md). One file for every admin design
 * (design/standard, so admin4 needs no other admin design).
 */
(function () {
    var form = document.querySelector( 'form[data-search-filters]' );
    if ( !form || form.getAttribute( 'data-search-filters-ready' ) )
        return;
    form.setAttribute( 'data-search-filters-ready', '1' );

    var bar = form.querySelector( '#search-filters' );

    function toArray( list )
    {
        return Array.prototype.slice.call( list || [] );
    }

    function controls()
    {
        return bar ? toArray( bar.querySelectorAll( 'input, select, textarea' ) ) : [];
    }

    // The state of the bar as one string: what Apply compares with
    function state()
    {
        return controls().map( function ( control ) {
            var type = ( control.getAttribute( 'type' ) || '' ).toLowerCase();
            if ( type === 'checkbox' || type === 'radio' )
                return control.name + '=' + ( control.checked ? control.value : '' );
            return control.name + '=' + control.value;
        } ).join( '&' );
    }

    var initial = state();

    function updateApply()
    {
        var changed = state() !== initial;
        toArray( form.querySelectorAll( '[data-search-apply]' ) ).forEach( function ( button ) {
            button.disabled = !changed;
        } );
    }

    function updateRange( fieldset )
    {
        var period = fieldset.querySelector( '[data-search-period]' ),
            range = fieldset.querySelector( '[data-search-range]' );
        if ( period && range )
            range.hidden = period.value !== 'custom';
    }

    var dateSets = toArray( form.querySelectorAll( '[data-search-dates]' ) );
    dateSets.forEach( updateRange );

    function onChange( event )
    {
        var target = event.target;
        if ( !target || !target.closest )
            return;
        var dates = target.closest( '[data-search-dates]' );
        if ( dates )
        {
            var period = dates.querySelector( '[data-search-period]' );
            // a date typed in chooses the custom range
            if ( period && target !== period && ( target.getAttribute( 'type' ) || '' ) === 'date' && target.value !== '' )
                period.value = 'custom';
            updateRange( dates );
        }
        if ( target.matches && target.matches( '[data-search-sort]' ) && event.type === 'change' )
        {
            send();
            return;
        }
        updateApply();
    }

    // Empty fields are left out of the address (an empty owner, date or attribute is no filter anyway)
    function dropEmpty()
    {
        toArray( form.querySelectorAll( 'input, select' ) ).forEach( function ( control ) {
            var type = ( control.getAttribute( 'type' ) || '' ).toLowerCase();
            if ( !control.name || type === 'checkbox' || type === 'radio' || type === 'submit' || type === 'button' )
                return;
            if ( control.value === '' && control.name !== 'SearchText' )
                control.disabled = true;
        } );
        // the dates of a range that is not custom
        dateSets.forEach( function ( fieldset ) {
            var period = fieldset.querySelector( '[data-search-period]' );
            if ( period && period.value !== 'custom' )
                toArray( fieldset.querySelectorAll( 'input[type="date"]' ) ).forEach( function ( input ) { input.disabled = true; } );
        } );
    }

    function send()
    {
        dropEmpty();
        form.submit();
    }

    form.addEventListener( 'change', onChange );
    form.addEventListener( 'input', onChange );
    form.addEventListener( 'submit', dropEmpty );
    // back to this page from the browser's cache: the fields left out are fields again
    if ( typeof window !== 'undefined' && window.addEventListener )
        window.addEventListener( 'pageshow', function () {
            toArray( form.querySelectorAll( 'input[disabled], select[disabled]' ) ).forEach( function ( control ) {
                if ( !control.hasAttribute( 'data-search-apply' ) )
                    control.disabled = false;
            } );
        } );

    toArray( form.querySelectorAll( '[data-search-sort-button]' ) ).forEach( function ( button ) {
        button.hidden = true;
    } );

    // The fold of a narrow page: the stylesheet folds the bar (.is-folded) only while the page is narrow, and hides
    // the button on a wide page
    var toggle = form.querySelector( '[data-search-toggle]' );
    if ( bar && toggle )
    {
        var setFolded = function ( folded ) {
            bar.classList.toggle( 'is-folded', folded );
            toggle.setAttribute( 'aria-expanded', folded ? 'false' : 'true' );
            toggle.textContent = toggle.getAttribute( folded ? 'data-text-show' : 'data-text-hide' );
        };
        toggle.hidden = false;
        setFolded( true );
        toggle.addEventListener( 'click', function () {
            setFolded( !bar.classList.contains( 'is-folded' ) );
        } );
    }

    updateApply();
})();
