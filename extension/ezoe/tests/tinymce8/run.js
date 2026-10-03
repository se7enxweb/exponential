#!/usr/bin/env node
// Runs the TinyMCE 8 browser tests: node run.js [spec …], e.g. node run.js link table
'use strict';

const fs = require( 'fs' );
const path = require( 'path' );
const env = require( './lib/env' );
const { Session } = require( './lib/session' );

const specDir = path.join( __dirname, 'specs' );
const all = fs.readdirSync( specDir ).filter( f => f.endsWith( '.js' ) ).sort();
const wanted = process.argv.slice( 2 );
const specs = wanted.length ? all.filter( f => wanted.some( w => f.replace( /^\d+-|\.js$/g, '' ) === w || f === w ) ) : all;

if ( !specs.length )
{
    console.error( 'No spec matches ' + wanted.join( ', ' ) + '. Available: ' + all.map( f => f.replace( /^\d+-|\.js$/g, '' ) ).join( ', ' ) );
    process.exit( 2 );
}

( async () => {
    console.log( 'TinyMCE 8 tests against ' + env.baseUrl + ' with ' + env.browser + ', object ' + env.objectId + ' (' + env.language + ')' + ( env.db ? '' : ', without database checks' ) );
    const session = await Session.start();
    const results = [];

    for ( const file of specs )
    {
        const spec = require( path.join( specDir, file ) );
        console.log( '\n■ ' + spec.name );
        let failed = 0, passed = 0;
        const t = {
            session,
            env,
            // records a check, detail is shown for failed checks
            check( condition, message, detail ) {
                if ( condition )
                {
                    passed++;
                    console.log( '  ✔ ' + message );
                }
                else
                {
                    failed++;
                    console.log( '  ✘ ' + message + ( detail !== undefined ? '\n      ' + ( typeof detail === 'string' ? detail : JSON.stringify( detail ) ) : '' ) );
                }
                return condition;
            },
            info( message ) {
                console.log( '    · ' + message );
            },
            skip( message ) {
                console.log( '  – skipped: ' + message );
            },
            // a step that fails as a whole when it throws, open dialogs are closed afterwards
            async step( name, fn ) {
                session.currentStep = spec.name + ': ' + name;
                try
                {
                    await fn();
                }
                catch ( e )
                {
                    failed++;
                    console.log( '  ✘ ' + name + ': ' + e.message.split( '\n' )[0] );
                    await session.screenshot( spec.name + ' ' + name ).catch( () => {} );
                    await session.page.evaluate( () => { const b = [ ...document.querySelectorAll( '.tox-dialog button' ) ].find( b => /Cancel|Abbrechen/.test( b.textContent ) ); b && b.click(); } ).catch( () => {} );
                }
            }
        };
        try
        {
            await spec.run( t );
        }
        catch ( e )
        {
            failed++;
            console.log( '  ✘ aborted: ' + e.message.split( '\n' )[0] );
            await session.screenshot( spec.name + ' aborted' ).catch( () => {} );
        }
        await session.discard().catch( () => {} );
        results.push( { name: spec.name, passed, failed } );
    }

    const logs = session.logs.slice();
    await session.close();

    console.log( '\n' + results.map( r => ( r.failed ? '✘ ' : '✔ ' ) + r.name + ': ' + r.passed + ' passed' + ( r.failed ? ', ' + r.failed + ' failed' : '' ) ).join( '\n' ) );
    if ( logs.length )
    {
        const counted = {};
        logs.forEach( l => { counted[l] = ( counted[l] || 0 ) + 1; } );
        console.log( '\nBrowser warnings and errors:\n' + Object.keys( counted ).map( l => '  ' + ( counted[l] > 1 ? counted[l] + '× ' : '' ) + l ).join( '\n' ) );
    }
    process.exit( results.some( r => r.failed ) ? 1 : 0 );
} )().catch( e => {
    console.error( e );
    process.exit( 2 );
} );
