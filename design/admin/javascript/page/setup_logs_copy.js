/*
 * Setup > Log files: the text of "Copy messages" (doc/eng-us/features/6.0/log-viewer.md, "Copy messages"). Pure, so a test
 * can run it without a browser: window.ExpLogCopy.lineOf( entry, opts ) is one entry as its row shows it, and
 * textOf( entries, opts ) the lines of several, in display order, one per line, as plain text (what a selection of the
 * rows would give, without the page around them).
 *
 * An entry is one of the feed (time ts, level lv, file f, source src, siteaccess sa, client ip, request u, parts p,
 * cut x) or a separator (sep). opts: wrap (the message's own line breaks are kept; without wrap a row shows them as
 * " ↵ "), levelName( lv ) (the level as the row writes it), rotated (the text of a separator), cut (the note of a cut
 * entry). The values are those the page shows: already masked by the server.
 */
( function ( root ) {
    'use strict';

    var messageOf = function ( entry, opts ) {
        var parts = entry.p || [], out = '';
        for ( var i = 0; i < parts.length; i++ )
            out += String( parts[i][0] );
        if ( !opts.wrap )
            out = out.replace( /\s*\n\s*/g, ' ↵ ' );
        if ( entry.x && opts.cut )
            out += ' ' + opts.cut;
        return out;
    };

    var lineOf = function ( entry, opts ) {
        opts = opts || {};
        if ( entry.sep )
            return opts.rotated || '';
        var level = entry.lv ? ( opts.levelName ? opts.levelName( entry.lv ) : entry.lv ) : '';
        var request = [ entry.sa, entry.ip, entry.u ].filter( function ( v ) { return v; } ).join( ' ' );
        return [ entry.ts || '', level, entry.f || '', entry.src || '', request, messageOf( entry, opts ) ]
            .filter( function ( v ) { return v !== ''; } ).join( ' ' );
    };

    var textOf = function ( entries, opts ) {
        var lines = [];
        for ( var i = 0; i < entries.length; i++ )
            lines.push( lineOf( entries[i], opts ) );
        return lines.join( '\n' );
    };

    root.ExpLogCopy = { lineOf: lineOf, textOf: textOf };
} )( typeof window !== 'undefined' ? window : this );
