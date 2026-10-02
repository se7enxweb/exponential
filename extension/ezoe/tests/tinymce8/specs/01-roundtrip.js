// Content opened and stored unchanged gives the same ezxml with TinyMCE 3 and TinyMCE 8
'use strict';

const { execFileSync } = require( 'child_process' );

module.exports = {
    name: 'roundtrip TinyMCE 3 / TinyMCE 8',
    async run( t )
    {
        const s = t.session, env = t.env;
        if ( !env.db )
            return t.skip( 'needs EZ_DB to read the stored ezxml' );

        const xmlFields = ( objectId, version ) => execFileSync( 'sqlite3', [ '-separator', '\t', env.db,
            `select id, replace(replace(data_text, char(10), '\\n'), char(9), ' ') from ezcontentobject_attribute where contentobject_id=${parseInt( objectId, 10 )} and version=${parseInt( version, 10 )} and language_code='${env.language.replace( /'/g, '' )}' and data_type_string='ezxmltext' order by id` ] )
            .toString().trim().split( '\n' ).filter( Boolean ).map( r => r.split( '\t' ) );

        for ( const objectId of env.roundtripObjects )
        {
            await t.step( 'object ' + objectId, async () => {
                const stored = {};
                for ( const engine of [ 'tinymce3', 'tinymce8' ] )
                {
                    await s.openDraft( { engine, objectId } );
                    await s.store();
                    stored[engine] = xmlFields( objectId, s.version );
                    await s.discard();
                }
                stored.tinymce3.forEach( ( [ id, xml ], i ) => {
                    const other = ( stored.tinymce8[i] || [] )[1];
                    t.check( xml === other, 'object ' + objectId + ', attribute ' + id + ': identical ezxml (' + xml.length + ' bytes)', firstDifference( xml, other ) );
                } );
            } );
        }
    }
};

function firstDifference( a = '', b = '' )
{
    let i = 0;
    while ( i < a.length && a[i] === b[i] )
        i++;
    return 'TinyMCE 3: …' + a.slice( Math.max( 0, i - 40 ), i + 80 ) + '\n      TinyMCE 8: …' + b.slice( Math.max( 0, i - 40 ), i + 80 );
}
