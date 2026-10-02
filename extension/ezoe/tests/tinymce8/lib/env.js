// Configuration of the TinyMCE 8 browser tests, all values can be set with environment variables
'use strict';

const fs = require( 'fs' );
const path = require( 'path' );

const root = path.resolve( __dirname, '../../../../..' );

const readPassword = () => {
    if ( process.env.EZ_PASSWORD )
        return process.env.EZ_PASSWORD;
    const file = process.env.EZ_PASSWORD_FILE || path.join( root, 'var/log/initial-admin-password' );
    if ( fs.existsSync( file ) )
    {
        const m = fs.readFileSync( file, 'utf8' ).match( /Password:\s*(\S+)/ );
        if ( m )
            return m[1];
    }
    throw new Error( 'No password: set EZ_PASSWORD or EZ_PASSWORD_FILE (file with a line "Password: …")' );
};

module.exports = {
    root,
    // admin siteaccess the tests log in to
    baseUrl: ( process.env.EZ_BASE_URL || 'http://localhost84/exponential6/admin' ).replace( /\/$/, '' ),
    user: process.env.EZ_USER || 'admin',
    password: readPassword,
    // firefox or chrome
    browser: process.env.EZ_BROWSER || 'firefox',
    browserPath: process.env.EZ_BROWSER_PATH || ( ( process.env.EZ_BROWSER || 'firefox' ) === 'firefox' ? '/usr/bin/firefox' : '/usr/bin/google-chrome' ),
    headless: process.env.EZ_HEADLESS !== '0',
    // object with an ezxmltext attribute; every test edits a new draft of it and discards it afterwards
    objectId: process.env.EZ_OBJECT || '91',
    language: process.env.EZ_LANGUAGE || 'eng-US',
    // objects compared between TinyMCE 3 and TinyMCE 8 by the roundtrip test
    roundtripObjects: ( process.env.EZ_ROUNDTRIP_OBJECTS || '91,92,114,135' ).split( ',' ).map( s => s.trim() ).filter( Boolean ),
    // optional sqlite database: stored ezxml is checked and the ezoe_engine preference is removed after the run
    db: process.env.EZ_DB !== undefined ? process.env.EZ_DB : ( fs.existsSync( path.join( root, 'var/storage/sqlite3/exponential6.db' ) ) ? path.join( root, 'var/storage/sqlite3/exponential6.db' ) : '' ),
    // search terms of the demo content: an image and an object of a class with images
    imageSearch: process.env.EZ_IMAGE_SEARCH || 'unsplash',
    imageClass: process.env.EZ_IMAGE_CLASS || 'Image',
    objectSearch: process.env.EZ_OBJECT_SEARCH || 'recipe',
    objectClass: process.env.EZ_OBJECT_CLASS || 'Recipe',
    // an existing object for embeds in the test content
    embedObject: process.env.EZ_EMBED_OBJECT || '95',
    screenshots: process.env.EZ_SCREENSHOTS || path.join( __dirname, '..', 'screenshots' ),
    // files uploaded by the upload test
    fixtures: path.join( __dirname, '..', 'fixtures' )
};
