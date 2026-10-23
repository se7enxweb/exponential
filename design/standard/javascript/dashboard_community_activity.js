/*
 * The dashboard block "community activity" (dashboard/community_activity.tpl): fetches the latest forum posts with
 * the values of #community-activity-config (number of items, feed URL, version). A file of its own, so the page
 * runs under a Content Security Policy without 'unsafe-inline' (doc/eng-us/features/6.0/content-security-policy.md).
 * Plain JavaScript, no jQuery: a GET to the feed service with Accept for JSON and no cookies, and the list built from
 * the answer. jQuery read "callback=?" in the address as a JSONP request; this one is a plain request and needs the
 * service to allow it. The feed service has been shut down: a failed request, an answer outside 2xx or one that is
 * not the feed hides the loading text and shows data-failed-text of the configuration instead.
 * The posts come from another site, so they go in as text: the title, the author and the date are text nodes, and a
 * link is only an http or https address.
 */
(function () {
    var config = document.getElementById( 'community-activity-config' );
    if ( !config )
        return;
    var numberOfItems = parseInt( config.getAttribute( 'data-number-of-items' ), 10 ) || 10,
        forumsRssUrl = config.getAttribute( 'data-feed-url' ) || '',
        versionString = config.getAttribute( 'data-version' ) || '',
        failedText = config.getAttribute( 'data-failed-text' ) || '';

    var each = function ( list, fn ) { Array.prototype.forEach.call( list, fn ); };

    function loaded() {
        each( document.querySelectorAll( '.community-activity .loading' ), function ( el ) { el.classList.add( 'hide' ); } );
    }

    function failed() {
        loaded();
        if ( !failedText )
            return;
        each( document.querySelectorAll( 'div.community-activity #feed-elements' ), function ( el ) {
            var p = document.createElement( 'p' );
            p.className = 'community-activity-failed';
            p.textContent = failedText;
            el.appendChild( p );
        } );
    }

    // an http or https address, with the campaign parameters of the dashboard; anything else is no link
    function link( address ) {
        try {
            var url = new URL( String( address ) );
            if ( url.protocol !== 'http:' && url.protocol !== 'https:' )
                return null;
            // the campaign names the links of the block's title carry (community_activity.tpl)
            var campaign = 'eZ Publish Community Project Dashboard';
            url.searchParams.set( 'utm_content', 'eZ Publish Community Project ' + versionString );
            url.searchParams.set( 'utm_source', campaign );
            url.searchParams.set( 'utm_medium', campaign );
            url.searchParams.set( 'utm_campaign', campaign );
            return url.href;
        } catch ( e ) {
            return null;
        }
    }

    function two( n ) { return ( n < 10 ? '0' : '' ) + n; }

    function entry( item ) {
        var li = document.createElement( 'li' ), h5 = li.appendChild( document.createElement( 'h5' ) );
        var href = link( item.link ), title = String( item.title || '' );
        if ( href ) {
            var a = h5.appendChild( document.createElement( 'a' ) );
            a.href = href;
            a.textContent = title;
        } else {
            h5.textContent = title;
        }
        var p = li.appendChild( document.createElement( 'p' ) ), date = new Date( item.publishedDate );
        p.appendChild( document.createTextNode( String( item.author || '' ) + ' - ' ) );
        if ( !isNaN( date.getTime() ) ) {
            p.appendChild( document.createElement( 'em' ) ).textContent = date.getDate() + '/' + ( date.getMonth() + 1 ) + '/' +
                date.getFullYear() + ' ' + date.getHours() + ':' + two( date.getMinutes() );
        }
        return li;
    }

    var xhr = new XMLHttpRequest();
    xhr.open( 'GET', 'https://ajax.googleapis.com/ajax/services/feed/load?v=1.0&num=' + numberOfItems + '&q=' + encodeURIComponent( forumsRssUrl ), true );
    // the wildcard type in two parts: whole, it would read as the start of a comment to tools that strip them
    xhr.setRequestHeader( 'Accept', 'application/json, text/javascript, */' + '*; q=0.01' );
    xhr.onerror = failed;
    xhr.ontimeout = failed;
    xhr.timeout = 15000;
    xhr.onload = function () {
        var data = null;
        if ( ( xhr.status >= 200 && xhr.status < 300 ) || xhr.status === 304 ) {
            try { data = JSON.parse( xhr.responseText ); } catch ( e ) { data = null; }
        }
        var feed = data && data.responseData && data.responseData.feed;
        if ( !feed ) {
            failed();
            return;
        }
        loaded();
        var entries = feed.entries, ul = document.createElement( 'ul' );
        each( entries == null ? [] : ( typeof entries.length === 'number' ? entries : [ entries ] ), function ( item ) {
            if ( item )
                ul.appendChild( entry( item ) );
        } );
        each( document.querySelectorAll( 'div.community-activity #feed-elements' ), function ( el, i ) {
            el.appendChild( i ? ul.cloneNode( true ) : ul );
        } );
    };
    xhr.send();
}());
