/*
 * class/datatype/list and class/datatype/view: sorting of the tables marked data-datatype-sort. A file of its own,
 * so the pages run under a Content Security Policy without 'unsafe-inline'
 * (doc/eng-us/features/6.0/content-security-policy.md).
 *
 * Every header cell with data-sort="text|number" becomes a button; a click sorts the rows of its table by the
 * data-value of the cell in that column (its text where it has none), a second click the other way round. The
 * header carries aria-sort. The label "Sort by %column" comes from the hidden element data-datatype-sort-text.
 * Without javascript the tables keep the order of the page.
 */
(function () {
    var tables = document.querySelectorAll( 'table[data-datatype-sort]' );
    if ( !tables.length ) return;
    var config = document.querySelector( '[data-datatype-sort-text]' );
    var label = config ? config.getAttribute( 'data-text-sort' ) || '' : '';

    function value( row, index, numeric ) {
        var cell = row.children[index];
        if ( !cell ) return numeric ? 0 : '';
        var v = cell.hasAttribute( 'data-value' ) ? cell.getAttribute( 'data-value' ) : cell.textContent;
        return numeric ? ( parseFloat( v ) || 0 ) : String( v ).trim().toLowerCase();
    }

    Array.prototype.forEach.call( tables, function ( table ) {
        var body = table.tBodies[0];
        if ( !body ) return;
        var heads = table.querySelectorAll( 'thead th[data-sort]' );
        Array.prototype.forEach.call( heads, function ( th ) {
            var numeric = th.getAttribute( 'data-sort' ) === 'number';
            var index = Array.prototype.indexOf.call( th.parentNode.children, th );
            var button = document.createElement( 'button' );
            button.type = 'button';
            button.className = 'a4-class-datatypes-sort';
            while ( th.firstChild ) button.appendChild( th.firstChild );
            if ( label ) button.title = label.split( '%column' ).join( button.textContent.trim() );
            th.appendChild( button );
            th.setAttribute( 'aria-sort', 'none' );
            button.addEventListener( 'click', function () {
                var ascending = th.getAttribute( 'aria-sort' ) !== 'ascending';
                // numbers start with the largest, the first click on a text column with A
                if ( numeric && th.getAttribute( 'aria-sort' ) === 'none' ) ascending = false;
                Array.prototype.forEach.call( heads, function ( other ) { other.setAttribute( 'aria-sort', 'none' ); } );
                th.setAttribute( 'aria-sort', ascending ? 'ascending' : 'descending' );
                var rows = Array.prototype.slice.call( body.rows );
                rows.sort( function ( a, b ) {
                    var x = value( a, index, numeric ), y = value( b, index, numeric );
                    var r = numeric ? x - y : x.localeCompare( y );
                    return ascending ? r : -r;
                } );
                rows.forEach( function ( row ) { body.appendChild( row ); } );
            } );
        } );
    } );
})();
