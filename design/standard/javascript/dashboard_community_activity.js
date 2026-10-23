/*
 * The dashboard block "community activity" (dashboard/community_activity.tpl): fetches the latest forum posts with
 * the values of #community-activity-config (number of items, feed URL, version). A file of its own, so the page
 * runs under a Content Security Policy without 'unsafe-inline' (doc/eng-us/features/6.0/content-security-policy.md).
 * Plain JavaScript, no jQuery: a GET to the feed service with Accept for JSON and no cookies, and the list built from
 * the answer as before. jQuery read "callback=?" in the address as a JSONP request; this one is a plain request and
 * needs the service to allow it. The feed service has been shut down, so either way the block shows its error text.
 */
(function () {
    var config = document.getElementById( 'community-activity-config' );
    if ( !config )
        return;
    var numberOfItems = config.getAttribute( 'data-number-of-items' ),
        forumsRssUrl = config.getAttribute( 'data-feed-url' ),
        versionString = config.getAttribute( 'data-version' );

    // Fetch the latest forum posts from share.ez.no
    var xhr = new XMLHttpRequest();
    xhr.open( 'GET', document.location.protocol + '//ajax.googleapis.com/ajax/services/feed/load?v=1.0&num=' + numberOfItems + '&callback=?&q=' + encodeURIComponent( forumsRssUrl ), true );
    xhr.setRequestHeader( 'Accept', 'application/json, text/javascript, */*; q=0.01' );
    xhr.onload = function()
    {
        var data;
        if ( ( xhr.status < 200 || xhr.status >= 300 ) && xhr.status !== 304 )
            return;
        try { data = JSON.parse( xhr.responseText ); } catch ( e ) { return; }

        // Hide the loading wheel
        Array.prototype.forEach.call( document.querySelectorAll( '.community-activity .loading' ), function( el ) { el.classList.add( 'hide' ); } );

        // Build the markup to show the latest posts
        var html = '<ul>';

        var entries = data.responseData.feed.entries;
        Array.prototype.forEach.call( entries == null ? [] : ( typeof entries.length === 'number' ? entries : [ entries ] ), function( item )
        {
            var $date = new Date( item.publishedDate );

            html += '<li>' +
                    '<h5><a href ="' + item.link + '?utm_content=eZ+Publish+Community+Project+' + versionString + '&utm_source=eZ+Publish+Community+Project+Dashboard&utm_medium=eZ+Publish+Community+Project+Dashboard&utm_campaign=eZ+Publish+Community+Project+Dashboard' + '">' + item.title + '</a></h5> ' +
                    '<p>' + item.author + ' - <em>' + $date.getDate() + '/' + ( $date.getMonth() + 1 ) + '/' + $date.getFullYear() + ' ' + $date.getHours() + ':' + $date.getMinutes() + '</em></p>' +
                    '</li>';
        });

        html += '</ul>';

        // Show the results (as markup; a script in it is not run)
        Array.prototype.forEach.call( document.querySelectorAll( 'div.community-activity #feed-elements' ), function( el ) { el.insertAdjacentHTML( 'beforeend', html ); } );
    };
    xhr.send();
}());
