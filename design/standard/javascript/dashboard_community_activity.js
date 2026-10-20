/*
 * The dashboard block "community activity" (dashboard/community_activity.tpl): fetches the latest forum posts with
 * the values of #community-activity-config (number of items, feed URL, version). A file of its own, so the page
 * runs under a Content Security Policy without 'unsafe-inline' (doc/eng-us/features/6.0/content-security-policy.md).
 */
(function () {
    var config = document.getElementById( 'community-activity-config' );
    if ( !config )
        return;
    var numberOfItems = config.getAttribute( 'data-number-of-items' ),
        forumsRssUrl = config.getAttribute( 'data-feed-url' ),
        versionString = config.getAttribute( 'data-version' );

    // Fetch the latest forum posts from share.ez.no
    $(document).ready(
    $.ajax({
        url: document.location.protocol + '//ajax.googleapis.com/ajax/services/feed/load?v=1.0&num=' + numberOfItems + '&callback=?&q=' + encodeURIComponent( forumsRssUrl ),
        dataType: 'json',
        success: function( data )
        {
            // Hide the loading wheel
            $( '.community-activity .loading' ).addClass( 'hide' );

            // Build the markup to show the latest posts
            var html = '<ul>';

            $( data.responseData.feed.entries ).each( function()
            {
                var $item = $(this);
                var $date = new Date( $item.attr("publishedDate") );

                //trace( $item.attr("link") );
                html += '<li>' +
                        '<h5><a href ="' + $item.attr( "link" ) + '?utm_content=eZ+Publish+Community+Project+' + versionString + '&utm_source=eZ+Publish+Community+Project+Dashboard&utm_medium=eZ+Publish+Community+Project+Dashboard&utm_campaign=eZ+Publish+Community+Project+Dashboard' + '">' + $item.attr( "title" ) + '</a></h5> ' +
                        '<p>' + $item.attr("author") + ' - <em>' + $date.getDate() + '/' + ( $date.getMonth() + 1 ) + '/' + $date.getFullYear() + ' ' + $date.getHours() + ':' + $date.getMinutes() + '</em></p>' +
                    // '<p>' + $item.attr("c:date") + '</p>' +
                        '</li>';
            });

            html += '</ul>';

            // Show the results
            $( 'div.community-activity #feed-elements' ).append( html );
        }
    })
    );
}());
